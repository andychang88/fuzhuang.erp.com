<?php
namespace Home\Controller;
use Home\Controller\CommonController;

class PlanshistoryController extends CommonController {
	
	public $controll_keywords = "Planshistory";
	
	public function __construct(){
		parent::__construct();
		$this->title = '出货记录列表';
	}
	
    public function index($plans_id){
    	
    	$Data = D($this->controll_keywords);

        $list = $Data->getPlanshistory(array('ph.plans_id'=>$plans_id));
        //echo '<pre>';print_r($list);
        
        $this->data = $list;
        
        $this->display();
    }
    
	public function add($plans_id){
    	
		$data = D('Plans')->getPlans(array('id'=>$plans_id));//echo '<pre>';print_r($data);
		$this->vo = $data[0];
        $this->display();
    }
	//添加出货记录
	public function insertRecord($goods_id = 0, $sku = '' ){//echo '<pre>';print_r(I('post.'));exit;

		$amount_ready_arr = I('post.amount_ready');
		$plans_id = I('post.plans_id');
		$plans_attr_id = I('post.plans_attr_id');
		$add_time = time();
		$memo = I('post.memo');//echo '<pre>';print_r($_POST);exit;

		$where = array('plans_id'=>$plans_id,'is_delete'=>0);
		$where_plans = array('id'=>$plans_id,'is_delete'=>0);
		
		$Planshistory = M('Planshistory');

		$Planshistory->startTrans(); 

		$ready_arr = array();

		foreach($amount_ready_arr as $size=>$val){
			if(empty($val[0])){
				continue;
			}

			$size = strtoupper($size);

			$ready_amount = $val[0];
			$ready_amount_history = D('Planshistory')->getHistorySizeCount($plans_attr_id, $size);
			$amount_max = D('Plans')->getPlanSizeCount($plans_attr_id, $size);
//echo D('Plans_attr')->_sql();exit;
			if($ready_amount + $ready_amount_history > $amount_max){
				$this->error('尺寸：'.$size.' 写入的出货数量'.$ready_amount.'有误(总出货数量'.($ready_amount + $ready_amount_history).'大于生产数量'.$amount_max.')！');
			}
			
			//if($ready_amount + $ready_amount_history == $amount_max){
				//M('Plans')->where($where_plans)->data('is_audit=4')->save();
			//}

			$ready_arr[$size] = $ready_amount;
			$arr = array('plans_id'=>$plans_id,
						'plans_attr_id'=>$plans_attr_id,
						'size'=>$size,
						'amount_ready'=>$ready_amount,
						'add_time'=>time(),
						'memo'=>$memo,
						'user_id'=>session('user_id'));

			//插入分销商产品主数据
			if(!$Planshistory->create($arr)) {
				$Planshistory->rollback(); 
				$this->error($Planshistory->getError());
			}

			$Planshistory->add();
			
		}

		$Planshistory->commit(); 

		$this->success('操作成功！');


	}
	public function edit($plans_id, $id){
			$Data = D($this->controll_keywords);
			$list = $Data->getPlanshistory(array('plans_id'=>$plans_id,'ph.id'=>$id));
			$this->vo = $list[0];
			$this->display();
	}
	
	
}