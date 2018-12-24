<?php
namespace app\index\controller;

class News extends BaseHome
{
    public function index()
    {
        $uid=session("userid");
        $re=db("user")->where("uid=$uid")->find();
        if($re){
            $this->assign("re",$re);
            //游戏规则
            $reb=db("lb")->where("fid=1")->find();
            $this->assign("reb",$reb);
            return $this->fetch();
        }else{
            $this->redirect("Login/out");
        } 
    }
    public function video()
    {
        $res=db("video")->where("status=1")->order("sort asc")->select();
        $this->assign("res",$res);
        return $this->fetch();
    }
    public function change()
    {
        $id=\input("id");
        $re=db("video")->where("id=$id")->find();
        if($re){
            $res=db("video")->where("id=$id")->setInc("play",1);
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo '1';
        }
    }
}