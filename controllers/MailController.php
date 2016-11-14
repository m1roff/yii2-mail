<?php

namespace mirkhamidov\mail\controllers;


use mirkhamidov\mail\models\Mail;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class MailController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'send' => ['POST'],
                ],
            ],
        ]);
    }

    public function actionSend($id)
    {
        $model = Mail::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }


    }
}