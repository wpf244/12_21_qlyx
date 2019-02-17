<?php

namespace app\index\controller;



use think\Db;

use think\Request;

class News extends BaseHome

{

    public function index()

    {

        $uid=session("userid");

        $re=db("user")->where("uid=$uid")->find();

        if($re){

            //入侵判断

            $judge = $this->judge($uid);

            if($judge != 'true'){

                $action = Request::instance()->action();

                return $judge == $action ? $this->fetch($judge) : $this->redirect($judge);

            }

            //页面数据

            $this->assign("re",$re);

            //游戏规则

            $reb=db("lb")->where("fid=1")->find();

            $this->assign("reb",$reb);

            

            return $this->fetch();

        }else{

            $this->redirect("Login/out");

        } 

    }

    public function video()

    {

        $res=db("video")->where("status=1")->order("sort asc")->select();

        $this->assign("res",$res);

        return $this->fetch();

    }

    public function change()

    {

        $id=\input("id");

        $re=db("video")->where("id=$id")->find();

        if($re){

            $res=db("video")->where("id=$id")->setInc("play",1);

            if($res){

                echo '0';

            }else{

                echo '2';

            }

        }else{

            echo '1';

        }

    }



    /**

     * 正在入侵页面

     * 需要判断会员是否正在入侵；

     * 需要判断会员是否刚刚完成入侵（排队返现成功）

     * 需要判断天界币是否充足（100点）

     * 判断是否有需要击杀的怪物

     * 

     * 在排队成功后，当有队号可以出队时取消修改排队状态，只修改出怪等级，排队状态和结束排队时间在合成最高等级怪物后才会修改

     *

     * @return void

     */

    public function invade(){

        $uid=session("userid");

        //开始入侵时入侵等级(同金额)

        $post_money = Request::instance()->param('level', 0);

        //当前排队信息

        $i = 1;

        $queue = array();

        $monster = array();

        do{

            $table_name = $i == 1 ? 'queue' : 'queue_'.$i;

            $queue = db($table_name)->where("uid=$uid")->where("status=0")->find();

            if($queue){

                $monster = db("monster")->where("sort", $queue['already_kill_number'])->where('level', $i)->find();

                break;

            }

            $i++;

        }while($i < 6);

        $this->assign('queue', $queue);//排队

        $this->assign('queue_table', $table_name);//排队队号

        $this->assign('monster', $monster);//怪物信息

        $lb_place = db('lb')->where('fid', 4)->find();

        $this->assign('lb_place', $lb_place);



        $log = db('log')->order("time desc")->find(); //最新一条日志

        if(!$log){

            $log = array('id'=>0, 'content'=>'', 'time'=>time());

        }

        $this->assign('log', $log);

        

        //入侵判断

        $judge = $this->judge($uid);

        if($judge != 'true'){

            $action = Request::instance()->action();

            return $judge == $action ? $this->fetch($judge) : $this->redirect($judge);

        }



        //会员判断

        $user = db("user")->where("uid=$uid")->find();

        if(!$user){

            $this->redirect("Login/out");

        }

        if($post_money == 0){

            //没有传值

            return $this->redirect("User/recharge_change");

        }

        if($user['money'] < $post_money){

            //天界币不足，去充值

            return $this->redirect("User/recharge_change");

        }

        

        Db::startTrans();

        try{

            $res_one = db("user")->where("uid=$uid")->setDec('money', $post_money); //扣钱

            if(!$res_one){

                throw new \Exception("操作失败");

            }

            if($post_money == 100){//百

                $table_name = 'queue';

            }elseif($post_money == 1000){//千

                $table_name = 'queue_2';

            }elseif($post_money == 10000){//万

                $table_name = 'queue_3';

            }elseif($post_money == 100000){//十万

                $table_name = 'queue_4';

            }elseif($post_money == 1000000){//百万

                $table_name = 'queue_5';

            }

            $res_two = db($table_name)->insertGetId(['uid'=>$uid, 'start_time'=>time()]); 

            if(!$res_two){

                throw new \Exception("操作失败");

            }

            // $homologous = $res_two != 1 ? ($res_two-1)/12 : 1.11; //求对应的出队数(当为1时，(1-1)/12也是整数)

            // if(intval($homologous) == $homologous){

            //     //如果是整数，说明需要出队

            //     $res_three = db("queue")->where("queue_id=$homologous")->update(['status'=>1, 'end_time'=>time()]); //修改状态

            //     $homologous_uid = db("queue")->where("queue_id=$homologous")->value('uid'); //获取出队行的uid

            //     // if(!$res_three){

            //     //     throw new \Exception("操作失败");

            //     // }

            // }

            //增加应当出现怪物数量

            $all_queue = db($table_name)->where('status', 0)->select();

            foreach($all_queue as $v){

                if($v['queue_id'] == $res_two){

                    continue;

                }

                $dequeue_id = $v['queue_id']*11; //出队需要的排队人数（当前需要乘以12为出队时的排队序号，减去自己排队需要为出队需要的人数）

                $next_monster = ($v['need_kill_number']+1)*$v['queue_id']+$v['queue_id']; //下一个怪物出现的排队序号（当前应当击杀的怪物数量*自己的排队序号为当前出现的最后一只怪物的排队序号）

                if($next_monster == $res_two){ //下一个怪物出现的排队序号与新加入排队号相等，给当前排队号增加一个怪物

                    $reg=db($table_name)->where("queue_id", $v['queue_id'])->find();
                    if($reg['need_kill_number'] < 11){
                        $add_monster = db($table_name)->where("queue_id", $v['queue_id'])->setInc('need_kill_number',1);
                    }
                   

                    // if(!$add_monster){

                    //     throw new \Exception("操作失败");

                    // }

                }

            }

            // $this->ddsc_log('会员'.$user['username'].'进入天界');

            Db::commit();    

        } catch (\Exception $e) {

            Db::rollback();

            return $this->redirect("News/index");

        }

        //执行到这里说明刚刚执行了排队操作，那么需要更新显示的数据，重定向到自己

        return $this->redirect('invade');

    }



