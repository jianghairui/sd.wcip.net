<?php
namespace app\api\controller;
use think\Db;
use my\Sendsms;

class Notifysms extends Base {

    //通知商家
    public function goodsOrder() {
        $order_id = input('param.orderid');
        $sms = new Sendsms();
        $tel = config('notify_tel');

        $sms_data['tpl_code'] = 'SMS_193247577';
        $sms_data['tel'] = $tel;
        $sms_data['param'] = [
            'status' => '已支付',
            'orderid' => $order_id
        ];
        $res = $sms->send($sms_data);
        if($res->Code === 'OK') {
            return ajax();
        }else {
            $this->msglog($this->cmd,$res->Message);
            return ajax($res->Message,-1);
        }
    }


    //通知用户


}