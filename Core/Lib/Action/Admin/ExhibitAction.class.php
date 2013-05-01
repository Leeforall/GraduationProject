<?php

class ExhibitAction extends AdminAction{

	public function _initialize() {
		parent::_initialize();	//RBAC 验证接口初始化
	}
	
	public function index(){
		import('ORG.Util.Page');//导入分页类
		$map=array();
		$ExhibitDAO = D('Exhibit');
		$map['user_id']=session('userid');
		$count=$ExhibitDAO->where($map)->count();
		$Page=new Page($count,C('web_admin_pagenum')); //实例化分页类，传入总数
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
		$list = $ExhibitDAO->where($map)->order('is_verified ASC')->page($nowPage.','.C('web_admin_pagenum'))->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->assign('count',$count);
		$this->display();
	}
	
	public function myexhibit(){
		$this->index();
	}
	
	public function join(){
		$ExhibitDAO = D('Exhibit');
		if(isset($_POST['dosubmit'])) {
			$chosens = $this->_POST('chosen');
			if(!is_array($chosens))$this->error('参数错误!');
			$exhibition_id=$_POST['id'];
			$ExhibitionExhibitDAO=D('ExhibitionExhibit');
			$where['exhibition_id']=$exhibition_id;
			$where['user_id']=session('userid');
			$ExhibitionExhibitDAO->where($where)->delete();
			foreach ($chosens as $id => $exhibit) {
				$data['user_id']=session('userid');
				$data['exhibit_id']=$id;
				$data['exhibition_id']=$exhibition_id;
				$result=$ExhibitionExhibitDAO->data($data)->add();
				if($result){
					continue;
				}else{
					$ExhibitionExhibitDAO->where($where)->delete();
					$this->error('参展品设置出错!');
				}
			}
			
			$ExhibitorExhibitionDAO=D('ExhibitorExhibition');
			$this->assign('jumpUrl',U('/Admin/Exhibition/view'));
			$this->success('参展品设置成功!');
		}else{
			import('ORG.Util.Page');//导入分页类
			$map=array();
			$map['user_id']=session('userid');
			$map['status']=1;
			$map['is_verified']=2;
			//$count=$ExhibitDAO->where($map)->count();
			//$Page=new Page($count,C('web_admin_pagenum')); //实例化分页类，传入总数
			// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
			//$nowPage = isset($_GET['p'])?$_GET['p']:1;
			//$show       = $Page->show();// 分页显示输出
			//$list = $ExhibitDAO->where($map)->order('is_verified ASC')->page($nowPage.','.C('web_admin_pagenum'))->select();
			
			//不分页才不会逻辑出错 。 先用不分页的做
			$list = $ExhibitDAO->where($map)->order('id ASC')->select();
			$exhibition_id=$_GET['id'];
			$ExhibitionExhibitDAO=D('ExhibitionExhibit');
			$where['exhibition_id']=$exhibition_id;
			$where['user_id']=session('userid');
			$exhibits=$ExhibitionExhibitDAO->field('exhibit_id')->where($where)->select();
			$this->assign('exhibits',json_encode($exhibits));
			$this->assign('list',$list);
			//$this->assign('page',$show);
			//$this->assign('type',$show);
			$this->display();		
		}
	}
	
	public function list_exhibits(){
		import('ORG.Util.Page');//导入分页类
		$map=array();
		$map['user_id']=$_GET['userid'];  // 获取展商id
		$ExhibitDAO = D('Exhibit');
		//$map['user_id']=session('userid');
		$count=$ExhibitDAO->where($map)->count();
		$Page=new Page($count,C('web_admin_pagenum')); //实例化分页类，传入总数
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
		$list = $ExhibitDAO->where($map)->order('is_verified ASC')->page($nowPage.','.C('web_admin_pagenum'))->select();
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		//$this->assign('type',$show);
		$this->display('index_admin');
	}
	
