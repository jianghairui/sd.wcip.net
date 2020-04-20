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


//    public function index() {
//        try {
//            $ids = Db::table('mp_user')->column('id');
//            foreach ($ids as $v) {
//                if(!in_array($v,[1,2,3,5,13,14])) {
//                    $res = Db::table('mp_user')->where('id','=',$v)->update(['nickname'=>randomkeys(10)]);
//                    echo $v . '-' . $res . ' success<br>';
//                }
//            }
//        } catch (\Exception $e) {
//            return ajax($e->getMessage(), -1);
//        }
//    }


//    private function index() {
//        $uid = input('param.uid',0);
//        if(!in_array($uid,[1,2,3])) {
//            die('非法操作');
//        }
//        $whereRole = [
//            ['uid','=',$uid]
//        ];
//        $whereUser = [
//            ['id','=',$uid]
//        ];
//        try {
//            $role_exist = Db::table('mp_user_role')->where($whereRole)->find();
//            if(!$role_exist) {
//                die('角色不存在');
//            }
//            @$this->rs_delete($role_exist['id_front']);
//            @$this->rs_delete($role_exist['id_back']);
//            @$this->rs_delete($role_exist['license']);
//            @$this->rs_delete($role_exist['cover']);
//            Db::table('mp_user_role')->where($whereRole)->delete();
//            Db::table('mp_user')->where($whereUser)->update(
//                [
//                    'role' => 0,
//                    'org' => ''
//                    ]
//            );
//        } catch (\Exception $e) {
//            return ajax($e->getMessage(), -1);
//        }
//
//        echo 'id_front deleted<br>';
//        echo 'id_back deleted<br>';
//        echo 'license deleted<br>';
//        echo 'cover deleted<br>';
//        echo 'SUCCESS<br>';
//
//
//
//    }



    public function test() {

        $start = microtime(true);
        try {
//            $note_ids = Db::table('mp_note')->column('id');
//            foreach ($note_ids as $v) {
//                Db::table('mp_note')->where('id','=',$v)->update(['uid'=>mt_rand(1,7)]);
//            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $end = microtime(true);
        echo bcsub($end,$start,6);
    }



}