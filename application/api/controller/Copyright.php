<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/4/1
 * Time: 13:35
 */
namespace app\api\controller;

use think\Db;
class Copyright extends Base {


    public function slideList() {
        $where = [
            ['status', '=', 1],
            ['type', '=', 3]
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
    //版权列表
    public function ipList() {
        $param['search'] = input('post.search');
        $param['cate_id'] = input('post.cate_id');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];

        if($param['search']) {
            $where[] = ['i.title','like',"%{$param['search']}%"];
        }
        if($param['cate_id']) {
            $where[] = ['i.cate_id','=',$param['cate_id']];
        }

        try {
            $list = Db::table('mp_ip')->alias('i')
                ->join('mp_ip_cate c','i.cate_id=c.id','left')
                ->field('i.id,i.title,i.cover,c.cate_name')
                ->where($where)
                ->order(['i.id'=>'DESC'])->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }

        return ajax($list);
    }
    //活动详情
    public function ipDetail() {
        $val['ip_id'] = input('post.ip_id');
        checkPost($val);
        try {
            $info = Db::table('mp_ip')
                ->where('id','=',$val['ip_id'])
                ->find();
            if(!$info) {
                return ajax('invalid ip_id',-4);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($info);
    }

    //ip分类
    public function ipCateList() {
        try {
            $cate_list = Db::table('mp_ip_cate')->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($cate_list);
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

    //申请合作授权
    public function ipApply() {
        $this->checkUid();
        $val['uid'] = $this->myinfo['uid'];
        $val['ip_id'] = input('post.ip_id');
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
            //检测版权是否存在

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
            Db::table('mp_ip_consult')->insert($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }



}