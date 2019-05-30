<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/16
 * Time: 16:00
 */

namespace Modules\Core\Base;

use Illuminate\Routing\Controller;
use Modules\Core\Invoke\OaInvoke;
use Monolog\Logger;

class BaseController extends Controller
{
    public $requests;
    public $files;
    public $method;
    public $time;
    public $url;

    public function __construct()
    {
        $this->requests = $this->getRequest();

        $this->getConfig();
        $this->verifyUser();
    }

    /**
     * 获取请求参数
     *
     * @param $request
     */
    public function getRequest()
    {
        $requests = [];

        foreach (\Request::all() as $key => $value) {
            $requests[$key] = trim($value);
        }

        $requests['p']        = isset($requests['p']) ? intval($requests['p']) : 1;
        $requests['pagesize'] = isset($requests['pagesize']) ? intval($requests['pagesize']) : config('constant.default_pagesize');
        $requests['ip']       = getIp();
        $requests['real_ip']  = getIp(false);

        //关键词
        $requests['keyword'] = isset($requests['keyword']) ? trim($requests['keyword']) : '';
        //start_time
        $requests['start_time'] = isset($requests['start_time']) ? strtr($requests['start_time'], ['.' => '-']) : '';
        //end_time
        $requests['end_time'] = isset($requests['end_time']) ? strtr($requests['end_time'], ['.' => '-']) : '';
        //time
        $requests['time'] = isset($requests['time']) ? strtr($requests['time'], ['.' => '-']) : '';
        //field
        $requests['field'] = isset($requests['field']) ? $requests['field'] : 'id';
        //sort
        $requests['sort']  = isset($requests['sort']) ? $requests['sort'] : 'desc';
        $requests['order'] = $requests['field'] . ' ' . $requests['sort'];

        $this->url    = \Request::path();
        $this->method = \Request::method();
        $this->files  = \Request::allFiles();
        $this->time   = time();

        return $requests;
    }

    /**
     * 一些全局变量从这边放到配置中去
     */
    public function getConfig()
    {
        $page     = $this->requests['p'];
        $pagesize = $this->requests['pagesize'];

        config([
            'global.page'     => $page,
            'global.pagesize' => $pagesize,
        ]);
    }

    public function verifyUser()
    {
        $OaInvoke    = new OaInvoke();
        $member_info = $OaInvoke->verifyUser($this->requests);
        if ($OaInvoke->hasError()) {
            $this->returnError($OaInvoke->getErrorMsgAndFlushError(), -10);
        }

        //保存用户信息
        BaseUser::setUserInfo($member_info);
        //todo 商户id号相关存储
    }

    /**
     * 返回成功
     *
     * @param string $msg
     */
    public function returnSuccess($msg = '操作成功')
    {
        $this->returnJson(1, $msg);
    }

    /**
     * 数据返回
     *
     * @param $resultData
     */
    public function returnJsonData($resultData)
    {
        $this->returnJson(1, '请求成功', (array) $resultData);
    }

    /**
     * 数据信息返回
     *
     * @param $resultData
     */
    public function returnJsonInfo($resultData)
    {
        if (empty($resultData)) {
            $resultData = null;
        }
        $this->returnJson(1, '请求成功', $resultData);
    }

    public function returnResult($code, $msg)
    {
        switch ($this->method) {
            case 'GET':
                echo view('msg', [
                    'msg' => $msg
                ]);
                exit();
            default:
                $this->returnJson($code, $msg);
                exit();
        }
    }

    /**
     * 返回失败
     *
     * @param string $msg
     */
    public function returnError($msg = '缺少必要的参数', $code = 0)
    {
        $code = $code == 1 ? 0 : $code;

        $this->returnJson($code, $msg);
    }

    /**
     * 返回json数据
     *
     * @param      $resultId
     * @param      $resultMsg
     * @param null $resultData
     */
    public function returnJson($resultId, $resultMsg, $resultData = null)
    {
        $this->requests['resultId']  = $resultId;
        $this->requests['resultMsg'] = $resultMsg;
        $data                      = array(
            'resultId'  => $resultId,
            'resultMsg' => $resultMsg
        );
        $data['resultData']        = array_merge([
            'fixed' => [
                'p'        => $this->requests['p'],
                'pagesize' => $this->requests['pagesize'],
            ]
        ], [
            'data' => $resultData
        ]);

        $this->ajaxReturn($data);
    }

    /**
     * Ajax方式返回数据到客户端
     *
     * @access protected
     *
     * @param mixed  $data        要返回的数据
     * @param String $type        AJAX返回数据格式
     * @param int    $json_option 传递给json_encode的option参数
     *
     * @return void
     */
    protected function ajaxReturn($data, $type = 'JSON', $json_option = 0)
    {
        switch (strtoupper($type)) {
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                echo json_encode($data, $json_option);
                exit();
        }
    }

    /**
     * 上传单个图片/文件
     *
     * @param $type
     *
     * @return mixed
     */
    public function uploadSingle($type)
    {
        $data = $this->upload($type);
        foreach ($data as $value) {
            return $value;
        }
    }

    /**
     * 上传文件
     *
     * @param $type
     */
    public function upload($type)
    {
        if (empty($this->files)) {
            return $this->returnError('文件不存在');
        }

        $info = uploadFile($type, $this->files);
        if ($info['status'] == 0) {
            $this->returnError('文件/图片上传失败，请重试,失败原因：' . $info['msg']);
        }

        return $info['data'];
    }

    /**
     * @throws \Exception
     */
    public function addLog()
    {
        if (empty(BaseUser::$member_name)) {
            return;
        }

        if ($this->requests['resultId'] != 1) {
            $level = Logger::ERROR;
        } else {
            $level = Logger::INFO;
        }
        $userinfo = [
            'job_id'      => BaseUser::$job_id,
            'member_name' => BaseUser::$member_name,
            'uuid'        => BaseUser::$uuid,
        ];
        addLog($this->requests->resultMsg, [
            'request'     => $this->requests,
            'method'      => $this->method,
            'userinfo'    => $userinfo,
            'merchant_id' => getMerchantId(),
            'url'         => $this->url,
        ], 'requests', $level);
    }

    /**
     * @throws \Exception
     */
    public function __destruct()
    {
        $this->addLog();
    }
}