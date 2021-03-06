<?php

namespace frontend\controllers;

use common\models\JustUserModel;
use frontend\controllers\base\BaseController;
use frontend\service\JustService;
use frontend\tools\WXBizDataCrypt;
use Yii;

/**
 * Just清单服务端-微信小程序Api
 */
class JustController extends BaseController
{

    const FAIL = 0;
    const AppId = 'wx74d768b469903a23';
    public $enableCsrfValidation = false; //禁用csrf，否则取不到post参数

    /**
     * login
     * @params utoken 用户唯一登录标识
     * @params code
     * @params encryptedData
     * @params iv
     */
    public function actionUserauthlogin()
    {
        //获取post请求来的json，并转成数组，获取参数
        $jsonData = file_get_contents('php://input');
        $params = json_decode($jsonData, true);
        $utoken = $params['utoken'];
        $code = $params['code'];
        $encryptedData = $params['encryptedData'];
        $iv = $params['iv'];
        $data = array();
        if (empty($code) || empty($encryptedData) || empty($iv)) {
            Yii::error('【用户登录】小程序端传参异常');
            $data = array(
                'msg' => '参数异常,请确认请求参数后重新发起请求',
            );
            return $this->apiResponse($data, self::FAIL);
        }

        //初始化redis
        $cache = Yii::$app->cache;
        if (!empty($utoken)) {
            //判断缓存是否过期，未过期直接返回utoken
            if ($cache->exists($utoken)) {
                Yii::error('命中缓存 utoken=' . $utoken);
                $data = array(
                    'utoken' => $utoken,
                    'user_id' => $cache->get($utoken)
                );
                return $this->apiResponse($data);
            }
            //去查wx_user表，有数据的话加缓存然后直接返回utoken
            $res = JustUserModel::find()->where(['open_id' => $utoken])->asArray()->one();
            if (!empty($res['id']) && $res['id'] >= 1) {
                Yii::error('命中查库');
                //查到后从新加缓存，减轻数据库压力
                $cache->set($res['open_id'], $res['id'], 86400);
                $data = array(
                    'utoken' => $res['open_id'],
                    'user_id' => $res['id']
                );
                return $this->apiResponse($data);
            }
        }
        //缓存和数据库都未查到，去微信api获取，并存库，加缓存
        //获取session_key & open_id
        $wxResponse = JustService::getSessionKey($code);
        if (empty($wxResponse)) {
            Yii::error('微信登录api响应失败');
            $data = array(
                'msg' => '微信登录api响应失败',
            );
            return $this->apiResponse($data, self::FAIL);
        }
        $sessionKey = $wxResponse['session_key'];
        $openId = $wxResponse['openid'];

        //解密用户数据，保存在userData中
        //$userData = '';
        $wxCrypt = new WXBizDataCrypt(self::AppId, $sessionKey);
        $decryptCode = $wxCrypt->decryptData($encryptedData, $iv, $userData);

        //直接拿openId当用户的utoken
        if ($decryptCode == 0) {
            $userData = json_decode($userData, true);
            //session_key中可能有反斜杠，昵称中可能有emjoy表情，同意base64一下
            $userData['session_key'] = base64_encode($sessionKey);
            $userData['nickName'] = base64_encode($userData['nickName']);
            //存库,成功返回id，失败返回0
            $res = JustService::addWxUser($userData);
            if ($res) {
                Yii::error('存库成功$userData=' . json_encode($userData));
                $cache->set($openId, $res, 86400);
                $data = array(
                    'utoken' => $openId,
                    'user_id' => $res
                );
                return $this->apiResponse($data);
            } else {
                Yii::error('JustService::addWxUser存库失败');
            }
        } else {
            Yii::error('用户数据解密失败,$decryptCode='.$decryptCode);
        }
        return $this->apiResponse($data, self::FAIL);
    }

    /**
     * 保存用户提交的建议
     */
    public function actionSavesuggest()
    {
        //获取post请求来的json，并转成数组，获取参数
        $jsonData = file_get_contents('php://input');
        $params = json_decode($jsonData, true);
        $utoken = $params['utoken'];
        $suggest = $params['suggest'];
        $contact = $params['contact'] ?? '未填写';

        if (empty($utoken) || empty($suggest)) {
            Yii::error('【保存建议】小程序端传参异常');
            $data = array(
                'msg' => '参数异常,请确认请求参数后重新发起请求',
            );
            return $this->apiResponse($data, self::FAIL);
        }

        $res = JustService::saveSuggest($utoken, $suggest, $contact);
        if ($res) {
            $data = array(
                'msg' => '建议保存成功'
            );
        }
        $data = array(
            'msg' => '建议保存失败',
        );
        return $this->apiResponse($data, self::FAIL);
    }

    /**
     * @return 获取使用次数
     */
    public function actionGetusenumber()
    {
        $useNumber = JustService::getUseNumber();
        if (!$useNumber) {
            $data = array();
            return $this->apiResponse($data, self::FAIL);
        }
        $data = array(
            'use_number' => $useNumber,
        );
        return $this->apiResponse($data);
    }

