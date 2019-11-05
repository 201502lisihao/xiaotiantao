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
            [['add_time'], 'integer'],
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