	public function exhibitofexhibition(){
		$map=array();
		$map['user_id']=session('userid');
		$map['exhibition_id']=$_GET['id'];
		import('ORG.Util.Page');//导入分页类		
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$view = D('MyexhibitView');
		$list = $view->where($map)->order('id ASC')->page($nowPage.','.C('web_admin_pagenum'))->select();
		$count=$view->where($map)->count();
		$Page=new Page($count,C('web_admin_pagenum')); //实例化分页类，传入总数
		$show       = $Page->show();// 分页显示输出
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->assign('count',$count);
		$this->display();
	}
	
	public function read(){
		$ExhibitDAO = D('Exhibit');
		if(isset($_GET['id'])){
			$id=intval($_GET['id']);
			$map['id']=$id;
			$exhibit=$ExhibitDAO->getExhibit($map);
		}
		$this->assign('info',$exhibit);
		$this->display();
	}
	
	public function index_admin(){
		import('ORG.Util.Page');//导入分页类
		$map=array();
		$ExhibitDAO = D('ExhibitExhibitorView'); //用视图可以查找exhibitor
		$count=$ExhibitDAO->where($map)->count();
		$Page=new Page($count,C('web_admin_pagenum')); //实例化分页类，传入总数
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
		$list = $ExhibitDAO->where($map)->order('is_verified ASC,modifytime DESC')->page($nowPage.','.C('web_admin_pagenum'))->select();
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		//$this->assign('type',$show);
		$this->display();
	}
	
	public function add(){
		$ExhibitDAO=D('Exhibit');
		if(isset($_POST['dosubmit'])) {
			$categoryid=$_POST['category_id'];
			$ExhibitTemplateDAO=D('ExhibitTemplate');
			$ids=$ExhibitTemplateDAO->where(array('exhibittype_id'=>$categoryid))->getField('id',true);
			$jsondata['category_id']=$categoryid;
			$maps=array();
			$index=0;
			foreach($ids as $id){
				$values=$_POST['value_'.$id];
				$name=$_POST['name_'.$id];
				//dump($id);
				//dump($values);
				$map['id']=$id;
				$map['name']=$name;
				$map['value']=$values;
				$maps[$index]=$map;
				$index++;
			}
			$jsondata['values']=$maps;
			if($ExhibitDAO -> create()){
				$ExhibitDAO->jsondata=json_encode($jsondata);
				$exhibit_id = $ExhibitDAO->add();
				if($exhibit_id){
					$this->assign("jumpUrl",U('/Admin/Exhibit/attachment?id='.$exhibit_id));
					$this->success('展品信息添加成功');
				}else{
					$this->error('展品信息添加失败！');
				}	
			}else{
				$this->error($ExhibitDAO->getError());
			}
			//dump($jsondata);
			//dump(json_encode($jsondata));
			//$this->display();
		}else{
			$ExhibittypeDAO=D('Exhibittype');
			$level_1=$level_1=$ExhibittypeDAO->where(array('parent_id'=>1))->select();
			$this->assign('level1',$level_1);
			$this->display();
		}
	}
	
	public function attachment(){
		$this->display();
	}
	
	public function edit(){
		$ExhibitDAO=D('Exhibit');
		if(isset($_POST['dosubmit'])) {
			if($ExhibitDAO -> create()){
				$ExhibitDAO ->modifytime=time();//增加一个更新时间，要不如果更新没改动就会失败
				$ExhibitDAO ->is_verified=0;  //修改后要重新审核
				$categoryid=$_POST['category_id'];
				$ExhibitTemplateDAO=D('ExhibitTemplate');
				$ids=$ExhibitTemplateDAO->where(array('exhibittype_id'=>$categoryid))->getField('id',true);
				$jsondata['category_id']=$categoryid;
				$maps=array();
				$index=0;
				foreach($ids as $id){
					$values=$_POST['value_'.$id];
					$name=$_POST['name_'.$id];
					//dump($id);
					//dump($values);
					$map['id']=$id;
					$map['name']=$name;
					$map['value']=$values;
					$maps[$index]=$map;
					$index++;
				}
				$jsondata['values']=$maps;
				$ExhibitDAO->jsondata=json_encode($jsondata);
				if($ExhibitDAO->save()){
					//$this->assign("jumpUrl",U('/Admin/Exhibit/attachment?id='.$exhibit_id));
					$this->assign("jumpUrl",U('/Admin/Exhibit/index'));
					$this->success('展品信息修改成功！');
				}else{
					$this->error('展品信息修改失败！');
				}	
			}else{
				$this->error($ExhibitDAO->getError());
			}
		}else{
			$id = $this->_get('id','intval',0);
			if(!$id)$this->error('参数错误!');
			$info = $ExhibitDAO->getExhibit(array('id'=>$id));
			$ExhibittypeDAO=D('Exhibittype');
			$level_1=$ExhibittypeDAO->where(array('parent_id'=>1))->select();
			$level_2=$ExhibittypeDAO->where(array('parent_id'=>$info['level_1']))->select();
			$level_3=$ExhibittypeDAO->where(array('parent_id'=>$info['level_2']))->select();
			$ExhibitTemplateDAO=D('ExhibitTemplate');
			$where['exhibittype_id']=$info['category_id'];
			$list=$ExhibitTemplateDAO->where($where)->select();
			$this->assign('list',$list);
			$this->assign('level1',$level_1);
			$this->assign('level2',$level_2);
			$this->assign('level3',$level_3);
			$this->assign('info',$info);
			$this->display('add');
		}
	}
	
