<?php
/** @var $model \mirkhamidov\mail\models\Mail */
/** @var $params array */
/** @var $moreData array */

use mirkhamidov\alert\Alert;
use mirkhamidov\mail\assets\LinkButtonAssets;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

LinkButtonAssets::register($this);

?>
<div class="mail-button-form">
    <?= Alert::widget(['delay' => 3000]); ?>
    <?php $form = ActiveForm::begin($params['formOptions']) ?>
    <?= $form->errorSummary($model) ?>

    <?= Html::activeHiddenInput($model, 'setid', ['value' => $model->primaryKey]) ?>
    
    <?php foreach ($moreData as $attr => $val) : ?>
        <?= Html::activeHiddenInput($model, 'moreData[' . $attr . ']', ['value' => $val]) ?>
    <?php endforeach ?>

    <?php if (!$model->hasErrors()) : ?>
    <?= Html::submitButton(
        $params['submitButtonText'],
        $params['submitButtonParams']
    )?>
    <?php else : ?>
        <i><?= \Yii::t('app', 'Can`t send e-mail until error are eliminated.') ?></i>
    <?php endif ?>
    <?php ActiveForm::end() ?>
</div>
