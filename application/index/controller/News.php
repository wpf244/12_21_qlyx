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
     * 需要判断能量是否充足（100点）
     * 判断是否有需要击杀的怪物
     *
     * @return void
     */
    public function invade(){
        $uid=session("userid");

        //当前排队信息
        $queue = db("queue")->where("uid=$uid")->where("status=0")->find();
        $this->assign('queue', $queue);
        $last_queue = db("queue")->order("queue_id desc")->find();
        $this->assign('last_queue', $last_queue);
        
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
        if($user['money'] < 100){
            //能量币不足，去充值
            return $this->redirect("User/recharge_change");
        }

        Db::startTrans();
        try{
            $res_one = db("user")->where("uid=$uid")->setDec('money', 100); //扣钱
            $res_two = db("queue")->insertGetId(['uid'=>$uid, 'start_time'=>time()]); //排队
            if(!$res_one || !$res_two){
                throw new \Exception("操作失败");
            }
            $homologous = $res_two != 1 ? ($res_two-1)/12 : 1.11; //求对应的出队数(当为1时，(1-1)/12也是整数)
            if(intval($homologous) == $homologous){
                //如果是整数，说明需要出队
                $res_three = db("queue")->where("queue_id=$homologous")->update(['status'=>1, 'end_time'=>time()]); //修改状态
                $homologous_uid = db("queue")->where("queue_id=$homologous")->value('uid'); //获取出队行的uid
                if(!$res_three){
                    throw new \Exception("操作失败");
                }
            }
            //增加应当出现怪物数量
            $all_queue = db("queue")->where('status', 0)->select();
            foreach($all_queue as $v){
                if($v['queue_id'] == $res_two){
                    continue;
                }
                $dequeue_id = $v['queue_id']*11; //出队需要的排队人数（当前需要乘以12为出队时的排队序号，减去自己排队需要为出队需要的人数）
                $next_monster = ($v['need_kill_number']+1)*$v['queue_id']+$v['queue_id']; //下一个怪物出现的排队序号（当前应当击杀的怪物数量*自己的排队序号为当前出现的最后一只怪物的排队序号）
                if($next_monster == $res_two){ //下一个怪物出现的排队序号与新加入排队号相等，给当前排队号增加一个怪物
                    $add_monster = db("queue")->where("queue_id", $v['queue_id'])->setInc('need_kill_number',1);
                    if(!$add_monster){
                        throw new \Exception("操作失败");
                    }
                }
            }
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
     * 修改已查看状态，增加能量
     *
     * @return void
     */
    public function finish(){
        $uid=session("userid");
        $over_look_queue = db("queue")->where("uid=$uid")->where("status=1")->where("look=0")->find();
        if(!$over_look_queue){
            return $this->redirect("News/index");
        }
        Db::startTrans();
        try{
            $res_one = db("queue")->where("uid=$uid")->where("status=1")->where("look=0")->setField('look',1);
            $res_two = db("user")->where("uid=$uid")->setInc('money', 1000); //给出队会员1000点能量
            if(!$res_one || !$res_two){
                throw new \Exception("系统错误，请再次尝试！");
            }
            Db::commit();    
        } catch (\Exception $e) {
            Db::rollback();
            return $this->redirect("News/index");
        }
        
        $this->assign('detail', $over_look_queue);
        return $this->fetch('finish');
    }

    /**
     * 击杀怪物数+1
     *
     * @return void
     */
    public function kill_monster(){
        $uid=session("userid");
        $queue = db("queue")->where("uid=$uid")->where("status=0")->find();
        if($queue['need_kill_number'] > $queue['already_kill_number']){
            $res = db("queue")->where("queue_id", $queue['queue_id'])->setInc('already_kill_number', 1);
            if($res){
                if($queue['need_kill_number'] >= $queue['already_kill_number']+2){
                    echo 2; //有下一个怪物
                }else{
                    echo 1; //没有下一个怪物
                }
            }else{
                echo 0; //杀怪失败
            }
        }else{
            echo -1; //怪物丢失
        }
        
    }

    /**
     * 判断入侵状态
     * 增加判断击杀怪物数量
     *
     * @param [int] $uid 会员id
     * @return void 'true'则通过判断，其他内容则为重定向页面
     */
    protected function judge($uid){
        $over_look_queue = db("queue")->where("uid=$uid")->where("status=1")->where("look=0")->find();
        if($over_look_queue){
            //如果有刚刚完成入侵并且未查看结果，判断是否已经击杀玩所有怪物，如果击杀则跳转到结果，否则跳转到正在入侵；
            if($over_look_queue['sum_number'] == $over_look_queue['already_kill_number']){ //是否已击杀完怪物
                return "finish";
            }else{
                return 'invade';
            }
        }

        $queueing = db("queue")->where("uid=$uid")->where("status=0")->find();
        if($queueing){
            //如果是正在入侵，则直接显示
            return 'invade';
        }
        return 'true';
    }
}