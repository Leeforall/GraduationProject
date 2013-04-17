<?php
/**
 * 系统注册
 * 
 */
class RegisterAction extends AdminAction {
	public function index(){
		//检测后台登录入口是否正确
        if(!session('?right_enter')) {
        	redirect(C('web_url'));exit;
        }
		if (session(C('USER_AUTH_KEY'))){
			redirect(C('cms_admin').'?s=Admin/Index');exit;
		}
		$memberRole=D('Role')->getRole(array('status'=>1,'name'=>'会员'));
		if(!$memberRole){
			$this->assign("jumpUrl",U('/Admin/Login'));
            $this->success('会员注册功能尚未开通！');
		}
		else{
			$role=D('Role')->getRole(array('status'=>1,'name'=>'会员'));
			$this->assign('role_id',$role['id']);
			$this->display();
		}
	}
	
	// 添加用户
    public function add(){
        $UserDB = D("User");
        if(isset($_POST['dosubmit'])) {
			$verify   = $this->_post('verify');
			if(session('verify') != md5($verify)) {
				$this->error('验证码错误！');
			}
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if(empty($password) || empty($repassword)){
                $this->error('密码必须！');
            }
            if($password != $repassword){
                $this->error('两次输入密码不一致！');
            }

            //根据表单提交的POST数据创建数据对象
            if($UserDB->create()){
                $user_id = $UserDB->add();
                if($user_id){
                    $data['user_id'] = $user_id;
					$data['role_id'] = $_POST['role'];
                    if (M("RoleUser")->data($data)->add()){
                        $this->assign("jumpUrl",U('/Admin/Login'));
                        $this->success('注册成功,您可以登录展上宝后台管理系统！');
                    }else{
						$where['id']=$user_id;
						$UserDB ->where($where)->delete();
                        $this->error('用户注册失败,请重试!');
                    }
                }else{
                     $this->error('用户注册失败!');
                }
            }else{
                $this->error($UserDB->getError());
            }
        }else{
			$role=D('Role')->getRole(array('status'=>1,'name'=>'会员'));
			$this->assign('role_id',$role['id']);
            $this->display('index');
        }
    }
}

?>