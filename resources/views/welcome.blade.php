<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Pitjarus</title>
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <script src="{{ mix('js/app.js') }}" defer></script>

        <script type="module" src=""></script>

    </head>
    <body class="antialiased">
        <div id="filter-area" class="m-4 p-4 grid grid-cols-4"> 
        <!-- border border-gray-300 border-solid rounded -->
            <div class="px-4 w-full">
                <select class="w-full" id="area" name="area[]" multiple="multiple">
                    @foreach($tableHead as $k => $v)
                        <option value="{{$v->area_id}}">{{$v->area_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="px-4">
                <input id="start-date" class="h-8 w-full border border-gray-400 border-solid rounded focus:border-gray-800" type="date">
            </div>
            <div class="px-4">
                <input id="end-date" class="h-8 w-full border border-gray-400 border-solid rounded focus:border-gray-800" type="date">
            </div>
            <div class="px-4">
                <button id="view" class="text-white h-8 w-full border border-gray-400 border-solid rounded bg-blue-500 hover:bg-blue-700">View</button>
            </div>
        </div>
        <div id="chart-area" class="m-4 p-4">
            <div id="container"></div>
        </div>
        <div id="table-area" class="m-4 p-4">
            <table id="table-com" class="w-full table border-collapse border border-slate-500">
                <thead>
                    <tr>
                        <th class="border border-slate-600">Brand</th>
                        @foreach($tableHead as $k => $v)
                            <th class="border border-slate-600">{{$v->area_name}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </body>

    
    <script type="module">
        var chart;
        var area;
        $(document).ready(function() {

            window.$('#area').select2({
                width: 'resolve'
            });


            // Create the chart
            chart = Highcharts.chart('container', {
                chart: {
                    type: 'column',
                    events: {
                        load: init_data
                    }
                },
                title: {
                    text: 'Compliances'
                },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Percentage (%)'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:.1f}%'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
                },

                series: [
                    {
                        name: "Area",
                        
                        data: []
                    }
                ],
            });
        })

        function init_data(){
            $.ajax({
                url: 'api/hc-compliance',
                type: "POST",
                dataType: "json",
                success: function(data) {
                    chart.addSeries({
                        name: "Area",
                        color: '#00FFDD',
                        data: data.data
                    });
                },
                cache: false
            });
        }

        function req_hc_data(){
            var areas = getSelectValues();

            var data = {
                    areas:areas,
                    start_date:$("#start-date").val(),
                    end_date:$("#end-date").val(),
                }
            $.ajax({
                url: 'api/hc-compliance',
                type: "POST",
                contentType: "application/json; charset=utf-8",
                data: JSON.stringify(data),
                dataType: "json",
                success: function(data) {
                    chart.series[1].remove()
                    chart.addSeries({
                        name: "Area",
                        color: '#00FFDD',
                        data: data.data
                    });
                },
                cache: false
            });
        }

        $.ajax({
            url: 'api/area',
            type: "GET",
            dataType: "json",
            success: function(data) {
                area = data.data
                $.ajax({
                    url: 'api/table-compliance',
                    type: "POST",
                    dataType: "json",
                    success: function(data) {
                        $("#table-com > tbody").html("");
                        
                        Object.keys(data.data).forEach((element) => {
                            var tmp = "<tr>"+
                                        "<td class='border border-slate-700'>"+
                                            element+
                                        "</td>";

                            console.log(data.data[element]);
                            area.forEach(element2 => {
                                tmp = tmp+
                                        "<td class='border border-slate-700'>"+
                                        data.data[element][element2.area_id]+"%"
                                        "</td>";
                            });
                            tmp = tmp+"</tr>"

                            $("#table-com > tbody").append(tmp);
                        });
                    

                    },
                    cache: false
                });
            },
            cache: false
        });


        function req_table_data(){
            var areas = getSelectValues();

            var data = {
                    areas:areas,
                    start_date:$("#start-date").val(),
                    end_date:$("#end-date").val(),
                }
            $.ajax({
                url: 'api/table-compliance',
                type: "POST",
                contentType: "application/json; charset=utf-8",
                data: JSON.stringify(data),
                dataType: "json",
                success: function(data) {
                    $("#table-com > thead").html("");
                    var tmp_head = "<tr>"+
                                    "<th class='border border-slate-600'>"+
                                        "Brand"
                                    "</th>";
                    console.log(Object.values(data.area));
                    Object.values(data.area).forEach((element) => {
                        tmp_head = tmp_head+
                                    "<th class='border border-slate-700'>"+
                                    element.area_name+
                                    "</th>";
                    });
                    tmp_head = tmp_head+"</tr>"

                    $("#table-com > thead").append(tmp_head);


                    $("#table-com > tbody").html("");
                    
                    Object.keys(data.data).forEach((element) => {
                        var tmp = "<tr>"+
                                    "<td class='border border-slate-700'>"+
                                        element+
                                    "</td>";

                        console.log(data.data[element]);
                        Object.values(data.area).forEach(element2 => {
                            tmp = tmp+
                                    "<td class='border border-slate-700'>"+
                                    data.data[element][element2.area_id]+"%"
                                    "</td>";
                        });
                        tmp = tmp+"</tr>"

                        
                        $("#table-com > tbody").append(tmp);
                    });
                

                },
                cache: false
            });
        }

        function getSelectValues() {
            var result = [];
            var options = $("#area").val();
            var opt;

            $("#area option:selected").each(function()
            {
                opt = $( this ).val();

                result.push(opt);
            });
            

            return result;
        }

        $(document).on("click", "#view", function(){
            req_hc_data();
            req_table_data();
        })
        
    </script>
</html>

