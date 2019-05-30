<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/29
 * Time: 10:32
 */

namespace Modules\Home\Http\Logics;

use Modules\Core\Base\BaseLogic;
use Modules\Core\Base\BaseUser;

class TestLogic extends BaseLogic
{
    public function test($data)
    {
        return BaseUser::getUserInfo();
    }
}