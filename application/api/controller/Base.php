<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/18
 * Time: 21:36
 */
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\exception\HttpResponseException;

require_once ROOT_PATH . '/extend/qiniu/autoload.php';
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use EasyWeChat\Factory;

class Base extends Controller {

    protected $controller = '';
    protected $cmd = '';
    protected $domain = '';
    protected $weburl = '';
    protected $mp_config = [];
    protected $myinfo = [];

    public function initialize()
    {
        parent::initialize();  // TODO: Change the autogenerated stub
        $this->controller = request()->controller();
        $this->cmd = request()->controller() . '/' . request()->action();
        $this->domain = config('domain');
        $this->weburl = config('weburl');
        $this->mp_config = [
            'app_id' => config('appid'),
            'secret' => config('app_secret'),
            'mch_id'             => config('mch_id'),
            'key'                => config('mch_key'),   // API 密钥
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          =>  config('cert_path'),
            'key_path'           =>  config('key_path'),
            // 下面为可选项,指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => APP_PATH . '/wechat.log',
            ],
        ];
        $this->checkSession();
        $this->checkUid();
    }

    private function checkSession() {
        $noneed = [
            'Test',
            'Notifysms',
            'Uni',
            'Plan',
            'Message',
            'Email',
            'Login/login',
            'Pay/vipnotify',
            'Pay/rolevipnotify',
            'Pay/ordernotify',
            'Pay/fundingnotify',

        ];
        if (in_array($this->controller,$noneed) || in_array($this->cmd, $noneed)) {
            return true;
        }else {
            $token = input('post.token');
            if(!$token) { throw new HttpResponseException(ajax('token is empty',-6)); }
            try {
                $token_exist = Db::table('mp_user_mp')->where('token','=',$token)->field('id,uid,nickname,sex,status,avatar,user_auth,session_key,token,last_login_time,openid,unionid')->find();
            }catch (\Exception $e) {
                throw new HttpResponseException(ajax($e->getMessage(),-1));
            }
            if($token_exist) {
                if(($token_exist['last_login_time'] + 3600*24*7) < time()) {
                    throw new HttpResponseException(ajax('invalid token',-3));
                }
                if($token_exist['status'] == 2) {
                    throw new HttpResponseException(ajax('已拉黑',-8));
                }
                $this->myinfo = $token_exist;
                return true;
            }else {
                throw new HttpResponseException(ajax('invalid token',-3));
            }
        }

    }

    protected function checkUid() {
        $need = [

            'Activity/uploadworks',
            'Activity/worksvote',

            'Copyright/ipapply',
            'Copyright/ipfree',

            'Funding/fundingpurchase',
            'Funding/fundingrelease',

            'Note/noterelease',
            'Note/commentadd',
            'Note/iflike',
            'Note/ilike',
            'Note/ifcollect',
            'Note/icollect',
            'Note/iffocus',
            'Note/ifocus',

            'Xuqiu/xuqiurelease',

            'Pay/vippay',
            'Pay/orderidpay',
            'Pay/ordersnpay',
            'Pay/fundingpay',

            'Shop/cartadd',
            'Shop/cartinc',
            'Shop/cartdec',
            'Shop/cartdel',
            'Shop/purchase',
            'Shop/carttopurchase',

            'My/getmynoteList',
            'My/notemod',
            'My/getmycollectednotelist',
            'My/modavatar',
            'My/modnickname',
            'My/modrealname',
            'My/modsex',
            'My/moddesc',
            'My/recharge',
            'My/roleviprecharge',
            'My/applyinfo',
            'My/roleapply',

//            'My/fundingorderlist',
//            'My/fundingrefundlist',
            'My/fundingorderdetail',
            'My/fundingrefundapply',
            'My/fundingorderconfirm',
            'My/fundingordercancel',
//            'My/orderlist',
//            'My/refundlist',
            'My/orderdetail',
            'My/refundapply',
            'My/orderconfirm',
            'My/ordercancel',
            'My/orderevaluate',
            'My/refundapply',

//            'My/addresslist',
            'My/addressadd',
            'My/addressdetail',
            'My/addressmod',
            'My/addressdel',
            'My/getdefaultaddress',

            'Sample/sampletake',

        ];
        if (!in_array($this->controller,$need) && !in_array($this->cmd, $need)) {
            return true;
        }else {
            if($this->myinfo['uid']) {
                return true;
            }else {
                throw new HttpResponseException(ajax('请绑定手机号',-7));
            }
        }
    }

    protected function getUserInfo() {
        if(!$this->myinfo['uid']) {
            throw new HttpResponseException(ajax('请绑定手机号',-7));
        }else {
            $whereUser = [
                ['id','=',$this->myinfo['uid']]
            ];
            try {
                $info = Db::table('mp_user')->where($whereUser)->find();
            } catch (\Exception $e) {
                throw new HttpResponseException(ajax($e->getMessage(),-1));
            }
            return $info;
        }
    }






    //七牛云判断文件是否存在
    public function qiniuFileExist($key) {
        $auth = new \Qiniu\Auth(config('qiniu_ak'), config('qiniu_sk'));
        $config = new Config();
        $bucketManager = new BucketManager($auth, $config);
        list($fileInfo, $err) = $bucketManager->stat(config('qiniu_bucket'), $key);
        if ($err) {
            return [
                'code' => 1,
                'msg' => 'qiniu_code:' . $err->code() .' , '. $err->message()
            ];
        }
        return true;
    }

    //七牛云移动文件
    protected function moveFile($srcKey,$destpath='upload/public/') {
        $auth = new \Qiniu\Auth(config('qiniu_ak'), config('qiniu_sk'));
        $config = new Config();
        $bucketManager = new BucketManager($auth, $config);

        $srcBucket = config('qiniu_bucket');
        $destBucket = config('qiniu_bucket');
        $arr = explode('/',$srcKey);
        $destKey = $destpath . end($arr);
        //如果一样不需要挪动
        if($srcKey == $destKey) {
            return [
                'code' => 0,
                'path' => $destKey
            ];
        }
        $err = $bucketManager->move($srcBucket, $srcKey, $destBucket, $destKey, true);
        if($err) {
            return [
                'code' => 1,
                'msg' => 'qiniu_code:' . $err->code() .' , '. $err->message()
            ];
        }else {
            return [
                'code' => 0,
                'path' => $destKey
            ];
        }

    }

    //七牛云删除文件
    protected function rs_delete($key) {
        $auth = new \Qiniu\Auth(config('qiniu_ak'), config('qiniu_sk'));
        $config = new Config();
        $bucketManager = new BucketManager($auth, $config);
        $bucketManager->delete(config('qiniu_bucket'), $key);
    }

    //小程序验证文本内容是否违规
    protected function msgSecCheck($msg) {
        $content = $msg;
        $app = Factory::payment($this->mp_config);
        $access_token = $app->access_token;
        $token = $access_token->getToken();
        $url = 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token=' . $token['access_token'];
        $res = curl_post_data($url, '{ "content":"'.$content.'" }');

        $result = json_decode($res,true);
        try {
            $audit = true;
            if($result['errcode'] !== 0) {
                $this->mplog($this->cmd,$this->myinfo['id'] .' : '. $content .' : '. var_export($result,true));
                switch ($result['errcode']) {
                    case 87014: $audit = false;break;
                    case 40001:
                        $audit = false;break;
                    default:$audit = false;;
                }
            }
        } catch (\Exception $e) {
            throw new HttpResponseException(ajax($e->getMessage(),-1));
        }
        return $audit;
    }

    /*微信图片敏感内容检测*/
    public function imgSecCheck($image_path) {
        $audit = true;
        $img = @file_get_contents($image_path);
        if(!$img) {
            $this->mplog($this->cmd,'file_get_contents(): php_network_getaddresses: getaddrinfo failed: Name or service not known');
            return true;
        }
        $filePath = '/dev/shm/tmp1.png';
        file_put_contents($filePath, $img);
        $obj = new \CURLFile(realpath($filePath));
        $obj->setMimeType("image/jpeg");
        $file['media'] = $obj;
        $app = Factory::payment($this->mp_config);
        $access_token = $app->access_token;
        $token = $access_token->getToken();
        $url = 'https://api.weixin.qq.com/wxa/img_sec_check?access_token=' . $token['access_token'];
        $info = curl_post_data($url,$file);
        $result = json_decode($info,true);
        try {
            if($result['errcode'] !== 0) {
                $this->mplog($this->cmd,$this->myinfo['id'] .' : '. $image_path .' : '. var_export($result,true));
                switch ($result['errcode']) {
                    case 87014: $audit = false;break;
                    case 40001:
                        $audit = false;break;
                    default:$audit = false;;
                }
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return $audit;
    }

    //Exception日志
    protected function log($cmd,$str) {
        $file= LOG_PATH . '/exception_api.log';
        create_dir($file);
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

    //支付回调日志
    protected function paylog($cmd,$str) {
        $file= LOG_PATH . '/notify.log';
        create_dir($file);
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

    //七牛云日志
    public function qiniuLog($cmd,$str) {
        $file= LOG_PATH . '/qiniu_error.log';
        create_dir($file);
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

    //模板消息日志
    protected function msglog($cmd,$str) {
        $file= LOG_PATH . '/message.log';
        create_dir($file);
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }


    //小程序验证内容违规
    protected function mplog($cmd,$str) {
        $file= LOG_PATH . '/mp.log';
        create_dir($file);
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

    //小程序
    protected function planlog($cmd,$str) {
        $file= LOG_PATH . '/plan.log';
        create_dir($file);
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

    protected function asyn_message_send($data) {
        $param = http_build_query($data);
        $allow = [
            'fundingOrder',
            'goodsOrder',
            'smsGoodsOrder'
        ];
        if(!in_array($data['action'],$allow)) {
            $this->msglog('Pay/asyn_message_send',$data['action'] . ' not in allow actions');
            die();
        }
        $fp = @fsockopen('ssl://' . $this->domain, 443, $errno, $errstr, 1);
        if (!$fp){
            $this->msglog('asyn_tpl_send','error fsockopen:' . $this->domain);
        }else{
            stream_set_blocking($fp,0);
            $http = "GET /api/message/" . $data['action'] . "?".$param." HTTP/1.1\r\n";
            $http .= "Host: ".$this->domain."\r\n";
            $http .= "Connection: Close\r\n\r\n";
            fwrite($fp,$http);
            usleep(1000);
            fclose($fp);
        }
    }





}