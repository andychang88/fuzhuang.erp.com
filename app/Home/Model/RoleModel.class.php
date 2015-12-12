<?php
namespace Home\Model;
use Think\Model;

    class RoleModel extends Model {
        // 定义自动验证
        protected $_validate    =   array(
            array('title','require','标题必须'),
            );
        // 定义自动完成
        protected $_auto    =   array(
            array('add_time','time',1,'function'),
            );
   	 	public function getRoleNames($where_arr=array()){
			
            $roles = $this->getRoles($where_arr, 'name,id');
			$result = array();
			foreach($roles as $key=>$val){
				if($val['id'] == C('ADMIN_ROLE_ID')){
					continue;
				}
				$result[$val['id']] = $val['name'];
			}
            return $result;
        }    
        public function getRoleName($id){
			return $this->where('id='.(int)$id)->getField('name');
        }    
        public function getRoles($where_arr=array(), $fields = '*'){
			$where_arr = array_merge(array('status'=>1), $where_arr);
            $roles = $this->field($fields)->where($where_arr)->select();
			
            return $roles;
        }
        
        public function getAllPrivs($role_id = ''){
        	$controllers = $this->getAllControllers();
        	
        	$result = array();
        	
        	foreach ($controllers as $key => $controller){
        		$controller_name = $controller['controller_name'];
        		$controllers[$key]['methodes'] = $this->getAllMethodes($controller_name, $role_id);
        	}
        	
        	return $controllers;
        }
        
        public function getAllMethodes($controller_name, $role_id = ''){
        	$result = M('modules')
        						 ->where('controller_name = "'.$controller_name.'" and controller_name != method_name')
        						 ->order('sort asc')
        						 ->select();
        	
        	if ($role_id){
        		$roleMethodes = $this->getMethodesByRoleId($role_id);
        		
        		foreach ($result as $module=>$methodes) {
        			if ( in_array($methodes['id'], $roleMethodes)) {
        				$result[$module]['selected'] = 1;
        			}
        		}
        	}
        	
        	return $result;
        }
        
        public function getAllControllers($where_arr=array(), $fields = '*'){
        	$result = M('modules')
        						 ->where('controller_name = method_name')
        						 ->order('sort asc')
        						 ->select();
        	return $result;
        }
        public function getMethodesByRoleId($role_id){
        	return M('role2modules')->where('role_id = "'.$role_id.'" ')
        						 ->getField('module_id', true);
        }
        public function deletePrivByRoleId($role_id){
        	return M('role2modules')->where('role_id = "'.$role_id.'" ')
        						 ->delete();
        }
        
        public function checkMethodExsits($data){
        	$where = 'controller_name="'.$data['controller_name'].'" 
        	and method_name="'.$data['method_name'].'" ';
        	
        	if( $data['keyword1'] && $data['keyword1_val']) {
        		$where .= ' and  keyword1="'.$data['keyword1'].'" and keyword1_val="'.$data['keyword1_val'].'" ';
        	}
        	M('modules')->where($where)->count();
        	return M('modules')->where($where)->count();
        }
        
    	public function checkPriv(){
    		
    		$module_name = MODULE_NAME;
    		$controller_name = CONTROLLER_NAME;
    		$action_name = ACTION_NAME;
    		
    		$allowed_public_pages = C('allowed_public_pages');
    		//echo '$module_name:'.$module_name.', $controller_name:'.$controller_name.', $action_name:'.$action_name.'<br>';exit;
    		$role_id = (int)session('role_id');
    		
    		if($role_id == C('ADMIN_ROLE_ID') || in_array($module_name.'/'.$controller_name.'/'.$action_name, $allowed_public_pages)){
    			return true;
    		}
    		
    		$where = "r2m.role_id = $role_id 
    				 and m.module_name = '$module_name' 
    				 and m.controller_name = '$controller_name' 
    				 and m.method_name = '$action_name' 
    				 ";
    		
    		$result =  $this ->table('think_modules as m')
			    			 ->join("think_role2modules r2m on m.id=r2m.module_id","LEFT")
			    			 ->where($where)
			    			 ->find();
			    			 
			    			 
			if(empty($result)){
				return false;
			}
			
			if( !empty($result['keyword1']) ){
				
				if( $result['keyword1_val'] == '%' ){
					return true;
				}
				
				$keyword1_url = I('get.'.$result['keyword1']);
				
				if( $keyword1_url != $result['keyword1_val']) {
					return false;
				}
			}
			
			return true;
    		
        }
    }