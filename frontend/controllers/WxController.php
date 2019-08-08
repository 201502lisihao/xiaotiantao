<?php

namespace frontend\controllers;

use common\models\WxUserModel;
use frontend\controllers\base\BaseController;
use frontend\service\WxService;
use frontend\tools\WXBizDataCrypt;
use Yii;

/**
 * 小甜桃服务端-微信小程序Api
 */
class WxController extends BaseController
{

    const SUCCESS = 1;
    public $enableCsrfValidation = false; //禁用csrf，否则取不到post参数

    /*
     * login
     * @params utoken 用户唯一登录标识
     * @params code
     * @params encryptedData
     * @params iv
     */
    public function actionUserauthlogin()
    {
        //获取post请求来的json，并转成数组，获取参数
        $jsonData = file_get_contents('php://input');
        $params = json_decode($jsonData, true);
        $utoken = $params['utoken'];
        $code = $params['code'];
        $encryptedData = $params['encryptedData'];
        $iv = $params['iv'];
        $data = array();
        //var_dump($utoken);exit;
        if (empty($code) || empty($encryptedData) || empty($iv)) {
            $data = array(
                'msg' => '参数异常,请确认请求参数后重新发起请求',
            );
            return $this->apiResponse($data);
        }

        //初始化redis
        $cache = Yii::$app->cache;
        if (!empty($utoken)) {
            //判断缓存是否过期，未过期直接返回utoken
            if ($cache->exists($utoken)) {
                Yii::error('命中缓存 utoken=' . $utoken);
                $data['success'] = self::SUCCESS;
                $data['utoken'] = $utoken;
                return $this->apiResponse($data, self::SUCCESS);
            }
            //去查wx_user表，有数据的话加缓存然后直接返回utoken
            $res = WxUserModel::find()->where(['open_id' => $utoken])->asArray()->one();
            if (!empty($res['id']) && $res['id'] >= 1) {
                Yii::error('命中查库');
                //查到后从新加缓存，减轻数据库压力
                $cache->set($res['open_id'], $res, 86400);

                $data['success'] = self::SUCCESS;
                $data['utoken'] = $res['open_id'];
                return $this->apiResponse($data, self::SUCCESS);
            }
        }
        //缓存和数据库都未查到，去微信api获取，并存库，加缓存
        //获取session_key & open_id
        $wxResponse = WxService::getSessionKey($code);
        if (empty($wxResponse)) {
            $data = array(
                'msg' => '微信登录api响应失败',
            );
            return $this->apiResponse($data);
        }
        $sessionKey = $wxResponse['session_key'];
        $openId = $wxResponse['openid'];

        //解密用户数据，保存在userData中
        $wxCrypt = new WXBizDataCrypt(self::AppId, $sessionKey);
        $decryptCode = $wxCrypt->decryptData($encryptedData, $iv, $userData);

        //直接拿openId当用户的utoken
        if ($decryptCode == 0) {
            $userData = json_decode($userData, true);
            //session_key中可能有反斜杠，昵称中可能有emjoy表情，同意base64一下
            $userData['session_key'] = base64_encode($sessionKey);
            $userData['nickName'] = base64_encode($userData['nickName']);
            //存库
            $res = WxService::addWxUser($userData);
            if ($res) {
                Yii::error('存库成功$userData=' . json_encode($userData));
                $cache->set($openId, $userData, 86400);
                $data = array(
                    'success' => self::SUCCESS,
                    'utoken' => $openId,
                );
                return $this->apiResponse($data, self::SUCCESS);
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
     * 根据经纬度获取最近门店
     * @params longitude 当前经度
     * @params latitude 当前维度
     */
    public function actionGetneareststore($longitude, $latitude)
    {
        $nearestStoreInfo = WxService::getNearestStore($longitude, $latitude);
        $data = array(
            'msg' => '服务器联通成功',
            'nearest_store_info' => $nearestStoreInfo,
        );
        return $this->apiResponse($data, self::SUCCESS);
    }
}
