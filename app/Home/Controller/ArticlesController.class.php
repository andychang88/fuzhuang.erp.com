<?php
namespace Home\Controller;
use Home\Controller\CommonController;

class ArticlesController extends CommonController {
	
	public $controll_keywords = "Articles";
	
	public function __construct(){
		parent::__construct();
		
		$this->title_index = "用户列表";
		$this->title_add = "添加用户";
		$this->levels = array('1'=>'一级','2'=>'二级','3'=>'三级',);
		$this->roleNames = D('Role')->getRoleNames();//print_r($roles);exit;
		
		$this->pagemenu = $this->sub_menus['users'];
		
	}
	
    public function index(){
    	
    	$this->page_title = '文章列表';
    	
    	$model = $this->model;
		
    	$where = $this->collectForm();
    	
		$data = $this->model->getArticles($where);
		
    	$this->page = $data['page_html'];
		$this->data = $data['data'];
		
        $this->display();
    }
    public function detail($id){
    	$id = (int)$id;
    	if ( empty($id) ){
    		$this->error('文章id错误。');
    	}
    	$article = $this->model->getArticle($id);
    	
    	$this->assign($article);
    	
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
	
	
	public function add(){
	
    	$title = I('post.title');
    	$content = I('post.content');
    	$viewer_role_id = I('post.viewer_role_id');
    	$viewer_id = I('post.viewer_id');
    	$id = I('get.id');
    	$action= I('get.act');
    	$post_id = I('post.id');
    	
    	$model = D($this->controll_keywords);
    	
    	if ($action == 'getUsersByRoleId'){
    		$role_id= (int)I('get.role_id');
    		$viewer_html = D('retailer')->getRetailerByRoleId($role_id);
    		die($viewer_html);
    	}
    	//编辑
    	if (empty($post_id) && !empty($id) ){
    		$article = $model->getArticle($id);//print_r($roles);exit;
    		
    		if(!empty($article)){
    			if ($article['viewer_role_id']){
    				$this->viewer_html = D('retailer')->getRetailerByRoleId($article['viewer_role_id'], $article['viewer_id']);
    			}
    			$this->vo = $article;
    		}else{
    			$this->error('没有找到要修改的文章！');
    			exit;
    		}
    	}
    	
    	//添加或者处理编辑角色
    	if ( !empty($title) ){
    		
    		$model->title = $title;
    		$model->content = $content;
    		$model->creator_id = session('user_id');
    		$model->viewer_role_id = $viewer_role_id;
    		$model->viewer_id = $viewer_id;
    		
    		$model->add_time = date('Y-m-d H:i:s');
    		
        	
    		if ($post_id) {
    			$method = 'save';
    			//$model->title = $title;
    			$model->where('id='.$post_id);
    		} else {
    			$method = 'add';
    		}
    		
    		if($model->$method(false)) {
                $this->success('操作成功！');
            }else{
                $this->error('写入错误！');
            }
            
            exit;
    	}
    	
        $this->display();
    }
    
	public function delete($id,$arr=array()){
		
		    $Form = D($this->controll_keywords);
		    
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