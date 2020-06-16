<?php
namespace app\api\controller;
use think\Db;
use my\Sendsms;

class Notifysms extends Base {

    //通知商家
    private function index() {
        $sms = new Sendsms();
        $tel = config('notify_tel');

        $sms_data['tpl_code'] = 'SMS_174925606';
        $sms_data['tel'] = $tel;
        $sms_data['param'] = [
            'code' => 5151231
        ];

        $res = $sms->send($sms_data);
        halt($res);
        if($res->Code === 'OK') {
            return ajax();
        }else {
            $this->msglog($this->cmd,$res->Message);
            return ajax($res->Message,-1);
        }
    }

    //通知商家
    public function goodsOrder() {
        $sms = new Sendsms();
        $tel = config('notify_tel');

        $code = mt_rand(100000,999999);
        $sms_data['tpl_code'] = 'SMS_174925606';
        $sms_data['tel'] = $tel;
        $sms_data['param'] = [
            'code' => $code
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