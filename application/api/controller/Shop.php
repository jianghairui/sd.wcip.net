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

    //商城主页顶部分类
    public function topCate() {
        try {
            $map = [
                ['del','=',0],
                ['status','=',1],
                ['pid','=',0]
            ];
            $list = Db::table('mp_goods_cate')->where($map)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }

    public function goodsList() {
        $val['type'] = input('post.type');
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $pcate_id = input('post.pcate_id',0);
        $cate_id = input('post.cate_id',0);
        $where = [];
        switch ($val['type']) {
            case 1:
                $where[] = ['g.batch','=',1];break;//小批量
            case 2:
                $where[] = ['g.sample','=',1];break;//免费拿样
            case 3:
                $where[] = ['g.mold','=',1];break;//免开模
            case 4:
                $where[] = ['g.recommend','=',1];break;//爆款推荐
            default:;
        }
        if($pcate_id) {
            $where[] = ['g.pcate_id','=',$pcate_id];
        }
        if($cate_id) {
            $where[] = ['g.cate_id','=',$cate_id];
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

    //购物车列表
    public function cartList() {
        try {
            $where = [
                ['c.uid','=',$this->myinfo['uid']]
            ];
            $list = Db::table('mp_cart')->alias('c')
                ->join("mp_goods g","c.goods_id=g.id","left")
                ->join("mp_goods_attr a","c.attr_id=a.id","left")
                ->field("c.id,c.uid,c.goods_id,c.num,c.use_attr,c.attr,c.attr_id,g.name,g.pics,g.price,g.carriage,g.stock,g.limit")
                ->where($where)->select();
            foreach ($list as &$v) {
                $v['cover'] = unserialize($v['pics'])[0];
                unset($v['pics']);
                if($v['use_attr']) {
                    $map_attr = [
                        ['id','=',$v['attr_id']],
                        ['goods_id','=',$v['goods_id']]
                    ];
                    $attr_exist = Db::table('mp_goods_attr')->where($map_attr)->find();

                    $price = $attr_exist['price'];
                }else {
                    $price = $v['price'];
                }
                $v['price'] = $price;
                $v['total_price'] = $price * $v['num'];
                $v['total_price'] = sprintf ( "%1\$.2f",$v['total_price']);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        return ajax($list);
    }
//加入购物车
    public function cartAdd() {
        $val['goods_id'] = input('post.goods_id');
        $val['num'] = input('post.num');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        $val['attr_id'] = input('post.attr_id',0);
        $val['use_attr'] = 0;
        if(!if_int($val['num'])) {
            return ajax($val['num'],-4);
        }
        try {
            $whereCart = [
                ['uid','=',$val['uid']]
            ];
            $count = Db::table('mp_cart')->where($whereCart)->count();
            if($count >= 10) {
                return ajax('购物车已经满啦',51);
            }
            $whereGoods = [
                ['id','=',$val['goods_id']]
            ];
            $goods_exist = Db::table('mp_goods')->where($whereGoods)->find();
            if(!$goods_exist) {
                return ajax($val['goods_id'],-4);
            }
            $map = [
                ['goods_id','=',$val['goods_id']],
                ['uid','=',$val['uid']]
            ];
            //是否使用规格
            if($val['attr_id']) {
                $val['use_attr'] = 1;
                $map_attr = [
                    ['id','=',$val['attr_id']],
                    ['goods_id','=',$val['goods_id']]
                ];
                $attr_exist = Db::table('mp_goods_attr')->where($map_attr)->find();
                if(!$attr_exist) {
                    return ajax($val['attr_id'],-4);
                }
                if($val['num'] > $attr_exist['stock']) {
                    return ajax('库存不足',52);
                }
                if($val['num'] > $goods_exist['limit']) {
                    return ajax('超出单笔限购数量',53);
                }
                $val['attr'] = $attr_exist['value'];
                $map[] = ['attr_id','=',$val['attr_id']];
                $cart_exist = Db::table('mp_cart')->where($map)->find();//购物车是否已经存在此商品
                if($cart_exist) {
                    if(($val['num'] + $cart_exist['num']) > $attr_exist['stock']) {
                        return ajax('商品+购件数(含购物车)超出库存',54);
                    }
                    if(($val['num'] + $cart_exist['num']) > $goods_exist['limit']) {
                        return ajax('超出单笔限购数量',53);
                    }
                    Db::table('mp_cart')->where($map)->setInc('num',$val['num']);
                }else {
                    $val['create_time'] = time();
                    Db::table('mp_cart')->insert($val);
                }
            }else {
                if($val['num'] > $goods_exist['stock']) {
                    return ajax('库存不足',52);
                }
                if($val['num'] > $goods_exist['limit']) {
                    return ajax('超出单笔限购数量',53);
                }
                $map[] = ['attr_id','=',0];
                $cart_exist = Db::table('mp_cart')->where($map)->find();//购物车是否已经存在此商品
                if($cart_exist) {
                    if(($val['num'] + $cart_exist['num']) > $goods_exist['stock']) {
                        return ajax('商品+购件数(含购物车)超出库存',54);
                    }
                    if(($val['num'] + $cart_exist['num']) > $goods_exist['limit']) {
                        return ajax('超出单笔限购数量',53);
                    }
                    Db::table('mp_cart')->where($map)->setInc('num',$val['num']);
                }else {
                    $val['create_time'] = time();
                    Db::table('mp_cart')->insert($val);
                }
            }

        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //购物车+++
    public function cartInc() {
        $val['cart_id'] = input('post.cart_id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['cart_id']],
                ['uid','=',$this->myinfo['uid']]
            ];
            $cart_exist = Db::table('mp_cart')->where($where)->find();
            if(!$cart_exist) {
                return ajax($val['cart_id'],-4);
            }
            $cart_exist['num'] += 1;
            $where_goods = [
                ['id','=',$cart_exist['goods_id']]
            ];
            $goods_exist = Db::table('mp_goods')->where($where_goods)->find();
            if($cart_exist['use_attr']) {
                $map_attr = [
                    ['id','=',$cart_exist['attr_id']],
                    ['goods_id','=',$cart_exist['goods_id']]
                ];
                $attr_exist = Db::table('mp_goods_attr')->where($map_attr)->find();
                if($cart_exist['num'] > $attr_exist['stock']) {
                    return ajax('此规格库存不足',55);
                }
                $price = $attr_exist['price'];
            }else {
                if($cart_exist['num'] > $goods_exist['stock']) {
                    return ajax('库存不足',52);
                }
                $price = $goods_exist['price'];
            }
            Db::table('mp_cart')->where($where)->setInc('num',1);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $data['price'] = $price;
        $data['num'] = $cart_exist['num'];
        $data['total_price'] = $price * $cart_exist['num'];
        $data['total_price'] = sprintf ( "%1\$.2f",$data['total_price']);
        return ajax($data);
    }
    //购物车---
    public function cartDec() {
        $val['cart_id'] = input('post.cart_id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['cart_id']],
                ['uid','=',$this->myinfo['uid']]
            ];
            $cart_exist = Db::table('mp_cart')->where($where)->find();
            if(!$cart_exist) {
                return ajax($val['cart_id'],-4);
            }
            $cart_exist['num'] -= 1;
            $where_goods = [
                ['id','=',$cart_exist['goods_id']]
            ];
            $goods_exist = Db::table('mp_goods')->where($where_goods)->find();
            if($cart_exist['use_attr']) {
                $map_attr = [
                    ['id','=',$cart_exist['attr_id']],
                    ['goods_id','=',$cart_exist['goods_id']]
                ];
                $attr_exist = Db::table('mp_goods_attr')->where($map_attr)->find();
                $price = $attr_exist['price'];
            }else {
                $price = $goods_exist['price'];
            }
            Db::table('mp_cart')->where($where)->setDec('num',1);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $data['price'] = $price;
        $data['num'] = $cart_exist['num'];
        $data['total_price'] = $price * $cart_exist['num'];
        $data['total_price'] = sprintf ( "%1\$.2f",$data['total_price']);
        return ajax($data);
    }
    //删除购物车
    public function cartDel() {
        $val['cart_id'] = input('post.cart_id');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['cart_id']],
                ['uid','=',$val['uid']]
            ];
            $cart_exist = Db::table('mp_cart')->where($where)->find();
            if(!$cart_exist) {
                return ajax($val['cart_id'],-4);
            }
            Db::table('mp_cart')->where($where)->delete();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //购买下单
    public function purchase() {
        $data['goods_id'] = input('post.goods_id');
        $data['num'] = input('post.num');
        $data['receiver'] = input('post.receiver');
        $data['tel'] = input('post.tel');
        $data['address'] = input('post.address');
        $data['uid'] = $this->myinfo['uid'];
        checkPost($data);
        $data['attr_id'] = input('post.attr_id');
        if(!if_int($data['num'])) {
            return ajax($data['num'],-4);
        }
        try {
            $userinfo = Db::table('mp_user')->where('id','=',$data['uid'])->field('id,vip')->find();
            $time = time();
            $pay_order_sn = create_unique_number('');
            $goods_exist = Db::table('mp_goods')->where('id','=',$data['goods_id'])->find();
            if (!$goods_exist) {
                return ajax('invalid goods_id', -4);
            }
            $shop_id = $goods_exist['shop_id'];
            if($data['num'] > $goods_exist['stock']) {
                return ajax('库存不足',52);
            }

            if($data['attr_id']) {
                $attr_id = input('post.attr_id');
                $where_attr = [
                    ['id','=',$attr_id],
                    ['goods_id','=',$data['goods_id']],
                ];
                $attr_exist = Db::table('mp_goods_attr')->where($where_attr)->find();
                if(!$attr_exist) {
                    return ajax('invalid attr_id',-4);
                }
                if($data['num'] > $attr_exist['stock']) {
                    return ajax('库存不足',52);
                }
                if($userinfo['vip'] && $goods_exist['use_vip_price']) {
                    $unit_price = $attr_exist['vip_price'];
                    $order_detail['vip_price'] = $attr_exist['vip_price'];
                }else {
                    $unit_price = $attr_exist['price'];
                }
                $order_detail['unit_price'] = $attr_exist['price'];
                $order_detail['use_attr'] = 1;
                $order_detail['attr_id'] = $data['attr_id'];
                $order_detail['attr'] = $attr_exist['value'];
            }else {
                if($userinfo['vip'] && $goods_exist['use_vip_price']) {
                    $unit_price = $goods_exist['vip_price'];
                    $order_detail['vip_price'] = $goods_exist['vip_price'];
                }else {
                    $unit_price = $goods_exist['price'];
                }
                $order_detail['unit_price'] = $goods_exist['price'];
                $order_detail['use_attr'] = 0;
                $order_detail['attr_id'] = 0;
                $order_detail['attr'] = '默认';
            }

            $total_price = $unit_price * $data['num'] + $goods_exist['carriage'];

            $insert_data = [
                'uid' => $data['uid'],
                'shop_id' => $shop_id,
                'pay_order_sn' => $pay_order_sn,
                'order_sn' => create_unique_number(''),
                'total_price' => $total_price,
                'pay_price' => $total_price,
                'carriage' => $goods_exist['carriage'],
                'receiver' => $data['receiver'],
                'tel' => $data['tel'],
                'address' => $data['address'],
                'create_time' => $time,
            ];

            Db::startTrans();
            $order_id = Db::table('mp_order')->insertGetId($insert_data);//店铺订单

            $order_detail['order_id'] = $order_id;
            $order_detail['goods_id'] = $goods_exist['id'];
            $order_detail['goods_name'] = $goods_exist['name'];
            $order_detail['num'] = $data['num'];
            $order_detail['unit_price'] = $goods_exist['price'];
            if($userinfo['vip'] && $goods_exist['use_vip_price']) {
                $order_detail['use_vip_price'] = 1;
            }
            $order_detail['total_price'] = $unit_price * $data['num'] + $goods_exist['carriage'];
            $order_detail['carriage'] = $goods_exist['carriage'];
            $order_detail['create_time'] = $time;

            $order_unite = [
                'uid' => $data['uid'],
                'pay_order_sn' => $pay_order_sn,
                'pay_price' => $insert_data['pay_price'],
                'order_ids' => implode(',',[$order_id]),
                'status' => 0,
                'create_time' => time()
            ];

            Db::table('mp_order_detail')->insert($order_detail);//订单商品详情
            Db::table('mp_goods')->where('id', $data['goods_id'])->setDec('stock',$data['num']);
            if($data['attr_id']) {
                Db::table('mp_goods_attr')->where($where_attr)->setDec('stock',$data['num']);
            }
            Db::table('mp_order_unite')->insert($order_unite);//统一支付订单
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(), -1);
        }
        return ajax($pay_order_sn);
    }

    //购物车去支付
    public function cartToPurchase() {
        $cart_ids = input('post.cart_id',[]);
        $val['receiver'] = input('post.receiver');
        $val['tel'] = input('post.tel');
        $val['address'] = input('post.address');
        $val['uid'] = $this->myinfo['uid'];
        checkPost($val);
        if(empty($cart_ids)) {  return ajax('请选择要结算的商品',56);}
        if(array_unique($cart_ids) !== $cart_ids) { return ajax($cart_ids,-4);}
        try {
            $userinfo = Db::table('mp_user')->where('id','=',$val['uid'])->field('id,vip')->find();
            $time = time();
            $whereCart = [
                ['c.id','IN',$cart_ids],
                ['c.uid','=',$val['uid']]
            ];
            $cart_list = Db::table('mp_cart')->alias('c')
                ->join("mp_goods g","c.goods_id=g.id","left")
                ->join("mp_goods_attr a","c.attr_id=a.id","left")
                ->where($whereCart)
                ->field("c.*,g.shop_id,g.price,g.use_vip_price,g.vip_price,g.name,g.carriage,g.weight,g.stock AS total_stock,a.id AS aid,a.price AS attr_price,a.vip_price AS attr_vip_price,a.stock,a.value")
                ->select();
            if(count($cart_ids) != count($cart_list)) { return ajax($cart_ids,-4);}

            $pay_order_sn = create_unique_number('');
            //筛选出不同的商户ID,每个商户一个订单
            $shop_ids = [];
            foreach ($cart_list as $v) {
                if(!in_array($v['shop_id'],$shop_ids)) {
                    $shop_ids[] = $v['shop_id'];
                }
            }

            $order_data_all = [];
            $insert_detail_all_arr = [];
            $card_delete_ids = []; //库存不足的商品,从购物车中删除
            $unite_order_price = 0;

            foreach ($shop_ids as $shop_id) {
                $total_order_price = 0;
                $carriage = 0;
                $insert_detail_all = [];

                foreach ($cart_list as $v) {
                    if($v['shop_id'] == $shop_id) {
                        if($v['use_attr']) {    //带规格商品
                            if(!$v['aid']) {  return ajax('invalid attr_id',-4);}
                            if($v['num'] > $v['stock']) { $card_delete_ids[] = $v['id']; }

                            if($userinfo['vip'] && $v['use_vip_price']) {
                                $unit_price = $v['attr_vip_price'];
                                $insert_detail['vip_price'] = $v['attr_vip_price'];
                            }else {
                                $unit_price = $v['attr_price'];
                                $insert_detail['vip_price'] = 0;
                            }

                            $insert_detail['unit_price'] = $v['attr_price'];
                            $insert_detail['use_attr'] = 1;
                            $insert_detail['attr_id'] = $v['attr_id'];
                            $insert_detail['attr'] = $v['value'];
                        }else { //不带规格商品
                            if($v['num'] > $v['total_stock']) {$card_delete_ids[] = $v['id'];}

                            if($userinfo['vip'] && $v['use_vip_price']) {
                                $unit_price = $v['vip_price'];
                                $insert_detail['vip_price'] = $v['vip_price'];
                            }else {
                                $unit_price = $v['price'];
                                $insert_detail['vip_price'] = 0;
                            }
                            $insert_detail['unit_price'] = $v['price'];
                            $insert_detail['use_attr'] = 0;
                            $insert_detail['attr_id'] = 0;
                            $insert_detail['attr'] = '默认';
                        }

                        $total_order_price += ($unit_price * $v['num']) + $v['carriage'];

                        $carriage += $v['carriage'];

                        $insert_detail['goods_id'] = $v['goods_id'];
                        $insert_detail['goods_name'] = $v['name'];
                        $insert_detail['num'] = $v['num'];
                        if($userinfo['vip'] && $v['use_vip_price']) {
                            $insert_detail['use_vip_price'] = 1;
                        }else {
                            $insert_detail['use_vip_price'] = 0;
                        }
                        $insert_detail['total_price'] = $unit_price * $v['num'] + $v['carriage'];
                        $insert_detail['carriage'] = $v['carriage'];
                        $insert_detail['create_time'] = $time;
                        $insert_detail_all[] = $insert_detail;
                    }
                }

                $order_data = [
                    'uid' => $val['uid'],
                    'shop_id' => $shop_id,
                    'pay_order_sn' => $pay_order_sn,
                    'order_sn' => create_unique_number(''),
                    'total_price' => $total_order_price,
                    'pay_price' => $total_order_price,
                    'carriage' => $carriage,
                    'receiver' => $val['receiver'],
                    'tel' => $val['tel'],
                    'address' => $val['address'],
                    'create_time' => $time,
                ];
                $order_data_all[] = $order_data;
                $insert_detail_all_arr[] = $insert_detail_all;

                $unite_order_price += $total_order_price;
            }
            if(!empty($card_delete_ids)) {
                $whereDelete = [
                    ['id','in',$card_delete_ids],
                    ['uid','=',$val['uid']]
                ];
                Db::table('mp_cart')->where($whereDelete)->delete();
                return ajax('部分商品库存不足,请重新结算',57);
            }

            Db::startTrans();
            $order_ids = [];
            foreach ($order_data_all as $key=>$value) {
                $order_id = Db::table('mp_order')->insertGetId($value);
                $order_detail_all = $insert_detail_all_arr[$key];
                foreach ($order_detail_all as $k=>&$v) {
                    $v['order_id'] = $order_id;
                }
                Db::table('mp_order_detail')->insertAll($order_detail_all);
                $order_ids[] = $order_id;
            }

            $order_unite = [
                'uid' => $this->myinfo['uid'],
                'pay_order_sn' => $pay_order_sn,
                'pay_price' => $unite_order_price,
                'order_ids' => implode(',',$order_ids),
                'status' => 0,
                'create_time' => time()
            ];
            Db::table('mp_order_unite')->insert($order_unite);

            foreach ($cart_list as $v) {
                Db::table('mp_goods')->where('id',$v['goods_id'])->setDec('stock',$v['num']);
                if($v['use_attr']) {
                    $where_attr = [
                        ['id','=',$v['attr_id']],
                        ['goods_id','=',$v['goods_id']],
                    ];
                    Db::table('mp_goods_attr')->where($where_attr)->setDec('stock',$v['num']);
                }
            }
            $whereDelete = [
                ['id','in',$cart_ids],
                ['uid','=',$this->myinfo['uid']]
            ];
            Db::table('mp_cart')->where($whereDelete)->delete();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(), -1);
        }
        return ajax($pay_order_sn);

    }




}