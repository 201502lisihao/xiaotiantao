<?php

namespace frontend\service;

use common\models\WxGoodsModel;
use common\models\WxOrdersModel;
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

    /**
     * @param $code
     * @return array|mixed 包含openid、session_key、unionid、errcode、errmsg
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

    /**
     * 创建订单
     */
    public static function createOrder($params)
    {
        if (!count($params)) {
            return 0;
        }
        $orderNo = self::createOrderNo();
        $storeName = $params['storeName'];

        $model = new WxOrdersModel();
        $model->order_no = $orderNo;
        $model->get_no = self::createGetNo($storeName, $orderNo);
        $model->user_id = $params['userId'];
        $model->store_name = $storeName;
        $model->order_detail = $params['cartList'];
        $model->price = $params['sumMonney'];
        $model->cut_money = $params['cutMoney'];
        $model->create_at = time();
        //以下暂时写死
        $model->order_status = '待支付';
        $model->get_time = 0;
        $model->type = '普通单';
        //执行存库
        if ($model->save(false)) {
            $ret = $model->attributes['id'];
        } else {
            $ret = 0;
        }
        return $ret;
    }

    /**
     * 生成订单号
     */
    private function createOrderNo()
    {
        $prefix = time();
        return $prefix . rand(1000, 9999);
    }

    /**
     * 根据门店生成自己的取单号前缀
     */
    private function createGetNo($storeName, $orderNo)
    {
        //前缀为A的店铺
        $aArray = array(
            '小甜桃(测试店)'
        );
        if (in_array($storeName, $aArray)) {
            return 'A' . substr($orderNo, 10, 4);
        }
    }

    /**
     * 保存或更新wx_user表
     * @param $userData
     * @return int
     */
    public static function addWxUser($userData)
    {
        //参数校验
        if (empty($userData['openId'])) {
            return 0;
        }
        $res = WxUserModel::find()->where(['open_id' => $userData['openId']])->one();
        if ($res) {
            //先查库，有的话则更新并返回id
            $res->nickname = $userData['nickName'];
            $res->gender = $userData['gender'];
            $res->language = $userData['language'];
            $res->city = $userData['city'];
            $res->province = $userData['province'];
            $res->headimg = $userData['avatarUrl'];
            if ($res->save(false)) {
                $ret = $res->id;
            } else {
                $ret = 0;
            }
        } else {
            //否者插入，并返回id
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
            if ($model->save(false)) {
                $ret = $model->attributes['id'];
            } else {
                $ret = 0;
            }
        }
        return $ret;
    }

    /**
     * 根据经纬度获取最近的门店
     * @param $longitude
     * @param $latitude
     * @return array|mixed
     */
    public static function getNearestStore($longitude, $latitude)
    {
        //获取附近的门店
        $storesArr = self::getNearlyStores($longitude, $latitude);
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

    /**
     * 获取附近所有的store信息以及距离，默认范围5km
     * @param $longitude
     * @param $latitude
     * @param int $distance
     * @return array
     */
    public static function getNearlyStores($longitude, $latitude, $distance = 10)
    {
        $dlng = 2 * asin(sin($distance / (2 * 6371)) / cos(deg2rad($latitude)));
        $dlng = rad2deg($dlng);

        $dlat = $distance / 6371;
        $dlat = rad2deg($dlat);
        //正方形四个角的坐标,经纬度小数点后6位，精确度1米
        $squareArr = array(
            'left-top' => array('lat' => round($latitude + $dlat, 6), 'lng' => round($longitude - $dlng, 6)),
            'right-top' => array('lat' => round($latitude + $dlat, 6), 'lng' => round($longitude + $dlng, 6)),
            'left-bottom' => array('lat' => round($latitude - $dlat, 6), 'lng' => round($longitude - $dlng, 6)),
            'right-bottom' => array('lat' => round($latitude - $dlat, 6), 'lng' => round($longitude + $dlng, 6)),
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

    /**
     * 获取距离，保留小数点后2位，单位是km
     * @param $longitude1 门店经纬度
     * @param $latitude1
     * @param $longitude2 用户当前经纬度
     * @param $latitude2
     * @return float
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

    public static function getGoodsListByStoreList($storeId)
    {
        $goodslist = WxGoodsModel::find()->where(['store_id' => $storeId])->asArray()->all();
        $retList = array();
        foreach ($goodslist as $key => $good) {
            $retList[$good['type_name']]['name'] = $good['type_name'];
            $retList[$good['type_name']]['goods'][] = $good;
        }
        $retList = array_values($retList);
        return $retList;
    }

    public static function getOrderListByUserId($userId)
    {
        $resArr = WxOrdersModel::find()->where(['user_id' => $userId])->asArray()->all();
        $orderList = array();
        foreach($resArr as $order){
            $order['create_at'] = date('Y-m-d H:i', $order['create_at']);
            if($order['get_time']){
                $order['get_time'] = date('Y-m-d H:i', $order['get_time']);
            }
            $orderList[] = $order;
        }

        return $orderList;
    }
}
