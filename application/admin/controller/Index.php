<?php
namespace app\admin\controller;

class Index extends BaseAdmin
  {
   public  function index(){
        
        $this->getSystemConfig(); 
        
        //会员数量
        // $user_cou=db("user")->count();
        // $this->assign("user_cou",$user_cou);

        // $today=db("user")->whereTime("u_ztime","week")->count();
        // $this->assign("today",$today);

        // $month=db("user")->whereTime("u_ztime","month")->count();
        // $this->assign("month",$month);

        // //站内通信
        // $email_cou=db("email")->count();
        // $this->assign("email_cou",$email_cou);

        // $today_email=db("email")->whereTime("time","week")->count();
        // $this->assign("today_email",$today_email);

        // $month_email=db("email")->whereTime("time","month")->count();
        // $this->assign("month_email",$month_email);

        // //今日升级申请
        // $apply=db("apply")->whereTime("time","today")->paginate(10);
        // $this->assign("apply",$apply);

        return view('index');
    }
    private function _deleteDir($R){
        $handle = opendir($R);
        while(($item = readdir($handle)) !== false){
            if($item != '.' and $item != '..'){
                if(is_dir($R.'/'.$item)){
                    $this->_deleteDir($R.'/'.$item);
                }else{
                    if(!unlink($R.'/'.$item))
                        die('error!');
                }
            }
        }
        closedir( $handle );
        return rmdir($R);
    }
    public function clearruntime(){
        if(input('user')==1){
            if($this->_deleteDir("../runtime/")){
                echo '1';
            }
        }
    }

     /**
     * 获取系统信息
     */
    public function getSystemConfig()
    {
        $system_config['os'] = php_uname(); // 服务器操作系统
        $system_config['server_software'] = $_SERVER['SERVER_SOFTWARE']; // 服务器环境
        $system_config['upload_max_filesize'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow'; // 文件上传限制             
        $system_config['port'] = $_SERVER['SERVER_PORT']; // 端口
        $system_config['dns'] = $_SERVER['HTTP_HOST']; // 服务器域名
        $system_config['php_version'] = PHP_VERSION; // php版本
        $system_config['openssl'] = extension_loaded('openssl'); //是否支付openssl
        $system_config['curl'] = function_exists('curl_init'); // 是否支持curl功能
        $system_config['upload_dir_jurisdiction'] = check_dir_iswritable(ROOT_PATH."public/uploads/"); // upload目录读写权限
        $system_config['runtime_dir_jurisdiction'] = check_dir_iswritable(ROOT_PATH."runtime/"); // runtime目录读写权限

        $this->assign("system_config", $system_config);
    }
    function modify(){
        if (! defined('CONTROLLER_NAME')) {
            define('CONTROLLER_NAME', $this->request->controller());
        }
        if (! defined('ACTION_NAME')) {
            define('ACTION_NAME', $this->request->action());
        }
        $id=\session('uid');
        $re=db("Admin")->where("id=$id")->find();
        $this->assign("re",$re);
        return view('modify');
    }
    function save(){
        $ob=db("Admin");
        $old_pwd=md5(input('old_pwd'));
        $id=input('id');
        $re=$ob->where("id=$id and pwd='$old_pwd'")->find();
        if($re){
            $data['pwd']=md5(input('pwd'));
            $res=$ob->where("id=$id")->update($data);
            if($res){
                $this->success("修改成功,请重新登录！",url('Login/logout'));
            }else{
                $this->error("修改失败！");
            }
        }else{
            $this->error("原密码错误！");
        }

        
    }

}