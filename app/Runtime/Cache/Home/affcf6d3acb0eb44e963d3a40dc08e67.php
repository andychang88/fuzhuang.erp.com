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
<table class='table table-condensed table-hover table-striped tablesorter table-fixed' >
   <thead>
    <tr class='colhead'>
	        <th>单号</th>
	        <th>Sku</th>
	        <th class="w-100px">产品图片</th>
	        <th class="w-400px">明细</th>
	    
			<th>成本</th>
	        <th>创建时间<br>出货时间</th>
	      
			<th>创建人</th>
			
	        <th>备注</th>
			<th>状态</th>
	        <th>操作</th>
	</tr>
	</thead>
	
	<tbody>
    <?php if(is_array($data)): $kk = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($kk % 2 );++$kk;?><tr  class='text-center' <?php if(($kk % 2) == 1): ?>class="altrow"<?php endif; ?> >
	        <td><?php echo ($vo["orderno"]); ?></td>
	        <td><?php echo ($vo["sku"]); ?></td>
	        <td>
	        
	        <?php if(empty($vo["photo"])): echo ($vo["title"]); ?> 
	        <?php else: ?>
	        
	        <a  rel="group"  id="<?php echo ($vo["sku"]); ?>-<?php echo ($vo["id"]); ?>" href="/Public/<?php echo ($vo["photo"]); ?>">
	        <img style="width:60px;height:60px;" src="/Public/<?php echo ($vo["photo"]); ?>" alt="<?php echo ($vo["title"]); ?>"  title="<?php echo ($vo["title"]); ?>" />
	        </a><?php endif; ?>
	        
	        </td>
	        <td>
			
			
						<table class=" w-350px table-bordered" id="tbl2"  align="center" >
						<?php if(!empty($vo["plans_attr"])): ?><tr>
							<th class="center">品牌</th>
							<th class="center">语言</th>
							<th class="center">颜色</th>
							<th>XS</th><th>S</th><th>M</th><th>L</th><th>XL</th><th>XXL</th>
							<th class="center">备注</th>
							</tr><?php endif; ?>
						<?php if(is_array($vo["plans_attr"])): $i = 0; $__LIST__ = $vo["plans_attr"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attr): $mod = ($i % 2 );++$i;?><tr>
							<td><?php echo ($attr["brand"]); ?></td>
							<td><?php echo ($attr["lang"]); ?></td>
							<td><?php echo ($attr["color"]); ?></td>
							<td><?php echo ($attr["num_xs"]); ?></td>
							<td><?php echo ($attr["num_s"]); ?></td>
							<td><?php echo ($attr["num_m"]); ?></td>
							<td><?php echo ($attr["num_l"]); ?></td>
							<td><?php echo ($attr["num_xl"]); ?></td>
							<td><?php echo ($attr["num_xxl"]); ?></td>
								 
							<td><?php echo ($attr["memo_attr"]); ?></td>
							
							
							</tr><?php endforeach; endif; else: echo "" ;endif; ?>

							
					   </table>
			
			
			</td>
	       
	        
			
			<td><?php echo ($vo["total_cost"]); ?></td>
	        <td><?php echo (date("Y-m-d",$vo["add_time"])); ?><br><?php echo (date("Y-m-d",$vo["produce_time"])); ?></td>
	       
			<td><?php echo ($vo["user_name"]); ?></td>
	        <td><?php echo ($vo["memo"]); ?></td>
			<td>
			<?php if($vo["is_audit"] == 0): ?>草稿<?php endif; ?>
			<?php if($vo["is_audit"] == 1): ?>生产审核中<?php endif; ?>
			<?php if($vo["is_audit"] == 2): ?>生产中<?php endif; ?>
			<?php if($vo["is_audit"] == 3): ?>生产审核驳回<?php endif; ?>
			<?php if($vo["is_audit"] == 4): ?>生产完成<?php endif; ?>
			<?php if($vo["is_audit"] == 5): ?>产品单<?php endif; ?>
			<?php if($vo["is_audit"] == 6): ?>财务审核中<?php endif; ?>
			<?php if($vo["is_audit"] == 7): ?>采购中<?php endif; ?>

			</td>

	        <td>
			
	        
	        <?php if($vo["is_audit"] == 0): ?><a href="/index.php/Plans/add/id/<?php echo ($vo["id"]); ?>">编辑</a> 
				<a onclick="return confirm('确认要删除吗？');"  href="/index.php/Plans/delete/id/<?php echo ($vo["id"]); ?>">删除</a><br>
	        	<a href="/index.php/Plans/audit/id/<?php echo ($vo["id"]); ?>/type/6">提交财务审核</a>
			<?php elseif($vo["is_audit"] == 6): ?>
			
				<a href="/index.php/Plans/audit/id/<?php echo ($vo["id"]); ?>/type/0">撤回</a>
	        	<?php if(($_SESSION['is_admin']== 1) ): ?><br><a href="/index.php/Plans/audit/id/<?php echo ($vo["id"]); ?>/type/2">财务审核通过</a><br>
	        	<a href="/index.php/Plans/audit/id/<?php echo ($vo["id"]); ?>/type/3">财务审核驳回</a><?php endif; ?>
			
	        <?php elseif($vo["is_audit"] == 2): ?>
				<a href="/index.php/planshistory/index/plans_id/<?php echo ($vo["id"]); ?>">出货记录</a>
				<br><a href="/index.php/Plans/audit/id/<?php echo ($vo["id"]); ?>/type/4">生产完成</a>
	        <?php elseif($vo["is_audit"] == 3): ?>
	        	审核已驳回
			<?php elseif($vo["is_audit"] == 4): ?>
				<a href="/index.php/Plans/plan_to_product/plans_id/<?php echo ($vo["id"]); ?>">生产入库</a>
			<?php elseif($vo["is_audit"] == 5): ?>
				无
			<?php elseif($vo["is_audit"] == 7): ?>
				<a href="/index.php/Plans/audit/id/<?php echo ($vo["id"]); ?>/type/0">撤回</a>
	        	<a href="/index.php/Plans/audit/id/<?php echo ($vo["id"]); ?>/type/4">生产完成</a>
	        <?php elseif($vo["is_audit"] == 1): ?>
	        	<a href="/index.php/Plans/audit/id/<?php echo ($vo["id"]); ?>/type/0">撤回</a>

				<?php if(($_SESSION['is_admin']== 1) ): ?><br><a href="/index.php/Plans/audit/id/<?php echo ($vo["id"]); ?>/type/2">生产审核通过</a><br>
	        	<a href="/index.php/Plans/audit/id/<?php echo ($vo["id"]); ?>/type/3">生产审核驳回</a><?php endif; endif; ?>
	       
	        </td>
	    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    
    </tbody>
    
    
    <tfoot><tr><td class="pager" colspan=10><div class="viciao"><?php echo ($page); ?></div></td></tr></tfoot>
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