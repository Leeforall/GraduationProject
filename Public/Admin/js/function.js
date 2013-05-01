/**
 * 后台公共JS函数库
 *
 */
function changestatus(action,id,message) {
	window.top.art.dialog.confirm(message, function(){
    	$.post(action,{'id':id},function(responce){
					console.log(responce);
			if(responce.data='1'){
					var win = art.dialog.open.origin;//来源页面
					// 如果父页面重载或者关闭其子对话框全部会关闭
					art.dialog.close();
					//api.title('警告').content('请注意artDialog两秒后将关闭！').lock().time(2);
					art.dialog.tips(responce.info, 2);
					win.location.reload();
				}else{
					art.dialog.tips(responce.info, 1.5);
				}
		});
	}, function(){
	    art.dialog.tips('你取消了操作');
	});
}

function doverify(action,val,id){
	switch(val){
		case "0":
			message="设为审核进行中？";
		break;
		case "1":
			message="确认审核不通过？";
		break;
		case "2":
			message="确认审核通过";
		break;
		default:
			return;
		
	}
	window.top.art.dialog.confirm(message, function(){
    	$.post(action,{'id':id,'val':val},function(responce){
					console.log(responce);
			if(responce.data='1'){
					var win = art.dialog.open.origin;//来源页面
					// 如果父页面重载或者关闭其子对话框全部会关闭
					art.dialog.close();
					//api.title('警告').content('请注意artDialog两秒后将关闭！').lock().time(2);
					art.dialog.tips(responce.info, 2);
					win.location.reload();
				}else{
					art.dialog.tips(responce.info, 1.5);
				}
		});
	}, function(){
	    art.dialog.tips('你取消了操作');
	});
}
 
 
function attachmentedit(action,originname,ext){
	art.dialog.data('oldname',originname);
	art.dialog.data('ext',ext);
	art.dialog.open(action,{
		id:'attachmentedit',
		lock: 'true',
		window: 'top'
	});
}
 
 
function confirmurl(url,message) {
	window.top.art.dialog.confirm(message, function(){
    	redirect(url);
	}, function(){
	    return true;
	});
	//if(confirm(message)) redirect(url);
}

function redirect(url) {
	location.href = url;
}

/**
 * 全选checkbox,注意：标识checkbox id固定为为check_box
 * @param string name 列表check名称,如 uid[]
 */
function selectall(name) {
	if ($("#check_box").attr("checked")=='checked') {
		$("input[name='"+name+"']").each(function() {
  			$(this).attr("checked","checked");
			
		});
	} else {
		$("input[name='"+name+"']").each(function() {
  			$(this).removeAttr("checked");
		});
	}
}
function openwinx(url,name,w,h) {
	if(!w) w=screen.width-4;
	if(!h) h=screen.height-95;
    window.open(url,name,"top=100,left=400,width=" + w + ",height=" + h + ",toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,status=no");
}

//表单提交时弹出确认消息
function submit_confirm(id,msg,w,h){
	if(!w) w=250;
	if(!h) h=100;
	  window.top.art.dialog({
      content:msg,
      lock:true,
      width:w,
      height:h,
      ok:function(){
        $("#"+id).submit();
        return true;
      },
      cancel: true
    });
}