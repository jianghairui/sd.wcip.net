<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/4/7
 * Time: 14:09
 */
namespace app\api\controller;

use think\Db;
use my\Sendsms;
use my\Kuaidiniao;
class My extends Base {

    public function myDetail() {
        try {
            $whereUser = [
                ['m.id','=',$this->myinfo['id']]
            ];
            $info = Db::table('mp_user_mp')->alias('m')
                ->join('mp_user u','m.uid=u.id','left')
                ->join('mp_user_role r','u.id=r.uid','left')
                ->where($whereUser)
                ->field('m.uid,m.openid,m.unionid,m.last_login_time,m.create_time,m.bind_time,u.nickname,u.avatar,u.realname,u.age,u.sex,u.avatar,u.tel,u.score,u.focus,u.subscribe,u.vip,u.vip_time,u.desc,IFNULL(u.role,0) AS role,u.org,r.busine')
                ->find();
            if($info['uid']) {
                $whereNote = [
                    ['uid','=',$info['uid']],
                    ['status','=',1]
                ];
                $note_num = Db::table('mp_note')->where($whereNote)->count();
                $whereSub = [['uid','=',$info['uid']]];
                $subscribe = Db::table('mp_user_focus')->where($whereSub)->count();
                $whereFans = [['to_uid','=',$info['uid']]];
                $focus = Db::table('mp_user_focus')->where($whereFans)->count();
            }else {
                $note_num = 0;
                $subscribe = 0;
                $focus = 0;
            }
            $info['note_num'] = $note_num;
            $info['focus'] = $focus;
            $info['subscribe'] = $subscribe;
            $info['vip_price'] = 199.00;
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }

    //获取我发的笔记列表
    public function getMyNoteList()
    {
        $curr_page = input('page',1);
        $perpage = input('perpage',10);
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;
        $where = [
            ['n.uid','=',$this->myinfo['uid']],
            ['n.del','=',0]
        ];
        try {
            $ret['count'] = Db::table('mp_note')->alias('n')->where($where)->count();
            $list = Db::table('mp_note')->alias('n')
                ->join('mp_user u','n.uid=u.id','left')
                ->where($where)
                ->field('n.id,n.content,n.pics,n.comment_num,n.create_time,u.nickname,u.avatar,n.like,n.status,n.width,n.height')
                ->order(['n.create_time'=>'DESC'])
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
            $v['before_time'] = time() - $v['create_time'];
        }
        $ret['list'] = $list;
        return ajax($ret);
    }
    //编辑笔记
    public function noteMod ()
    {
        $val['id'] = input('post.id');
        $val['content'] = input('post.content');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        $image = input('post.pics',[]);
        if(!$this->msgSecCheck($val['content'])) {
            return ajax('内容包含敏感词',38);
        }
        try {
            $where = [
                ['id','=',$val['id']],
                ['uid','=',$val['uid']]
            ];
            $exist = Db::table('mp_note')->where($where)->find();
            if(!$exist) {
                return ajax($val['id'],-4);
            }
            if($exist['status'] != 2) {
                return ajax('当前状态无法修改',61);
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

            $old_pics = unserialize($exist['pics']);
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
            $val['status'] = 0;
            Db::table('mp_note')->where($where)->update($val);
        }catch (\Exception $e) {
            foreach ($image_array as $v) {
                if(!in_array($v,$old_pics)) {
                    $this->rs_delete($v);
                }
            }
            return ajax($e->getMessage(),-1);
        }
        foreach ($old_pics as $v) {
            if(!in_array($v,$image_array)) {
                $this->rs_delete($v);
            }
        }
        return ajax();
    }
    //获取我的收藏笔记列表
    public function getMyCollectedNoteList() {
        $curr_page = input('page',1);
        $perpage = input('perpage',10);
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;
        try {
            $whereCollect = [
                ['uid','=',$this->myinfo['uid']]
            ];
            $note_ids = Db::table('mp_note_collect')->where($whereCollect)->column('note_id');
            if(empty($note_ids)) {
                return ajax([]);
            }
            $whereNote = [
                ['n.id','IN',$note_ids]
            ];
            $count = Db::table('mp_note')->alias('n')
                ->join('mp_user u','n.uid=u.id','left')
                ->where($whereNote)->count();
            $list = Db::table('mp_note')->alias('n')
                ->join('mp_user u','n.uid=u.id','left')
                ->where($whereNote)
                ->field('n.id,n.comment_num,n.create_time,n.pics,n.like,n.width,n.height,u.nickname,u.avatar')
                ->order(['n.create_time'=>'DESC'])
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
            $v['before_time'] = time() - $v['create_time'];
        }
        $data['count'] = $count;
        $data['list'] = $list;
        return ajax($data);
    }
    //我的关注列表
    public function mySubscribeList() {
        $curr_page = input('page',1);
        $perpage = input('perpage',10);
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;
        try {
            $whereSub = [
                ['uid','=',$this->myinfo['uid']]
            ];
            $list = Db::table('mp_user_focus')->alias('f')
                ->join('mp_user u','f.to_uid=u.id','left')
                ->where($whereSub)
                ->field('f.*,u.nickname,u.avatar')
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->select();
            $whereMyFans = [
                ['to_uid','=',$this->myinfo['uid']]
            ];
            $my_fans_ids = Db::table('mp_user_focus')->where($whereMyFans)->column('uid');
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $data['list'] = $list;
        $data['my_fans_ids'] = $my_fans_ids;
        return ajax($data);
    }
    //我的粉丝列表
    public function myFansList() {
        $curr_page = input('page',1);
        $perpage = input('perpage',10);
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;
        try {
            $whereFans = [
                ['to_uid','=',$this->myinfo['uid']]
            ];
            $list = Db::table('mp_user_focus')->alias('f')
                ->join('mp_user u','f.uid=u.id','left')
                ->where($whereFans)
                ->field('f.*,u.nickname,u.avatar')
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->select();
            $whereMySub = [
                ['uid','=',$this->myinfo['uid']]
            ];
            $my_sub_ids = Db::table('mp_user_focus')->where($whereMySub)->column('to_uid');
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $data['list'] = $list;
        $data['my_sub_ids'] = $my_sub_ids;
        return ajax($data);
    }
    //修改头像
    public function modAvatar() {
        $val['avatar'] = input('post.avatar');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        $userinfo = $this->getUserInfo();
        try {
            if($val['avatar']) {
                if (substr($val['avatar'],0,4) != 'http') {
                    $qiniu_exist = $this->qiniuFileExist($val['avatar']);
                    if($qiniu_exist !== true) {
                        return ajax($qiniu_exist['msg'] . ' :'.$val['avatar'],5);
                    }
                    $img_check = $this->imgSecCheck(config('qiniu_weburl') . $val['avatar']);
                    if(!$img_check) {
                        return ajax('图片内容违规',59);
                    }
                    $qiniu_move = $this->moveFile($val['avatar'],'upload/avatar/');
                    if($qiniu_move['code'] == 0) {
                        $val['avatar'] = $qiniu_move['path'];
                    }else {
                        return ajax($qiniu_move['msg'],101);
                    }
                }
            }else {
                return ajax('请上传头像',3);
            }
            Db::table('mp_user')->where('id','=',$val['uid'])->update(['avatar'=>$val['avatar']]);
        } catch (\Exception $e) {
            if ($val['avatar'] != $userinfo['avatar'] &&  substr($val['avatar'],0,4) != 'http') {
                $this->rs_delete($val['avatar']);
            }
            return ajax($e->getMessage(), -1);
        }
        if ($val['avatar'] != $userinfo['avatar'] && substr($userinfo['avatar'],0,4) != 'http') {
            $this->rs_delete($userinfo['avatar']);
        }
        return ajax();

    }
    //修改昵称
    public function modNickname() {
        $val['nickname'] = input('post.nickname');
        checkPost($val);
        if(!$this->msgSecCheck($val['nickname'])) {
            return ajax('昵称包含敏感词',62);
        }
        try {
            Db::table('mp_user')->where('id','=',$this->myinfo['uid'])->update($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //修改真实姓名
    public function modRealname() {
        $val['realname'] = input('post.realname');
        checkPost($val);
        try {
            Db::table('mp_user')->where('id','=',$this->myinfo['uid'])->update($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //修改性别 0未知 1 男 2 女
    public function modSex() {
        $val['sex'] = input('post.sex');
        checkPost($val);
        $val['sex'] = intval($val['sex']);
        if(!in_array($val['sex'],[0,1,2], true)) {
            return ajax('非法参数sex',-4);
        }
        try {
            Db::table('mp_user')->where('id','=',$this->myinfo['uid'])->update($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //修改个人简介
    public function modDesc() {
        $val['desc'] = input('post.desc','');
        checkPost($val);
        if(!$this->msgSecCheck($val['desc'])) {
            return ajax('内容包含敏感词',38);
        }
        try {
            Db::table('mp_user')->where('id','=',$this->myinfo['uid'])->update($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    //充值
    public function recharge()
    {
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        try {
            $val['price'] = 0.03;
            $val['days'] = 365;
            $val['create_time'] = time();
            $val['desc'] = '充值年度会员';
            $val['order_sn'] = create_unique_number('v');
            Db::table('mp_vip_order')->insert($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($val);

    }

    /*------ 申请角色 START ------*/

    //获取申请信息
    public function applyInfo() {
        $uid = $this->myinfo['uid'];
        try {
            $info = Db::table('mp_user_role')->where('uid','=',$uid)->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($info) {
            return ajax($info);
        }else {
            return ajax();
        }
    }

    //申请角色
    public function roleApply() {
        //公司名,负责人姓名,身份证号,身份证正反面,资质证明
        $val['role'] = input('post.role');
        $val['org'] = input('post.org');
        $val['name'] = input('post.name');
        $val['identity'] = input('post.identity');
        $val['busine'] = input('post.busine');
        checkPost($val);
        $val['uid'] = $this->myinfo['uid'];

        $tmp['id_front'] = input('post.id_front');
        $tmp['id_back'] = input('post.id_back');
        $tmp['license'] = input('post.license');

        if(!in_array($val['role'],[1,2])) {
            return ajax('invalid role',-4);
        }
        if (!isCreditNo_simple($val['identity'])) {
            return ajax('无效的身份证号', 13);
        }
        if(!$tmp['id_front'] || !$tmp['id_back']) {
            return ajax('上传身份证正反面',44);
        }
        if(!$tmp['license']) {
            return ajax('请上传资质证明',45);
        }

        try {
            $whereRole = [
                ['uid','=',$val['uid']]
            ];
            $role_exist = Db::table('mp_user_role')->where($whereRole)->find();
            if($role_exist) {
                if($role_exist['role_check'] == 1 || $role_exist['role_check'] == 2) {//审核中和已通过的无法修改
                    return ajax('当前状态无法申请角色',46);
                }
            }
            $whereUser = [
                ['id','=',$this->myinfo['uid']]
            ];
            $user = Db::table('mp_user')->where($whereUser)->field('tel')->find();
            $val['tel'] = $user['tel'];
            //验证图片是否存在
            foreach ($tmp as $k=>$v) {
                $qiniu_exist = $this->qiniuFileExist($v);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'] . ' :'.$k,5);
                }
            }
            //转移七牛云图片
            foreach ($tmp as $k=>$v) {
                $qiniu_move = $this->moveFile($v,'upload/role/');
                if($qiniu_move['code'] == 0) {
                    $val[$k] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'] .' :' . $k . '',-1);
                }
            }

            $val['role_check'] = 1;
            if($role_exist) {
                Db::table('mp_user_role')->where('uid',$val['uid'])->update($val);
            }else {
                $val['create_time'] = time();
                Db::table('mp_user_role')->insert($val);
            }
        }catch (\Exception $e) {//异常删图
            if($role_exist) {
                foreach ($tmp as $k=>$v) {
                    if(isset($val[$k]) && $role_exist[$k] != $val[$k]) {
                        $this->rs_delete($val[$k]);
                    }
                }
            }else {
                foreach ($tmp as $k=>$v) {
                    if(isset($val[$k])) {
                        $this->rs_delete($val[$k]);
                    }
                }
            }
            return ajax($e->getMessage(),-1);
        }
        if($role_exist) {//正常删图
            foreach ($tmp as $k=>$v) {
                if($val[$k] != $role_exist[$k]) {
                    $this->rs_delete($role_exist[$k]);
                }
            }
        }
        return ajax();
    }
    /*------ 申请角色 END ------*/


    /*------ 众筹订单管理 START ------*/
    //众筹订单列表
    public function fundingOrderList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $status = input('post.status','');
        $order = ['o.id'=>'DESC'];
        $where = [
            ['o.uid','=',$this->myinfo['uid']],
            ['o.del','=',0],
            ['o.refund_apply','=',0]
        ];
        if($status !== '') {
            $where[] = ['o.status','=',$status];
        }
        try {
            $list = Db::table('mp_funding_order')->alias('o')
                ->join('mp_funding f','o.funding_id=f.id','left')
                ->join('mp_funding_goods g','o.goods_id=g.id','left')
                ->where($where)
                ->field('o.id,o.pay_order_sn,o.pay_price,o.unit_price,o.num,o.status,o.create_time,o.type,f.title AS funding_title,f.cover,f.id AS funding_id,g.name AS goods_name,g.pics')
                ->order($order)
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
        }
        return ajax($list);
    }
    //众筹售后列表
    public function fundingRefundList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $type = input('post.type','');
        $order = ['o.id'=>'DESC'];

        $where = [
            ['o.uid','=',$this->myinfo['uid']],
            ['o.del','=',0]
        ];
        switch ($type) {
            case '1':$where[] = ['o.refund_apply','=',1];break;
            case '2':$where[] = ['o.refund_apply','=',2];break;
            default:$where[] = ['o.refund_apply','IN',[1,2]];
        }
        try {
            $list = Db::table('mp_funding_order')->alias('o')
                ->join('mp_funding f','o.funding_id=f.id','left')
                ->join('mp_funding_goods g','o.goods_id=g.id','left')
                ->where($where)
                ->field('o.id,o.pay_order_sn,o.pay_price,o.unit_price,o.num,o.type,o.refund_apply,o.status,o.create_time,o.type,f.title AS funding_title,f.cover,g.name AS goods_name,g.pics')
                ->order($order)
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
        }
        return ajax($list);
    }
    //众筹订单详情
    public function fundingOrderDetail() {
        $val['order_id'] = input('post.order_id');
        checkPost($val);
        try {
            $where = [
                ['o.uid','=',$this->myinfo['uid']],
                ['o.id','=',$val['order_id']],
                ['o.del','=',0]
            ];
            $info = Db::table('mp_funding_order')->alias('o')
                ->join('mp_funding_goods g','o.goods_id=g.id','left')
                ->join('mp_funding f','o.funding_id=f.id','left')
                ->where($where)
                ->field('o.id,o.pay_order_sn,o.trans_id,o.pay_price,o.unit_price,o.num,o.receiver,o.tel,o.address,o.type,o.refund_apply,o.reason,o.status,o.create_time,o.pay_time,o.send_time,o.finish_time,o.refund_time,o.tracking_name,o.tracking_num,g.name AS goods_name,g.pics,f.id AS funding_id,f.title AS funding_title,f.cover')
                ->find();
            if(!$info) {
                return ajax('invalid order_id',4);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $info['pics'] = unserialize($info['pics']);
        return ajax($info);
    }
    //申请退款
    public function fundingRefundApply() {
        $val['order_id'] = input('post.order_id');
        $val['reason'] = input('post.reason');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['order_id']],
                ['uid','=',$this->myinfo['uid']],
                ['refund_apply','=',0],
                ['status','IN',[1,2,3]],
                ['del','=',0]
            ];
            $info = Db::table('mp_funding_order')->where($where)->find();
            if(!$info) {
                return ajax('invalid order_id',4);
            }
            Db::table('mp_funding_order')->where($where)->update([
                'refund_apply' => 1,
                'reason' => $val['reason']
            ]);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //确认收货
    public function fundingOrderConfirm() {
        $val['order_id'] = input('post.order_id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['order_id']],
                ['uid','=',$this->myinfo['uid']],
                ['status','=',2],
                ['del','=',0]
            ];
            $exist = Db::table('mp_funding_order')->alias('o')->where($where)->find();
            if(!$exist) {
                return ajax( 'invalid order_id',4);
            }
            $update_data = [
                'status' => 3,
                'finish_time' => time()
            ];
            Db::table('mp_funding_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //取消众筹订单
    public function fundingOrderCancel() {
        $val['order_id'] = input('post.order_id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['order_id']],
                ['uid','=',$this->myinfo['uid']],
                ['status','=',0],
                ['del','=',0]
            ];
            $exist = Db::table('mp_funding_order')->where($where)->find();
            if(!$exist) {
                return ajax( 'invalid order_id',4);
            }
            $update_data = [
                'del' => 1
            ];
            Db::table('mp_funding_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    /*------ 众筹订单管理 END ------*/

    /*------ 商品订单管理 START------*/
    //我的订单列表
    public function orderList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $status = input('post.status','');
        $where = "del=0 AND uid=".$this->myinfo['uid'];
        $where .= " AND `status` IN (0,1,2,3) AND `del`=0 AND `refund_apply`=0";
        $order = " ORDER BY `id` DESC";
        $orderby = " ORDER BY `d`.`id` DESC";
        if($status !== '') {
            $where .= " AND status=" . $status;
        }
        try {
            $sql = "SELECT `o`.`id`,`o`.`pay_order_sn`,`o`.`pay_price`,`o`.`total_price`,`o`.`carriage`,`o`.`create_time`,`o`.`refund_apply`,`o`.`status`,`o`.`refund_apply`,`d`.`order_id`,`d`.`goods_id`,`d`.`num`,`d`.`unit_price`,`d`.`use_vip_price`,`d`.`vip_price`,`d`.`goods_name`,`d`.`attr`,`g`.`pics` 
FROM (SELECT * FROM mp_order WHERE " . $where . $order ." LIMIT ".($curr_page-1)*$perpage.",".$perpage.") `o` 
LEFT JOIN `mp_order_detail` `d` ON `o`.`id`=`d`.`order_id`
LEFT JOIN `mp_goods` `g` ON `d`.`goods_id`=`g`.`id`
" . $orderby;
            $list = Db::query($sql);
            $order_id = [];
            $newlist = [];
            foreach ($list as $v) {
                $order_id[] = $v['id'];
            }
            $uniq_order_id = array_unique($order_id);
            foreach ($uniq_order_id as $v) {
                $child = [];
                foreach ($list as $li) {
                    if($li['order_id'] == $v) {
                        $data['id'] = $li['id'];
                        $data['pay_order_sn'] = $li['pay_order_sn'];
                        $data['total_price'] = $li['total_price'];
                        $data['carriage'] = $li['carriage'];
                        $data['status'] = $li['status'];
                        $data['refund_apply'] = $li['refund_apply'];
                        $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
                        $data_child['goods_id'] = $li['goods_id'];
                        $data_child['cover'] = unserialize($li['pics'])[0];
                        $data_child['goods_name'] = $li['goods_name'];
                        $data_child['num'] = $li['num'];
                        $data_child['unit_price'] = $li['unit_price'];
                        $data_child['use_vip_price'] = $li['use_vip_price'];
                        $data_child['vip_price'] = $li['vip_price'];
                        if($li['use_vip_price']) {
                            $data_child['total_price'] = sprintf ( "%1\$.2f",($li['vip_price'] * $li['num']));
                        }else {
                            $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                        }
                        $data_child['attr'] = $li['attr'];
                        $child[] = $data_child;
                    }
                }
                $data['child'] = $child;
                $newlist[] = $data;
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        return ajax($newlist);
    }
    //我的售后列表
    public function refundList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $type = input('post.type',1);
        if(!in_array($type,[1,2,3])) {
            return ajax($type,-4);
        }
        $where = "del=0 AND uid=".$this->myinfo['uid'];
        $order = " ORDER BY `id` DESC";
        $orderby = " ORDER BY `d`.`id` DESC";
        if($type == 1) {
            $where .= " AND refund_apply=1";
        }else if($type == 2){
            $where .= " AND refund_apply=2";
        }else {
            $where .= " AND refund_apply IN (1,2)";
        }
        try {
            $list = Db::query("SELECT 
`o`.`id`,`o`.`pay_order_sn`,`o`.`pay_price`,`o`.`total_price`,`o`.`carriage`,`o`.`create_time`,`o`.`refund_apply`,`o`.`status`,`o`.`refund_apply`,`d`.`order_id`,`d`.`goods_id`,`d`.`num`,`d`.`unit_price`,`d`.`goods_name`,`d`.`attr`,`g`.`pics` 
FROM (SELECT * FROM mp_order WHERE " . $where . $order . " LIMIT ".($curr_page-1)*$perpage.",".$perpage.") `o` 
LEFT JOIN `mp_order_detail` `d` ON `o`.`id`=`d`.`order_id`
LEFT JOIN `mp_goods` `g` ON `d`.`goods_id`=`g`.`id`
".$orderby);

            $order_id = [];
            $newlist = [];
            foreach ($list as $v) {
                $order_id[] = $v['id'];
            }
            $uniq_order_id = array_unique($order_id);
            foreach ($uniq_order_id as $v) {
                $child = [];
                foreach ($list as $li) {
                    if($li['order_id'] == $v) {
                        $data['id'] = $li['id'];
                        $data['pay_order_sn'] = $li['pay_order_sn'];
                        $data['total_price'] = $li['total_price'];
                        $data['carriage'] = $li['carriage'];
                        $data['status'] = $li['status'];
                        $data['refund_apply'] = $li['refund_apply'];
                        $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
                        $data_child['goods_id'] = $li['goods_id'];
                        $data_child['cover'] = unserialize($li['pics'])[0];
                        $data_child['goods_name'] = $li['goods_name'];
                        $data_child['num'] = $li['num'];
                        $data_child['unit_price'] = $li['unit_price'];
                        $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                        $data_child['attr'] = $li['attr'];
                        $child[] = $data_child;
                    }
                }
                $data['child'] = $child;
                $newlist[] = $data;
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        return ajax($newlist);
    }
    //查看订单详情
    public function orderDetail() {
        $val['order_id'] = input('post.order_id');
        checkPost($val);
        $where = [
            ['o.id','=',$val['order_id']],
            ['o.uid','=',$this->myinfo['uid']],
            ['o.del','=',0]
        ];
        try {
            $list = Db::table('mp_order')->alias('o')
                ->join("mp_order_detail d","o.id=d.order_id","left")
                ->join("mp_goods g","d.goods_id=g.id","left")
                ->where($where)
                ->field("o.id,o.pay_order_sn,o.pay_price,o.total_price,o.carriage,o.receiver,o.tel,o.address,o.create_time,o.refund_apply,o.status,d.id AS order_detail_id,d.order_id,d.num,d.unit_price,d.use_vip_price,d.vip_price,d.goods_id,d.goods_name,d.attr,d.evaluate,g.pics")->select();
            if(!$list) {
                return ajax('invalid order_id',4);
            }

            $data = [];
            $child = [];
            foreach ($list as $li) {
                $data['pay_order_sn'] = $li['pay_order_sn'];
                $data['receiver'] = $li['receiver'];
                $data['tel'] = $li['tel'];
                $data['address'] = $li['address'];
                $data['total_price'] = $li['total_price'];
                $data['carriage'] = $li['carriage'];
                $data['amount'] = $li['total_price'] - $data['carriage'];
                $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
                $data['refund_apply'] = $li['refund_apply'];
                $data['status'] = $li['status'];
                $data_child['cover'] = unserialize($li['pics'])[0];
                $data_child['order_detail_id'] = $li['order_detail_id'];
                $data_child['goods_id'] = $li['goods_id'];
                $data_child['goods_name'] = $li['goods_name'];
                $data_child['num'] = $li['num'];
                $data_child['unit_price'] = $li['unit_price'];
                $data_child['use_vip_price'] = $li['use_vip_price'];
                $data_child['vip_price'] = $li['vip_price'];
                if($li['use_vip_price']) {
                    $data_child['total_price'] = sprintf ( "%1\$.2f",($li['vip_price'] * $li['num']));
                }else {
                    $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                }
                $data_child['attr'] = $li['attr'];
                $data_child['evaluate'] = $li['evaluate'];
                $data_child['comment'] = Db::table('mp_goods_comment')->where('order_detail_id','=',$li['order_detail_id'])->value('comment');
                $data_child['cover'] = unserialize($li['pics'])[0];
                $child[] = $data_child;
            }
            $data['child'] = $child;
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        return ajax($data);

    }
    //申请退款
    public function refundApply() {
        $val['order_id'] = input('post.order_id');
        $val['reason'] = input('post.reason');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['order_id']],
                ['uid','=',$this->myinfo['uid']],
                ['status','in',[1,2,3]],
                ['del','=',0]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax( 'invalid order_id',4);
            }
            $update_data = [
                'refund_apply' => 1,
                'reason' => $val['reason']
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //确认收货
    public function orderConfirm() {
        $val['order_id'] = input('post.order_id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['order_id']],
                ['uid','=',$this->myinfo['uid']],
                ['status','=',2],
                ['del','=',0]
            ];
            $exist = Db::table('mp_order')->alias('o')->where($where)->find();
            if(!$exist) {
                return ajax( 'invalid order_id',44);
            }
            $update_data = [
                'status' => 3,
                'finish_time' => time()
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //取消订单
    public function orderCancel() {
        $val['order_id'] = input('post.order_id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['order_id']],
                ['uid','=',$this->myinfo['uid']],
                ['status','=',0],
                ['del','=',0]
            ];
            $exist = Db::table('mp_order')->alias('o')->where($where)->find();
            if(!$exist) {
                return ajax( 'invalid order_id',44);
            }
            $update_data = [
                'del' => 1
            ];
            Db::table('mp_order')->where($where)->update($update_data);
            $detail_list = Db::table('mp_order_detail')->where('order_id','=',$exist['id'])->select();
            foreach ($detail_list as $v) {
                if($v['use_attr'] == 1) {
                    Db::table('mp_goods_attr')->where('id','=',$v['attr_id'])->setInc('stock',$v['num']);
                }
                Db::table('mp_goods')->where('id','=',$v['goods_id'])->setInc('stock',$v['num']);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();

    }
    //获取快递信息
    public function getKdTrace() {
        $data['order_id'] = input('post.order_id');
        checkPost($data);
        try {
            $whereOrder = [
                ['status','IN',[2,3]],
                ['id','=',$data['order_id']]
            ];
            $order_exist = Db::table('mp_order')->where($whereOrder)->find();
            if(!$order_exist) {
                return ajax('订单不存在或状态已改变',24);
            }
            $whereTracking = [
                ['name','=',$order_exist['tracking_name']]
            ];
            $tracking_exist = Db::table('mp_tracking')->where($whereTracking)->find();
            if(!$tracking_exist) {
                return ajax('物流不存在',-4);
            }
            $tracking_code = $tracking_exist['code'];
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $kuaidi = new Kuaidiniao();
        $result = $kuaidi->getOrderTracesByJson($tracking_code,$order_exist['tracking_num']);
        $result['tracking_name'] = $order_exist['tracking_name'];
        return ajax($result);
    }
    //订单评价
    public function orderEvaluate() {
        $val['order_detail_id'] = input('post.order_detail_id');
        $val['comment'] = input('post.comment');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        $val['create_time'] = time();
        try {
            if(!$this->msgSecCheck($val['comment'])) {
                return ajax('内容包含敏感词',38);
            }
            $where_order_detail = [
                ['uid','=',$this->myinfo['uid']],
                ['id','=',$val['order_detail_id']]
            ];
            $order_detail_exist = Db::table('mp_order_detail')->where($where_order_detail)->find();
            if(!$order_detail_exist) {
                return ajax('invalid order_detail_id',-4);
            }
            if($order_detail_exist['evaluate']) { return ajax('不可重复评价',25); }
            $where_order = [
                ['id','=',$order_detail_exist['order_id']]
            ];
            $order_exist = Db::table('mp_order')->where($where_order)->find();
            if(!$order_exist) {
                return ajax('invalid order_id',-4);
            }
            if($order_exist['status'] != 3) { return ajax('订单未完成,无法评价',26); }

            $val['order_id'] = $order_exist['id'];
            $val['goods_id'] = $order_detail_exist['goods_id'];
            Db::table('mp_goods_comment')->insert($val);
            Db::table('mp_order_detail')->where($where_order_detail)->update(['evaluate'=>1]);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    /*------ 商品订单结束 END------*/



    /*------收货地址管理 START------*/
    //我的地址列表
    public function addressList() {
        $uid = $this->myinfo['uid'];
        try {
            $where = [
                ['uid','=',$uid]
            ];
            $list = Db::table('mp_address')->where($where)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }
    //添加收货地址
    public function addressAdd() {
        $val['uid'] = $this->myinfo['uid'];
        $val['provincename'] = input('post.provincename');
        $val['cityname'] = input('post.cityname');
        $val['countyname'] = input('post.countyname');
        $val['detail'] = input('post.detail');
        $val['tel'] = input('post.tel');
        $val['username'] = input('post.username');
        $val['default'] = input('post.default',0);
        checkPost($val);
        $val['postalcode'] = input('post.postalcode');
        if(!is_tel($val['tel'])) {
            return ajax('',6);
        }
        try {
            $id = Db::table('mp_address')->insertGetId($val);
            if($val['default']) {
                $whereDefault = [
                    ['id','<>',$id],
                    ['uid','=',$val['uid']]
                ];
                Db::table('mp_address')->where($whereDefault)->update(['default'=>0]);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //收货地址详情
    public function addressDetail() {
        $val['id'] = input('post.id');
        checkPost($val);
        $uid = $this->myinfo['uid'];
        $where = [
            ['id','=',$val['id']],
            ['uid','=',$uid]
        ];
        try {
            $info = Db::table('mp_address')->where($where)->find();
            if(!$info) {
                return ajax('',-4);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }
    //修改收货地址
    public function addressMod() {
        $val['id'] = input('post.id');
        $uid = $this->myinfo['uid'];
        $val['provincename'] = input('post.provincename');
        $val['cityname'] = input('post.cityname');
        $val['countyname'] = input('post.countyname');
        $val['detail'] = input('post.detail');
        $val['tel'] = input('post.tel');
        $val['username'] = input('post.username');
        $val['default'] = input('post.default',0);
        checkPost($val);
        $val['postalcode'] = input('post.postalcode');
        if(!is_tel($val['tel'])) {
            return ajax('',6);
        }
        $where = [
            ['id','=',$val['id']],
            ['uid','=',$uid]
        ];
        try {
            $info = Db::table('mp_address')->where($where)->find();
            if(!$info) {
                return ajax('',-4);
            }
            Db::table('mp_address')->where($where)->update($val);
            if($val['default']) {
                $whereDefault = [
                    ['id','<>',$val['id']],
                    ['uid','=',$uid]
                ];
                Db::table('mp_address')->where($whereDefault)->update(['default'=>0]);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //删除收货地址
    public function addressDel() {
        $val['id'] = input('post.id');
        checkPost($val);
        try {
            $uid = $this->myinfo['uid'];
            $where = [
                ['id','=',$val['id']],
                ['uid','=',$uid]
            ];
            $info = Db::table('mp_address')->where($where)->find();
            if(!$info) {
                return ajax('invalid id',-4);
            }
            Db::table('mp_address')->where($where)->delete();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //获取我的默认收货地址
    public function getDefaultAddress() {
        $uid = $this->myinfo['uid'];
        $where = [
            ['default','=',1],
            ['uid','=',$uid]
        ];
        try {
            $info = Db::table('mp_address')->where($where)->find();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }
    /*------收货地址管理 END------*/





}