<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/4/8
 * Time: 14:18
 */
namespace app\api\controller;

use think\Db;
class Xuqiu extends Base {

    //获取轮播图
    public function slideList() {
        $where = [
            ['status', '=', 1],
            ['type', '=', 6]
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

    public function factoryList() {
        $recommend = input('post.recommend');
        $whereRole = [];
        $whereRole[] = ['role','=',2];
//        if($recommend) {
//            $whereRole[] = ['recommend','=',1];
//        }
        try {
            $list = Db::table('mp_user_role')->where($whereRole)
                ->field('uid,role,org,cover AS logo')
                ->order(['id'=>'DESC'])
                ->limit(0,9)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }

    public function xuqiuRelease() {
        $userinfo = $this->getUserInfo();
        if(!in_array($userinfo['role'],[1,2])) {
            return ajax('此角色无权操作',50);
        }
        $val['uid'] = $this->myinfo['uid'];
        $val['title'] = input('post.title','');
        $val['min_price'] = input('post.min_price','');
        $val['max_price'] = input('post.max_price','');
        $val['num'] = input('post.num','');
        $val['deadline'] = input('post.deadline','');
        $val['invoice'] = input('post.invoice','');
        $val['sample'] = input('post.sample','');
        $val['area'] = input('post.area','');
        $val['tel'] = input('post.tel','');
        $val['linkman'] = input('post.linkman','');
        checkPost($val);
        $val['desc'] = input('post.desc','');
        $val['create_time'] = time();
        $image = input('post.pics',[]);

        if(!is_currency($val['min_price']) || !is_currency($val['max_price'])) {
            return ajax('无效的金额',47);
        }
        if(!if_int($val['num'])) {
            return ajax('无效的数字',48);
        }
        if(!is_date($val['deadline'])) {
            return ajax('无效的日期',49);
        }
        if(!is_tel($val['tel'])) {
            return ajax('无效的手机号',6);
        }
        try {
            if(is_array($image)) {
                if(count($image) > 9) {
                    return ajax('最多上传9张图片',8);
                }
                //验证图片是否存在
                foreach ($image as $v) {
                    $qiniu_exist = $this->qiniuFileExist($v);
                    if($qiniu_exist !== true) {
                        return ajax($qiniu_exist['msg'] . ' :'.$v,5);
                    }
                }
            }else {
                $image = [];
            }
            $image_array = [];
            //转移七牛云图片
            foreach ($image as $v) {
                $qiniu_move = $this->moveFile($v,'upload/xuqiu/');
                if($qiniu_move['code'] == 0) {
                    $image_array[] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'] .' :' . $v . '',-1);
                }
            }
            $val['pics'] = serialize($image_array);
            Db::table('mp_xuqiu')->insert($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();

    }

    public function xuqiuList() {
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [
            ['x.del','=',0]
        ];
        try {
            $list = Db::table('mp_xuqiu')->alias('x')
                ->join("mp_user u","x.uid=u.id","left")
                ->field("x.id,x.uid,x.title,x.min_price,x.max_price,x.num,x.deadline,x.invoice,x.sample,x.area,x.num,u.org,x.pics,x.tel,x.linkman,x.desc")
                ->order(['x.id'=>'DESC'])
                ->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
        }
        return ajax($list);
    }

    public function xuqiuDetail() {
        $param['xuqiu_id'] = input('post.xuqiu_id');
        checkPost($param);
        try {
            $whereXuqiu = [
                ['x.id','=',$param['xuqiu_id']]
            ];
            $xuqiu_exist = Db::table('mp_xuqiu')->alias('x')
                ->join('mp_user u','x.uid=u.id','left')
                ->field('x.*,u.org,u.avatar')
                ->where($whereXuqiu)->find();
            if(!$xuqiu_exist) {
                return ajax('invliad xuqiu_id',-4);
            }
            $xuqiu_exist['pics'] = unserialize($xuqiu_exist['pics']);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($xuqiu_exist);

    }



}