<?php

namespace frontend\controllers;

use frontend\controllers\base\BaseController;
use frontend\tools\WXBizDataCrypt;
use common\models\WxUserModel;
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

    public function actionModeltest(){
        //查询
        $res = WxUserModel::find()->where(['open_id' => '3'])->asArray()->one();
        var_dump($res);
        exit;
        //保存或更新
        $model = new WxUserModel();
        $model->open_id = '3';
        $model->session_key = '3';
        $model->nickname = 'test';
        $model->gender = 't';
        $model->language = 'test';
        $model->city = 'test';
        $model->province = 'test';
        $model->country = 'test';
        $model->headimg = 'test';
        $model->add_time = time();
        $res = $model->save();
        return $this->apiResponse($res,1);
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
        $data = array();

        if(empty($code) || empty($encryptedData) || empty($iv)){
            $data = array(
                'msg' => '参数异常,请确认请求参数后重新发起请求',
            );
            return $this->apiResponse($data);    
        }

        //初始化redis
        $cache = Yii::$app->cache;
        //判断缓存是否过期，未过期直接返回utoken
        if( ! empty($utoken) && $cache->get($utoken)){
            $data['success'] = self::SUCCESS;
            $data['utoken'] = $utoken;
            return $this->apiResponse($data,self::SUCCESS);
        }
        //去查wx_user表，有数据的话加缓存然后直接返回utoken
        $res = WxUserModel::find()->where(['open_id' => $utoken])->asArray()->one();
        if( ! empty($res['id']) && $res['id'] >= 1){
            //查到后从新加缓存，减轻数据库压力
            $cache->set($res['open_id'],$res,86400);

            $data['success'] = self::SUCCESS;
            $data['utoken'] = $res['open_id'];
            return $this->apiResponse($data,self::SUCCESS);
        }
        
        //缓存和数据库都未查到，去微信api获取，并存库，加缓存
        //获取session_key & open_id
        $wxResponse = $this->getSessionKey($code);
        $sessionKey = $wxResponse['session_key'];
        $openId = $wxResponse['openid'];

        //解密用户数据，保存在userData中
        $wxCrypt = new WXBizDataCrypt(self::AppId,$sessionKey);
        $decryptCode = $wxCrypt->decryptData($encryptedData, $iv, $userData);
        
        //直接拿openId当用户的utoken
        if($decryptCode == 0){
            $userData = json_decode($userData,true);
            $userData['session_key'] = $sessionKey;
            //var_dump($userData);exit;
            //存库
            $res = $this->addWxUser($userData);
            if($res){
                $cache->set($openId,$userData,86400);
                $data = array(
                    'success' => self::SUCCESS,
                    'utoken' => $openId,
                );
                return $this->apiResponse($data,self::SUCCESS);
            } else {
                //存库失败,打日志
                echo '存库失败';
            }
        } else {
            //用户数据解密失败,打日志
        }
        return $this->apiResponse($data);
    }

    /*
     * @params code
     * @return ret 包含openid、session_key、unionid、errcode、errmsg
     */
    private function getSessionKey($code){
        $client = new \GuzzleHttp\Client();
        $resObject = $client->request('GET', 'https://api.weixin.qq.com/sns/jscode2session?appid=' .self::AppId. '&secret=' .self::AppSecret. '&js_code=' .$code. '&grant_type=' . self::GrantType);
        $resJson = $resObject->getBody();
        $ret = json_decode($resJson,true);
        if(empty($ret['session_key']) || empty($ret['openid'])){
            $data = array(
                'msg' => '微信端响应失败,具体原因：'.$ret['errmsg'],
            );
            return $this->apiResponse($data);
        }
        return $ret;
    }

    /*
     *
     */
    private function addWxUser($userData){
        $model = new WxUserModel();
        $model->open_id = $userData['openId'];
        $model->session_key = $userData['session_key'];
        $model->nickname = $userData['nickName'];
        $model->gender = $userData['gender'];
        $model->language = $userData['language'];
        $model->city = $userData['city'];
        $model->province = $userData['province'];
        $model->country = $userData['country'];
        $model->headimg = $userData['avatarUrl'];
        $model->add_time = time();
        $res = $model->save();
        return $res;
    }
}