	public function changestatus(){
		$id =$_REQUEST['id'];
		$ExhibitDAO=D('Exhibit');
		$map['id']=intval($id);
		$exhibit=$ExhibitDAO->getExhibit($map,'id,status');
		if($exhibit['status']=='1'){
			$map['status']=0;
		}else if($exhibit['status']=='0'){
			$map['status']=1;
		}else{
			$map['status']=1;
		}
		$result=$ExhibitDAO->save($map);
		if($result){
			$this->ajaxReturn($result,'状态修改成功',1);
		}else{
			$this->ajaxReturn($result,'状态修改失败',0);
		}
	}
	
	public function verify(){
		$id =$_REQUEST['id'];
		$ExhibitDAO=D('Exhibit');
		$map['id']=intval($id);
		$exhibit=$ExhibitDAO->getExhibit($map,'id,is_verified');
		if($_REQUEST['val']=='0'){
			$map['is_verified']=0;
		}else if($_REQUEST['val']=='1'){
			$map['is_verified']=1;
		}else if($_REQUEST['val']=='2'){
			$map['is_verified']=2;
		}
		$result=$ExhibitDAO->save($map);
		if($result){
			$this->ajaxReturn($result,'审核成功',1);
		}else{
			$this->ajaxReturn($result,'审核失败',0);
		}
	}
	
	//删除展览类型
	public function del(){
		$id = $this->_get('id','intval',0);
        if(!$id)$this->error('参数错误!');
		$ExhibitDAO=D('Exhibit');
        if($ExhibitDAO->delExhibit('id='.$id)){
            $this->assign("jumpUrl",U('/Admin/Exhibit/index'));
            $this->success('删除成功！');
        }else{
            $this->error('删除失败!');
        }
	}
	
	public function template(){
		$ExhibittypeDAO=D('Exhibittype');
		$level_1=$level_1=$ExhibittypeDAO->where(array('parent_id'=>1))->select();
		$this->assign('level1',$level_1);
		$this->display();
	}
	
	public function template_del(){
		$ExhibitTemplateDAO=D('ExhibitTemplate');
		if(isset($_GET['exhibittype_id'])){
			$where['exhibittype_id']=intval($_GET['exhibittype_id']);
			$result=$ExhibitTemplateDAO->where($where)->delete();
			if($result){
				//$this->success('模板删除成功！！！');
				$this->redirect('Admin/Exhibit/template_display',array('id'=>$_GET['exhibittype_id']));
			}else{
				//$this->error('模板删除失败！！');
				$this->redirect('Admin/Exhibit/template_display',array('id'=>$_GET['exhibittype_id']),3,'<h1>模板删除失败！！！，请重试…………</h1>');
			}
		}
	}
	
