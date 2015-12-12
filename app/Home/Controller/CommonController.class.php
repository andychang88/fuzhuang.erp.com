<?php
namespace Home\Controller;

use Think\Controller;

class CommonController extends Controller {
	
	public $title = '';
	public $controll_keywords = "Plans";
	
	function _initialize() {//echo '<pre>';print_r($_SESSION);exit;
		$user_id = session('user_id');
		if(empty($user_id) && !in_array(ACTION_NAME,C('NOT_LOGIN_ACTIONS'))){
			$this->redirect(('Users/login'), array(), 3, $meta.'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />请登录...');
			//$this->error("请登录...!", 'Users/login');
			//die('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />请初始化登录<br>'.$url);
			//$this->redirect(MODULE_NAME.'/Retailer/login/id/1', array(), 3, '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />自动登录，页面跳转中...');
		}
		
		$allowed = D('Role')->checkPriv();
		if(! $allowed){
			$this->error("您没有权限访问该页面!", __MODULE__.C('DEFAULT_ALLOWED_PAGE'));
		}
		
	}
	
	public function __construct(){
		parent::__construct();
		
		if (!empty($this->controll_keywords)){
			$this->model = D( $this->controll_keywords );
		}

		$this->system_name = "兔芭露<span style='vertical-align:super; font-size:9px'>TM</span>产销存管理系统";
		$this->page_title = "RMS";
		$this->brand = C('brand');
		$this->brand_lang = C('brand_lang');
		$this->retailer_products_status = C('retailer_products_status');
		$this->plans_status = C('plans_status');
		
		
		$this->retailers = D('Retailer')->getRetailers();
		$this->retailer_id_selected = (int)I('get.retailer_id');
		
		$this->retailer_payment_status = C('retailer_payment_status');
		$this->retailer_payment_status_selected = (int)I('get.retailer_payment_status');
		
		$this->retailer_apply_status = C('retailer_apply_status');
		$this->retailer_apply_status_selected = (int)I('get.retailer_apply_status');
		
		$this->bill_status = C('retailer_bill_status');
		$this->bill_status_selected = (int)I('get.bill_status');
		
		
		$this->initSubMenus();
		
		$this->CONTROLLER_NAME = CONTROLLER_NAME;
		$this->ACTION_NAME = ACTION_NAME;
		
		$notices = D('articles')->getArticles(array(), 5);
		$this->notices = $notices['data'];
		
		
	}
	
	public function add(){
    	
        $this->display();
    }
    public function upload(){
    	//echo '<pre>';print_r($_FILES);exit;
    	if(empty($_FILES['photo']['tmp_name']) ) return '';
    	
    	$upload = new \Think\Upload();// 实例化上传类
	 	$upload->maxSize   =     3145728 ;// 设置附件上传大小
	 	$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	 	$upload->rootPath  =     C('UPLOAD_ROOT_PATH') ;// 设置附件上传大小
	 	$upload->savePath  =      'Uploads/'; // 设置附件上传目录
	 	
	 	$info   =   $upload->upload();
	 	
    	if(!$info) {// 上传错误提示错误信息
	 		$this->error($upload->getError());
	 	}else{// 上传成功 获取上传文件信息
	 		return $info['photo']['savepath'].$info['photo']['savename']; 
	 	}
    }
	public function insert(){
	 	$Form   =   D($this->controll_keywords);
	 	
        if($Form->create()) {
        	
        	$photo_url   =   $this->upload();
        	if(!empty($photo_url)){

				if(empty($Form->photo_field)) 
					$photo_field = 'photo';
				else 
					$photo_field = $Form->photo_field;

        		$Form->$photo_field = $photo_url;

        	}
        	
	 		$Form->user_id = session('user_id');
	 		$Form->update_time = time();
	 		
        	//$Form->photo = $info['photo']['savepath'].$info['photo']['savename']; 
            $result =   $Form->add();
            if($result) {
                $this->success('操作成功！');
            }else{
                $this->error('写入错误！');
            }
        }else{
            $this->error($Form->getError());
        }
    }
    public function read($id=0){
        $Form   =   M($this->controll_keywords);
	    // 读取数据
	    $data =   $Form->find($id);
	    if($data) {
	        $this->data =   $data;// 模板变量赋值
	    }else{
	        $this->error('数据错误  error:'.$Form->getError());
	    }
	    
	    $this->display();
    }
	
