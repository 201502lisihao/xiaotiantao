<?php

namespace frontend\service;

use common\models\WxUserModel;
use frontend\service\base\WxBaseService;
use Yii;

/*
 * 微信小程序service层
 */

class WxService extends WxBaseService
{
    const WxGetOpenIdUrl = 'https://api.weixin.qq.com/sns/jscode2session';//微信获取openId和session_key的url
    const AppId = 'wx8ab7f049e4f4bee3';
    const AppSecret = '9c62768747737a8b29c87eca90c8d9cd';
    const GrantType = 'authorization_code';

    /*
     * @params code
     * @return ret 包含openid、session_key、unionid、errcode、errmsg
     */
    public static function getSessionKey($code)
    {
        $client = new \GuzzleHttp\Client();
        $resObject = $client->request('GET', self::WxGetOpenIdUrl . '?appid=' . self::AppId . '&secret=' . self::AppSecret . '&js_code=' . $code . '&grant_type=' . self::GrantType);
        $resJson = $resObject->getBody();
        $ret = json_decode($resJson, true);
        if (empty($ret['session_key']) || empty($ret['openid'])) {
            Yii::error('微信登录api响应失败，$ret=' . json_encode($ret));
            $ret = [];
        }
        return $ret;
    }

    /*
     * 保存或更新wx_user表
     */
    public static function addWxUser($userData): bool
    {
        //参数校验
        if (empty($userData['openId'])) {
            return false;
        }
        //先查库，有的话则更新
        $res = WxUserModel::find()->where(['open_id' => $userData['openId']])->one();
        if ($res) {
            $res->nickname = $userData['nickName'];
            $res->gender = $userData['gender'];
            $res->language = $userData['language'];
            $res->city = $userData['city'];
            $res->province = $userData['province'];
            $res->headimg = $userData['avatarUrl'];
            $ret = $res->save(false);
        } else {
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
            $ret = $model->save(false);
        }
        return $ret;
    }

    /*
     * 根据经纬度获取最近的门店
     */
    public static function getNearestStore($longitude, $latitude)
    {
        //获取附近的门店
        $storesArr = self::getStores($longitude, $latitude);
        return $storesArr;
        //计算距离，返回最近的门店信息

    }

    /*
     * 获取附近所有的store信息以及距离
     * @distance 半径，默认3km
     * 6371km 地球半径
     */
    public static function getStores($longitude, $latitude, $distance = 3)
    {
        $dlng = 2 * asin(sin($distance / (2 * 6371)) / cos(deg2rad($latitude)));
        $dlng = rad2deg($dlng);

        $dlat = $distance / 6371;
        $dlat = rad2deg($dlat);
        //正方形四个角的坐标
        return array(
            'left-top' => array('lat' => $latitude + $dlat, 'lng' => $longitude - $dlng),
            'right-top' => array('lat' => $latitude + $dlat, 'lng' => $longitude + $dlng),
            'left-bottom' => array('lat' => $latitude - $dlat, 'lng' => $longitude - $dlng),
            'right-bottom' => array('lat' => $latitude - $dlat, 'lng' => $longitude + $dlng)
        );
    }
}