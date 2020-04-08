<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/4/8
 * Time: 16:58
 */
namespace app\api\controller;


use think\Db;
class Shop extends Base {


    public function goodsDetail() {
        $val['goods_id'] = input('post.goods_id');
        checkPost($val);
        $where = [
            ['g.id','=',$val['goods_id']]
        ];
        try {
            $info = Db::table('mp_goods')->alias('g')
                ->join('mp_user u','g.shop_id=u.id','left')
                ->field('g.*,u.org')
                ->where($where)
                ->find();
            if(!$info) {
                return ajax('invalid goods_id',-4);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }




}