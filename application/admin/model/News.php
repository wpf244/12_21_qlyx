<?php
namespace app\admin\model;

use think\Model;

class News extends Model{
    /**
     * 添加新闻分类
     * */
    public function addNewstype($data){
        $re=db('news_type')->insert($data);
        return $re;
    }
    /**
     * 修改新闻分类
     * **/
    public function updateNewstype($where,$data){
        $res=db('news_type')->where("$where")->update($data);
        return $res;
    }
    /**
     * 删除分类
     * **/
    public function deleteType($id){
        $re=db('news_type')->where("ty_id=$id")->find();
        if($re){
            $res=db('news')->where("fid=$id")->find();
            if($res){
                $dels=db('news')->where("fid=$id")->delete();
            }
        }
        $del=db('news_type')->where("ty_id=$id")->delete();
        return $del;
    }
    /**
     * 添加新闻
     * */
    public function addNews($data){
        $re=db('news')->insert($data);
        return $re;
    }
    /**
     * 修改状态
     * */
    public function updateStatus($id){
        $re=db("news")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db('news')->where("id=$id")->setField("status",0);
                return $res;
            }elseif($re['status'] == 0){
                $res=db('news')->where("id=$id")->setField("status",1);
                return $res;
            }
        }else{
            return false;
        }
    }
    public function updateGroom($id){
        $re=db("news")->where("id=$id")->find();
        if($re){
            if($re['groom'] == 1){
                $res=db('news')->where("id=$id")->setField("groom",0);
                return $res;
            }elseif($re['groom'] == 0){
                $res=db('news')->where("id=$id")->setField("groom",1);
                return $res;
            }
        }else{
            return false;
        }
    }
    /**
     * 修改新闻
     * **/
    public function updateNews($where,$data){
        $res=db('news')->where("$where")->update($data);
        return $res;
    }
    /**
     * 删除新闻
     * **/
    public function deleteNews($id){
        $re=db('news')->where("id=$id")->find();
        
        $del=db('news')->where("id=$id")->delete();
        return $del;
    }
    /**
     * 删除多条数据
     * */
    public function deleteAll($arr){
        foreach ($arr as $v){
            $re=db('news')->where("id=$v")->find();
            if($re){
               
                $del=db('news')->where("id=$v")->delete();
    
            }else{
                return false;
            }
        }
        return $del;
    }
    
    
    
}