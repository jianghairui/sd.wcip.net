<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/4
 * Time: 10:50
 */
namespace app\api\controller;
use EasyWeChat\Factory;
use think\Db;

class Pay extends Base {
    //充值支付
    public function vipPay() {
        $val['order_sn'] = input('post.order_sn');
        checkPost($val);
        $where = [
            ['order_sn','=',$val['order_sn']],
            ['status','=',0],
            ['uid','=',$this->myinfo['uid']]
        ];

        $app = Factory::payment($this->mp_config);
        try {
            $order_exist = Db::table('mp_vip_order')->where($where)->find();
            if(!$order_exist) {
                return ajax('order_sn',4);
            }
            $result = $app->order->unify([
                'body' => 'VIP充值',
                'out_trade_no' => $val['order_sn'],
//                'total_fee' => 1,
                'total_fee' => floatval($order_exist['price'])*100,
                'notify_url' => $this->weburl . 'api/pay/vipNotify',
                'trade_type' => 'JSAPI',
                'openid' => $this->myinfo['openid'],
            ]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        if($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            return ajax($result['err_code_des'],-1);
        }

        try {
            $sign['appId'] = $result['appid'];
            $sign['timeStamp'] = strval(time());
            $sign['nonceStr'] = $result['nonce_str'];
            $sign['signType'] = 'MD5';
            $sign['package'] = 'prepay_id=' . $result['prepay_id'];
            $sign['paySign'] = getSign($sign);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($sign);
    }
    //充值支付回调接口
    public function vipNotify() {
        //将返回的XML格式的参数转换成php数组格式
        $xml = file_get_contents('php://input');
        $data = xml2array($xml);
        $this->paylog($this->cmd,var_export($data,true));
        if($data) {
            if($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS') {
                $whereOrder = [
                    ['order_sn','=',$data['out_trade_no']],
                    ['status','=',0]
                ];
                try {
                    $order_exist = Db::table('mp_vip_order')->where($whereOrder)->find();
                    if($order_exist) {
                        $update_data = [
                            'status' => 1,
                            'trans_id' => $data['transaction_id'],
                            'pay_time' => time(),
                        ];
                        Db::table('mp_vip_order')->where($whereOrder)->update($update_data);

                        $whereUser = [
                            ['id','=',$order_exist['uid']]
                        ];
                        $user = Db::table('mp_user')->where($whereUser)->find();
                        if($user['vip'] == 1) {
                            $update_user = [
                                'vip' => 1,
                                'vip_time' => $user['vip_time'] + $order_exist['days']*3600*24
                            ];
                        }else {
                            $update_user = [
                                'vip' => 1,
                                'vip_time' => time() + $order_exist['days']*3600*24
                            ];
                        }
                        Db::table('mp_user')->where($whereUser)->update($update_user);
                    }
                }catch (\Exception $e) {
                    $this->log($this->cmd,$e->getMessage());
                }
            }

        }
        exit(array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));

    }

    //充值支付
    public function roleVipPay() {
        $val['order_sn'] = input('post.order_sn');
        checkPost($val);
        $where = [
            ['order_sn','=',$val['order_sn']],
            ['status','=',0],
            ['uid','=',$this->myinfo['uid']]
        ];

        $app = Factory::payment($this->mp_config);
        try {
            $order_exist = Db::table('mp_role_vip_order')->where($where)->find();
            if(!$order_exist) {
                return ajax('order_sn',4);
            }
            $result = $app->order->unify([
                'body' => '角色会员充值',
                'out_trade_no' => $val['order_sn'],
//                'total_fee' => 1,
                'total_fee' => floatval($order_exist['price'])*100,
                'notify_url' => $this->weburl . 'api/pay/roleVipNotify',
                'trade_type' => 'JSAPI',
                'openid' => $this->myinfo['openid'],
            ]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        if($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            return ajax($result['err_code_des'],-1);
        }

        try {
            $sign['appId'] = $result['appid'];
            $sign['timeStamp'] = strval(time());
            $sign['nonceStr'] = $result['nonce_str'];
            $sign['signType'] = 'MD5';
            $sign['package'] = 'prepay_id=' . $result['prepay_id'];
            $sign['paySign'] = getSign($sign);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($sign);
    }
    //充值支付回调接口
    public function roleVipNotify() {
        //将返回的XML格式的参数转换成php数组格式
        $xml = file_get_contents('php://input');
        $data = xml2array($xml);
        $this->paylog($this->cmd,var_export($data,true));
        if($data) {
            if($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS') {
                $whereOrder = [
                    ['order_sn','=',$data['out_trade_no']],
                    ['status','=',0]
                ];
                try {
                    $order_exist = Db::table('mp_role_vip_order')->where($whereOrder)->find();
                    if($order_exist) {
                        $update_data = [
                            'status' => 1,
                            'trans_id' => $data['transaction_id'],
                            'pay_time' => time(),
                        ];
                        Db::table('mp_role_vip_order')->where($whereOrder)->update($update_data);

                        $whereUser = [
                            ['id','=',$order_exist['uid']]
                        ];
                        $user = Db::table('mp_user')->where($whereUser)->find();
                        if($user['role_vip']) {
                            $update_user = [
                                'role_vip' => $order_exist['vip_id'],
                                'role_vip_time' => $user['role_vip_time'] + $order_exist['days']*3600*24
                            ];
                        }else {
                            $update_user = [
                                'role_vip' => $order_exist['vip_id'],
                                'role_vip_time' => time() + $order_exist['days']*3600*24
                            ];
                        }
                        Db::table('mp_user')->where($whereUser)->update($update_user);
                    }
                }catch (\Exception $e) {
                    $this->log($this->cmd,$e->getMessage());
                }
            }

        }
        exit(array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));

    }

    //购物车支付、联合ID支付单号支付
    public function orderIdPay() {
        $val['uid'] = $this->myinfo['uid'];
        $order_id = input('post.order_id',[]);
        if(!is_array($order_id) || empty($order_id)) {
            return ajax('请选择要支付的订单',58);
        }
        $whereOrder = [
            ['id','IN',$order_id],
            ['status','=',0],
            ['uid','=',$val['uid']]
        ];
        try {
            $pay_price = 0;
            $order_list = Db::table('mp_order')->where($whereOrder)->select();
            if(!$order_list || count($order_list) !== count($order_id)) {
                return ajax($order_id,4);
            }
            foreach ($order_list as $v) {
                if(($v['create_time'] + config('order_deadline') - time() - config('order_deadline')/2) <= 0) {
                    return ajax('订单已失效',63);
                }
                $pay_price += $v['pay_price'];
            }
            $pay_order_sn = create_unique_number('');
            $insert_data = [
                'uid' => $val['uid'],
                'pay_order_sn' => $pay_order_sn,
                'pay_price' => $pay_price,
                'order_ids' => implode(',',$order_id),
                'status' => 0,
                'create_time' => time()
            ];
            Db::table('mp_order_unite')->insert($insert_data);
            Db::table('mp_order')->where($whereOrder)->update(['pay_order_sn' => $pay_order_sn]);
            $app = Factory::payment($this->mp_config);
            $result = $app->order->unify([
                'body' => '山洞文创产品',
                'out_trade_no' => $pay_order_sn,
//                'total_fee' => 1,
                'total_fee' => floatval($pay_price)*100,
                'notify_url' => $this->weburl . 'api/pay/orderNotify',
                'trade_type' => 'JSAPI',
                'openid' => $this->myinfo['openid']
            ]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            return ajax($result['err_code_des'],-1);
        }
        try {
            $sign['appId'] = $result['appid'];
            $sign['timeStamp'] = strval(time());
            $sign['nonceStr'] = $result['nonce_str'];
            $sign['signType'] = 'MD5';
            $sign['package'] = 'prepay_id=' . $result['prepay_id'];
            $sign['paySign'] = getSign($sign);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($sign);
    }
    //直接支付、待支付订单支付、联合订单号支付
    public function orderSnPay() {
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        $pay_order_sn = input('post.pay_order_sn');
        if(!$pay_order_sn) {
            return ajax('请选择要支付的订单',58);
        }
        $whereUnite = [
            ['pay_order_sn','=',$pay_order_sn],
            ['status','=',0],
            ['uid','=',$val['uid']]
        ];
        $whereOrder = [
            ['pay_order_sn','=',$pay_order_sn],
            ['status','=',0],
            ['uid','=',$val['uid']]
        ];
        try {
            $unite_exist =  Db::table('mp_order_unite')->where($whereUnite)->find();
            if(!$unite_exist) {
                return ajax('invalid pay_order_sn',4);
            }
            $pay_price = $unite_exist['pay_price'];
            $order_ids = explode(',',$unite_exist['order_ids']);
            $order_list = Db::table('mp_order')->where($whereOrder)->column('id');
            if(!$order_list || count($order_list) !== count($order_ids)) {
                return ajax($order_ids,4);
            }
            if(($unite_exist['create_time'] + config('order_deadline') - time() - config('order_deadline')/2) <= 0) {
                return ajax('订单已失效',63);
            }
            $app = Factory::payment($this->mp_config);
            $result = $app->order->unify([
                'body' => '山洞文创产品',
                'out_trade_no' => $pay_order_sn,
//                'total_fee' => 1,
                'total_fee' => floatval($pay_price)*100,
                'notify_url' => $this->weburl . 'api/pay/orderNotify',
                'trade_type' => 'JSAPI',
                'openid' => $this->myinfo['openid']
            ]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            return ajax($result['err_code_des'],-1);
        }
        try {
            $sign['appId'] = $result['appid'];
            $sign['timeStamp'] = strval(time());
            $sign['nonceStr'] = $result['nonce_str'];
            $sign['signType'] = 'MD5';
            $sign['package'] = 'prepay_id=' . $result['prepay_id'];
            $sign['paySign'] = getSign($sign);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($sign);
    }
    //订单支付回调接口
    public function orderNotify() {
//将返回的XML格式的参数转换成php数组格式
        $xml = file_get_contents('php://input');
        $data = xml2array($xml);
        $this->paylog($this->cmd,var_export($data,true));
        if($data) {
            if($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS') {
                $whereUnite = [
                    ['pay_order_sn','=',$data['out_trade_no']],
                    ['status','=',0]
                ];
                try {
                    $unite_exist = Db::table('mp_order_unite')->where($whereUnite)->find();
                    if($unite_exist) {
                        $update_data = [
                            'status' => 1,
                            'trans_id' => $data['transaction_id'],
                            'pay_time' => time()
                        ];
                        Db::table('mp_order_unite')->where($whereUnite)->update($update_data);
                        Db::table('mp_order')->where($whereUnite)->update($update_data);
                        //变更商品销量
                        $order_ids = explode(',',$unite_exist['order_ids']);
                        $whereDetail = [
                            ['order_id','IN',$order_ids]
                        ];
                        $detail = Db::table('mp_order_detail')->where($whereDetail)->field('id,goods_id,num')->select();
                        $this->paylog($this->cmd,var_export($detail,true));
                        foreach ($detail as $v) {
                            $whereGoods = [
                                ['id','=',$v['goods_id']]
                            ];
                            Db::table('mp_goods')->where($whereGoods)->setInc('sales',$v['num']);
                        }

                        //给店家发送短信
                        $sms_data = [
                            'pay_order_sn' => $data['out_trade_no'],
                            'action' => 'smsGoodsOrder'
                        ];
                        $this->asyn_message_send($sms_data);
                    }
                }catch (\Exception $e) {
                    $this->log($this->cmd,$e->getMessage());
                }
            }
        }
        exit(array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));
    }


    //订单支付
    public function fundingPay() {
        $val['pay_order_sn'] = input('post.pay_order_sn');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        $where = [
            ['pay_order_sn','=',$val['pay_order_sn']],
            ['status','=',0],
            ['uid','=',$val['uid']]
        ];
        $app = Factory::payment($this->mp_config);
        try {
            $order_exist = Db::table('mp_funding_order')->where($where)->find();
            if(!$order_exist) {
                return ajax($val['pay_order_sn'],4);
            }
            $total_price = $order_exist['pay_price'];
            $result = $app->order->unify([
                'body' => '山洞文创众筹',
                'out_trade_no' => $val['pay_order_sn'],
//                'total_fee' => 1,
                'total_fee' => floatval($total_price)*100,
                'notify_url' => $this->weburl . 'api/pay/fundingNotify',
                'trade_type' => 'JSAPI',
                'openid' => $this->myinfo['openid']
            ]);
        }catch (\Exception $e) {
            $this->log($this->cmd . '-1',$e->getMessage());
            return ajax($e->getMessage(),-1);
        }
        $this->log($this->cmd,var_export($result,true));

        if($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            return ajax($result['err_code_des'],-1);
        }
        try {
            $sign['appId'] = $result['appid'];
            $sign['timeStamp'] = strval(time());
            $sign['nonceStr'] = $result['nonce_str'];
            $sign['signType'] = 'MD5';
            $sign['package'] = 'prepay_id=' . $result['prepay_id'];
            $sign['paySign'] = getSign($sign);
        }catch (\Exception $e) {
            $this->log($this->cmd . '-2',$e->getMessage());
            return ajax($e->getMessage(),-1);
        }
        return ajax($sign);
    }
    //订单支付回调接口
    public function fundingNotify() {
//将返回的XML格式的参数转换成php数组格式
        $xml = file_get_contents('php://input');
        $data = xml2array($xml);
        $this->paylog($this->cmd,var_export($data,true));
        if($data) {
            if($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS') {
                $map = [
                    ['pay_order_sn','=',$data['out_trade_no']],
                    ['status','=',0]
                ];
                try {
                    $order_exist = Db::table('mp_funding_order')->where($map)->find();
                    if($order_exist) {
                        $update_data = [
                            'trans_id' => $data['transaction_id'],
                            'pay_time' => time()
                        ];
                        $whereFunding = [
                            ['id','=',$order_exist['funding_id']]
                        ];
                        $funding_exist = Db::table('mp_funding')->where($whereFunding)->find();
                        if(!$funding_exist) {
                            $this->paylog($this->cmd,$data['out_trade_no'] . '未找到此众筹项目');
                            exit(array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));
                        }
                        $funding_data['curr_money'] = $funding_exist['curr_money'] + $order_exist['total_price'];
                        $funding_data['order_num'] = $funding_exist['order_num'] + 1;
                        if($order_exist['type'] == 1) { //有偿订单
                            $update_data['status'] = 1;
                            $funding_data['paid_money'] = $funding_exist['paid_money'] + $order_exist['total_price'];
                            $whereGoods = [
                                ['id','=',$order_exist['goods_id']]
                            ];
                            //商品+销量
                            Db::table('mp_funding_goods')->where($whereGoods)->setInc('sales',$order_exist['num']);//销量+1
                        }else {                         //无偿订单
                            $update_data['status'] = 3;
                            $funding_data['free_money'] = $funding_exist['free_money'] + $order_exist['total_price'];
                        }
                        //修改订单状态,众筹金额增加
                        Db::table('mp_funding_order')->where('pay_order_sn','=',$data['out_trade_no'])->update($update_data);
                        Db::table('mp_funding')->where($whereFunding)->update($funding_data);
                        //发送模板消息
                        $tpl_data = [
                            'order_id' => $order_exist['id'],
                            'action' => 'fundingOrder'
                        ];
                        $this->asyn_message_send($tpl_data);
                    }
                }catch (\Exception $e) {
                    $this->log($this->cmd,$e->getMessage());
                }
            }
        }
        exit(array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));
    }








}