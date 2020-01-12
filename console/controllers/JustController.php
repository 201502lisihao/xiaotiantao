<?php

namespace console\controllers;

use http\Client;
use yii\console\Controller;

class JustController extends Controller{
    const WxGetAccessTokenUrl = "https://api.weixin.qq.com/cgi-bin/token";
    const AppId = 'wx74d768b469903a23';
    const AppSecret = 'abf836a0dea96975ef43dcc423070fdc';

    public function actionTest(){
        echo "test\n";
    }


    // 定时获取access_token并刷新缓存
    // 0 */1 * * * php /root/xiaotiantao/yii just/cacheaccesstoken
    public function actionCacheaccesstoken(){
        $flag = true;
        $minute = date('i', time());
        while ($flag && $minute <= 10){
            //请求微信获取accessToken
            //https请求方式: GET https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET
            $client = new Client();
            $response = $client->request('GET', self::WxGetAccessTokenUrl . '?grant_type=client_credential&appid=' . self::AppId .'&secret=' . self::AppSecret);
            if (!empty($response['access_token'])){
                $accessToken = $response['access_token'];
                //存入缓存
                $cache = Yii::$app->cahce;
                $cache->set('wx_access_token', $accessToken);
                $flag = false;
            }
        }
    }
}