<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('PROJECT_ROOT', preg_replace('/(nomo\-interface\/)(.*)/', 'nomo-interface', __DIR__));

require PROJECT_ROOT . '/vendor/autoload.php';
include_once PROJECT_ROOT . '/config/ConfigSecret.php';

$ConfigSecret = new ConfigSecret();

$binance = new \ccxt\binance(array(
    'apiKey' => $ConfigSecret->getSetting('BINANCE_API_KEY'), // replace with your keys
    'secret' => $ConfigSecret->getSetting('BINANCE_API_SECRET'),
));

$portfolio           = ['BTC', 'USDT', 'BUSD', 'BNB', 'ETH', 'DOT', 'BAKE', 'ADA'];
$portfolio           = ['BTC', 'USDT'];

$balances = $binance->fetch_balance();
$quotes = [];
$quote_balances = array();
foreach ($balances['info']['balances'] as $key => $value){

//    echo '<p>'.$key.' '.$value.'</p>';
    if (strpos($value['asset'], 'UP') === false && strpos($value['asset'], 'DOWN') === false && strpos($value['asset'], 'BULL') === false && strpos($value['asset'], 'BEAR') === false) {

        if ($value['free'] > 0) {
            $quote_balances[$value['asset']] = array(
                'free' => $value['free'],
                'locked' => $value['locked'],
                'default_check' => in_array($value['asset'], $portfolio) ? true : false
            );
        }
        $quotes[] = $value['asset'];
    }
}

sort($quotes);
ksort($quote_balances);

$preset_select_values = array(
    'select1'=> '',
    'select2'=> '',
    'select3'=> '',
);
if(isset($_GET['quotes_content'])){
    $quote_url = explode(',', $_GET['quotes_content']);
    $counti = count($quote_url);
    $idx = 1;
    for($i = 0; $i < $counti; $i++){
        if(in_array($quote_url[$i], $quotes)){
            $preset_select_values['select'.$idx] = $quote_url[$i];
            $idx++;
        }
    }
}




if(isset($_POST['timeframe'])){
    $params = array();
    $trading_bases = array();
    $trading_quotes = array();

    foreach ($_POST as $input => $value){
        if(in_array($input, ['futures_enabled', 'margin_enabled']) && $value == 'on'){
            $params[] = '--'.$input;
        }
        elseif (in_array($input, ['future_leverage', 'fund_percentage', 'timeframe', 'trade_side'])){
            $params[] = '--'.$input.'="'.$value.'"';
        }
        elseif(strpos($input, 'trading_base_') !== false && $value != ''){
            $trading_bases[] = $value;
        }
        elseif(strpos($input, 'checkbox_quotes_') !== false && $value == 'on'){
            $trading_quotes[] = str_replace('checkbox_quotes_', '', $input);
        }
    }

    if(count($trading_bases) > 0)
        $params[] = '--trading_bases="'.implode(',', $trading_bases).'"';

    if(count($trading_quotes) > 0)
        $params[] = '--trading_quotes="'.implode(',', $trading_quotes).'"';


    $command = (PROJECT_ROOT.'/../nomo-ccxt/venv/bin/python '.PROJECT_ROOT.'/../nomo-ccxt/nomo/Services/EnterPosition.py '.implode(' ', $params));
    $exec_cmd = 'echo "'.date("Y-m-d h:i").'" >> '.PROJECT_ROOT.'/../nomo-ccxt/logs/EnterPosition.log';
//    $exec_cmd = 'echo "'.date("Y-m-d h:i").' '.addslashes($command).'" >> '.PROJECT_ROOT.'/../nomo-ccxt/logs/EnterPosition.log 2>&1';
    $test_out = shell_exec($exec_cmd);
    echo '<p>'.$test_out.'</p>';
    $output = shell_exec($command.' >> '.PROJECT_ROOT.'/../nomo-ccxt/logs/EnterPosition.log 2>&1');
    echo '<p>'.$exec_cmd.'</p>';
    echo $command;

}

?>

<form action="" method="post">
    <p><input name="futures_enabled" type="checkbox" checked> Future Leverage: <input name="future_leverage" type="number" value="3" style="width: 40px">x</p>
    <p><input name="margin_enabled" type="checkbox" > Margin Isolated</p>
    <p>Percentage Funds Allocated: <input name="fund_percentage" type="text" value="0.05" style="width: 50px"></p>
    <p>Side: <select name="trade_side">
        <option value="BUY">BUY</option>
        <option value="SELL">SELL</option>
    </select>
    </p>
    <p>Timeframe: <select name="timeframe">
        <option value="1m">1m</option>
        <option value="3m" selected>3m</option>
        <option value="5m">5m</option>
        <option value="15m">15m</option>
        <option value="30m">30m</option>
        <option value="1h">1h</option>
        <option value="2h">2h</option>
        <option value="4h">4h</option>
        <option value="1d">1d</option>
    </select>
    </p>
    <p></p>

    <select name="trading_base_1">
        <option></option>
        <?php

            foreach ($quotes as $quote) {
                echo '<option value="'.$quote.'" '.($preset_select_values['select1'] == $quote ? 'selected' : '').'>'.$quote.'</option>';
            }
        ?>
    </select>
    <p></p>

    <select name="trading_base_2">
        <option></option>
        <?php
        foreach ($quotes as $quote) {
            echo '<option value="'.$quote.'" '.($preset_select_values['select2'] == $quote ? 'selected' : '').'>'.$quote.'</option>';
        }
        ?>
    </select>


    <p></p>

    <select name="trading_base_3">
        <option></option>
        <?php
        foreach ($quotes as $quote) {
            echo '<option value="'.$quote.'" '.($preset_select_values['select3'] == $quote ? 'selected' : '').'>'.$quote.'</option>';
        }
        ?>
    </select>

    <p></p>

    <?php

        foreach ($quote_balances as $quote => $value){
            echo '<p><input type="checkbox" name="checkbox_quotes_'.$quote.'" '.($value['default_check'] == true ? 'checked' : '').'> '.$quote.': '.$value['free'].'</p>';
        }
    ?>

    <button type="submit">Trade</button>
</form>