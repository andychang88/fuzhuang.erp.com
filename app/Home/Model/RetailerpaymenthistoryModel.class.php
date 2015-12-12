<?php
namespace Home\Model;
use Think\Model;

    class RetailerpaymenthistoryModel extends Model {
        // 定义自动验证
        protected $_validate    =   array(
            array('title','require','标题必须'),
            );
        // 定义自动完成
        protected $_auto    =   array(
            array('add_time','time',1,'function'),
            );

        
        public function getRetailers($where_arr=array()){
            $retailers = $this->where($where_arr)->select();
            return $retailers;
        }
        public function updateAuditStatus($id, $status){
        	$data['audit_status'] = (int)$status;
	    	$data['audit_time'] = date('Y-m-d H:i:s');
	        $this->where('id='.(int)$id)->data($data)->save();
        }
        public function getPaymentHistory($where_arr=array()){
        	
        	$data = $this->table('think_retailerpaymenthistory as ph')
        				 ->join('think_retailer2goods as r2g on ph.retailer2goods_id=r2g.id','left')
        				 ->field('ph.*, r2g.orderno,r2g.total_amount')
        				 ->where($where_arr)
        				 ->order('id desc')
        				 ->selectPage();
        	$list = $data['data'];
        	
        	$retailer_payment_status = C('retailer_payment_status');
        	
	        foreach ($list as $key=>$row){
	        	$list[$key]['status_name'] = $retailer_payment_status[$row['audit_status']];
	        	//$list[$key]['orderno'] = D('retailer2goods')->getOrderNo($row['retailer2goods_id']);
	        	$list[$key]['operator_name'] = D('retailer')->getRetailerName($row['operator_id']);
	        	$list[$key]['retailer_name'] = D('retailer')->getRetailerName($row['retailer_id']);
	        	
	        	$list[$key]['totalAmountAlreadyPaid'] = D('retailer2goods')->getTotalAmountAlreadyPaid($row['retailer2goods_id']);
	        	
	        	$list[$key]['totalAmountShouldPaid'] = D('retailer2goods')->getTotalAmountShouldPaid($row['retailer2goods_id']);
	        }
	        
	        $data['data'] = $list;
	        
	        return $data;
	        
        }
    }