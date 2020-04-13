<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/4/7
 * Time: 11:21
 */
namespace app\api\controller;

use think\Db;
use my\Sendsms;
class Activity extends Base {

    //获取轮播图
    public function slideList() {
        $where = [
            ['status', '=', 1],
            ['type', '=', 5]
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

    //活动列表
    public function activityList() {
        $param['status'] = input('post.status','');
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);

        $where = [
            ['del','=',0]
        ];
        if($param['status'] !== '') {
            $where[] = ['status','=',$param['status']];
        }
        try {
            $list = Db::table('mp_activity')
                ->field('id,title,cover,works_num,start_time,end_time,status')
                ->where($where)
                ->order(['id'=>'DESC'])->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        foreach ($list as &$v) {
            $v['status'] = 1;
            if(time() < $v['start_time']) {
                $v['status'] = 0;
            }
            if(time() > $v['end_time']) {
                $v['status'] = 2;
            }
        }
        return ajax($list);
    }

    //活动详情
    public function activityDetail() {
        $param['activity_id'] = input('post.activity_id');
        checkPost($param);
        try {
            $whereAc = [
                ['id','=',$param['activity_id']]
            ];
            $info = Db::table('mp_activity')->where($whereAc)->find();
            if(!$info) {
                return ajax('invalid activity_id',-4);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);

    }

    //上传参赛作品
    public function uploadWorks() {
        $val['uid'] = $this->myinfo['uid'];
        $val['activity_id'] = input('post.activity_id');
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        checkPost($val);
        $val['create_time'] = time();
        $image = input('post.pics', []);
        if(!$this->msgSecCheck($val['title'])) {
            return ajax('标题包含敏感词',37);
        }
        if(!$this->msgSecCheck($val['desc'])) {
            return ajax('内容包含敏感词',38);
        }
        if (is_array($image) && !empty($image)) {
            if (count($image) > 9) {
                return ajax('超出上传数量限制', 39);
            }
        } else {
            return ajax('请传入图片', 3);
        }
        try {
            $whereAc = [
                ['id', '=', $val['activity_id']]
            ];
            $ac_exist = Db::table('mp_activity')->where($whereAc)->find();
            if (!$ac_exist) {
                return ajax('非法参数activity_id', -4);
            }
            if ($ac_exist['start_time'] > time()) {
                return ajax('活动未开始', 40);
            }
            if ($ac_exist['end_time'] <= time()) {
                return ajax('活动已结束', 41);
            }
            if ($ac_exist['deadline'] <= time()) {
                return ajax('投稿时间已结束', 42);
            }
            //七牛云上传多图
            $image_array = [];
            foreach ($image as $v) {
                $qiniu_exist = $this->qiniuFileExist($v);
                if($qiniu_exist !== true) {
                    return ajax('图片已失效请重新上传',5);
                }
            }
            foreach ($image as $v) {
                $qiniu_move = $this->moveFile($v,'upload/works/');
                if($qiniu_move['code'] == 0) {
                    $image_array[] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],100);
                }
            }
            $val['pics'] = serialize($image_array);

            Db::table('mp_activity_works')->insert($val);
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
        $param['activity_id'] = input('post.activity_id');
        $param['search'] = input('post.search');
        $param['order'] = input('post.order',1);
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        $whereWork = [
            ['w.status','=',1],
            ['w.del','=',0]
        ];
        try {
            if($param['activity_id']) {
                $whereWork[] = ['w.activity_id', '=', $param['activity_id']];
            }
            if($param['search']) {
                $whereWork[] = ['w.title', 'like', "%{$param['search']}%"];
            }
            if($param['order'] == 1) {
                $order = ['w.id'=>'DESC'];
            }else {
                $order = ['w.vote'=>'DESC'];
            }
            $list = Db::table('mp_activity_works')->alias('w')
                ->join("mp_activity a", "w.activity_id=a.id", "left")
                ->join("mp_user u", "w.uid=u.id", "left")
                ->where($whereWork)
                ->field("w.id,w.title,w.vote,w.pics,w.desc,w.create_time,u.nickname,u.avatar")
                ->order($order)
                ->limit(($curr_page - 1) * $perpage, $perpage)->select();

            if(!$this->myinfo['uid']) { $this->myinfo['uid'] = -1; }
            $myvote = Db::table('mp_activity_works_vote')->where('uid','=',$this->myinfo['uid'])->column('work_id');
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
        $param['work_id'] = input('post.work_id');
        checkPost($param);
        if(!$this->myinfo['uid']) { $this->myinfo['uid'] = -1; }
        try {
            //作品是否存在
            $whereWorks = [
                ['w.id', '=',$param['work_id']],
                ['w.del', '=',0]
            ];
            $work_exist = Db::table('mp_activity_works')->alias('w')
                ->join("mp_user u", "w.uid=u.id", "left")
                ->where($whereWorks)
                ->field("w.id,w.uid,w.title,w.desc,w.pics,w.vote,w.activity_id,w.create_time,w.status,w.reason,u.avatar,u.nickname")
                ->find();
            if (!$work_exist) {
                return ajax('invalid activity_id', -4);
            }
            //我是否投票
            $myvote = Db::table('mp_activity_works_vote')->where('uid','=',$this->myinfo['uid'])->column('work_id');
            if(in_array($work_exist['id'],$myvote)) {
                $work_exist['if_vote'] = true;
            }else {
                $work_exist['if_vote'] = false;
            }
            $whereFocus = [
                ['uid','=',$this->myinfo['uid']],
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
        $work_exist['pics'] = unserialize($work_exist['pics']);
        return ajax($work_exist);
    }
    //作品投票
    public function worksVote()
    {
        $val['work_id'] = input('post.work_id');
        checkPost($val);
        try {
            $whereWork = [
                ['id', '=', $val['work_id']]
            ];
            $workExist = Db::table('mp_activity_works')->where($whereWork)->find();
            if (!$workExist) {
                return ajax('invalid work_id', -4);
            }
            $whereVote = [
                ['work_id', '=', $val['work_id']],
                ['uid', '=', $this->myinfo['uid']]
            ];
            $vote_exist = Db::table('mp_activity_works_vote')->where($whereVote)->find();
            if ($vote_exist) {
                return ajax('只能投一次票', 43);
            }
            $whereAc = [
                ['id', '=', $workExist['activity_id']]
            ];
            $ac_exist = Db::table('mp_activity')->where($whereAc)->find();
            if ($ac_exist['start_time'] > time()) {
                return ajax('活动未开始', 26);
            }
            if ($ac_exist['end_time'] <= time()) {
                return ajax('活动已结束', 25);
            }
            $insert_data = [
                'work_id' => $val['work_id'],
                'uid' => $this->myinfo['uid'],
                'create_time' => time()
            ];
            Db::table('mp_activity_works_vote')->insert($insert_data);
            Db::table('mp_activity_works')->where($whereWork)->setInc('vote', 1);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    //发送手机短信
    public function sendSms() {
        $val['tel'] = input('post.tel');
        checkPost($val);
        $sms = new Sendsms();
        $tel = $val['tel'];

        if(!is_tel($tel)) {
            return ajax('无效的手机号',6);
        }
        try {
            $code = mt_rand(100000,999999);
            $insert_data = [
                'tel' => $tel,
                'code' => $code,
                'create_time' => time()
            ];
            $sms_data['tpl_code'] = 'SMS_174925606';
            $sms_data['tel'] = $val['tel'];
            $sms_data['param'] = [
                'code' => $code
            ];
            $exist = Db::table('mp_verify')->where('tel','=',$tel)->find();
            if($exist) {
                if((time() - $exist['create_time']) < 60) {
                    return ajax('1分钟内不可重复发送',11);
                }
                $res = $sms->send($sms_data);
                if($res->Code === 'OK') {
                    Db::table('mp_verify')->where('tel',$tel)->update($insert_data);
                    return ajax();
                }else {
                    return ajax($res->Message,-1);
                }
            }else {
                $res = $sms->send($sms_data);
                if($res->Code === 'OK') {
                    Db::table('mp_verify')->insert($insert_data);
                    return ajax();
                }else {
                    return ajax($res->Message,-1);
                }
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }

    //众筹提交表单
    public function activityRelease() {
        $val['uid'] = $this->myinfo['uid'];
        $val['title'] = input('post.title');
        $val['company'] = input('post.company');
        $val['name'] = input('post.name');
        $val['tel'] = input('post.tel');
        $val['email'] = input('post.email');
        $val['code'] = input('post.code');
        checkPost($val);
        $val['desc'] = input('post.desc');
        $val['create_time'] = time();

        if(!is_tel($val['tel'])) {
            return ajax($val['tel'],6);
        }
        if(!is_email($val['email'])) {
            return ajax($val['email'],7);
        }
        try {
            // 检验短信验证码
            $whereCode = [
                ['tel','=',$val['tel']],
                ['code','=',$val['code']]
            ];
            $code_exist = Db::table('mp_verify')->where($whereCode)->find();
            if($code_exist) {
                if((time() - $code_exist['create_time']) > 60*5) {//验证码5分钟过期
                    return ajax('验证码已过期',32);
                }
            }else {
                return ajax('验证码无效',33);
            }
            unset($val['code']);
            Db::table('mp_activity_consult')->insert($val);
            Db::table('mp_verify')->where($whereCode)->delete();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }




}