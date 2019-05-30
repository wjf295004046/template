<?php
/**
 * redis基本类，后续有需要可在这里添加日志或配置等
 * User: Administrator
 * Date: 2019/5/6
 * Time: 10:33
 */

namespace Modules\Core\Logic;

use Illuminate\Support\Facades\Redis;
use Modules\Core\Base\BaseLogic;

class RedisLogic extends BaseLogic
{
    private static $instance;

    private static $redis_keys = [
        'erp_order_id' => 'erp:order:id'
    ];

    public function get($key)
    {
        try {
            return Redis::get($key);
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function set($key, $value, $timeout = 0)
    {
        try {
            $res = Redis::set($key, $value);

            if (empty($res)) {
                return $this->setError('Redis插入失败');
            }

            if (!empty($timeout)) {
                Redis::expire($key, $timeout);
            }
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function incr($key)
    {
        try {
            $res = Redis::incr($key);
            if (empty($res)) {
                return $this->setError('Redis获取失败');
            }

            return $res;
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function rPush($key, $value)
    {
        try {
            return Redis::rPush($key, $value);
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function lPush($key, $value)
    {
        try {
            return Redis::lPush($key, $value);
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function rPop($key)
    {
        try {
            return Redis::rPop($key);
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function lPop($key)
    {
        try {
            return Redis::lPop($key);
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 获取redis实例
     *
     * @return RedisLogic
     */
    public static function getRedis()
    {
        if (!empty(self::$instance)) {
            return self::$instance;
        }

        $Redis = new self();

        self::$instance = $Redis;

        return $Redis;
    }

    /**
     * 获取redis key
     *
     * @param $key
     *
     * @return mixed|string
     */
    public static function getRedisKey($key)
    {
        return self::$redis_keys[$key] ?? '';
    }
}