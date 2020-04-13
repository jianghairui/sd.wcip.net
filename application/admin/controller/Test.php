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
        $uid = input('param.uid',0);
        if(!in_array($uid,[1,2,3])) {
            die('非法操作');
        }
        $whereRole = [
            ['uid','=',$uid]
        ];
        $whereUser = [
            ['id','=',$uid]
        ];
        try {
            $role_exist = Db::table('mp_user_role')->where($whereRole)->find();
            if(!$role_exist) {
                die('角色不存在');
            }
            @$this->rs_delete($role_exist['id_front']);
            @$this->rs_delete($role_exist['id_back']);
            @$this->rs_delete($role_exist['license']);
            @$this->rs_delete($role_exist['cover']);
            Db::table('mp_user_role')->where($whereRole)->delete();
            Db::table('mp_user')->where($whereUser)->update(
                [
                    'role' => 0,
                    'org' => ''
                    ]
            );
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        echo 'id_front deleted<br>';
        echo 'id_back deleted<br>';
        echo 'license deleted<br>';
        echo 'cover deleted<br>';
        echo 'SUCCESS<br>';



    }



}