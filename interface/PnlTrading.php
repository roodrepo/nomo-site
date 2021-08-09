<!DOCTYPE html>
<html>
<header>
    <link href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

</header>
<body>

<div id="clock-wrapper"></div>

<input id="dateFrom" value="2021-08-01" class="refreshTable myDatePicker">
<input id="dateTo" value="2021-08-09" class="refreshTable myDatePicker">
<select id="dateType" class="refreshTable">
    <option value="Open" selected>Open</option>
    <option value="Close">Close</option>
</select>

<select id="resultType" class="refreshTable">
    <option value="Table" selected>Table</option>
    <option value="Graph">Graph</option>
</select>


<select id="compareTo" class="refreshTable">
    <option value="Year" selected>Year</option>
    <option value="Quarter">Quarter</option>
    <option value="Month">Month</option>
    <option value="Week">Week</option>
    <option value="Day">Day</option>
</select>


<select id="groupBy" class="refreshTable">
    <option value="Pair" selected>Pair</option>
    <option value="Timeframe">Timeframe</option>
    <option value="Strategy">Strategy</option>
    <option value="Leverage">Leverage</option>
    <option value="Quote">Quote</option>
    <option value="Base">Base</option>
    <option value="Side">Side</option>
    <option value="Type">Type</option>
    <option value="Year">Year</option>
    <option value="Quarter">Quarter</option>
    <option value="Month">Month</option>
    <option value="Week">Week</option>
    <option value="Day">Day</option>
</select>


<table id="pnl" class="display" style="width:100%">
    <thead>
    <tr>
        <th>Showname</th>
        <th>Profit Factor</th>
        <th>% Profit avg</th>
        <th>% Win Profit avg</th>
        <th>% Loss Profit avg</th>
        <th># Trade</th>
        <th>% Win Rate</th>
        <th>% Fees</th>
        <th>Hold. Time avg</th>
        <th>Win Hold. Time avg</th>
        <th>Loss Hold. Time avg</th>
        <th>Drawdown avg</th>
        <th>Updraw avg</th>
        <th>% Biggest loss</th>
        <th>% Biggest win</th>
    </tr>
    </thead>
</table>
<div>
    <ul>
        <li id="filterType"></li>
        <li id="filterSide"></li>
        <li id="filterBase"></li>
        <li id="filterQuote"></li>
        <li id="filterLeverage"></li>
        <li id="filterStrategy"></li>
        <li id="filterTimeframe"></li>
        <li id="filterPair"></li>
    </ul>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script>

    $(document).ready(function() {

        var pnlTable = $('#pnl').DataTable( {
            "ajax": {
                "url": '/api/services/PnlTrading.php',
                "dataType": "json",
                "type": 'POST',
                "data": function(d){
                    d.dateFrom          = $('#dateFrom').val();
                    d.dateTo            = $('#dateTo').val();
                    d.dateType          = $('#dateType').val();
                    d.resultType        = $('#resultType').val();
                    d.compareTo         = $('#compareTo').val();
                    d.groupBy           = $('#groupBy').val();

                    d.filterType        = $('#filterType').text();
                    d.filterSide        = $('#filterSide').text();
                    d.filterBase        = $('#filterBase').text();
                    d.filterQuote       = $('#filterQuote').text();
                    d.filterLeverage    = $('#filterLeverage').text();
                    d.filterStrategy    = $('#filterStrategy').text();
                    d.filterTimeframe   = $('#filterTimeframe').text();
                    d.filterPair        = $('#filterPair').text();
                }
            },
            "bPaginate": false,
            "columns": [
                { "data": "showname" },
                { "data": "perc_profit_factor" },
                { "data": "avg_profit_perc" },
                { "data": "avg_profit_perc_win" },
                { "data": "avg_profit_perc_loss" },
                { "data": "nb_trade" },
                { "data": "win_rate" },
                { "data": "fees_perc" },
                { "data": "avg_holding_time" },
                { "data": "avg_holding_time_win" },
                { "data": "avg_holding_time_loss" },
                { "data": "avg_drawdown_perc" },
                { "data": "avg_updraw_perc" },
                { "data": "biggest_perc_loss" },
                { "data": "biggest_perc_win" },
            ],

            "pageLength": 100,
        } );


        $( ".myDatePicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
        $( ".refreshTable" ).change( function() {
            pnlTable.ajax.reload()
        });

        function setDatetime(){
            var date = new Date();
            $('#clock-wrapper').html(
                date.getUTCFullYear() + "-" + date.getUTCMonth() + "-" + date.getUTCDate() + " " +date.getUTCHours() + ":" + date.getUTCMinutes()
            );
        }

        setInterval(function() {
            setDatetime();
        }, 60000);
        setDatetime();

    } );

</script>
</body>
</html>