<?php 
    header('Content-Type:textml;charset=GB2312');
    $orderid        = trim($_REQUEST['orderid']);
        $opstate        = trim($_REQUEST['opstate']);
        $ovalue         = trim($_REQUEST['ovalue']);
        $sign           = trim($_REQUEST['sign']);
    

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
