<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/8
 * Time: 11:11
 */

namespace app\api\controller;
use my\Sendsms;
use think\Db;
use think\Exception;

class Api extends Base
{
    //获取轮播图列表
    public function slideList() {
        $where = [
            ['status', '=', 1],
            ['type', '=', 1]
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

    public function videoList() {
        $whereVideo = [
            ['use_video','=',1]
        ];
        try {
            $list = Db::table('mp_goods')
                ->where($whereVideo)
                ->field('id,name,poster,video_url')
                ->limit(0,3)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }

    public function goodsList() {
        $val['type'] = input('post.type');
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $where = [];
        switch ($val['type']) {
            case 1:;break;//小批量
            case 2:;break;//免费拿样
            case 3:;break;//免开模
            case 4:;break;//爆款推荐
            default:;
        }
        $order = ['id'=>'DESC'];
        try {
            $list = Db::table('mp_goods')->alias('g')
                ->join('mp_user u','g.shop_id=u.id','left')
                ->field('g.id,g.name,g.price,g.use_vip_price,g.vip_price,g.poster,u.org')
                ->where($where)
                ->order($order)
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);

    }































    //收集formid
    public function collectFormid() {
        $val['formid'] = input('post.formid');
        $val['uid'] = $this->myinfo['id'];
        checkPost($val);
        if($val['formid'] == 'the formId is a mock one') {
            return ajax();
        }
        $val['create_time'] = time();
        try {
            Db::table('mp_formid')->insert($val);
        } catch (\Exception $e) {
            $this->log($this->cmd,$e->getMessage());
            return ajax($e->getMessage(), -1);
        }
        return ajax($val);
    }

    //获取省级列表
    public function getProvinceList() {
        try {
            $where = [
                ['pcode','=',0]
            ];
            $list = Db::table('mp_city')->where($where)->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }

    //获取城市列表
    public function getCityList() {
        $val['provinceCode'] = input('post.province_code');
        try {
            if($val['provinceCode']) {
                $where = [
                    ['pcode','=',$val['provinceCode']],
                    ['level','=',2]
                ];
            }else {
                return ajax([]);
            }
            $list = Db::table('mp_city')->where($where)->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }

    //获取区列表
    public function getRegionList() {
        $val['cityCode'] = input('post.city_code');
        try {
            if($val['cityCode']) {
                $where = [
                    ['pcode','=',$val['cityCode']],
                    ['level','=',3]
                ];
            }else {
                return ajax([]);
            }
            $list = Db::table('mp_city')->where($where)->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }






}