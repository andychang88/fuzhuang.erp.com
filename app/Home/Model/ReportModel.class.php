<?php
namespace Home\Model;
use Home\Model\CommonModel;

    class ReportModel extends CommonModel {
        // 定义自动验证
        protected $_validate    =   array(
            array('title','require','标题必须'),
            );
        // 定义自动完成
        protected $_auto    =   array(
            //array('add_time','time',1,'function'),
            );
            
        protected $autoCheckFields = false;
        
    	public function salesNumByBrand($from_date, $end_date){
    		$where_arr = array_merge(array('is_delete'=>0),$where_arr);
    		
    		$end_date = $end_date.' 23:59:59';
    		
    		$sql = "select  sum(IFNULL(s.num_s,0)) as num_s, sum(IFNULL(s.num_xs,0)) as num_xs, sum(IFNULL(s.num_m,0)) as num_m, 
    						sum(IFNULL(s.num_l,0)) as num_l, sum(IFNULL(s.num_xl,0)) as num_xl, sum(IFNULL(s.num_xxl,0)) as num_xxl,
    						r2ga.brand as brand,s.id   
    				from think_soldrecords s 
    				left join think_retailer2goods r2g on s.retailer2goods_id=r2g.id 
    				left join think_retailer2goods_attr r2ga on s.attr_id=r2ga.attr_id 
    				where s.add_time >= '$from_date' and s.add_time <= '$end_date' and s.is_delete=0 
    				group by r2ga.brand 
    				limit 10 
    				";
    		
            $result = $this->query($sql);
            
            //echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($result);exit;
            return $result;
        }   
        
        public function salesAmountByBrand($from_date, $end_date){
    		$where_arr = array_merge(array('is_delete'=>0),$where_arr);
    		
    		$sql = "select sum(IFNULL(s.num_s,0) * IFNULL(r2g.price,0)) as num_s, sum(IFNULL(s.num_xs,0) * IFNULL(r2g.price,0)) as num_xs, sum(IFNULL(s.num_m,0) * IFNULL(r2g.price,0)) as num_m, 
    						sum(IFNULL(s.num_l,0) * IFNULL(r2g.price,0)) as num_l, sum(IFNULL(s.num_xl,0) * IFNULL(r2g.price,0)) as num_xl, sum(IFNULL(s.num_xxl,0) * IFNULL(r2g.price,0))  as num_xxl,
    						r2ga.brand as brand    
    				from think_soldrecords s 
    				left join think_retailer2goods r2g on s.retailer2goods_id=r2g.id 
    				left join think_retailer2goods_attr r2ga on s.attr_id=r2ga.id 
    				where s.add_time >= '$from_date' and s.add_time <= '$end_date' and s.is_delete=0 
    				group by r2ga.brand 
    				limit 10 
    				";
    		
            $result = $this->query($sql);
            
            
            return $result;
        }  
        
        public function getRetailerRanks($from_date, $end_date){
    		$where_arr = array_merge(array('is_delete'=>0),$where_arr);
    		
    		$sql = "select  ( sum(IFNULL(s.num_s,0) * IFNULL(r2g.price,0)) + sum(IFNULL(s.num_xs,0) * IFNULL(r2g.price,0)) + sum(IFNULL(s.num_m,0) * IFNULL(r2g.price,0)) +  
    						sum(IFNULL(s.num_l,0) * IFNULL(r2g.price,0)) + sum(IFNULL(s.num_xl,0) * IFNULL(r2g.price,0)) + sum(IFNULL(s.num_xxl,0) * IFNULL(r2g.price,0)) )  as sales,
    						r.retailer_name as retailer_name, r.id as retailer_id      
    				from think_soldrecords s 
    				left join think_retailer r on r.id=s.retailer_id 
    				left join think_retailer2goods r2g on s.retailer2goods_id=r2g.id 
    				left join think_retailer2goods_attr r2ga on s.attr_id=r2ga.id 
    				where s.add_time >= '$from_date' and s.add_time <= '$end_date' and s.is_delete=0 
    				group by s.retailer_id  
    				order by sales desc 
    				limit 30 
    				";
    		
            $result = $this->query($sql);
            
            
            return $result;
        }  
        
        
    }