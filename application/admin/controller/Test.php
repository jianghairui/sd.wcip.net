<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2019/11/4
 * Time: 16:35
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;
class Test extends Base {

    public function index() {

//        $val = [];
//        $use_video = 0;
//        $exist = [
//            'id' => 1,
//            'url' => 'abcd'
//        ];
//        if($use_video) {
//            $val['url'] = 'abcd';
//        }
//
//        try {
//            echo $exist['nickname'];
//        } catch (\Exception $e) {
//            if($val['url'] == $exist) {
//                $this->excep('YES','YES');
//            }else {
//                $this->excep('NO','NO');
//            }
//            return ajax($e->getMessage(),-111);
//        }

//        echo 'SUCCESS<br>';

        echo gen_nickname();
//
//        $this->assign('nickname',$nicheng);
//
//        return $this->fetch();

    }



}