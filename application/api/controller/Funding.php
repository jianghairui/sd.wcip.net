<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/4/1
 * Time: 16:57
 */
namespace app\api\controller;

use think\Db;
use my\Sendsms;
class Funding extends Base {

    public function slideList() {
        $where = [
            ['status', '=', 1],
            ['type', '=', 4]
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
    //众筹列表
    public function fundingList() {
        $param['status'] = input('post.status','');
        $param['search'] = input('post.search');
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);

        $where = [
            ['del','=',0]
        ];
        if($param['status'] !== '') {
            $where[] = ['status','=',$param['status']];
        }
        if($param['search']) {
            $where[] = ['title','like',"%{$param['search']}%"];
        }
        try {
            $list = Db::table('mp_funding')
                ->field('id,title,cover,need_money,curr_money,order_num,start_time,end_time,status')
                ->where($where)
                ->order(['id'=>'DESC'])->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        return ajax($list);
    }
    //众筹详情
    public function fundingDetail() {
        $val['funding_id'] = input('post.funding_id','');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['funding_id']],
                ['del','=',0]
            ];
            $info = Db::table('mp_funding')
                ->where($where)->find();
            if(!$info) {
                return ajax('非法参数',-4);
            }
            $whereUser = [
                ['id','=',$info['uid']]
            ];
            $user = Db::table('mp_user')->where($whereUser)->field('id,nickname,avatar')->find();
            $info['nickname'] = $user['nickname'];
            $info['avatar'] = $user['avatar'];
            $info['time_count'] = $info['end_time'] - time();
            if(!$info) { return ajax('非法参数id',-4);}
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        return ajax($info);
    }
    //众筹商品列表
    public function fundingGoodsList() {
        $val['funding_id'] = input('post.funding_id');
        checkPost($val);
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        try {
            $where = [
                ['funding_id','=',$val['funding_id']],
                ['del','=',0]
            ];
            $list = Db::table('mp_funding_goods')->where($where)
                ->field('id,price,name,desc,pics,sales,funding_id')
                ->limit(($curr_page - 1)*$perpage,$perpage)
                ->order(['id'=>'DESC'])->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
        }
        return ajax($list);
    }
    //众筹商品详情
    public function fundingPurchase() {
        $val['funding_id'] = input('post.funding_id');
        checkPost($val);
        $val['goods_id'] = input('post.goods_id','');
        $val['num'] = input('post.num',0);
        $val['receiver'] = input('post.receiver','');
        $val['tel'] = input('post.tel','');
        $val['address'] = input('post.address','');
        $val['desc'] = input('post.desc','');
        $val['uid'] = $this->myinfo['id'];
        $val['create_time'] = time();
        $val['pay_order_sn'] = create_unique_number('F');

        try {
            $whereFunding = [
                ['id','=',$val['funding_id']]
            ];
            $funding_exist = Db::table('mp_funding')->where($whereFunding)->find();
            if($funding_exist['start_time'] > time()) {
                return ajax('众筹未开始',35);
            }
            if($funding_exist['end_time'] < time()) {
                return ajax('众筹已结束',36);
            }
            $whereGoods = [
                ['id','=',$val['goods_id']],
                ['del','=',0]
            ];
            $goods_exist = Db::table('mp_funding_goods')->where($whereGoods)->find();
            if(!$goods_exist) {
                return ajax('非法参数goods_id',-4);
            }
            if($goods_exist['funding_id'] != $val['funding_id']) {
                return ajax('goods_id does not match the funding_id',-4);
            }

            if($val['goods_id']) {
                $val['type'] = 1;
                $postArray['num'] = $val['num'];
                $postArray['receiver'] = $val['receiver'];
                $postArray['tel'] = $val['tel'];
                $postArray['address'] = $val['address'];
                foreach ($postArray as $value) {
                    if (is_null($value) || $value === '') {
                        return ajax($postArray,-2);
                    }
                }
                if(!if_int($val['num']) || $val['num'] <= 0) {return ajax('非法参数num',-4);}
                if(!is_tel($val['tel'])) {return ajax('无效的手机号',6);}

                $val['unit_price'] = $goods_exist['price'];
                $val['pay_price'] = $goods_exist['price']*$val['num'];
                $val['total_price'] = $val['pay_price'];
            }else {
                $val['goods_id'] = 0;
                $val['pay_price'] = input('post.pay_price');
                $val['total_price'] = $val['pay_price'];
                if(!$val['pay_price']) {
                    return ajax(['pay_price'=>$val['pay_price']],-2);
                }
                if(!is_currency($val['pay_price'])) {
                    return ajax('无效的金额',70);
                }
                $val['type'] = 2;
            }
            Db::table('mp_funding_order')->insert($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1999);
        }
        return ajax($val['pay_order_sn']);

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
    public function fundingRelease() {
        $this->checkUid();
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
            Db::table('mp_funding_consult')->insert($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

}