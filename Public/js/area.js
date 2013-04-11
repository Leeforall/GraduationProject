function loadArea(select,areaType) {
	var areaId=select.value;
	setValue(select.id);
    $.post(ajaxurl,{'areaId':areaId},function(data){
        if(areaType=='city'){
           $('#'+areaType).html('<option value="-1">市/县</option>');
           $('#district').html('<option value="-1">镇/区</option>');
        }else if(areaType=='district'){
           $('#'+areaType).html('<option value="-1">镇/区</option>');
        }
        if(areaType!='null'&&areaId!=-1){
            $.each(data,function(no,items){
                $('#'+areaType).append('<option value="'+items.area_id+'">'+items.area_name+'</option>');
            });
        }
    });
}

function setValue(id){
	var val=$('#'+id).find("option:selected").text();
	$("#"+id+"_name").val(val)
}

function loadData(select,Type) {
	var Id=select.value;
	if(Id==0){
		$('#tab').hide();
	}else{
		$('#tab').show();
	}
	$.post(getTypes,{'Id':Id},function(data){
		if(Type=='Level_2'){
			$('#'+Type).html('');
			$('#Level_3').html('');
		}else if(Type=='Level_3'){
			$('#'+Type).html('');
		}
		if(Type!='null'&&Id>0){
			if(data!=null){
				$.each(data,function(no,items){
					$('#'+Type).append('<option value="'+items.id+'">'+items.type_name+'</option>');
				});
			}
			$('#'+Type).append('<option value="-1">其他</option>');
		}
		getTypeInfo();
	});
}

function getTypeInfo(){
	var id_l1=$('#Level_1').find("option:selected").val();
	var id_l2=$('#Level_2').find("option:selected").val();
	var id_l3=$('#Level_3').find("option:selected").val();
		if(!(typeof  id_l1==="undefined")&&(typeof  id_l2==="undefined")&&(typeof  id_l3==="undefined")){
			if(id_l1>0){
				$.post(getTypeById,{'Id':id_l1},function(data){
					if(data!=null){
						setInputData(data[0]); 
					}
				});
			}else{
				setInputData(null);
				$('#parent_id').val(1);
				$('#type_level').val(1);
			}
		}
		if(!(typeof  id_l1==="undefined")&&!(typeof  id_l2==="undefined")&&(typeof  id_l3==="undefined")){
			if(id_l2>0){
				$.post(getTypeById,{'Id':id_l2},function(data){
					if(data!=null){
						setInputData(data[0]); 
					}
				});
			}else{
				setInputData(null);
				$('#parent_id').val(id_l1);
				$('#type_level').val(2);
			}
		}
		if(!(typeof  id_l1==="undefined")&&!(typeof  id_l2==="undefined")&&!(typeof  id_l3==="undefined")){
			if(id_l3>0){
				$.post(getTypeById,{'Id':id_l3},function(data){
					if(data!=null){
						setInputData(data[0]); 
					}
				});
			}else{
				setInputData(null);
				$('#parent_id').val(id_l2);
				$('#type_level').val(3);
			}
		}
}

function setInputData(item){
	if(item!=null){
		$('#dosubmit').val('修 改');
		$('#manage_template').show();
		var id=item.id;
		var pid=item.parent_id;
		var remark=item.remark;
		var status=item.status;
		var type_name=item.type_name;
		var type_level=item.type_level;	
		$('#type_id').remove();
		$('#name').val(type_name);
		$('#remark').val(remark);
		$('#parent_id').val(pid);
		$('#type_level').val(type_level);
		$('#template_add').attr('action',actionEdit);
		var str='<input type="hidden" id="type_id" name="id" value=\''+id+'\'/>';
		$('#template_add').append(str);
		if(status=='1')
			$("input[name='status'][value=1]").attr("checked",true); 
		else	
			$("input[name='status'][value=0]").attr("checked",true);  
	}else{
		$('#dosubmit').val('添 加');
		$('#name').val(null);
		$('#remark').val(null);
		$("input[name='status'][value=1]").attr("checked",true); 
		$('#type_id').remove();
		$('#parent_id').val(null);
		$('#type_level').val(null);
		$('#template_add').attr('action',actionAdd);
		$('#manage_template').hide();
	}
}