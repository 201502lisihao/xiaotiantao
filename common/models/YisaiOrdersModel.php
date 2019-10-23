<?php
namespace common\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "wx_orders".
 *
 * @property int $id
 * @property string $order_no
 * @property string $get_no
 * @property int $user_id
 * @property string $order_status
 * @property string $store_name
 * @property int $get_time
 * @property int $type
 * @property text $order_detail
 * @property text $order_note
 * @property double $cut_money
 * @property double $price
 * @property int $create_at
 */
class YisaiOrdersModel extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yisai_orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_no', 'create_at'], 'required'],
            [['id', 'user_id', 'create_at'], 'integer'],
            [['id'], 'unique'],
            [['order_detail'], 'text']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
        ];
    }
}
