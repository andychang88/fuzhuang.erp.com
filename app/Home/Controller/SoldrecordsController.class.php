<?php
namespace Home\Controller;
use Home\Controller\CommonController;

class SoldrecordsController extends CommonController {
	
	public $controll_keywords = "Soldrecords";
	public function __construct(){
		parent::__construct();
	}
    public function index($retailer2goods_id=0){
    	
    	$this->page_title = 'Sku:'.$sku.'的售出记录';
    	
    	$where = $this->collectForm();
    	
    	$Data = D($this->controll_keywords);
        $soldrecords = $Data->getSoldRecords($where);
        $this->assign($soldrecords);
        //echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($this->data);exit;
        $this->display();
    }
	public function collectForm(){
		$where = array();
		$retailer2goods_id = I('get.retailer2goods_id');
		$retailer_id = I('get.retailer_id');
		$starttime = strtotime(I('get.starttime'));
		$endtime = strtotime(I('get.endtime'));
		$sku = I('get.sku');
		$orderno = I('get.orderno');
		
		if(!empty($retailer2goods_id)){
			$where['s.retailer2goods_id'] = $retailer2goods_id;
		}
		if(!empty($retailer_id)){
			$where['r2g.retailer_id'] = $retailer_id;
		}
		if(!empty($sku)){
			$where['r2g.sku'] = $sku;
		}
		if(!empty($orderno)){
			$where['r2g.orderno'] = $orderno;
		}
		
		if(!empty($starttime)){
			$where['s.add_time'] = array('gt', ($starttime) );
		}
		
		if(!empty($endtime)){
			if( $where['s.add_time'] ){
				$where['s.add_time'] = array(array('gt', ($starttime) ), array('lt', ($endtime) ));
			} else {
				$where['s.add_time'] = array('lt', ($endtime) );
			}
			
		}
		
		
		return $where;
	}

	public function doAllo($goods_id=''){
		$this->retailers = D('Retailer')->getRetailers();
		$this->good = D('Goods')->getGoods(array('id'=>$goods_id));
		
		$this->display();
		
	}
	public function doAlloInsert($goods_id){
		$Form   =   D('Retailer2goods');
		
        if($Form->create()) {
            $result =   $Form->add();
            if($result) {
            	
            	//统计该sku的分配总数量
            	$amount = $Form->getAlloGoodsAmount(array('goods_id'=>$goods_id));
            	
            	//更新sku的分配总数量
            	D('Goods')->updateGoods(array('stock_used'=>$amount,'id'=>$goods_id));
            	
                $this->success('操作成功！');
            }else{
                $this->error('写入错误！');
            }
        }else{
            $this->error($Form->getError());
        }
	}
	
	public function alloDetail($goods_id=0,$retailer_id=0){
		$this->data   =   D('Retailer2goods')->getGoodsRetailer($goods_id, $retailer_id);
		$this->display();
		//echo '<pre>';print_r($data);exit;
		
	}
	public function soldDetail($goods_id='', $retailter_id=''){
		
	}
	
public function add($retailer2goods_id = 0, $sku = '' ){
    	
		if ( IS_POST ) {
			$this->insertSoldRecord();
			exit;
		}
		
		if( empty($retailer2goods_id)){
			$this->error("错误：分销商产品不存在！");
			exit;
		}
    	$this->page_title = '添加售出产品';
		
		$Data = D('Retailer2goods');
        $data = $Data->getRetailerGoods22(array('retailer_id'=>0,'retailer2goods_id'=>$retailer2goods_id));
        $list = $data['data'][0];
		
		$this->vo = $list;
		
        $this->display();
    }

	private function insertSoldRecord(){
		
		$retailer2goods_id = I('post.retailer2goods_id');
		$amount_ready = I('post.amount_ready');
		$attr_id_arr = I('post.attr_id');
		$memo_arr = I('post.memo');
		$retailer_id = I('post.retailer_id');

		$size_arr = array('xs','s','m','l','xl','xxl');
		

		$Soldrecords   =   M('Soldrecords');
		$Soldrecords->startTrans(); 

		foreach($attr_id_arr as $attr_id_key=>$attr_id){

			$data_attr = array();

			foreach($size_arr as $size){
				if(empty($amount_ready[$attr_id_key + 1][$size])){
					continue;
				}
				$field = 'num_'.$size;
				$data_attr[$field] = intval($amount_ready[$attr_id_key + 1][$size]);


			}
			if(empty($data_attr)){
				continue;
			}

			$data_attr['retailer_id'] = $retailer_id;
			$data_attr['retailer2goods_id'] = $retailer2goods_id;
			$data_attr['attr_id'] = $attr_id;
			$data_attr['memo'] = $memo_arr[$attr_id_key+1];
			

			//插入分销商卖出产品数据
			if(!$Soldrecords->create($data_attr)) {
				$Soldrecords->rollback(); 
				$this->error($Soldrecords->getError());
			}
			
			$Soldrecords_id 	 =   $Soldrecords->add();

			if(!$Soldrecords_id) {
					$Soldrecords->rollback(); 
					$this->error('添加售出记录写入错误！');
			}

		}
		
		$Soldrecords->commit();
		$this->success('添加售出记录写入成功！');

	}
	
	
	
	
}