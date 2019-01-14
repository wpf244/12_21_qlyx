<?php
namespace app\index\controller;

use think\Controller;



class BaseHome extends Controller
{
    function _initialize(){
        
        if(empty(session('userid'))){
            $this->redirect("Login/login");
        }
        $sys=db('sys')->where("id=1")->find();
        $this->assign("sys",$sys);
        
        if (!defined('CONTROLLER_NAME')) {
            $controller = $this->request->controller();
        }else{
            $controller = '';
        }

        $re=db("user")->where("uid", session('userid'))->find();
        if($re['status'] != 1 && $controller != "User"){
            $this->redirect("User/bank_card");
        }
    }

    public function ddsc_log($content){
        db("log")->insert(['content'=>$content, 'time'=>time()]);
    }
}