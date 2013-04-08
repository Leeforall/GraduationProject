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
        if(areaType!='null'){
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