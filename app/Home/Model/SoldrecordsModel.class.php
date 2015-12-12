<?php
namespace Home\Model;
use Home\Model\CommonModel;

    class SoldrecordsModel extends CommonModel {
        // 定义自动验证
        protected $_validate    =   array(
            array('title','require','标题必须'),
            );
        // 定义自动完成
        protected $_auto    =   array(
            //array('add_time','time',1,'function'),
            );
            
    	public function getAlloGoodsAmount($where_arr=array()){
    		$where_arr = array_merge(array('is_delete'=>0),$where_arr);
    		
            $goods = $this->where($where_arr)->sum('amount');
            return $goods;
        }
    	public function getSoldNumber($where, $size, $time_before=0){
        	
        	$where_str = " is_delete=0 ";
        	
        	if ($time_before) {
        		$where_str .= " and add_time >= '".date( "Y-m-d H:i:s", strtotime($time_before) )."' ";
        	}
        	
        	if(isset($where['attr_id'])){
        		$where_str .= "attr_id=".(int)$where['attr_id'];
        	}
        	
    		if(isset($where['retailer2goods_id'])){
        		$where_str .= " and retailer2goods_id=".(int)$where['retailer2goods_id'];
        	}
        	
    		if(isset($where['retailer_id'])){
        		$where_str .= " and retailer_id=".(int)$where['retailer_id'];
        	}
        	
        	if(empty($size)) return 0;
        	
        	$data = $this->where($where_str)->sum($size);
        	//echo $data;exit;
			return intval($data); 
			
        }
        public function getSoldAmount($attr_id=0,$size=''){
        	if(empty($size)) return 0;
        	
        	$data = $this->where("attr_id=".$attr_id." and is_delete=0")->sum($size);
        	//echo $data;exit;
			return intval($data);      	
        }
        public function getSoldRecords($where){
        	$where['s.is_delete'] = 0;
        	$where = $this->filterWhereByPriv("r2g.retailer_id", $where);
        	
        	$field = "	s.*,
        				r2g.sku,r2g.orderno,
        				r2gr.brand,r2gr.lang,r2gr.color,
        				g.photo,g.title 
        				";
        	/**
        	$table = "think_soldrecords as s 
        				left join think_retailer r on s.retailer_id=r.id 
        				left join think_retailer2goods_attr rga on s.attr_id=rga.id 
        				";
        	/**/
        	$data = $this->table('think_soldrecords as s ')
        		->field($field)
        		->where($where)
        		->join('think_retailer2goods as r2g on r2g.id=s.retailer2goods_id', 'left')
        		->join('think_retailer2goods_attr as r2gr on r2gr.attr_id=s.attr_id', 'left')
        		->join('think_goods as g on g.id=r2g.goods_id', 'left')
        		->order('s.id desc')
        		->selectPage();
        	
        	$list = $data['data'];
        	//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($data);exit;
        	/**
        	foreach( $list as $key=>$val ){
        		$retailer2goods_id = $val['retailer2goods_id'];
        		$retailer_good = $this->getModelRow('retailer2goods', 'id='.$retailer2goods_id);
        		$goods_id = $retailer_good['goods_id'];
        		$good_info = $this->getModelRow('goods', 'id='.$goods_id);
        		
        		$list[$key]['orderno'] = $retailer_good['orderno'];
        		$list[$key]['sku'] = $good_info['sku'];
        		$list[$key]['brand'] = $good_info['brand'];
        		$list[$key]['photo'] = $good_info['photo'];
        		$list[$key]['color'] = $good_info['color'];
        		$list[$key]['title'] = $good_info['title'];
        	}
        	/**/
        	$data['data'] = $list;
        	//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($data);exit;
        	//echo '<pre>';print_r($data);exit;
			return $data;      		 		 
        }
    public function getSoldRecords222($retailer2goods_id=0){
        	$where = "is_delete=0 and retailer2goods_id=".$retailer2goods_id."";
        	$where = $this->filterWhereByPriv("retailer_id", $where);
        	/**
        	$field = "
        					r.retailer_name
							rga.brand,rga.lang,rga.color,
        					s.id ,s.add_time,s.num_xs,s.num_s,s.num_m,s.num_l,s.num_xl,s.num_xxl,s.amount as retailer_amount ";
        	
        	$table = "think_soldrecords as s 
        				left join think_retailer r on s.retailer_id=r.id 
        				left join think_retailer2goods_attr rga on s.attr_id=rga.id 
        				";
        	/**/
        	$data = $this->table('think_soldrecords')
        		->where($where)
        		->order('id desc')
        		->selectPage();
        		
        	$list = $data['data'];
        	
        	foreach( $list as $key=>$val ){
        		$retailer2goods_id = $val['retailer2goods_id'];
        		$retailer_good = $this->getModelRow('retailer2goods', 'id='.$retailer2goods_id);
        		$goods_id = $retailer_good['goods_id'];
        		$good_info = $this->getModelRow('goods', 'id='.$goods_id);
        		
        		$list[$key]['orderno'] = $retailer_good['orderno'];
        		$list[$key]['sku'] = $good_info['sku'];
        		$list[$key]['brand'] = $good_info['brand'];
        		$list[$key]['photo'] = $good_info['photo'];
        		$list[$key]['color'] = $good_info['color'];
        		$list[$key]['title'] = $good_info['title'];
        	}
        	
        	$data['data'] = $list;
        	//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($data);exit;
        	//echo '<pre>';print_r($data);exit;
			return $data;      		 		 
        }
    	public function getSoldRecords22($goods_id=0, $retailer_id=0){
        	$sql = "select g.sku,g.title,g.stock_total,g.stock_used,
        					r.retailer_name,sum(r2g.amount) as retailer_amount,
        					concat(round(sum(r2g.amount)/g.stock_total,2)*100,'%') as percent,
        					r2g.id,r2g.add_time 
        			from think_soldrecords as s  
        			left join think_goods g on s.goods_id=g.id 
        			left join think_retailer r on s.retailer_id=r.id 
        			where s.is_delete=0 and g.id=".$goods_id." and r.id=".$retailer_id." 
        			group by r.id 
        			order by r2g.id desc";
        	$data = $this->query($sql);
        
			return $data;      		 		 
        }
        
    }