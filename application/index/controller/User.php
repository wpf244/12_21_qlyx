<?php
namespace app\index\controller;

use think\Request;
use think\Db;

class User extends BaseHome
{
    public function index()
    {
        $uid=session("userid");
        $re=db("user")->where("uid=$uid")->find();
        if($re){
            $this->assign("re",$re);
            if($re['status'] != 1){
                $this->redirect("User/recharge_change");
            }
            return $this->fetch();
        }else{
            $this->redirect("Login/out");
        }
    }
    public function usave()
    {
        $uid=session("userid");
        $re=db("user")->where("uid=$uid")->find();
        if($re){
            if($re['status'] != 1){
                $this->redirect("User/recharge_change");
            }
            $data['username']=input('username');
            $res=db("user")->where("uid=$uid")->update($data);
            echo '0';
        }else{
            echo '1';
        }
    }

    /**
     * 提现页面
     *
     * @return void
     */
    public function withdraw(){
        $re=db("user")->where("uid=$uid")->find();
        if($re){
            if($re['status'] != 1){
                $this->redirect("User/recharge_change");
            }
        }
        $reb=db("lb")->where("fid=2")->find();
        $this->assign("reb",$reb);
        $reb=db("lb")->where("fid=3")->find();
        $this->assign("reb2",$reb);
        return $this->fetch();
    }

    /**
     * 提现提交
     *
     * @return void
     */
    public function withdraw_save(){
        $uid=session("userid");
        $re=db("user")->where("uid=$uid")->find();
        if($re){
            if($re['status'] != 1){
                $this->redirect("User/recharge_change");
            }
            //获取参数
            $bank = Request::instance()->param('bank', '');
            $card = Request::instance()->param('card', '');
            $number = Request::instance()->param('number', 0);
            if($bank == '' || $card == '' || $number <= 0){
                echo '2';
                return;
            }
            //判断会员有没有足够的能量币
            if($number > $re['money']){
                echo '3';
                return;
            }
            //获取手续费
            $reb=db("lb")->where("fid=3")->find();
            $service = ((int)$reb['name']/100)*$number;
            $end_number = $number - $service;
            //增加提现数据，扣会员能量币
            Db::startTrans();
            try{
                $res_one = db('user')->where("uid=$uid")->setDec('money', $number);
                
                $res_two = db('withdraw')->insert(['uid'=>$uid, 'bank'=>$bank, 'card'=>$card, 'number'=>$number, 'service'=>$service, 'end_number'=>$end_number, 'create_time'=>time()]);
                if(!$res_one || !$res_two){
                    throw new \Exception("操作失败");
                }
                Db::commit();    
            } catch (\Exception $e) {
                Db::rollback();
                echo '2';
                return;
            }
            echo '0';
        }else{
            echo '1';
        }
    }

    public function recharge_change(){
        return $this->fetch();
    }

    public function bank_card(){
        $lib_banktype	= array(
        //     array('name' => '中信银行|银行卡支付', 'code' => '962'),
        //     array('name' => '中国银行|银行卡支付', 'code' => '963'),
        //     array('name' => '中国农业银行|网上银行签约客户', 'code' => '964'),
        //     array('name' => '中国建设银行|网上银行签约客户', 'code' => '965'),
        //     array('name' => '中国工商银行|工行手机支付（仅限工行手机签约客户）', 'code' => '966'),
        //     array('name' => '中国工商银行|网上签约注册用户', 'code' => '967'),
        //     array('name' => '浙商银行|银行卡支付', 'code' => '968'),
        //     array('name' => '浙江稠州商业银行|浙江稠州商业银行', 'code' => '969'),
        //     array('name' => '招商银行|银行卡支付', 'code' => '970'),
        //     array('name' => '邮政储蓄|银联网上支付签约客户', 'code' => '971'),
        //     array('name' => '兴业银行|在线兴业', 'code' => '972'),
        //     array('name' => '顺德农村信用合作社|顺德信用合作社借记卡（顺德地区）', 'code' => '973'),
        //     array('name' => '深圳发展银行|发展卡支付', 'code' => '974'),
        //     array('name' => '上海银行|银行卡支付', 'code' => '975'),
        //     array('name' => '上海农村商业银行|如意借记卡（上海地区）', 'code' => '976'),
        //     array('name' => '浦东发展银行|东方卡', 'code' => '977'),
        //     array('name' => '平安银行|平安借记卡', 'code' => '978'),
        //     array('name' => '南京银行|银行卡支付', 'code' => '979'),
        //     array('name' => '民生银行|民生卡', 'code' => '980'),
        //     array('name' => '交通银行|太平洋卡', 'code' => '981'),
        //     array('name' => '华夏银行|华夏借记卡', 'code' => '982'),
        //     array('name' => '杭州银行|银行卡支付', 'code' => '983'),
        //     array('name' => '广州市农村信用社|麒麟借记卡（广州地区）,广州市商业银行|羊城万事顺卡借记卡（广州地区）', 'code' => '984'),
        //     array('name' => '广东发展银行|银行卡支付', 'code' => '985'),
        //     array('name' => '光大银行|银行卡支付', 'code' => '986'),
        //     array('name' => '东亚银行|银行卡支付', 'code' => '987'),
        //     array('name' => '渤海银行|银行卡支付', 'code' => '988'),
        //     array('name' => '北京银行|北京银行', 'code' => '989'),
        //     array('name' => '北京农村商业银行|银行卡支付', 'code' => '990'),
        //     array('name' => '支付宝', 'code' => '992'),
        //     array('name' => '财付通', 'code' => '993'),
        //   array('name' => '微信支付', 'code' => '1004'),
        //    array('name' => '网银WAP', 'code' => '1005'),
            array('name' => '支付宝WAP', 'code' => '1006'),
         //    array('name' => '微信WAP', 'code' => '1007')
        );
        $this->assign('lib_banktype',$lib_banktype);
        return $this->fetch();
    }
}