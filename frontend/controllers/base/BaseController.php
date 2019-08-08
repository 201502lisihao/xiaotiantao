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

    /**
     * @param $data
     * @param int $status
     * @return false|string
     */
    protected function apiResponse($data, $status = 1)
    {
        $code = self::SUCCESS;
        $msg = 'api响应成功';
        if ($status == 0) {
            $code = self::FAIL;
            $msg = 'api响应失败';
        }
        $data['code'] = $code;
        $data['msg'] = $data['msg'] ?? $msg;
        $response = json_encode($data);
		return $response;
	}
}
