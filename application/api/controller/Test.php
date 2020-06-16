<?php
namespace app\api\controller;

use my\Sendsms;
use think\Db;
class Test extends Base {

    //通知商家
    public function goodsOrder() {
        $sms = new Sendsms();
        $tel = '13102163019';

        $sms_data['tpl_code'] = 'SMS_181860022';
        $sms_data['tel'] = $tel;
        $sms_data['param'] = [
            'name' => '已支付',
            'org' => '11236'
        ];
        $res = $sms->send($sms_data);
        if($res->Code === 'OK') {
            return ajax();
        }else {
            $this->msglog($this->cmd,$res->Message);
            return ajax($res->Message,-1);
        }
    }


    public function test() {
        $start_time = microtime(true);

        $this->asyn_sms(['order_id'=>1]);

        $end_time = microtime(true);

        echo bcsub($end_time,$start_time,6);
    }

    protected function asyn_sms($data) {
        $param = http_build_query($data);
        $fp = @fsockopen('ssl://' . $this->domain, 443, $errno, $errstr, 20);
        if (!$fp){
            $this->msglog($this->cmd,'error fsockopen');
        }else{
            stream_set_blocking($fp,0);
            $http = "GET /api/test/index?".$param." HTTP/1.1\r\n";
            $http .= "Host: ".$this->domain."\r\n";
            $http .= "Connection: Close\r\n\r\n";
            fwrite($fp,$http);
            usleep(1000);
            fclose($fp);
        }
    }


}