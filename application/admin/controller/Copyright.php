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


}