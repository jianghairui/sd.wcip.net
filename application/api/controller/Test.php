<?php
namespace app\api\controller;

use my\Sendsms;
use think\Db;
class Test extends Base {

    //获取分类12
    public function cateList() {
        try {
            $map = [
                ['del','=',0],
                ['status','=',1]
            ];
            $list = Db::table('mp_goods_cate')->where($map)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $list = $this->recursion($list,0);
        return ajax($list);

    }


    private function recursion($array,$pid=0) {
        $to_array = [];
        foreach ($array as $v) {
            if($v['pid'] == $pid) {
                $v['child'] = $this->recursion($array,$v['id']);
                $to_array[] = $v;
            }
        }
        return $to_array;
    }

    //通知商家
    private function goodsOrder() {
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

    private function test() {
        $start_time = microtime(true);

        $this->asyn_sms(['order_id'=>1]);

        $end_time = microtime(true);

        echo bcsub($end_time,$start_time,6);
    }

    private function asyn_sms($data) {
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