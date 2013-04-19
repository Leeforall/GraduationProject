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
	
	public function template_del(){
		$ExhibitTemplateDAO=D('ExhibitTemplate');
		if(isset($_GET['id'])){
			$where['exhibittype_id']=intval($_GET['id']);
			$result=$ExhibitTemplateDAO->where($where)->delete();
			if($result){
				$this->success('模板删除成功！！！');
			}else{
				$this->error('模板删除失败！！');
			}
		}
	}
	
	public function template_delone()
	{
		$id = $this->_get('id','intval',0);
        if(!$id)$this->error('参数错误!');
		$name= $_GET['name'];
		$ExhibitTemplateDAO=D('ExhibitTemplate');
		$where['exhibittype_id']=$id;
		$where['att_name']=$name;
		$result=$ExhibitTemplateDAO->where($where)->delete();
		if($result){
			$this->success('属性"'.$name.'"删除成功！！！');
		}else{
			$this->error('属性"'.$name.'"删除失败！！');
		}
	}
	
	public function template_add(){
		$ExhibitTemplateDAO=D('ExhibitTemplate');
		if(isset($_POST['dosubmit'])) {
			/*dump($_POST['att_name']);
			dump($_POST['att_type']);
			dump($_POST['unit_select']);
			dump($_POST['att_unit']);
			dump($_POST['att_values']);*/
			$names=$_POST['att_name'];
			$types=$_POST['att_type'];
			$hasunits=$_POST['unit_select'];
			$units=$_POST['att_unit'];
			$values=$_POST['att_values'];
			if((count($names)==count($types))&&
				(count($names)==count($hasunits))&&
				(count($names)==count($units))&&
				(count($names)==count($values))){
				$length=count($names);
				//dump($length);
				$i=0;
				for(;$i<$length;$i++){
					$data['exhibittype_id'] = $_POST['exhibittype_id'];
					$data['att_name'] = $names[$i];
					$data['att_type'] = $types[$i];
					$data['has_unit'] = $hasunits[$i];
					$data['unit'] = $units[$i];
					$data['value'] = $values[$i];
					$attr_values=explode("\n",$data['value']);
					$data['html_value']=$this->createHtmlElement($types[$i],$names[$i],$attr_values);
					//dump($data['html_value']);
					$result=D('ExhibitTemplate')->data($data)->add();
					if($result){
						continue;
					}else{
						break;
					}
				}
				if($i==$length){
					$this->success('模板添加成功');
				}else{
					$this->error('模板添加有错误fuck！');
				}
			}else{
				$this->error('模板添加有错误！');
			}
		}else{
			$this->display('template');
		}
	}
	
	private function createHtmlElement($type,$name,$values){
		$str="";
		switch($type){
			case "1":
				$att_text="<input type='text' name='value[]' id='att_text'/>";
				$str.=$att_text;
			break;
			case "2":
				foreach ($values as $value) {
					$att_checkbox = "<input type='checkbox' id='att_checkbox' name='value[]' value='".$value."' >".$value."</input>";
					$str.=$att_checkbox;
				}
			break;
			case "3":
				$str.="<select id='att_select'  name='value[]' style='width:150px'>";
				foreach ($values as $value) {
					$option="<option value='".$value."'>".$value."</option>";
					$str.=$option;
				}
				$str.="</select>";
			break;
			default:
		}
		$str.="<input type='hidden' name='name[]' id='att_name' value='".$name."'/>";
		return $str;
	}
	
	public function template_test(){
		$ExhibitTemplateDAO=D('ExhibitTemplate');
		if(isset($_GET['id'])){
			$where['exhibittype_id']=$_GET['id'];
			$list=$ExhibitTemplateDAO->where($where)->select();
			$this->assign('list',$list);
			$this->display();
		}
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