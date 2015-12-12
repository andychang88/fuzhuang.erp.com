<?php
namespace Home\Model;
use Home\Model\CommonModel;

    class GoodsModel extends CommonModel {
        
    	public function getGoods($where=array()){
            $where = array_merge(array('is_delete'=>0,),$where);
			if(!session('is_admin')){
				$where['user_id'] = session('user_id');
			}
			
			$data = $this->order('id desc')->where($where)->selectPage();
			$list = $data['data'];
			
			foreach ($list as $key=>$row){
				$list[$key]['operator_name'] = D('Retailer')->where("id=".intval($row['operator_id']))->getField("retailer_name");
				$list[$key]['plans_attr'] = $this->getGoodsAttr(array('goods_id'=>$row['id']));
			}
			
			$data['data'] = $list;

			return $data;
        }
        
    	public function getGoodsAttr($where_history){
			$where_history = array_merge(array('is_delete'=>0),$where_history);
			$plans_attr = M('goods_attr')->where($where_history)->select();

			

			foreach($plans_attr as $key=>$attr){

				$goods_id = $attr['goods_id'];
				$attr_id = $attr['id'];

				$plans_attr[$key]['num_xs'] = $attr['num_xs']?$attr['num_xs']:'';
				$plans_attr[$key]['num_s'] = $attr['num_s']?$attr['num_s']:'';
				$plans_attr[$key]['num_m'] = $attr['num_m']?$attr['num_m']:'';
				$plans_attr[$key]['num_l'] = $attr['num_l']?$attr['num_l']:'';
				$plans_attr[$key]['num_xl'] = $attr['num_xl']?$attr['num_xl']:'';
				$plans_attr[$key]['num_xxl'] = $attr['num_xxl']?$attr['num_xxl']:'';

				//�Ѿ������ȥ������
				$plans_attr[$key]['num_xs_allocated'] = D('retailer2goods')->getAllocatedCount($goods_id,$attr_id,'num_xs');
				$plans_attr[$key]['num_s_allocated'] = D('retailer2goods')->getAllocatedCount($goods_id,$attr_id,'num_s');
				$plans_attr[$key]['num_m_allocated'] = D('retailer2goods')->getAllocatedCount($goods_id,$attr_id,'num_m');
				$plans_attr[$key]['num_l_allocated'] = D('retailer2goods')->getAllocatedCount($goods_id,$attr_id,'num_l');
				$plans_attr[$key]['num_xl_allocated'] = D('retailer2goods')->getAllocatedCount($goods_id,$attr_id,'num_xl');
				$plans_attr[$key]['num_xxl_allocated'] = D('retailer2goods')->getAllocatedCount($goods_id,$attr_id,'num_xxl');
			}

			return $plans_attr;
		}
    	public function updateGoods($data=array()){
            $goods = $this->save($data);
            return $goods;
        }
        public function getStockSellBalance($data=array()){
        	$size_arr = array('num_xs', 'num_s', 'num_m', 'num_l', 'num_xl', 'num_s', 'num_xxl');
        	$sql = "SELECT * 
        			FROM `think_goods_attr` as ga where ga.is_delete=0 and goods_id=201
					group by brand, lang, color, ".implode(', ', $size_arr);
        	$result = $this->query($sql);
        	
        	foreach ($result as $key=>$row){
        		
        		$sold_info = array();
        		
        		foreach ($size_arr as $s){
        			$totalSold = $this->getSoldNumberByAttr($row, $s);//分销商卖出去的总数
        			$totalAllo = $this->getRetailerAllocatedNumber($row, $s);//分配给分销商的总数
        			$totalNumberInStock = $this->getNumberInStock($row, $s);//仓库中的总数
        			$totalNumberInProduction = $this->getNumberInProduction($row, $s);//正在生产数量
        			$remainedOfRetailer = $totalAllo - $sold;//分销商目前持有的总数
        			$remainedOfStock = $totalNumberInStock - $totalAllo;//仓库目前还剩下的数目
        			
        			$sale7days = $this->getSoldNumberByAttr($row, $s, '-7 days');
        			$sale15days = $this->getSoldNumberByAttr($row, $s, '-15 days');
        			$sale3days = $this->getSoldNumberByAttr($row, $s, '-3 days');
        			
        			$tmp = array();
        			$tmp['totalSold'] = $totalSold;
        			$tmp['totalAllo'] = $totalAllo;
        			$tmp['totalNumberInStock'] = $totalNumberInStock;
        			$tmp['totalNumberInProduction'] = $totalNumberInProduction;
        			$tmp['remainedOfRetailer'] = $remainedOfRetailer;
        			$tmp['remainedOfStock'] = $remainedOfStock;
        			
        			$tmp['sale7days'] = round($sale7days / 7, 2);
        			$tmp['sale15days'] = round($sale15days / 15, 2);
        			$tmp['sale3days'] = round($sale3days / 3, 2);
        			
        			$tmp['isAlert'] = 0;
        			
        			if ($remainedOfStock < $sale7days){
        				$tmp['isAlert'] = 1;
        			}
        			
        			$sold_info[strtoupper(str_replace('num_', '', $s))] = $tmp;
        		}
        		
        		$result[$key]['sold_info'] = $sold_info;
        		
        	}
        	
        	return $result;
        }
        
        public function getNumberInProduction($row, $size){
        	$sql = "SELECT sum(IFNULL(ga.".$size.",0)) as cnt 
        	FROM  think_plans_attr ga  
        	 where  
        	  ga.brand='".$row['brand']."' and 
        	  ga.lang='".$row['lang']."' and 
        	  ga.color='".$row['color']."' and 
        	  ga.brand='".$row['brand']."' and 
        	  
        	   ga.is_delete=0 
					 ";
        	$result = $this->query($sql);
        	
        	if (empty($result)){
        		return '0';
        	}
        	
        	return (int)$result[0]['cnt'];
        }
        public function getNumberInStock($row, $size){
        	$sql = "SELECT sum(IFNULL(ga.".$size.",0)) as cnt 
        	FROM  think_goods_attr ga  
        	 where  
        	  ga.brand='".$row['brand']."' and 
        	  ga.lang='".$row['lang']."' and 
        	  ga.color='".$row['color']."' and 
        	  ga.brand='".$row['brand']."' and 
        	  
        	   ga.is_delete=0 
					 ";
        	$result = $this->query($sql);
        	
        	if (empty($result)){
        		return '0';
        	}
        	
        	return (int)$result[0]['cnt'];
        }
        
        public function getSoldNumberByAttr($row, $size, $time_before=0){
        	
        	$when_time = "";
        	
        	if ($time_before) {
        		$when_time = " and s.add_time >= '".date( "Y-m-d H:i:s", strtotime($time_before) )."' ";
        	}
        	
        	$sql = "SELECT sum(IFNULL(s.".$size.",0)) as cnt 
        	FROM `think_soldrecords` as s 
        	left join think_retailer2goods_attr r2ga on r2ga.attr_id=s.attr_id  
        	 where 
        	  r2ga.brand='".$row['brand']."' and 
        	  r2ga.lang='".$row['lang']."' and 
        	  r2ga.color='".$row['color']."' and 
        	  r2ga.brand='".$row['brand']."' and 
        	  
        	  s.is_delete=0 and r2ga.is_delete=0 
					 ".$when_time;
        	$result = $this->query($sql);
        	
        	if (empty($result)){
        		return '0';
        	}
        	
        	return (int)$result[0]['cnt'];
        }
        
        public function getRetailerAllocatedNumber($row, $size){
        	$sql = "SELECT sum(IFNULL(r2ga.".$size.",0)) as cnt 
        	FROM  think_retailer2goods_attr r2ga 
        	 where  
        	  r2ga.brand='".$row['brand']."' and 
        	  r2ga.lang='".$row['lang']."' and 
        	  r2ga.color='".$row['color']."' and 
        	  r2ga.brand='".$row['brand']."' and 
        	  
        	   r2ga.is_delete=0 
					 ";
        	$result = $this->query($sql);
        	
        	if (empty($result)){
        		return '0';
        	}
        	
        	return (int)$result[0]['cnt'];
        }
    }