<?php

namespace Modules\Home\Http\Controllers;

use Modules\Core\Base\BaseController;
use Modules\Home\Http\Logics\TestLogic;

class TestController extends BaseController
{
    public function test()
    {
        $data   = $this->requests;
        $logic  = new TestLogic();
        $result = $logic->test($data);
        if ($logic->hasError()) {
            $this->returnError($logic->getErrorMsg());
        }
        $this->returnJsonData($result);
    }

    public function testRedis()
    {
        $data   = $this->requests;
        $logic  = new TestLogic();
        $result = $logic->testRedis($data);
        if ($logic->hasError()) {
            $this->returnError($logic->getErrorMsg());
        }
        $this->returnJsonData($result);
    }
}
