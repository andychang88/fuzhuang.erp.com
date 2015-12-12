<?php
namespace Home\Model;
use Think\Model;

    class RetailerapplyModel extends Model {
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
        
        public function getRetailerRepplies($where_arr=array()){
        	$data = $this->order('id desc')->where($where_arr)->selectPage();
        	$list = $data['data'];
        	$retailer_apply_status = C('retailer_apply_status');
        	
	        foreach ($list as $key=>$row){
	        	$is_audit = $row['is_audit'];
	        	$list[$key]['apply_user'] = D('retailer')->where('id='.$row['apply_user_id'])->getField('retailer_name');
	        	$list[$key]['sold_amount'] = D('Soldrecords')->getSoldAmount($row['id']);
	        	$list[$key]['stock_left'] = $row['stock_total'] - $row['stock_used'];
	        	$list[$key]['audit_status'] = $retailer_apply_status[$row['is_audit']];
	        }
	        
	        $data['data'] = $list;
	        
	        return $data;
	        
        }
    }