<?php
namespace Home\Model;
use Think\Model;

    class FinanceModel extends Model {
        // 定义自动验证
        protected $_validate    =   array(
            array('title','require','标题必须'),
            );
        // 定义自动完成
        protected $_auto    =   array(
            array('add_time','time',1,'function'),
            array('produce_time','strtotime',3,'function'),
            );
            
        public function getRetailers($where_arr=array()){
            $retailers = $this->where($where_arr)->select();
            return $retailers;
        }
    }