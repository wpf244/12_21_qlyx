<?php
namespace app\admin\controller;

class Log extends BaseAdmin
{
    public function lister()
    {
        $list=db("user_log")->order("id desc")->paginate(20);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function index()
    {
        $list=db("sys_log")->order("id desc")->paginate(20);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }

}