<?php

namespace mirkhamidov\mail\models;

use mirkhamidov\mail\models\base\MailLog as BaseMailLog;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class MailLog
 * @package mirkhamidov\mail\models
 *
 * @property array $data
 */
class MailLog extends BaseMailLog
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 99;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function setSender($value)
    {
        if (is_array($value)) {
            $this->sender = Json::encode($value);
        } else {
            $this->sender = $value;
        }
    }

    public function setData($value)
    {
        $this->data_data = Json::encode(ArrayHelper::merge($this->data, $value));
    }

    public function getData()
    {
        if (!empty($this->data_data)) {
            return Json::decode($this->data_data);
        }
        return [];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMail()
    {
        return $this->hasOne(Mail::className(), ['id' => 'mail_id']);
    }
}
