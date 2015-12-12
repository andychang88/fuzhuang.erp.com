<?php
namespace Home\Model;
use Think\Model;

    class RetailerModel extends Model {
        // 定义自动验证
        protected $_validate    =   array(
            array('title','require','标题必须'),
            );
        // 定义自动完成
        protected $_auto    =   array(
            array('add_time','time',1,'function'),
			array('passwd','pwdHash',3,'callback'),
			
            );
        

        public function getUsersList($where_arr=array()){
        	
        	$where_arr = array_merge(array('is_delete'=>0),$where_arr);
        	
        	if ( !session('is_admin') ) {
        		//$where_arr['parent_id'] = session('user_id');
        		$tmp_where['parent_id'] = session('user_id');
        		$tmp_where['id'] = session('user_id');
        		$tmp_where['_logic'] = 'or';
        		
       
        		$where_arr['_complex'] =  $tmp_where;
        		
        	}
        	
            $data = $this->order('id desc')->where($where_arr)->selectPage();
            
            $list = $data['data'];
            
	        foreach($list as $key=>$val){
				$list[$key]['role_name'] = M('role')->where('id='.$val['role_id'])->getField('name');
				$list[$key]['parent_name'] = $this->getRetailerName($val['parent_id']);
			}
			
			$data['data'] = $list;
			
			return $data;
        }
        public function getRetailerName($retailer_id){
        	return $this->where('id='.(int)$retailer_id)->getField('retailer_name');
        }
        public function getRetailers($where_arr=array()){
        	
			$where_arr = array_merge(array('role_id'=>C('RETAILER_ROLE_ID'),'is_delete'=>'0'), $where_arr);
			if( !session('is_admin') && session('role_id') ) {
				
				$tmp['id'] = session('user_id');
				$tmp['parent_id'] = session('user_id');
				$tmp['_logic'] = 'OR';
				
				$where_arr['_complex'] = $tmp;
			}
			
            $retailers = $this->where($where_arr)->select();
			$data = array();
			foreach($retailers as $val){
				$data[$val['id']] = $val['retailer_name'];
			}
            return $data;
        }
	    public function getRetailerByRoleId($role_id, $seleted_id=0){
			$users = $this->where('role_id='.(int)$role_id)->select();
			if(empty($users)){
				return '<option value="0">请选择用户</option>';
			}
			$html = "<option value='0'>全部用户</option>";
			foreach ($users as $user){
				$html .= "<option ".($user['id'] == $seleted_id? ' selected ':'')."value='".$user['id']."'>".$user['retailer_name']."</option>";
			}
			return $html;
		}
		public function pwdHash($passwd) {
			if($passwd) {
				return pwdHash($passwd);
			}else{
				
				return false;
			}
		}
    }