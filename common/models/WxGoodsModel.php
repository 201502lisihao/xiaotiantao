<?php

namespace common\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "wx_goods".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $image_url
 * @property string $price
 * @property string $type_name 分类名称
 * @property int $create_at
 */
class WxGoodsModel extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wx_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price'], 'number'],
            [['create_at', 'store_id'], 'integer'],
            [['name', 'description', 'image_url', 'type_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'image_url' => Yii::t('app', 'Image Url'),
            'price' => Yii::t('app', 'Price'),
            'type_name' => Yii::t('app', 'Type Name'),
            'store_id' => Yii::t('app', 'Store Id'),
            'create_at' => Yii::t('app', 'Create At'),
        ];
    }
}
