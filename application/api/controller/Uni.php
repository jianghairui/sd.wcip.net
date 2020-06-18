<?php
namespace app\api\controller;

use think\Db;
class Uni extends Base {

    public function goodsList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $where = [
            ['g.status','=',1]
        ];
        $order = ['g.id'=>'DESC'];
        try {
            $list = Db::table('mp_goods')->alias('g')
                ->join('mp_user u','g.shop_id=u.id','left')
                ->field('g.id,g.name,g.price,g.use_vip_price,g.vip_price,g.poster,g.pics,u.org')
                ->where($where)
                ->order($order)
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['poster'] = unserialize($v['pics'])[0];
        }
        return ajax($list);
    }

}
