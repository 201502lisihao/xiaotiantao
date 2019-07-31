<?php

namespace frontend\controllers\base;

use yii\web\Controller;

/**
 * 基础控制器
 */
class BaseController extends Controller
{
	const FAIL = 400;
	const SUCCESS = 200;

	public function beforeAction($action)
	{
		if(!parent::beforeAction($action))
		{
			return false;
		}
		return true;
	}	

	//封装一个响应方法
	protected function apiResponse($data,$status=0){
		$code = self::FAIL;
		$msg = 'fail';
		if ($status == 1) {
			$code = self::SUCCESS;
			$msg = 'success';
		}
		$ret = array(
			'code' => $code,
			'msg' => $msg,
			'data' => $data
		);
		$response = json_encode($ret);
		return $response;
	}
}
