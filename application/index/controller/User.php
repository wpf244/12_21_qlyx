<?php
namespace app\index\controller;

class User extends BaseHome
{
    public function index()
    {
        $uid=session("userid");
        $re=db("user")->where("uid=$uid")->find();
        if($re){
            $this->assign("re",$re);
            
            return $this->fetch();
        }else{
            $this->redirect("Login/out");
        }
    }
    public function usave()
    {
        $uid=session("userid");
        $re=db("user")->where("uid=$uid")->find();
        if($re){
            $data['username']=input('username');
            $res=db("user")->where("uid=$uid")->update($data);
            echo '0';
        }else{
            echo '1';
        }
    }
}