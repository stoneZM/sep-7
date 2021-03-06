<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 2017/9/7
 * Time: 19:54
 */

namespace app\user\controller;
use app\common\controller\Base;
use app\user\model\User;

define('SEND_MSG_CODE','send_smg_random_code');

class Login extends Base
{
    protected $user = null;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }

    public function login()
    {

      if (IS_POST){
           $phone_num = base64_decode(input('phone_num'));
           $password = base64_decode(input('password'));
           $rem_pwd = base64_decode(input('rem_pwd'));

           if ($this->user->login($phone_num,$password,$rem_pwd)){
              return json(array('code'=>1,'message'=>'登录成功'));
           }else{
               return json(array('code'=>0,'message'=>$this->user->getError()));
           }
      }
      return $this->fetch();
    }

    public function reg(){

    	if(IS_POST){

            $data['phone_num'] = base64_decode(input('phone_num'));
            $data['password'] =  md5(base64_decode(input('password')));
            $verify = base64_decode(input('verify_code'));
            $phone_code = base64_decode(input('phone_code'));

            $this->checkPhoneCode($phone_code,$data['phone_num']);
            $this->checkVerify($verify);
            if ($this->user->save($data)){
                 return json(array('code'=>1));
            }else{
               return json(array('code'=>0,'msg'=>$this->user->getError()));
            }
    	}
        $random_code = User::getRandomCode();
        cookie(SEND_MSG_CODE,$random_code);
        $this->assign('random_code',$random_code);
    	return $this->fetch();
    }

    public function reset_pwd(){

    	if (IS_POST) {
            $phone = base64_decode(input('phone_num'));
            $password = base64_decode(input('password'));
           if($this->user->reset_password($phone,$password)){
               return json(array('code'=>1,"message"=>'重置成功'));
           }else{
               return json(array('code'=>0,'message'=>$this->user->getError()));
           }
    	}
        $random_code = User::getRandomCode();
        cookie(SEND_MSG_CODE,$random_code);
        $this->assign('random_code',$random_code);
    	return $this->fetch();
    }

    public function get_phone_code(){

        $phone = base64_decode(input('phone'));
        $type = base64_decode(input('type'));
        $code = base64_decode(input('random_code'));
        $msg_code =  ($type=='reset_pwd')?1:0;
        $user = $this->user->get(array('phone_num'=>$phone));
        if ($user&&$msg_code==0){
            return json(array('code'=>0,'message'=>'该手机号已被注册'));
        }
        $random_code = cookie('send_smg_random_code');
        // 防刷验证码校验
        if ($random_code != $code){
            return json(array('code'=>0,'message'=>'非法请求'));
        }

        $result = \org\Smsbao::send($phone,$msg_code);
        if ($result['code']==0){
            $_is_success = 1;
            $message = $result['message'];
        }else{
            $_is_success = 0;
            $message = $result['message'];
        }
        return json(array('code'=>$_is_success,'message'=>$message));
    }

    public function logout(){
        $this->user->logout();
        $this->redirect(url('/user/login'));

    }




}