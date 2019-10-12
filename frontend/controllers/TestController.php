<?php

namespace frontend\controllers;

use common\models\WxOrdersModel;
use common\models\WxUserModel;
use frontend\controllers\base\BaseController;
use Yii;

/**
 * Test
 */
class TestController extends BaseController
{
    const SUCCESS = 1;
    public $enableCsrfValidation = false; //禁用csrf，否则取不到post参数

    /*
     * https://www.qianzhuli.top/test/test
     */
    public function actionTest($id)
    {
        if (empty($id)) {
            $data = array();
            return $this->apiResponse($data);
        }
        $data = array(
            'msg' => '服务器联通成功',
        );
        return $this->apiResponse($data, self::SUCCESS);
    }


    /*
     * test api
     * https://www.qianzhuli.top/test/logtest
     */
    public function actionLogtest()
    {
        //code
        Yii::error('111111111111111111');
    }

    /*
	 * test api
	 * https://www.qianzhuli.top/test/cachetest
	 */
    public function actionCachetest()
    {
        $cache = Yii::$app->cache;
        if (empty($cache->get('test'))) {
            $cache->set('test', 'TestValue', 86400);
        }
        $res = $cache->get('test');
        $data = array(
            'redis' => $res,
        );
        return $this->apiResponse($data, self::SUCCESS);
    }

    /*
     * https://www.qianzhuli.top/test/httptest
     */
    public function actionHttptest()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://api.github.com/repos/guzzle/guzzle');
        $data = array(
            'code' => $response->getStatusCode(),
            'header' => $response->getHeaderLine('content-type'),
        );
        return $this->apiResponse($data, self::SUCCESS);
    }

    public function actionOrdersmodeltest(){
        $res = WxOrdersModel::find()->where(['id' => 0])->asArray()->one();
        var_dump($res);
    }
}
