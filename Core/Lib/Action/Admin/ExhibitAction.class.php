<?php

class ExhibitAction extends AdminAction{


	public function _initialize() {
		parent::_initialize();	//RBAC 验证接口初始化
	}
	
	public function index(){
		
	}
	
	
	public function template_add(){
		$level_1=$level_1=D('exhibittype')->where(array('parent_id'=>1))->select();
		$this->assign('level',$level_1);
		$this->display();
	}
	
	public function getTypes(){
        $where['parent_id']=$_REQUEST['Id'];
        $data=D('exhibittype')->where($where)->select();
        $this->ajaxReturn($data);
    }
	
	
	public function getTypeById(){
        $where['id']=$_REQUEST['Id'];
        $data=D('exhibittype')->where($where)->select();
        $this->ajaxReturn($data);
    }

}

?>