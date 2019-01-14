<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Loader;

Loader::import('dypay.bankpay', EXTEND_PATH);
Loader::import('threepay.pay',EXTEND_PATH);

class Paythree extends Controller{
       public function recharge()
       {
         $order_id = uniqid();
         $amount = Request::instance()->param("amount");   //充值的金额
         $bank_type = Request::instance()->param("bank_type");   //充值类型 1微信 2支付宝

         $arr['ordernum']=$order_id;
         if($bank_type == 1){
             $arr['netway']="WX_QR"; //微信
         }else{
             $arr['netway']="ZFB_QR"; //支付宝
         }
         $arr['amount']=floatval($amount);
         $arr['callbackurl']="http://www.qvanlixiaoyouxi.com/Index/Pays/notifyurl";
         $pay = new \threepay\pay();
         $pays = $pay->pay($arr); 
        //var_dump($pays);
        if($pays['statecode'] == 00){
            $uid=session("userid");
            db('recharge')->insert(['uid'=>$uid, 'orderid'=>$order_id, 'number'=>$amount, 'create_time'=>time()]);
            $ewm=Code($pays['qrcodeurl']);
          //  $ewm="/qrcode/1547178821.jpg";
            $this->assign("ewm",$ewm);
            $this->assign("orderid",$order_id);
            return $this->fetch();
        }else{
             $this->redirect("User/index");
         }
      
         

       }
       public function getjie(){
           $orderid=\input("code");
           $re=db("recharge")->where("orderid = '$orderid'")->find();
            if ($re['status'] == '1') {
                echo '1';
            }else {
                echo '0';
            }
       }

    // public function recharge(){
    //     header('Content-Type:textml;charset=GB2312');
    //     //商户ID
    //     $merchant_id		= '10095';
    //     //通信密钥
    //     $merchant_key		= 'b5c6efa9854545b98fec30c9d315d3e5';
    //     //==========================卡类支付配置=============================
    //     //支付的区域 0代表全国通用	
    //     $restrict			= '0';
    //     //接收下行数据的地址, 该地址必须是可以再互联网上访问的网址
    //     $callback_url		= "http://www.tianjieyouxi.com/Index/Pays/notifyurl";   
    //     $callback_url_muti  = "http://www.tianjieyouxi.com/Index/Pays/notifyurl";

    //     //======================网银支付配置=================================
    //     //接收网银支付接口的地址
    //     $bank_callback_url	= "http://www.tianjieyouxi.com/Index/Pays/notifyurl";  
    //     //网银支付跳转回的页面地址
    //     $bank_hrefbackurl	= 'http://www.tianjieyouxi.com/Index/Pays/index';

    //     $order_id = uniqid(); //您的订单Id号，你必须自己保证订单号的唯一性，本平台不会限制该值的唯一性
    //     $pay_type = 'bank';  //充值方式：bank为网银，card为卡类支付
    //     $amount = Request::instance()->param("amount");   //充值的金额
    //     $bank_type = Request::instance()->param("bank_type");   //银行类型

    //     $uid=session("userid");
    //     db('recharge')->insert(['uid'=>$uid, 'orderid'=>$order_id, 'number'=>$amount, 'create_time'=>time()]);

    //     $bankpay = new \dypay\bankpay();
    //     $bankpay->parter = $merchant_id;  //商家Id
    //     $bankpay->key = $merchant_key; //商家密钥
    //     $bankpay->type = $bank_type;   //银行类型
    //     $bankpay->value = $amount;    //提交金额
    //     $bankpay->orderid = $order_id;   //订单Id号
    //     $bankpay->callbackurl = $bank_callback_url; //下行url地址
    //     $bankpay->hrefbackurl = $bank_hrefbackurl; //跳转回的页面地址
    //     //发送
    //     $url = $bankpay->send();
    // }

    // 服务器点对点返回
    public function notifyurl(){
   
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
              
               die("opstate=0");
            }
        }
        exit("opstate=0"); 
    }


    public function r_n(){
      
        header('Content-Type:text/html;charset=GB2312');

        //获取返回的下行数据
        $orderid        = trim(input('orderid'));
        $opstate        = trim(input('opstate'));
        $ovalue         = trim(input('ovalue'));
        $sysorderid		= trim(input('sysorderid'));
        $completiontime		= trim(input('systime'));

        //进行爱扬签名认证
        $bankpay		= new \dypay\bankpay();
      //  $bankpay->key	= 'b5c6efa9854545b98fec30c9d315d3e5';
        $bankpay->recive();
      //  $this->recharge_notify();

        die("opstate=0");
    }

    /**
     * 充值回调，给会员增加天界币
     *
     * @return void
     */
    public function recharge_notify(){
        $uid=session("userid");
        $re=db("user")->where("uid=$uid")->find();
        if($re){
            $money = 100; //假定充值金额
            $res = db("user")->where('uid=$uid')->setInc('money', $money); //充值
            if($res){
                echo '0';
            }else{
                echo '1';
            }
        }else{
            echo '1';
        }
    }   
}