    /**
     * 小程序通知用户生成海报完成，奖励抽奖券接口
     */
    public function actionCreateticket()
    {
        //获取post请求来的json，并转成数组，获取参数
        $jsonData = file_get_contents('php://input');
        $params = json_decode($jsonData, true);
        $userId = $params['userId'] ?? '';
        $channel = $params['channel'] ?? '';
        $data = array();
        if (empty($userId) || empty($channel)){
            $data['msg'] = '传参异常';
            return $this->apiResponse($data, self::FAIL);
        }

        //下单锁，每人1次奖券flag获取机会
        //一个月有效期
        $restOfTime = 2592000;
        $cache = Yii::$app->cache;
        $key = $userId . '_get_ticket_by_post';
        if ($cache->get($key)) {
            $data['msg'] = '该方式每人仅限获得一张奖券';
            return $this->apiResponse($data, self::FAIL);
        }
        $result = JustService::createTicket($userId, $channel);
        $cache->set($key, 1, $restOfTime);

        if ($result) {
            $data = array(
                'orderId' => $result,
                'msg' => '奖券获取成功'
            );
            return $this->apiResponse($data);
        }
        $data['msg'] = '奖券获取失败';
        return $this->apiResponse($data, self::FAIL);
    }

    /**
     * 根据user_id查用户所有订单
     * @params userId
     */
    public function actionGetraffleticketlistbyuserid($userId)
    {
        if (empty($userId)) {
            $data = array(
                'msg' => '传入的userId为空'
            );
            return $this->apiResponse($data, self::FAIL);
        }
        //userId对应wx_user表中id字段
        $raffleTicketList = JustService::getRaffleticketListByUserId($userId);
        if (count($raffleTicketList)){
            $data = array(
                'msg' => '获取奖券成功',
                'raffle_ticket_list' => $raffleTicketList,
            );
            return $this->apiResponse($data);
        }
        $data = array(
            'msg' => '无奖券',
            'raffle_ticket_list' => false
        );
        return $this->apiResponse($data, self::FAIL);
    }

    /**
     * 小程序端获取access_token时调用
     * access_token后端定时刷新，然后放入缓存
     */
    public function actionGetaccesstoken(){
        $data = array();
        $cache = Yii::$app->cache;
        if ($cache->get('wx_access_token')){
            $data['access_token'] = $cache->get('wx_access_token');
        }
        return $this->apiResponse($data);
    }

    //小程序调用获取带参数二维码的接口（新年活动用）
    public function actionGetaqrcodepath(){
        //获取post请求来的json，并转成数组，获取参数
        $jsonData = file_get_contents('php://input');
        $params = json_decode($jsonData, true);
        $scene = $params['scene'] ?? '';
        $page = $params['page'] ?? '';
        $accessToken = $params['access_token'] ?? '';
        $data = array();
        if (empty($scene) || empty($page) || empty($accessToken)){
            $data['msg'] = '传参异常';
            return $this->apiResponse($data, self::FAIL);
        }
        $res = JustService::getAqrCodePath($scene, $page, $accessToken);
        if (is_array($res) && !empty($res['errcode'])){
            $data['msg'] = $res['errmsg'];
            return $this->apiResponse($data, self::FAIL);
        }
        //保存图片到服务器，返回url
        file_put_contents('/home/wwwroot/default/xiaotiantao/frontend/web/statics/images/webImages/'. $scene .'.png', $res);
        $url = 'https://www.qianzhuli.top/statics/images/webImages/' . $scene .'.png';

        $data['url'] = $url;
        return $this->apiResponse($data);
    }

    public function actionFriendhelp(){
        //获取post请求来的json，并转成数组，获取参数
        $jsonData = file_get_contents('php://input');
        $params = json_decode($jsonData, true);
        $friendUserId = $params['friendUserId'] ?? 0;
        $userId = $params['userId'] ?? 0;
        $data = array();
        if (empty($friendUserId) || empty($userId)){
            $data['msg'] = '传参异常';
            return $this->apiResponse($data, self::FAIL);
        }

        $res = JustService::friendHelp($friendUserId, $userId);
        if ($res){
            $data['msg'] = '好友助力各得一张奖券成功';
            return $this->apiResponse($data);
        }
        $data['msg'] = '好友助力各得一张奖券失败';
        return $this->apiResponse($data, self::FAIL);
    }


    /**
     * 获取新年开奖信息
     */
    public function actionGetwininfo() {
        $data = array();

        //test
        //$data['win_code'] = '';
        //$data['win_user_nickname'] = '';
        //$data['win_user_img_path'] = '';
        //return $this->apiResponse($data);

        //从缓存中取开奖结果
        $cache = Yii::$app->cache;
        if ($cache->get('winner_info_2020')){
            $resultArr = @unserialize($cache->get('winner_info_2020'));
            $data['win_code'] = $resultArr['win_code'];
            $data['win_user_nickname'] = base64_decode($resultArr['win_user_nickname']);
            $data['win_user_img_path'] = $resultArr['win_user_img_path'];
            return $this->apiResponse($data);
        }
        $data['msg'] = '获取开奖信息失败';
        return $this->apiResponse($data, self::FAIL);
    }
}
