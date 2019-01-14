<?php
/**
 * BaseAdmin.php
 * 朵朵科技
 * 官方网址 www.dd371.com
 * ===============================
 * @uses ECHO
 * @date 2018.11.29
 * @version 1.0
 *
 * **/
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Sever;

class BaseAdmin extends Controller{
    function _initialize(){
        if (!defined('CONTROLLER_NAME')) {
            define('CONTROLLER_NAME', $this->request->controller());
        }
        if (!defined('ACTION_NAME')) {
            define('ACTION_NAME', $this->request->action());
        }
        if(empty(session('uid'))){
            $this->redirect("login/index");
        }
        $sys=db('sys')->where("id=1")->find();
        $this->assign("sys",$sys);
        
        $uid=session('uid');
        $admin=db('admin')->where("id=$uid")->find();
        $this->assign("admin",$admin);

        $level=$admin['level'];
        if($level == 0){
            $controls=db("carte")->where("pid=0")->order("c_sort asc")->select();
            foreach($controls as $ks=> $vs){
                $controls[$ks]['ways']=db("carte")->where("pid={$vs['cid']}")->order("c_sort asc")->select();
            }
            $this->assign("controls",$controls);
        }else{
            $controls_arr=explode(",",$admin['control']);
            $way_arr=explode(",",$admin['way']);

            $controls=db("carte")->where(array("cid"=>array("in",$controls_arr)))->order("c_sort asc")->select();
            foreach($controls as $ks => $vs){
                $controls[$ks]['ways']=db("carte")->where(array("cid"=>array("in",$way_arr)))->where("pid={$vs['cid']}")->order("c_sort asc")->select();
            }
            $this->assign("controls",$controls);
        }
        
        $this->logs=new Sever();
        

        
    }
}