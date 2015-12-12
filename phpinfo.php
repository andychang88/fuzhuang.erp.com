<?php 
//phpinfo(); 
@ini_set('display_errors', '1');
error_reporting(E_ALL);
 
 
spl_autoload_register('myautoload'); 
 
function myautoload($classname) {
        include 'cls.php';
}
 
 
class testa{  
        function f(){ echo 'aaa';exit;
                include 'cls.php'; 
                         
                $a = new testb(); 
                echo $a->f(); 
                         
        }       
                 
        function f2(){ 
                $a = new testb(); 
                echo $a->f(); 
        }       
} 
 
$c =new testa(); 
$c->f2();
