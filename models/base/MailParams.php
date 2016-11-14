<?php

namespace mirkhamidov\mail\models\base;

use Yii;

/**
 * This is the model class for table "{{%mail_params}}".
 *
 * @property integer $id
 * @property integer $mail_id
 * @property string $key
 * @property string $value
 * @property string $value_default
 * @property string $rules_data
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Mail $mail
 */
class MailParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mail_params}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mail_id'], 'integer'],
            [['key'], 'required'],
            [['value', 'value_default', 'rules_data'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['key'], 'string', 'max' => 255],
            [['mail_id', 'key'], 'unique', 'targetAttribute' => ['mail_id', 'key'], 'message' => 'The combination of Mail ID and Param Key has already been taken.'],
            [['mail_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mail::className(), 'targetAttribute' => ['mail_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'mail_id' => Yii::t('backend', 'Mail ID'),
            'key' => Yii::t('backend', 'Param Key'),
            'value' => Yii::t('backend', 'Param Value'),
            'value_default' => Yii::t('backend', 'Param Default Value'),
            'rules_data' => Yii::t('backend', 'Param Rules for Yii2-rules'),
            'created_at' => Yii::t('backend', 'Created At'),
            'updated_at' => Yii::t('backend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMail()
    {
        return $this->hasOne(Mail::className(), ['id' => 'mail_id']);
    }

    /**
     * @inheritdoc
     * @return \mirkhamidov\mail\models\query\MailParamsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \mirkhamidov\mail\models\query\MailParamsQuery(get_called_class());
    }
}
