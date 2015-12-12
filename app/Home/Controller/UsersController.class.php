<?php
namespace Home\Controller;
use Home\Controller\CommonController;

class UsersController extends CommonController {
	
	public $controll_keywords = "Retailer";
	
	public function __construct(){
		parent::__construct();
		
		$this->title_index = "用户列表";
		$this->title_add = "添加用户";
		$this->levels = array('1'=>'一级','2'=>'二级','3'=>'三级',);
		$this->roleNames = D('Role')->getRoleNames();//print_r($roles);exit;
		
		$this->pagemenu = $this->sub_menus['users'];
		
	}
	
	public function login(){
		
		if (IS_POST){
			$this -> handle_login();
		}
		
		$this -> display();
	}
	
	private function handle_login(){
		$username = I('post.username');
		$userpass = I('post.passwd');
		
		$error = '';
		$meta = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		
		if(empty($userpass)){
			$error = '用户密码不能为空';
		}else{
			$userpass = D('Retailer')->pwdHash($userpass);
			$where = array('retailer_name'=>$username,'passwd'=>$userpass);
			
			$user = D('Retailer')->where($where)->find();
			
			if(!empty($user)){
				
				//print_r($retailer);exit;
				session('user_id', $user['id']);
				session('nickname', $user['retailer_name']);
				session('role_id', $user['role_id']);
				
				if($user['role_id'] == C('ADMIN_ROLE_ID')){
					session('is_admin', 1);
				}
				$this->success("登录成功，页面跳转中...", C('INDEX_PAGE'));
			}
		}
		
		$this->error("登录错误，请重新登录...", C('LOGIN_PAGE'));

	}
	
	public function logout(){
		session(null);
		$meta = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$this->success("已经退出登录...", C('LOGIN_PAGE'));
	}
	
    public function index(){
    	
    	$this->title = '用户列表';
    	
    	$model = D($this->controll_keywords);
		
    	$where = $this->collectForm();
    	
		$data = $model->getUsersList($where);
		
    	$this->page = $data['page_html'];
		$this->data = $data['data'];
		$this->search_status = 'retailer_products_status';
		
        $this->display();
    }
    
    
    
	public function collectForm(){
		$where = array();
		$role_id = I('get.role_id');
		$starttime = I('get.starttime');
		$endtime = I('get.endtime');
		
		
		if( $role_id ){
			$where['role_id'] = $role_id;
			$this->role_id_selected = $role_id;
		}
    	
		
		if( $starttime && $endtime){
			$where['_complex'] = "add_time>=".strtotime($starttime)." and add_time<=". strtotime($endtime);
		}

		return $where;
	}
	
    public function setPaymentInfo($id){
		$Data = D('Retailer2goods');
		$list = $Data->getRetailerGoods22(array('retailer_id'=>0,'retailer2goods_id'=>$id));

		$this->vo = $list[0];

		$this->title = "设置付款信息";
		$this->bill_time = time();
		$this->display();
	}
	
	
	public function updatePasswd(){
		
		$passwd = I('post.passwd');
    	$post_role_id = I('post.id');
    	
    	//仅仅修改密码
    	if ($passwd && $post_role_id ) {
    		
    		//非管理员只能修改自己的密码
    		if (session('is_admin') == false && session('user_id') != $post_role_id) {
    			 $this->error('修改用户错误！');
    		}
    		
    		$model = D('retailer');
    		
	    	$model->passwd = $model->pwdHash($passwd);
    		
    		if($model->where('id='.$post_role_id)->save()) {
                $this->success('操作成功！');
            }else{
                $this->error('写入错误！');
            }
            
            exit;
    	}
    	
		
		$this->id = I('get.id');
		$this->retailer_name = I('get.retailer_name');
		
        $this->display();
	}
	
	public function add(){
	
    	$retailer_name = I('post.retailer_name');
    	$passwd = I('post.passwd');
    	$post_role_id = I('post.id');
    	$id = I('get.id');
    	
    	$model = D('retailer');
    	
    	//编辑角色
    	if ( !empty($id) ){
    		$retailers = $model->getUsersList(array('id'=>$id));//print_r($roles);exit;
    		
    		if(!empty($retailers)){
    			$this->vo = $retailers['data'][0];
    		}else{
    			$this->error('没有找到要修改的用户！');
    			exit;
    		}
    	}
    	
    	//添加或者处理编辑角色
    	if ( !empty($retailer_name) ){
    		
    		$model->retailer_name = $retailer_name;
    		$model->role_id = I('post.role_id');
    		$model->level = I('post.level');
    		$model->retailer_name = $retailer_name;
    		$model->memo = I('post.memo');
    		
    		if ( $passwd ) {
    			$model->passwd = $model->pwdHash($passwd);
    		}
    		
    		$model->update_time = time();
    		$model->add_time = time();
    		
    		if ( !session('is_admin') ) {
    			$model->parent_id = session('user_id');
        	}
        	
    		if ($post_role_id) {
    			$method = 'save';
    			$model->where('id='.$post_role_id);
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
    	
    	$this->updatePasswd = I('get.updatePasswd');
    	
        $this->display();
    }
    
	public function delete($id,$arr=array()){
		
		    $Form = D('retailer2goods');
		    
		    if(empty($arr)){
		    	$arr = array('is_delete'=>1);
		    }
			
    		$result =   $Form->where('id='.$id)->data($arr)->save();
    		
			if($result) {
	            $this->success('操作成功！');
	        }else{
				
	            $this->error('删除错误！'.$Form->getError());
	        }
	        
    		/**
    		 *  $User->where('id=5')->delete(); // 删除id为5的用户数据
				$User->delete('1,2,5'); // 删除主键为1,2和5的用户数据
				$User->where('status=0')->delete(); // 删除所有状态为0的用户数据
    		 */
	}
	
	
}