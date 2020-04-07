<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/4/12
 * Time: 9:53
 */
namespace app\admin\controller;

use think\Db;
class Activity extends Base {

    public function activityList() {

        $param['status'] = input('param.status','');
        $param['datetype'] = input('param.datetype','1');
        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $param['search'] = input('param.search');
        $param['status'] = input('param.status');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [
            ['a.del','=',0]
        ];

        if(!is_null($param['status']) && $param['status'] !== '') {
            $where[] = ['a.status','=',$param['status']];
        }

        switch ($param['datetype']) {
            case '1':$condition_date = 'a.create_time';break;
            case '2':$condition_date = 'a.start_time';break;
            case '3':$condition_date = 'a.deadline';break;
            case '4':$condition_date = 'a.end_time';break;
            default:$condition_date = 'a.create_time';
        }
        switch ($param['status']) {
            case '1'://未开始
                $where[] = ['a.start_time','>',time()];break;
            case '2'://创意中
                $where[] = ['a.start_time','<',time()];
                $where[] = ['a.deadline','>=',time()];break;
            case '3'://已结束
                $where[] = ['a.end_time','<',time()];break;
            default:;
        }
        if($param['datemin']) {
            $where[] = [$condition_date,'>=',strtotime($param['datemin'])];
        }
        if($param['datemax']) {
            $where[] = [$condition_date,'<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])))];
        }

        if($param['search']) {
            $where[] = ['a.title|a.org','like',"%{$param['search']}%"];
        }

        try {
            $count = Db::table('mp_activity')->alias('a')
                ->join('mp_user_role ro','a.uid=ro.uid','left')
                ->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_activity')->alias('a')
                ->join('mp_user_role ro','a.uid=ro.uid','left')
                ->field('a.*,ro.org')
                ->where($where)
                ->order(['a.id'=>'DESC'])->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('param',$param);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
    //活动详情
    public function activityDetail() {
        $id = input('param.id');
        try {
            $info = Db::table('mp_activity')
                ->where('id','=',$id)
                ->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $this->assign('info',$info);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
    //添加活动
    public function activityAdd() {
        if(request()->isPost()) {
            $val['uid'] = input('post.uid',0);
            $val['title'] = input('post.title');
            $val['theme'] = input('post.theme');
            $val['explain'] = input('post.explain');
            $val['linkman'] = input('post.linkman');
            $val['tel'] = input('post.tel');
            $val['email'] = input('post.email');
            $val['start_time'] = input('post.start_time');
            $val['deadline'] = input('post.deadline');
            $val['end_time'] = input('post.end_time');
            $val['desc'] = input('post.desc');
            checkInput($val);
            $val['weixin'] = input('post.weixin');
            $val['status'] = 1;
            $val['start_time'] = strtotime($val['start_time'] . ' 00:00:00');
            $val['deadline'] = strtotime($val['deadline'] . ' 23:59:59');
            $val['end_time'] = strtotime($val['end_time'] . ' 23:59:59');

            $use_video = input('post.use_video',0);
            $cover = input('post.cover');
            $video_url = input('post.video_url');
            try {
                if(!$cover) {
                    return ajax('请传入封面图',-1);
                }
                if($use_video) {
                    if(!$video_url) { return ajax('请上传视频',-1); }
                    $val['use_video'] = 1;
                }else {
                    $val['use_video'] = 0;
                }

                $whereUser = [
                    ['id','=',$val['uid']]
                ];
                $org_exist = Db::table('mp_user')->where($whereUser)->find();
                if(!$org_exist) {
                    return ajax('发布人不存在',-1);
                }
                $qiniu_exist = $this->qiniuFileExist($cover);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($cover,'upload/activity/');
                if($qiniu_move['code'] == 0) {
                    $val['cover'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
                if($use_video) {
                    $qiniu_exist = $this->qiniuFileExist($video_url);
                    if($qiniu_exist !== true) {
                        return ajax($qiniu_exist['msg'],-1);
                    }
                    $qiniu_move = $this->moveFile($video_url,'upload/activityvideo/');
                    if($qiniu_move['code'] == 0) {
                        $val['video_url'] = $qiniu_move['path'];
                    }else {
                        return ajax($qiniu_move['msg'],-2);
                    }
                }
                Db::table('mp_activity')->insert($val);
            } catch (\Exception $e) {
                $this->rs_delete($val['cover']);
                if($use_video) {
                    $this->rs_delete($val['video_url']);
                }
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        return $this->fetch();

    }
    //修改活动
    public function activityMod() {
        if(request()->isPost()) {
            $val['uid'] = input('post.uid',0);
            $val['title'] = input('post.title');
            $val['theme'] = input('post.theme');
            $val['explain'] = input('post.explain');
            $val['linkman'] = input('post.linkman');
            $val['tel'] = input('post.tel');
            $val['email'] = input('post.email');
            $val['start_time'] = input('post.start_time');
            $val['deadline'] = input('post.deadline');
            $val['end_time'] = input('post.end_time');
            $val['desc'] = input('post.desc');
            $val['id'] = input('post.id');
            checkInput($val);
            $val['weixin'] = input('post.weixin');
            $val['status'] = 1;
            $val['start_time'] = strtotime($val['start_time'] . ' 00:00:00');
            $val['deadline'] = strtotime($val['deadline'] . ' 23:59:59');
            $val['end_time'] = strtotime($val['end_time'] . ' 23:59:59');
            $use_video = input('post.use_video',0);
            $cover = input('post.cover');
            $video_url = input('post.video_url');
            try {
                if(!$cover) { return ajax('请传入封面图',-1); }
                if($use_video) {
                    if(!$video_url) { return ajax('请上传视频',-1); }
                    $val['use_video'] = 1;
                }else {
                    $val['use_video'] = 0;
                }

                $whereUser = [
                    ['id','=',$val['uid']]
                ];
                $org_exist = Db::table('mp_user')->where($whereUser)->find();
                if(!$org_exist) {
                    return ajax('发布人不存在',-1);
                }

                $where = [
                    ['id','=',$val['id']]
                ];
                $activity_exist = Db::table('mp_activity')->where($where)->find();
                if(!$activity_exist) {
                    return ajax('非法操作',-1);
                }
                $qiniu_exist = $this->qiniuFileExist($cover);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($cover,'upload/activity/');
                if($qiniu_move['code'] == 0) {
                    $val['cover'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }

                if($use_video) {
                    $qiniu_exist = $this->qiniuFileExist($video_url);
                    if($qiniu_exist !== true) {
                        return ajax($qiniu_exist['msg'],-1);
                    }
                    $qiniu_move = $this->moveFile($video_url,'upload/activityvideo/');
                    if($qiniu_move['code'] == 0) {
                        $val['video_url'] = $qiniu_move['path'];
                    }else {
                        return ajax($qiniu_move['msg'],-2);
                    }
                }
                Db::table('mp_activity')->update($val);
            } catch (\Exception $e) {
                if($val['cover'] != $activity_exist['cover']) {
                    $this->rs_delete($val['cover']);
                }
                if($use_video) {
                    if($val['video_url'] != $activity_exist['video_url']) {
                        $this->rs_delete($val['video_url']);
                    }
                }
                return ajax($e->getMessage(), -1);
            }
            if($val['cover'] != $activity_exist['cover']) {
                $this->rs_delete($activity_exist['cover']);
            }
            if($use_video) {
                if($val['video_url'] != $activity_exist['video_url']) {
                    $this->rs_delete($activity_exist['video_url']);
                }
            }
            return ajax();
        }
    }

    //活动展示
    public function activityShow() {
        $map[] = ['id','=',input('post.id',0)];
        try {
            Db::table('mp_activity')->where($map)->update(['show'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //活动隐藏
    public function activityHide() {
        $map[] = ['id','=',input('post.id',0)];
        try {
            Db::table('mp_activity')->where($map)->update(['show'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //置顶、取消置顶
    public function activityRecommend() {
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_activity')->where($where)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            if($exist['recommend'] == 1) {
                Db::table('mp_activity')->where($where)->update(['recommend'=>0]);
                return ajax(0);
            }else {
                Db::table('mp_activity')->where($where)->update(['recommend'=>1]);
                return ajax(1);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
    }

    //作品列表
    public function workList() {
        $param['status'] = input('param.status','');
        $param['activity_id'] = input('param.activity_id');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [
            ['w.del','=',0]
        ];
        if(!is_null($param['status']) && $param['status'] !== '') {
            $where[] = ['a.status','=',$param['status']];
        }
        if($param['activity_id']) {
            $where[] = ['w.activity_id','=',$param['activity_id']];
        }
        if($param['search']) {
            $where[] = ['w.title','like',"%{$param['search']}%"];
        }

        $order = ['w.id'=>'DESC'];

        try {
            $count = Db::table('mp_activity_works')->alias('w')
                ->join('mp_activity a','w.activity_id=a.id','left')
                ->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_activity_works')->alias('w')
                ->join('mp_activity a','w.activity_id=a.id','left')
                ->field('w.*,a.title AS activity_title,a.uid AS org')
                ->where($where)
                ->order($order)->limit(($curr_page - 1)*$perpage,$perpage)->select();
            $whereAc = [
                ['del','=',0]
            ];
            $activitylist = Db::table('mp_activity')->where($whereAc)->field('id,title')->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('param',$param);
        $this->assign('activitylist',$activitylist);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }

    //作品详情
    public function workDetail() {
        $param['id'] = input('param.id','');
        try {
            $where = [
                ['w.id','=',$param['id']]
            ];
            $info = Db::table('mp_activity_works')->alias('w')
                ->join('mp_activity r','w.activity_id=a.id','left')
                ->join('mp_user u','w.uid=u.id','left')
                ->field('w.*,a.title AS activity_title,a.org,u.nickname,u.avatar,i.title AS idea_title,i.content AS idea_content')
                ->where($where)
                ->find();
            if(!$info) {
                die('非法操作');
            }
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$info);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
    //作品编辑
    public function workMod() {
        $val['id'] = input('post.id');
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        checkInput($val);
        try {
            $whereIdea = [['id','=',$val['id']]];
            $idea_exist = Db::table('mp_activity_works')->where($whereIdea)->find();
            if(!$idea_exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_activity_works')->where($whereIdea)->update($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }


    //作品审核-通过
    public function workPass() {
        $whereWorks = [
            ['status','=',0],
            ['id','=',input('post.id',0)]
        ];
        try {
            $exist = Db::table('mp_activity_works')->where($whereWorks)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            $whereReq = [
                ['id','=',$exist['activity_id']]
            ];

            Db::table('mp_activity_works')->where($whereWorks)->update(['status'=>1]);
            Db::table('mp_activity')->where($whereReq)->setInc('works_num',1);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //作品审核-拒绝
    public function workReject() {
        $map = [
            ['status','=',0],
            ['id','=',input('post.id',0)]
        ];
        $reason = input('post.reason','');
        try {
            $exist = Db::table('mp_activity_works')->where($map)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_activity_works')->where($map)->update(['status'=>2,'reason'=>$reason]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //作品显示
    public function workShow() {
        $map[] = ['id','=',input('post.id',0)];
        try {
            Db::table('mp_activity_works')->where($map)->update(['show'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //作品隐藏
    public function workHide() {
        $map[] = ['id','=',input('post.id',0)];
        try {
            Db::table('mp_activity_works')->where($map)->update(['show'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //作品删除-拒绝
    public function workDel() {
        $map = [
            ['id','=',input('post.id',0)]
        ];
        try {
            $exist = Db::table('mp_activity_works')->where($map)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            $whereAc = [
                ['id','=',$exist['activity_id']]
            ];
            Db::table('mp_activity_works')->where($map)->update(['del'=>1]);
            Db::table('mp_activity')->where($whereAc)->setDec('works_num',1);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }





}