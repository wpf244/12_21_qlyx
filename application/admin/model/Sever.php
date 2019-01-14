<?php
/**
 * Sever.php
 * 朵朵科技
 * 官方网址 www.dd371.com
 * ===============================
 * @uses ECHO
 * @date 2018.11.29
 * @version 1.0
 *
 * **/
namespace app\admin\model;

use think\Model;
use think\Request;

class Sever extends Model
{
    public function add_logs($datas)
    {
        $ip=Request::instance()->ip();
        $datas['ip']=$ip;
        $datas['time']=\date("Y-m-d H:i:s",\time());
        return db('sys_log')->insert($datas);
    }
}