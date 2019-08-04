<?php

namespace common\models;

use Yii;
use common\models\base\BaseModel;

/*
 * This is the model class for table "wx_user".
 * @property int $id
 * @property string $open_id
 * @property string $nickname
 * @property string $gender
 * @property string $language
 * @property string $city
 * @property string $province
 * @property string $country
 * @property string $headimg
 * @property int $add_time
 */
class WxUserModel extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wx_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['open_id', 'nickname', 'language', 'city', 'province', 'country', 'headimg'], 'required'],
            [['add_time'], 'integer'],
            [['open_id', 'headimg'], 'string', 'max' => 255],
            [['nickname', 'city', 'province', 'country'], 'string', 'max' => 200],
            [['gender'], 'integer', 'max' => 1],
            [['language'], 'string', 'max' => 30],
            [['open_id'], 'unique'],
            [['session_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'open_id' => Yii::t('app', 'Open ID'),
            'session_key' => Yii::t('app', 'Session_Key'),
            'nickname' => Yii::t('app', 'Nickname'),
            'gender' => Yii::t('app', 'Gender'),
            'language' => Yii::t('app', 'Language'),
            'city' => Yii::t('app', 'City'),
            'province' => Yii::t('app', 'Province'),
            'country' => Yii::t('app', 'Country'),
            'headimg' => Yii::t('app', 'Headimg'),
            'add_time' => Yii::t('app', 'Add Time'),
        ];
    }
}
