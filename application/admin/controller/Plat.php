<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/8
 * Time: 18:21
 */
namespace app\admin\controller;
use think\Exception;
use think\Db;
class Plat extends Base {
    //轮播图列表
    public function slideList() {
        $param['type'] = input('param.type');
        $page['query'] = http_build_query(input('param.'));
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        try {
            $where = [];
            if($param['type']) {
                $where[] = ['s.type','=',$param['type']];
            }
            $count = Db::table('mp_slideshow')->alias('s')->where($where)->count();

            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_slideshow')->alias('s')
                ->join('mp_slideshow_type t','s.type=t.id','left')
                ->where($where)
                ->field('s.*,t.type_name')
                ->order(['s.sort'=>'ASC'])
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
            $slideshow_type = Db::table('mp_slideshow_type')->select();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('slideshow_type',$slideshow_type);
        $this->assign('page',$page);
        $this->assign('param',$param);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }

    public function slideAdd() {
        try {
            $slideshow_type = Db::table('mp_slideshow_type')->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('slideshow_type',$slideshow_type);
        return $this->fetch();
    }

    //添加轮播图POST
    public function slideAddPost() {
        $val['title'] = input('post.title');
        $val['status'] = input('post.status');
        $val['type'] = input('post.type',1);
        checkInput($val);
        $val['url'] = input('post.url');
        $val['pic'] = input('post.pic');
        if(!$val['pic']) {
            return ajax('请传入图片',-1);
        }
        try {
            $whereType = [
                ['id','=',$val['type']]
            ];
            $type_exist = Db::table('mp_slideshow_type')->where($whereType)->find();
            if(!$type_exist) {
                return ajax('invalid type',-4);
            }
            $val['size'] = $type_exist['size'];
            $qiniu_exist = $this->qiniuFileExist($val['pic']);
            if($qiniu_exist !== true) {
                return ajax($qiniu_exist['msg'],-1);
            }
            $qiniu_move = $this->moveFile($val['pic'],'upload/slide/');
            if($qiniu_move['code'] == 0) {
                $val['pic'] = $qiniu_move['path'];
            }else {
                return ajax($qiniu_move['msg'],-1);
            }
            Db::table('mp_slideshow')->insert($val);
        }catch (\Exception $e) {
            $this->rs_delete($val['pic']);
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    //修改轮播图
    public function slideDetail() {
        $val['id'] = input('param.id');
        try {
            $exist = Db::table('mp_slideshow')->where('id','=',$val['id'])->find();
            $slideshow_type = Db::table('mp_slideshow_type')->select();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        if(!$exist) {
            $this->error('非法操作',url('Banner/slideshow'));
        }
        $this->assign('info',$exist);
        $this->assign('slideshow_type',$slideshow_type);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }

    //修改轮播图POST
    public function slideMod() {
        $val['title'] = input('post.title');
        $val['id'] = input('post.slideid');
        $val['status'] = input('post.status');
        $val['type'] = input('post.type',1);
        checkInput($val);
        $val['url'] = input('post.url');
        $val['pic'] = input('post.pic');
        try {
            $whereType = [
                ['id','=',$val['type']]
            ];
            $type_exist = Db::table('mp_slideshow_type')->where($whereType)->find();
            if(!$type_exist) {
                return ajax('invalid type',-4);
            }
            $val['size'] = $type_exist['size'];
            $exist = Db::table('mp_slideshow')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            $qiniu_exist = $this->qiniuFileExist($val['pic']);
            if($qiniu_exist !== true) {
                return ajax($qiniu_exist['msg'],-1);
            }
            $qiniu_move = $this->moveFile($val['pic'],'upload/slide/');
            if($qiniu_move['code'] == 0) {
                $val['pic'] = $qiniu_move['path'];
            }else {
                return ajax($qiniu_move['msg'],-1);
            }
            Db::table('mp_slideshow')->update($val);
        }catch (Exception $e) {
            if($val['pic'] !== $exist['pic']) {
                $this->rs_delete($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        if($val['pic'] !== $exist['pic']) {
            $this->rs_delete($exist['pic']);
        }
        return ajax([],1);
    }

    //删除轮播图
    public function slide_del() {
        $val['id'] = input('post.slideid');
        checkInput($val);
        try {
            $whereSlide = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_slideshow')->where($whereSlide)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_slideshow')->where($whereSlide)->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $this->rs_delete($exist['pic']);
        return ajax([],1);
    }

    //轮播图排序
    public function sortSlide() {
        $val['id'] = input('post.id');
        $val['sort'] = input('post.sort');
        checkInput($val);
        try {
            Db::table('mp_slideshow')->update($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val);
    }

    //禁用轮播图
    public function slide_stop() {
        $val['id'] = input('post.slideid');
        checkInput($val);
        try {
            $exist = Db::table('mp_slideshow')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_slideshow')->where('id',$val['id'])->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    //启用轮播图
    public function slide_start() {
        $val['id'] = input('post.slideid');
        checkInput($val);
        try {
            $exist = Db::table('mp_slideshow')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_slideshow')->where('id',$val['id'])->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }






    //视频列表
    public function videoList() {
        try {
            $list = Db::table('mp_video')->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
    //修改视频
    public function videoDetail() {
        $val['id'] = input('param.id');
        try {
            $exist = Db::table('mp_video')->where('id','=',$val['id'])->find();
            if(!$exist) {
                die('非法操作');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$exist);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }

    //修改视频
    public function videoMod() {
        $val['title'] = input('post.title');
        $val['id'] = input('post.id');
        checkInput($val);
        $val['video_url'] = input('post.video_url');
        $val['poster'] = input('post.poster');
        if(!$val['poster']) {
            return ajax('请传入封面',-1);
        }
        if(!$val['video_url']) {
            return ajax('请传入视频',-1);
        }
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_video')->where($where)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }

            $qiniu_exist = $this->qiniuFileExist($val['poster']);
            if($qiniu_exist !== true) {
                return ajax($qiniu_exist['msg'],-1);
            }
            $qiniu_exist = $this->qiniuFileExist($val['video_url']);
            if($qiniu_exist !== true) {
                return ajax($qiniu_exist['msg'],-1);
            }

            $qiniu_move = $this->moveFile($val['poster'],'upload/poster/');
            if($qiniu_move['code'] == 0) {
                $val['poster'] = $qiniu_move['path'];
            }else {
                return ajax($qiniu_move['msg'],-2);
            }

            $qiniu_move = $this->moveFile($val['video_url'],'upload/video/');
            if($qiniu_move['code'] == 0) {
                $val['video_url'] = $qiniu_move['path'];
            }else {
                return ajax($qiniu_move['msg'],-2);
            }
            Db::table('mp_video')->update($val);
        } catch (\Exception $e) {
            if($val['poster'] != $exist['poster']) {
                $this->rs_delete($val['poster']);
            }
            if($val['video_url'] !== $exist['video_url']) {
                $this->rs_delete($val['video_url']);
            }
            return ajax($e->getMessage(), -1);
        }
        if($val['poster'] != $exist['poster']) {
            $this->rs_delete($exist['poster']);
        }
        if($val['video_url'] !== $exist['video_url']) {
            $this->rs_delete($exist['video_url']);
        }
        return ajax();
    }


}