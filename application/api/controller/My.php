<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/4/7
 * Time: 14:09
 */
namespace app\api\controller;

use think\Db;
use my\Sendsms;
class My extends Base {

    public function myDetail() {
        try {
            $whereUser = [
                ['m.id','=',$this->myinfo['id']]
            ];
            $info = Db::table('mp_user_mp')->alias('m')
                ->join('mp_user u','m.uid=u.id','left')
                ->join('mp_user_role r','u.id=r.uid','left')
                ->where($whereUser)
                ->field('m.uid,m.openid,m.unionid,m.last_login_time,m.create_time,m.bind_time,u.nickname,u.avatar,u.realname,u.age,u.sex,u.avatar,u.tel,u.score,u.focus,u.subscribe,u.vip,u.vip_time,u.desc,u.role,u.org,r.busine')
                ->find();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }

    //充值
    public function recharge()
    {
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        try {
            $val['price'] = 0.03;
            $val['days'] = 365;
            $val['create_time'] = time();
            $val['desc'] = '充值年度会员';
            $val['order_sn'] = create_unique_number('v');
            Db::table('mp_vip_order')->insert($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($val);

    }

    /*------ 申请角色 START ------*/

    //获取申请信息
    public function applyInfo() {
        $uid = $this->myinfo['uid'];
        try {
            $info = Db::table('mp_user_role')->where('uid','=',$uid)->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($info) {
            return ajax($info);
        }else {
            return ajax();
        }
    }

    //申请角色
    public function roleApply() {
        //公司名,负责人姓名,身份证号,身份证正反面,资质证明
        $val['role'] = input('post.role');
        $val['org'] = input('post.org');
        $val['name'] = input('post.name');
        $val['identity'] = input('post.identity');
        $val['busine'] = input('post.busine');
        checkPost($val);
        $val['uid'] = $this->myinfo['uid'];

        $tmp['id_front'] = input('post.id_front');
        $tmp['id_back'] = input('post.id_back');
        $tmp['license'] = input('post.license');

        if(!in_array($val['role'],[1,2])) {
            return ajax('invalid role',-4);
        }
        if (!isCreditNo_simple($val['identity'])) {
            return ajax('无效的身份证号', 13);
        }
        if(!$tmp['id_front'] || !$tmp['id_back']) {
            return ajax('上传身份证正反面',44);
        }
        if(!$tmp['license']) {
            return ajax('请上传资质证明',45);
        }

        try {
            $whereRole = [
                ['uid','=',$val['uid']]
            ];
            $role_exist = Db::table('mp_user_role')->where($whereRole)->find();
            if($role_exist) {
                if($role_exist['role_check'] == 1 || $role_exist['role_check'] == 2) {//审核中和已通过的无法修改
                    return ajax('当前状态无法申请角色',46);
                }
            }
            $whereUser = [
                ['id','=',$this->myinfo['uid']]
            ];
            $user = Db::table('mp_user')->where($whereUser)->field('tel')->find();
            $val['tel'] = $user['tel'];
            //验证图片是否存在
            foreach ($tmp as $k=>$v) {
                $qiniu_exist = $this->qiniuFileExist($v);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'] . ' :'.$k,5);
                }
            }
            //转移七牛云图片
            foreach ($tmp as $k=>$v) {
                $qiniu_move = $this->moveFile($v,'upload/role/');
                if($qiniu_move['code'] == 0) {
                    $val[$k] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'] .' :' . $k . '',-1);
                }
            }

            $val['role_check'] = 1;
            if($role_exist) {
                Db::table('mp_user_role')->where('uid',$val['uid'])->update($val);
            }else {
                $val['create_time'] = time();
                Db::table('mp_user_role')->insert($val);
            }
        }catch (\Exception $e) {//异常删图
            if($role_exist) {
                foreach ($tmp as $k=>$v) {
                    if(isset($val[$k]) && $role_exist[$k] != $val[$k]) {
                        $this->rs_delete($val[$k]);
                    }
                }
            }else {
                foreach ($tmp as $k=>$v) {
                    if(isset($val[$k])) {
                        $this->rs_delete($val[$k]);
                    }
                }
            }
            return ajax($e->getMessage(),-1);
        }
        if($role_exist) {//正常删图
            foreach ($tmp as $k=>$v) {
                if($val[$k] != $role_exist[$k]) {
                    $this->rs_delete($role_exist[$k]);
                }
            }
        }
        return ajax();
    }


    /*------ 申请角色 END ------*/

}