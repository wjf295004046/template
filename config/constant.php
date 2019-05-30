<?php
/**
 * 常量配置
 * User: Administrator
 * Date: 2019/4/28
 * Time: 15:25
 */

return [
    /**
     * 默认分页数量
     */
    'default_pagesize' => 20,

    /**
     * 其他模块url
     */
    'oa_url'           => env('OA_URL', null),
    'order_url'        => env('ORDER_URL', null),
    'logistics_url'    => env('LOGISTICS_URL', null),
    'product_url'      => env('PRODUCT_URL', null),
    'storage_url'      => env('STORAGE_URL', null),
    'purchase_url'     => env('PURCHASE_URL', null),
    'finance_url'      => env('FINANCE_URL', null),
    'publish_url'      => env('PUBLISH_URL', null),
    'image_url'        => env('IMAGE_URL', null),
    'file_url'         => env('FILE_URL', null),

];