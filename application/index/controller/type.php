<?php
namespace app\index\controller;
use think\Db;
use think\Controller;


class Type extends Controller
{   
	
    public function getcate()
    {
        $data = Db::table('yx_goodtype')->where('t_status=1')->select();
        if(!empty($data)){
        	api_success($data);
        }else{
        	api_success();
        }
    }

    public function getgoods(){
    	
    }

    
    
    

    

    
















}