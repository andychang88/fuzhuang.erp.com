<?php
return array(
'DB_TYPE'   => 'mysql', // ��ݿ�����
'DB_HOST'   => '127.0.0.1', // ��������ַ
'DB_NAME'   => 'andy_lijian', // ��ݿ���
'DB_USER'   => 'root', // �û���
'DB_PWD'    => '', // ����
'DB_PORT'   => 3306, // �˿�
'DB_PREFIX' => 'think_', // ��ݿ��ǰ׺

'TMPL_ACTION_SUCCESS'=>'Public/success',
'TMPL_ACTION_ERROR'=>'Public/error',

'SHOW_ERROR_MSG'=>true,

'LAYOUT_ON'=>true,
'LAYOUT_NAME'=>'layout',

'TMPL_CACHE_ON'=> false,

'SHOW_PAGE_TRACE' =>true, 
'TAGLIB_PRE_LOAD' => 'html' ,
'INDEX_PAGE'=>'Index/index',
'LOGIN_PAGE'=>'Users/login',

'UPLOAD_ROOT_PATH' => './Public/',

'PAGE_SIZE'=>10,
'ADMIN_USERS'=>array('admin'=>'admin'),
'PLAN_USERS'=>array(),
'RETAILER_USERS'=>array(),

'ADMIN_ROLE_ID'=>3,
'RETAILER_ROLE_ID'=>23,
'NOT_LOGIN_ACTIONS'=>array('login','login2','handle_login'),

'brand'=>array('兔芭露'=>'兔芭露', '安娜贝拉'=>'安娜贝拉', '芭弩腊'=>'芭弩腊', ),
'brand_lang'=>array('中文'=>'中文','英文'=>'英文'),

'retailer_bill_status'=>array('1'=>'待付款','2'=>'付款待审核','3'=>'已付款','4'=>'已废弃',),
'retailer_bill_status_default'=>2,
'plans_status'=>array('1'=>'停产','2'=>'计划','3'=>'已付款','4'=>'已废弃',),
'finance_status'=>array('1'=>'待付款','2'=>'付款待审核','3'=>'已付款','4'=>'已废弃',),
'retailer_payment_status'=>array('0'=>'待审核','1'=>'审核通过','2'=>'审核驳回'),
'retailer_apply_status'=>array('0'=>'草稿','1'=>'审核中','2'=>'审核通过','3'=>'审核驳回','4'=>'废除'),


'DEFAULT_ALLOWED_PAGE' => '/Index/index',
'allowed_public_pages' => array('Home/Users/login',
								'Home/Users/logout',
								'Home/Index/index',

),
'page_menus'=>array('users'=>array(			array('url'=>__MODULE__.'/Users/index','text'=>'用户列表'),
								array('url'=>__MODULE__.'/Users/add','text'=>'添加用户'),
								array('url'=>__MODULE__.'/Role/index','text'=>'角色列表'),
								array('url'=>__MODULE__.'/Role/add','text'=>'添加角色'),
								array('url'=>__MODULE__.'/Role/moduleList','text'=>'权限列表'),
					),
					'users2'=>array(			array('url'=>__MODULE__.'/Users/index','text'=>'用户列表'),
								array('url'=>__MODULE__.'/Users/add','text'=>'添加用户'),
								array('url'=>__MODULE__.'/Role/index','text'=>'角色列表'),
								array('url'=>__MODULE__.'/Role/add','text'=>'添加角色'),
								array('url'=>__MODULE__.'/Role/moduleList','text'=>'权限列表'),
					),
			),
);