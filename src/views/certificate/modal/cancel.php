<?php

use hipanel\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = Yii::t('hipanel:certificate', '');
$this->params['breadcrumbs'][] = ['label' => $label, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin([
    'id' => 'cancel-certificate-form',
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
]) ?>
    <?= Yii::t('hipanel:certificate', 'Certificate will be immediately revoked without any refunds or ability to reissue this certificate') ?>
    <?= '. ' ?>
    <?= Yii::t('hipanel:certificate', 'Are you sure to cancel certificate for {name}?', ['name' => $model->name]) ?>
    <?= Html::activeHiddenInput($model, 'id') ?>
    <?= $form->field($model, 'reason') ?>
    <?= Html::submitButton(Yii::t('hipanel:certificate', 'Cancel'), ['class' => 'btn btn-danger btn-flat']) ?>
<?php $form->end() ?>

