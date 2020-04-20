<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/20
 * Time: 16:36
 */

namespace app\api\controller;
use EasyWeChat\Factory;
use think\Db;
use my\Sendsms;
class Login extends Base {

    //小程序登录
    public function login()
    {
        $code = input('post.code');
        checkPost(['code'=>$code]);
        $app = Factory::miniProgram($this->mp_config);
        $info = $app->auth->session($code);
        if(isset($info['errcode']) && $info['errcode'] !== 0) {
            return ajax($info,-1);
        }
        $ret['openid'] = $info['openid'];
        $ret['session_key'] = $info['session_key'];

        try {
            $token = md5($ret['openid'] . time());
            $whereOpenid = [
                ['openid','=',$ret['openid']]
            ];
            $exist = Db::table('mp_user_mp')->where($whereOpenid)->find();
            if($exist) {
                Db::table('mp_user_mp')->where($whereOpenid)->update([
                    'last_login_time'=>time(),
                    'token'=>$token,
                    'session_key'=>$ret['session_key']
                ]);
                if($exist['uid']) {
                    $uid = $exist['uid'];
                }else {
                    $uid = '';
                }
            }else {
                $insert = [
                    'nickname' => randomkeys(10),
                    'create_time' => time(),
                    'last_login_time' => time(),
                    'openid' => $ret['openid'],
                    'session_key' => $ret['session_key'],
                    'token' => $token
                ];
                Db::table('mp_user_mp')->insertGetId($insert);
                $uid = '';
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $json['token'] = $token;
        $json['uid'] = $uid;
        return ajax($json);
    }
    //保存用户信息
    public function userAuth() {
        $iv = input('post.iv');
        $encryptData = input('post.encryptedData');
        checkPost([
            'iv' => $iv,
            'encryptedData' => $encryptData
        ]);
        if(!$iv || !$encryptData) {
            return ajax([],-2);
        }
        $app = Factory::miniProgram($this->mp_config);
        try {
            $decryptedData = $app->encryptor->decryptData($this->myinfo['session_key'], $iv, $encryptData);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $user = $this->myinfo;
        try {
            $data['nickname'] = $decryptedData['nickName'];
            $data['avatar'] = $decryptedData['avatarUrl'];
            $data['sex'] = $decryptedData['gender'];
            $data['unionid'] = $decryptedData['unionId'];
            //未授权过的新用户
            if(!$user['user_auth']) {
                $data['user_auth'] = 1;
            }
            Db::table('mp_user_mp')->where('id','=',$this->myinfo['id'])->update($data);

        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $this->rs_delete($user['avatar']);//删除旧头像
        return ajax();
    }
    //检测用户是否授权
    public function checkUserAuth() {
        $uid = $this->myinfo['id'];
        try {
            $userauth = Db::table('mp_user_mp')->where('id',$uid)->value('user_auth');
            if($userauth == 1) {
                return ajax(true);
            }else {
                return ajax(false);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }
    //保存手机号
    public function getPhoneNumber() {
        $iv = input('post.iv');
        $encryptData = input('post.encryptedData');
        checkPost([
            'iv' => $iv,
            'encryptedData' => $encryptData
        ]);
        if(!$iv || !$encryptData) {
            return ajax([],-2);
        }
        $app = Factory::miniProgram($this->mp_config);
        try {
            $decryptedData = $app->encryptor->decryptData($this->myinfo['session_key'], $iv, $encryptData);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        try {
            $data['tel'] = $decryptedData['phoneNumber'];
            Db::table('mp_user_mp')->where('openid','=',$this->myinfo['openid'])->update($data);
            //如果已授权手机号
            if($this->myinfo['tel']) {
                return ajax($decryptedData);
            }
            Db::table('mp_user_mp')->where('id','=',$this->myinfo['id'])->update($data);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($decryptedData);
    }
    //绑定用户发送手机短信
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
    //绑定用户(手机号)
    public function bindUser() {
        $val['tel'] = input('post.tel');
        $val['code'] = input('post.code');
        checkInput($val);
        try {
            if(!is_tel($val['tel'])) {
                return ajax('invalid tel',6);
            }
            if($this->myinfo['uid']) {
                return ajax('此微信号已有关联手机号',34);
            }
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

            $whereUser = [
                ['tel','=',$val['tel']]
            ];
            $user_exist = Db::table('mp_user')->where($whereUser)->find();
            if($user_exist) {
                //用户存在,检测手机号是否绑定
                if($user_exist['mp_openid'] && $user_exist['mp_openid'] !== $this->myinfo['openid']) {
                    return ajax('此手机号已关联其他微信号',31);
                }else {
                    $update_user_data = [
                        'mp_openid' => $this->myinfo['openid'],
                        'last_login_time' => $this->myinfo['last_login_time'],
                        'login_type' => 1
                    ];
                    if(!$user_exist['nickname']) { $update_user_data['nickname'] = $this->myinfo['nickname']; }
                    if(!$user_exist['avatar']) { $update_user_data['avatar'] = $this->myinfo['avatar']; }

                    $update_data = [
                        'tel' => $val['tel'],
                        'uid' => $user_exist['id'],
                        'bind_time' => time()
                    ];
                    Db::startTrans();
                    Db::table('mp_user')->where($whereUser)->update($update_user_data);
                    Db::table('mp_user_mp')->where('id','=',$this->myinfo['id'])->update($update_data);
                    Db::commit();
                }
            }else {
                //用户不存在,创建新用户
                $insert_user_data = [
                    'nickname' => $this->myinfo['nickname'],
                    'sex' => $this->myinfo['sex'],
                    'avatar' => $this->myinfo['avatar'],
                    'tel' => $val['tel'],
                    'last_login_time' => $this->myinfo['last_login_time'],
                    'login_type' => 1,
                    'mp_openid' => $this->myinfo['openid'],
                    'create_time' => time()
                ];
                Db::startTrans();
                $uid = Db::table('mp_user')->insertGetId($insert_user_data);
                $update_data = [
                    'tel' => $val['tel'],
                    'uid' => $uid,
                    'bind_time' => time()
                ];
                Db::table('mp_user_mp')->where('id','=',$this->myinfo['id'])->update($update_data);
                Db::commit();
            }
        } catch (\Exception $e) {
            Db::rollback();
            $this->log($this->cmd,$e->getMessage());
            return ajax($e->getMessage(), -1);
        }
        return ajax();

    }





}