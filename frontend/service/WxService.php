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
    public static function test()
    {
        return 'test';
    }

    /*
     * @params code
     * @return ret 包含openid、session_key、unionid、errcode、errmsg
     */
    public static function getSessionKey($code)
    {
        $client = new \GuzzleHttp\Client();
        $resObject = $client->request('GET', 'https://api.weixin.qq.com/sns/jscode2session?appid=' . self::AppId . '&secret=' . self::AppSecret . '&js_code=' . $code . '&grant_type=' . self::GrantType);
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
}