<?php
namespace Home\Model;
use Think\Model;

    class PlanshistoryModel extends Model {
        // 定义自动验证
        protected $_validate    =   array(
            array('title','require','标题必须'),
            );
        // 定义自动完成
        protected $_auto    =   array(
            array('add_time','strtotime',3,'function'),
            array('produce_time','strtotime',3,'function'),
            );
            
        public function getPlanshistory($where_arr){
			$field = 'p.sku,p.title,p.photo,p.id as plans_id,
						ph.amount_ready,ph.add_time,ph.user_id,ph.memo,ph.id,ph.plans_attr_id,ph.size,
						pa.*   
						';

			$where = array_merge(array('ph.is_delete'=>0), $where_arr);
			if(!session('is_admin')){
				$where['ph.user_id'] = session('user_id');
			}

			$count = $this->field($field)
				->table('think_planshistory ph ')
				->join('think_plans p on ph.plans_id=p.id', 'LEFT')
				->join('think_plans_attr pa on ph.plans_id=pa.plans_id and ph.plans_attr_id=pa.id', 'LEFT')
				->where($where)->count();
			$p = getpage2($count);	


            $data = $this->field($field)
				->table('think_planshistory ph ')
				->join('think_plans p on ph.plans_id=p.id', 'LEFT')
				->join('think_plans_attr pa on ph.plans_id=pa.plans_id and ph.plans_attr_id=pa.id', 'LEFT')
				->where($where)
				->order('ph.id desc')
				->limit($p->firstRow.','.$p->listRows)
				->select();
//			echo $this->_sql();
			foreach($data as $key=>$val){
				$plans_id = $val['plans_id'];
				$data[$key]['plans_attr'] = D('Plans')->getPlansAttr($plans_id);

				$size = strtolower($val['size']);
				$field = 'num_'.$size;
				$data[$key]['stock_amount'] = $val[$field];
			}
            return $data;
        }

		public function getHistorySizeCount($plans_attr_id, $size){
			if(empty($size)) return 0;

			if(strpos($size,'num_') === 0){
				$size = substr($size,4);
			}

			$size = strtoupper($size);

			$num = D('Planshistory')
				->where('plans_attr_id='.$plans_attr_id.' and size="'.$size.'" and is_delete=0')
				->sum('amount_ready');//echo D('Planshistory')->_sql();exit;
			return intval($num);
			
		}
    }