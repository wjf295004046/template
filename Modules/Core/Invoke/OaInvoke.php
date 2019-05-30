<?php
/**
 * 请求OA模块
 * User: Administrator
 * Date: 2019/4/28
 * Time: 16:36
 */

namespace Modules\Core\Invoke;

use GuzzleHttp\Exception\GuzzleException;
use Modules\Core\Base\BaseLogic;

class OaInvoke extends BaseLogic
{
    /**
     * 校验并获取用户信息
     *
     * @param $token
     *
     * @return array
     */
    public function verifyUser($data)
    {
        $url = config('constant.oa_url');
        $query = '/home/index/thirdVerifyUser';

        try {
            $result = getExternalData($url . $query, $data, 'POST');
        } catch (GuzzleException $e) {
            return $this->setError($e->getMessage());
        }

        if (empty($result)) {
            return $this->setError('请求失败');
        }

        if ($result['resultId'] != 1) {
            return $this->setError($result['resultMsg']);
        }

        return $result['resultData']['data'];
    }
}