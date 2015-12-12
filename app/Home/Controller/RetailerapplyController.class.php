<?php
namespace Home\Controller;
use Home\Controller\CommonController;

class RetailerapplyController extends CommonController {
	
	public $controll_keywords = "Retailerapply";
	
	public function __construct(){
		parent::__construct();
		
		$this->title_add = '添加预订退订单';
		$this->title = '预订退订列表';
		
		$this->reserveType = array('1'=>'预订', '2'=>'退订');
		$this->selected_apply_user_id = I('get.apply_user_id');
		$this->selected_type = I('get.type');
		
	}
	
	public function collectForm(){
		$where = array();
		$apply_user_id = I('get.apply_user_id');
		$starttime = strtotime(I('get.starttime'));
		$endtime = strtotime(I('get.endtime'));
		$retailer_apply_status = I('get.retailer_apply_status');
		$type = I('get.type');
		
		if(!empty($apply_user_id)){
			$where['apply_user_id'] = $apply_user_id;
		}

		if(!empty($starttime)){
			$where['add_time'] = array('gt', ($starttime) );
		}
		
		if(!empty($endtime)){
			if( $where['add_time'] ){
				$where['add_time'] = array(array('gt', ($starttime) ), array('lt', ($endtime) ));
			} else {
				$where['add_time'] = array('lt', ($endtime) );
			}
			
		}
		
		if(!empty($retailer_apply_status) && strlen($retailer_apply_status) > 0){
			$where['is_audit'] = $retailer_apply_status;
		}
		
		if(!empty($type)){
			$where['type'] = $type;
		}
		
		return $where;
	}
	
    public function index(){
    	
    	$Data = D($this->controll_keywords);
    	$where = array('is_delete'=>0);
    	
    	$where = $this->collectForm();
    	
    	if(!session('is_admin')){
    		$where['apply_user_id'] = session('user_id');
    	}
    	
        $data = $Data->getRetailerRepplies($where);
        
        $this->data = $data['data'];
        $this->page = $data['page_html'];
        
        $this->display();
    }
    
	public function add($id=0){
		//编辑一个计划单
		if ($id) {
			$Data = D($this->controll_keywords);
			$list = $Data->getRetailerRepplies(array('id'=>$id));
	
	        $this->vo = $list['data'][0];
		}
		
		//编辑提交或者添加一个计划单
		if (IS_POST) {
			$method = $id ? 'update' : 'insert';
			$this -> $method();
		}
		
        $this->display();
    }
    
	//审核通过
    public function auditResultOk($id=''){
    		$arr = array('is_audit'=>2, 'audit_time'=>time());
			$this->audit($id, $arr);
    }
	//审核驳回
    public function auditResultReset($id=''){
    		$arr = array('is_audit'=>3, 'audit_time'=>time());
			$this->audit($id, $arr);
    }
    
    //废除
	public function auditCancel($id){
			$arr = array('is_audit'=>4, 'audit_time'=>time());
			$this->audit($id, $arr);
	}
	
	//提交审核
	public function auditSubmit($id){
			$arr = array('is_audit'=>1);
			$this->audit($id, $arr);
	}
	
	private function audit($id, $arr){
		$this->delete($id, $arr);
	}
	
	
}