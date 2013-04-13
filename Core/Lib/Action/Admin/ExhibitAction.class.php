<?php

class ExhibitAction extends AdminAction{


	public function _initialize() {
		parent::_initialize();	//RBAC 验证接口初始化
	}
	
	public function index(){
		
	}
	
	public function template(){
		$ExhibittypeDAO=D('Exhibittype');
		$level_1=$level_1=$ExhibittypeDAO->where(array('parent_id'=>1))->select();
		$this->assign('level',$level_1);
		$this->display();
	}
	
	public function template_test(){
		$this->display();
	}
	
	public function type_add(){
		$ExhibittypeDAO=D('Exhibittype');
		if(isset($_POST['dosubmit'])) {
			if($ExhibittypeDAO -> create()){
				$exhibit_id = $ExhibittypeDAO->add();
				if($exhibit_id){
					$this->assign("jumpUrl",U('/Admin/Exhibit/template'));
                    $this->success('添加成功！');
				}else{
					$this->error('添加失败!');
				}
			}else{
				$this->error($ExhibitionDAO->getError());
			}
		}
	}
	
	public function type_edit(){
		$ExhibittypeDAO=D('Exhibittype');
		if(isset($_POST['dosubmit'])) {
			if($ExhibittypeDAO -> create()){
				if($ExhibittypeDAO->save()){
					$this->assign("jumpUrl",U('/Admin/Exhibit/template'));
                    $this->success('编辑成功！');
				}else{
					$this->error('编辑失败!');
				}
			}else{
				$this->error($ExhibittypeDAO->getError());
			}
		}
	}
	
	//删除展览类型
	public function del(){
		$id = $this->_get('id','intval',0);
        if(!$id)$this->error('参数错误!');
        $ExhibittypeDAO = D('Exhibittype');
        if($ExhibittypeDAO->delType('id='.$id)){
            $this->assign("jumpUrl",U('/Admin/Exhibit/template'));
            $this->success('删除成功！');
        }else{
            $this->error('删除失败!');
        }
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