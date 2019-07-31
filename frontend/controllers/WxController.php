<?php

namespace frontend\controllers;

use frontend\controllers\base\BaseController;
use Yii;

/**
 * 小甜桃自主下单-微信小程序Api
 */
class WxController extends BaseController
{
	//testApi
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
}
