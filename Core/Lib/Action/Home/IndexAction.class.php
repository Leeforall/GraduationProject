<?php
/**
 * 前台入口模块
 * 
 */
class IndexAction extends HomeAction {
    public function index(){
		$this->display();
	}
	
	public function getExhibitions(){
		$Dao=new Model();
		
		$list=$Dao->table('tp_Exhibitiontype Exhibitiontype,tp_Exhibition Exhibition,tp_Areas province ,tp_Areas city,tp_Areas district')
		->where('Exhibitiontype.id=Exhibition.type_id AND Exhibition.province_id=province.area_id AND province.area_type=1
		 AND city.area_type=2 AND Exhibition.city_id=city.area_id  AND district.area_type=3 AND Exhibition.district_id=district.area_id' )
		->field('Exhibition.id, Exhibitiontype.name type,Exhibition.name_cn,Exhibition.name_en,Exhibition.period_start,
		Exhibition.period_end,Exhibition.day_start,Exhibition.day_end,Exhibition.logo_url,Exhibition.is_on_show,Exhibition.contact,Exhibition.contact_add,
		Exhibition.hall,Exhibition.address,Exhibition.telephone,Exhibition.mobilephone,Exhibition.fax,Exhibition.postcode,
		Exhibition.website,province.area_name province,city.area_name city,district.area_name district,Exhibition.organizer,Exhibition.host,
		Exhibition.coorganizer,Exhibition.supporter,Exhibition.description') 
		->order('Exhibition.id desc' )->select();
		
		
		/*$list = D('Exhibition')->select();*/
		//$this->assign('list',$list);
		//dump($list);
		//$this->display();
		$this->ajaxReturn($list,'成功',1);
	}
	
	
	public function getAttachmentByExhibitionId($id){
		$Dao=D('Attachment');
		
		$list=$Dao->where(array('foreign_id'=>$id,'type_id'=>1))->select();
		//$this->assign('list',$list);
		//dump($list);
		//$this->display();
		$this->ajaxReturn($list,'成功',1);
	}
	
}