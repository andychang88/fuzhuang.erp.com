<?php
namespace Home\Model;
use Home\Model\CommonModel;

    class ArticlesModel extends CommonModel {
        // 定义自动验证
        protected $_validate    =   array(
            array('title','require','标题必须'),
            );
        // 定义自动完成
        protected $_auto    =   array(
            array('add_time','time',1,'function'),
            array('update_time','time',1,'function'),
            
            );
            

		public function getArticles($where=array(), $limit = 0){

			$where = array_merge(array('is_delete'=>0),$where);
			if(!session('is_admin')){
				
				$where['viewer_id'] = array('in', array(session('user_id'), 0));
				$where['viewer_role_id'] = array('in', array(session('role_id'), 0));
				
			}

			if ($limit){
				$this->limit($limit);
			}
			
			$data = $this->order('id desc')->where($where)->selectPage();//echo $data;exit;
			$lists = $data['data'];
			
			foreach ($lists as $key=>$row){
				$viewers = '';
				if( !empty($row['viewer_role_id']) ){
					$role_name = D('role')->getRoleName($row['viewer_role_id']);
					$viewers = '部门：'.$role_name;
				} else {
					$viewers = '全体部门';
				}
				
				if( !empty($row['viewer_id']) ){
					$view_name = D('retailer')->getRetailerName($row['viewer_id']);
					$viewers .= ' 【'.$view_name.'】';
				}
				
				$lists[$key]['viewers'] = $viewers;
				$lists[$key]['creator'] = D('retailer')->getRetailerName($row['creator_id']);
			}
			
			$data['data'] = $lists;
			return $data;
		}

		public function getArticle($id){
			return $this->where('id='.(int)$id)->find();
		}
		

    }

	