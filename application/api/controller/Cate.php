<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/4/9
 * Time: 10:10
 */
namespace app\api\controller;

use think\Db;
class Cate extends Base {

    public function videoList() {
        $whereVideo = [
            ['use_video','=',1]
        ];
        try {
            $list = Db::table('mp_goods')
                ->where($whereVideo)
                ->field('id,name,poster,video_url')
                ->limit(0,10)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }

    public function ipCateList() {
        $whereCate = [
            ['recommend','=',1]
        ];
        try {
            $cate_list = Db::table('mp_ip_cate')->where($whereCate)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($cate_list);
    }


    //获取分类
    public function goodsCateList() {
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




}
