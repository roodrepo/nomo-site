<?php

function checkCoinBureauLastVideo($ConfigSecret, $balances){
    $channel_id  = 'UCqK_GSMbpiV8spgD3ZGloSw';

    $url_params = array(
        'part=snippet',
        'key='.$ConfigSecret->getSetting('YOUTUBE_API_KEY'),
        'channelId='.$channel_id,
        'order=date',
        'maxResults=1',
        'regionCode=UK'
    );

    $youtubeLatestVideo = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/search?'.implode('&', $url_params)), true);
    if (count($youtubeLatestVideo) > 0) {
        $url_params = array(
            'part=snippet',
            'key=' . $ConfigSecret->getSetting('YOUTUBE_API_KEY'),
            'id=' . $youtubeLatestVideo['items'][0]['id']['videoId'],
        );

        $videoInfo = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/videos?' . implode('&', $url_params)), true);
        $tags_currency_video = array();

        $ignore_quotes = ["BTC", "ETH", "BUSD", "USDC", "EURO", "TUSD", "GBP", "EUR", "XZC", "AUD", "AED", "BRL", "CAD", "CHF", "CZK",
            "DKK", "GHS", "HKD", "HUF", "JPY", "KES", "KZT", "MXN", "NGN", "NOK", "NZD", "PEN", "PLN", "RUB",
            "SEK", "TRY", "UAH", "UGX", "VENU", "VND", "ZAR", "PAXG", "PAX", "SUSD"];

        foreach ($balances as $key => $value) {
            if ($key == strtoupper($key) && strpos($key, 'UP') === false && strpos($key, 'DOWN') === false && strpos($key, 'BULL') === false && strpos($key, 'BEAR') === false)
                if (!in_array($key, $ignore_quotes) and in_array($key, $videoInfo['items'][0]['snippet']['tags']))// key in videoInfo['items'][0]['snippet']['tags']:
                    $tags_currency_video[] = $key;
        }
        return array(
            'title'         => $youtubeLatestVideo['items'][0]['snippet']['title'],
            'tags_video'    => $tags_currency_video,
            'videoId'       => $youtubeLatestVideo["items"][0]["id"]["videoId"],
            'quotes'        => $tags_currency_video
        );
    }

    return null;
}