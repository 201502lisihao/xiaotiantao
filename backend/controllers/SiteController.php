<?php
namespace backend\controllers;

use backend\models\LoginForm;
use common\models\JustSuggestModel;
use common\models\JustUserModel;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'justuser', 'deljustuser', 'cache', 'suggest', 'dealsuggest', 'deletesuggest'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    //首页
    public function actionIndex()
    {
        return $this->render('index');
    }

    //登录
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    //退出登录
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    //Just清单用户管理
    public function actionJustuser()
    {
        $model = new JustUserModel();
        $result = $model->find()->orderBy("id DESC")->limit(50)->asArray()->all();
        // 获取最新总数
        $count = $model->find()->count();
        // 获取上次请求的总数
        $cacheKey = "last_time_user_count";
        $cache = Yii::$app->cache;

        $addCount = 0;
        if ($cache->get($cacheKey)){
            $addCount = $count - $cache->get($cacheKey);
        }
        $cache->set($cacheKey, $count);

        //var_dump($result);exit;
        return $this->render('justuser',['data' => array_reverse($result), 'count' => $count, 'add_count' => $addCount]);
    }

    /**
     * 删除用户
     */
    public function actionDeljustuser($id){
        $model = new JustUserModel();
        $query = $model->find()->where(['id' => $id])->one();
        if (!empty($query)) {
            $query->delete();
        }
        return $this->actionJustuser();
    }

    /**
     * 缓存管理
     */
    public function actionCache(){
        var_dump('研究研究怎么获取到cache的所有keys');
        //研究研究怎么获取到cache的所有keys
//        $cache = Yii::$app->cache;
        //获取全部缓存
//        $cacheList = $cache->keys('*');
//        var_dump($cacheList);
//        return $this->render('cache', array('data' => $cacheList));
    }

    //Just清单意见管理
    public function actionSuggest()
    {
        $model = new JustSuggestModel();
        $result = $model->find()->asArray()->all();

        $data = array();
        $userModel = new JustUserModel();
        //给意见填上用户信息
        foreach ($result as $key => $value){
            $data[$key] = $value;
            $openId = $value['open_id'];
            if(empty($data[$key]['contact'])){
                $data[$key]['contact'] = '暂无';
            }
            $userInfo = $userModel->find()->where(['open_id' => $openId])->one();
            if (!empty($userInfo)){
                $data[$key]['headimg'] = $userInfo['headimg'];
                $data[$key]['nickname'] = $userInfo['nickname'];
            }else{
                $data[$key]['headimg'] = 'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKcB66pHIIyetic3nRqJ5Kuq5Y54OntIIJxJAtqjiaUTg6WwAt7JFgOoHICVWpicX6fW0MibHPLduJzzg/132';
                $data[$key]['nickname'] = '匿名用户';
            }
        }
        //var_dump($result);exit;
        return $this->render('suggest',['data' => array_reverse($data)]);
    }

    /**
     * 删除意见
     */
    public function actionDeletesuggest($id){
        $model = new JustSuggestModel();
        $query = $model->find()->where(['id' => $id])->one();
        if(!empty($query)){
            $query->delete();
        }
        return $this->actionSuggest();
    }

}
