<?php

namespace mirkhamidov\mail\models\base;

use Yii;

/**
 * This is the model class for table "{{%mail}}".
 *
 * @property integer $id
 * @property string $alias
 * @property integer $is_active
 * @property string $name
 * @property string $subject
 * @property string $content_text
 * @property string $content_html
 * @property string $created_at
 * @property string $updated_at
 *
 * @property MailLog[] $mailLogs
 * @property MailParams[] $mailParams
 */
class Mail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active'], 'integer'],
            [['name', 'subject', 'content_text', 'content_html'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['alias', 'name', 'subject', 'content_text', 'content_html'], 'string', 'max' => 255],
            [['alias'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'alias' => Yii::t('backend', 'Mail Template Alias'),
            'is_active' => Yii::t('backend', 'Is this item active?'),
            'name' => Yii::t('backend', 'Arbitrary name'),
            'subject' => Yii::t('backend', 'E-mail subject'),
            'content_text' => Yii::t('backend', 'Alias for text message representation'),
            'content_html' => Yii::t('backend', 'Alias for html message representation'),
            'created_at' => Yii::t('backend', 'Created At'),
            'updated_at' => Yii::t('backend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailLogs()
    {
        return $this->hasMany(MailLog::className(), ['mail_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailParams()
    {
        return $this->hasMany(MailParams::className(), ['mail_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \mirkhamidov\mail\models\query\MailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \mirkhamidov\mail\models\query\MailQuery(get_called_class());
    }
}
