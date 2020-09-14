<?php


namespace App\Service;


use DateInterval;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class ExchangeRatesApi
{
    const URL_BASE = 'https://api.exchangeratesapi.io/latest';

    public static function getExchangeRate($base = 'EUR', $divisa= 'USD')
    {
        $cache = new FilesystemAdapter();

        /** @var CacheItem $itemCache */
        $itemCache = $cache->getItem("rate.$base.$divisa");
        $itemCache->expiresAfter(DateInterval::createFromDateString('12 hour'));

        if ($itemCache->isHit()) {
            $rate = $itemCache->get();
        } else {
            $rate = $cache->get("rate.$base.$divisa", function ($base) {
                $data = ['base' => $base];
                $url = sprintf("%s?%s", self::URL_BASE, http_build_query($data));

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $result = json_decode(curl_exec($curl), true);
                curl_close($curl);

                return $result['rates']['USD'];
            });
        }

        return $rate;
    }
}