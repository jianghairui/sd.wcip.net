<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/4/14
 * Time: 11:04
 */
namespace app\api\controller;

use think\Db;
class Person extends Base {

    public function detail() {
        $param['uid'] = input('post.uid');
        checkPost($param);
        if(!$this->myinfo['uid']) { $this->myinfo['uid'] = -1; }
        try {
            $whereRole = [
                ['u.id','=',$param['uid']]
            ];
            $role_exist = Db::table('mp_user')->alias('u')
                ->join('mp_user_role r','u.id=r.uid','left')
                ->where($whereRole)
                ->field('u.id AS uid,u.role,u.nickname,u.avatar,u.org,u.desc,r.cover AS logo,r.busine,r.tel')
                ->find();
            if(!$role_exist) {
                return ajax('invalid uid',-4);
            }
            $role_exist['focus'] = Db::table('mp_user_focus')->where('to_uid','=',$param['uid'])->count();
            $role_exist['subscribe'] = Db::table('mp_user_focus')->where('uid','=',$param['uid'])->count();
            $whereIfocus = [
                ['uid','=',$this->myinfo['uid']],
                ['to_uid','=',$param['uid']]
            ];
            $focus_exist = Db::table('mp_user_focus')->where($whereIfocus)->find();
            if($focus_exist) {
                $role_exist['ifocus'] = true;
            }else {
                $role_exist['ifocus'] = false;
            }
            if($role_exist['role'] != 0) {
                $role_exist['desc'] = $role_exist['busine'];
            }
            $whereNote = [
                ['uid','=',$param['uid']],
                ['status','=',1],
                ['del','=',0]
            ];
            if($role_exist['role']) {
                $role_exist['nickname'] = $role_exist['org'];
            }
            $role_exist['note_num'] = Db::table('mp_note')->where($whereNote)->count();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($role_exist);

    }

    public function goodsList() {
        $param['uid'] = input('post.uid');
        checkPost($param);
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;
        $whereGoods = [
            ['g.shop_id','=',$param['uid']],
            ['g.status','=',1]
        ];
        $order = ['g.id'=>'DESC'];
        try {
            $whereUser = [
                ['id','=',$param['uid']]
            ];
            $user_exist = Db::table('mp_user')->where($whereUser)->find();
            if(!$user_exist) {
                return ajax('invalid uid',-4);
            }else {
                if($user_exist['role'] == 0) {
                    return ajax([]);
                }
            }
            $list = Db::table('mp_goods')->alias('g')
                ->join('mp_user u','g.shop_id=u.id','left')
                ->field('g.id,g.name,g.price,g.use_vip_price,g.vip_price,g.poster,g.pics,u.org')
                ->where($whereGoods)
                ->order($order)
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['poster'] = unserialize($v['pics'])[0];
        }
        return ajax($list);
    }

    public function noteList() {
        $param['uid'] = input('post.uid');
        checkPost($param);
        $curr_page = input('page',1);
        $perpage = input('perpage',10);
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;
        if(!$this->myinfo['uid']) { $this->myinfo['uid'] = -1; }
        try {
            $whereUser = [
                ['id','=',$param['uid']]
            ];
            $user_exist = Db::table('mp_user')->where($whereUser)->find();
            if(!$user_exist) {
                return ajax('invalid uid',-4);
            }

            $where = [
                ['n.status','=',1],
                ['n.uid','=',$param['uid']],
                ['n.del','=',0]
            ];
            $list = Db::table('mp_note')->alias('n')
                ->join('mp_user u','n.uid=u.id','left')
                ->where($where)
                ->field('n.id,n.content,n.pics,u.nickname,n.like,n.comment_num,n.create_time,u.avatar,n.width,n.height')
                ->order(['n.create_time'=>'DESC'])
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
            $map = [
                ['uid','=',$this->myinfo['uid']]
            ];
            $like_ids = Db::table('mp_note_like')->where($map)->column('note_id');
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
            if(in_array($v['id'],$like_ids)) {
                $v['ilike'] = 1;
            }else {
                $v['ilike'] = 0;
            }
            $v['before_time'] = time() - $v['create_time'];
        }
        return ajax($list);
    }


}