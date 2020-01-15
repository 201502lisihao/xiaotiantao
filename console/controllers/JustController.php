<?php

namespace console\controllers;

use yii\console\Controller;
use Yii;

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
        while ($flag && $minute <= 58){
            //请求微信获取accessToken
            //https请求方式: GET https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET
            $client = new \GuzzleHttp\Client();
            $resObject = $client->request('GET', self::WxGetAccessTokenUrl . '?grant_type=client_credential&appid=' . self::AppId .'&secret=' . self::AppSecret);
            $resJson = $resObject->getBody();
            $response = json_decode($resJson, true);
            if (!empty($response['access_token'])){
                $accessToken = $response['access_token'];
                echo $accessToken;
                //存入缓存
                $cache = Yii::$app->cache;
                $cache->set('wx_access_token', $accessToken, 7200);
                $flag = false;
                continue;
            }
            sleep(60);
        }
        echo "缓存access_token脚本完成\n";
    }
}
