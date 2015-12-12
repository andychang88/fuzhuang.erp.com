<?php
namespace Home\Controller;
use Home\Controller\CommonController;

class GoodsController extends CommonController {
	
	public $controll_keywords = "Goods";
	public function __construct(){
		parent::__construct();
	}
    public function index(){
    	
    	$this->title = '产品列表';
    	
    	$Data = D($this->controll_keywords);
    	
    	$where = $this->collectForm();
    	
		$list = $Data->getGoods($where);

    	$this->page = $list['page_html'];
        $this->data = $list['data'];
        
        $this->display();
    }
	public function collectForm(){
		$where = array();
		$bill_status = I('get.status');
		$orderno = I('get.orderno');
		$retailer_id = I('get.retailer_id');
		
		if(!empty($bill_status)){
			$where['bill_status'] = $bill_status;
		}

		if(!empty($orderno)){
			$where['orderno'] = $orderno;
		}

		if(!empty($retailer_id)){
			$where['retailer_id'] = $retailer_id;
		}
		return $where;
	}
	public function add($id=0){
    	//编辑一个计划单
		if ($id) {
			$Data = D($this->controll_keywords);
			$list = $Data->getGoods(array('id'=>$id));
	
	        $this->vo = $list['data'][0];
		}
		
		//编辑提交或者添加一个产品
		if (IS_POST) {
			$this -> insertGood();
		}
		
        $this->display('add');
    }
    
	
	/**
	 * 创建一个新产品（正常情况下，不应该使用该方法。产品有生产计划单转入的。）
	 * Enter description here ...
	 */
	public function insertGood(){
		$id = I('post.id');
		$data['sku'] = I('post.sku');
		$data['title'] = I('post.title');
		$data['total_cost'] = I('post.total_cost');
		$data['produce_time'] = I('post.produce_time')?strtotime(I('post.produce_time')):'';
		$data['memo'] = I('post.memo');
		$data['is_audit'] = 0;
		$data['operator_id'] = session('user_id');
		$data['add_time'] = time();

		
//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r(I('post.'));exit;
		$photo_url   =   $this->upload();
        if(!empty($photo_url)){
        		$data['photo'] = $photo_url;
        }
		
        $modelName 		= 'Goods';
        $modelAttrName  = 'Goods_attr';
        $attr2modelId = 'goods_id';
        
		$model 		= D($modelName);
		$modelAttr = M($modelAttrName);

		//添加生产计划单
		if(empty($id)){
			$data['orderno'] = $model -> genOrderNo('GD');
		}
		

		$model -> startTrans(); 

		if (!$model->create($data)){
			$model->rollback();
			$error = $model->getError();
			$this->error($error);
		}

		if(!empty($id)){//保存主数据
			$model->where('id='.$id)->save(); 
			$mainId = $id; 
		}else{
			
			$mainId = $model->add(); 
		}
		

		$data_attr = array();
		$data_attr_ids_arr = I('post.attr_ids');
		$data_attr_ids_rm_arr = I('post.attr_ids_rm');
		$data_attr_color_arr = I('post.color');
		$data_attr_brand_arr = I('post.brand');
		$data_attr_lang_arr = I('post.lang');
		$data_attr_memo_attr_arr = I('post.memo_attr');
		
		$data_attr_num_XS = I('post.num_xs');
		$data_attr_num_S = I('post.num_s');
		$data_attr_num_M = I('post.num_m');
		$data_attr_num_L = I('post.num_l');
		$data_attr_num_XL = I('post.num_xl');
		$data_attr_num_XXL = I('post.num_xxl');

		
		foreach($data_attr_color_arr as $key=>$val){
			$data_attr = array();
			$data_attr[$attr2modelId] = $mainId;
			$data_attr['brand'] = $data_attr_brand_arr[$key];
			$data_attr['color'] = $data_attr_color_arr[$key];
			$data_attr['lang'] = $data_attr_lang_arr[$key];
			$data_attr['memo_attr'] = $data_attr_memo_attr_arr[$key];

			$data_attr['num_xs'] = $data_attr_num_XS[$key];
			$data_attr['num_s'] = $data_attr_num_S[$key];
			$data_attr['num_m'] = $data_attr_num_M[$key];
			$data_attr['num_l'] = $data_attr_num_L[$key];
			$data_attr['num_xl'] = $data_attr_num_XL[$key];
			$data_attr['num_xxl'] = $data_attr_num_XXL[$key];


			$data_attr['add_time'] = time();

			if (!$modelAttr->create($data_attr)){
						$model->rollback();
						$error = $modelAttr->getError();
						$this->error($error);
			}

			if(!empty($data_attr_ids_arr[$key])){
				$modelAttr->where('id='.$data_attr_ids_arr[$key])->save(); 
			}else{
				$modelAttr->add(); 
			}


		}

		//delete the specify attributes
		if (!empty($data_attr_ids_rm_arr)){
			foreach ($data_attr_ids_rm_arr as $key=>$val){
				if(empty($val)){
					continue;
				}
				$modelAttr->where('id='.$val)->data(array('is_delete'=>1))->save(); 
			}
		}
				
		$model->commit();

		$this->success('操作成功！');



		//echo '<pre>';
		//print_r($_POST);
		//exit;
	}
	
	
    
