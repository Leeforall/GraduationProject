<?php
/**
 * 系统注册
 * 
 */
class RegisterAction extends AdminAction {
	public function index(){
		//检测后台登录入口是否正确
        /*if(!session('?right_enter')) {
        	redirect(C('web_url'));exit;
        }
		if (session(C('USER_AUTH_KEY'))){
			redirect(C('cms_admin').'?s=Admin/Index');exit;
		}*/
		$this->display();
	}
}

?>