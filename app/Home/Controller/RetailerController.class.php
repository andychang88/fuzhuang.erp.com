<?php
namespace Home\Controller;
use Home\Controller\CommonController;

class RetailerController extends CommonController {
	
	public $controll_keywords = "Retailer";
	
	public function __construct(){
		parent::__construct();
		
		$this->levels = array('1'=>'一级','2'=>'二级','3'=>'三级',);
		$this->roleNames = D('Role')->getRoleNames();
		
	}
	
	
	
	
	
	public function myGoods(){
    	$this->page_title = '产品列表';
		$Data = D('Retailer2goods');
		
		$where = $this -> collectForm();
		
//print_r($where);exit;
        $data = $Data->getRetailerGoods22($where);


        $this->data = $data['data'];
        $this->page = $data['page_html'];
        
        $this->retailer_products_status_selected = $where['bill_status'];
       
        
        $this->display();
    }
    
	public function collectForm(){
		$where = array();
		$bill_status = I('get.bill_status');
		$orderno = I('get.orderno');
		$retailer_id = I('get.retailer_id');
		
		$where['bill_status'] = C('retailer_products_status_default');
		
		if(isset($bill_status)){
			$where['bill_status'] = $bill_status;
		}

		if(isset($orderno)){
			$where['orderno'] = $orderno;
		}

		if(isset($retailer_id)){
			$where['retailer_id'] = $retailer_id;
		}
		return $where;
	}
	
    public function setPaymentInfo($id=0){
    	
    	
    	if (I('get.paymentid')) {
    		$paymentid = (int)I('get.paymentid');
    		
    	}
    	
    	if (IS_POST) {
    		$paymentid = I('post.paymentid');
    		$data['retailer2goods_id'] = I('post.id');
    		$data['retailer_id'] = I('post.retailer_id');
    		$data['paid_amount'] = I('post.bill_amount');
    		$data['memo'] = I('post.memo');
    		$data['paid_time'] = date("Y-m-d H:i:s");
    		$data['operator_id'] = session('user_id');
    		$data['audit_status'] = 0;
    		
    		//$total_should_paid = D('retailer2goods')->getTotalAmountShouldPaid($data['retailer2goods_id']);
    		//$total_already_paid = D('retailer2goods')->getTotalAmountAlreadyPaid($data['retailer2goods_id']);
    		
    		if ( $data['paid_amount'] && $data['retailer2goods_id'] ) {
	    		$photo_url   =   $this->upload();
		        if(!empty($photo_url)){
		        	$data['cert_img'] = $photo_url;
		        }
		        
		        if( $paymentid ){
		        	$data['id'] = $paymentid;
		        	$method = 'save';
		        } else {
		        	$method = 'add';
		        }
		        
		        if (M('retailerpaymenthistory')->$method($data)) {
		        	M('retailer2goods')->where('id='.(int)I('post.id'))->data(array('bill_status'=>2))->save();
		        	$this->msg_tip = "付款申请已经提交，请等待审核！";
		        }
		        
		        
		        
    		} else {
    			$this->msg_tip = "付款申请提交数据为空，请联系管理员！";
    		}
	    	
	        
    	}
    	
    	$this->payment = M('retailerpaymenthistory')->where('id='.(int)$paymentid)->find();
    	
		$Data = D('Retailer2goods');
		$list = $Data->getRetailerGoods22(array('retailer_id'=>0,'retailer2goods_id'=>$id));

		$this->vo = $list['data'][0];

		$this->page_title = "返回产品列表";
		
		$this->display();
	}
	
	private function updateBillAmount($id=0,$bill_status=0){
		$this->controll_keywords = "Retailer2goods";
		$act = I('post.act');
		$id = I('post.id');
		$bill_status = I('post.bill_status');

		$data = array();

		if($act != 'set_payment' &&  $bill_status > 0){
			$data = array('bill_status'=>$bill_status,'id'=>$id);
			$this->update($data,'is_param');
		}else{
			$this->update();
		}

	}
    
	public function paymenthistory(){
    	
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
		$operator_id = I('get.operator_id');
		$orderno = I('get.orderno');
		$starttime = I('get.starttime');
		$endtime = I('get.endtime');
		$retailer_payment_status = (int)I('get.retailer_payment_status');
		
		
		if( $operator_id ){
			$where['operator_id'] = $operator_id;
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
	
	public function delete($id,$arr=array()){
		
		    $Form = D('retailer2goods');
		    
		    if(empty($arr)){
		    	$arr = array('is_delete'=>1);
		    }
			
    		$result =   $Form->where('id='.$id)->data($arr)->save();
    		
			if($result) {
	            $this->success('操作成功！');
	        }else{
				
	            $this->error('删除错误！'.$Form->getError());
	        }
	        
    		/**
    		 *  $User->where('id=5')->delete(); // 删除id为5的用户数据
				$User->delete('1,2,5'); // 删除主键为1,2和5的用户数据
				$User->where('status=0')->delete(); // 删除所有状态为0的用户数据
    		 */
	}
	
	
}