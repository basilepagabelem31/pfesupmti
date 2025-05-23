@extends('layout.default')

@section('title', 'Apexcharts.js')

@push('js')
	<script src="/assets/plugins/@highlightjs/cdn-assets/highlight.min.js"></script>
	<script src="/assets/js/demo/highlightjs.demo.js"></script>
	<script src="/assets/plugins/apexcharts/dist/apexcharts.min.js"></script>
	<script src="/assets/js/demo/chart-apex.demo.js"></script>
	<script src="/assets/js/demo/sidebar-scrollspy.demo.js"></script>
@endpush

@section('content')
	<!-- BEGIN container -->
	<div class="container">
		<!-- BEGIN row -->
		<div class="row justify-content-center">
			<!-- BEGIN col-10 -->
			<div class="col-xl-10">
				<!-- BEGIN row -->
				<div class="row">
					<!-- BEGIN col-9 -->
					<div class="col-xl-9">
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Charts</a></li>
							<li class="breadcrumb-item active">Apexcharts.js</li>
						</ul>
					
						<h1 class="page-header">
							Apexcharts.js <small>page header description goes here...</small>
						</h1>
					
						<hr class="mb-4">
					
						<!-- BEGIN #apexChart -->
						<div id="apexChart">
							<h4>Basic Example</h4>
							<p>Apexcharts.js is a modern javaScript charting library to build interactive charts and visualizations with simple API. Please read the <a href="https://apexcharts.com/" target="_blank">official documentation</a> for the full list of options.</p>
						</div>
						<!-- END #apexChart -->
					
						<!-- BEGIN #apexChartLineChart -->
						<div id="apexChartLineChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Line Chart</h6>
									<div id="apexLineChart"></div>
								</div>
								<div class="hljs-container">
									<pre><code class="xml" data-url="/assets/data/chart-apex/code-1.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #chartJsLineChart -->
					
						<!-- BEGIN #apexChartColumnChart -->
						<div id="apexChartColumnChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Column Chart</h6>
									<div id="apexColumnChart"></div>
								</div>
								<div class="hljs-container">
									<pre><code class="xml" data-url="/assets/data/chart-apex/code-2.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #apexChartColumnChart -->
					
						<!-- BEGIN #apexChartAreaChart -->
						<div id="apexChartAreaChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Area Chart</h6>
									<div id="apexAreaChart"></div>
								</div>
								<div class="hljs-container">
									<pre><code class="xml" data-url="/assets/data/chart-apex/code-3.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #apexChartAreaChart -->
					
						<!-- BEGIN #apexChartBarChart -->
						<div id="apexChartBarChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Bar Chart</h6>
									<div id="apexBarChart"></div>
								</div>
								<div class="hljs-container">
									<pre><code class="xml" data-url="/assets/data/chart-apex/code-4.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #apexChartBarChart -->
					
						<!-- BEGIN #apexChartMixedChart -->
						<div id="apexChartMixedChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Mixed Chart</h6>
									<div id="apexMixedChart"></div>
								</div>
								<div class="hljs-container">
									<pre><code class="xml" data-url="/assets/data/chart-apex/code-5.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #apexChartMixedChart -->
					
						<!-- BEGIN #apexChartCandlestickChart -->
						<div id="apexChartCandlestickChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Candlestick Chart</h6>
									<div id="apexCandlestickChart"></div>
								</div>
								<div class="hljs-container">
									<pre><code class="xml" data-url="/assets/data/chart-apex/code-6.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #apexChartCandlestickChart -->
					
						<!-- BEGIN #apexChartBubbleChart -->
						<div id="apexChartBubbleChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Bubble Chart</h6>
									<div id="apexBubbleChart"></div>
								</div>
								<div class="hljs-container">
									<pre><code class="xml" data-url="/assets/data/chart-apex/code-7.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #apexChartBubbleChart -->
					
						<!-- BEGIN #apexChartScatterChart -->
						<div id="apexChartScatterChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Scatter Chart</h6>
									<div id="apexScatterChart"></div>
								</div>
								<div class="hljs-container">
									<pre><code class="xml" data-url="/assets/data/chart-apex/code-8.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #apexChartScatterChart -->
					
						<!-- BEGIN #apexChartHeatmapChart -->
						<div id="apexChartHeatmapChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Heatmap Chart</h6>
									<div id="apexHeatmapChart"></div>
								</div>
								<div class="hljs-container">
									<pre><code class="xml" data-url="/assets/data/chart-apex/code-9.json"></code></pre>
								</div>
								<div class="hljs-container"><pre><code class="xml"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #apexChartHeatmapChart -->
					
						<!-- BEGIN #apexChartPieChart -->
						<div id="apexChartPieChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Pie Chart</h6>
									<div id="apexPieChart"></div>
								</div>
								<div class="hljs-container">
									<pre><code class="xml" data-url="/assets/data/chart-apex/code-10.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #apexChartPieChart -->
					
						<!-- BEGIN #apexChartRadialBarChart -->
						<div id="apexChartRadialBarChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Radial Bar Chart</h6>
									<div id="apexRadialBarChart"></div>
								</div>
								<div class="hljs-container">
									<pre><code class="xml" data-url="/assets/data/chart-apex/code-11.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #apexChartRadialBarChart -->
					
						<!-- BEGIN #apexChartRadarChart -->
						<div id="apexChartRadarChart" class="mb-5">
							<div class="card">
								<div class="card-body">
									<h6 class="mb-3">Radar Chart</h6>
											<div id="apexRadarChart"></div>
										</div>
										<div class="hljs-container">
											<pre><code class="xml" data-url="/assets/data/chart-apex/code-12.json"></code></pre>
										</div>
									</div>
								</div>
								<!-- END #apexChartRadarChart -->
							</div>
							<!-- END col-9-->
							<!-- BEGIN col-3 -->
							<div class="col-xl-3">
								<!-- BEGIN #sidebar-bootstrap -->
								<nav id="sidebar-bootstrap" class="navbar navbar-sticky d-none d-xl-block">
									<nav class="nav">
										<a class="nav-link" href="#apexChart" data-toggle="scroll-to">Apexcharts.js</a>
										<a class="nav-link" href="#apexChartLineChart" data-toggle="scroll-to"> - line chart</a>
										<a class="nav-link" href="#apexChartColumnChart" data-toggle="scroll-to"> - column chart</a>
										<a class="nav-link" href="#apexChartAreaChart" data-toggle="scroll-to"> - area chart</a>
										<a class="nav-link" href="#apexChartBarChart" data-toggle="scroll-to"> - bar chart</a>
										<a class="nav-link" href="#apexChartMixedChart" data-toggle="scroll-to"> - mixed chart</a>
										<a class="nav-link" href="#apexChartCandlestickChart" data-toggle="scroll-to"> - candlestick chart</a>
										<a class="nav-link" href="#apexChartBubbleChart" data-toggle="scroll-to"> - bubble chart</a>
										<a class="nav-link" href="#apexChartScatterChart" data-toggle="scroll-to"> - scatter chart</a>
										<a class="nav-link" href="#apexChartHeatmapChart" data-toggle="scroll-to"> - heatmap chart</a>
										<a class="nav-link" href="#apexChartPieChart" data-toggle="scroll-to"> - pie chart</a>
										<a class="nav-link" href="#apexChartRadialBarChart" data-toggle="scroll-to"> - radial bar chart</a>
										<a class="nav-link" href="#apexChartRadarChart" data-toggle="scroll-to"> - radar chart</a>
									</nav>
								</nav>
								<!-- END #sidebar-bootstrap -->
							</div>
							<!-- END col-3 -->
						</div>
						<!-- END row -->
					</div>
					<!-- END col-10 -->
				</div>
				<!-- END row -->
			</div>
			<!-- END container -->
@endsection
