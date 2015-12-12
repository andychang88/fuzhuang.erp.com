<?php
namespace Home\Model;
use Home\Model\CommonModel;

    class Retailer2goodsModel extends CommonModel {
        // 定义自动验证
        protected $_validate    =   array(
            array('title','require','标题必须'),
            );
        // 定义自动完成
        protected $_auto    =   array(
            array('add_time','time',1,'function'),
            );
        protected $_map = array(
			'photo' =>'bill_photo',
		);    
		public $photo_field = 'bill_photo';
		
		public function getRow($retailer2goods_id){
			return $this->where('id='.(int)$retailer2goods_id)->find();
		}
		
    	public function getAlloGoodsAmount($where_arr=array()){
    		$where_arr = array_merge(array('is_delete'=>0),$where_arr);
    		
            $goods = $this->where($where_arr)->sum('amount');
            return $goods;
        }

        
    	/**
         * 查看供应商的产品售出记录
         * @param unknown_type $retailer_id
         */
    	public function getRetailerSoldRecords($retailer_id='',$goods_id=''){
            $sql = "select g.sku,g.title,g.stock_total,g.stock_used,
        					r.retailer_name,
        					s.id,s.add_time 
        			from think_soldrecords as s  
        			left join think_goods g on s.goods_id=g.id 
        			left join think_retailer r on s.retailer_id=r.id 
        			where s.is_delete=0 and s.retailer_id=".$retailer_id." and s.goods_id=".$goods_id." 
        			order by s.id desc";
        	$data = $this->query($sql);
        	
        	return $data;
        }
        
        /**
         * 查看供应商的产品
         * @param unknown_type $retailer_id
         */
    	public function getRetailerGoods($retailer_id=0){
            $sql = "select g.sku,g.title,g.stock_total,g.stock_used,g.id as goods_id,
        					r.retailer_name,sum(r2g.amount) as retailer_amount,
        					r2g.id,r2g.add_time 
        			from think_retailer2goods as r2g 
        			left join think_goods g on r2g.goods_id=g.id 
        			left join think_retailer r on r2g.retailer_id=r.id 
        			where r2g.is_delete=0 and r.id=".$retailer_id." 
        			group by r2g.goods_id  
        			order by r2g.id desc";
        	$data = $this->query($sql);
        	
        	return $data;
        }
        /**
         * 查看供应商的产品
         * @param unknown_type $retailer_id
         */
    	public function getRetailerGoods22($where_arr=array('retailer_id'=>0,'retailer2goods_id'=>'0','goods_id'=>'')){
			$where = "r2g.is_delete=0 ";
			
    		//非管理只能查看自己的，管理员可根据条件筛选
    		$where = $this->filterWhereByPriv("r2g.retailer_id", $where);
			
    		if(!empty($where_arr['retailer_id'])){
				$where .= " and r2g.retailer_id=".$where_arr['retailer_id'];
			}
			
			if(!empty($where_arr['retailer2goods_id'])){
				$where .= " and r2g.id=".$where_arr['retailer2goods_id'];
			}
			if(!empty($where_arr['goods_id'])){
				$where .= " and r2g.goods_id=".$where_arr['goods_id'];
			}
			if(!empty($where_arr['orderno'])){
				$where .= " and r2g.orderno='".$where_arr['orderno']."'";
			}
			if(!empty($where_arr['bill_status'])){
				$where .= " and r2g.bill_status=".$where_arr['bill_status'];
			}
			

			$fields = "g.sku,g.title,g.stock_total,g.stock_used,g.id as goods_id,g.photo,g.brand,g.lang,g.color,g.size,
        					r.retailer_name,(r2g.amount) as retailer_amount,
        					r2g.*";
			
		
			$data = $this
				->field($fields)
				->table("think_retailer2goods  as r2g")
				->join("think_goods g on r2g.goods_id=g.id","LEFT")
				->join("think_retailer r on r2g.retailer_id=r.id","LEFT")
				->where($where)
				//->group("r2g.goods_id")
				->order("r2g.id desc")
				->selectPage();
				
			//echo $this->getLastSql();exit;	
			
           
        	if(!empty($data)){
        		$list = $data['data'];
				foreach($list as $key=>$val){
					$name = '';
					switch ($val['bill_status']){
						case 1 : $name = '待付款';break;
						case 2 : $name = '付款待审核';break;
						case 3 : $name = '已付款';break;
						case 4 : $name = '已废弃';break;
					}
					
					$list[$key]['bill_status_name'] = $name;
					//the number already sold
					$list[$key]['sold_amount'] = D('Soldrecords')->getSoldAmount($val['goods_id'], $val['retailer_id']);
					
					//the toal amount should paid
					$list[$key]['total_amount'] = $this->getTotalAmountShouldPaid($val['id'], $val['price']);
					//the amount already paid
					$list[$key]['bill_amount'] = $this->getTotalAmountAlreadyPaid($val['id']);
					
					$list[$key]['plans_attr'] = $this->getRetailerGoodsAttr($val['id']);
				}
				$data['data'] = $list;
			}
			
        	return $data;
        }
        
        public function getTotalAmountShouldPaid($retailer2goods_id, $price = 0){
        	
        	if (empty($price)) {
        		$price = $this->where('id='.$retailer2goods_id)->getField('price');
        	}
        	
        	$sql = "SELECT SUM(num_xs)+sum(num_s)+sum(num_m)+sum(num_l)+sum(num_xl)+sum(num_xxl) AS tp_sum 
        	FROM `think_retailer2goods_attr` 
        	WHERE retailer2goods_id=".$retailer2goods_id;
        	
        	$result = M('retailer2goods_attr')->query($sql);
        	if (empty($result)) {
        		return 0;
        	} else {
        		return round($result[0]['tp_sum'] * $price, 2);
        	}
        	
        }
        
    	public function getTotalAmountAlreadyPaid($retailer2goods_id){
        	return M('retailerpaymenthistory')->where('retailer2goods_id='.$retailer2goods_id.' and audit_status = 1 and is_delete=0')->sum('paid_amount');
        }
        public function getOrderNo($retailer2goods_id){
        	return $this->where('id='.(int)$retailer2goods_id.' and is_delete=0')->getField('orderno');
        }
        /**
         * 查看一个产品的供应商的分配情况
         * @param unknown_type $goods_id
         */
        public function getGoodsRetailer($where=array()){

			$where = array_merge(array('r2g.is_delete'=>0),$where);

			if(!empty($arr['goods_id'])){
				$where['g.id'] = $arr['goods_id'];
			}
			if(!empty($arr['retailer_id'])){
				$where['r2g.retailer_id'] = $arr['retailer_id'];
			}
			
			$fields = "g.sku,g.title,g.stock_total,g.stock_used,g.id as goods_id,g.photo,g.brand,g.color,g.size,  
        					r.retailer_name,r.id as retailer_id, (r2g.amount) as retailer_amount,
        					concat(round((r2g.amount)/g.stock_total,2)*100,'%') as percent,
        					r2g.* ";
			
			$data = $this->table("think_retailer2goods  as r2g")
				->field($fields)
				->join("think_goods g on r2g.goods_id=g.id","LEFT")
				->join("think_retailer r on r2g.retailer_id=r.id","LEFT")
				->where($where)
				//->group("r2g.retailer_id")
				->order("r2g.id desc")
				//->limit($p->firstRow.','.$p->listRows)
				->selectPage();
				
				
				//echo M()->_sql();
				//exit;
           
			$list = $data['data'];
			
        	if(!empty($list)){
				foreach($list as $key=>$val){
					$name = '';
					switch ($val['bill_status']){
						case 1 : $name = '待付款';break;
						case 2 : $name = '付款待审核';break;
						case 3 : $name = '已付款';break;
						case 4 : $name = '已废弃';break;
					}
					
					$list[$key]['bill_status_name'] = $name;
					$list[$key]['sold_amount'] = D('Soldrecords')->getSoldAmount($val['goods_id'],$val['retailer_id']);
				}
			}

			$data['data'] = $list;
			
        	return $data;

		}
		//某一个产品产品某型号，已经分配了多少个出去
		public function getAllocatedCount($goods_id, $attr_id, $size){
			if(empty($size)) return 0;

			if(strpos($size,'num_') === false){
				$size = 'num_'.strtolower($size);
			}

			$num = M('retailer2goods_attr a')
				->join('think_retailer2goods g on a.retailer2goods_id = g.id ','left')
				->where('a.goods_id='.$goods_id.'  and a.attr_id = '.$attr_id.' 
						and g.bill_status !=4 and a.is_delete=0 and g.is_delete=0')
				->sum('a.'.$size);//echo M()->_sql();exit;
			return intval($num);

		}
		
		public function getRetailerGoodsAttr($retailer2goods_id){
			$retailer2goods_id = (int)$retailer2goods_id;
			$where_history = array('is_delete'=>0, 'retailer2goods_id'=>$retailer2goods_id);
			$plans_attr = M('retailer2goods_attr')
				->field('*')
				->where($where_history)->select();
				
			$retailer_id = M('retailer2goods')->where('id='.$retailer2goods_id)->getField('retailer_id');

			

			foreach($plans_attr as $key=>$attr){

				$plans_attr[$key]['num_xs'] = $attr['num_xs']?$attr['num_xs']:'';
				$plans_attr[$key]['num_s'] = $attr['num_s']?$attr['num_s']:'';
				$plans_attr[$key]['num_m'] = $attr['num_m']?$attr['num_m']:'';
				$plans_attr[$key]['num_l'] = $attr['num_l']?$attr['num_l']:'';
				$plans_attr[$key]['num_xl'] = $attr['num_xl']?$attr['num_xl']:'';
				$plans_attr[$key]['num_xxl'] = $attr['num_xxl']?$attr['num_xxl']:'';
				
				$goods_id = $attr['goods_id'];

				//已经售出去的数量
				$tmp_where = array(
									'retailer2goods_id'=>$retailer2goods_id);
				
				$plans_attr[$key]['num_xs_sold'] = D('soldrecords')->getSoldNumber($tmp_where,'num_xs');
				$plans_attr[$key]['num_s_sold'] = D('soldrecords')->getSoldNumber($tmp_where,'num_s');
				$plans_attr[$key]['num_m_sold'] = D('soldrecords')->getSoldNumber($tmp_where,'num_m');
				$plans_attr[$key]['num_l_sold'] = D('soldrecords')->getSoldNumber($tmp_where,'num_l');
				$plans_attr[$key]['num_xl_sold'] = D('soldrecords')->getSoldNumber($tmp_where,'num_xl');
				$plans_attr[$key]['num_xxl_sold'] = D('soldrecords')->getSoldNumber($tmp_where,'num_xxl');

			}

			return $plans_attr;
		}
		
        
    }