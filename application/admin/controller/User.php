<?php
namespace app\admin\controller;

class User extends BaseAdmin
{
   public function lister()
   {
        $list=db("admin")->select();
        $this->assign("list",$list);    

        return  $this->fetch();
   }
   public function add()
   {
       return  $this->fetch();
   }
   public function save()
   {
       $username=input('username');
       $re=db("admin")->where("username='$username'")->find();
       if($re){
           $this->error("此账号已存在",url('lister'));
       }else{
           $data=input('post.');
           $data['pwd']=md5($data['pwd']);
           $rea=db("admin")->insert($data);
           if($rea){
               $this->success("添加成功",url('lister'));
           }else{
            $this->error("添加失败",url('lister'));
           }
       }
   } 
   public function change()
   {
       $id=input('id');
       $re=db("admin")->where("id=$id")->find();
       if($re){
           if($re['level'] == 0){
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
        $id=input('id');
        $re=db("admin")->where("id=$id")->find();
        $this->assign("re",$re);
        return $this->fetch();
    }
    public function usave()
    {
        $id=input('id');
        $re=\db("admin")->where("id=$id")->find();
        if($re){
             $pwd=input('pwd');
             if(!empty($pwd)){
                 $data['pwd']=md5($pwd);
             }
             $data['username']=input('username');
             $data['level']=input('level');
             $res=\db("admin")->where("id=$id")->update($data);
             if($res){
                $this->success("修改成功",url('lister'));
             }else{
                $this->error("修改失败",url('lister'));
             }
        }else{
            $this->error("参数错误",url("lister"));
        }
    }
    public function delete()
    {
        $id=input('id');
        $re=\db("admin")->where("id=$id")->find();
        if($re){
          if($id == 1){
              echo '1';
          }else{
              $del=\db("admin")->where("id=$id")->delete();
              echo '0';
          }
        }else{
            echo '2';
        }
    }
    public function power()
    {
        $list=db("carte")->where("pid=0")->select();
        foreach($list as $k => $v){
            $list[$k]['lists']=db("carte")->where("pid={$v['cid']}")->select();
        }
        $this->assign("list",$list);

        $id=input('id');
        $admin=db("admin")->where("id=$id")->find();
        $this->assign("admin",$admin);

        $control=$admin['control'];
        $control_arr=explode(",",$control);
        $this->assign("control_arr",$control_arr);

        $way=$admin['way'];
        $way_arr=explode(",",$way);
        $this->assign("way_arr",$way_arr);
        
        return $this->fetch();
    }
    public function power_save()
    {
        $id=\input('id');
        $re=\db("admin")->where("id=$id")->find();
        if($re){
          
            $control=\input('controller/a');
            $way=\input('function/a');

            $data['control']=implode(",",array_keys($control));
            $data['way']=implode(",",array_keys($way));
            
            $res=\db("admin")->where("id=$id")->update($data);
            if($res){
               $this->success("权限配置成功",url('lister'));
            }else{
                $this->error("权限配置失败",url('lister'));
            }
            
        }else{
            $this->error("非法操作",url('lister'));
        }

        
    }







    
    
    
    
    
}