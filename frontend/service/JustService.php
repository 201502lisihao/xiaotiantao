<?php

namespace frontend\service;

use common\models\JustSuggestModel;
use common\models\JustTicketModel;
use common\models\JustUserModel;
use frontend\service\base\WxBaseService;
use Yii;

/*
 * 微信小程序service层
 */

class JustService extends WxBaseService
{
    const WxGetOpenIdUrl = 'https://api.weixin.qq.com/sns/jscode2session';//微信获取openId和session_key的url
    const AppId = 'wx74d768b469903a23';
    const AppSecret = 'abf836a0dea96975ef43dcc423070fdc';
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
     * 保存或更新just_wx_user表
     * @param $userData
     * @return int
     */
    public static function addWxUser($userData)
    {
        //参数校验
        if (empty($userData['openId'])) {
            return 0;
        }
        $res = JustUserModel::find()->where(['open_id' => $userData['openId']])->one();
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
            $model = new JustUserModel();
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
        $res = JustUserModel::find()->where(['id' => '31'])->one();
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
     * 保存用户建议
     */
    public static function saveSuggest($utoken, $suggest, $contact)
    {
        //加锁，防止重复提交
        $cache = Yii::$app->cache;
        $key = $utoken . '_suggest';
        if($cache->exists($key)){
            return 0;
        }else{
            $cache->set($key, 'locked', 1);
        }

        $model = new JustSuggestModel();
        $model->open_id = $utoken;
        $model->suggest = $suggest;
        $model->contact = $contact;
        $model->add_time = time();
        if ($model->save(false)) {
            $ret = $model->attributes['id'];
        } else {
            $ret = 0;
        }
        return $ret;
    }

    /**
     * 小程序通知用户生成海报完成，奖励抽奖券
     */
    public static function createTicket($params)
    {
        if (!isset($params['userId'])) {
            return 0;
        }
        //下单锁，每人1次奖券海报获取机会
        //一个月有效期
        $restOfTime = 2592000;
        $cache = Yii::$app->cache;
        $key = $params['userId'] . '_get_ticket_by_post';
        if ($cache->get($key)) {
            return 0;
        } else {
            $cache->set($key, 1, $restOfTime);
        }

        //取当前奖券发到多少号了,自增
        if($cache->get('ticket_cache_id')){
            $ticketCacheId = $cache->get('ticket_cache_id');
        } else {
            $ticketCacheId = 0;
        }
        $currentTicketCacheId = $ticketCacheId + 1;
        $cache->set('ticket_cache_id', $currentTicketCacheId, $restOfTime);


        $model = new JustTicketModel();
        $model->ticket_code = self::createTicketCode($currentTicketCacheId);
        $model->user_id = $params['userId'];
        $model->channel = $params['channel'];
        $model->create_at = time();
        //以下暂时写死
        $model->ticket_status = 1; //1有效、2无效

        //执行存库
        if ($model->save(false)) {
            $ret = $model->attributes['id'];
        } else {
            $ret = 0;
        }
        return $ret;
    }

    /**
     * 生成奖券号码
     */
    private function createTicketCode($ticketCacheId)
    {
        $strs="QWERTYUIOPASDFGHJKLZXCVBNM";
        $salt=substr(str_shuffle($strs),mt_rand(0,strlen($strs)-3), 2);
        $code = sprintf("%08d", 2);
        return $salt . $code;
    }

    /**
     * 根据user_id获取订单列表
     * @param $userId
     * @return array
     */
    public static function getRaffleticketListByUserId($userId)
    {
        $resArr = JustTicketModel::find()->where(['user_id' => $userId])->asArray()->all();
        //返回的时候将数组倒叙
        return array_reverse($resArr);
    }
}
