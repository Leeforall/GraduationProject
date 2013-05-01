<?php

class ExhibitorAction extends AdminAction{

	public function _initialize() {
        parent::_initialize();  //RBAC 验证接口初始化
    }
	
	public function index(){
		import('ORG.Util.Page');//导入分页类
		$map=array();
		$ExhibitorDAO = D('Exhibitor');
		$count=$ExhibitorDAO->where($map)->count();
		$Page=new Page($count,C('web_admin_pagenum')); //实例化分页类，传入总数
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
		$list = $ExhibitorDAO->where($map)->order('id ASC')->page($nowPage.','.C('web_admin_pagenum'))->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		//$this->assign('type',$show);
		$this->display();
	}
	
	public function index_admin(){
		import('ORG.Util.Page');//导入分页类
		$map=array();
		$ExhibitorDAO = D('Exhibitor');
		$count=$ExhibitorDAO->where($map)->count();
		$Page=new Page($count,C('web_admin_pagenum')); //实例化分页类，传入总数
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
		$list = $ExhibitorDAO->where($map)->order('is_verified ASC, modifytime DESC')->page($nowPage.','.C('web_admin_pagenum'))->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		//$this->assign('type',$show);
		$this->display();
	}
	
	public function home(){
		$ExhibitorDAO = D('Exhibitor');
		$where['user_id']=session('userid');
		$exhibitor=$ExhibitorDAO->getExhibitor($where);
		$this->assign('info',$exhibitor);
		$where['exhibitor_id']=$exhibitor['id'];
		$categories=D("ExhibitorExhibittype")->where($where)->select();
		$this->assign('categories',$categories);
		$this->display();
	}
	
	public function add(){
		$ExhibitorDAO = D('Exhibitor');
		if(isset($_POST['dosubmit'])) {
			if($ExhibitorDAO -> create()){
				$exhibitor_id = $ExhibitorDAO->add();
				if($exhibitor_id){
					$categoryids=$_POST['categoryid'];
					$categorynames=$_POST['categoryname'];
					if(count($categoryids)==count($categorynames)){
						$length=count($categoryids);
						$i=0;
						for(;$i<$length;$i++){
							$data['exhibitor_id'] = $exhibitor_id;
							$data['exhibittype_id'] = $categoryids[$i];
							$data['category_name'] = $categorynames[$i];
							if (D("ExhibitorExhibittype")->data($data)->add()){
								continue;
							}else{
								break;
							}
						}
						if($i==$length){
							$this->assign("jumpUrl",U('/Admin/Exhibitor/home'));
							$this->success('展商信息添加成功！');
						}else{
							$where['exhibitor_id']=$exhibitor_id;
							D("ExhibitorExhibittype")->where($where)->delete();
							$this->error('展商信息添加成功,但主要经营行业添加失败!');
						}
					}
				}else{
					$this->error('展商信息添加失败！');
				}
			}else{
				$this->error($ExhibitorDAO->getError());
			}
		}else{
			$ExhibittypeDAO=D('Exhibittype');
			$level_1=$level_1=$ExhibittypeDAO->where(array('parent_id'=>1))->select();
			$this->assign('level',$level_1);
			$privince=$province=D('areas')->where(array('parent_id'=>1))->select();
			$this->assign('province',$province);
			$ExhibitorType = D('Exhibitortype')->getAllType(array('status'=>1),'sort DESC');
			$this->assign('type',$ExhibitorType);
			$this->display();
		}
	}
	
	public function getAreas(){
        $where['parent_id']=$_REQUEST['areaId'];
        $area=D('areas')->where($where)->select();
        $this->ajaxReturn($area);
    }
	
	public function getTypes(){
        $where['parent_id']=$_REQUEST['Id'];
        $data=D('exhibittype')->where($where)->select();
        $this->ajaxReturn($data);
    }
	
	public function del(){
		
	}
	
