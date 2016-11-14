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


use mirkhamidov\mail\models\Mail;
use mirkhamidov\mail\models\MailLog;
use mirkhamidov\mail\widgets\MailButtonFormWidget;
use yii\base\DynamicModel;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

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
     * @return bool
     * @throws \Exception
     */
    public function sendMail($mailAlias, $recipient, array $moreData = [])
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
            $res = $this->send($model, $moreData);
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
     * Send email
     * @param Mail $model
     * @param array $moreData
     */
    public function send(Mail &$model, array $moreData = [])
    {
        $res = \Yii::$app->mailer->compose(
            ['html' => $model->content_html, 'text' => $model->content_text],
            $moreData
        )
            ->setFrom($this->sender)
            ->setTo($model->recipient)
            ->setSubject($model->subject . (YII_DEBUG ? ' ' . rand(9, 99999) : null))
            ->send();

        $this->logMail($model, $res, $moreData);
        return $res;
    }

    /**
     * @param Mail $model
     * @param $resultOfMailer
     * @param array $moreData
     */
    public function logMail(Mail $model, $resultOfMailer, array $moreData = [])
    {
        /** @var \yii\web\View $view */
        $view = \Yii::$app->getView();
        $modelLog = new MailLog();
        $modelLog->mail_id = $model->primaryKey;
        $modelLog->recipient = $model->recipient;
        $modelLog->setSender($this->sender);
        $modelLog->composed_html = $view->render($model->content_html, $moreData);
        $modelLog->composed_text = $view->render($model->content_text, $moreData);
        $modelLog->data = ['moreData' => $moreData];

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