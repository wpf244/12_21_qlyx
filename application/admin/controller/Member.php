<?php

namespace app\admin\controller;



use think\Request;



class Member extends BaseAdmin

{

    public function lister()

    {

        $phone = Request::instance()->param('phone', '');

        $username = Request::instance()->param('username', '');

        $map=[];

        if($phone){

            $map['phone']=array("like",'%'.$phone.'%');

        }

        if($username){

            $map['username']=array("like",'%'.$username.'%');

        }

        $list=db("user")->where($map)->order("uid desc")->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

      

        $page=$list->render();

        $this->assign("page",$page);   

        $this->assign('phone', $phone);

        $this->assign('username', $username);



        return $this->fetch();

    }



    /**

     * 充值能量币

     *

     * @return void

     */

    public function recharge_money()

    {

        $money = Request::instance()->param('money', 0);

        $uid = Request::instance()->param('uid');

        $res = db('user')->where("uid=$uid")->setInc('money', $money);

        if($res){

            return array('error_code'=>1, 'data'=>$money, 'msg'=>'充值成功!');

        }else{

            return array('error_code'=>-1, 'data'=>$money, 'msg'=>'充值失败!');

        }

    }



    /**

     * 充值状态

     *

     * @return void

     */

    public function status(){

        $id = Request::instance()->param('id', 0);

        $user = db('user')->where("uid=$id")->find();

        if($user){

            if($user['status'] == 1){

                return array('error_code'=>1, 'data'=>'', 'msg'=>'修改成功!');

            }

            $res = db('user')->where("uid=$id")->setField('status', 1);

            if($res){

                return array('error_code'=>1, 'data'=>'', 'msg'=>'修改成功!');

            }else{

                return array('error_code'=>-1, 'data'=>'', 'msg'=>'修改失败!');

            }

        }

        

    }



    /**

     * 提现列表

     *

     * @return void

     */

    public function withdraw(){

        $status = Request::instance()->param('status', '-1');

        $start = Request::instance()->param('start', '');

        $end = Request::instance()->param('end', '');

        $map = [];

        if($status != -1){

            $map['w.status'] = $status;

        }

        if($start != '' && $end != ''){

            $map['create_time'] = array(array('egt',strtotime($start)),array('elt',strtotime($end.' 23:55:55')),'AND');

        }elseif($start == '' && $end != ''){

            $map['create_time'] = array('elt',strtotime($end.' 23:55:55'));

        }elseif($start != '' && $end == ''){

            $map['create_time'] = array('egt',strtotime($start));

        }

        $list = db("withdraw")->alias('w')->field('w.id, w.status, w.bank, w.card, w.number, w.service, w.end_number, w.create_time, w.audit_time, w.over_time, u.phone, u.username')->where($map)->join('ddsc_user u', 'u.uid=w.uid')->order("w.status asc")->order('create_time desc')->paginate(10,false,['query'=>request()->param()]);

        $this->assign('list', $list);

        $this->assign('status', $status);

        $this->assign('start', $start);

        $this->assign('end', $end);

        return $this->fetch('withdraw');

    }



    /**

     * 提现状态修改

     *

     * @return void

     */

    public function withdraw_pass(){

        $id = Request::instance()->param('id');

        $ftype = Request::instance()->param('ftype');

        db('withdraw')->where('id', $id)->setField('status', $ftype);

        $withdraw = db('withdraw')->where('id', $id)->find();

        if($ftype == 1){

            db('withdraw')->where('id', $id)->setField('audit_time',time());

        }elseif($ftype == 2){

            db('withdraw')->where('id', $id)->setField('over_time',time());

        }elseif($ftype == 3){

            //驳回，返回余额

            db('withdraw')->where('id', $id)->setField('over_time',time());

            if(!$withdraw['audit_time']){

                db('withdraw')->where('id', $id)->setField('audit_time',time());

            }

            db('user')->where('uid', $withdraw['uid'])->setInc('money', $withdraw['number']);

        }

    }



    /**

     * 充值记录

     *

     * @return void

     */

    public function recharge(){

        $status = Request::instance()->param('status', '-1');

        $start = Request::instance()->param('start', '');

        $end = Request::instance()->param('end', '');

        $map = [];

        if($status != -1){

            $map['status'] = $status;

        }

        if($start != '' && $end != ''){

            $map['create_time'] = array(array('egt',strtotime($start)),array('elt',strtotime($end.' 23:55:55')),'AND');

        }elseif($start == '' && $end != ''){

            $map['create_time'] = array('elt',strtotime($end.' 23:55:55'));

        }elseif($start != '' && $end == ''){

            $map['create_time'] = array('egt',strtotime($start));

        }

        $list = db("recharge")->alias('r')->where($map)->where('r.status=1')->join('ddsc_user u', 'u.uid=r.uid')->order("u.status asc")->order('create_time desc')->paginate(10,false,['query'=>request()->param()]);

        $this->assign('list', $list);

        $this->assign('status', $status);

        $this->assign('start', $start);

        $this->assign('end', $end);

        return $this->fetch("recharge");

    }



























