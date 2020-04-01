<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/8
 * Time: 11:11
 */

namespace app\api\controller;
use my\Sendsms;
use think\Db;
use think\Exception;

class Api extends Base
{
    //获取轮播图列表
    public function slideList() {
        $where = [
            ['status', '=', 1],
            ['type', '=', 1]
        ];
        try {
            $list = Db::table('mp_slideshow')->where($where)
                ->field('id,title,url,pic')
                ->order(['sort' => 'ASC'])->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }

    //上传参赛作品
    public function uploadWorks() {
        $val['uid'] = $this->myinfo['id'];
        $val['req_id'] = input('post.req_id');
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        checkPost($val);
        $val['idea_id'] = input('post.idea_id');
        $val['create_time'] = time();
        $user = $this->myinfo;
        $image = input('post.pics', []);
        if(!$this->msgSecCheck($val['title'])) {
            return ajax('标题包含敏感词',63);
        }
        if(!$this->msgSecCheck($val['desc'])) {
            return ajax('内容包含敏感词',64);
        }
        if (is_array($image) && !empty($image)) {
            if (count($image) > 9) {
                return ajax('最多上传9张图片', 67);
            }
        } else {
            return ajax('请传入图片', 3);
        }
        try {
            $whereReq = [
                ['id', '=', $val['req_id']]
            ];
            $exist = Db::table('mp_req')->where($whereReq)->find();
            if (!$exist) {
                return ajax('非法参数req_id', -4);
            }
            if ($exist['start_time'] > time()) {
                return ajax('活动未开始', 26);
            }
            if ($exist['end_time'] <= time()) {
                return ajax('活动已结束', 25);
            }
            if ($exist['deadline'] <= time()) {
                return ajax('投稿时间已结束', 27);
            }
            if ($user['role'] != 2) {
                return ajax('只有设计师可以参加', 28);
            }
            if (!$user['user_auth']) {
                return ajax('用户未授权', 56);
            }
            if ($user['role_check'] != 2) {
                return ajax('角色还未认证', 29);
            }
            if($val['idea_id']) {
                $whereIdea = [
                    ['id','=',$val['idea_id']]
                ];
                $idea_exist = Db::table('mp_req_idea')->where($whereIdea)->find();
                if(!$idea_exist) {
                    return ajax('非法参数idea_id',-4);
                }
                $whereWork = [
                    ['req_id', '=', $val['req_id']],
                    ['idea_id', '=', $val['idea_id']],
                    ['uid', '=', $this->myinfo['id']]
                ];
                $workExist = Db::table('mp_req_works')->where($whereWork)->find();
                if ($workExist) {
                    return ajax('已投过此创意,不可重复投稿', 58);
                }
            }
            //七牛云上传多图
            $image_array = [];
            foreach ($image as $v) {
                $qiniu_exist = $this->qiniuFileExist($v);
                if($qiniu_exist !== true) {
                    return ajax('图片已失效请重新上传',66);
                }
            }
            foreach ($image as $v) {
                $qiniu_move = $this->moveFile($v,'upload/works/');
                if($qiniu_move['code'] == 0) {
                    $image_array[] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],101);
                }
            }
            $val['pics'] = serialize($image_array);

            Db::table('mp_req_works')->insert($val);
            Db::table('mp_req')->where($whereReq)->setInc('works_num',1);
            if($val['idea_id']) {
                Db::table('mp_req_idea')->where($whereIdea)->setInc('works_num',1);
            }
        } catch (\Exception $e) {
            foreach ($image_array as $v) {
                $this->rs_delete($v);
            }
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //获取参赛作品列表
    public function worksList() {
        $val['req_id'] = input('post.req_id');
        $val['idea_id'] = input('post.idea_id');
        $val['order'] = input('post.order',1);
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        $where = [
            ['w.status','=',1],
            ['w.del','=',0]
        ];
        try {
            if($val['req_id']) {
                $where[] = ['w.req_id', '=', $val['req_id']];
            }
            if($val['idea_id']) {
                $where[] = ['w.idea_id', '=', $val['idea_id']];
            }
            if($val['order'] == 1) {
                $order = ['w.id'=>'DESC'];
            }else {
                $order = ['w.vote'=>'DESC'];
            }
            $list = Db::table('mp_req_works')->alias('w')
                ->join("mp_req r", "w.req_id=r.id", "left")
                ->join("mp_user u", "w.uid=u.id", "left")
                ->where($where)
                ->field("w.id,w.title,w.vote,w.pics,w.bid_num,w.desc,w.create_time,u.nickname,u.avatar")
                ->order($order)
                ->limit(($curr_page - 1) * $perpage, $perpage)->select();
            $myvote = Db::table('mp_works_vote')->where('uid','=',$this->myinfo['id'])->column('work_id');
            foreach ($list as &$v) {
                if(in_array($v['id'],$myvote)) {
                    $v['if_vote'] = true;
                }else {
                    $v['if_vote'] = false;
                }
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
        }
        return ajax($list);
    }
    //参赛作品详情
    public function worksDetail() {
        $val['id'] = input('post.id');
        checkPost($val);
        try {
            //作品是否存在
            $whereWorks = [
                ['w.id', '=',$val['id']],
                ['w.del', '=',0]
            ];
            $work_exist = Db::table('mp_req_works')->alias('w')
                ->join("mp_req_idea i", "w.idea_id=i.id", "left")
                ->join("mp_user u", "w.uid=u.id", "left")
                ->where($whereWorks)
                ->field("w.id,w.uid,w.title,w.desc,w.pics,w.vote,w.bid_num,w.req_id,w.create_time,w.status,w.reason,u.avatar,u.nickname,i.title AS idea_title,i.vote AS idea_vote")
                ->find();
            if (!$work_exist) {
                return ajax($val['id'], 89);
            }
            //我是否投票
            $myvote = Db::table('mp_works_vote')->where('uid','=',$this->myinfo['id'])->column('work_id');
            if(in_array($work_exist['id'],$myvote)) {
                $work_exist['if_vote'] = true;
            }else {
                $work_exist['if_vote'] = false;
            }
            //我是否竞标
            $bidding_exist = Db::table('mp_bidding')->where([
                ['work_id', '=', $val['id']],
                ['uid', '=', $this->myinfo['id']]
            ])->find();
            $where_req = [['id','=',$work_exist['req_id']]];
            $req_exist = Db::table('mp_req')->where($where_req)->find();
            if ($bidding_exist) {
                $work_exist['bidding_btn'] = false;
                if ($req_exist['end_time'] <= time()) {
                    $work_exist['bidding_btn'] = true;
                }
            } else {
                $work_exist['bidding_btn'] = true;
            }
            $whereFocus = [
                ['uid','=',$this->myinfo['id']],
                ['to_uid','=',$work_exist['uid']]
            ];
            $focus_exist = Db::table('mp_user_focus')->where($whereFocus)->find();
            if($focus_exist) {
                $work_exist['ifocus'] = true;
            }else {
                $work_exist['ifocus'] = false;
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $work_exist['req_uid'] = $req_exist['uid'];
        $work_exist['req_title'] = $req_exist['title'];
        $work_exist['pics'] = unserialize($work_exist['pics']);
        return ajax($work_exist);
    }
    //作品投票
    public function worksVote()
    {
        $val['work_id'] = input('post.work_id');
        checkPost($val);
        $user = $this->myinfo;
        try {
            $whereWork = [
                ['id', '=', $val['work_id']]
            ];
            $workExist = Db::table('mp_req_works')->where($whereWork)->find();
            if (!$workExist) {
                return ajax($val['work_id'], -4);
            }

            $whereVote = [
                ['work_id', '=', $val['work_id']],
                ['uid', '=', $this->myinfo['id']]
            ];
            $vote_exist = Db::table('mp_works_vote')->where($whereVote)->find();
            if ($vote_exist) {
                return ajax('只能投一次票', 32);
            }

            $whereReq = [
                ['id', '=', $workExist['req_id']]
            ];
            $req_exist = Db::table('mp_req')->where($whereReq)->find();
            if ($req_exist['start_time'] > time()) {
                return ajax('活动未开始', 26);
            }
            if ($req_exist['end_time'] <= time()) {
                return ajax('活动已结束', 25);
            }
            if($req_exist['deadline'] > time()) {
                return ajax('创意期间不可投票',71);
            }
            if ($req_exist['vote_time'] <= time()) {
                return ajax('投票时间已结束', 30);
            }
            $insert_data = [
                'work_id' => $val['work_id'],
                'uid' => $this->myinfo['id'],
                'vip' => $user['vip'],
                'req_id' => $workExist['req_id'],
                'create_time' => time()
            ];
            Db::table('mp_works_vote')->insert($insert_data);
            Db::table('mp_req_works')->where($whereWork)->setInc('vote', 1);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }





























    //设计师列表
    public function designerList() {
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        try {
            $where = [
                ['role', '=', 2],
                ['role_check','=',2]
            ];
            $whereFocus = [
                ['uid','=',$this->myinfo['id']]
            ];
            $myFocus = Db::table('mp_user_focus')->where($whereFocus)->column('to_uid');
            $list = Db::table('mp_user')
                ->where($where)
                ->field("id,nickname,avatar,idea_num,works_num,focus,level")
                ->limit(($curr_page - 1) * $perpage, $perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            if(in_array($v['id'],$myFocus)) {
                $v['if_focus'] = true;
            }else {
                $v['if_focus'] = false;
            }
        }
        return ajax($list);
    }
    //博物馆列表
    public function bwgList() {
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        try {
            $where = [
                ['u.role', '=', 1],
                ['u.role_check','=',2]
            ];
            $list = Db::table('mp_user')->alias('u')
                ->join('mp_user_role r','u.id=r.uid','left')
                ->where($where)
                ->field("u.id,u.org,u.req_num,u.focus,u.level,r.cover")
                ->limit(($curr_page - 1) * $perpage, $perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }
    //工厂列表
    public function factoryList() {
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        $province_code = input('post.province_code');
        $city_code = input('post.city_code');
        $region_code = input('post.region_code');
        $search = input('post.search');
        try {
            $where = [
                ['u.role', '=', 3],
                ['u.role_check','=',2]
            ];
            $order = ['u.id'=>'DESC'];
            if ($province_code) {
                $where[] = ['r.province_code','=',$province_code];
            }
            if ($city_code) {
                $where[] = ['r.city_code','=',$city_code];
            }
            if ($region_code) {
                $where[] = ['r.region_code','=',$region_code];
            }
            if ($search) {
                $where[] = ['u.org','like',"%{$search}%"];
            }
            $list = Db::table('mp_user')->alias('u')
                ->join('mp_user_role r','u.id=r.uid','left')
                ->where($where)
                ->order($order)
                ->field("u.id,u.org,u.bid_num,u.focus,u.level,r.cover")
                ->limit(($curr_page - 1) * $perpage, $perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }


    //充值类目列表
    public function getVipList() {
        $where = [
            ['status','=',1]
        ];
        try {
            $list = Db::table('mp_vip')->where($where)
                ->field('id,title,detail,price,pic,days')
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }
    //充值
    public function recharge()
    {
        $val['vip_id'] = input('post.vip_id');
        $val['name'] = input('post.name');
        $val['tel'] = input('post.tel');
        $val['address'] = input('post.address');
        $val['uid'] = $this->myinfo['id'];

        checkPost($val);
        try {
            $exist = Db::table('mp_vip')->where('id', $val['vip_id'])->find();
            if (!$exist) {
                return ajax('invalid vip_id', -4);
            }
            $val['price'] = $exist['price'];
            $val['days'] = $exist['days'];
            $val['create_time'] = time();
            $val['order_sn'] = create_unique_number('v');
            Db::table('mp_vip_order')->insert($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($val);

    }

    //角色充值类目列表
    public function getRole3LevelList() {
        try {
            $list = Db::table('mp_role3_level')->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }

    public function role3Recharge() {
        $val['level_id'] = input('post.level_id');
        checkPost($val);
        try {
            if($this->myinfo['role'] != 3 || $this->myinfo['role_check'] != 2) {
                return ajax('套餐仅限通过审核的工厂',90);
            }
            $whereLevel = [
                ['id','=',$val['level_id']]
            ];
            $level_exist = Db::table('mp_role3_level')->where($whereLevel)->find();
            if(!$level_exist) {
                return ajax($val['level_id'],-4);
            }
            $whereCurr = [
                ['uid','=',$this->myinfo['id']],
                ['end_time','>',time()]
            ];
            $curr_exist = Db::table('mp_role3_curr')->where($whereCurr)->find();
            if($curr_exist) {
                return ajax('当前套餐结束前无法购买新套餐',91);
            }
            $val['uid'] = $this->myinfo['id'];
            $val['pay_order_sn'] = create_unique_number('');
            $val['pay_price'] = $level_exist['price'];
            $val['role_level'] = $level_exist['level'];
            $val['days'] = $level_exist['days'];
            $val['role_level'] = $level_exist['level'];
            $val['create_time'] = time();
            Db::table('mp_role3_order')->insert($val);

        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($val['pay_order_sn']);
    }









    //收集formid
    public function collectFormid() {
        $val['formid'] = input('post.formid');
        checkPost($val);
        if($val['formid'] == 'the formId is a mock one') {
            return ajax();
        }
        $val['uid'] = $this->myinfo['id'];
        $val['create_time'] = time();
        try {
            Db::table('mp_formid')->insert($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($val);
    }

    //获取活动详情
    public function getAcInfo() {
        try {
            $whereAc = [
                ['id','=',1]
            ];
            $info = Db::table('mp_tmp_ac')->where($whereAc)->field('id,title,pics,content')->find();
            if(!$info) {
                return ajax('activity not exists',-4);
            }
            $info['pics'] = unserialize($info['pics']);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }

    //参加活动发送短信
    public function joinAcSendSms() {
        $val['tel'] = input('post.tel');
        checkPost($val);
        $sms = new Sendsms();
        $tel = $val['tel'];

        if(!is_tel($tel)) {
            return ajax('invalid tel',6);
        }
        try {
            $code = mt_rand(100000,999999);
            $insert_data = [
                'tel' => $tel,
                'code' => $code,
                'create_time' => time()
            ];
            $sms_data['tel'] = $val['tel'];
            $sms_data['param'] = [
                'code' => $code
            ];
            $whereTel = [
                ['tel','=',$tel]
            ];

            $exist = Db::table('mp_verify')->where($whereTel)->find();
            if($exist) {

                if((time() - $exist['create_time']) < 60) {
                    return ajax('1分钟内不可重复发送',11);
                }
                $res = $sms->send($sms_data);

                if($res->Code === 'OK') {
                    Db::table('mp_verify')->where($whereTel)->update($insert_data);
                    return ajax();
                }else {
                    return ajax($res->Message,12);
                }
            }else {
                $res = $sms->send($sms_data,'SMS_178465508');
                if($res->Code === 'OK') {
                    Db::table('mp_verify')->insert($insert_data);
                    return ajax();
                }else {
                    return ajax($res->Message,12);
                }
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }

    //参加活动
    public function joinAc() {
        $val['name'] = input('post.name');
        $val['sex'] = input('post.sex');
        $val['tel'] = input('post.tel');
        $val['code'] = input('post.code');
        checkPost($val);
        $val['uid'] = $this->myinfo['id'];
        $val['weixin'] = input('post.weixin','');
        $val['create_time'] = date('Y-m-d H:i:s');

        if (!is_tel($val['tel'])) {
            return ajax('无效的手机号', 6);
        }
        try {
            //验证短信验证码
            $whereCode = [
                ['tel','=',$val['tel']],
                ['code','=',$val['code']]
            ];
            $code_exist = Db::table('mp_verify')->where($whereCode)->find();
            if($code_exist) {
                if((time() - $code_exist['create_time']) > 60*5) {
                    return ajax('验证码已过期',17);
                }
            }else {
                return ajax('验证码无效',16);
            }

            unset($val['code']);
            Db::table('mp_tmp_sign')->insert($val);
            Db::table('mp_verify')->where($whereCode)->delete();
        }catch (\Exception $e) {//异常删图
            return ajax($e->getMessage(),-1);
        }
        return ajax();

    }

    //获取省级列表
    public function getProvinceList() {
        try {
            $where = [
                ['pcode','=',0]
            ];
            $list = Db::table('mp_city')->where($where)->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }

    //获取城市列表
    public function getCityList() {
        $val['provinceCode'] = input('post.province_code');
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
        $val['cityCode'] = input('post.city_code');
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