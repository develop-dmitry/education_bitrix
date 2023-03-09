<?php

namespace Sale\Handlers\Delivery;

use Bitrix\Sale\Delivery\CalculationResult;
use Bitrix\Sale\Shipment;

class AirplaneHandler extends \Bitrix\Sale\Delivery\Services\Base
{
    protected static $isCalculatePriceImmediately = true;

    protected static $whetherAdminExtraServicesShow = true;

    protected $handlerCode = 'BITRIX_AIRPLANE';

    public static function getClassTitle()
    {
        return 'Доставка личным самолетом';
    }

    public static function getClassDescription()
    {
        return 'Доставка личным самолетом';
    }

    protected function calculateConcrete(Shipment $shipment)
    {
        $result = new CalculationResult();

        $result->setDeliveryPrice(random_int(1000, 10000));

        return $result;
    }
}