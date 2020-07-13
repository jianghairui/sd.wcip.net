<?php
namespace app\api\controller;

use think\Db;
class Sample extends Base {

    public function sampleList() {
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [
            ['s.del','=',0]
        ];
        try {
            $list = Db::table('mp_sample')->alias('s')
                ->join('mp_user_role r','s.shop_id=r.uid','left')
                ->where($where)
                ->field('s.id as sample_id,s.name,s.poster,s.stock,r.org')
                ->limit(($curr_page - 1)*$perpage,$perpage)
                ->order(['s.id'=>'DESC'])
                ->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }

    public function sampleDetail() {
        $post['sample_id'] = input('param.sample_id');
        checkPost($post);
        $whereSample = [
            ['s.id','=',$post['sample_id']]
        ];
        try {
            $info = Db::table('mp_sample')->alias('s')
                ->join('mp_user_role r','s.shop_id=r.uid','left')
                ->where($whereSample)
                ->field('s.id as sample_id,s.name,s.poster,s.stock,s.detail,s.pics,s.use_video,s.video_url,s.shop_id,r.org')
                ->find();
            if(!$info) {
                return ajax('invalid sample_id',-4);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $info['pics'] = unserialize($info['pics']);
        return ajax($info);
    }

    public function sampleTake() {
        $post['sample_id'] = input('param.sample_id');
        checkPost($post);
        $whereSample = [
            ['id','=',$post['sample_id']]
        ];
        $userinfo = $this->getUserInfo();
        if($userinfo['role'] !== 1) {
            return ajax('只有认证文旅机构可以领取',68);
        }
        try {
            $sample_exist = Db::table('mp_sample')->where($whereSample)->find();
            if(!$sample_exist) {
                return ajax('invalid sample_id',-4);
            }
            $whereRecord = [
                ['uid','=',$this->myinfo['uid']],
                ['sample_id','=',$post['sample_id']]
            ];
            if($sample_exist['stock'] <= 0) {
                return ajax('免费名额不足',67);
            }
            $record_exist = Db::table('mp_sample_record')->where($whereRecord)->find();
            if($record_exist) {
                return ajax('每个用户只能领取一次',66);
            }
            $insert_data = [
                'uid' => $this->myinfo['uid'],
                'sample_id' => $post['sample_id'],
                'create_time' => time()
            ];
            Db::startTrans();
            Db::table('mp_sample_record')->insert($insert_data);
            Db::table('mp_sample')->where($whereSample)->setDec('stock',1);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

//    public function



}