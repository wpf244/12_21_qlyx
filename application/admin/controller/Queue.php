<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;

class Queue extends BaseAdmin{

    public function invade(){
        $phone = Request::instance()->param('phone', '');
        $username = Request::instance()->param('username', '');
        $status = Request::instance()->param('status', -1);
        $map=[];
        if($phone){
            $map['phone']=array("like",'%'.$phone.'%');
        }
        if($status != -1){
            $map['status'] = $status;
        }
        if($username){
            $map['username']=array("like",'%'.$username.'%');
        }
        $list = db('queue')->alias('q')->order('start_time desc')->where($map)->join('ddsc_user u', 'q.uid=u.uid')->paginate(20,false,['query'=>request()->param()]);
        $this->assign('list', $list);
        $this->assign('phone', $phone);
        $this->assign('username', $username);
        $this->assign('status', $status);
        return $this->fetch('invade');
    }
}