	public function doAllo($goods_id=''){
		
		if(IS_POST){
			$this->doAlloInsert();
		}
		$data = D('Goods')->getGoods(array('id'=>$goods_id));
		$this->vo = $data['data'][0];
		$this->display();
		
	}
	
	
	//分配产品给供应商
	private function doAlloInsert(){

		$data['goods_id'] = I('post.goods_id');
		$data['sku'] = I('post.sku');
		$data['retailer_id'] = I('post.retailer_id');
		$data['total_amount'] = I('post.total_amount');
		$data['price'] = I('post.price');
		$data['operator_id'] = session('user_id');
		

		$attr_id_arr = I('post.attr_id');
		$allo_amount = I('post.allo_amount');
		$memo_arr = I('post.memo');

		//print_r($data);echo $allo_amount;exit;
		if(empty($allo_amount) || empty($data['retailer_id']) || empty($data['price']) ){
			$this->error('错误：请选择分销商、产品价格、数量！');
		}

		$Retailer2goods   =   D('Retailer2goods');
		
		$data['orderno'] = $Retailer2goods->genOrderNo('GD');

		$Retailer2goods->startTrans(); 


		//插入分销商产品主数据
        if(!$Retailer2goods->create($data)) {
			$Retailer2goods->rollback(); 
            $this->error($Retailer2goods->getError());
        }
		
		$retailer2goods_id 	 =   $Retailer2goods->add();

        if(!$retailer2goods_id) {
            	$this->error('分销商产品主数据写入错误！');
        }

		$size_arr = array('xs','s','m','l','xl','xxl');

	//print_r($attr_id_arr);exit;
		foreach($attr_id_arr as $attr_id_key=>$attr_id){

			//插入分销商产品属性数据
			$data_attr = array();

			if(!empty($allo_amount[$attr_id_key + 1])){
				foreach($size_arr as $size){
					if(empty($allo_amount[$attr_id_key + 1][$size])){
						continue;
					}
					$field = 'num_'.$size;
					$data_attr[$field] = intval($allo_amount[$attr_id_key + 1][$size]);
				}
			}

			if(empty($data_attr)){
				continue;
			}

			

			$goods_attr = D('goods')->getGoodsAttr(array('id'=>$attr_id));

			$data_attr['retailer2goods_id'] = $retailer2goods_id;
			$data_attr['goods_id'] = I('post.goods_id');
			$data_attr['retailer_id'] = I('post.retailer_id');
			$data_attr['brand'] = $goods_attr[0]['brand'];
			$data_attr['attr_id'] = $attr_id;
			$data_attr['lang'] = $goods_attr[0]['lang'];
			$data_attr['color'] = $goods_attr[0]['color'];
			$data_attr['memo_attr'] = $memo_arr[$attr_id_key + 1];

			$retailer2goods_attr = M('retailer2goods_attr');
//print_r($data_attr);exit;
			//插入分销商产品属性数据
			if(!$retailer2goods_attr->create($data_attr)) {
				$Retailer2goods->rollback(); 
				$this->error($retailer2goods_attr->getError());
				
			}

			$retailer2goods_attr->add();
		}


		$Retailer2goods->commit();
		$this->success('分配成功！');



	}
	
	public function alloDetail(){
		
		$this->page_title = '分配详情列表';
		$Data = D('Retailer2goods');
		
		$where = $this -> collectAlloDetailForm();
		
//print_r($where);exit;
        $data = $Data->getRetailerGoods22($where);
       
        $this->assign($data);
        
        $this->retailer_products_status_selected = $where['bill_status'];
       
        
        $this->display();
		//echo '<pre>';print_r($data);exit;
		
	}
	public function collectAlloDetailForm(){
		$where = array();
		$bill_status = I('get.bill_status');
		$orderno = I('get.orderno');
		$retailer_id = I('get.retailer_id');
		
		if(!empty($bill_status)){
			$where['bill_status'] = $bill_status;
		}

		if(!empty($orderno)){
			$where['orderno'] = $orderno;
		}

		if(!empty($retailer_id)){
			$where['retailer_id'] = $retailer_id;
		}
		return $where;
	}
	
	public function alert(){
		$goods_balance = $this->model->getStockSellBalance();
		$this->data = $goods_balance;
		
		//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($goods_balance);exit;
		$this->display();
	}
	
}