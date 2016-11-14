<?php

namespace mirkhamidov\mail\models;

use mirkhamidov\mail\models\base\MailParams as BaseMailParams;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * Class MailParams
 * @package mirkhamidov\mail\models
 *
 * @property array $paramRules
 */
class MailParams extends BaseMailParams
{
    /** @inheritdoc */
    public function __set($name, $value)
    {
        if ($name == 'rules_data' && is_array($value)) {
            $value = Json::encode($value);
        }
        return parent::__set($name, $value);
    }

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

    /**
     * @return array|mixed
     */
    public function getParamRules()
    {
        if (!empty($this->rules_data)) {
            return Json::decode($this->rules_data);
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
