<?php
namespace app\index\controller;

use think\Controller;


class Pays extends Controller
{
    public function notifyurl()
    {
        header('Content-Type:textml;charset=GB2312');
        $orderid        = trim(input('orderid'));
        $opstate        = trim(input('opstate'));
        $ovalue         = trim(input('ovalue'));
        $sign           = trim(input('sign'));
        

        if($opstate == 0){
          
            $signkey= 'b5c6efa9854545b98fec30c9d315d3e5';
            //$signkey = $channel_model->where($channel_where)->getField('signkey');

            $sign_text  = "orderid=$orderid&opstate=$opstate&ovalue=$ovalue".$signkey;
            $sign_md5 = md5($sign_text);
            if($sign_md5 == $sign){
                $re=db("recharge")->where("orderid= '$orderid' ")->find();
                if($re['status'] == 0){
                    $res=db("recharge")->where("orderid= '$orderid' ")->setField("status",1);
                }
                $uid=$re['uid'];
                db("user")->where("uid=$uid")->setInc("money",$ovalue);
              
               die("opstate=0");
            }
        }
        exit("opstate=0"); 
    }
    public function index()
    {
    //    $this->redirect("User/index");
        header('Content-Type:textml;charset=GB2312');
        $orderid        = trim(input('orderid'));
        $opstate        = trim(input('opstate'));
        $ovalue         = trim(input('ovalue'));
        $sign           = trim(input('sign'));
        

        if($opstate == 0){
          
            $signkey= 'b5c6efa9854545b98fec30c9d315d3e5';
            //$signkey = $channel_model->where($channel_where)->getField('signkey');

            $sign_text  = "orderid=$orderid&opstate=$opstate&ovalue=$ovalue".$signkey;
            $sign_md5 = md5($sign_text);
            if($sign_md5 == $sign){
                $re=db("recharge")->where("orderid= '$orderid' ")->find();
                if($re['status'] == 0){
                    $res=db("recharge")->where("orderid= '$orderid' ")->setField("status",1);
                }
                $uid=$re['uid'];
                db("user")->where("uid=$uid")->setInc("money",$ovalue);
                $this->redirect("User/index");
               
            }
        }
        $this->redirect("User/index");
    }
}