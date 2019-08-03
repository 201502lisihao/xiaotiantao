<?php

namespace frontend\controllers;

use frontend\controllers\base\BaseController;
use frontend\tools\WxBizDataCrypt;
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

    const AppId = 'wx8ab7f049e4f4bee3';
    const AppSecret = '9c62768747737a8b29c87eca90c8d9cd';
    const GrantType = 'authorization_code';
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
		$data = array(
			'code' => $response->getStatusCode(),
            'header' => $response->getHeaderLine('content-type'),
		);
		return $this->apiResponse($data,self::SUCCESS);
	}

	/*
	 * login
     * @params utoken 用户唯一登录标识
     * @params code 
     * @params encryptedData 
     * @params iv
	 */
	public function actionUserauthlogin(){
        //获取post请求来的json，并转成数组，获取参数
        $jsonData = file_get_contents('php://input');
        $params = json_decode($jsonData,true);
        $utoken = $params['utoken'];
        $code = $params['code'];
        $encryptedData = $params['encryptedData'];
        $iv = $params['iv'];
        
        //if(empty($code) || empty($encryptedData) || empty($iv)){
        if(true){
            $data = array(
                'msg' => '参数异常,请确认请求参数后重新发起请求',
            );
            return $this->apiResponse($data);    
        }

        $data = array();

        //初始化redis
        $cache = Yii::$app->cache;
        //判断缓存是否过期，未过期直接返回utoken
        if( ! empty($utoken) && $cache->get($utoken)){
            $data['success'] = self::SUCCESS;
            $data['utoken'] = $utoken;
            return $this->apiResponse($data,self::SUCCESS);
        }
        
        //获取session_key
        $wxResponse = $this->getSessionKey($code);
        $sessionKey = $wxResponse['session_key'];

        //解密用户数据，保存在userData中
        $wxCrypt = new WxBizDataCrypt(self::AppId,$session_key);
        $decryptCode = $wxCrypt->decryptData($encryptedData, $iv, $userData);
        
        //生成新的utoken
        $newUtoken = md5(uniqid(md5(microtime(true)),true));
        
        if($decryptCode == 0){
            $userData = jsondecode($userData,true);
            $data['success'] = 1;
            $data['utoken'] = $newUtoken;
            $userId = $this->addWxUser($userData);
            if($userId < 1 || empty($userId)){
                
            }
        } else {
            
        }

        return $this->apiResponse($data,self::SUCCESS);
    }

    /*
     * @params code
     * @return ret 包含openid、session_key、unionid、errcode、errmsg
     */
    private function getSessionKey($code){
        $client = new \GuzzleHttp\Client();
        $ret = $client->request('GET', 'https://api.weixin.qq.com/sns/jscode2session?appid=' .self::AppId. '&secret=' .self::AppSecret. '&js_code=' .$code. '&grant_type=' . self::GrantType);
        if($ret['errcode'] != 0){
            $data = array(
                'msg' => '微信端响应失败,具体原因：'.$ret['errmsg'],
            );
            return $this->apiResponse($data);
        }
        return $ret;
    }
}
