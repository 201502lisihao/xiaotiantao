<?php

namespace frontend\service;

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
}
