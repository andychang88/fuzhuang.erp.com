<?php
namespace Home\Model;
use Home\Model\CommonModel;

    class PlansModel extends CommonModel {
        // 定义自动验证
        protected $_validate    =   array(
            array('title','require','标题必须'),
            );
        // 定义自动完成
        protected $_auto    =   array(
            array('add_time','time',1,'function'),
            array('update_time','time',1,'function'),
            
            );
            
        public function getRetailers($where_arr=array()){
            $retailers = $this->where($where_arr)->select();
            return $retailers;
        }

		public function getPlans($where=array()){

			$where = array_merge(array('is_delete'=>0),$where);
			if(!session('is_admin')){
				$where['plan_creator_id'] = session('user_id');
			}

			$data = $this->order('id desc')->where($where)->selectPage();//echo $this->getLastSql();exit;
			$list = $data['data'];
			
			foreach ($list as $key=>$row){
				
				$list[$key]['user_name'] = D('Retailer')->where("id=".intval($row['plan_creator_id']))->getField("retailer_name");
				$list[$key]['plans_attr'] = $this->getPlansAttr(array('plans_id'=>$row['id']));
				
			}

			$data['data'] = $list;

			return $data;
		}

		public function getPlansAttr($where_history){
			$where_history = array_merge(array('is_delete'=>0),$where_history);
			$plans_attr = M('Plans_attr')->where($where_history)->select();
			foreach($plans_attr as $key=>$attr){
				$plans_attr[$key]['num_xs'] = $attr['num_xs']?$attr['num_xs']:'';
				$plans_attr[$key]['num_s'] = $attr['num_s']?$attr['num_s']:'';
				$plans_attr[$key]['num_m'] = $attr['num_m']?$attr['num_m']:'';
				$plans_attr[$key]['num_l'] = $attr['num_l']?$attr['num_l']:'';
				$plans_attr[$key]['num_xl'] = $attr['num_xl']?$attr['num_xl']:'';
				$plans_attr[$key]['num_xxl'] = $attr['num_xxl']?$attr['num_xxl']:'';

				$plans_attr[$key]['num_xs_ready'] = D('planshistory')->getHistorySizeCount($attr['id'],'num_xs');
				$plans_attr[$key]['num_s_ready'] = D('planshistory')->getHistorySizeCount($attr['id'],'num_s');
				$plans_attr[$key]['num_m_ready'] = D('planshistory')->getHistorySizeCount($attr['id'],'num_m');
				$plans_attr[$key]['num_l_ready'] = D('planshistory')->getHistorySizeCount($attr['id'],'num_l');
				$plans_attr[$key]['num_xl_ready'] = D('planshistory')->getHistorySizeCount($attr['id'],'num_xl');
				$plans_attr[$key]['num_xxl_ready'] = D('planshistory')->getHistorySizeCount($attr['id'],'num_xxl');
			}

			return $plans_attr;
		}

		public function getPlanSizeCount($plans_attr_id, $size){
			if(strpos($size,'num_') === false){
				$size = 'num_'.strtolower($size);
			}

			$num = D('Plans_attr')
				->where('id='.$plans_attr_id.'  and is_delete=0')
				->getField($size);//echo D('Plans_attr')->_sql();exit;
			return intval($num);

		}
		

    }

	