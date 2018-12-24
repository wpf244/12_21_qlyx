<?php
namespace app\admin\controller;

use tp5er\Backup;


class Sql extends BaseAdmin
{
    public $save_path = 'Data/';
    public function lister()
    {
        $list=db("sql")->order("sql_id desc")->paginate(10);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);

        $this->assign('save_path', $this->save_path);
        return $this->fetch();
    }
    public function backups_exe()
    {
        $config=array(
            'path'     => './Data/',//数据库备份路径
            'part'     => 20971520,//数据库备份卷大小
            'compress' => 0,//数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level'    => 9 //数据库备份文件压缩级别 1普通 4 一般  9最高
        );       
        $db= new Backup();
        $data1 = $db->getFile();
        $data = $db->dataList();
        foreach($data as $k => $v){
            $name=$v['name'];
            $start= $db->setFile()->backup("$name", 0);
        }

        $arr['sql_name']=$data1['filename'];
        $arr['create_time']=time();
        $re=db("sql")->insert($arr);
        if($re){
            $this->success("备份成功");
        }else{
            $this->error("备份失败");
        }

    }
    public function sql_delete()
    {
        $id=input('id');
        $re=db("sql")->where("sql_id=$id")->find();
        if($re){
            $del=db("sql")->where("sql_id=$id")->delete();
            if($del){
                $this->success("删除成功");
            }else{
                $this->error("删除失败");
            }
        }else{
            $this->error("参数错误");
        }
    }
}