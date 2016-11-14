<?php

namespace mirkhamidov\mail\models\base;

use Yii;

/**
 * This is the model class for table "{{%mail_log}}".
 *
 * @property string $id
 * @property integer $mail_id
 * @property integer $status
 * @property string $recipient
 * @property string $sender
 * @property string $composed_text
 * @property string $composed_html
 * @property string $data_data
 *
 * @property Mail $mail
 */
class MailLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mail_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mail_id', 'status'], 'integer'],
            [['composed_text', 'composed_html', 'data_data'], 'string'],
            [['recipient', 'sender'], 'string', 'max' => 255],
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
            'status' => Yii::t('backend', '0-default'),
            'recipient' => Yii::t('backend', 'Mail Recipient'),
            'sender' => Yii::t('backend', 'Mail Sender'),
            'composed_text' => Yii::t('backend', 'Composed mail by text'),
            'composed_html' => Yii::t('backend', 'Composed mail by Html'),
            'data_data' => Yii::t('backend', 'JSON data'),
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
     * @return \mirkhamidov\mail\models\query\MailLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \mirkhamidov\mail\models\query\MailLogQuery(get_called_class());
    }
}
