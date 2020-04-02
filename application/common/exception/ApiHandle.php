<?php
/**
 * Created by PhpStorm.
 * User: sleep
 * Date: 2018/3/31
 * Time: 下午4:34
 */

namespace app\common\exception;

use Exception;
use think\exception\Handle;

class ApiHandle extends Handle
{
    function render(Exception $e)
    {
        $this->excep('ApiHandle/render',$e->getMessage());
        return ajax($e->getMessage(), -1, 400);
    }


    //Exception日志
    protected function excep($cmd,$str) {
        $file= LOG_PATH . '/exception.log';
        create_dir($file);
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

}