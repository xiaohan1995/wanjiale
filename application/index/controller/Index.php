<?php
namespace app\index\controller;
use think\Db;
use wxxcx\wxBizDataCrypt;


class Index
{   
	
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ad_bd568ce7058a1091"></think>';
    }

    public function ceshi(){
        
    	$data = Db::table('user')->where('id',1)->find();
    	return json($data);
    }

    public function ce(){
        
    	$data = Db::table('user')->where('id',1)->find();
    	return json($data);
    }

    public function login(){
    	$encryptedData = input('post.encryptedData');
        $code          = input('post.code');
        $iv            = input('post.iv');
        print_r($iv);die;
        $appid     = 'wx731ab9f741a5a603';
        $appsecret = '92eb447789ec2c9fbeb3db6b2ecaf2ed';
        // step1
        // 通过 code 用 curl 向腾讯服务器发送请求获取 session_key
        $session_key = $this->sendCode($appid, $appsecret, $code);

        // step2
        // 用过 session_key 用 sdk 获得用户信息
        $save = [];
        // 相关参数为空判断
        if (empty($session_key) || empty($encryptedData) || empty($iv)) {
            $msg = "信息不全";
            return $this->ApiSuccess($save, $msg);
        }

        //进行解密
        $userinfo = $this->getUserInfo($encryptedData, $iv, $session_key, $appid);
        
        // 解密成功判断
        if (isset($userinfo['code']) && 10001 == $userinfo['code']) {
            $msg = "请重试"; // 用户不应看到程序细节
            return $this->ApiSuccess($save, $msg);
        }

        session('myinfo', $userinfo);
        $save['openid']    = &$userinfo['openId'];
        $save['uname']     = &$userinfo['nickName'];
        $save['unex']      = &$userinfo['gender'];
        $save['address']   = &$userinfo['city'];
        $save['avatarUrl'] = &$userinfo['avatarUrl'];
        $save['time']      = time();
        $map['openid']     = &$userinfo['openId'];

        $msg = "获取成功";

        //返回用户信息
        return $this->ApiSuccess($save, $msg);

    }

    //获取微信用户信息
    private function sendCode($appid, $appsecret, $code)
    {
        // 拼接请求地址
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='
            . $appid . '&secret=' . $appsecret . '&js_code='
            . $code . '&grant_type=authorization_code';

        $arr = $this->vegt($url);
        $arr = json_decode($arr, true);

        return $arr['session_key'];
    }
    
    // curl 封装
    private function vegt($url)
    {
        $info = curl_init();
        curl_setopt($info, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($info, CURLOPT_HEADER, 0);
        curl_setopt($info, CURLOPT_NOBODY, 0);
        curl_setopt($info, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($info, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($info, CURLOPT_URL, $url);
        $output = curl_exec($info);
        curl_close($info);
        return $output;
    }

    //信息解密
    private function getUserInfo($encryptedData, $iv, $session_key, $APPID)
    {
        //进行解密
        $pc         = new WXBizDataCrypt($APPID, $session_key);
        $decodeData = "";
        $errCode    = $pc->decryptData($encryptedData, $iv, $decodeData);
        //判断解密是否成功
        if ($errCode != 0) {
            return [
                'code'    => 10001,
                'message' => 'encryptedData 解密失败',
            ];
        }
        //返回解密数据
        return json_decode($decodeData, true);
    }



}
