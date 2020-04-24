<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/25
 * Time: 16:12
 */
namespace app\merchant\controller;
use my\Auth;
use think\Db;
use think\Controller;
use think\exception\HttpResponseException;

require_once ROOT_PATH . '/extend/qiniu/autoload.php';
use Qiniu\Config;
use Qiniu\Storage\BucketManager;

class Base extends Controller {

    protected $config = [];
    protected $weburl = '';
    protected $domain = '';
    protected $cmd;

    public function initialize() {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->cmd = request()->controller() . '/' . request()->action();
        $this->domain = config('domain');
        $this->weburl = config('weburl');
        $this->config = [
            'app_id' => config('appid'),
            'secret' => config('app_secret'),
            'mch_id'             => config('mch_id'),
            'key'                => config('mch_key'),   // API 密钥
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          =>  config('cert_path'),
            'key_path'           =>  config('key_path'),
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => APP_PATH . '/wechat.log',
            ],
        ];

        if(!$this->needSession()) {
            if(request()->isPost()) {
                throw new HttpResponseException(ajax('session 无效',-5));
            }else {
                $this->error('请登录后操作',url('Login/index'));
            }
        }


    }

    private function needSession() {
        $noNeedSession = [
            'Plan',
            'Login/index',
            'Login/vcode',
            'Login/login',
            'Login/test',
            'Test/index'
        ];
        if (in_array($this->cmd, $noNeedSession) || in_array(request()->controller(),$noNeedSession)) {
            return true;
        }else {
            if(session('username') && session('mploginstatus') && session('mploginstatus') == md5(session('username') . config('login_key'))) {
                if(session('username') !== config('superman')) {
//                    $auth = new Auth();
//                    $bool = $auth->check($this->cmd,session('admin_id'));
//                    if(!$bool) {
//                        if(request()->isPost()) {
//                            throw new HttpResponseException(ajax('没有权限',-1));
//                        }else {
//                            exit($this->fetch('public/noAuth'));
//                        }
//                    }
                }
                return true;
            }else {
                return false;
            }
        }
    }

    protected function getip() {
        $unknown = 'unknown';
        if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        /*
        处理多层代理的情况
        或者使用正则方式：$ip = preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;
        */
        if (false !== strpos($ip, ','))
            $ip = reset(explode(',', $ip));
        return $ip;
    }

    protected function log($log_detail = '', $type = 0) {
        $insert['detail'] = $log_detail;
        $insert['admin_id'] = session('admin_id');
        $insert['create_time'] = time();
        $insert['ip'] = $this->getip();
        $insert['type'] = $type;
        Db::table('mp_syslog')->insert($insert);
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

    //七牛云文件转移
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

    //七牛云文件删除
    protected function rs_delete($key) {
        $auth = new \Qiniu\Auth(config('qiniu_ak'), config('qiniu_sk'));
        $config = new Config();
        $bucketManager = new BucketManager($auth, $config);
        $bucketManager->delete(config('qiniu_bucket'), $key);
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

    //Exception日志
    protected function refundLog($cmd,$str) {
        $file= LOG_PATH . '/order_refund.log';
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

    //Exception日志
    protected function excep($cmd,$str) {
        $file= LOG_PATH . '/exception.log';
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


}