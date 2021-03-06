<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2019/11/5
 * Time: 17:50
 */
namespace app\admin\controller;

use think\Db;
class Xuqiu extends Base {

    public function xuqiuList() {

        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [
            ['x.del','=',0]
        ];

        if($param['datemin']) {
            $where[] = ['x.create_time','>=',date('Y-m-d 00:00:00',strtotime($param['datemin']))];
        }

        if($param['datemax']) {
            $where[] = ['x.create_time','<=',date('Y-m-d 23:59:59',strtotime($param['datemax']))];
        }

        if($param['search']) {
            $where[] = ['x.title','like',"%{$param['search']}%"];
        }

        $count = Db::table('mp_xuqiu')->alias("x")->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('mp_xuqiu')->alias('x')
                ->join("mp_user u","x.uid=u.id","left")
                ->field("x.*,u.org")
                ->order(['x.id'=>'DESC'])
                ->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }

    public function xuqiuPass() {
        $param['id'] = input('post.id');
        checkInput($param);
        $whereXuqiu = [
            ['status','=',0],
            ['id','=',$param['id']]
        ];
        try {
            $xuqiu_exist = Db::table('mp_xuqiu')->where($whereXuqiu)->find();
            if(!$xuqiu_exist) {
                return ajax('非法操作',-1);
            }
            $update_data = [
                'status' => 1,
                'check_time' =>time()
            ];
            Db::table('mp_xuqiu')->where($whereXuqiu)->update($update_data);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    public function xuqiuReject() {
        $param['id'] = input('post.id');
        $param['reason'] = input('post.reason');
        checkInput($param);
        $whereXuqiu = [
            ['status','=',0],
            ['id','=',$param['id']]
        ];
        try {
            $xuqiu_exist = Db::table('mp_xuqiu')->where($whereXuqiu)->find();
            if(!$xuqiu_exist) {
                return ajax('非法操作',-1);
            }
            $update_data = [
                'status' => 2,
                'reason' => $param['reason'],
                'check_time' =>time()
            ];
            Db::table('mp_xuqiu')->where($whereXuqiu)->update($update_data);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    public function xuqiuDetail() {
        $id = input('param.id');
        try {
            $info = Db::table('mp_xuqiu')->alias("x")
                ->join("mp_user u","x.uid=u.id","left")
                ->field("x.*,u.org")
                ->where('x.id','=',$id)->find();
        }catch (\Exception $e) {
            die('参数无效');
        }
        $this->assign('info',$info);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }

    public function xuqiuDel() {
        $val['id'] = input('post.id',0);
        checkInput($val);
        try {
            $wherexuqiu = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_xuqiu')->where($wherexuqiu)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_xuqiu')->where($wherexuqiu)->update(['del'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

//    public function xuqiuModPost() {
//        $val['title'] = input('post.title');
//        $val['content'] = input('post.content');
//        $val['id'] = input('post.id');
//        checkInput($val);
//        try {
//            $where = [
//                ['id','=',$val['id']]
//            ];
//            Db::table('mp_xuqiu')->where($where)->update($val);
//        }catch (\Exception $e) {
//            return ajax($e->getMessage(),-1);
//        }
//        return ajax();
//    }

    public function recommend() {
        $id = input('post.id');
        try {
            $where = [
                ['id','=',$id]
            ];
            $exist = Db::table('mp_xuqiu')->where($where)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            if($exist['recommend'] == 1) {
                Db::table('mp_xuqiu')->where($where)->update(['recommend'=>0]);
                return ajax(false);
            }else {
                Db::table('mp_xuqiu')->where($where)->update(['recommend'=>1]);
                return ajax(true);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }

}