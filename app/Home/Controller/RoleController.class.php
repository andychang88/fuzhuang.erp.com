<?php
namespace Home\Controller;
use Home\Controller\CommonController;

class RoleController extends CommonController {
	
	public $controll_keywords = "Role";
	
	public function __construct(){
		parent::__construct();
		
		$this->title_index = "用户列表";
		$this->title_add = "添加用户";
		$this->levels = array('1'=>'一级','2'=>'二级','3'=>'三级',);
		$this->roleNames = D('Role')->getRoleNames();//print_r($roles);exit;
		
		
	}
	
    public function add(){
    	
    	$role_name = I('post.name');
    	$id = I('get.id');
    	
    	//编辑角色
    	if ( !empty($id) ){
    		$roles = D('Role')->getRoles(array('id'=>$id));//print_r($roles);exit;
    		
    		if(!empty($roles)){
    			$this->role = $roles[0];
    		}else{
    			$this->error('没有找到要修改的角色！');
    			exit;
    		}
    	}
    	
    	//添加或者处理编辑角色
    	if ( !empty($role_name) ){
    		
    		$model = D('role');
    		
    		$id = I('post.id');
    		
    		$model->name = $role_name;
    		$model->remark = I('post.remark');
    		$model->create_time = time();
    		$model->update_time = time();
    		
    		if ($id) {
    			$method = 'save';
    			$model->where('id='.$id);
    		} else {
    			$method = 'add';
    		}
    		
    		if($model->$method()) {
                $this->success('操作成功！');
            }else{
                $this->error('写入错误！');
            }
            
            exit;
    	}
    	
    	$this->display();
    	
    }
    
    
    //给用户分配权限
    public function priv(){
    	
    	$role_id = I('get.role_id')? I('get.role_id') : I('post.role_id');
    	
    	if ( IS_POST && $role_id) {
    		
    		$module2methodes 	= I('post.methodes');
    		$role2modules 		= M('role2modules');
    		
    		$role2modules -> startTrans(); 
    		
    		D('role') -> deletePrivByRoleId($role_id);
    		//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($module2methodes);exit;
    		foreach ( $module2methodes as $module => $methodes ){
    			foreach ( $methodes as $method_id ){
    				$data = array('role_id' => $role_id, 'module_id' => $method_id);
    				
    				if (! $role2modules->data($data)->add()){
    					$role2modules -> rollback(); 
    					$error = $role2modules->getError();
						$this->error($error);exit;
    				}
    			}
    		}
    		
    		$role2modules->commit();
    		
    		$this->success('操作成功！');exit;
    		//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($methodes);exit;
    	}
		 
    	$this->allModules = D('role')->getAllPrivs($role_id);
    	
    	//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($this->allModules);exit;
    	$this->role_id = $role_id;
    	
    	$this->display();
    }
    
    //添加权限模块、所有权限模块列表
    public function moduleList($default_controller_name = ''){
    	
    	
    	if ( IS_POST ) {
    		$data = array();
    		$data['name'] 				= trim(I('post.name'));
    		$id 				= (int)I('post.id');
    		$data['controller_name'] 	= trim(I('post.controller_name'));
    		$data['method_name'] 		= trim(I('post.method_name'));
    		$data['keyword1'] 		= trim(I('post.keyword1'));
    		$data['keyword1_val'] 		= trim(I('post.keyword1_val'));
    		$data['sort'] 				= (int)I('post.sort');
    		$data['module_name'] 		= 'Home';
    		
    		if ($data['name'] && $data['controller_name'] && $data['method_name']) {
    			
    			$success = false;
    			
    			if( $id ){
    				M('modules')->where('id='.$id)->data($data)->save();
    				$success = true;
    			} else {
    				$isExsits = D('role')->checkMethodExsits($data);
    				if ( ! $isExsits && M('modules')->data($data)->add()) {
    					$success = true;
    				}
    			}
    			
    			if ( $success ) {
    				$this->msgtip = "添加成功！";
    			} else {
    				$this->msgtip = "添加失败，记录已经存在！";
    			}
    		}
    	}
    	
    	$this->default_controller_name = $default_controller_name;
    	
    	$this->allModules = D('role')->getAllPrivs();
    	//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($this->allModules);exit;
    	$this->display();
    }
    public function getModule($id){
    	$this->vo = M('modules')->where('id='.(int)$id)->find();
    	
    	C('LAYOUT_ON', 0);
    	
    	$html = $this->fetch('form_add');
    	
    	C('LAYOUT_ON', 1);
    	
    	die($html);
    }
    public function deleteModule($id){
    	if ( M('modules')->where('id='.(int)$id)->delete() ) {
    		$this->success('操作成功！');
    	} else {
    		$this->error('写入错误！');
    	}
    }
    public function index(){
    	//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($this->roles);exit;
    	
    	$this->roles = D('Role')->getRoles();//print_r($roles);exit;
    	$this->display();
    }
   
	
}