	public function edit($id=0){
	    $Form   =   M($this->controll_keywords);
	    $this->vo   =   $Form->find($id);
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
	public function updateById($id,$arr=array()){
		
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
	    
	}
	public function update($data=array(), $is_param=false){
	    $Form   =   D($this->controll_keywords);

		$param_arr = array();
		if($is_param){
			$param_arr = $data;
		}
	    if($data = $Form->create($param_arr)) {
	    	
	    	$photo_url   =   $this->upload();
        	if(!empty($photo_url)){

				if(empty($Form->photo_field)) 
					$photo_field = 'photo';
				else 
					$photo_field = $Form->photo_field;

        		$Form->$photo_field = $photo_url;
        	}
        	
			foreach($data as $key=>$val){
				$Form->$key = $val;
			}
			
			if( !isset($data['is_audit'])) {
				$Form->is_audit = 0;
			}
			
			if( !isset($data['update_time'])) {
				$Form->update_time = time();
			}
        	
        	
	        $result =   $Form->save();
	        
	        if($result) {
	            $this->success('操作成功！');
	        }else{
	            $this->error('写入错误！'.$Form->getError());
	        }
	    }else{
			
	        $this->error($Form->getError());
	    }
	    /**
	     * 
	         $Form = M("Form"); 
    // 要修改的数据对象属性赋值
    $data['title'] = 'ThinkPHP';
    $data['content'] = 'ThinkPHP3.1版本发布';
    $Form->where('id=5')->save($data); // 根据条件保存修改的数据
    
	     */
	    /**
	     * $Form->where('id=5')->setField('title','ThinkPHP');
	     */
	}
	
	private  function initSubMenus(){
		$submenus = array(
					'Retailer'=>array(			
								array('url'=>__MODULE__.'/Retailer/myGoods','text'=>'产品列表'),
								array('url'=>__MODULE__.'/Retailer/paymenthistory','text'=>'付款历史列表'),
							
					),
					'Soldrecords'=>array(			
								array('url'=>__MODULE__.'/Soldrecords/index','text'=>'售出列表'),
								
							
					),
					'Finance'=>array(			
								array('url'=>__MODULE__.'/Finance/index','text'=>'分销商付款审核'),
							
					),
					'Users'=>array(			
								array('url'=>__MODULE__.'/Users/index','text'=>'用户列表'),
								array('url'=>__MODULE__.'/Users/add','text'=>'添加用户'),
								array('url'=>__MODULE__.'/Role/index','text'=>'角色列表'),
								array('url'=>__MODULE__.'/Role/add','text'=>'添加角色'),
								array('url'=>__MODULE__.'/Role/moduleList','text'=>'权限列表'),
					),
					'Role'=>array(			
								array('url'=>__MODULE__.'/Users/index','text'=>'用户列表'),
								array('url'=>__MODULE__.'/Users/add','text'=>'添加用户'),
								array('url'=>__MODULE__.'/Role/index','text'=>'角色列表'),
								array('url'=>__MODULE__.'/Role/add','text'=>'添加角色'),
								array('url'=>__MODULE__.'/Role/moduleList','text'=>'权限列表'),
					),
					'Plans'=>array(			
								array('url'=>__MODULE__.'/plans/index','text'=>'计划单列表'),
								array('url'=>__MODULE__.'/plans/add','text'=>'新建计划单'),
							
					),
					'Goods'=>array(			
								array('url'=>__MODULE__.'/Goods/index','text'=>'产品列表'),
								array('url'=>__MODULE__.'/Goods/add','text'=>'添加产品'),
								array('url'=>__MODULE__.'/Goods/alert','text'=>'智能预警'),
							
					),
					'Retailerapply'=>array(			
								array('url'=>__MODULE__.'/Retailerapply/','text'=>'预订退订列表'),
								array('url'=>__MODULE__.'/Retailerapply/add','text'=>'添加预订退订单'),
								array('url'=>__MODULE__.'/Retailerapply/index/type/1/retailer_apply_status/1','text'=>'预订单','selectedby'=>'type,retailer_apply_status'),
								array('url'=>__MODULE__.'/Retailerapply/index/type/2/retailer_apply_status/1','text'=>'退订单','selectedby'=>'type,retailer_apply_status'),
							
					),
					'Report'=>array(			
								array('url'=>__MODULE__.'/Report/showBrandStat/type/bynum','text'=>'销售数量统计','selectedby'=>'type'),
								array('url'=>__MODULE__.'/Report/showBrandStat/type/byamt','text'=>'销售金额统计','selectedby'=>'type'),
								array('url'=>__MODULE__.'/Report/showRetailerRanks','text'=>'分销商销售排名','selectedby'=>'type'),
							
					),
					'Articles'=>array(			
								array('url'=>__MODULE__.'/Articles/index','text'=>'通知列表'),
								array('url'=>__MODULE__.'/Articles/add','text'=>'添加通知'),
							
					),
			);
			
			
		if ($submenus[CONTROLLER_NAME]) {
			
			$item = $submenus[CONTROLLER_NAME];
			
			foreach ($item as $key=>$val) {
				
				list(,,$controller_name,$action) = explode('/', $val['url'], 4);
				
				if( $controller_name != CONTROLLER_NAME ){
					continue;
				}
				
				$action = $action ? $action : 'index';
				
				if( $action == ACTION_NAME ){
					$item[$key]['selected'] = 1;
					$selected_key = $key;
				} else if (isset($val['selectedby'])) {
					$selectedbyfields = explode(',', $val['selectedby']);
					
					
					$counter = 0;
					foreach ($selectedbyfields as $field) {
						$tmp_val = I('get.'.$field);
						if(strlen($tmp_val)>0 && strpos($val['url'], $field.'/'.$tmp_val) !== false) {
							$counter++;
						}
					}
					
					if($counter == count($selectedbyfields)){
						$item[$key]['selected'] = 1;
					}
					
					if( $item[$key]['selected'] && $item[$selected_key]['selected'] ){
						$item[$selected_key]['selected'] = 0;
					}
					
				}
				
			}
			
			$this->submenus = $item;
		}
		
		
	}
	

		
}