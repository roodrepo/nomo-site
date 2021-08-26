<!DOCTYPE html>
<html>
<header>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://nightly.datatables.net/css/dataTables.bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="include/css/pnl.css">

</header>
<body>


<div id="filter-btn" class="hidden">
    <span class="material-icons">filter_list</span>
</div>

<div id="filters-list" class="hidden"></div>


<div id="clock-wrapper"></div>

<input id="dateFrom" value="<?php echo gmdate("Y-m-d"); ?>" class="refreshTable myDatePicker">
<input id="dateTo" value="<?php echo gmdate("Y-m-d"); ?>" class="refreshTable myDatePicker">
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
    <option value="Hour">Hour</option>
    <option value="Trade">Trade</option>
</select>

<div id="pnlContainer">
    <table id="pnl" class="table table-striped table-bordered" style="width:100%">
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
            <th>MAE avg</th>
            <th>MFE avg</th>
            <th>% Biggest loss</th>
            <th>% Biggest win</th>
        </tr>
        </thead>
    </table>
</div>

<div id="tradeContainer" class="hidden">
    <table id="trade" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>Pair</th>
            <th class="date_col">Date open</th>
            <th class="date_col">Date close</th>
            <th>Side</th>
            <th>Timeframe</th>
            <th>Leverage</th>
            <th>Type</th>
            <th>Strategy</th>
            <th>Result</th>
            <th># Profit</th>
            <th>% Profit</th>
            <th># Fee</th>
            <th>% Fee</th>
            <th>Amount open</th>
            <th>% MAE</th>
            <th>% MFE</th>
        </tr>
        </thead>
    </table>
</div>
<!--<div>-->
<!--    <ul>-->
<!--        <li id="filterType"></li>-->
<!--        <li id="filterSide"></li>-->
<!--        <li id="filterBase"></li>-->
<!--        <li id="filterQuote"></li>-->
<!--        <li id="filterLeverage"></li>-->
<!--        <li id="filterStrategy"></li>-->
<!--        <li id="filterTimeframe"></li>-->
<!--        <li id="filterPair"></li>-->
<!--    </ul>-->
<!--</div>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script>

    $(document).ready(function() {

        var filters = {
            'filterType': [],
            'filterSide': [],
            'filterBase': [],
            'filterQuote': [],
            'filterLeverage': [],
            'filterStrategy': [],
            'filterTimeframe': [],
            'filterPair': [],
        }

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

                    d.filterType        = filters['filterType'].join('|');
                    d.filterSide        = filters['filterSide'].join('|');
                    d.filterBase        = filters['filterBase'].join('|');
                    d.filterQuote       = filters['filterQuote'].join('|');
                    d.filterLeverage    = filters['filterLeverage'].join('|');
                    d.filterStrategy    = filters['filterStrategy'].join('|');
                    d.filterTimeframe   = filters['filterTimeframe'].join('|');
                    d.filterPair        = filters['filterPair'].join('|');
                }
            },
            "fixedHeader": true,
            "searching": false,
            "paging": false,
            "info": false,
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

        var ignoreTradeAjaxRequest = true;

        var tradeTable = $('#trade').DataTable( {
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

                    d.filterType        = filters['filterType'].join('|');
                    d.filterSide        = filters['filterSide'].join('|');
                    d.filterBase        = filters['filterBase'].join('|');
                    d.filterQuote       = filters['filterQuote'].join('|');
                    d.filterLeverage    = filters['filterLeverage'].join('|');
                    d.filterStrategy    = filters['filterStrategy'].join('|');
                    d.filterTimeframe   = filters['filterTimeframe'].join('|');
                    d.filterPair        = filters['filterPair'].join('|');
                    d.ignore            = ignoreTradeAjaxRequest
                }
            },
            "searching": false,
            "paging": false,
            "info": false,
            "bPaginate": false,
            "fixedHeader": true,
            "columns": [
                { "data": "pair" },
                { "data": "date_open" },
                { "data": "date_close" },
                { "data": "side" },
                { "data": "timeframe" },
                { "data": "leverage" },
                { "data": "trading_type" },
                { "data": "strategy" },
                { "data": "result_trade" },
                { "data": "profit_amount" },
                { "data": "profit_perc" },
                { "data": "fees_amount" },
                { "data": "fees_perc" },
                { "data": "open_amount" },
                { "data": "drawdown_perc" },
                { "data": "updraw_perc" },
            ],


        } );

        function refreshFilters(){
            var htmlContentFilterBox = ''
            for(filterType in filters){
                if(filters[filterType].length > 0){
                    htmlContentFilterBox += '<p>'+filterType.replace('filter', '')+'</p><ul>'
                    var i =0
                    for(filter in filters[filterType]){
                        htmlContentFilterBox += '<li>'+filters[filterType][filter]+'<span filterType="'+filterType+'" idx="'+i+'" class="filter-btn material-icons-outlined">clear</span></li>'
                        i++
                    }
                    htmlContentFilterBox += '</ul>'
                }
            }

            if(htmlContentFilterBox == ''){
                $('#filters-list').addClass('hidden')
            }
            else{
                $('#filters-list').removeClass('hidden')
                $('#filters-list').html(htmlContentFilterBox)
            }

        }

        $('#pnl tbody').on( 'click', 'tr', function () {
            $(this).toggleClass('selected');
            $('#filter-btn').removeClass('hidden');
        } );

        $('#filters-list').on( 'click', '.filter-btn', function () {
            filters[$(this).attr('filterType')].splice($(this).attr('idx'), 1);
            pnlTable.ajax.reload()
            refreshFilters()
        });


        $('#filter-btn').on( 'click', function () {
            $('#filter-btn').addClass('hidden');
            var shownames = $.map(pnlTable.rows('.selected').data(), function (item) {
                return item['showname']
            });

            // TODO: This is temporary as the stored procedure cannot split string at the moment
            if(shownames.length > 1){
                shownames = [shownames[0]]
            }

            filters['filter'+$('#groupBy').val()] = shownames
            pnlTable.ajax.reload()
            refreshFilters()
        } );

        $( ".myDatePicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
        $( ".refreshTable" ).change( function() {
            ignoreTradeAjaxRequest = false

            if($('#groupBy').val() == 'Trade'){
                $('#tradeContainer').removeClass('hidden');
                $('#pnlContainer').addClass('hidden');
                tradeTable.ajax.reload();
            }
            else{
                $('#tradeContainer').addClass('hidden');
                $('#pnlContainer').removeClass('hidden');
                pnlTable.ajax.reload()
            }

        });

        function setDatetime(){
            var date = new Date();
            $('#clock-wrapper').html(
                date.getUTCFullYear() + "-" + (parseInt(date.getUTCMonth()) + 1 ) + "-" + date.getUTCDate() + " " +date.getUTCHours() + ":" + date.getUTCMinutes()
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