<?php
/*
* Admin分组公共类
*/
class AdminAction extends CmsAction{
    public function _initialize(){
        parent::_initialize();
        // 后台用户权限检查
        if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
            import('ORG.Util.RBAC');
            if (!RBAC::AccessDecision()) {
                //检查认证识别号
                if (!$_SESSION [C('USER_AUTH_KEY')]) {
                    //跳转到认证网关
                    redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
                }
                // 没有权限 抛出错误
                if (C('RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    redirect(C('RBAC_ERROR_PAGE'));
                } else {
                    if (C('GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
                    }
                    // 提示错误信息
                    $this->error(L('_VALID_ACCESS_'));
                }
            }
        }

    }
	
	/*
	* 空操作
	* 前台模块操作指定错误时调用
	*/
    public function _empty(){
    	$this->display(C('ERROR_PAGE'));
    	exit;
    }
/*
	public function getProvince(){
		//获取省级地区
        $province=D('areas')->where(array('parent_id'=>1))->select();
		return $province;
	}
	
	public function getArea(){
        $where['parent_id']=$_REQUEST['areaId'];
        $area=D('areas')->where($where)->select();
        $this->ajaxReturn($area);
    }
	*/
}