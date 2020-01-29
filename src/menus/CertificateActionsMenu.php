<?php
/**
 * SSL certificates module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-certificate
 * @package   hipanel-module-certificate
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\certificate\menus;

use hipanel\widgets\AjaxModalWithTemplatedButton;
use hiqdev\yii2\menus\Menu;
use yii\helpers\Html;
use yii\web\JsExpression;
use Yii;

class CertificateActionsMenu extends Menu
{
    public $model;

    public function items()
    {
        return [
            'view' => [
                'label' => Yii::t('hipanel', 'View'),
                'icon' => 'fa-info',
                'url' => ['@certificate/view', 'id' => $this->model->id],
                'encode' => false,
                'visible' => true,
            ],
            'renew' => [
                'label' => Yii::t('hipanel:certificate', 'Renew'),
                'icon' => 'fa-refresh',
                'url' => ['@certificate/add-to-cart-renew', 'model_id' => $this->model->id],
                'encode' => false,
                'visible' => $this->model->isRenewable(),
                'linkOptions' => [
                    'data-pjax' => 0,
                ],
            ],
            'reissue' => [
                'label' => Yii::t('hipanel:certificate', 'Reissue'),
                'icon' => 'fa-refresh',
                'url' => ['@certificate/reissue', 'id' => $this->model->id],
                'encode' => false,
                'visible' => $this->model->isReissuable(),
            ],
            'revalidate' => [
                'label' => Yii::t('hipanel:certificate', 'Revalidate'),
                'icon' => 'fa-refresh',
                'url' => ['@certificate/revalidate'],
                'linkOptions' => [
                    'data' => [
                        'method' => 'post',
                        'pjax' => '0',
                        'form' => 'revalidate',
                        'params' => [
                            'Certificate[id]' => $this->model->id,
                        ],
                    ],
                ],
                'encode' => false,
                'visible' => $this->model->isReValidateable(),
            ],
            'send-notifications' => [
                'label' => Yii::t('hipanel:certificate', 'Resend validation'),
                'icon' => 'fa-location-arrow',
                'url' => ['@certificate/send-notifications'],
                'linkOptions' => [
                    'data' => [
                        'method' => 'post',
                        'pjax' => '0',
                        'form' => 'send-notifications',
                        'params' => [
                            'Certificate[id]' => $this->model->id,
                        ],
                    ],
                ],
                'encode' => false,
                'visible' => $this->model->isValidationResendable(),
            ],
            'cancel' => [
                'label' => AjaxModalWithTemplatedButton::widget([
                    'ajaxModalOptions' => [
                        'id' => "cancel-certificate-modal1-{$this->model->id}",
                        'bulkPage' => true,
                        'header' => Html::tag('h4', Yii::t('hipanel:certificate', 'Confirm certificate cancel'), ['class' => 'label-danger']),
                        // 'headerOptions' => ['class' => 'label-danger'],
                        'scenario' => 'default',
                        'actionUrl' => ['cancel', 'id' => $this->model->id],
                        'handleSubmit' => ['cancel', 'id' => $this->model->id],
                        'toggleButton' => [
                            'tag' => 'a',
                            'label' => Html::tag('i', null, ['class' => 'fa fa-fw fa-trash-o']) . '&nbsp;' . Yii::t('hipanel', 'Cancel'),
                            'style' => 'cursor: pointer;',
                            'onClick' => new JsExpression("$(this).parents('.menu-button').find('.popover').popover('hide');"),
                        ],
                    ],
                    'toggleButtonTemplate' => '{toggleButton}',
                ]),
                'encode' => false,
                'visible' => Yii::$app->user->can('certificate.update'),
            ],
            'delete' => [
                'label' => AjaxModalWithTemplatedButton::widget([
                    'ajaxModalOptions' => [
                        'id' => "cancel-certificate-modal1-{$this->model->id}",
                        'bulkPage' => true,
                        'header' => Html::tag('h4', Yii::t('hipanel:certificate', 'Confirm certificate delete'), ['class' => 'label-danger']),
                        'headerOptions' => ['class' => 'label-danger'],
                        'scenario' => 'default',
                        'actionUrl' => ['delete', 'id' => $this->model->id],
                        'handleSubmit' => ['delete', 'id' => $this->model->id],
                        'toggleButton' => [
                            'tag' => 'a',
                            'label' => Html::tag('i', null, ['class' => 'fa fa-fw fa-trash-o']) . '&nbsp;' . Yii::t('hipanel', 'Delete'),
                            'style' => 'cursor: pointer;',
                            'onClick' => new JsExpression("$(this).parents('.menu-button').find('.popover').popover('hide');"),
                        ],
                    ],
                    'toggleButtonTemplate' => '{toggleButton}',
                ]),
                'encode' => false,
                'visible' => Yii::$app->user->can('certificate.delete') && $this->model->isDeleteable(),
            ],
        ];
    }
}
