<?php
namespace Home\Controller;
use Home\Controller\CommonController;
class IndexController extends CommonController {
    public function index(){
    	$this->display();
    }
	public function test(){
		
    	$arr = M('goods')->where('id=200')->getField('sku,id');
		echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($arr);exit;
		exit;
    }
}