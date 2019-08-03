<?php

namespace frontend\controllers;

use frontend\controllers\base\BaseController;
use Yii;

/**
 * 小甜桃服务端-微信小程序Api
 */
class WxController extends BaseController
{
    //禁用csrf，否则接收不到json数据
    public $enableCsrfValidation = false;
    
    //apiResponse的第二个参数
    const SUCCESS = 1;

    //GET https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
    const AppId = 'wx8ab7f049e4f4bee3';
    const AppSecret = '9c62768747737a8b29c87eca90c8d9cd';
    const Grant_type = 'authorization_code';
    const WxGetOpenIdUrl = 'https://api.weixin.qq.com/sns/jscode2session';


    /*
     * https://www.qianzhuli.top/wx/test
     */
    public function actionTest($id){
        if(empty($id)){
            $data = array();
            return $this->apiResponse($data);
        }
        $data = array(
            'msg' => '服务器联通成功',
        );
        return $this->apiResponse($data,self::SUCCESS);
    }

    /*
	 * test api
	 * https://www.qianzhuli.top/wx/cachetest
	 */
	public function actionCachetest(){
        $cache = Yii::$app->cache;
        if(empty($cache->get('test'))){    
            $cache->set('test','TestValue',86400);
        }
        $res = $cache->get('test');
        $data = array(
            'redis' => $res,
        );
        return $this->apiResponse($data,self::SUCCESS);
	}
	
	public function actionHttptest(){
		$client = new \GuzzleHttp\Client();
		$response = $client->request('GET', 'https://api.github.com/repos/guzzle/guzzle');
		//echo $response->getStatusCode(); # 200
		//echo $response->getHeaderLine('content-type'); # 'application/json; charset=utf8'
		//echo $response->getBody();
		$data = array(
			'code' => $response->getStatusCode(),
            'header' => $response->getHeaderLine('content-type'),
            'body' => $response->getBody(),
		);
		return $this->apiResponse($data,self::SUCCESS);
	}

	/*
	 * login
     * @params code 微信的临时登录code
	 */
	public function actionUserauthlogin(){
        $jsonData = file_get_contents("php://input");
        $params = json_decode($jsonData,true);
        $data = array(
            'params' => $params,
        );
        return $this->apiResponse($data,self::SUCCESS);
    }
}
