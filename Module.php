<?php
/*
 * This file is part of the Mirkhamidov project.
 *
 * (c) Mirkhamidov project <http://github.com/mirkhamidov/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mirkhamidov\mail;

use Yii;
use mirkhamidov\mail\models\Mail;
use mirkhamidov\mail\models\MailLog;
use mirkhamidov\mail\widgets\MailButtonFormWidget;
use yii\base\DynamicModel;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\i18n\MessageFormatter;

class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'mirkhamidov\mail\controllers';

    /**
     * @var string|array
     */
    public $sender;

    /**
     * @var null|array
     */
    private $_sendMailErrors = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::setAlias('@mirkhamidov', __DIR__);

        if (!empty(\Yii::$app->params['supportEmail'])) {
            if (is_array(\Yii::$app->params['supportEmail'])) {
                $this->sender = \Yii::$app->params['supportEmail'];
            } else {
                $this->sender = [\Yii::$app->params['supportEmail'] => \Yii::$app->name];
            }
        }

        if (empty($this->sender)) {
            throw new Exception(\Yii::t('app', 'Set "sender" parametr to the module "mmail" or add "supportEmail" info to params section.'));
        }
    }

    /**
     *
     *
     *
     * @param $mailId
     * @param array $moreData
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function linkButton($mailId, $recipient, array $moreData = [], array $params = [])
    {
        return MailButtonFormWidget::widget([
            'mailId' => $mailId,
            'moreData' => $moreData,
            'params' => $params,
            'recipient' => $recipient
        ]);
    }


    /**
     * Validate, if all necessary params exists
     * @param Mail $model
     * @param array $moreData
     * @return bool
     */
    public function validateMoreParams(Mail &$model, array $moreData = [])
    {
        $_data = [];
        $_rules = [];

        if (empty($model->recipient)) {
            $model->addError('recipient', \Yii::t('app', '"recipient" parameter not set'));
        }

        /**
         * @param $key
         * @param $params
         * @return array
         */
        $_addRules = function ($key, $params) {
            return ArrayHelper::merge([$key], $params);
        };

        foreach ($model->mailParams as $param) {
            /** @var $param \mirkhamidov\mail\models\MailParams */
            $_data[$param->key] = null;
            if (isset($moreData[$param->key])) {
                $_data[$param->key] = $moreData[$param->key];
            }

            if (ArrayHelper::isIndexed($param->paramRules)) {
                foreach ($param->paramRules as $rule) {
                    $_rules[] = $_addRules($param->key, $rule);
                }
            } else {
                $_rules[] = $_addRules($param->key, $param->paramRules);
            }

        }

        if (!empty($_data)) {
            $dynaModel = DynamicModel::validateData($_data, $_rules);
            if ($dynaModel->hasErrors()) {
                $model->addErrors($dynaModel->getErrors());
                return false;
            }
        }

        return true;
    }


    /**
     * @param $mailAlias
     * @param $recipient
     * @param array $moreData
     * @param array $params
     * @return bool
     * @throws \Exception
     */
    public function sendMail($mailAlias, $recipient, array $moreData = [], array $params = [])
    {
        $model = Mail::findOne(['alias' => $mailAlias]);
        if (!$model) {
            throw new \Exception(\Yii::t('app', 'Entry with alias "{alias}" not found', [
                'alias' => $mailAlias,
            ]));
        }

        $model->recipient = $recipient;

        $this->validateMoreParams($model, $moreData);

        if (!$model->hasErrors()) {
            $res = $this->send($model, $moreData, $params);
            if ($res) {
                return true;
            } else {
                $this->addSendMailErrors('Send mail error');
            }
        } else {
            $this->addSendMailErrors($model->getErrors());
        }
        return false;
    }

    public function hasSendMailErrors()
    {
        return !empty($this->_sendMailErrors);
    }

    public function addSendMailErrors($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        $this->_sendMailErrors = ArrayHelper::merge($this->_sendMailErrors, $value);
    }

    public function getSendMailErrors()
    {
        return $this->_sendMailErrors;
    }

    /**
     * @var null|MessageFormatter
     */
    private $_messageFormatter = null;

    /**
     * @return MessageFormatter
     */
    public function getMessageFormatter()
    {
        if ($this->_messageFormatter === null) {
            $this->_messageFormatter = new MessageFormatter();
        }
        return $this->_messageFormatter;
    }

    /**
     * Formats a message via [ICU message format](http://userguide.icu-project.org/formatparse/messages)
     *
     * @param string $message The pattern string to insert parameters into.
     * @param array $params The array of name value pairs to insert into the format string.
     * @return false|string
     */
    public function format($message, array $params = [])
    {
        return $this->getMessageFormatter()->format($message, $params, \Yii::$app->language);
    }


    /**
     * Send email
     * @param Mail $model
     * @param array $moreData
     * @param array $params
     * @return bool
     */
    public function send(Mail &$model, array $moreData = [], array $params = [])
    {
        $_subject = $model->subject . (YII_DEBUG ? ' ' . rand(9, 99999) : null);
        if (!empty($model->config['subject']['replace'])
            && !empty($params['subject'])
        ) {
            $_subject = ($this->format($_subject, $params['subject']));
        }

        /** @var \yii\swiftmailer\Message $mailer */
        $mailer = \Yii::$app->mailer->compose(
            ['html' => $model->content_html, 'text' => $model->content_text],
            $moreData
        );
        $mailer->setFrom($this->sender);
        $mailer->setTo($model->recipient);
        $mailer->setSubject($_subject);

        if (!empty($params['attach'])) {
            foreach ($params['attach'] as $attachment) {
                if (empty($attachment['file'])) {
                    throw new InvalidArgumentException(Yii::t('app', 'Set "file" for attachment.'));
                }
                $_attachmentParams = [];
                if (!empty($attachment['params'])) {
                    $_attachmentParams = $attachment['params'];
                }
                $mailer->attach($attachment['file'], $_attachmentParams);
            }
        }


        $res = $mailer->send();

        $additionalData = [
            'subject' => $_subject,
            'moreData' => $moreData,
        ];
        $this->logMail($model, $res, $additionalData);
        return $res;
    }

    /**
     * @param Mail $model
     * @param $resultOfMailer
     * @param array $additionalData
     */
    public function logMail(Mail $model, $resultOfMailer, array $additionalData = [])
    {
        $_moreData = [];
        if (!empty($additionalData['moreData'])) {
            $_moreData = $additionalData['moreData'];
        }
        /** @var \yii\web\View $view */
        $view = \Yii::$app->getView();
        $modelLog = new MailLog();
        $modelLog->mail_id = $model->primaryKey;
        $modelLog->recipient = $model->recipient;
        $modelLog->setSender($this->sender);
        $modelLog->composed_html = $view->render($model->content_html, $_moreData);
        $modelLog->composed_text = $view->render($model->content_text, $_moreData);
        $modelLog->data = $additionalData;

        if ($resultOfMailer) {
            $modelLog->status = MailLog::STATUS_SUCCESS;
        } else {
            $modelLog->status = MailLog::STATUS_FAIL;
        }
        if (!$modelLog->save()) {
            // TODO: smth
        }
    }
}