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
                        'actions' => ['logout', 'index', 'just', 'deljustuser', 'suggest', 'dealsuggest', 'deletesuggest'],
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
    public function actionJust()
    {
        $model = new JustUserModel();
        $result = $model->find()->asArray()->all();
        //var_dump($result);exit;
        return $this->render('just',['data' => array_reverse($result)]);
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
     * 删除用户
     */
    public function actionDeljustuser($id){
        $model = new JustUserModel();
        $query = $model->find()->where(['id' => $id])->one();
        if (!empty($query)) {
            $query->delete();
        }
        return $this->actionJust();
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

//    //新闻管理
//
//    public function actionDelnews($id){
//        $model = new PostsModel();
//        $query = $model->find()->where(['id' => $id])->one();
//        if (!empty($query)) {
//            $query->delete();
//        }
//        return $this->actionNews();
//    }
//
//    //删除新闻 增删改---删除
//
//    public function actionNews()
//    {
//        $model = new PostsModel();
//        $result = $model->find()->select(['id', 'title', 'summary', 'label_img', 'is_valid', 'user_name'])->asArray()->all();
//        //var_dump($result);exit;
//        return $this->render('news', ['data' => $result]);
//    }
//
    //审核新闻

    public function actionDealsuggest($id){
        $model = new JustSuggestModel();
        $query = $model->find()->where(['id' => $id])->one();
        //完成审核
        $query->status = 1;
        $query->save();
        return $this->actionSuggest();
    }
//
//    //查看新闻
//    public function actionLooknews($id){
//        $model = new PostsModel();
//        $query = $model->find()->where(['id' => $id])->asArray()->one();
//        return $this->render('look',['post' => $query]);
//    }

}
