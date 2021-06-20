<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('PROJECT_ROOT', preg_replace('/(nomo\-interface\/)(.*)/', 'nomo-interface/', __DIR__));

require PROJECT_ROOT . '/vendor/autoload.php';
include_once PROJECT_ROOT . '/config/ConfigSecret.php';

ini_set("allow_url_fopen", 1);

$channel_id  = 'UCqK_GSMbpiV8spgD3ZGloSw';
$ConfigSecret = new ConfigSecret();

$binance = new \ccxt\binance(array(
    'apiKey' => $ConfigSecret->getSetting('BINANCE_API_KEY'), // replace with your keys
    'secret' => $ConfigSecret->getSetting('BINANCE_API_SECRET'),
));

$url_params = array(
    'part=snippet',
    'key='.$ConfigSecret->getSetting('YOUTUBE_API_KEY'),
    'channelId='.$channel_id,
    'order=date',
    'maxResults=1',
    'regionCode=UK'
);
echo '<p>Checking</p>';
$youtubeLatestVideo = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/search?'.implode('&', $url_params)), true);
if (count($youtubeLatestVideo) > 0){
    $url_params = array(
        'part=snippet',
        'key=' . $ConfigSecret->getSetting('YOUTUBE_API_KEY'),
        'id=' . $youtubeLatestVideo['items'][0]['id']['videoId'],
    );

    $videoInfo = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/videos?' . implode('&', $url_params), true));

    $tags_currency_video = array();

    $ignore_quotes = ["BUSD", "USDC", "EURO", "TUSD", "GBP", "EUR", "XZC", "AUD", "AED", "BRL", "CAD", "CHF", "CZK",
        "DKK", "GHS", "HKD", "HUF", "JPY", "KES", "KZT", "MXN", "NGN", "NOK", "NZD", "PEN", "PLN", "RUB",
        "SEK", "TRY", "UAH", "UGX", "VENU", "VND", "ZAR", "PAXG", "PAX", "SUSD"];


    $balances = $binance->fetch_balance();
    foreach ($balances as $key => $value){
        if ($key == strtoupper($key) && strpos($key, 'UP') !== false && strpos($key, 'DOWN') !== false && strpos($key, 'BULL') !== false && strpos($key, 'BEAR') !== false)
            if(in_array($key, $videoInfo['items'][0]['snippet']['tags']))// key in videoInfo['items'][0]['snippet']['tags']:
                $tags_currency_video[] = $key;
    }

    $messageTelegram = $youtubeLatestVideo['items'][0]['snippet']['title'] . '\n'
    . implode(', ', $tags_currency_video) . '\n'
    . 'https://www.youtube.com/watch?v=' . $youtubeLatestVideo['items'][0]['id']['videoId']  . '\n\n'
    . '/strat/coinbureau?quotes_content=' . implode(',', $tags_currency_video) . '\n\n';

    $sendTelegram = json_decode(file_get_contents('https://api.telegram.org/' . $ConfigSecret->getSetting('TELEGRAM_API_SECRET') . '/sendMessage?chat_id=' . $ConfigSecret->getSetting('TELEGRAM_CHATID') . '&text=' . urlencode($messageTelegram)), true);

    $callmebot = json_decode(file_get_contents('http://api.callmebot.com/start.php?user=@Roods13&text=' . implode(',', $tags_currency_video) . '&lang=en&rpt=4'), true);

    echo '<p>Telegram Sent</p>';
//    for key in balances.keys():
//        if key == key.upper() and 'UP' not in key and 'DOWN' not in key and 'BULL' not in key and 'BEAR' not in key and key not in ignore_quotes:
//            if key in videoInfo['items'][0]['snippet']['tags']:
//                tags_currency_video.append(key)


}