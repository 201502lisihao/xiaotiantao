<?php

namespace frontend\service;

use common\models\YisaiOrdersModel;
use common\models\YisaiWxUserModel;
use frontend\service\base\WxBaseService;
use Yii;

/*
 * 伊赛微信小程序service层
 */

class YisaiService extends WxBaseService
{
    const WxGetOpenIdUrl = 'https://api.weixin.qq.com/sns/jscode2session';//微信获取openId和session_key的url
    const AppId = 'wx4de1abc18a84ed98';
    const AppSecret = '905507cd45656bb15e73fd3638a0a345';
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
        $res = YisaiWxUserModel::find()->where(['open_id' => $userData['openId']])->one();
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
            $model = new YisaiWxUserModel();
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
     * @return int
     * 获取使用次数
     */
    public static function getUseNumber()
    {
        //累计人数表也在wx_user表中，以一条记录的形式存在 city字段记录人数
        $res = YisaiWxUserModel::find()->where(['id' => '31'])->one();
        if ($res) {
            $useNumber = $res->city;
            //调用就代表使用次数+1再存进去
            $res->city = ++$useNumber;
            if (!$res->save(false)) {
                $useNumber = 0;
            }
        } else {
            $useNumber = 0;
        }
        return $useNumber;
    }

    /**
     * 下单（转发得积分）
     */
    public static function createOrder($params)
    {
        if (!isset($params['userId'])) {
            return 0;
        }
        //下单锁，防止重复下单
        $cache = Yii::$app->cache;
        $key = $params['userId'] . '_create_order';
        if ($cache->exists($key)) {
            return 0;
        } else {
            $cache->set($key, 'lock', 1);
        }

        $model = new YisaiOrdersModel();
        $model->order_no = self::createOrderNo();
        $model->user_id = $params['userId'];
        $model->apply_from = $params['applyFrom'];
        $model->award_point = $params['awardPoint'];
        $model->order_detail = serialize($params['orderDetail']);
        $model->create_at = time();
        //以下暂时写死
        $model->order_status = '待核销';
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
     * 根据user_id获取订单列表
     * @param $userId
     * @return array
     */
    public static function getOrderListByUserId($userId)
    {
        $resArr = YisaiOrdersModel::find()->where(['user_id' => $userId])->asArray()->all();
        $orderList = array();
        //转换时间格式
        foreach ($resArr as $order) {
            $order['create_at'] = date('Y-m-d H:i', $order['create_at']);
            $orderList[] = $order;
        }
        //返回的时候将数组倒叙
        return array_reverse($orderList);
    }

    /**
     * 更改积分订单状态为已核销
     */
    public static function orderExchange($orderId)
    {
        $res = YisaiOrdersModel::find()->where(['id' => $orderId])->one();
        if (!$res) {
            return 0;
        }
        $res->order_status = '已核销';
        if ($res->save(false)) {
            $ret = $res->id;
        } else {
            $ret = 0;
        }
        return $ret;
    }

    /**
     * 批量核销积分
     */
    public static function batchCunsume($userId, $point)
    {
        $ret = false;
        $ordersObj = YisaiOrdersModel::find()->where(['user_id' => $userId, 'order_status' => '待核销'])->all();
        if (count($ordersObj) >= $point) {
            for ($i = 0; $i < $point; $i++) {
                $ordersObj[$i]->order_status = '已核销';
                $ordersObj[$i]->save(false);
            }
            $ret = true;
            return $ret;
        } else {
            return $ret;
        }

    }
}