    /**

     * 完成入侵页面

     * 修改已查看状态，增加天界币

     *

     * @return void

     */

    public function finish(){

        $uid=session("userid");

        $i = 1;

        do{

            $table_name = $i == 1 ? 'queue' : 'queue_'.$i;

            $over_look_queue = db($table_name)->where("uid=$uid")->where("status=1")->where("look=0")->find();

            if($over_look_queue){

                break;

            }

            $i++;

        }while($i < 6);

        if(!$over_look_queue){

            return $this->redirect("News/index");

        }

        Db::startTrans();

        try{

            $res_one = db($table_name)->where("uid=$uid")->where("status=1")->where("look=0")->setField('look',1);

            if($i == 1){

                $money = 1000;

            }elseif($i == 2){

                $money = 10000;

            }elseif($i == 3){

                $money = 100000;

            }elseif($i == 4){

                $money = 1000000;

            }elseif($i == 5){

                $money = 10000000;

            }else{

                $money == 0;

            }

            $res_two = db("user")->where("uid=$uid")->setInc('money', $money); //给出队会员天界币

            if(!$res_one || !$res_two || $money == 0){

                throw new \Exception("系统错误，请再次尝试！");

            }

            $user = db("user")->where("uid=$uid")->find();

            $i++;

            if($user['level'] < $i){

                db("user")->where("uid", $uid)->setField('level', $i); //更改会员等级

            }

            // $this->ddsc_log('会员'.$user['username'].'已合成契约神兽');

            Db::commit();    

        } catch (\Exception $e) {

            Db::rollback();

            return $this->redirect("News/index");

        }

        $this->assign('money', $money);

        $this->assign('detail', $over_look_queue);

        $lb_place = db('lb')->where('fid', 5)->find();

        $this->assign('lb_place', $lb_place);

        return $this->fetch('finish');

    }



    /**

     * 击杀怪物数+1

     *

     * @return void

     */

    public function kill_monster(){

        $uid=session("userid");

        $i = 1;

        do{

            $table_name = $i == 1 ? 'queue' : 'queue_'.$i;

            $queue = db($table_name)->where("uid=$uid")->where("status=0")->find();

            if($queue){

                break;

            }

            $i++;

        }while($i < 6);

        $queue = db($table_name)->where("uid=$uid")->where("status=0")->find();

        if($queue['need_kill_number'] > $queue['already_kill_number']){

            $res = db($table_name)->where("queue_id", $queue['queue_id'])->setInc('already_kill_number', 1);

            if($res){ //合成成功

                if($queue['already_kill_number'] + 1 == 11){ //满级

                    db($table_name)->where("queue_id",$queue['queue_id'])->update(['status'=>1, 'end_time'=>time()]);

                }

                $id = Request::instance()->param('id', 0);

                $monster = db("monster")->where("id", $id)->find(); //当前怪物

                $next_monster = db("monster")->where('level', $monster['level'])->where('sort', $monster['sort']+1)->find();

                if($queue['need_kill_number'] >= $queue['already_kill_number']+2){

                    return array('status'=>2, 'monster'=>$next_monster);//有下一个怪物

                }else{

                    return array('status'=>1, 'monster'=>$next_monster); //没有下一个怪物

                }

            }else{

                return array('status'=>0); //杀怪失败

            }

        }else{

            return array('status'=>-1); //怪物丢失

        }

    }



    /**

     * 入侵记录

     *

     * @return void

     */

    public function r_log(){

        // $id = Request::instance()->param('id', 0);

        $queue_table = Request::instance()->param('queue_table', '');

        // $res = db("log")->where("id", ">", $id)->select();

        $queue = db($queue_table)->order("queue_id desc")->find();

        $uid=session("userid");

        $user = db($queue_table)->where("uid=$uid")->where("status=0")->find();

        return array('queue'=>$queue,'user'=>$user);

    }   



    /**

     * 判断入侵状态

     * 增加判断击杀怪物数量

     *

     * @param [int] $uid 会员id

     * @return void 'true'则通过判断，其他内容则为重定向页面

     */

    protected function judge($uid){

        $i = 1;

        $queueing = array();

        do{

            $table_name = $i == 1 ? 'queue' : 'queue_'.$i;

            $queueing = db($table_name)->where("uid=$uid")->where("status=0")->find();

            $over_look_queue = db($table_name)->where("uid=$uid")->where("status=1")->where("look=0")->find();

            if($over_look_queue){

                //如果有刚刚完成入侵并且未查看结果，判断是否已经击杀玩所有怪物，如果击杀则跳转到结果，否则跳转到正在入侵；

                return $over_look_queue['sum_number'] == $over_look_queue['already_kill_number'] ? "finish" : 'invade';//是否已击杀完怪物

            } 

            if($queueing){//如果是正在入侵，则直接显示

                return 'invade';

            }

            $i++;

        }while($i < 6);

        return 'true';

    }

}