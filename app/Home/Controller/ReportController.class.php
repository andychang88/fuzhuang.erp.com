<?php
namespace Home\Controller;
use Home\Controller\CommonController;
class ReportController extends CommonController {
	public $colors;
	public $templates;
	
	function __construct(){
		parent::__construct();
		
		$this->colors = array('num_s'=>'255,255,0','num_xs'=> '255,0,0','num_m'=> '0,0,128','num_l'=> '0,100,0','num_xl'=> '144,238,144','num_xxl'=> '28,28,28');
		$this->templates = '{
			fillColor : "rgba(%s,0.5)",
			strokeColor : "rgba(220,220,220,0.8)",
			highlightFill: "rgba(%s,0.75)",
			highlightStroke: "rgba(220,220,220,1)",
			data : [%s]
		}';
	}
    public function index(){
    	$this->showBrandStat('bynum');exit;
    }
	public function test(){
    	$arr = M('goods')->where('id=200')->getField('sku,id');
		echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($arr);exit;
		exit;
    }
    /*
     * 一周内，一个月内, 按照品牌,个型号销售数量的报表；
     */
    public function showBrandStat($type='byamt'){
    	
    	$starttime = I('get.starttime');
    	$endtime = I('get.endtime');
    	
    	if (empty($starttime)){
    		$starttime = date('Y-m-d', strtotime('-7 days'));
    	}
    	if (empty($endtime)){
    		$endtime = date('Y-m-d');
    	}
    	
    	$this->starttime = $starttime;
    	$this->endtime = $endtime;
    	
    	if($type == 'bynum'){
    		$this->iconTitle = "产品销售数量统计(个)";
    		$rows = D('report')->salesNumByBrand($starttime, $endtime);
    	}else{
    		$this->iconTitle = "产品销售金额统计(RMB)";
    		$rows = D('report')->salesAmountByBrand($starttime, $endtime);
    	}
    	
    	
    	$brands = array();
    	
    	foreach ($rows as $row){
    		$brands['brands'][] = $row['brand'];
    		unset($row['brand']);
    		foreach ($row as $name=>$size){
    			$brands['size'][$name][] = (int)$size;
    		}
    	}
    	
    	foreach ($brands['size'] as $key=>$val){
    		$brands['size'][$key] = implode(',', $val);
    	}
    	
    	//图表的横向标签
    	$brands_arr = $brands['brands'];
    	$labels = "";
    	for($i=0,$len=count($brands_arr); $i<$len; $i++){
    		$labels .= '"'.$brands_arr[$i].'"';
    		if($i<($len-1)){
    			$labels .= ',';
    		}
    	}
    	
    	$this->labels = $labels;
    	
    	$size = $brands['size'];
	    $colors = $this->colors;
		$templates = $this->templates;
		
		
		$i=0;$len=count($size);
		$datasets = "";
		$icon_intro = "<table>";
		foreach($size as $name=>$val){
			$color = $colors[$name];
			$size_name = strtoupper(str_replace('num_', '  ', $name));
			$icon_intro .= "<tr><td><div class='bar_icon' style='background-color:rgba(".$color.",1);'></div></td><td>".$size_name."</td></tr>";
			$datasets .= sprintf($templates, $color, $color,$val);
			if($i<($len-1)){
				$datasets .= ',';
			}
	
			$i++;
		}
		$icon_intro .= "</table>";
	
		$this->icon_intro = $icon_intro;
		
		$this->datasets = $datasets;
		
		
    	$this->assign($brands);
    	//$this->brands = $brands;
    	//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($brands);exit;
    	$this->display();
    	
    }
   
	/*
     * 分销商销售额排名
     */
    public function showRetailerRanks(){
    	
    	$starttime = I('get.starttime');
    	$endtime = I('get.endtime');
    	
    	if (empty($starttime)){
    		$starttime = date('Y-m-d', strtotime('-7 days'));
    	}
    	if (empty($endtime)){
    		$endtime = date('Y-m-d');
    	}
    	
    	$this->starttime = $starttime;
    	$this->endtime = $endtime;
    	
    	$this->iconTitle = "分销商销售金额排名(RMB)";
    	
    	$ranks = D('report')->getRetailerRanks($starttime, $endtime);
    	
    	$templates = '{
					value: %s,
					color:"#%s",
					highlight: "#%s",
					label: "%s"
				}';
    	
    	$labels = "";
    	$datasets = "";
    	$colors = array('FF0000','B22222','FFA500','F08080','FF69B4','B03060','DDA0DD',
    					'FF8C69','8B4C39','FFA500','CD8500','FF6EB4','FFB5C5'
    	);
    	$i=0;$len=count($ranks);
    	foreach ($ranks as $rank){
    		$labels .= '"'.$rank['retailer_name'].'"';
    		
    		if ($colors[$i]) {
    			$color = $colors[$i];
    		}else{
    			$color = '000000';
    		}
    	
    		$datasets .= sprintf($templates, $rank['sales'], $color, $color,'分销商：'.$rank['retailer_name'].', 销售额  ');
    		
    		if($i<($len-1)){
				$datasets .= ',';
				$labels .= ',';
			}
	
			$i++;
    	}
    	
    	$this->labels = $labels;
    	$this->datasets = $datasets;
    	//echo  "<meta charset='UTF-8' /><br>".__FILE__.' line:'.__LINE__.'============'."<pre>";print_r($datasets);print_r($labels);exit;
    	$this->display();
    	
    }
    private function getColor($color_id){
    	if ( $color_id >= 255){
    			$color_id = $color_id % 255;
    		}
    		$color = "$color_id,$color_id,$color_id";
    }
}