    public function change()

    {

        $id=input('id');

        $re=db("user")->where("uid=$id")->find();

        if($re){

           if($re['u_status'] == 0){

            $data['u_status']=1;

            $data['u_jtime']=\time();



            $res=\db("user")->where("uid=$id")->update($data);



            $datas['u_id']=0;

            $datas['p_id']=$id;

            $datas['time']=time();

            db("user_log")->insert($datas);

            echo '1';

           }else{

            echo '2'; 

           } 

            

            

        }else{

            echo '0';

        }

    }

    public function changes()

    {

        $id=input('id');

        $re=db("user")->where("uid=$id")->find();

        if($re){

           if($re['u_status'] == 1){

            $data['u_status']=0;

            $data['u_jtime']="";



            $res=\db("user")->where("uid=$id")->update($data);



            echo '1';

           }else{

            echo '2'; 

           } 

            

            

        }else{

            echo '0';

        }

    }

    public function delete()

    {

        $id=input('id');

        $re=db("user")->where("uid=$id")->find();

        if($re){

            $data['pid']=$re['pid'];

            $del=db("user")->where("uid=$id")->delete();

            if($del){

                $res=db("user")->where("pid=$id")->select();

                if($res){

                    $resss=db("user")->where("pid=$id")->update($data);

                }

                echo '0';

            }else{

                echo '1';

            }

        }else{

            echo '2';

        }

    }

    public function modifys()

    {

        $data=db("user")->field("u_name")->select();

        $arr=array();

        foreach($data as $v){

            $arr[]=$v['u_name'];

        }

        $this->assign("data",json_encode($arr,JSON_UNESCAPED_UNICODE));

        

        $id=input('id');

        $re=db("user")->where("uid=$id")->find();

        if($re){

            $this->assign("re",$re);

            return $this->fetch();

        }else{

            $this->redirect('lister');

        }



    }

    public function add()

    {

        $data=db("user")->field("u_name")->select();

        $arr=array();

        foreach($data as $v){

            $arr[]=$v['u_name'];

        }

        $this->assign("data",json_encode($arr,JSON_UNESCAPED_UNICODE));

        return $this->fetch();

    }

    public function save()

    {

        $pid=input('pid');

        $data=input('post.');

        if(empty($pid)){

            $data['pid']=0;

        }else{

            $re=db("user")->where("u_name='$pid'")->find();

            if($re){

                

                $data['pid']=$re['uid'];

              

            }else{

                $this->error("推荐人不存在",url('lister'));exit;

            }

        }

        if(\input('u_status')){

            $data['u_status']=1;

            $data['u_jtime']=time();

        }

        $data['u_pwd']=md5(input('u_pwd'));

        $data['u_pwds']=md5(\input('u_pwds'));

        $data['u_ztime']=time();

        $code=\time();

        $data['u_code']=mb_substr($code,-6,6);

        

        $rea=db("user")->insert($data);

        if($rea){

            $this->success("添加成功",url('lister'));

        }else{

            $this->error("系统繁忙，请稍后再试",url('lister'));

        }

        

    }

    public function usave()

    {

        $uid=input('uid');

        $re=db("user")->where("uid=$uid")->find();

        if($re){

            $pid=input('pid');

            if(empty($pid)){

                $data['pid']=0;

            }else{

                $re=db("user")->where("u_name='$pid'")->find();

                if($re){

                    $data['pid']=$re['uid'];  

                }else{

                    $this->error("推荐人不存在",url('lister'));exit;

                }

            }

            if(!empty('u_pwd')){

                $data['u_pwd']=md5(input('u_pwd'));

            }

            if(!empty('u_pwds')){

                $data['u_pwds']=md5(input('u_pwds'));

            }

            $data['u_name']=input('u_name');

            $data['level']=input('level');

            $data['u_phone']=input('u_phone');

            $data['u_wx']=input('u_wx');

            $data['u_alipay']=input('u_alipay');

            if(\input('u_status')){

                $data['u_status']=1;

            }else{

                $data['u_status']=$re['u_status'];

            }

            $res=db("user")->where("uid=$uid")->update($data);

            if($res){

                $this->success("修改成功",url('lister'));

            }else{

                $this->error("修改失败",url('lister'));

            }



        }else{

            $this->error("系统繁忙，请稍后再试",url('lister'));

        }

    }



































 

}