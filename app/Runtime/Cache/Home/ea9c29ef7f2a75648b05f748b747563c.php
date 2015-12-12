<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang='zh-cn'>
<head>
<meta charset='utf-8'>
<meta http-equiv='X-UA-Compatible' content='IE=edge'>
<meta name="renderer" content="webkit"> 
<title>RMS-<?php echo ($page_title); ?></title>
       
<script>var config={"webRoot":"/","cookieLife":30,"requestType":"GET","pathType":"clean","requestFix":"-","moduleVar":"m","methodVar":"f","viewVar":"t","defaultView":"html","themeRoot":"/theme/","currentModule":"admin","currentMethod":"index","clientLang":"zh-cn","requiredFields":"","router":"/index.php","timeout":30000};
var lang={"submitting":"\u7a0d\u5019...","save":"\u4fdd\u5b58","timeout":"\u8fde\u63a5\u8d85\u65f6\uff0c\u8bf7\u68c0\u67e5\u7f51\u7edc\u73af\u5883\uff0c\u6216\u91cd\u8bd5\uff01"};
</script>

<script src='/Public/Js/all.js?v=7.2'></script>
<link rel='stylesheet' href='/Public/theme/default/zh-cn.default.css?v=7.2' type='text/css' media='screen' />
<link rel='stylesheet' href='/Public/Css/page.css?v=7.2' type='text/css' media='screen' />

<script type="text/javascript" src="/Public/Js/marquee.js?v=2.1.3"></script>

<script type="text/javascript" src="/Public/Js/fancyBox/source/jquery.fancybox.js?v=2.1.3"></script>
<link rel="stylesheet" type="text/css" href="/Public/Js/fancyBox/source/jquery.fancybox.css?v=2.1.2" media="screen" />


