<?php
namespace common\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "just_ticket".
 *
 * @property int $id
 * @property string $ticket_code
 * @property int $user_id
 * @property int $create_at
 */
class JustTicketModel extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'just_ticket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ticket_code', 'create_at'], 'required'],
            [['id', 'user_id', 'create_at'], 'integer'],
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
        ];
    }
}
