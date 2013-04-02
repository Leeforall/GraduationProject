<?php

class ExhibitorAction extends AdminAction{

	public function _initialize() {
        parent::_initialize();  //RBAC 验证接口初始化
    }
	
	public function index(){
	
	}
	
	//参展商类型列表
	public function type(){
		import('ORG.Util.Page');//导入分页类
		$map=array();
		$ExhibitorTypeDB = D('Exhibitortype');
		$count=$ExhibitorTypeDB->where($map)->count();
		$Page=new Page($count); //实例化分页类，传入总数
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
		$list = $ExhibitorTypeDB->where($map)->order('id ASC')->page($nowPage.','.C('web_admin_pagenum'))->select();
		
		//dump($count); //for debug
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		//$this->assign('type',$show);
		$this->display();
	}
	
	//增加参展商类型
	public function type_add(){
		$ExhibitorTypeDB=D('Exhibitortype');
		if(isset($_POST['dosubmit'])){
			if($ExhibitorTypeDB->create()){
				if($ExhibitorTypeDB->add()){
					$this->assign("jumpUrl",U('/Admin/Exhibitor/type'));
					$this->success('添加成功！');
				}else{
					$this->error('添加失败!');
				}
			}else{
				$this->error();
			}
		}else{
            $this->display();
		}
	}
	
	//编辑参展商类型
	public function type_edit(){
		$ExhibitorTypeDB = D('Exhibitortype');   //调用制定一直的模型层
		if(isset($_POST['dosubmit'])) {
            //根据表单提交的POST数据创建数据对象
            if($ExhibitorTypeDB->create()){
                if($ExhibitorTypeDB->save()){
                    $this->assign("jumpUrl",U('/Admin/Exhibitor/type'));
                    $this->success('编辑成功！');
                }else{
                     $this->error('编辑失败!');
                }
            }else{
                $this->error($ExhibitorTypeDB->getError());
            }
        }else{
            $id = $this->_get('id','intval',0);
            if(!$id)$this->error('参数错误!');
            $info = $ExhibitorTypeDB->getType(array('id'=>$id));
            $this->assign('info',$info);
            $this->display();
        }
	}
	
	//删除参展商类型
	public function type_del(){
		$id = $this->_get('id','intval',0);
        if(!$id)$this->error('参数错误!');
        $ExhibitorTypeDB=D('Exhibitortype');
        if($ExhibitorTypeDB->delType('id='.$id)){
            $this->assign("jumpUrl",U('/Admin/Exhibitor/type'));
            $this->success('删除成功！');
        }else{
            $this->error('删除失败!');
        }
	}
	
}