<style type="text/css">
#tbl2{border:1px solid #E6E6E6;border-collapse:collapse;}
#tbl2 th,#tbl2 td{padding:5px 5px;}
#tbl2 th{background:#E6E6E6;}
.w-no{width:45px;}
.w-orderno{width:140px;}
.w-img{width:100px;}
.w-time{width:80px;}
.w-size{width:360px;}
.w-operat{width:150px;}
.padding-10{padding:10px;}
.padding-15{padding:15px;}
.padding-20{padding:20px;}
.outer .main .table tr > th:first-child, .outer .main .table tr > td:first-child{
padding:15px;
}
#notices{margin-left:10px;}
#notices li a:link,#notices li a:visited,#notices li a:hover{font-weight:bold;color:red;}
.table-article{width:1200px;text-align:center;border:1px solid #ccc;word-break:break-all;word-wrap:break-word;}
.break-word{table-layout:fixed;word-break: normal;word-wrap:break-word;}
</style>
<script language="javascript">
function addTableRow(){
	var tmp_tr = $('#rowBase').clone();
	tmp_tr.find('td:last').html('<input type="button" onclick="removeTableRow(this)" value="删除该行" />');
	tmp_tr.find('input[name="attr_ids[]"]').val('');
	
	$('#trTotal').before(tmp_tr);
}
function removeTableRow(obj){
	var tmp_tr = $(obj).parents('tr:first');
	
	var clone_input = tmp_tr.find('input[name="attr_ids[]"]').clone();
	clone_input.attr('name','attr_ids_rm[]');
	$(obj).parents('form:first').prepend(clone_input);
	
	tmp_tr.remove();
}

$(function(){
	bgclock = $('#poweredby').get(0);
	clockon(bgclock);
	//提交表单后，错误退回原页面，修正按钮不可用状态
	$('.btn-submit').removeAttr('disabled');
	
	$(".form-datetime").datetimepicker(
	{
	    weekStart: 1,
	    todayBtn:  1,
	    autoclose: 1,
	    todayHighlight: 1,
	    startView: 2,
	    forceParse: 0,
	    format: "yyyy-mm-dd"
	});
	$(".form-date").datetimepicker(
			{
				language:  "zh-CN",
			    weekStart: 1,
			    todayBtn:  1,
			    autoclose: 1,
			    todayHighlight: 1,
			    startView: 2,
			    minView: 2,
			    forceParse: 0,
			    format: "yyyy-mm-dd"
	});
	
	 $("a[rel=group]").fancybox({ 
	        'titlePosition' : 'over', 
	        'cyclic'        : false, 
	        'titleFormat'    : function(title, currentArray, currentIndex, currentOpts) { 
	                    return '<span id="fancybox-title-over">' + (currentIndex + 1) + 
	 ' / ' + currentArray.length + (title.length ? '   ' + title : '') + '</span>'; 
	                } 
	  }); 

	 $('#notices').marquee({
         auto: true,
         interval: 3000,
         showNum: 1,
         stepLen: 1,
         type: 'vertical'
     });
})

function clockon(bgclock)
{
    var now = new Date();
    var year = now.getFullYear();
    var month = now.getMonth();
    var date = now.getDate();
    var day = now.getDay();
    var hour = now.getHours();
    var minu = now.getMinutes();
    var sec = now.getSeconds();
    var week;
    month = month+1;
    if(month<10)month="0"+month;
    if(date<10)date="0"+date;
    if(hour<10)hour="0"+hour;
    if(minu<10)minu="0"+minu;
    if(sec<10)sec="0"+sec;
    var arr_week = new Array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
    week = arr_week[day];
    var time = "";
    time = year+"年"+month+"月"+date+"日  "+week+"  "+hour+":"+minu+":"+sec;
    bgclock.innerHTML="["+time+"]";
    var timer = setTimeout("clockon(bgclock)",1000);
            
}
</script>

</head>
<body class="m-admin-index">
     
<header id='header'>

  <div id='topbar'>
    <div class='pull-right' id='topnav'>
    	<div class='dropdown' id='userMenu'>
	    	<a href='javascript:;' data-toggle='dropdown'><i class='icon-user'></i> <?php echo (session('nickname')); ?> <span class='caret'></span></a>
	    	<ul class='dropdown-menu'>
			<li><a href='/index.php/Users/updatePasswd/id/<?php echo (session('user_id')); ?>' target='' class='iframe' data-width='500'>更改密码</a></li>
			</ul>
		</div>
		<a href='/index.php/Users/logout' >退出</a>
		
	</div>
    <h5 id='companyname'><?php echo ($system_name); ?>    </h5>
  </div>
  
  <nav id='mainmenu'>
    <ul class='nav'>
		<li <?php if((CONTROLLER_NAME) == "Retailer"): ?>class='active'<?php endif; ?> ><a href='/index.php/Retailer/myGoods'  id='menumy'><i class="icon-home"></i><span> 分销商产品</span></a></li>
		<li <?php if((CONTROLLER_NAME) == "Soldrecords"): ?>class='active'<?php endif; ?> ><a href='/index.php/Soldrecords'  ><span> 售出管理</span></a></li>
		<li <?php if((CONTROLLER_NAME) == "Retailerapply"): ?>class='active'<?php endif; ?> ><a  href='/index.php/retailerapply'  id='menuproduct'>预订退订申请</a></li>
		<li <?php if((CONTROLLER_NAME) == "Goods"): ?>class='active'<?php endif; ?> ><a href='/index.php/goods'  id='menuproject'>仓库管理</a></li>
		<li <?php if((CONTROLLER_NAME) == "Plans"): ?>class='active'<?php endif; ?> ><a href='/index.php/plans'  id='menuqa'>生产计划单</a></li>
		<li <?php if((CONTROLLER_NAME) == "Finance"): ?>class='active'<?php endif; ?> ><a href='/index.php/Finance/index'  id='menureport'>财务管理</a></li>
		<li <?php if((CONTROLLER_NAME) == "Users"): ?>class='active'<?php endif; ?> ><a href='/index.php/Users/index'  id='menudoc'>用户管理</a></li>
		<li <?php if((CONTROLLER_NAME) == "Report"): ?>class='active'<?php endif; ?> ><a href='/index.php/Report/index'  id='menudoc'>统计报表</a></li>
		<li <?php if((CONTROLLER_NAME) == "Articles"): ?>class='active'<?php endif; ?> ><a href='/index.php/Articles/index'  id='menudoc'>通知</a></li>
	</ul>
	
  </nav>
  
  <nav id="modulemenu">
    <ul class='nav'>
		<li><span id="myname"><i class="icon-user"></i> <?php echo (session('nickname')); ?>&nbsp;<i class="icon-angle-right"></i>&nbsp;</span></li>
		
		<?php if(is_array($submenus)): $i = 0; $__LIST__ = $submenus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li  <?php if($menu["selected"] == 1): ?>class='active'<?php endif; ?>  ><a href='<?php echo ($menu["url"]); ?>' target='_self' ><?php echo ($menu["text"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
		
		
	</ul>
  </nav>
</header>


    

<div id="wrap">
   <div class="outer">
  
		

<div class="main">

<meta charset='utf-8'>
      <FORM method="get" id="search_frm"  action="/index.php/Retailerapply/index">
      <table class="search_table">
      
      <tr>
      	<td>申请人</td><td><select id="" name="apply_user_id" onchange="" ondblclick="" class="" ><option value="" >请选择分销商</option><?php  foreach($retailers as $key=>$val) { if(!empty($selected_apply_user_id) && ($selected_apply_user_id == $key || in_array($key,$selected_apply_user_id))) { ?><option selected="selected" value="<?php echo $key ?>"><?php echo $val ?></option><?php }else { ?><option value="<?php echo $key ?>"><?php echo $val ?></option><?php } } ?></select></td>
      	<td>申请时间</td><td><input  class="w-120px form-datetime" type="text" name="starttime" value="<?php echo ($_GET['starttime']); ?>" /> ~ <input  class="w-120px form-datetime" type="text" name="endtime" value="<?php echo ($_GET['endtime']); ?>" /></td>
		<td>申请类型</td><td><select id="" name="type" onchange="" ondblclick="" class="w-90px" ><option value="" >请选择申请类型</option><?php  foreach($reserveType as $key=>$val) { if(!empty($selected_type) && ($selected_type == $key || in_array($key,$selected_type))) { ?><option selected="selected" value="<?php echo $key ?>"><?php echo $val ?></option><?php }else { ?><option value="<?php echo $key ?>"><?php echo $val ?></option><?php } } ?></select></td>
		<td>状态</td><td><select id="" name="retailer_apply_status" onchange="" ondblclick="" class="w-90px" ><option value="" >请选择申请类型</option><?php  foreach($retailer_apply_status as $key=>$val) { if(!empty($retailer_apply_status_selected) && ($retailer_apply_status_selected == $key || in_array($key,$retailer_apply_status_selected))) { ?><option selected="selected" value="<?php echo $key ?>"><?php echo $val ?></option><?php }else { ?><option value="<?php echo $key ?>"><?php echo $val ?></option><?php } } ?></select></td>
		
		<td colspan = 2>
         	<INPUT type="submit" value="查找">&nbsp;&nbsp;
      	</td>
      </tr>
      </table>

 </FORM>


<br>

<table class='table table-condensed table-hover table-striped tablesorter table-fixed' >
    <thead>
    <tr class='colhead'>
	        <th>编号</th>
	        <th>Sku</th>
	        <th>产品图片</th>
	        <th>品牌</th>
	        <th>颜色</th>
	        <th>大小</th>
	        <th>类型</th>
	        <th>预订数量</th>
	        <th>申请人</th>
	        <th>添加时间</th>
	        <th>状态</th>
	        <th>备注</th>
	        <th class="w-300px">操作</th>
	</tr>
	</thead>
	<?php if(empty($data)): ?><tr><td  class='text-center'  colspan=13>没有数据</td></tr><?php endif; ?>
	
    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr  class='text-center' >
	        <td><?php echo ($vo["id"]); ?></td>
	        <td><?php echo ($vo["sku"]); ?></td>
	        <td>
	        
	        <?php if(empty($vo["photo"])): echo ($vo["title"]); ?> 
	        <?php else: ?>
	        
	        <a  rel="group"  id="<?php echo ($vo["sku"]); ?>-<?php echo ($vo["id"]); ?>" href="/Public/<?php echo ($vo["photo"]); ?>">
	        <img style="width:60px;height:60px;" src="/Public/<?php echo ($vo["photo"]); ?>" alt="<?php echo ($vo["title"]); ?>"  title="<?php echo ($vo["title"]); ?>" />
	        </a><?php endif; ?>
	        
	        </td>
	        <td><?php echo ($vo["brand"]); ?></td>
	        <td><?php echo ($vo["color"]); ?></td>
	        <td><?php echo ($vo["size"]); ?></td>
	        <td><?php if($vo["type"] == 1): ?>预订 <?php else: ?> 退订<?php endif; ?> </td>
	        <td><?php echo ($vo["stock_total"]); ?></td>
	        <td><?php echo ($vo["apply_user"]); ?></td>
	        <td><?php echo (date("Y-m-d",$vo["add_time"])); ?></td>
	        <td><?php echo ($vo["audit_status"]); ?></td>
	        <td><?php echo ($vo["memo"]); ?></td>
	        <td>
	        
	        <?php if($vo["is_audit"] == 1 or $vo["is_audit"] == 0): ?><a href="/index.php/Retailerapply/add/id/<?php echo ($vo["id"]); ?>">编辑</a><?php endif; ?>
	        
	        <?php if($vo["is_audit"] == 0): ?><a href="/index.php/Retailerapply/auditSubmit/id/<?php echo ($vo["id"]); ?>">提交审核</a>
	        <?php elseif($vo["is_audit"] == 1): ?>
	        	<a href="/index.php/Retailerapply/auditCancel/id/<?php echo ($vo["id"]); ?>">撤回</a>
				
	        	<a href="/index.php/Retailerapply/auditResultOk/id/<?php echo ($vo["id"]); ?>">审核通过</a>
	        	<a href="/index.php/Retailerapply/auditResultReset/id/<?php echo ($vo["id"]); ?>">审核驳回</a>
				
				
	        <?php elseif($vo["is_audit"] == 2): ?>
	        	审核已通过
	        <?php elseif($vo["is_audit"] == 3): ?>
	        	审核已驳回<?php endif; ?>
	       
	        </td>
	    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    
    <tr><td class="pager" colspan=13><div class="viciao"><?php echo ($page); ?></div></td></tr>
    </table>
  </div>  
    


	</div>
</div> 
  
<div id="footer">
  <div id="notices" class="pull-left">
  
    	<?php if(!empty($notices)): ?><ul class="tree">
    		<?php if(is_array($notices)): $i = 0; $__LIST__ = $notices;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="/index.php/Articles/detail/id/<?php echo ($vo["id"]); ?>">系统通知：<?php echo ($vo["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
    	</ul><?php endif; ?>
    
  </div>
  
  <div id="poweredby">
    
  </div>
</div>

</body></html>