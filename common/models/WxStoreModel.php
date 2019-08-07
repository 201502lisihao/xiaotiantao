<?php

namespace common\models;

use Yii;
use common\models\base\BaseModel;

/**
 * This is the model class for table "wx_store".
 *
 * @property int $id
 * @property string $store_name 店铺名称
 * @property string $store_addr 店铺地址
 * @property string $store_time 营业时间
 * @property string $longitude 经度
 * @property string $latitude 维度
 * @property int $create_at 记录插入时间
 */
class WxStoreModel extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wx_store';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'store_name', 'create_at'], 'required'],
            [['id', 'create_at'], 'integer'],
            [['store_name', 'store_addr', 'store_time', 'longitude', 'latitude'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'store_name' => Yii::t('app', 'Store Name'),
            'store_addr' => Yii::t('app', 'Store Addr'),
            'store_time' => Yii::t('app', 'Store Time'),
            'longitude' => Yii::t('app', 'Longitude'),
            'latitude' => Yii::t('app', 'Latitude'),
            'create_at' => Yii::t('app', 'Create At'),
        ];
    }
}
