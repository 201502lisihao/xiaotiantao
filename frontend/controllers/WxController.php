<?php

namespace frontend\controllers;

use frontend\controllers\base\BaseController;
use Yii;

/**
 * 小甜桃自主下单-微信小程序Api
 */
class WxController extends BaseController
{
    //GET https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
    const AppId = 'wx8ab7f049e4f4bee3';
    const AppSecret = '9c62768747737a8b29c87eca90c8d9cd';
    const Grant_type = 'authorization_code';
    const WxGetOpenIdUrl = 'https://api.weixin.qq.com/sns/jscode2session';


    /*
	 * test api
	 * https://www.qianzhuli.top/wx/test
	 */
	public function actionTest($id){
		if(empty($id)){
			$data = [];
			return $this->apiResponse($data);	
		}
		$data = array(
			"msg"=>"请求服务器成功",
		);
		$status = 1;
		return $this->apiResponse($data, $status);
	}
	
	public function actionHttptest(){
		$client = new \GuzzleHttp\Client();
		$response = $client->request('GET', 'https://api.github.com/repos/guzzle/guzzle');
		//echo $response->getStatusCode(); # 200
		//echo $response->getHeaderLine('content-type'); # 'application/json; charset=utf8'
		//echo $response->getBody();
		$data = array(
			'code' => $response->getStatusCode(),
		);
		return $this->apiResponse($data,1);
	}

	/*
	 * login
	 */
	public function actionLogin($code){
        
    }
}
