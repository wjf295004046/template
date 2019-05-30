<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/29
 * Time: 9:53
 */

namespace Modules\Core\Base;

class BaseLogic extends BaseModel
{
    public $time;
    public $max_page = 1000000;

    /**
     * pdf大小 10k
     *
     * @var int
     */
    public $pdf_size = 3 * 1024;

    public function __construct()
    {
        $this->time = time();

        parent::__construct();
    }

    /**
     * 验证规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'accepted' => ':attribute 必须是 yes、on、1 或 true',
            'active_url' => ':attribute 必须是基于 PHP 函数 dns_get_record 的，有 A 或 AAAA 记录的值',
            'after' => ':attribute 必须在 :date 之后',
            'after_or_equal' => ':attribute 必须大于等于 :date',
            'alpha' => ':attribute 必须是英文字母',
            'alpha_num' => ':attribute 必须是数字',
            'between' => ':attribute 必须在 :min 和 :max 之间',
            'digits' => ':attribute 必须是 :value 位的数字',
            'digits_between' => ':attribute 必须是 :min 到 :max 为长度的数字',
            'in' => ':attribute 必须是 :values 其中一个',
            'filled' => ':attribute 不能为空',
            'min' => ':attribute 不能小于 :min 个字符',
            'max' => ':attribute 不能大于 :min 个字符',
            'not_in' => ':attribute 不能是 :values 其中一个',
            'numeric' => ':attribute 必须是数字',
            'present' => ':attribute 验证字段必须出现在输入数据中但可以为空',
            'required' => ':attribute 不能为空',
        ];
    }

}