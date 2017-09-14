<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 2017/9/11
 * Time: 23:54
 */

namespace app\admin\controller;
use app\admin\model\UserModel;
use app\common\controller\Base;
define('USER_MAN_BACK_PARAMS','USERMANBACKPARAMS');

class User extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->user_model = new UserModel();
    }

    public function index(){


        $is_vip = input('is_vip')?:0;
        $user_list = $this->user_model->getUserList($is_vip);
        $data = array(
            'user_list' => $user_list,
            'page' => $user_list->render()
        )  ;
        $select_str['select_0'] = ($is_vip==0)?'selected="selected"':'';
        $select_str['select_1'] = ($is_vip==1)?'selected="selected"':"";
        $select_str['select_2'] = ($is_vip==2)?'selected="selected"':"";

        $this->assign($data);
        $this->assign($select_str);
        $this->assign('meta_title','用户列表');
        $params['is_vip'] =  $is_vip;
        $params['page'] = array_key_exists('page',$_REQUEST)?$_REQUEST['page']:1;
        cookie(USER_MAN_BACK_PARAMS,json_encode($params));
        return $this->fetch();
    }

    public function manage(){

        if(IS_POST){
            $id = input('id');
            $data['phone_num'] = input('phone_num');
            $password = input('password');
            if ($password){
                $data['password'] = md5($password);
            }
            $data['status'] = input('status');
            $data['is_vip'] = input('is_vip');

            $user_exist  = $this->user_model->where(array('id'=>$id))->count();
            if (!$user_exist){
                return $this->error('用户不存在');
            }
            //检查手机号是否存在
            $user_info = $this->user_model->where(array('phone_num'=>$data['phone_num']))->find();
            if ($user_info){
                return $this->error('手机号已存在');
            }
            if($this->user_model->where(array('id'=>$id))->update($data)){
                $back_params = json_decode(cookie(USER_MAN_BACK_PARAMS),true);

                return $this->success('编辑成功',url('/admin/user/index',$back_params));
            }else{
                return $this->error('编辑失败');
            }
        }
        $id = input('id');

        if (!$id){
            return $this->error('参数错误');
        }
        $user = $this->user_model->getUser($id,1);
        if (!$user){
            return $this->error($this->user_model->getError());
        }

        $this->assign($user);
        $this->assign('meta_title','用户管理');
        return $this->fetch();
    }

}