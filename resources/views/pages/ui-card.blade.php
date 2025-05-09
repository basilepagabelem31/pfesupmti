@extends('layout.default')

@section('title', 'Card')

@push('js')
	<script src="/assets/plugins/masonry-layout/dist/masonry.pkgd.min.js"></script>
	<script src="/assets/plugins/@highlightjs/cdn-assets/highlight.min.js"></script>
	<script src="/assets/js/demo/highlightjs.demo.js"></script>
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
							<li class="breadcrumb-item"><a href="#">UI KITS</a></li>
							<li class="breadcrumb-item active">CARD</li>
						</ul>
						
						<h1 class="page-header">
							Card <small>page header description goes here...</small>
						</h1>
						
						<hr class="mb-4">
						
						<!-- BEGIN #basicUsage -->
						<div id="basicUsage" class="mb-5">
							<h4>Basic Usage</h4>
							<p>
								Bootstrap’s cards provide a flexible and extensible content container with multiple variants and options.
								Please read the <a href="https://getbootstrap.com/docs/5.3/components/card/" target="_blank">official Bootstrap documentation</a> for the full list of options.
							</p>
							<div class="card">
								<div class="card-body">
									<div class="row">
										<div class="col-xl-6">
											<div class="card">
												<div class="card-header fw-bold small">
													Card header here
												</div>
												<div class="card-body">
													<h5 class="card-title">Card title</h5>
													<h6 class="card-subtitle mb-3 text-body text-opacity-50">Card subtitle</h6>
													<p class="card-text mb-3">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
													<a href="#" class="card-link">Card link</a>
													<a href="#" class="card-link">Another link</a>
												</div>
											</div>
										</div>
										<div class="col-xl-6">
											<div class="card">
												<div class="card-header fw-bold small">
													Card with list group
												</div>
												<div class="card-header bg-none p-0">
													<ul class="list-group list-group-flush">
														<li class="list-group-item">Cras justo odio</li>
														<li class="list-group-item">Dapibus ac facilisis in</li>
														<li class="list-group-item">Vestibulum at eros</li>
													</ul>
												</div>
												<div class="card-body">
													<a href="#" class="card-link">Card link</a>
													<a href="#" class="card-link">Another link</a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="hljs-container rounded-bottom">
									<pre><code class="xml" data-url="/assets/data/ui-card/code-1.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #basicUsage -->
						
						<!-- BEGIN #fullscreen -->
						<div id="fullscreen" class="mb-5">
							<h4>Fullscreen (extended feature)</h4>
							<p>
								This is an extended feature from Bootstrap card. Add an attribute
								<code>data-toggle="card-expand"</code> to any link or button to trigger 
								the maximize or minimize the card element
							</p>
							<div class="card">
								<div class="row">
									<div class="col-lg-8">
										<div class="card-body">
											<div class="card">
												<div class="card-header fw-bold small d-flex">
													<span class="flex-grow-1">Card header here</span>
													<a href="#" data-toggle="card-expand" class="text-body text-opacity-50 text-decoration-none"><i class="fa fa-fw fa-expand"></i> Expand</a>
												</div>
												<div class="card-body">
													<h5 class="card-title">Card title</h5>
													<h6 class="card-subtitle mb-3 text-body text-opacity-50">Card subtitle</h6>
													<p class="card-text mb-3">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
													<a href="#" class="card-link">Card link</a>
													<a href="#" class="card-link">Another link</a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="hljs-container rounded-bottom">
									<pre><code class="xml" data-url="/assets/data/ui-card/code-2.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #fullscreen -->
						
						<!-- BEGIN #sizing -->
						<div id="sizing" class="mb-5">
							<h4>Sizing</h4>
							<p>
								Cards assume no specific <code>width</code> to start, so they’ll be 100% wide unless otherwise stated. You can change this as needed with custom CSS, grid classes, grid Sass mixins, or utilities.
							</p>
							<div class="card">
								<div class="card-body">
									<div class="row">
										<div class="col-sm-8">
											<div class="card">
												<div class="card-body">
													<h5 class="card-title">Special title treatment</h5>
													<p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
													<a href="#" class="btn btn-outline-theme">Go somewhere</a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="hljs-container rounded-bottom">
									<pre><code class="xml" data-url="/assets/data/ui-card/code-3.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #sizing -->
						
						<!-- BEGIN #navigation -->
						<div id="navigation" class="mb-5">
							<h4>Navigation</h4>
							<p>
								Add some navigation to a card’s header (or block) with Bootstrap’s nav components.
							</p>
							<div class="card">
								<div class="card-body">
									<div class="row">
										<div class="col-xl-6">
											<div class="small text-body text-opacity-50 mb-3"><b class="fw-bold">NAV TABS</b></div>
											<div class="card mb-3">
												<div class="card-header">
													<ul class="nav nav-tabs card-header-tabs">
														<li class="nav-item">
															<a class="nav-link active" href="#">Active</a>
														</li>
														<li class="nav-item">
															<a class="nav-link" href="#">Link</a>
														</li>
														<li class="nav-item">
															<a class="nav-link disabled" href="#">Disabled</a>
														</li>
													</ul>
												</div>
												<div class="card-body">
													<h5 class="card-title">Special title treatment</h5>
													<p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
													<a href="#" class="btn btn-outline-theme">Go somewhere</a>
												</div>
											</div>
										</div>
										<div class="col-xl-6">
											<div class="small text-body text-opacity-50 mb-3"><b class="fw-bold">NAV PILLS</b></div>
											<div class="card mb-3">
												<div class="card-header">
													<ul class="nav nav-pills card-header-pills">
														<li class="nav-item">
															<a class="nav-link active" href="#">Active</a>
														</li>
														<li class="nav-item">
															<a class="nav-link" href="#">Link</a>
														</li>
														<li class="nav-item">
															<a class="nav-link disabled" href="#">Disabled</a>
														</li>
													</ul>
												</div>
												<div class="card-body">
													<h5 class="card-title">Special title treatment</h5>
													<p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
													<a href="#" class="btn btn-outline-theme">Go somewhere</a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="hljs-container rounded-bottom">
									<pre><code class="xml" data-url="/assets/data/ui-card/code-4.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #navigation -->
						
						<!-- BEGIN #images -->
						<div id="images" class="mb-5">
							<h4>Images</h4>
							<p>
								Cards include a few options for working with images. Choose from appending “image caps” at either end of a card, overlaying images with card content, or simply embedding the image in a card.
							</p>
							<div class="card">
								<div class="card-body">
									<div class="row">
										<div class="col-xl-6">
											<div class="small text-body text-opacity-50 mb-3"><b class="fw-bold">IMAGE CAPS</b></div>
											<div class="card mb-3">
												<div class="card-body pb-0">
													<img src="https://placehold.co/600x400/c9d2e3/212837" alt="" class="card-img-top">
												</div>
												<div class="card-body">
													<h5 class="card-title">Card title</h5>
													<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
													<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
												</div>
											</div>
										</div>
										<div class="col-xl-6">
											<div class="small text-body text-opacity-50 mb-3"><b class="fw-bold">IMAGE OVERLAY</b></div>
											<div class="card p-3 mb-3">
												<img src="https://placehold.co/600x750/000000/ffffff" alt="" class="card-img">
												<div class="card-img-overlay m-3 text-white">
													<h5 class="card-title">Card title</h5>
													<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
													<p class="card-text">Last updated 3 mins ago</p>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="hljs-container rounded-bottom">
									<pre><code class="xml" data-url="/assets/data/ui-card/code-5.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #images -->
						
						<!-- BEGIN #horizontal -->
						<div id="horizontal" class="mb-5">
							<h4>Horizontal</h4>
							<p>
								Using a combination of grid and utility classes, cards can be made horizontal in a mobile-friendly and responsive way.
							</p>
							<div class="card">
								<div class="card-body">
									<div class="card mb-3">
										<div class="card-body">
											<div class="row gx-0 align-items-center">
												<div class="col-md-4">
													<img src="https://placehold.co/480x360/c9d2e3/212837" alt="" class="card-img rounded-0">
												</div>
												<div class="col-md-8">
													<div class="card-body">
														<h5 class="card-title">Card title</h5>
														<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
														<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="hljs-container rounded-bottom">
									<pre><code class="xml" data-url="/assets/data/ui-card/code-6.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #horizontal -->
						
						<!-- BEGIN #cardStyles -->
						<div id="cardStyles" class="mb-5">
							<h4>Card Styles</h4>
							<p>
								Cards include various options for customizing their backgrounds, borders, and color.
							</p>
							<div class="card">
								<div class="card-body">
									<div class="row">
										<div class="col-xl-6">
											<div class="small text-body text-opacity-50 mb-3"><b class="fw-bold">BACKGROUND AND BORDER</b></div>
											<div class="card border-theme bg-theme bg-opacity-25 mb-3">
												<div class="card-header border-theme fw-bold small text-body">HEADER</div>
												<div class="card-body">
													<h5 class="card-title">Primary card title</h5>
													<p class="card-text text-body text-opacity-75">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
												</div>
											</div>
										</div>
										<div class="col-xl-6">
											<div class="small text-body text-opacity-50 mb-3"><b class="fw-bold">BORDER AND COLOR</b></div>
											<div class="card border-theme mb-3">
												<div class="card-header border-theme text-theme fw-bold small">HEADER</div>
												<div class="card-body">
													<h5 class="card-title text-theme">Primary card title</h5>
													<p class="card-text text-theme text-opacity-75">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="hljs-container rounded-bottom">
									<pre><code class="xml" data-url="/assets/data/ui-card/code-7.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #cardStyles -->
						
						<!-- BEGIN #cardGroups -->
						<div id="cardGroups" class="mb-5">
							<h4>Card groups</h4>
							<p>
								Use card groups to render cards as a single, attached element with equal width and height columns. Card groups use <code>display: flex;</code> to achieve their uniform sizing.
							</p>
							<div class="card">
								<div class="card-body">
									<div class="card-group">
										<div class="card">
											<div class="card-body">
												<h5 class="card-title">Card title</h5>
												<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
												<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
											</div>
										</div>
										<div class="card">
											<div class="card-body">
												<h5 class="card-title">Card title</h5>
												<p class="card-text">This card has supporting text below as a natural lead-in to additional content.</p>
												<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
											</div>
										</div>
										<div class="card">
											<div class="card-body">
												<h5 class="card-title">Card title</h5>
												<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This card has even longer content than the first to show that equal height action.</p>
												<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
											</div>
										</div>
									</div>
								</div>
								<div class="hljs-container rounded-bottom">
									<pre><code class="xml" data-url="/assets/data/ui-card/code-8.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #cardGroups -->
						
						<!-- BEGIN #gridCards -->
						<div id="gridCards" class="mb-5">
							<h4>Grid cards</h4>
							<p>
								Use the Bootstrap grid system and its <a href="https://getbootstrap.com/docs/5.3/layout/grid/#row-columns"><code>.row-cols</code> classes</a> to control how many grid columns (wrapped around your cards) you show per row. For example, here’s <code>.row-cols-1</code> laying out the cards on one column, and <code>.row-cols-md-2</code> splitting four cards to equal width across multiple rows, from the medium breakpoint up.
							</p>
							<div class="card">
								<div class="card-body">
									<div class="row row-cols-1 row-cols-md-2 g-3">
										<div class="col">
											<div class="card h-100">
												<div class="card-body">
													<h5 class="card-title">Card title</h5>
													<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
													<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
												</div>
											</div>
										</div>
										<div class="col">
											<div class="card h-100">
												<div class="card-body">
													<h5 class="card-title">Card title</h5>
													<p class="card-text">This card has supporting text below as a natural lead-in to additional content.</p>
													<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
												</div>
											</div>
										</div>
										<div class="col">
											<div class="card h-100">
												<div class="card-body">
													<h5 class="card-title">Card title</h5>
													<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This card has even longer content than the first to show that equal height action.</p>
													<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
												</div>
											</div>
										</div>
										<div class="col">
											<div class="card h-100">
												<div class="card-body">
													<h5 class="card-title">Card title</h5>
													<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
													<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="hljs-container rounded-bottom">
									<pre><code class="xml" data-url="/assets/data/ui-card/code-9.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #cardGroups -->
						
						<!-- BEGIN #cardColumns -->
						<div id="cardColumns" class="mb-5">
							<h4>Card columns</h4>
							<p>
								In <code>v5</code>, cards can be organized into Masonry-like columns with masonry javascript included.
							</p>
							<div class="card">
								<div class="card-body">
									<div class="row" data-masonry='{"percentPosition": true }'>
										<div class="col-sm-6 col-lg-4 mb-4">
											<div class="card">
												<div class="card-body">
													<h5 class="card-title">Card title that wraps to a new line</h5>
													<p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-lg-4 mb-4">
											<div class="card p-3">
												<blockquote class="blockquote mb-0 card-body">
													<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
													<footer class="blockquote-footer">
														<small class="text-muted">
															Someone famous in <cite title="Source Title">Source Title</cite>
														</small>
													</footer>
												</blockquote>
											</div>
										</div>
										<div class="col-sm-6 col-lg-4 mb-4">
											<div class="card">
												<div class="card-body">
													<h5 class="card-title">Card title</h5>
													<p class="card-text">This card has supporting text below as a natural lead-in to additional content.</p>
													<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-lg-4 mb-4">
											<div class="card text-center">
												<blockquote class="blockquote mb-0 card-body">
													<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat.</p>
													<footer class="blockquote-footer text-white">
														<small>
															Someone famous in <cite title="Source Title">Source Title</cite>
														</small>
													</footer>
												</blockquote>
											</div>
										</div>
										<div class="col-sm-6 col-lg-4 mb-4">
											<div class="card text-center">
												<div class="card-body">
													<h5 class="card-title">Card title</h5>
													<p class="card-text">This card has a regular title and short paragraphy of text below it.</p>
													<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-lg-4 mb-4">
											<div class="card p-3 text-end">
												<blockquote class="blockquote mb-0">
													<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
													<footer class="blockquote-footer">
														<small class="text-muted">
															Someone famous in <cite title="Source Title">Source Title</cite>
														</small>
													</footer>
												</blockquote>
											</div>
										</div>
										<div class="col-sm-6 col-lg-4 mb-4">
											<div class="card">
												<div class="card-body">
													<h5 class="card-title">Card title</h5>
													<p class="card-text">This is another card with title and supporting text below. This card has some additional content to make it slightly taller overall.</p>
													<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="hljs-container rounded-bottom">
									<pre><code class="xml" data-url="/assets/data/ui-card/code-10.json"></code></pre>
								</div>
							</div>
						</div>
						<!-- END #cardColumns -->
					</div>
					<!-- END col-9-->
					<!-- BEGIN col-3 -->
					<div class="col-xl-3">
						<!-- BEGIN #sidebar-bootstrap -->
						<nav id="sidebar-bootstrap" class="navbar navbar-sticky d-none d-xl-block">
							<nav class="nav">
								<a class="nav-link" href="#basicUsage" data-toggle="scroll-to">Basic usage</a>
								<a class="nav-link" href="#fullscreen" data-toggle="scroll-to">Fullscreen (extended feature)</a>
								<a class="nav-link" href="#sizing" data-toggle="scroll-to">Sizing</a>
								<a class="nav-link" href="#navigation" data-toggle="scroll-to">Navigation</a>
								<a class="nav-link" href="#images" data-toggle="scroll-to">Images</a>
								<a class="nav-link" href="#horizontal" data-toggle="scroll-to">Horizontal</a>
								<a class="nav-link" href="#cardStyles" data-toggle="scroll-to">Card styles</a>
								<a class="nav-link" href="#cardGroups" data-toggle="scroll-to">Card groups</a>
								<a class="nav-link" href="#cardDecks" data-toggle="scroll-to">Card decks</a>
								<a class="nav-link" href="#cardColumns" data-toggle="scroll-to">Card columns</a>
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
