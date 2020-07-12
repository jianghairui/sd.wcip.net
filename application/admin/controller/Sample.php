<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/19
 * Time: 13:45
 */
namespace app\admin\controller;
use think\Db;
class Sample extends Base {

//样品列表
    public function sampleList() {
        $param['shop_id'] = input('param.shop_id','');
        $param['status'] = input('param.status','');
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',20);
        $where = [
            ['s.del','=',0]
        ];

        if($param['shop_id'] !== '') {
            $where[] = ['s.shop_id','=',$param['shop_id']];
        }
        if($param['status'] !== '') {
            $where[] = ['s.status','=',$param['status']];
        }
        if($param['search']) {
            $where[] = ['s.name','like',"%{$param['search']}%"];
        }

        try {
            $count = Db::table('mp_sample')->alias('s')->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);

            $list = Db::table('mp_sample')->alias('s')
                ->join('mp_user_role r','s.shop_id=r.uid','left')
                ->where($where)
                ->field('s.*,r.name AS role_name,r.org,r.role')
                ->limit(($curr_page - 1)*$perpage,$perpage)
                ->order(['s.id'=>'DESC'])
                ->select();
            $whereShop = [
                ['status','=',1],
                ['role','<>',0]
            ];
            $shoplist = Db::table('mp_user')->where($whereShop)->field('id,nickname,org,role')->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        foreach ($list as &$v) {
            $v['poster'] = unserialize($v['pics'])[0];
        }
        $this->assign('shoplist',$shoplist);
        $this->assign('list',$list);
        $this->assign('param',$param);
        $this->assign('page',$page);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
//添加样品
    public function sampleAdd() {
        try {
            $whereShop = [
                ['role','<>',0]
            ];
            $shop_list = Db::table('mp_user')->where($whereShop)->field('id,nickname,org,role')->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('shop_list',$shop_list);
        return $this->fetch();
    }
//样品详情
    public function sampleDetail() {
        $id = input('param.id');
        try {
            $info = Db::table('mp_sample')->where('id','=',$id)->find();
            if(!$info) {
                die('非法参数');
            }
            $whereShop = [
                ['role','<>',0]
            ];
            $shop_list = Db::table('mp_user')->where($whereShop)->field('id,nickname,org,role')->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$info);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        $this->assign('shop_list',$shop_list);
        return $this->fetch();
    }
//添加样品POST
    public function sampleAddPost() {
        $val['name'] = input('post.name');
        $val['stock'] = input('post.stock');
        $val['shop_id'] = input('post.shop_id',0);
        $val['create_time'] = time();
        checkInput($val);
        $val['detail'] = input('post.detail');
        $use_video = input('post.use_video',0);
        $video_url = input('post.video_url');
        $poster = input('post.poster');
        $image = input('post.pic_url',[]);

        if(!$poster) { return ajax('请上传封面',-1); }
        try {
            $qiniu_exist = $this->qiniuFileExist($poster);
            if($qiniu_exist !== true) {
                return ajax($qiniu_exist['msg'],-1);
            }
            if($use_video) {
                if(!$video_url) { return ajax('请上传视频文件',-1); }
                $qiniu_exist = $this->qiniuFileExist($video_url);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $val['use_video'] = 1;
            }else {
                $val['use_video'] = 0;

            }
            $limit = 9;
            if(is_array($image) && !empty($image)) {
                if(count($image) > $limit) {
                    return ajax('最多上传'.$limit.'张图片',-1);
                }
                foreach ($image as $v) {
                    $qiniu_exist = $this->qiniuFileExist($v);
                    if($qiniu_exist !== true) {
                        return ajax('图片已失效请重新上传',-1);
                    }
                }
            }else {
                return ajax('请上传样品图片',-1);
            }

            $qiniu_move = $this->moveFile($poster,'upload/sampleposter/');
            if($qiniu_move['code'] == 0) {
                $val['poster'] = $qiniu_move['path'];
            }else {
                return ajax($qiniu_move['msg'],-1);
            }
            if($use_video) {
                $qiniu_move = $this->moveFile($video_url,'upload/samplevideo/');
                if($qiniu_move['code'] == 0) {
                    $val['video_url'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
            }
            $image_array = [];
            foreach ($image as $v) {
                $qiniu_move = $this->moveFile($v,'upload/sample/');
                if($qiniu_move['code'] == 0) {
                    $image_array[] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-3);
                }
            }
            $val['pics'] = serialize($image_array);
            Db::table('mp_sample')->insert($val);
        }catch (\Exception $e) {
            $this->rs_delete($val['poster']);
            if($use_video) {
                $this->rs_delete($val['video_url']);
            }
            foreach ($image_array as $v) {
                $this->rs_delete($v);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
//修改样品POST
    public function sampleMod() {
        $val['name'] = input('post.name');
        $val['stock'] = input('post.stock');
        $val['shop_id'] = input('post.shop_id',0);
        $val['id'] = input('post.id');
        $val['create_time'] = time();
        checkInput($val);
        $val['detail'] = input('post.detail');
        $use_video = input('post.use_video',0);
        $video_url = input('post.video_url');
        $poster = input('post.poster');
        if(!$poster) { return ajax('请上传封面',-1); }
        $image = input('post.pic_url',[]);

        try {
            $map = [
                ['id','=',$val['id']],
                ['del','=',0]
            ];
            $exist = Db::table('mp_sample')->where($map)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }

            $qiniu_exist = $this->qiniuFileExist($poster);//七牛云文件 poster 是否存在
            if($qiniu_exist !== true) {
                return ajax($qiniu_exist['msg'],-1);
            }
            if($use_video) {
                if(!$video_url) { return ajax('请上传视频文件',-1); }
                $qiniu_exist = $this->qiniuFileExist($video_url);//七牛云文件 video_url 是否存在
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $val['use_video'] = 1;
            }else {
                $val['use_video'] = 0;
            }
            $limit = 9;
            if(is_array($image) && !empty($image)) {
                if(count($image) > $limit) {
                    return ajax('最多上传'.$limit.'张图片',-1);
                }
                foreach ($image as $v) {
                    $qiniu_exist = $this->qiniuFileExist($v);
                    if($qiniu_exist !== true) {
                        return ajax('图片已失效请重新上传',-1);
                    }
                }
            }else {
                return ajax('请上传样品图片',-1);
            }

            $poster_move = $this->moveFile($poster,'upload/sampleposter/');
            if($poster_move['code'] == 0) {
                $val['poster'] = $poster_move['path'];
            }else {
                return ajax($poster_move['msg'],-1);
            }
            if($use_video) {
                $video_move = $this->moveFile($video_url,'upload/samplevideo/');
                if($video_move['code'] == 0) {
                    $val['video_url'] = $video_move['path'];
                }else {
                    return ajax($video_move['msg'],-2);
                }
            }
            $old_pics = unserialize($exist['pics']);
            $image_array = [];

            foreach ($image as $v) {
                $image_move = $this->moveFile($v,'upload/sample/');
                if($image_move['code'] == 0) {
                    $image_array[] = $image_move['path'];
                }else {
                    return ajax($image_move['msg'],-2);
                }
            }
            $val['pics'] = serialize($image_array);
            Db::table('mp_sample')->where($map)->update($val);
        }catch (\Exception $e) {
            if($val['poster'] !== $exist['poster']) {
                $this->rs_delete($val['poster']);
            }
            if($use_video && $val['video_url'] !== $exist['video_url']) {
                $this->rs_delete($val['video_url']);
            }
            foreach ($image_array as $v) {
                if(!in_array($v,$old_pics)) {
                    $this->rs_delete($v);
                }
            }
            return ajax($e->getMessage(),-1);
        }
        if($val['poster'] !== $exist['poster']) {
            $this->rs_delete($exist['poster']);
        }
        if($use_video && $val['video_url'] !== $exist['video_url']) {
            $this->rs_delete($exist['video_url']);
        }
        foreach ($old_pics as $v) {
            if(!in_array($v,$image_array)) {
                $this->rs_delete($v);
            }
        }
        return ajax([],1);
    }
//下架
    public function sampleHide() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id],
            ['status','=',1]
        ];
        try {
            Db::table('mp_sample')->where($map)->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//上架
    public function sampleShow() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id],
            ['status','=',0]
        ];
        try {
            Db::table('mp_sample')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//删除样品
    public function sampleDel() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id]
        ];
        try {
            $res = Db::table('mp_sample')->where($map)->update(['del'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax();
        }else {
            return ajax('共修改0条记录',-1);
        }
    }
    //样品排序
    public function sortSample() {
        $val['id'] = input('post.id');
        $val['sort'] = input('post.sort');
        checkInput($val);
        try {
            Db::table('mp_sample')->update($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val);
    }

    //领取记录
    public function sampleRecord() {
        $param['shop_id'] = input('param.shop_id','');
        $param['status'] = input('param.status','');
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',20);
        $where = [
            ['s.del','=',0]
        ];

        if($param['shop_id'] !== '') {
            $where[] = ['s.shop_id','=',$param['shop_id']];
        }
        if($param['status'] !== '') {
            $where[] = ['s.status','=',$param['status']];
        }
        if($param['search']) {
            $where[] = ['s.name','like',"%{$param['search']}%"];
        }

        die('TODO');
        try {
            $count = Db::table('mp_sample')->alias('s')->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);

            $list = Db::table('mp_sample')->alias('s')
                ->join('mp_user_role r','s.shop_id=r.uid','left')
                ->where($where)
                ->field('s.*,r.name AS role_name,r.org,r.role')
                ->limit(($curr_page - 1)*$perpage,$perpage)
                ->order(['s.id'=>'DESC'])
                ->select();
            $whereShop = [
                ['status','=',1],
                ['role','<>',0]
            ];
            $shoplist = Db::table('mp_user')->where($whereShop)->field('id,nickname,org,role')->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        foreach ($list as &$v) {
            $v['poster'] = unserialize($v['pics'])[0];
        }
        $this->assign('shoplist',$shoplist);
        $this->assign('list',$list);
        $this->assign('param',$param);
        $this->assign('page',$page);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }


}