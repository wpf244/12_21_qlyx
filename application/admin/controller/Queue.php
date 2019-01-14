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
        $list = db('queue')->alias('q')->field('q.queue_id,q.status,q.uid,q.look,q.start_time,q.end_time,u.phone,u.username')->order('start_time desc')->where($map)->join('ddsc_user u', 'q.uid=u.uid')->paginate(20,false,['query'=>request()->param()]);
        $this->assign('list', $list);
        $this->assign('phone', $phone);
        $this->assign('username', $username);
        $this->assign('status', $status);
        return $this->fetch('invade');
    }

    public function invade_2(){
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
        $list = db('queue_2')->alias('q')->field('q.queue_id,q.status,q.uid,q.look,q.start_time,q.end_time,u.phone,u.username')->order('start_time desc')->where($map)->join('ddsc_user u', 'q.uid=u.uid')->paginate(20,false,['query'=>request()->param()]);
        $this->assign('list', $list);
        $this->assign('phone', $phone);
        $this->assign('username', $username);
        $this->assign('status', $status);
        return $this->fetch('invade');
    }

    public function invade_3(){
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
        $list = db('queue_3')->alias('q')->field('q.queue_id,q.status,q.uid,q.look,q.start_time,q.end_time,u.phone,u.username')->order('start_time desc')->where($map)->join('ddsc_user u', 'q.uid=u.uid')->paginate(20,false,['query'=>request()->param()]);
        $this->assign('list', $list);
        $this->assign('phone', $phone);
        $this->assign('username', $username);
        $this->assign('status', $status);
        return $this->fetch('invade');
    }

    public function invade_4(){
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
        $list = db('queue_4')->alias('q')->field('q.queue_id,q.status,q.uid,q.look,q.start_time,q.end_time,u.phone,u.username')->order('start_time desc')->where($map)->join('ddsc_user u', 'q.uid=u.uid')->paginate(20,false,['query'=>request()->param()]);
        $this->assign('list', $list);
        $this->assign('phone', $phone);
        $this->assign('username', $username);
        $this->assign('status', $status);
        return $this->fetch('invade');
    }

    public function invade_5(){
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
        $list = db('queue_5')->alias('q')->field('q.queue_id,q.status,q.uid,q.look,q.start_time,q.end_time,u.phone,u.username')->order('start_time desc')->where($map)->join('ddsc_user u', 'q.uid=u.uid')->paginate(20,false,['query'=>request()->param()]);
        $this->assign('list', $list);
        $this->assign('phone', $phone);
        $this->assign('username', $username);
        $this->assign('status', $status);
        return $this->fetch('invade');
    }

    /**
     * 进化管理
     *
     * @return void
     */
    public function monster(){
        $list = db('monster')->select();
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function monster_update(){
        $id = Request::instance()->param('id', 0);
        $detail = db("monster")->where('id', $id)->find();
        $this->assign('detail', $detail);
        return $this->fetch();
    }

    public function monster_update_save(){
        $id = Request::instance()->param('id', 0);
        $shiqi = Request::instance()->param('shiqi', '');
        $shuxing = Request::instance()->param('shuxing', '');
        $zhandouzhi = Request::instance()->param('zhandouzhi', '');
        $suoxunengliang = Request::instance()->param('suoxunengliang', '');
        $jinjie = Request::instance()->param('jinjie', '');
        $tedian = Request::instance()->param('tedian', '');
        if(!is_string(input('image'))){
            $image=uploads('image');
        }else{
            $image = '';
        }
        $res = db("monster")->where("id", $id)->update(['shiqi'=>$shiqi, 'shuxing'=>$shuxing, 'zhandouzhi'=>$zhandouzhi, 'suoxunengliang'=>$suoxunengliang, 'jinjie'=>$jinjie, 'tedian'=>$tedian, 'image'=>$image]);
        if($res){
            $this->success("修改成功！",url('monster'));
        }else{
            $this->error("修改失败！");
        }
    }
}