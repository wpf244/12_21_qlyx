<?php
namespace app\index\controller;



class Login extends Common
{
    public function login()
    {
        return view('login');
    }
    public function register()
    {
        return $this->fetch();
    }
    public function checkphone()
    {
        $phone=input('phone');
        $code =mt_rand(100000,999999);       
        $data['phone']=$phone;
        $data['code']=$code;
        $data['time']=time();
        $re=\db("sms_code")->where("phone='$phone'")->find();
        if($re){
            $del=db("sms_code")->where("phone='$phone'")->delete();
        }
        $rea=db("sms_code")->insert($data);
 
        Post($phone,$code);

    }
    public function checkphones()
    {
        $phone=input('phone');
        $reu=db("user")->where("phone",$phone)->find();
        if($reu){
            $code =mt_rand(100000,999999);       
            $data['phone']=$phone;
            $data['code']=$code;
            $data['time']=time();
            $re=\db("sms_code")->where("phone='$phone'")->find();
            if($re){
                $del=db("sms_code")->where("phone='$phone'")->delete();
            }
            $rea=db("sms_code")->insert($data);
    
            Post($phone,$code);
        }else{
            
            echo '1';exit;
        }
        

    }
    public function save()
    {
        $phone=input('phone');
        $res=db("user")->where("phone",$phone)->find();
        if($res){
            $this->error("此手机号码已经注册",url('register'));exit;
        }else{
            $code=input('yzm');
            $re=db("sms_code")->where(['phone'=>$phone,'code'=>$code])->find();
            if($re){
                $time=$re['time'];
                $times=time();
                $c_time=($times-$time);
                if($c_time < 300){
                    $del=db("sms_code")->where("id={$re['id']}")->delete();
                                       
                        $data['phone']=$phone;                      
                        $data['pwd']=md5(\input('pwd'));
                        $data['username']=input('username');
                        $data['time']=time();
        
                        $rea=db("user")->insert($data);
                        if($rea){
                            $this->success("注册成功",url('User/recharge_change'));
                        }else{
                            $this->success("注册失败",url('register'));
                        }
                 
                }else{
                    $this->error("验证码已失效",url('register'));
                }
            }else{
                $this->error("验证码错误",url('register'));
            }
        }
      
    }
    public function forget()
    {
        return $this->fetch();
    }
    public function usave()
    {
        $phone=input('phone');
        $res=db("user")->where("phone",$phone)->find();
        if($res){
            $code=input('yzm');
            $re=db("sms_code")->where(['phone'=>$phone,'code'=>$code])->find();
            if($re){
                $time=$re['time'];
                $times=time();
                $c_time=($times-$time);
                if($c_time < 300){
                    $del=db("sms_code")->where("id={$re['id']}")->delete();
                        $uid=$res['uid'];              
                        $data['pwd']=md5(\input('pwd')); 
        
                        $rea=db("user")->where("uid=$uid")->update($data);
                        if($rea){
                            $this->success("修改成功",url('login'));
                        }else{
                            $this->success("修改成功",url('login'));
                        }
                 
                }else{
                    $this->error("验证码已失效",url('forget'));
                }
            }else{
                $this->error("验证码错误",url('forget'));
            }
            
        }else{
            $this->error("此手机号码未注册",url('register'));exit;
        }
    }
    public function check(){
        
         $u_name=input('post.phone');
         $pwd=md5(input('post.pwd'));
         $re=db("user")->where(array('phone'=>$u_name,'pwd'=>$pwd))->find();
         if($re){
            session('userid',$re['uid']);
            if($re['status'] != 1){
                $this->success('登陆成功 ^_^',url('User/recharge_change'));
            }else{
                $this->success('登陆成功 ^_^',url('News/index'));
            }
            
         }else{
             $this->error('登录失败：用户名或密码错误。',url('Login/login'));
         }
     }
     public function out(){
        session("userid",null);
        $this->redirect('Login/login');
    }



}