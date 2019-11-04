<?php

namespace common\models;

use common\models\base\BaseModel;
use Yii;

class JustSuggestModel extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'just_suggest';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['open_id', 'suggest', 'add_time'], 'required'],
            [['add_time'], 'integer'],
            [['open_id'], 'string', 'max' => 255],
            [['open_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [

        ];
    }
}
