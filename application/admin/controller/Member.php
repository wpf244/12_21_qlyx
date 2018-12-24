<?php
namespace app\admin\controller;

class Member extends BaseAdmin
{
    public function lister()
    {
        $list=db("user")->order("uid desc")->paginate(10);
        $this->assign("list",$list);
      
        $page=$list->render();
        $this->assign("page",$page);   

      

        return $this->fetch();
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