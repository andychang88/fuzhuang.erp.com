<?php
namespace Home\Controller;
use Home\Controller\CommonController;

class RetailerpaymenthistoryController extends CommonController {
	
	public $controll_keywords = "retailerpaidhistory";
	
	public function __construct(){
		parent::__construct();
		
		$this->retailer_payment_status = C('retailer_payment_status');
		
		
	}
	
	public function collectForm(){
		$where = array();
		$apply_user_id = I('get.apply_user_id');
		$starttime = strtotime(I('get.starttime'));
		$endtime = strtotime(I('get.endtime'));
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
    		$where['retailer_id'] = session('user_id');
    	}
    	
        $data = $Data->getPaymentHistory($where);
        
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
    		$arr = array('is_audit'=>2);
			$this->audit($id, $arr);
    }
	//审核驳回
    public function auditResultReset($id=''){
    		$arr = array('is_audit'=>3);
			$this->audit($id, $arr);
    }
    
    //取消审核
	public function auditCancel($id){
			$arr = array('is_audit'=>0);
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