<?php
namespace Home\Controller;
use Think\Controller;
class FormController extends Controller {
    public function index(){
    	$this->title = 'item list';
    	$Data = M('Form'); // ʵ��Data���ģ��
        $this->data = $Data->select();
        $this->display();
    }
	public function insert(){
	 	$Form   =   D('Form');
        if($Form->create()) {
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
        $Form   =   M('Form');
	    // 读取数据
	    $data =   $Form->find($id);
	    if($data) {
	        $this->data =   $data;// 模板变量赋值
	    }else{
	        $this->error('数据错误  error:'.$Form->getError());
	    }
	    
	    $this->title = $Form->where('id=2')->getField('title');
	    
	    $this->display();
    }
	public function edit($id=0){
	    $Form   =   M('Form');
	    $this->vo   =   $Form->find($id);
	    $this->display();
	}
	public function update(){
	    $Form   =   D('Form');
	    if($Form->create()) {
	        $result =   $Form->save();
	        if($result) {
	            $this->success('操作成功！');
	        }else{
	            $this->error('写入错误！');
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
	public function delete($id){
		    $Form = M('Form');
    		$result =   $Form->delete($id);
    		
			if($result) {
	            $this->success('操作成功！');
	        }else{
	            $this->error('删除错误！');
	        }
	        
    		/**
    		 *  $User->where('id=5')->delete(); // 删除id为5的用户数据
				$User->delete('1,2,5'); // 删除主键为1,2和5的用户数据
				$User->where('status=0')->delete(); // 删除所有状态为0的用户数据
    		 */
	}
}