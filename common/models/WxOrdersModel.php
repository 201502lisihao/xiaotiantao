<?php
namespace common\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "wx_orders".
 *
 * @property int $id
 * @property string $order_no
 * @property int $user_id
 * @property string $order_status
 * @property string $store_name
 * @property int $get_time
 * @property int $type
 * @property double $price
 * @property int $create_at
 */
class WxOrdersModel extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wx_orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_no', 'create_at'], 'required'],
            [['id', 'user_id', 'get_time', 'create_at'], 'integer'],
            [['price'], 'number'],
            [['order_no', 'order_status', 'store_name'], 'string', 'max' => 255],
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
            'order_no' => Yii::t('app', 'Order No'),
            'user_id' => Yii::t('app', 'User ID'),
            'order_status' => Yii::t('app', 'Order Status'),
            'store_name' => Yii::t('app', 'Store Name'),
            'get_time' => Yii::t('app', 'Get Time'),
            'type' => Yii::t('app', 'Type'),
            'price' => Yii::t('app', 'Price'),
            'create_at' => Yii::t('app', 'Create At'),
        ];
    }
}
