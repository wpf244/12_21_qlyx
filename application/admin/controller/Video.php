<?php
namespace app\admin\controller;

class Video extends BaseAdmin
{
    public function lister()
    {
        $keywords=input('keywords');    
      
        $this->assign("keywords",$keywords);
        $map['title']=array('like',"%" . $keywords . "%");
        $list=db('video')->where($map)->order(['sort'=>'asc','id'=>'desc'])->paginate(10,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function add()
    {
        return $this->fetch();
    }
    public function save(){
        $data=\input("post.");
        if(!is_string(input('image'))){
            $data['image']=uploads('image');
            $data['thumb']='/public/uploads/thumb/'.uniqid('',true).'.jpg';
            $image = \think\Image::open(ROOT_PATH.$data['image']);
            $image->thumb(148,80,\think\Image::THUMB_CENTER)->save(ROOT_PATH.$data['thumb']);
        }      
        $data['time']=time();
        $re=db("video")->insert($data);
        if($re){
            $this->success("添加成功！",url('lister'));
        }else{
            $this->error("添加失败！");
        }
    }
    public function change(){
        $id=input('id');
        $re=db("video")->where("id=$id")->find();
        if($re){
           if($re['status'] == 0){
               $res=db("video")->where("id=$id")->setField("status",1);
           }
           if($re['status'] == 1){
            $res=db("video")->where("id=$id")->setField("status",0);
           }
           if($res){
            echo '1';
            }else{
                echo '0';
            }
        }else{
            echo '2';
        }
    }
    public function modifys(){
       
        $id=input('id');
        $re=db("video")->where("id=$id")->find();
        $this->assign("re",$re);
        return $this->fetch();
    }
    public function usave(){
        $id=input('id');
        $re=db("video")->where("id=$id")->find();
        if($re){
            $data=input('post.');
            if(!is_string(input('image'))){
                $data['image']=uploads('image');
                $data['thumb']='/public/uploads/thumb/'.uniqid('',true).'.jpg';
                $image = \think\Image::open(ROOT_PATH.$data['image']);
                $image->thumb(148,80,\think\Image::THUMB_CENTER)->save(ROOT_PATH.$data['thumb']);
                
            }else{
                $data['image']=$re['image'];
            }
           
            $res=db("video")->where("id=$id")->update($data);
            if($res){
                $this->success("修改成功！",url('lister'));
            }else{
                $this->error("修改失败！",url('lister'));
            }
        }else{
            $this->error("参数错误！",url('lister'));
        }
       
    }
    public function sort(){
        $data=input('post.');
      
        foreach ($data as $id => $sort){
            db("video")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function delete(){
        $id=input('id');
        $re=db("video")->where("id=$id")->find();
        if($re){
            $del=db("video")->where("id=$id")->delete();
            $this->redirect('lister');
        }else{
            $this->redirect('lister');
        }
        
    }
    public function delete_all(){
        $id=input('id');
        $arr=explode(",", $id);
        foreach($arr as $v){
            $re=db("video")->where("id={$v}")->find();
            if($re){
                db("video")->where("id={$v}")->delete();
            }
        }
        $this->redirect('lister');
    }
}