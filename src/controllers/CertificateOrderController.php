<?php

namespace hipanel\modules\certificate\controllers;

use hipanel\models\Ref;
use hipanel\modules\certificate\forms\CsrGeneratorForm;
use hipanel\modules\certificate\forms\OrderForm;
use hipanel\modules\certificate\repositories\CertRepository;
use hipanel\modules\certificate\widgets\PreOrderQuestion;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class CertificateOrderController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', [
            'models' => CertRepository::create()->getTypes(),
        ]);
    }

    public function actionCsrGenerator($productId = null)
    {
        $model = new CsrGeneratorForm();
        $model->productId = $productId;
        $orderUrl = Url::toRoute(['@certificate/order/order', 'productId' => $model->productId]);

        return $this->render('csr-generator', [
            'countries' => array_change_key_case(Ref::getList('country_code'), CASE_UPPER),
            'model' => $model,
            'orderUrl' => $orderUrl,
        ]);
    }

    public function actionOrder($productId = null, $email = null)
    {
        $model = new OrderForm();
        $model->attributes = [
            'productId' => $productId,
            'email' => $email,
        ];
        if (Yii::$app->request->isAjax) {
            sleep(2);
            return $this->renderAjax('_orderForm', compact('model'));
        } else {
            return $this->render('order', compact('model'));
        }
    }

    public function actionGetLinksModal($productId)
    {
        return PreOrderQuestion::links($productId);
    }
}
