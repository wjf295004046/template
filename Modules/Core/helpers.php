<?php
/**
 * 公共函数
 * User: Administrator
 * Date: 2019/4/28
 * Time: 14:00
 */

/**
 * 数组转对象
 *
 * @param $data
 *
 * @return object
 */
function arrayToObject($data)
{
    return (object) $data;
}

/**
 * 对象转数组
 *
 * @param $data
 *
 * @return array
 */
function objectToArray($data)
{
    return (array) json_decode(json_encode($data), true);
}

/**
 * 获取商户号
 *
 * @return int
 */
function getMerchantId()
{
    return \Modules\Core\BaseUser::$merchant_id ?? 0;
}

/**
 * 获取ip地址
 *
 * @param bool $long
 * @param int  $ip_addr
 *
 * @return int|string
 */
function getIp($long = true, $ip_addr = 0)
{
    if ($ip_addr != 0) {
        $ip = str_contains($ip_addr, '.') ? $ip_addr : long2ip($ip_addr);
    } else {
        $ip = \Request::ip();
    }
    if ($long) {
        return bindec(decbin(ip2long($ip)));
    } else {
        return $ip;
    }
}

/**
 * 返回运行状态
 *
 * @return \Illuminate\Config\Repository|int|mixed
 */
function checkStatus()
{
    return config('constant.running_status') ?? 1;
}

/**
 * 发送POST请求
 *
 * @param        $url
 * @param string $data
 * @param array  $header
 * @param string $type
 * @param bool   $authentication
 *
 * @return array|mixed
 */
function curlSend($url, $data = '', $type = 'get', $header = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if ('post' === $type) {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    } elseif ('put' == $type) {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    if (!empty($header)) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    }
    $result = curl_exec($curl);
    $err    = curl_error($curl);
    curl_close($curl);

    if (!empty($err)) {
        E($err);
    }

    return json_decode($result, true);
}

/**
 * @param        $url
 * @param string $data
 * @param string $type
 * @param string $data_type  get: query  post: form_params  json数据：json
 * @param array  $headers
 * @param array  $other_options
 *
 * @return mixed
 * @throws \GuzzleHttp\Exception\GuzzleException
 */
function getExternalData($url, $data = '', $type = 'GET', $data_type = 'query', $headers = [], $other_options = [])
{
    $Client = new \GuzzleHttp\Client();

    $options = [];

    if (!empty($data)) {
        $options[$data_type] = $data;
    }

    if (!empty($header)) {
        $options['headers'] = $headers;
    }

    if (!empty($other_options)) {
        $options = array_merge($options, $other_options);
    }

    $response = $Client->request(strtoupper($type), $url, $options);

    $res      = $response->getBody();

    return json_decode($res, true);
}

/**
 * 强制走主库
 */
function queryMaster()
{
    config([
        'global.query_master' => true,
    ]);
}

/**
 * 取消强制走主库
 */
function unlockQueryMaster()
{
    config([
        'global.query_master' => false
    ]);
}

/**
 *判断是否走主库
 *
 * @return bool|\Illuminate\Config\Repository|mixed
 */
function isQueryMaster()
{
    return config('global.query_master') ?? false;
}

/**
 * @param $url
 *
 * @return string
 */
function genImgUrl($url)
{
    if (empty ($url)) {
        return $url;
    }

    return config('constant.image_url') . $url;
}

/**
 * @param $url
 *
 * @return string
 */
function genFileUrl($url)
{
    if (empty ($url)) {
        return $url;
    }

    return config('constant.file_url') . $url;
}

function uploadFile($file_type, $files)
{
    $return    = [
        'status' => 1,
        'msg'    => '上传成功',
        'data'   => [],
    ];
    $file_path = getUploadFilePath($file_type);
    foreach ($files as $key => $File) {
//        $File = new \Illuminate\Http\UploadedFile;
        $extension = $File->getClientOriginalExtension();
        $file_name = getUploadFileName($File->getClientOriginalName(), $extension);
        if (in_array($extension, [
            'gif',
            'jpg',
            'jpeg',
            'bmp',
            'png',
            'swf'
        ])) {
            $disk = 'image';
//        \Illuminate\Support\Facades\Storage::disk('image')->put()
        } else {
            $disk = 'file';
        }

        try {
            $return['data'][$key] =  $File->store($file_path, $disk);
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'msg'    => $e->getMessage(),
            ];
        }
    }
    
    return $return;
}

/**
 * 获取上传文件的路径
 *
 * @param $file_type
 *
 * @return string
 */
function getUploadFilePath($file_type)
{
    return $file_type . '/' . date('Y/m/d') . '/';
}

/**
 * 获取上传文件名
 *
 * @param $filename
 * @param $extension
 *
 * @return string
 */
function getUploadFileName($filename, $extension)
{
    return md5(microtime() . $filename) . '.' . $extension;
}

/**
 * 添加文件日志
 *
 * @param string $message 日志内容
 * @param array  $context 其他参数
 * @param string $channel 频道
 * @param int    $level   日志等级
 *
 * @throws Exception
 */
function addLog($message, $context = [], $channel = 'requests', $level = \Monolog\Logger::INFO)
{
//        $output    = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
//        $Formatter = new LineFormatter($output, null, true);
    $type = strtolower(\Monolog\Logger::getLevelName($level));

    $log_path = config('app.log_path') . '/' . $channel . '_' . $type . '_' . date('Ymd') . '.log';
    $Stream   = new Monolog\Handler\StreamHandler($log_path, $level);

    $Logger = new \Monolog\Logger('request_log');
    $Logger->pushHandler($Stream);

    $Logger->$type($message, $context);
}

function sendMail($to_email, $to_name, $subject, $send_arr, $view_url)
{
    \App\Jobs\JobsSendEmail::dispatch($to_email, $to_name, $subject, $send_arr, $view_url);
}

function sendErrorMail($name, $msg, $data = [], $type = 'queue')
{
    switch (strtolower($type)) {
        case 'queue':
            $type_name = '队列';
            break;
        case 'cli':
            $type_name = '脚本';
            break;
        default:
            $type_name = '未知';
            break;
    }
    $subject  = $type_name . ' 报错通知';
    $send_arr = [
        'type'      => $type,
        'type_name' => $type_name,
        'msg'       => (string) $msg,
        'name'      => (string) $name,
        'data'      => (array) $data,
    ];

    foreach ((array) config('mail.notify_emails') as $mail_info) {
        if (empty($mail_info['to_email'])) {
            continue;
        }
        $to_email = $mail_info['to_email'];
        $to_name  = $mail_info['to_name'] ?? $mail_info['to_email'];

        sendMail($to_email, $to_name, $subject, $send_arr, config('constant.email_template'));
    }
}

/**
 * 将$data转成以$key_name为键名的数据，如果传了$key_value，那么就为一维数组，值为$key_value
 *
 * @param        $data
 * @param        $key_name
 * @param string $key_value
 *
 * @return array
 */
function value_to_key($data, $key_name, $key_value = '')
{
    $result = [];
    foreach ($data as $data_item) {
        if (empty($key_value)) {
            $result[$data_item[$key_name]] = $data_item;
        } else {
            $result[$data_item[$key_name]] = $data_item[$key_value];
        }
    }

    return $result;
}
