<?php
class ExhibitionModel extends Model {

	//自动验证
	protected $_validate=array(
		array('name_cn','require','展览名称必须！',1,'',3),
		array('name_cn','','展览名称已经存在！',1,'unique',3), // 新增修改时候验证name_cn字段是否唯一
		array('status','require','展览状态必须！',1,'',3),
		array('is_on_show','require','展出是否进行必须！',1,'',3)
	);
	
	//自动完成
	protected $_auto = array ( 
		array('createtime','time',1,'function'), 	//新增时
		array('is_verified',1), 	//新增时
	);
	
	//取得所有展览
	public function getAllExhibition($where = '' , $order = 'id  ASC', $limit=''){
		return $this->where($where)->order($order)->limit($limit)->select();
	}
	
	//取得单个展览
	public function getExhibition($where = '',$field = '*'){
		return $this->field($field)->where($where)->find();
	}
	
	//删除展览
	public function delExhibition($where){
		if($where){
			return $this->where($where)->delete();
		}else{
			return false;
		}
	}
}

?>