	public function template_delone()
	{
		$id = $this->_get('id','intval',0);
        if(!$id)$this->error('参数错误!');
		$ExhibitTemplateDAO=D('ExhibitTemplate');
		$where['id']=$id;
		$exhibittype_id=intval($_GET['exhibittype_id']);
		$result=$ExhibitTemplateDAO->where($where)->delete();
		if($result){
			//$this->success('属性"'.$name.'"删除成功！！！');
			//有延迟重定向；
			//$this->redirect('Admin/Exhibit/template_display',array('id'=>$id),1,'<h1>删除成功！！！</h1>');
			//无延迟重定向；
			$this->redirect('Admin/Exhibit/template_display',array('id'=>$exhibittype_id));
		}else{
			//$this->error('属性"'.$name.'"删除失败！！');
			$this->redirect('Admin/Exhibit/template_display',array('id'=>$exhibittype_id),3,'<h1>删除失败！！！，请重试…………</h1>');
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
					//dump($data['html_value']);
					$newid=D('ExhibitTemplate')->data($data)->add();
					if($newid){
						$obj['html_value']=$this->createHtmlElement($types[$i],$newid,$names[$i],$attr_values);
						$obj['id']=$newid;
						if(D('ExhibitTemplate')->save($obj))
							continue;
						else{
							break;
						}
					}else{
						break;
					}
				}
				if($i==$length){
					//$this->success('模板添加成功');
					//无延迟重定向；
					$this->redirect('Admin/Exhibit/template_display',array('id'=>$_POST['exhibittype_id']));
				}else{
					//$this->error('模板添加有错误fuck！');
					$this->redirect('Admin/Exhibit/template_display',array('id'=>$_POST['exhibittype_id']),3,'<h1>模板添加有错误fuck！，请重试…………</h1>');
				}
			}else{
				$this->redirect('Admin/Exhibit/template_display',array('id'=>$_POST['exhibittype_id']),3,'<h1>模板添加有错误！，请重试…………</h1>');
			}
		}else{
			$this->display('template');
		}
	}
	
	/**
	ThinkPHP的完全开发手册中有提到，可以使用表达式更新,例如：
$User = M("User"); 
$data['name'] = 'ThinkPHP';
$data['score'] = array('exp','score+1'); 
$data['num'] = array('exp','num+1'); 
$User->where('id=5')->save($data);
或者使用另外提供的setField方法进行多字段更新
$User->where('id=5')->setField(array('socre','num'),array(array('exp','score+1'),array('exp','num+1')));
	*/
	
	private function createHtmlElement($type,$id,$name,$values){
		$str="";
		switch($type){
			case "1":
				$att_text="<input type='text' name='value_".$id."[]' id='Attribute_".$id."'/>";
				$str.=$att_text;
			break;
			case "2":
				$i=1;
				foreach ($values as $value) {
					$value=$this->trimStr($value);//注意这里要对字符串进行处理，过滤掉换行符
					$att_checkbox = "<input type='checkbox' id='Attribute_".$id."_".$i."' name='value_".$id."[]' value='".$value."' >".$value."</input>";
					$str.=$att_checkbox;
					++$i;
				}
			break;
			case "3":
				$str.="<select id='Attribute_".$id."'  name='value_".$id."[]' style='width:150px'>";
				$str.="<option>请选择</option>";
				foreach ($values as $value) {
					$value=$this->trimStr($value);//注意这里要对字符串进行处理，过滤掉换行符
					$option="<option value='".$value."'>".$value."</option>";
					$str.=$option;
				}
				$str.="<option value='其他'>其他</option>";
				$str.="</select>";
			break;
			default:
		}
		$str.="<input type='hidden' name='name_".$id."' id='Attribute_Name_".$id."' value='".$name."'/>";
		return $str;
	}
	
	//字符串处理函数
	private function trimStr($str){
		$order   = array("\r\n", "\n", "\r");
		$replace = '';
		return str_replace($order, $replace, $str);
	}
	
	public function template_display(){
		$ExhibitTemplateDAO=D('ExhibitTemplate');
		if(isset($_GET['id'])){
			$where['exhibittype_id']=$_GET['id'];
			$list=$ExhibitTemplateDAO->where($where)->select();
			$this->assign('list',$list);
			$this->display();
		}
	}
	
	public function getTemplate(){
        $where['exhibittype_id']=$_REQUEST['Id'];
		$where['status']=1;
        $data=D('ExhibitTemplate')->where($where)->select();
        $this->ajaxReturn($data);
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