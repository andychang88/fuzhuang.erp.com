<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
// 后台用户模块
class UserController extends CommonController {
    function _filter(&$map){
        $map['id'] = array('egt',2);
        if(!empty($_POST['account'])) {
            $map['account'] = array('like',"%".$_POST['account']."%");
        }
    }

    // 检查帐号
    public function checkAccount() {
        if(!preg_match('/^[a-z]\w{4,}$/i',$_POST['account'])) {
            $this->error( '用户名必须是字母，且5位以上！');
        }
        $User = M("User");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['account'];
        $result  =  $User->getByAccount($name);
        if($result) {
            $this->error('该用户名已经存在！');
        }else {
            $this->success('该用户名可以使用！');
        }
    }

    // 插入数据
    public function insert() {
        // 创建数据对象    
        $User	 =	 D("User");
        $role_id=$_REQUEST['role_id'];
        if(!$User->create()) {
            $this->error($User->getError());
        }else{
            // 写入帐号数据
            if($result	 =	 $User->add()) {
                $this->addRole($result,$role_id);
                $this->success('用户添加成功！');
            }else{
                $this->error('用户添加失败！');
            }
        }
    }
    function update() {
         if(!preg_match('/^[a-z]\w{4,}$/i',$_POST['account'])) {
           echo '用户名必须是字母，且5位以上！';
           return;      
        }
        $model = D("User");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['account'];
        $result  =  $model->getByAccount($name);//model里面_call()
        if($result) {
            //$this->error('该用户名已经存在！');
            echo "该用户名已经存在！";
            return;
        }
       
        if (false === $model->create()) {       
             $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
          
             //替代方法
           // $this->success('修改成功');
             $url='/User/index';
            $this->redirect($url);
           
                           
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
    //新增，为了绑定用户角色
    public function add(){
         $model	=	M("Role");
        $list	=	$model->where('status=1')->select();
        $this->assign('list',$list);
        $this->display();
    }
//新增role_id
    protected function addRole($userId,$role_id) {
        //新增用户自动加入相应权限组
        $RoleUser = M("RoleUser");
        $RoleUser->user_id=$userId;
        // 默认加入网站编辑组
        $RoleUser->role_id=$role_id;
        $RoleUser->add();
    }

    //重置密码
    public function resetPwd() {
        $id  =  $_POST['id'];
        $password = $_POST['password'];
        if(''== trim($password)) {
            $this->error('密码不能为空！');
        }
        $User = M('User');
        $User->password	=	md5($password);
        $User->id			=	$id;
        $result	=	$User->save();
        if(false !== $result) {
            $this->success("密码修改为$password");
        }else {
            $this->error('重置密码失败！');
        }
    }
}