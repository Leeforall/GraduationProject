<?php
/**
 * 
 * Attachment(附件管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
//if(!defined("Yourphp")) exit("Access Denied");
class AttachmentAction extends  AdminAction {

	protected $lang,$dao;
    function _initialize()
    {	
		/*$this->isadmin= $_REQUEST['isadmin'] ? $_REQUEST['isadmin'] : 0;
		$this->sysConfig = F('sys.config');
		if(APP_LANG){
			$this->Lang = F('Lang');
			$this->assign('Lang',$this->Lang);
			if($_GET['l']){
				if(!$this->Lang[$_GET['l']]['status'])$this->error ( L ( 'NO_LANG' ) );
				$lang=$_GET['l'];
			}else{
				$lang=$this->sysConfig['DEFAULT_LANG'];
			}
			define('LANG_NAME', $lang);
			define('LANG_ID', $this->Lang[$lang]['id']);

			$this->Config = F('Config_'.LANG_NAME);			
		}else{
			$this->Config = F('Config');		
		} 
		
		if($_POST['PHPSESSID'] && $_POST['swf_auth_key'] && $_POST['userid']){
			if($_POST['swf_auth_key']==sysmd5($_POST['PHPSESSID'].$_POST['userid'],$this->sysConfig['ADMIN_ACCESS'])){
				$this->userid = $_POST['userid'];
				if(APP_LANG) $this->Config = F('Config_'.$_POST['lang']);
			}
		}		
		if(!$this->userid){
			if($this->isadmin){
				import('@.Action.Adminbase');
				$Adminbase=new AdminbaseAction();
				$Adminbase->_initialize();
				$this->userid=  $_SESSION[C('USER_AUTH_KEY')];
				$this->groupid=  $_SESSION['groupid'];
			}else{
				C('ADMIN_ACCESS',$this->sysConfig['ADMIN_ACCESS']);
				if(cookie('auth')){
					if(!strstr($_SERVER['HTTP_USER_AGENT'],'Flash'))cookie('YP_cookie',$_SERVER['HTTP_USER_AGENT']);
					$HTTP_USER_AGENT = strstr($_SERVER['HTTP_USER_AGENT'],'Flash') ? cookie('cookie') : $_SERVER['HTTP_USER_AGENT'];
					$yourphp_auth_key = sysmd5($this->sysConfig['ADMIN_ACCESS'].$HTTP_USER_AGENT);
					list($userid, $groupid ,$password) = explode("-", authcode(cookie('auth'), 'DECODE', $yourphp_auth_key));
					$this->userid = $userid;
					$this->groupid = $groupid; 
				}
				if(!$this->userid){
					$this->assign('jumpUrl',U('User/Login/index'));
					$this->error(L('no_login'));
				}
			}
		}
		$this->assign($this->Config);*/
		
		
		$this->dao=M('Attachment');
    }
	public function index(){

		$postd=array('typeid','filetype','foreignid','file_limit','file_types','file_size','moduleid');
		foreach((array)$_REQUEST as $key=>$res){
			if(in_array($key,$postd))$postdata[$key]=$res;
		}
		$upsetup = implode('-',$postdata);
		//$yourphp_auth_key = sysmd5(C('ADMIN_ACCESS').$_SERVER['HTTP_USER_AGENT']);
		//$enupsetup = authcode($auth, 'DECODE', $yourphp_auth_key);
		//if(!$enupsetup || $upsetup!=$enupsetup)  $this->error (L('do_empty'));

		$sessid = time();

		$userid=session('userid');
		$count = $this->dao->where('status=0 and user_id ='.$this->userid)->count();
		$this->assign('no_use_files',$count);
		$this->assign('small_upfile_limit',$_REQUEST['file_limit'] - $count);


		$types = '*.'.str_replace(",",";*.",$_REQUEST['file_types']); ;
		$this->assign('module_id',$_REQUEST['moduleid']);
		$this->assign('file_type',$_REQUEST['filetype']);
		$this->assign('foreign_id',$_REQUEST['foreignid']);
		$this->assign('type_id',$_REQUEST['typeid']);
		$this->assign('file_types',$types);
		$this->assign('file_size',$_REQUEST['file_size']);
		$this->assign('file_limit',$_REQUEST['file_limit']);
		$this->assign('sessid',$sessid);
 
		$this->display();
	}
	
	//Logo 图标上传
	public function uploadLogo(){
	
		// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
		if (isset($_POST["PHPSESSID"])) {
			session_id($_POST["PHPSESSID"]);
		}
		import("ORG.Net.UploadFile"); 
        $upload = new UploadFile(); 
		 //设置上传文件大小 
        $upload->maxSize = 3292200;
		//设置需要生成缩略图，仅对图像文件有效 
        $upload->thumb = true; 
		//缩略图文件名前缀
		$upload->thumbPrefix ="s_";
        //设置上传文件类型 
        $upload->allowExts = explode(',','jpg,gif,png,jpeg'); 
        //设置附件上传目录 
        $upload->savePath = UPLOAD_PATH; 
		 //设置上传文件规则 
        $upload->saveRule = uniqid;
		//设置缩略图最大宽度 
        $upload->thumbMaxWidth = '100'; 
        //设置缩略图最大高度 
        $upload->thumbMaxHeight = '100'; 
		 //删除原图 
        $upload->thumbRemoveOrigin = true; 
        if (!$upload->upload()) { 
			$this->ajaxReturn(0,$upload->getErrorMsg(),0);
        } else { 
            //取得成功上传的文件信息 
            $uploadList = $upload->getUploadFileInfo(); 
			$imagearr = explode(',', 'jpg,gif,png,jpeg,bmp,ttf,tif'); 
			$returndata['filepath'] = __ROOT__.substr($uploadList[0]['savepath'].'s_'.strtolower($uploadList[0]['savename']),1);
			$returndata['fileext']  = strtolower($uploadList[0]['extension']); 
			$returndata['filename'] =  $uploadList[0]['name'];
			$returndata['filesize'] = $uploadList[0]['size']; 
			
			$this->ajaxReturn($returndata,'上传成功', '1');
		}
	}

	public function upload(){
		//if($_POST['swf_auth_key']!= sysmd5($_POST['PHPSESSID'].$this->userid)) $this->ajaxReturn(0,'1-'.$_POST['PHPSESSID'],0);
 
		import("ORG.Net.UploadFile"); 
        $upload = new UploadFile(); 
		//$upload->supportMulti = false;
        //设置上传文件大小 
        $upload->maxSize = 3292200;
		$upload->autoSub = true; 
		$upload->subType = 'date';
		$upload->dateFormat = 'Ym';
        //设置上传文件类型 
		$filetype=intval($_REQUEST['file_type']);
		switch($filetype){
			case 1:
				$upload->allowExts = explode(',','docx,doc,pdf,xls,xlsx'); 
			break;
			case 2:
				$upload->allowExts = explode(',','jpg,gif,png,jpeg'); 
			break;
			case 3:
				$upload->allowExts = explode(',','flv,avi,rmvb'); 
			break;
			default:
	
		}
        //设置附件上传目录 
        $upload->savePath = UPLOAD_PATH; 
		 //设置上传文件规则 
        $upload->saveRule = uniqid; 


        //删除原图 
        $upload->thumbRemoveOrigin = true; 
        if (!$upload->upload()) { 
			$this->ajaxReturn(0,$upload->getErrorMsg(),0);
        } else { 
            //取得成功上传的文件信息 
            $uploadList = $upload->getUploadFileInfo(); 
			
			
			if($_REQUEST['addwater']){ //$this->Config['watermark_enable']  $_REQUEST['addwater']
				import("ORG.Util.Image");  
				Image::water($uploadList[0]['savepath'].$uploadList[0]['savename'],'',$this->Config);
			}
			
			$data=array();
			$model = M('Attachment');
			//保存当前数据对象
			$data['module_id'] = intval($_REQUEST['module_id']);
			$userid=intval($_REQUEST['user_id']);
			$data['user_id'] = intval($userid);
			$data['foreign_id']=intval($_REQUEST['foreign_id']);  //方便按模块管理附件
			$data['type_id']=intval($_REQUEST['type_id']);  //所属模块类型，1为展会模块，2为展品模块，以后可以扩展其他
			$data['file_type']=intval($_REQUEST['file_type']);   //目前有4种类型，0未知，1文档，2图片，3视频flv，4其他
			$data['file_name'] = $uploadList[0]['name'];
			$data['file_path'] = __ROOT__.substr($uploadList[0]['savepath'].strtolower($uploadList[0]['savename']),1);
			$data['file_size'] = $uploadList[0]['size']; 
			$data['file_ext'] = strtolower($uploadList[0]['extension']); 
			$data['createtime'] = time();
			$data['upload_ip'] = get_client_ip();
			$aid = $model->add($data); 
			$returndata['aid']		= $aid;
			$returndata['filepath'] = $data['file_path'];
			$returndata['fileext']  = $data['file_ext'];
			$returndata['filesize'] = $data['file_size'];
			$returndata['filetype'] = $data['file_type'];
			$returndata['filename'] = $data['file_name'];		

			$this->ajaxReturn($returndata,'上传成功', '1');
        }	
	}

	public function changestatus(){
		$id =$_REQUEST['id'];
		$AttachmentDAO = D('Attachment');
		$map['id']=intval($id);
		$attachment=$AttachmentDAO->field('id,status')->where($map)->find();
		if($attachment['status']=='1'){
			$map['status']=0;
		}else if($attachment['status']=='0'){
			$map['status']=1;
		}else{
			$map['status']=1;
		}
		$result=$AttachmentDAO->save($map);
		if($result){
			$this->ajaxReturn($result,'状态修改成功',1);
		}else{
			$this->ajaxReturn($result,'状态修改失败',0);
		}
	}
	
	public function attachmentlist(){
		$id = $this->_get('id','intval',0);
		$type_id = $this->_get('type','intval',0);
        if(!$id||!$type_id)$this->error('参数错误!');
		$AttachmentDAO = D('Attachment');
		$where['user_id']=session('userid');
		$where['foreign_id']=$id ;
		$where['type_id']=$type_id;
		$where['file_type']=1;   //图片类型
		$docs=$AttachmentDAO->where($where)->order('id ASC')->select();
		$where['file_type']=2;   //视频类型
		$pics=$AttachmentDAO->where($where)->order('id ASC')->select();
		$where['file_type']=3;   //资料类型
		$vedios=$AttachmentDAO->where($where)->order('id ASC')->select();
		$where['file_type']=4;   //other类型
		$ohters=$AttachmentDAO->where($where)->order('id ASC')->select();
		$count=count($docs)+count($pics)+count($ohters)+count($vedios);
		$this->assign('count',$count);
		$this->assign('docs',$docs);
		$this->assign('pics',$pics);
		$this->assign('vedios',$vedios);
		$this->assign('typeid',$type_id);
		$this->assign('foreignid',$id);
		//$this->assign('ohters',$ohters);
		$this->display();
		
	}
	
	public function edit(){
		if(isset($_POST['file_name'])){
			$id =$_REQUEST['id'];
			$AttachmentDAO = D('Attachment');
			$data['user_id']=session('userid');
			$data['file_name']=$_REQUEST['file_name'];
			$data['id']=$id;
			$result=$AttachmentDAO->save($data);
			$where['id']=$id;
			$attachment=$AttachmentDAO->where($where)->find();
			if($result){
				//$this->assign("jumpUrl",U('/Admin/Attachment/attachmentlist',array('id'=>$attachment['foreign_id'],'type'=>$attachment['type_id'])));
				$this->ajaxReturn($result,'修改成功', '1');
			}else{
				$this->ajaxReturn($result,'修改失败', '1');
			}
		}else{		
			$this->display();
		}
	}
	
	public function filelist(){

		$where= $_REQUEST['typeid'] ?  " status=1 " : " status=0 ";
		$where .=" and userid = ".$this->userid ;
		import ( '@.ORG.Page' );
		$count = $this->dao->where($where)->count();
		$page=new Page($count,12); 
		$imagearr = explode(',', 'jpg,gif,png,jpeg,bmp,ttf,tif'); 

		$page->urlrule = 'javascript:ajaxload('.$_REQUEST['typeid'].',{$page},\''.$_REQUEST['inputid'].'\','.$this->isadmin.');';
		$show = $page->show(); 
		$this->assign("page",$show);
		$list=$this->dao->order('aid desc')->where($where)
		->limit($page->firstRow.','.$page->listRows)->select();
		foreach((array)$list as $key=>$r){
		$list[$key]['thumb']=in_array($r['fileext'],$imagearr) ? $r['filepath'] : __ROOT__.'/Public/Images/ext/'.$r['fileext'].'.png'; 
		}
		$this->assign('list',$list);
		$this->display();
	}

	function delfile($id){
		if(empty($id)){
		$id=$_REQUEST['id'];
		}
		$r = delattach(array('id'=>$id,'user_id'=>session('userid')));
		if($r){		 
			$this->success ("附件删除成功" );
		}else{
			$this->error ( "附件删除失败" );
		}
	}
	
	function delall($foreignid,$typeid){
		if(empty($foreignid)){
		$aid=$_REQUEST['foreignid'];
		}
		if(empty($typeid)){
		$aid=$_REQUEST['typeid'];
		}
		$r = delattach(array('foreign_id'=>$foreignid,'type_id'=>$typeid,'user_id'=>session('userid')));
		if($r){		 
			$this->success ("附件删除成功" );
		}else{
			$this->error ( "附件删除失败" );
		}
	}
	
	function cleanfile(){

		$r = delattach(array('status'=>0,'userid'=>$this->userid));
		if($r){		 
			$this->success ( L ( 'delete_ok' ) );
		}else{
			$this->error ( L ( 'delete_error' ) );
		}
	}
	
}
?>