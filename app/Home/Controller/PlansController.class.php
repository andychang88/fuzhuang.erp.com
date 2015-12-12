<?php
namespace Home\Controller;
use Home\Controller\CommonController;

class PlansController extends CommonController {
	
	public $controll_keywords = "Plans";
	
	public function __construct(){
		parent::__construct();
		$this->title = '供应商付款待审核列表';
	}
	public function collectForm(){
		$where = array();
		$bill_status = I('post.status');
		$orderno = I('post.orderno');
		$retailer_id = I('post.retailer_id');
		
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
	
    public function index(){
    	
    	$Data = D($this->controll_keywords);

    	$where = $this->collectForm();
    	
		$list = $Data->getPlans($where);

		$this->page = $list['page_html'];
        $this->data = $list['data'];
        
        $this->display();
    }
    
	public function add($id=0){
		//编辑一个计划单
		if ($id) {
			$Data = D($this->controll_keywords);
			$list = $Data->getPlans(array('id'=>$id));
	
	        $this->vo = $list['data'][0];
		}
		
		//编辑提交或者添加一个计划单
		if (IS_POST) {
			$this -> insertPlan();
		}
		//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($this->vo);exit;
        $this->display();
    }

	public function edit($id){
		
        //print_r($list[0]);exit;
		//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($list);exit;
        $this->display();
	}
	public function purchase_finish($id=0){
		$Data = D($this->controll_keywords);

		$purchase_finish_time = I('post.purchase_finish_time');
		if(!empty($purchase_finish_time)){
			$id = I('post.id');
			$Data->where('id='.$id)
				->data(array(
				'purchase_finish_time'=>strtotime($purchase_finish_time),
				'is_audit'=>1))
				->save();
			$this->success('操作成功！');
			exit;
		}

		$list = $Data->getPlans(array('id'=>$id));

        $this->vo = $list[0];
        //print_r($list[0]);exit;
		
        $this->display();
	}

	private function insertPlan(){
		$id = I('post.id');
		$data['sku'] = I('post.sku');
		$data['title'] = I('post.title');
		$data['total_cost'] = I('post.total_cost');
		$data['produce_time'] = I('post.produce_time')?strtotime(I('post.produce_time')):'';
		$data['memo'] = I('post.memo');
		$data['is_audit'] = 0;
		$data['plan_creator_id'] = session('user_id');
		$data['add_time'] = time();


		$photo_url   =   $this->upload();
        if(!empty($photo_url)){
        		$data['photo'] = $photo_url;
        }
		
        $modelName 		= 'Plans';
        $modelAttrName  = 'Plans_attr';
        $attr2modelId = 'plans_id';
        
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

	public function audit($id, $type){
			$arr = array('is_audit'=>0,'is_audit'=>$type);
			$this->updateById($id, $arr);
	}
	
	public function plan_to_product($plans_id){
			$Data = D($this->controll_keywords);
			$list = $Data->getPlans(array('id'=>$plans_id));
			if(empty($list)){
				$this->error('错误：生产单不存在！');
			}
			
			$list= $list['data'];

			$goods = D('Goods');
			$goods->startTrans(); 

			foreach ($list as $row){

				$data = array();

				$data['sku'] = $row['sku'];
				$data['title'] = $row['title'];
				$data['photo'] = $row['photo'];
				$data['operator_id'] = session('user_id');
				$data['plans_id'] = $plans_id;
				$data['add_time'] = time();
				
				$data['orderno'] = $Data->genOrderNo('ST');

				//插入产品主信息
				if (!$goods->create($data)){
					$goods->rollback();
					$error = $goods->getError();
					$this->error($error);
				}

				

				$goods_id = $goods->add(); 
				$plans_attr = $row['plans_attr'];

				$goods_attr = M('goods_attr');
				
				//插入产品属性信息
				foreach($plans_attr as $key=>$val){
					$data_attr = array();
					$data_attr['goods_id'] = $goods_id;
					$data_attr['brand'] = $val['brand'];
					$data_attr['color'] = $val['color'];
					$data_attr['lang'] = $val['lang'];
					$data_attr['memo_attr'] = $val['memo_attr'];

					$data_attr['num_xs'] = $val['num_xs'];
					$data_attr['num_s'] = $val['num_s'];
					$data_attr['num_m'] = $val['num_m'];
					$data_attr['num_l'] = $val['num_l'];
					$data_attr['num_xl'] = $val['num_xl'];
					$data_attr['num_xxl'] = $val['num_xxl'];

					$data_attr['add_time'] = time();

					if (!$goods_attr->create($data_attr)){
								$goods->rollback();
								$error = $goods_attr->getError();
								$this->error($error);
					}

					$goods_attr->add(); 
					
					#计划单的属性标记为已经转换为产品
					M('plans_attr')->where('id='.$val['id'])->data(array('plan_to_good'=>1))->save();


				}

			}

			//生产单转为产品单后，生产单状态为 已转为产品单
			D($this->controll_keywords)->where('id='.$plans_id)->data(array('is_audit'=>'5'))->save();

			$goods->commit();
			$this->success('生产单转为产品单成功！');

	}
	
}