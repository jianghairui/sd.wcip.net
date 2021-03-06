<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/4/8
 * Time: 13:14
 */
namespace app\api\controller;

use think\Db;

class Note extends Base {
    //获取笔记列表
    public function getNoteList() {
        $search = input('post.search','');
        $type = input('post.type',0);
        $curr_page = input('page',1);
        $perpage = input('perpage',10);
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;
        $where = [
            ['n.status','=',1],
            ['n.del','=',0],
            ['n.recommend','=',1]
        ];
        if($search) {
            $where[] = ['n.title','like',"%{$search}%"];
        }
        if(!$this->myinfo['uid']) { $this->myinfo['uid'] = -1; }
        try {
            switch ($type) {
                case 1:
                    $whereUser = [
                        ['role','=',1]
                    ];
                    $uids = Db::table('mp_user')->where($whereUser)->column('id');
                    if(!empty($uids)) {
                        $where[] = ['uid','in',$uids];
                    }else {
                        return ajax([]);
                    }
                    break;
                case 2:
                    $whereUser = [
                        ['role','=',2]
                    ];
                    $uids = Db::table('mp_user')->where($whereUser)->column('id');
                    if(!empty($uids)) {
                        $where[] = ['uid','in',$uids];
                    }else {
                        return ajax([]);
                    }
                    break;
                default:;
            }

            $ret['count'] = Db::table('mp_note')->alias('n')->where($where)->count();
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
        $ret['list'] = $list;
        return ajax($ret);
    }
    //发布笔记
    public function noteRelease () {
        $val['content'] = input('post.content');
        $val['width'] = input('post.width',1);
        $val['height'] = input('post.height',1);
        checkPost($val);
        $val['uid'] = $this->myinfo['uid'];
        $val['goods_id'] = input('post.goods_id');
        $val['create_time'] = time();
        $image = input('post.pics',[]);
        if(!$this->msgSecCheck($val['content'])) {
            return ajax('内容包含敏感词',64);
        }

        try {
            if($val['goods_id']) {
                $whereGoods = [
                    ['id','=',$val['goods_id']]
                ];
                $goods_exist = Db::table('mp_goods')->where($whereGoods)->find();
                if(!$goods_exist) {
                    return ajax('invalid goods',-4);
                }
            }else {
                unset($val['goods_id']);
            }
            if(is_array($image) && !empty($image)) {
                if(count($image) > 9) {
                    return ajax('最多上传9张图片',8);
                }
                //验证图片是否存在
                foreach ($image as $v) {
                    $qiniu_exist = $this->qiniuFileExist($v);
                    if($qiniu_exist !== true) {
                        return ajax($qiniu_exist['msg'] . ' :'.$v,5);
                    }
                }
            }else {
                return ajax('请传入图片',3);
            }
            $image_array = [];
            //转移七牛云图片
            foreach ($image as $v) {
                $qiniu_move = $this->moveFile($v,'upload/note/');
                if($qiniu_move['code'] == 0) {
                    $image_array[] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'] .' :' . $v . '',-1);
                }
            }
            $val['pics'] = serialize($image_array);
            Db::table('mp_note')->insert($val);
        }catch (\Exception $e) {
            foreach ($image_array as $v) {
                $this->rs_delete($v);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
    //获取笔记详情
    public function getNoteDetail() {
        $val['note_id'] = input('post.note_id');
        checkPost($val);
        if(!$this->myinfo['uid']) { $this->myinfo['uid'] = -1; }
        try {
            $info = Db::table('mp_note')->alias('n')
                ->join('mp_user u','n.uid=u.id','left')
                ->join('mp_goods g','n.goods_id=g.id','left')
                ->where('n.id','=',$val['note_id'])
                ->field('n.id,n.uid,n.content,n.pics,n.like,n.status,n.reason,n.goods_id,n.comment_num,n.create_time,g.poster,g.name AS goods_name,u.nickname,u.avatar')
                ->find();
            if(!$info) {
                return ajax('invalid id',-4);
            }
            $whereIlike = [
                ['uid','=',$this->myinfo['uid']],
                ['note_id','=',$val['note_id']]
            ];
            $exist = Db::table('mp_note_like')->where($whereIlike)->find();
            if($exist) {
                $info['ilike'] = true;
            }else {
                $info['ilike'] = false;
            }
            $whereIcollect = [
                ['uid','=',$this->myinfo['uid']],
                ['note_id','=',$val['note_id']]
            ];
            $exist = Db::table('mp_note_collect')->where($whereIcollect)->find();
            if($exist) {
                $info['icollect'] = true;
            }else {
                $info['icollect'] = false;
            }
            $whereFocus = [
                ['uid','=',$this->myinfo['uid']],
                ['to_uid','=',$info['uid']]
            ];
            $exist = Db::table('mp_user_focus')->where($whereFocus)->find();
            if($exist) {
                $info['ifocus'] = true;
            }else {
                $info['ifocus'] = false;
            }
            $whereComment = [
                ['c.to_cid','=',0],
                ['c.note_id','=',$val['note_id']]
            ];
            $info['comment_count'] = Db::table('mp_note_comment')->alias('c')
                    ->join('mp_user u','c.uid=u.id','left')
                    ->where($whereComment)->count();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        $info['pics'] = unserialize($info['pics']);
        return ajax($info);
    }
    //获取评论列表
    public function commentList() {
        $val['note_id'] = input('post.note_id');
        checkPost($val);
        try {
            $exist = Db::table('mp_note')->where('id',$val['note_id'])->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            $list = DB::query("SELECT c.id,c.note_id,c.uid,c.to_cid,c.to_uid,c.content,c.root_cid,c.create_time,u.avatar,u.nickname,IFNULL(u2.nickname,'') AS to_nickname 
FROM mp_note_comment c 
LEFT JOIN mp_user u ON c.uid=u.id 
LEFT JOIN mp_user u2 ON c.to_uid=u2.id 
WHERE c.note_id=?",[$val['note_id']]);
            foreach ($list as &$v) {
                $v['before_time'] = time() - $v['create_time'];
            }
            $list = $this->recursion($list);
            return ajax($list);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }
    //发表评论
    public function commentAdd() {
        $val['note_id'] = input('post.note_id');
        $val['content'] = input('post.content');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        $val['to_cid'] = input('post.to_cid');
        if(!$this->msgSecCheck($val['content'])) {
            return ajax('评论内容包含敏感词',60);
        }
        try {
            $whereNote = [
                ['id','=',$val['note_id']]
            ];
            $exist = Db::table('mp_note')->where($whereNote)->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            if($val['to_cid']) {
                $map = [
                    ['id','=',$val['to_cid']],
                    ['note_id','=',$val['note_id']]
                ];
                $comment_exist = Db::table('mp_note_comment')->where($map)->find();
                if($comment_exist) {
                    $val['to_uid'] = $comment_exist['uid'];
                    if($comment_exist['to_cid'] == 0) {
                        $val['root_cid'] = $comment_exist['id'];
                    }else {
                        $val['root_cid'] = $comment_exist['root_cid'];
                    }
                }else {
                    return ajax('invalid to_cid',-4);
                }
            }else {
                $val['to_cid'] = 0;
                $val['to_uid'] = 0;
                $val['root_cid'] = 0;
            }
            $val['create_time'] = time();
            Db::startTrans();
            Db::table('mp_note_comment')->insert($val);
            Db::table('mp_note')->where($whereNote)->setInc('comment_num',1);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
    //判断是否点赞
    public function ifLike() {
        $val['note_id'] = input('post.note_id');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        try {
            $exist = Db::table('mp_note')->where('id',$val['note_id'])->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            $map = [
                ['uid','=',$val['uid']],
                ['note_id','=',$val['note_id']]
            ];
            $exist = Db::table('mp_note_like')->where($map)->find();
            if($exist) {
                $like = true;
            }else {
                $like = false;
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($like);
    }
    //点赞,取消
    public function iLike() {
        $val['note_id'] = input('post.note_id');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        try {
            $exist = Db::table('mp_note')->where('id',$val['note_id'])->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            $map = [
                ['uid','=',$val['uid']],
                ['note_id','=',$val['note_id']]
            ];
            $exist = Db::table('mp_note_like')->where($map)->find();
            if($exist) {
                Db::table('mp_note_like')->where($map)->delete();
                Db::table('mp_note')->where('id',$val['note_id'])->setDec('like',1);
                return ajax(false);
            }
            Db::table('mp_note_like')->insert($val);
            Db::table('mp_note')->where('id',$val['note_id'])->setInc('like',1);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax(true);
    }
    //判断是否收藏
    public function ifCollect() {
        $val['note_id'] = input('post.note_id');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        try {
            $exist = Db::table('mp_note')->where('id',$val['note_id'])->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            $map = [
                ['uid','=',$val['uid']],
                ['note_id','=',$val['note_id']]
            ];
            $exist = Db::table('mp_note_collect')->where($map)->find();
            if($exist) {
                $like = true;
            }else {
                $like = false;
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($like);
    }
    //收藏/取消收藏
    public function iCollect() {
        $val['note_id'] = input('post.note_id');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        $val['create_time'] = time();
        try {
            $exist = Db::table('mp_note')->where('id',$val['note_id'])->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            $map = [
                ['uid','=',$val['uid']],
                ['note_id','=',$val['note_id']]
            ];
            $exist = Db::table('mp_note_collect')->where($map)->find();
            if($exist) {
                Db::table('mp_note_collect')->where($map)->delete();
                Db::table('mp_note')->where('id',$val['note_id'])->setDec('collect',1);
                return ajax(false);
            }
            Db::table('mp_note_collect')->insert($val);
            Db::table('mp_note')->where('id',$val['note_id'])->setInc('collect',1);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax(true);
    }
    //判断是否关注
    public function ifFocus() {
        $val['to_uid'] = input('post.to_uid');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        try {
            $exist = Db::table('mp_user')->where('id',$val['to_uid'])->find();
            if(!$exist) {
                return ajax('invalid to_uid',-4);
            }
            $map = [
                ['uid','=',$val['uid']],
                ['to_uid','=',$val['to_uid']]
            ];
            $exist = Db::table('mp_user_focus')->where($map)->find();
            if($exist) {
                $like = true;
            }else {
                $like = false;
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($like);
    }
    //关注/取消关注
    public function iFocus() {
        $val['to_uid'] = input('post.to_uid');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        $val['create_time'] = time();
        try {
            $user_exist = Db::table('mp_user')->where('id',$val['to_uid'])->find();
            if(!$user_exist) {
                return ajax('invalid to_uid',-4);
            }
            if($val['to_uid'] == $val['uid']) {
                return ajax('不能关注自己',38);
            }
            $map = [
                ['uid','=',$val['uid']],
                ['to_uid','=',$val['to_uid']]
            ];
            $exist = Db::table('mp_user_focus')->where($map)->find();
            if($exist) {
                Db::table('mp_user_focus')->where($map)->delete();
//                Db::table('mp_user')->where('id',$val['to_uid'])->setDec('focus',1);
//                Db::table('mp_user')->where('id',$val['uid'])->setDec('ifocus',1);
                return ajax(false);
            }else {
                Db::table('mp_user_focus')->insert($val);
//                Db::table('mp_user')->where('id',$val['to_uid'])->setInc('focus',1);
//                Db::table('mp_user')->where('id',$val['uid'])->setInc('ifocus',1);
                return ajax(true);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }
    //发布笔记选择商品
    public function goodsList() {
        $param['type'] = input('post.type',1);
        checkPost($param);
        try {
            if(!$this->myinfo['uid']) {
                return ajax([]);
            }
            $param['type'] = intval($param['type']);
            switch ($param['type']) {
                case 1:
                    $whereOrder = [
                        ['uid','=',$this->myinfo['uid']]
                    ];
                    $order_ids = Db::table('mp_order')->where($whereOrder)->column('id');
                    if(empty($order_ids)) {
                        $list = [];
                    }else {
                        $whereGoods = [
                            ['d.order_id','in',$order_ids]
                        ];
                        $list = Db::table('mp_order_detail')->alias('d')
                            ->join('mp_goods g','d.goods_id=g.id','left')
                            ->field('d.goods_id,d.goods_name,d.unit_price AS price,d.attr,g.pics,g.poster,g.sales')
                            ->where($whereGoods)
                            ->select();
                    }
                    break;
                case 2:
                    $whereGoods = [
                        ['shop_id','=',$this->myinfo['uid']]
                    ];
                    $list = Db::table('mp_goods')
                        ->field('id AS goods_id,name AS goods_name,price,"默认" AS attr,pics,poster')
                        ->where($whereGoods)
                        ->select();
                    break;
                default:
                    $list = [];
            }
            foreach ($list as &$v) {
                $v['poster'] = unserialize($v['pics'])[0];
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }


    private function sortMerge($node,$pid=0)
    {
        $arr = array();
        foreach($node as $key=>$v)
        {
            if($v['pid'] == $pid)
            {
                $v['child'] = $this->sortMerge($node,$v['id']);
                $arr[] = $v;
            }
        }
        return $arr;
    }

    private function recursion($array,$to_cid=0) {
        $to_array = [];
        foreach ($array as $v) {
            if($v['root_cid'] == $to_cid) {
                $v['child'] = $this->recursion($array,$v['id']);
                $to_array[] = $v;
            }
        }
        return $to_array;
    }



}