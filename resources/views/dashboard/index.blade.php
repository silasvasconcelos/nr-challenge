@extends('layouts.app')

@section('css')
	<style type="text/css">
		body {
			background-color: #ffffffd1;
		}
		.custom-row {
			margin-top: 20px;
			margin-left: 0;
			margin-right: 0;
		}
		.number-records {
			text-align: center;
		    font-size: 2em;
		}
	</style>
@endsection

@section('content')
	
	<div class="row custom-row">
		
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
			<div class="card">
			  <div class="card-header">
			    Number of records
			  </div>
			  <div class="card-body">
			    <blockquote class="blockquote mb-0">
			      <div class="number-records d-none" id="total-crawlers">{!! $total_crawlers !!}</div>
			    </blockquote>

			    <div id="piechart" style="width: 100%; height: auto;"></div>
			  
			  </div>
			</div>
			<button type="button" class="btn btn-success col-md-12" style="margin-top: 20px;" id="btn-start" data-url="{{ route('dashboard.start_crawlers') }}">Start service</button>
		</div>

		<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
			<div class="table-responsive">

				<table class="table table-sm">
					<caption>The last 10 records recovered</caption>
				  <thead class="thead-light">
				    <tr>
				      <th scope="col">Origin</th>
				      <th scope="col">Name</th>
				      <th scope="col">Object</th>
				    </tr>
				  </thead>
				  <tbody id="crawlers" data-url="{{ route('dashboard.verify') }}">
				  	@if ($last_crawlers->isNotEmpty())
				  		@foreach ($last_crawlers as $crawler)
						    <tr>
						      <td>{{ $crawler->origin }}</td>
						      <td>{{ $crawler->name }}</td>
						      <td>{!! str_limit($crawler->object, 30, '...') !!}</td>
						    </tr>
				  		@endforeach
				  	@else
					    <tr>
					    	<td colspan="3" class="text-center">No records</td>
					    </tr>
				  	@endif
				  </tbody>
				</table>
			</div>

		</div>
	</div>

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

		var data_chart_header = ['Origin', 'Number of records'],
			data_chart = [data_chart_header];

      function drawChart() {

        var data = google.visualization.arrayToDataTable(data_chart);

        var options = {
          // title: 
          is3D: true
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
        $('#total-crawlers').removeClass('d-none');
      }
      drawChart();
</script>
<script type="text/javascript">
	$(function () {
		
		$('#btn-start').on('click', function () {
		  $.post($(this).data('url'));
		});

		var tbody = $('tbody#crawlers'),
			url = tbody.data('url'),
			total_crawlers = $('#total-crawlers');
		setInterval(function () {
			$.getJSON(url, function (data) {
				if (typeof data.total != "undefined") {
					total_crawlers.text(data.total);
				}
				if (typeof data.chart != "undefined") {
					data_chart = [data_chart_header];
					$.each(data.chart, function (l,v) {
						data_chart.push([l,v]);
					});
					drawChart();
				}
				tbody.find('tr').remove();
				if (typeof data.crawlers != "undefined" && data.crawlers.length > 0) {
					$.each(data.crawlers, function (k, i) {
							tbody.prepend(`<tr>
						      <td>${i.origin}</td>
						      <td>${i.name}</td>
						      <td>${i.object}</td>
						    </tr>`);
					})
				} else {
					tbody.prepend(`<tr><td colspan="3" class="text-center">No records</td></tr>`);
				}
			});
		}, 3000);
	
	});
</script>
@endsection