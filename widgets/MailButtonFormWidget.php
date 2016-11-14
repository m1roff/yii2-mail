<?php
/**
 *
 */

namespace mirkhamidov\mail\widgets;


use mirkhamidov\mail\models\Mail;
use mirkhamidov\mail\ModuleTrait;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * Class MailButtonFormWidget
 * @package mirkhamidov\mail\widgets
 *
 * $moreData param options
 *  array formOptions Fully passed to [[\yii\widgets\ActiveForm::begin()]]
 *  array submitButtonParams Fully passed to
 *  string $submitButtonText
 *
 * TODO: add description
 *
 * @mixin ModuleTrait
 */
class MailButtonFormWidget extends Widget
{
    use ModuleTrait;

    public $mailId;

    public $moreData = [];

    public $params = [];

    /** @var Mail */
    public $model;

    public $viewFile = '@mirkhamidov/widgets/views/link_button.php';

    public $recipient = null;

    public $submitButtonParams = [
        'class' => 'btn btn-default mail-btn-form-submit',
    ];

    public $formOptions = [
        'method' => 'POST',
        'options' => [
            'data' => [
                'pjax' => true,
            ],
        ],
    ];

    public function init()
    {

        if (!$this->model) {
            $this->model = Mail::findOne(['alias' => $this->mailId]);
        }

        if (!$this->model) {
            throw new \Exception(\Yii::t('app', 'Entry with alias "{alias}" not found', [
                'alias' => $this->mailId,
            ]));
        }

        $this->model->recipient = $this->recipient;

        $this->getModule()->validateMoreParams($this->model, $this->moreData);

        if (\Yii::$app->request->isPjax) {
            $_model = new Mail();
            if ($_model->load(\Yii::$app->request->post()) && $_model->setid == $this->model->primaryKey) {
                $this->getModule()->validateMoreParams($this->model, $_model->moreData);

                if (!$this->model->hasErrors()) {
                    $res = $this->getModule()->send($this->model, $_model->moreData);

                    if ($res) {
                        \Yii::$app->session->setFlash('success', \Yii::t('app', 'Message sent successfully.'));
                    } else {
                        \Yii::$app->session->setFlash('error', \Yii::t('app', 'Error occurred while sending message.'));
                    }
                }
            }
        }


        // init submit button
        if (empty($this->params['submitButtonText'])) {
            $this->params['submitButtonText'] = $this->model->name;
        }

        if (empty($this->params['submitButtonParams'])) {
            $this->params['submitButtonParams'] = $this->submitButtonParams;
        } else {
            $this->params['submitButtonParams'] = ArrayHelper::merge(
                $this->submitButtonParams,
                $this->params['submitButtonParams']
            );
        }

        $this->params['submitButtonParams'] = ArrayHelper::merge($this->params['submitButtonParams'],[
            'data' => [
                'toggle' => 'popover',
                'trigger' => 'focus hover',
                'title' => 'Пример письма',
                'content' => nl2br($this->render($this->model->content_text, $this->model->mailParamsDefault())),
            ]
        ]);
        // END init submit button

        // form options
//        $this->formOptions['action'] = Url::to(['/mmail/mail/send', 'id' => $this->model->primaryKey]);
        if (empty($this->params['formOptions'])) {
            $this->params['formOptions'] = $this->formOptions;
        } else {
            $this->params['formOptions'] = ArrayHelper::merge(
                $this->formOptions,
                $this->params['formOptions']
            );
        }
        // END form options
    }

    public function run()
    {
        return $this->renderFile($this->viewFile, [
            'params' => $this->params,
            'moreData' => $this->moreData,
            'model' => $this->model,
        ]);
    }


}