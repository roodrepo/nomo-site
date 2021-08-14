<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_POST['ignore']) and ($_POST['ignore'] === True or $_POST['ignore'] === 'True')){
    echo json_encode(array('data' => array()));
    die();
}


define('PROJECT_ROOT', preg_replace('/(nomo\-interface\/)(.*)/', 'nomo-interface', __DIR__));

include_once PROJECT_ROOT . '/config/ConfigSecret.php';

$ConfigSecret = new ConfigSecret();
$dbh = new PDO('mysql:host='.$ConfigSecret->getSetting('SQL_HOST').';dbname='.$ConfigSecret->getSetting('DB_NAME'), $ConfigSecret->getSetting('SQL_USERNAME'), $ConfigSecret->getSetting('SQL_PASSWORD'));

$params = array(
    ':dateFrom' => isset($_POST['dateFrom']) ? $_POST['dateFrom'] : '',
    ':dateTo' => isset($_POST['dateTo']) ? $_POST['dateTo'] : '',
    ':dateType' => isset($_POST['dateType']) ? $_POST['dateType'] : 'Open',
    ':resultType' => isset($_POST['resultType']) ? $_POST['resultType'] : '',
    ':compareTo' => isset($_POST['compareTo']) ? $_POST['compareTo'] : '',
    ':groupBy' => isset($_POST['groupBy']) ? $_POST['groupBy'] : '',

    ':filterType' => isset($_POST['filterType']) ? $_POST['filterType'] : '',
    ':filterSide' => isset($_POST['filterSide']) ? $_POST['filterSide'] : '',
    ':filterBase' => isset($_POST['filterBase']) ? $_POST['filterBase'] : '',
    ':filterQuote' => isset($_POST['filterQuote']) ? $_POST['filterQuote'] : '',
    ':filterLeverage' => isset($_POST['filterLeverage']) ? $_POST['filterLeverage'] : '',
    ':filterStrategy' => isset($_POST['filterStrategy']) ? $_POST['filterStrategy'] : '',
    ':filterTimeframe' => isset($_POST['filterTimeframe']) ? $_POST['filterTimeframe'] : '',
    ':filterPair' => isset($_POST['filterPair']) ? $_POST['filterPair'] : '',
);

$stmt = $dbh->prepare("call Proc_PnL_Result(
	:dateFrom, 	            -- _dateFrom
    :dateTo,	            -- _dateTo
    :dateType, 		        -- _dateType
    :resultType, 			-- _resultType
    :compareTo, 			-- _compareTo
    :groupBy,	            -- _groupBy
    
    :filterType, 			-- _filterType
    :filterSide, 			-- _filterSide
    :filterBase, 			-- _filterBase
    :filterQuote, 			-- _filterQuote
    :filterLeverage, 		-- _filterLeverage
    :filterStrategy, 		-- _filterStrategy
    :filterTimeframe, 		-- _filterTimeframe
    :filterPair 			-- _filterPair
);");
$stmt->execute($params);

$response = array('data' => $stmt->fetchAll(PDO::FETCH_ASSOC));


echo json_encode($response);
