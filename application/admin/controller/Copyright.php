<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/4/1
 * Time: 13:35
 */
namespace app\admin\controller;

use think\Db;
class Copyright extends Base {


    public function ipList() {

        $param['show'] = input('param.show','');
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [
            ['i.del','=',0]
        ];

        if(!is_null($param['show']) && $param['show'] !== '') {
            $where[] = ['i.show','=',$param['show']];
        }
        if($param['search']) {
            $where[] = ['i.title','like',"%{$param['search']}%"];
        }

        try {
            $count = Db::table('mp_ip')->alias('i')
                ->join('mp_ip_cate c','i.cate_id=c.id','left')
                ->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_ip')->alias('i')
                ->join('mp_ip_cate c','i.cate_id=c.id','left')
                ->field('i.*,c.cate_name')
                ->where($where)
                ->order(['i.id'=>'DESC'])->limit(($curr_page - 1)*$perpage,$perpage)->select();
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
    public function ipDetail() {
        $id = input('param.id');
        try {
            $info = Db::table('mp_ip')
                ->where('id','=',$id)
                ->find();
            $cate_list = Db::table('mp_ip_cate')->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $this->assign('info',$info);
        $this->assign('cate_list',$cate_list);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
    //添加活动
    public function ipAdd() {
        if(request()->isPost()) {
            $val['cate_id'] = input('post.cate_id',0);
            $val['title'] = input('post.title');
            $val['obligee'] = input('post.obligee');
            $val['obligee_status'] = input('post.obligee_status');
            $val['obligee_range'] = input('post.obligee_range');
            $val['area'] = input('post.area');
            $val['desc'] = input('post.desc');
            checkInput($val);
            $val['forever'] = input('post.forever',0);
            if(!$val['forever']) {
                $val['start_time'] = input('post.start_time');
                $val['end_time'] = input('post.end_time');
                if(!$val['start_time'] ||!$val['end_time']) {
                    return ajax('请选择期限时间',-1);
                }
            }
            $val['content'] = input('post.content');
            $val['create_time'] = time();
            $cover = input('post.cover');
            try {
                if(!$cover) {
                    return ajax('请传入封面图',-1);
                }
                $qiniu_exist = $this->qiniuFileExist($cover);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }

                $qiniu_move = $this->moveFile($cover,'upload/ip/');

                if($qiniu_move['code'] == 0) {
                    $val['cover'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }

                Db::table('mp_ip')->insert($val);
            } catch (\Exception $e) {
                $this->rs_delete($val['cover']);
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        try {
            $cate_list = Db::table('mp_ip_cate')->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('cate_list',$cate_list);
        return $this->fetch();

    }
    //修改活动
    public function ipMod() {
        if(request()->isPost()) {
            $val['cate_id'] = input('post.cate_id',0);
            $val['title'] = input('post.title');
            $val['obligee'] = input('post.obligee');
            $val['obligee_status'] = input('post.obligee_status');
            $val['obligee_range'] = input('post.obligee_range');
            $val['area'] = input('post.area');
            $val['desc'] = input('post.desc');
            $val['id'] = input('post.id');
            checkInput($val);
            $val['forever'] = input('post.forever',0);
            if(!$val['forever']) {
                $val['start_time'] = input('post.start_time');
                $val['end_time'] = input('post.end_time');
                if(!$val['start_time'] ||!$val['end_time']) {
                    return ajax('请选择期限时间',-1);
                }
            }
            $val['content'] = input('post.content');
            $val['create_time'] = time();
            $cover = input('post.cover');
            try {
                $whereIp = [
                    ['id','=',$val['id']]
                ];
                $exist = Db::table('mp_ip')->where($whereIp)->find();
                if(!$exist) {
                    return ajax('invalid id',-1);
                }

                if(!$cover) {
                    return ajax('请传入封面图',-1);
                }

                $qiniu_exist = $this->qiniuFileExist($cover);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }

                $qiniu_move = $this->moveFile($cover,'upload/ip/');

                if($qiniu_move['code'] == 0) {
                    $val['cover'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
                Db::table('mp_ip')->where($whereIp)->update($val);
            } catch (\Exception $e) {
                if($val['cover'] !== $exist['cover']) {
                    $this->rs_delete($val['cover']);
                }
                return ajax($e->getMessage(), -1);
            }
            if($val['cover'] !== $exist['cover']) {
                $this->rs_delete($exist['cover']);
            }
            return ajax();
        }
    }

    //活动展示
    public function ipShow() {
        $map[] = ['id','=',input('post.id',0)];
        try {
            Db::table('mp_ip')->where($map)->update(['show'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //活动隐藏
    public function ipHide() {
        $map[] = ['id','=',input('post.id',0)];
        try {
            Db::table('mp_ip')->where($map)->update(['show'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //置顶、取消置顶
    public function ipRecommend() {
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_ip')->where($where)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            if($exist['recommend'] == 1) {
                Db::table('mp_ip')->where($where)->update(['recommend'=>0]);
                return ajax(0);
            }else {
                Db::table('mp_ip')->where($where)->update(['recommend'=>1]);
                return ajax(1);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
    }





    //分类列表
    public function cateList() {
        $where = [];
        try {
            $list = Db::table('mp_ip_cate')->where($where)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
//添加分类
    public function cateAdd() {
        return $this->fetch();
    }
//添加分类POST
    public function cateAddPost() {
        $val['cate_name'] = input('post.cate_name');
        checkInput($val);
        $icon = input('post.icon');
        try {
            if($icon) {
                $qiniu_exist = $this->qiniuFileExist($icon);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($icon,'upload/ipcate/');
                if($qiniu_move['code'] == 0) {
                    $val['icon'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
            }
            Db::table('mp_ip_cate')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['icon'])) {
                $this->rs_delete($val['icon']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([]);
    }
//分类详情
    public function cateDetail() {
        $id = input('param.id');
        try {
            $info = Db::table('mp_ip_cate')->where('id',$id)->find();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$info);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
//修改分类POST
    public function cateModPost() {
        $val['cate_name'] = input('post.cate_name');
        $val['id'] = input('post.id',0);
        checkInput($val);
        $icon = input('post.icon');

        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_ip_cate')->where($where)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            if($icon) {
                $qiniu_exist = $this->qiniuFileExist($icon);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($icon,'upload/ipcate/');
                if($qiniu_move['code'] == 0) {
                    $val['icon'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
            }
            Db::table('mp_ip_cate')->where($where)->update($val);
        }catch (\Exception $e) {
            if(isset($val['icon']) && $val['icon'] != $exist['icon']) {
                $this->rs_delete($val['icon']);
            }
            return ajax($e->getMessage(),-1);
        }
        if(isset($val['icon']) && $val['icon'] != $exist['icon']) {
            $this->rs_delete($exist['icon']);
        }
        return ajax([]);
    }
//隐藏分类
    public function cateHide() {
        $id = input('post.id');
        try {
            $whereCate = [
                ['id','=',$id]
            ];
            $exist = Db::table('mp_ip_cate')->where($whereCate)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_ip_cate')->where($whereCate)->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//显示分类
    public function cateShow() {
        $id = input('post.id');
        try {
            $whereCate = [
                ['id','=',$id]
            ];
            $exist = Db::table('mp_ip_cate')->where($whereCate)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_ip_cate')->where($whereCate)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//删除分类
    public function cateDel() {
        $id = input('post.id');
        try {
            $whereCate = [
                ['id','=',$id]
            ];
            $exist = Db::table('mp_ip_cate')->where($whereCate)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_ip_cate')->where($whereCate)->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        @$this->rs_delete($exist['icon']);
        return ajax();
    }

    //推荐分类
    public function cateRecommend() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id]
        ];
        try {
            $goods_exist = Db::table('mp_ip_cate')->where($map)->find();
            if($goods_exist['recommend'] == 1) {
                Db::table('mp_ip_cate')->where($map)->update(['recommend'=>0]);
                return ajax(0);
            }else {
                Db::table('mp_ip_cate')->where($map)->update(['recommend'=>1]);
                return ajax(1);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

    }

    public function consultList() {
        $param['search'] = input('param.search','');
        $param['contact'] = input('param.contact','');
        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $page['query'] = http_build_query(input('param.'));
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];
        if($param['search']) {
            $where[] = ['i.title','like',"%{$param['search']}%"];
        }
        if($param['contact'] !== '') {
            $where[] = ['c.contact','=',$param['contact']];
        }
        if($param['datemin']) {
            $where[] = ['c.create_time','>=',strtotime($param['datemin'])];
        }
        if($param['datemax']) {
            $where[] = ['c.create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])))];
        }
        try {
            $count = Db::table('mp_ip_consult')->alias('c')
                ->join('mp_ip i','c.ip_id=i.id','left')
                ->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_ip_consult')->alias('c')
                ->join('mp_ip i','c.ip_id=i.id','left')
                ->where($where)
                ->field('c.*,i.title')
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('param',$param);
        return $this->fetch();
    }

    public function contact() {
        $id = input('post.id');
        try {
            $where = [
                ['id','=',$id]
            ];
            Db::table('mp_ip_consult')->where($where)->update(['contact'=>1,'contact_time'=>time()]);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }


}