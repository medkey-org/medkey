<?php
namespace app\modules\config\entities;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use yii\base\InvalidValueException;

/**
 * Class CurrencyEntity
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class CurrencyEntity
{
    /**
     * @var array
     */
    static $currencyList = [];


    /**
     * @param string $code
     * @return array
     */
    public static function findCurrency($code)
    {
        $currencies = static::currencyListData();
        if (isset($currencies[$code])) {
            return $currencies[$code];
        }
        throw new InvalidValueException('Code currency not found.'); // todo normalize text
    }

    /**
     * @return array
     */
    public static function currencyListData()
    {
        /**
         *   'USD' =>
        array (
        'alphabeticCode' => 'USD',
        'currency' => 'US Dollar',
        'minorUnit' => 2,
        'numericCode' => 840,
        ),
         * 'EUR' =>  array (
        'alphabeticCode' => 'EUR',
        'currency' => 'Euro',
        'minorUnit' => 2,
        'numericCode' => 978,
        ),
         *
         *  'RUB' => array (
        'alphabeticCode' => 'RUB',
        'currency' => 'Russian Ruble',
        'minorUnit' => 2,
        'numericCode' => 643,
        ),
         */
        $replaceCurrency = [
            'USD' =>
                [
                    'alphabeticCode' => 'USD',
                    'currency' => 'US Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 840,
                ],
            'EUR' =>
                [
                    'alphabeticCode' => 'EUR',
                    'currency' => 'Euro',
                    'minorUnit' => 2,
                    'numericCode' => 978,
                ],
            'RUB' =>
                [
                    'alphabeticCode' => 'RUB',
                    'currency' => 'Russian Ruble',
                    'minorUnit' => 2,
                    'numericCode' => 643,
                ],
        ];

        $currencies = $replaceCurrency; // ISOCurrencies
        $map = [];
        foreach ($currencies as $key => $currency) {
            /** @var $currency Currency */
            $map[$key] = $key;
        }

        return $map;
    }

    /**
     * @param string $value
     * @param string $currency
     * @return string
     */
    public static function moneyEncode($value, $currency)
    {
        $currencies = new ISOCurrencies();
        $moneyParser = new DecimalMoneyParser($currencies);
        $money = $moneyParser->parse($value, $currency);
        return $money->getAmount();
    }

    /**
     * @param string $value
     * @param string $currency
     * @return bool|string
     */
    public static function moneyDecode($value, $currency)
    {
        $money = new Money($value, new Currency($currency));
        $moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());
        return $moneyFormatter->format($money);
    }
}
