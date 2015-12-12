<?php
namespace Home\Controller;
use Home\Controller\CommonController;

class FinanceController extends CommonController {
	
	public $controll_keywords = "Retailer2goods";
	
	public function __construct(){
		parent::__construct();
		$this->title = '供应商付款待审核列表';
		$this->search_status_default = 2;
	}
	public function collectForm(){
		
		$where = array();
		$bill_status = I('get.status');
		$orderno = I('get.orderno');
		$retailer_id = I('get.retailer_id');
		
		$where['r2g.bill_status'] = $this->search_status_default;//默认展示待审核的
		
		if(!empty($bill_status)){
			$where['r2g.bill_status'] = $bill_status;
		}

		if(!empty($orderno)){
			$where['r2g.orderno'] = $orderno;
		}

		if(!empty($retailer_id)){
			$where['r2g.retailer_id'] = $retailer_id;
		}
		return $where;
	}
	
    public function index(){
    	
    	if (I('get.delete')) {
    		M('retailerpaymenthistory')->where('id='.(int)I('get.delete'))->data(array('is_delete'=>1))->save();
    		$this->msg_tip = "删除成功";
    	}
    	
    	$Data = D('retailerpaymenthistory');
    	
    	$where = $this->collectFormHistory();
    	
    	if(!session('is_admin')){
    		$where['ph.retailer_id'] = session('user_id');
    	}
    	$where['ph.is_delete'] = 0;

        $data = $Data->getPaymentHistory($where);
        
        $this->data = $data['data'];
        $this->page = $data['page_html'];
        
        $this->display();
    }
	
    public function collectFormHistory(){
		$where = array();
		$retailer_id = I('get.retailer_id');
		$orderno = I('get.orderno');
		$starttime = I('get.starttime');
		$endtime = I('get.endtime');
		$retailer_payment_status = (int)I('get.retailer_payment_status');
		
		
		if( $retailer_id ){
			$where['retailer_id'] = $retailer_id;
			$this->retailer_id_selected = $retailer_id;
		}
    	if( $orderno ){
			$where['orderno'] = $orderno;
		}
		
		if( $starttime && $endtime){
			$tmp['paid_time'] = array('gt', $starttime);
			$tmp['paid_time'] = array('lt', $endtime);
			
			$where['_complex'] = $tmp;
		}

		if( isset($_GET['retailer_payment_status']) && strlen($_GET['retailer_payment_status'])>0){
			$where['audit_status'] = (int)$_GET['retailer_payment_status'];
		}
		return $where;
	}
    
	public function updateBillAmount($id=0,$bill_status=0){
		$this->controll_keywords = "Retailer2goods";
		$act = I('post.act');

		$data = array();

		if($act != 'set_payment' &&  $bill_status > 0){
			$data = array('bill_status'=>$bill_status,'id'=>$id);
			$this->update($data,'is_param');
		}else{
			$this->update();
		}

	}
	
	public function audit($id, $status){
    	
        D('retailerpaymenthistory')->updateAuditStatus($id, $status);
        $this->success('审核成功');
        
    }
    
    public function clean($id, $retailer2goods_id){
    	D('retailerpaymenthistory')->updateAuditStatus($id, 1);
    	M('retailer2goods')->where('id='.(int)$retailer2goods_id)->data(array('bill_status'=>3))->save();
    	$this->success('操作成功');
    }
	public function add(){
    	$this->title = '生产计划单列表';
        $this->display();
    }

}