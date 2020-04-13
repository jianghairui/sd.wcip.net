<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/25
 * Time: 16:09
 */
namespace app\admin\controller;
use think\Db;
use think\Exception;
use EasyWeChat\Factory;
use think\exception\HttpResponseException;

class User extends Base {

    //会员列表
    public function userList() {
        $param['role_check'] = input('param.role_check','');
        $param['role'] = input('param.role','');
        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];

        if($param['role_check'] !== '') {
            $where[] = ['r.role_check','=',$param['role_check']];
        }
        if($param['role'] !== '') {
            $where[] = ['u.role','=',$param['role']];
        }
        if($param['datemin']) {
            $where[] = ['u.create_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['datemin'])))];
        }

        if($param['datemax']) {
            $where[] = ['u.create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])))];
        }

        if($param['search']) {
            $where[] = ['u.nickname|u.tel','like',"%{$param['search']}%"];
        }
        $order = ['u.id'=>'DESC'];
        try {
            $count = Db::table('mp_user')->alias('u')
                ->join('mp_user_role r','u.id=r.uid','left')
                ->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_user')->alias('u')
                ->join('mp_user_role r','u.id=r.uid','left')
                ->where($where)->order($order)
                ->field('u.*,r.role AS tmp_role,r.role_check')
                ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('param',$param);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
    //用户详情
    public function userDetail() {
        $id = input('param.id');
        $where = [
            ['u.id','=',$id]
        ];
        try {
            $info = Db::table('mp_user')->alias('u')
                ->join('mp_user_role r','u.id=r.uid','left')
                ->field('u.*,r.cover,r.role AS tmp_role,r.org AS role_org,r.name,r.identity,r.id_front,r.id_back,r.tel as role_tel,r.role_check,r.license,r.busine,r.province_code,r.city_code,r.region_code')
                ->where($where)
                ->find();
            $whereProvince = [
                ['pcode','=',0]
            ];
            $province_list = Db::table('mp_city')->where($whereProvince)->select();
            $city_list = [];
            $region_list = [];
            if($info['province_code']) {
                $whereCity = [
                    ['pcode','=',$info['province_code']]
                ];
                $city_list = Db::table('mp_city')->where($whereCity)->select();
            }
            if($info['city_code']) {
                $whereRegion = [
                    ['pcode','=',$info['city_code']]
                ];
                $region_list = Db::table('mp_city')->where($whereRegion)->select();
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $this->assign('info',$info);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        $this->assign('province_list',$province_list);
        $this->assign('city_list',$city_list);
        $this->assign('region_list',$region_list);
        return $this->fetch();
    }

    public function userMod() {
        $val['uid'] = input('post.uid');
        checkInput($val);

        $val['province_code'] = input('post.provinceCode',0);
        $val['city_code'] = input('post.cityCode',0);
        $val['region_code'] = input('post.regionCode',0);
        $cover = input('post.cover');
        try {
            $whereRole = [
                ['uid','=',$val['uid']]
            ];
            $role_exist = Db::table('mp_user_role')->where($whereRole)->find();
            if(!$role_exist) {
                return ajax('非法参数',-1);
            }
            if($val['region_code']) {
                $region = Db::table('mp_city')->where('code','=',$val['region_code'])->find();
                if(!$region) {
                    return ajax('无效的地区编码',-1);
                }
            }
            if ($val['city_code']){
                $city = Db::table('mp_city')->where('code','=',$val['city_code'])->find();
                if(!$city) {
                    return ajax('无效的地区编码',-1);
                }
            }
            if ($val['province_code']) {
                $province = Db::table('mp_city')->where('code','=',$val['province_code'])->find();
                if(!$province) {
                    return ajax('无效的地区编码',-1);
                }
            }
            $update_data = [
                'province_code' => $val['province_code'],
                'city_code' => $val['city_code'],
                'region_code' => $val['region_code']
            ];
            if($cover) {
                $qiniu_exist = $this->qiniuFileExist($cover);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($cover,'upload/role/');
                if($qiniu_move['code'] == 0) {
                    $val['cover'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
                $update_data['cover'] = $val['cover'];
            }

            Db::table('mp_user_role')->where($whereRole)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();

    }
    //申请角色-通过审核
    public function rolePass() {
        $param['uid'] = input('post.id','');
        checkInput($param);
        try {
            $whereRole = [
                ['role_check','=',1],
                ['uid','=',$param['uid']]
            ];
            $role_exist = Db::table('mp_user_role')->where($whereRole)->field('id,role,uid,org')->find();
            if(!$role_exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_user_role')->where($whereRole)->update([
                'role_check'=>2,
                'check_time'=>time()
            ]);

            $whereUser = [
                ['id','=',$param['uid']]
            ];
            Db::table('mp_user')->where($whereUser)->update([
                'role' => $role_exist['role'],
                'org' => $role_exist['org']
            ]);
            $tpl_data = [
                'action' => 'rolePass',
                'uid' => $role_exist['uid']
            ];
            $this->asyn_tpl_send($tpl_data);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //申请角色-拒绝审核
    public function roleReject() {
        $param['uid'] = input('post.id','');
        $param['reason'] = input('post.reason','');
        checkInput($param);
        try {
            $whereRole = [
                ['role_check','=',1],
                ['uid','=',$param['uid']]
            ];
            $role_exist = Db::table('mp_user_role')->where($whereRole)->field('id,role,uid,org')->find();
            if(!$role_exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_user_role')->where($whereRole)->update([
                'role_check'=>3,
                'check_time'=>time(),
                'reason' => $param['reason']
            ]);
            $tpl_data = [
                'action' => 'roleReject',
                'uid' => $role_exist['uid']
            ];
            $this->asyn_tpl_send($tpl_data);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //充值类目列表
    public function vipList() {
        $where = [
            ['status','=',1],
            ['del','=',0]
        ];
        try {
            $list = Db::table('mp_vip')->where($where)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
    //添加充值类目
    public function vipAdd() {
        return $this->fetch();
    }
    //添加充值类目POST
    public function vipAddPost() {
        $val['title'] = input('post.title');
        $val['price'] = input('post.price');
        $val['detail'] = input('post.detail');
        $val['days'] = input('post.days');
        checkInput($val);
        $pic = input('post.pic');
        if(!$pic) {
            return ajax('请上传图片',-1);
        }
        try {
            $qiniu_exist = $this->qiniuFileExist($pic);
            if($qiniu_exist !== true) {
                return ajax($qiniu_exist['msg'],-1);
            }
            $qiniu_move = $this->moveFile($pic,'upload/vip/');
            if($qiniu_move['code'] == 0) {
                $val['pic'] = $qiniu_move['path'];
            }else {
                return ajax($qiniu_move['msg'],-2);
            }
            Db::table('mp_vip')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                $this->rs_delete($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);

    }
    //充值类目详情
    public function vipDetail() {
        $id = input('param.id');
        try {
            $info = Db::table('mp_vip')->where('id',$id)->find();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$info);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
    //充值类目编辑
    public function vipModPost() {
        $val['title'] = input('post.title');
        $val['price'] = input('post.price');
        $val['detail'] = input('post.detail');
        $val['days'] = input('post.days');
        $val['pic'] = input('post.pic');
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $qiniu_exist = $this->qiniuFileExist($val['pic']);
            if($qiniu_exist !== true) {
                return ajax($qiniu_exist['msg'],-1);
            }

            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_vip')->where($where)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            $qiniu_move = $this->moveFile($val['pic'],'upload/vip/');
            if($qiniu_move['code'] == 0) {
                $val['pic'] = $qiniu_move['path'];
            }else {
                return ajax($qiniu_move['msg'],-2);
            }
            Db::table('mp_vip')->where($where)->update($val);
        }catch (\Exception $e) {
            if($val['pic'] != $exist['pic']) {
                $this->rs_delete($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        if($val['pic'] != $exist['pic']) {
            $this->rs_delete($exist['pic']);
        }
        return ajax([],1);

    }
    //删除会员
    public function vipDel() {
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_vip')->where($where)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
//            Db::table('mp_vip')->where($where)->delete();
            Db::table('mp_vip')->where($where)->update(['del'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
//        $this->rs_delete($exist['pic']);
        return ajax();
    }

    //拉黑用户
    public function userStop() {
        $id = input('post.id');
        $map = [
            ['status','=',1],
            ['id','=',$id]
        ];
        try {
            $res = Db::table('mp_user')->where($map)->update(['status'=>2]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax([],1);
        }else {
            return ajax('拉黑失败',-1);
        }
    }
    //恢复用户
    public function userGetback() {
        $id = input('post.id');
        $map = [
            ['status','=',2],
            ['id','=',$id]
        ];
        try {
            $res = Db::table('mp_user')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax([],1);
        }else {
            return ajax('恢复失败',-1);
        }
    }

    public function rechargeList() {
        $param['status'] = input('param.status','');
        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        if($param['datemin']) {
            $where[] = ['o.create_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['datemin'])))];
        }
        if($param['datemax']) {
            $where[] = ['o.create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])))];
        }
        if($param['search']) {
            $where[] = ['o.nickname|o.tel','like',"%{$param['search']}%"];
        }

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];
        try {
            $count = Db::table('mp_vip_order')->alias('o')->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_vip_order')->alias('o')
                ->join("mp_user u","o.uid=u.id","left")
                ->join("mp_vip v","o.vip_id=v.id","left")
                ->order(['o.create_time'=>'DESC'])
                ->field("o.*,u.nickname,u.avatar,v.title")
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function rechargeDetail() {
        $id = input('param.id');
        try {
            $where = [
                ['o.id','=',$id]
            ];
            $info = Db::table('mp_vip_order')->alias('o')
                ->join("mp_vip v","o.vip_id=v.id","left")
                ->where($where)
                ->field("o.*,v.title,v.detail,v.pic")
                ->find();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$info);
        return $this->fetch();
    }

    protected function asyn_tpl_send($data) {
        $param = http_build_query($data);
        $allow = [
            'rolePass',
            'roleReject'
        ];
        if(!in_array($data['action'],$allow)) {
            $this->msglog('User/asyn_tpl_send',$data['action'] . ' not in allow actions');
            die();
        }
        $fp = @fsockopen('ssl://' . $this->domain, 443, $errno, $errstr, 1);
        if (!$fp){
            $this->msglog('asyn_tpl_send','error fsockopen:' . $this->domain);
        }else{
            stream_set_blocking($fp,0);
            $http = "GET /api/message/" . $data['action'] . "?".$param." HTTP/1.1\r\n";
            $http .= "Host: ".$this->domain."\r\n";
            $http .= "Connection: Close\r\n\r\n";
            fwrite($fp,$http);
            usleep(1000);
            fclose($fp);
        }
    }

    //获取城市列表
    public function getCityList() {
        $val['provinceCode'] = input('post.provinceCode');
        try {
            if($val['provinceCode']) {
                $where = [
                    ['pcode','=',$val['provinceCode']],
                    ['level','=',2]
                ];
            }else {
                return ajax([]);
            }
            $list = Db::table('mp_city')->where($where)->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }
    //获取区列表
    public function getRegionList() {
        $val['cityCode'] = input('post.cityCode');
        try {
            if($val['cityCode']) {
                $where = [
                    ['pcode','=',$val['cityCode']],
                    ['level','=',3]
                ];
            }else {
                return ajax([]);
            }
            $list = Db::table('mp_city')->where($where)->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }



}