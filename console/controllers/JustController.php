<?php

namespace console\controllers;

use common\models\JustTicketModel;
use common\models\JustUserModel;
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

    // 2020年1月31日20:00开奖
    // 0 20 31 1 * php /root/xiaotiantao/yii just/cachewinresult
    public function actionCachewinresult(){
        $time = 1580472000;
        $flag = true;
        $num = 0;
            
        while($flag){
            if(10 < ++$num){
                break;
            }
            Yii::info('新年开奖脚本第' . $num . '次执行。');

            $result = JustTicketModel::find()->where(['<=', 'create_at', $time])->asArray()->all();
            if( ! empty($result)){
                shuffle($result);
                $winner = $result[0];
                $code = $winner['ticket_code'];
                $userId = $winner['user_id'];
                //查用户信息
                $userInfo = JustUserModel::find()->where(['id' => $userId])->asArray()->one();
                if( ! empty($userInfo)){
                    $nickname = $userInfo['nickname'];
                    $headimg = $userInfo['headimg'];
                    //放入缓存
                    $val = serialize(array(
                        'win_code' => $code,
                        'win_user_nickname' => $nickname,
                        'win_user_img_path' => $headimg
                    ));
                    var_dump($val);
                    $cache = Yii::$app->cache;
                    if($cache->set('winner_info_2020', $val, 30*86400)){
                        $flag = false;
                    }
                }
                
            }            
            sleep(10);
        }

        if($flag){
            Yii::info('[开奖脚本] 执行失败，执行' . $num . '次');
        } else {
            Yii::info('[开奖脚本] 执行成功!');
        }    
    }
}
