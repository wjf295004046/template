<?php
/**
 * 用户类
 * User: Administrator
 * Date: 2019/4/28
 * Time: 15:29
 */

namespace Modules\Core\Base;

class BaseUser
{
    /**
     * 用户id
     *
     * @var int
     */
    public static $id = 0;

    /**
     * 工号
     *
     * @var int
     */
    public static $job_id = 0;

    /**
     * 唯一串号
     *
     * @var string
     */
    public static $uuid = '';

    /**
     * 员工姓名
     *
     * @var string
     */
    public static $member_name = '';

    /**
     * 用户token
     *
     * @var string
     */
    public static $token = '';

    /**
     * 商户id
     *
     * @var int
     */
    public static $merchant_id = '';

    /**
     * 部门id
     *
     * @var int
     */
    public static $department_id = '';

    /**
     * 手机号
     *
     * @var string
     */
    public static $mobile = '';

    /**
     * 用户状态
     *
     * @var int
     */
    public static $status = 0;

    /**
     * 最后登录时间戳
     *
     * @var int
     */
    public static $last_login_time = 0;

    public static $role_id = '';

    public static function setUserInfo($data)
    {
        foreach ($data as $key => $value) {
            self::$$key = trim($value);
        }
    }

    /**
     * 返回用户信息
     *
     * @return array
     */
    public static function getUserInfo()
    {
        return [
            'id'            => self::$id,
            'job_id'        => self::$job_id,
            'uuid'          => self::$uuid,
            'member_name'   => self::$member_name,
            'token'         => self::$token,
            'department_id' => self::$department_id,
            'mobile'        => self::$mobile
        ];
    }
}