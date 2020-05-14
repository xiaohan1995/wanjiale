<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/*
  接口调用成功公用方法
*/
function api_success($data='',$msg='success'){
    if(empty($data)){
    	$data = array();
    }
    $ret=array(
       'code'=>1,
       'message'=>$msg,
       'data'=>$data
    );
    exit(json_encode($ret));
}

/*
  接口调用失败公用方法
*/

 function api_error($msg='error',$errcode=''){
    $ret = array(
        'code'=>$errcode,
        'message'=>$msg,
        'data'=>''
     );
    exit(json_encode($ret));
 }