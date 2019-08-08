<?php

namespace frontend\service;

use common\models\WxStoreModel;
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
        if(empty($storesArr)){
            return [];
        }
        //return $storesArr;
        //计算距离，返回最近的门店信息
        $minDistance = 99.00;
        $nearestKey = 0;
        foreach ($storesArr as $key => $store) {
            if (!empty($store['store_distance']) && $store['store_distance'] < $minDistance) {
                $minDistance = $store['store_distance'];
                $nearestKey = $key;
            }
        }
        $nearestStoreInfo = $storesArr[$nearestKey];
        return $nearestStoreInfo;
    }

    /*
     * 获取附近所有的store信息以及距离
     * @distance 距离，默认5km
     * 6371km 地球半径
     */
    public static function getStores($longitude, $latitude, $distance = 5)
    {
        $dlng = 2 * asin(sin($distance / (2 * 6371)) / cos(deg2rad($latitude)));
        $dlng = rad2deg($dlng);

        $dlat = $distance / 6371;
        $dlat = rad2deg($dlat);
        //正方形四个角的坐标
        $squareArr = array(
            'left-top' => array('lat' => round($latitude + $dlat, 5), 'lng' => round($longitude - $dlng, 5)),
            'right-top' => array('lat' => round($latitude + $dlat, 5), 'lng' => round($longitude + $dlng, 5)),
            'left-bottom' => array('lat' => round($latitude - $dlat, 5), 'lng' => round($longitude - $dlng, 5)),
            'right-bottom' => array('lat' => round($latitude - $dlat, 5), 'lng' => round($longitude + $dlng, 5)),
        );
        //获取需要查询店铺的经度和维度范围
        $minLongitude = $squareArr['left-bottom']['lng'];
        $maxLongitude = $squareArr['right-bottom']['lng'];
        $minLatidute = $squareArr['left-bottom']['lat'];
        $maxLatidute = $squareArr['left-top']['lat'];
        //数据库中获取附近门店
        $storesArr = WxStoreModel::findBySql("select * from wx_store where longitude >= " . $minLongitude ." and longitude <= " . $maxLongitude . " and latitude >= " . $minLatidute . " and latitude <= " . $maxLatidute . ";")->asArray()->all();
        if (empty($storesArr)) {
            return [];
        }
        //计算附近门店到用户的距离
        foreach ($storesArr as $id => $storeInfo) {
            $storeDistance = self::getDistance($storeInfo['longitude'], $storeInfo['latitude'], $longitude, $latitude);
            $storesArr[$id]['store_distance'] = $storeDistance;
        }
        return $storesArr;
    }

    /*
     * 获取两组经纬度直接的距离
     * @params longitude1 latitude1 店铺经纬度
     * @params longitude2 latitude2 用户当前经纬度
     */
    private function getDistance($longitude1, $latitude1, $longitude2, $latitude2)
    {
        //将角度转为狐度
        $radLng1 = deg2rad($longitude1);
        $radLng2 = deg2rad($longitude2);
        $radLat1 = deg2rad($latitude1);
        $radLat2 = deg2rad($latitude2);

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6371;
        return round($distance, 2);

    }
}
