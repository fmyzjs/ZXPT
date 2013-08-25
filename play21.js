$(document).ready(function(){
	$("#startpark").click(function(){
	jPrompt('萝卜值:', '1', '请输入你要压多少萝卜', function(r) {
    		if( r )
			if(r > 0 && r <= 200){
			$("#mess").html("您压了"+r+"点萝卜");
			$.getJSON("play21.php?action=init&bonus="+r+"&t="+new Date() ,function(data){ 
			var $park = data['park'];
			if($park=='start'){
				jAlert('您已开局');
			}else if($park=='no'){
				jAlert('您的萝卜不足');
			}
				$("#computerpark").html(data['compark']);
				$("#computernum").html(data['comnum']+"点");
				$("#playpark").html(data['playpark']);
				$("#playnum").html(data['playnum']+"点");
					if(data['playnum'] == 21)
						jAlert('黑杰克!');
			});
			}else{
				jAlert("测试阶段，只允许使用1至200个萝卜");
			}
		});
	});
	$("#stoppark").click(function(){
		$.getJSON("play21.php?action=stop&t="+new Date(),function(data){ 
			var $park = data['park'];
			if($park=='nostart'){
				jAlert('还未开局');
			}else{
				$("#computerpark").html(data['compark']);
				$("#computernum").html(data['comnum']+"点");
				if(data['comnum'] > 21)
				jAlert("庄家爆掉了，恭喜你获得了："+data['playbonus']+"点萝卜");
				else
				jAlert("你获得了："+data['playbonus']+"点萝卜");
			}
		});
	});
	$("#retpark").click(function(){
		$.getJSON("play21.php?action=retpark&t="+new Date(),function(data){
			var $park = data['park'];
			var $playnum = data['playnum'];
				if($park=='nostart'){
					jAlert('还未开局');
				}else{
					$("#playpark").html(data['playpark']);
					$("#playnum").html(data['playnum']+"点");
					if($playnum > 21)
						jAlert('您爆掉了');
					if($playnum == 21)
						jAlert('恭喜您获得抽得了21点');
				}
		});
	});
	$(".has_children").click(function(){
		$(".has_children:eq(1)>a:contains('c')").remove();
		var $a1 = $("<a>zz</a>");
		$(this).append($a1);			//添加控件
	        $(this).siblings().removeClass("highlight")   //siblings选择同样控件
        	        .children("a").slideUp().end();	//hide(),fadeOut()
							//slow,normal,fast
        	$(this).addClass("highlight")		//添加效果
                	.children("a").slideDown().end();	//show(),fadeIn()
		$(this).clone(true).appendTo("#menu2");  //克隆控件
		$("#menu2>.has_children").css("opacity","0.5");	//不透明度
	});
	$("a").click(function(event){
		event.stopPropagation();    //停止事件冒泡
	});
});