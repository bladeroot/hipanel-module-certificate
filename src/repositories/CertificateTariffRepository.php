<?php
/**
 * Certificate plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-certificate
 * @package   hipanel-module-certificate
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\certificate\repositories;

use hipanel\models\Ref;
use hipanel\helpers\ArrayHelper;
use hipanel\modules\certificate\models\CertificateType;
use hipanel\modules\finance\models\CertificateResource;
use hipanel\modules\finance\models\Tariff;
use yii\base\Application;
use yii\web\UnprocessableEntityHttpException;

class CertificateTariffRepository
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Returns the tariff for the certificate operations
     * Caches the API request for 3600 seconds and depends on client id and seller login.
     * @return Tariff|null The certificate tariff or boolean `false` when no tariff was found
     */
    public function getTariff()
    {
        if ($this->app->user->isGuest) {
            $seller = $this->app->user->seller;
            $client_id = null;
        } else {
            $seller = $this->app->user->identity->seller;
            $client_id = $this->app->user->id;
        }

        return $this->app->get('cache')->getOrSet([__METHOD__, $seller, $client_id], function () use ($seller, $client_id) {
            $res = Tariff::find()
                ->action('get-available-info')
                ->joinWith('resources')
                ->andFilterWhere(['type' => 'certificate'])
                ->andFilterWhere(['seller' => $seller])
                ->andWhere(['with_resources' => true])
                ->all();

            if (is_array($res) && !empty($res)) {
                return reset($res);
            }

            return null;
        }, 1); /// TODO change to 3600 XXX
    }

    /**
     * @param Tariff $tariff
     * @param string $type
     * @param bool $orderByDefault whether to order prices by name
     * @see orderResources
     * @return array
     */
    public function getResources(Tariff $tariff = null, $type = CertificateResource::TYPE_CERT_REGISTRATION, $orderByDefault = true)
    {
        if ($tariff === null) {
            $tariff = $this->getTariff();
        }

        $resources = array_filter((array)$tariff->resources, function ($resource) use ($type) {
            return $resource->type === $type;
        });
        foreach ($resources as $key => &$resource) {
            $resource->certificateType = CertificateType::getKnownType($resource->object_id);
            if (!$resource->certificateType) {
                unset($resources[$key]);
            }
            if (!$resource->getPriceForPeriod(1)) {
                unset($resources[$key]);
            }
        }

        if ($orderByDefault) {
            return $this->orderResources($resources);
        }

        return $resources;
    }

    /**
     * @param Resource[] $zones array of domain resources to be sorted
     * @return array sorted by the default zone resources
     */
    public function orderResources($resources)
    {
        ArrayHelper::multisort($resources, 'certificateType.name');

        /*uasort($result, function ($a, $b) {
            return $a->zone === Certificate::DEFAULT_ZONE;
        });*/

        return $resources;
    }

    public static function getCertificateRefs()
    {
        $refs = CertificateType::getKnownTypes();
        var_dump($refs);
        die;
    }
}