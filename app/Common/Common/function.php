<?php
function getpage($count){
	$pagesize=C('PAGE_SIZE');
	
    $p=new Think\Page($count,$pagesize);
    $p->lastSuffix=false;
    /**
    $p->setConfig('header','<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;&nbsp;每页<b>%LIST_ROW%</b>条&nbsp;&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $p->setConfig('prev','上一页');
    $p->setConfig('next','下一页');
    $p->setConfig('last','末页');
    $p->setConfig('first','首页');
    $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
	/**/
    $p->parameter=I('get.');

    $page_html = $p->show();
    $limit_start = $p->firstRow;
    $limit_num = $p->listRows;
    
	return array($page_html, $limit_start, $limit_num);
}

function getpage2($count){
	$pagesize=C('PAGE_SIZE');
	$p=new Think\Page($count,$pagesize);
	$p->parameter=I('get.');
	return $p;
}

function format_time($time){
	if(is_numeric($time)){
		$time = date('Y-m-d H:i:s');
	}
	return str_replace(' ', '<br>', $time);
}

