<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use common\models\WxUserModel;
use common\models\PostsModel;

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
                        'actions' => ['logout', 'index', 'user', 'news', 'deluser','delnews','checknews','looknews'],
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

    //用户管理
    public function actionUser(){
        $model = new WxUserModel();
        $result = $model->find()->asArray()->all();
        //var_dump($result);exit;
        return $this->render('user',['data' => array_reverse($result)]);
    }
    //删除用户
    public function actionDeluser($id){
        $model = new WxUserModel();
        $query = $model->find()->where(['id' => $id])->one();
        if (!empty($query)) {
            $query->delete();
        }
        return $this->actionUser();
    }
    //新闻管理
    public function actionNews(){
        $model = new PostsModel();
        $result = $model->find()->select(['id','title','summary','label_img','is_valid','user_name'])->asArray()->all();
        //var_dump($result);exit;
        return $this->render('news',['data' => $result]);
    }
    //删除新闻 增删改---删除
    public function actionDelnews($id){
        $model = new PostsModel();
        $query = $model->find()->where(['id' => $id])->one();
        if (!empty($query)) {
            $query->delete();
        }
        return $this->actionNews();
    }
    //审核新闻
    public function actionChecknews($id){
        $model = new PostsModel();
        $query = $model->find()->where(['id' => $id])->one();
        //完成审核
        $query->is_valid = 1;
        $query->save();
        return $this->actionNews();
    }
    //查看新闻
    public function actionLooknews($id){
        $model = new PostsModel();
        $query = $model->find()->where(['id' => $id])->asArray()->one();
        return $this->render('look',['post' => $query]);
    }

}
