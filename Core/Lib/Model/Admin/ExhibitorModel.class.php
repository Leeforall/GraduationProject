<?php
class ExhibitorModel extends Model {

	//自动验证
	protected $_validate=array(
		array('name_cn','require','参展商名称必须！',1,'',3),
		array('name_cn','','参展商名称已经存在！',1,'unique',3), // 新增修改时候验证name_cn字段是否唯一
		array('user_id','','已经有公司信息，不可以添加！',1,'unique',3), // 新增修改时候验证user_id字段是否唯一
		array('status','require','参展商状态必须！',1,'',3),
		array('contact','require','联系人必须！',1,'',3)
	);
	
	//自动完成
	protected $_auto = array ( 
		array('createtime','time',1,'function'), 	//新增时
		array('modifytime','time',1,'function'), 	//新增时
		array('is_verified',0), 	//新增时
	);
	
	//取得所有展览
	public function getAllExhibitor($where = '' , $order = 'id  ASC', $limit=''){
		return $this->where($where)->order($order)->limit($limit)->select();
	}
	
	//取得单个展览
	public function getExhibitor($where = '',$field = '*'){
		return $this->field($field)->where($where)->find();
	}
	
	//删除展览
	public function delExhibitor($where){
		if($where){
			return $this->where($where)->delete();
		}else{
			return false;
		}
	}
}

?>