	public function read(){
		$ExhibitorDAO = D('Exhibitor');
		if(isset($_GET['id'])){
			$id=intval($_GET['id']);
			$map['id']=$id;
			$exhibitor=$ExhibitorDAO->getExhibitor($map);
			$where['exhibitor_id']=$id;
			$categories=D("ExhibitorExhibittype")->where($where)->select();
			$this->assign('categories',$categories);
			$this->assign('info',$exhibitor);
		}
		$this->display();
	}
	
	public function verify(){
		$id =$_REQUEST['id'];
		$ExhibitorDAO=D('Exhibitor');
		$map['id']=intval($id);
		$exhibitor=$ExhibitorDAO->getExhibitor($map,'id,is_verified');
		if($_REQUEST['val']=='0'){
			$map['is_verified']=0;
		}else if($_REQUEST['val']=='1'){
			$map['is_verified']=1;
		}else if($_REQUEST['val']=='2'){
			$map['is_verified']=2;
		}
		$result=$ExhibitorDAO->save($map);
		if($result){
			$this->ajaxReturn($result,'审核成功',1);
		}else{
			$this->ajaxReturn($result,'审核失败',0);
		}
	}
	
	public function edit(){
		$ExhibitorDAO = D('Exhibitor');
		if(isset($_POST['dosubmit'])) {
			if($ExhibitorDAO -> create()){
				$ExhibitorDAO ->modifytime=time();//增加一个更新时间，要不如果更新没改动就会失败
				$ExhibitorDAO ->is_verified=0;  //修改后要重新审核
				if($ExhibitorDAO->save()){
					$categoryids=$_POST['categoryid'];
					$categorynames=$_POST['categoryname'];
					if(count($categoryids)==count($categorynames)){
						$where['exhibitor_id']=$_POST['id'];
						D("ExhibitorExhibittype")->where($where)->delete();
							$length=count($categoryids);
							$i=0;
							for(;$i<$length;$i++){
								$data['exhibitor_id'] = $_POST['id'];;
								$data['exhibittype_id'] = $categoryids[$i];
								$data['category_name'] = $categorynames[$i];
								if (D("ExhibitorExhibittype")->data($data)->add()){
									continue;
								}else{
									break;
								}
							}
							if($i==$length){
								$this->assign("jumpUrl",U('/Admin/Exhibitor/home'));
								$this->success('我的展商信息编辑成功！');
							}else{
								$this->error('我的展商信息编辑成功,但主要经营行业更新失败!');
							}
						
					}
				}else{
					$this->error('我的展商信息编辑失败!');
				}
			}else{
				$this->error($ExhibitorDAO->getError());
			}
		}else{
			$id = $this->_get('id','intval',0);
            if(!$id)$this->error('参数错误!');
            $info = $ExhibitorDAO->getExhibitor(array('id'=>$id));
			$CategoryDAO=D('ExhibitorExhibittype');
			$category=$CategoryDAO->where(array('exhibitor_id'=>$id))->select();
			$AreaDao = D('areas');
			$ExhibittypeDAO=D('Exhibittype');
			$level_1=$level_1=$ExhibittypeDAO->where(array('parent_id'=>1))->select();
			$this->assign('level',$level_1);
			$province=$AreaDao->where(array('parent_id'=>1))->select();
			$city=$AreaDao->where(array('parent_id'=>$info['province_id']))->select();
			$district=$AreaDao->where(array('parent_id'=>$info['city_id']))->select();
			$type = D('Exhibitortype')->getAllType(array('status'=>1),'sort DESC');
			$this->assign('province',$province);
			$this->assign('category',$category);
			$this->assign('city',$city);
			$this->assign('district',$district);
            $this->assign('info',$info);
			$this->assign('type',$type);
            $this->display('add');
		}
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
				$this->error($ExhibitorTypeDB->getError());
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
	
	//类型排序权重更新
	public function type_sort(){
		$sorts = $this->_POST('sort');
		if(!is_array($sorts))$this->error('参数错误!');
		foreach ($sorts as $id => $sort) {
			D('Exhibitortype')->upType( array('id' =>$id , 'sort' =>intval($sort) ) );
		}
		$this->assign("jumpUrl",U('/Admin/Exhibitor/type'));
		$this->success('更新完成！');
	}

	
}