<?php
namespace Home\Model;
use Think\Model;

    class CommonModel extends Model {
      
        public function genPlanOrderNo(){
            return 'PL'.date("YmdHis");
        }   
		public function genOrderNo($prefix = 'PR'){
            return $prefix . date("YmdHis");
        }
        public function getModelRow($model_name, $where){
        	return M($model_name)->where($where)->find();
        }
    	public function filterWhereByPriv($field, $where){
    		
    		if( empty($where) ){
    			$where = " 1 ";
    		}
            if( is_string($where) ){
            	if ( !session('is_admin') ) {
            		$where .= " and  ".$field."=".session('user_id')."";
            	}
            }else if( is_array($where) ){
            	
            	if ( !session('is_admin') ) {
            		$where[$field] = session('user_id');
            	}
            }
            
            return $where;
        }
    }