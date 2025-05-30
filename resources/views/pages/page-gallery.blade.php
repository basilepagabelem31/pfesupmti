@extends('layout.default', [
	'appClass' => 'app-content-full-height',
	'appContentClass' => 'p-0',
	'htmlAttribute' => ' itemscope itemtype="http://schema.org/WebPage"'
])

@section('title', 'Gallery')

@push('css')
  <link href="/assets/plugins/photoswipe/dist/photoswipe.css" rel="stylesheet">
@endpush

@push('js')
	<script src="/assets/js/demo/page-gallery.demo.js" type="module"></script>
@endpush

@section('content')
  <!-- BEGIN d-flex -->
	<div class="d-block d-md-flex align-items-stretch h-100">
		<!-- BEGIN gallery-menu-container -->
		<div class="gallery-menu-container">
			<!-- BEGIN scrollbar -->
			<div data-scrollbar="true" data-height="100%" data-skip-mobile="true">
				<!-- BEGIN gallery-menu -->
				<div class="gallery-menu">
					<div class="gallery-menu-header">Main</div>
					<div class="gallery-menu-item"><a href="#" class="gallery-menu-link active"><i class="fa fa-fw fa-image me-1"></i> Photos</a></div>
					<div class="gallery-menu-item"><a href="#" class="gallery-menu-link"><i class="fa fa-fw fa-heart me-1"></i> Memories</a></div>
					<div class="gallery-menu-header">Shared</div>
					<div class="gallery-menu-item"><a href="#" class="gallery-menu-link"><i class="fa fa-fw fa-cloud me-1"></i> Activity</a></div>
					<div class="gallery-menu-header">Albums</div>
					<div class="gallery-menu-item"><a href="#" class="gallery-menu-link"><i class="fa fa-fw fa-camera me-1"></i> All Photos</a></div>
					<div class="gallery-menu-item"><a href="#" class="gallery-menu-link"><i class="fa fa-fw fa-user me-1"></i> People</a></div>
					<div class="gallery-menu-item"><a href="#" class="gallery-menu-link"><i class="fa fa-fw fa-map-pin me-1"></i> Places</a></div>
					<div class="gallery-menu-item"><a href="#" class="gallery-menu-link"><i class="fa fa-fw fa-camera-retro me-1"></i> Selfies</a></div>
					<div class="gallery-menu-item"><a href="#" class="gallery-menu-link"><i class="fa fa-fw fa-video me-1"></i> Panaromas</a></div>
					<div class="gallery-menu-item"><a href="#" class="gallery-menu-link"><i class="fa fa-fw fa-star me-1"></i> Depth Effect</a></div>
					<div class="gallery-menu-item"><a href="#" class="gallery-menu-link"><i class="fa fa-fw fa-mobile me-1"></i> Screenshots</a></div>
				</div>
				<!-- END gallery-menu -->
			</div>
			<!-- end scrollbar -->
		</div>
		<!-- END gallery-menu-container -->
		<!-- BEGIN gallery-content-container -->
		<div class="gallery-content-container">
			<!-- BEGIN scrollbar -->
			<div data-scrollbar="true" data-height="100%">
				<!-- BEGIN gallery-content -->
				<div class="gallery-content">
					<!-- BEGIN gallery -->
					<div class="gallery">
						<!-- BEGIN gallery-header -->
						<div class="d-flex align-items-center mb-3">
							<!-- BEGIN gallery-title -->
							<div class="gallery-title mb-0">
								<a href="#">
								Wedding <i class="fa fa-chevron-right"></i> 
								<small>May 11, 2022</small>
								</a>
							</div>
							<!-- END gallery-title -->
							
							<!-- BEGIN btn-group -->
							<div class="ms-auto btn-group">
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-play"></i></a>
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-plus"></i></a>
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-upload"></i></a>
							</div>
							<!-- END btn-group -->
						</div>
						<!-- END gallery-header -->
						
						<!-- BEGIN gallery-image -->
						<div class="gallery-image">
							<ul class="gallery-image-list">
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-42.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-42.jpg" alt="Wedding Image 1" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-43.jpg" data-pswp-width="752" data-pswp-height="442"><img src="/assets/img/gallery/gallery-43.jpg" alt="Wedding Image 2" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-44.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-44.jpg" alt="Wedding Image 3" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-45.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-45.jpg" alt="Wedding Image 4" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-46.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-46.jpg" alt="Wedding Image 5" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-47.jpg" data-pswp-width="752" data-pswp-height="532"><img src="/assets/img/gallery/gallery-47.jpg" alt="Wedding Image 6" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-48.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-48.jpg" alt="Wedding Image 7" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-49.jpg" data-pswp-width="752" data-pswp-height="1130"><img src="/assets/img/gallery/gallery-49.jpg" alt="Wedding Image 8"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-50.jpg" data-pswp-width="752" data-pswp-height="1128"><img src="/assets/img/gallery/gallery-50.jpg" alt="Wedding Image 9"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-51.jpg" data-pswp-width="752" data-pswp-height="866"><img src="/assets/img/gallery/gallery-51.jpg" alt="Wedding Image 10"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-52.jpg" data-pswp-width="752" data-pswp-height="752"><img src="/assets/img/gallery/gallery-52.jpg" alt="Wedding Image 11"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-53.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-53.jpg" alt="Wedding Image 12" class="img-portrait"></a></li>
							</ul>
						</div>
						<!-- END gallery-image -->
					</div>
					<!-- END gallery -->
					<!-- BEGIN gallery -->
					<div class="gallery">
						<!-- BEGIN gallery-header -->
						<div class="d-flex align-items-center mb-3">
							<!-- BEGIN gallery-title -->
							<div class="gallery-title mb-0">
								<a href="#">
								Collections #339 <i class="fa fa-chevron-right"></i> 
								<small>May 8, 2022</small>
								</a>
							</div>
							<!-- END gallery-title -->
							
							<!-- BEGIN btn-group -->
							<div class="ms-auto btn-group">
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-play"></i></a>
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-plus"></i></a>
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-upload"></i></a>
							</div>
							<!-- END btn-group -->
						</div>
						<!-- END gallery-header -->
						
						<!-- BEGIN gallery-image -->
						<div class="gallery-image">
							<ul class="gallery-image-list">
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-21.jpg" data-pswp-width="752" data-pswp-height="564"><img src="/assets/img/gallery/gallery-21.jpg" alt="Collection #145 Image 1"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-22.jpg" data-pswp-width="752" data-pswp-height="564"><img src="/assets/img/gallery/gallery-22.jpg" alt="Collection #145 Image 2"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-23.jpg" data-pswp-width="752" data-pswp-height="486"><img src="/assets/img/gallery/gallery-23.jpg" alt="Collection #145 Image 3" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-24.jpg" data-pswp-width="752" data-pswp-height="564"><img src="/assets/img/gallery/gallery-24.jpg" alt="Collection #145 Image 4"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-25.jpg" data-pswp-width="752" data-pswp-height="1128"><img src="/assets/img/gallery/gallery-25.jpg" alt="Collection #145 Image 5"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-26.jpg" data-pswp-width="752" data-pswp-height="564"><img src="/assets/img/gallery/gallery-26.jpg" alt="Collection #145 Image 6"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-27.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-27.jpg" alt="Collection #145 Image 7" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-28.jpg" data-pswp-width="752" data-pswp-height="564"><img src="/assets/img/gallery/gallery-28.jpg" alt="Collection #145 Image 8"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-29.jpg" data-pswp-width="752" data-pswp-height="1128"><img src="/assets/img/gallery/gallery-29.jpg" alt="Collection #145 Image 9"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-30.jpg" data-pswp-width="752" data-pswp-height="940"><img src="/assets/img/gallery/gallery-30.jpg" alt="Collection #145 Image 10"></a></li>
							</ul>
						</div>
						<!-- END gallery-image -->
					</div>
					<!-- END gallery -->
					<!-- BEGIN gallery -->
					<div class="gallery">
						<!-- BEGIN gallery-header -->
						<div class="d-flex align-items-center mb-3">
							<!-- BEGIN gallery-title -->
							<div class="gallery-title mb-0">
								<a href="#">
								Collections #144 <i class="fa fa-chevron-right"></i> 
								<small>May 4, 2022</small>
								</a>
							</div>
							<!-- END gallery-title -->
							
							<!-- BEGIN btn-group -->
							<div class="ms-auto btn-group">
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-play"></i></a>
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-plus"></i></a>
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-upload"></i></a>
							</div>
							<!-- END btn-group -->
						</div>
						<!-- END gallery-header -->
						
						<!-- BEGIN gallery-image -->
						<div class="gallery-image">
							<ul class="gallery-image-list">
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-31.jpg" data-pswp-width="752" data-pswp-height="752"><img src="/assets/img/gallery/gallery-31.jpg" alt="Collection #144 Image 1"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-32.jpg" data-pswp-width="752" data-pswp-height="1128"><img src="/assets/img/gallery/gallery-32.jpg" class="img-portrait" alt="Collection #144 Image 2"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-33.jpg" data-pswp-width="752" data-pswp-height="564"><img src="/assets/img/gallery/gallery-33.jpg" alt="Collection #144 Image 3"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-34.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-34.jpg" alt="Collection #144 Image 4" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-35.jpg" data-pswp-width="752" data-pswp-height="1136"><img src="/assets/img/gallery/gallery-35.jpg" alt="Collection #144 Image 5"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-36.jpg" data-pswp-width="752" data-pswp-height="1128"><img src="/assets/img/gallery/gallery-36.jpg" alt="Collection #144 Image 6"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-37.jpg" data-pswp-width="752" data-pswp-height="480"><img src="/assets/img/gallery/gallery-37.jpg" alt="Collection #144 Image 7" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-38.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-38.jpg" alt="Collection #144 Image 8" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-39.jpg" data-pswp-width="752" data-pswp-height="422"><img src="/assets/img/gallery/gallery-39.jpg" alt="Collection #144 Image 9" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-40.jpg" data-pswp-width="752" data-pswp-height="464"><img src="/assets/img/gallery/gallery-40.jpg" alt="Collection #144 Image 10" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-41.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-41.jpg" alt="Collection #144 Image 11" class="img-portrait"></a></li>
							</ul>
						</div>
						<!-- END gallery-image -->
					</div>
					<!-- END gallery -->
					<!-- BEGIN gallery -->
					<div class="gallery">
						<!-- BEGIN gallery-header -->
						<div class="d-flex align-items-center mb-3">
							<!-- BEGIN gallery-title -->
							<div class="gallery-title mb-0">
								<a href="#">
								Collection #143 <i class="fa fa-chevron-right"></i> 
								<small>May 3, 2022</small>
								</a>
							</div>
							<!-- END gallery-title -->
							
							<!-- BEGIN btn-group -->
							<div class="ms-auto btn-group">
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-play"></i></a>
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-plus"></i></a>
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-upload"></i></a>
							</div>
							<!-- END btn-group -->
						</div>
						<!-- END gallery-header -->
						
						<!-- BEGIN gallery-image -->
						<div class="gallery-image">
							<ul class="gallery-image-list">
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-1.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-1.jpg" alt="Collection #143 Image 1" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-2.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-2.jpg" alt="Collection #143 Image 2" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-3.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-3.jpg" alt="Collection #143 Image 3" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-4.jpg" data-pswp-width="752" data-pswp-height="564"><img src="/assets/img/gallery/gallery-4.jpg" alt="Collection #143 Image 4"></a></li>
							</ul>
						</div>
						<!-- END gallery-image -->
					</div>
					<!-- END gallery -->
					<!-- BEGIN gallery -->
					<div class="gallery">
						<!-- BEGIN gallery-header -->
						<div class="d-flex align-items-center mb-3">
							<!-- BEGIN gallery-title -->
							<div class="gallery-title mb-0">
								<a href="#">
									Random <i class="fa fa-chevron-right"></i> 
									<small>May 1, 2022</small>
								</a>
							</div>
							<!-- END gallery-title -->
							
							<!-- BEGIN btn-group -->
							<div class="ms-auto btn-group">
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-play"></i></a>
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-plus"></i></a>
								<a href="#" class="btn btn-outline-default btn-sm"><i class="fa fa-upload"></i></a>
							</div>
							<!-- END btn-group -->
						</div>
						<!-- END gallery-header -->
						
						<!-- BEGIN gallery-image -->
						<div class="gallery-image">
							<ul class="gallery-image-list">
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-5.jpg" data-pswp-width="752" data-pswp-height="500"><img src="/assets/img/gallery/gallery-5.jpg" alt="Random Image 1" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-6.jpg" data-pswp-width="752" data-pswp-height="564"><img src="/assets/img/gallery/gallery-6.jpg" alt="Random Image 2"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-7.jpg" data-pswp-width="752" data-pswp-height="476"><img src="/assets/img/gallery/gallery-7.jpg" alt="Random Image 3" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-8.jpg" data-pswp-width="752" data-pswp-height="472"><img src="/assets/img/gallery/gallery-8.jpg" alt="Random Image 4" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-9.jpg" data-pswp-width="752" data-pswp-height="504"><img src="/assets/img/gallery/gallery-9.jpg" alt="Random Image 5" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-10.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-10.jpg" alt="Random Image 6" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-11.jpg" data-pswp-width="752" data-pswp-height="564"><img src="/assets/img/gallery/gallery-11.jpg" alt="Random Image 7"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-12.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-12.jpg" alt="Random Image 8" class="img-portrait"></a></li>
								<li><a href="javascript:;" data-pswp-src="/assets/img/gallery/gallery-13.jpg" data-pswp-width="752" data-pswp-height="502"><img src="/assets/img/gallery/gallery-13.jpg" alt="Random Image 9" class="img-portrait"></a></li>
							</ul>
						</div>
						<!-- END gallery-image -->
					</div>
					<!-- END gallery -->
				</div>
				<!-- END gallery-content -->
			</div>
			<!-- END scrollbar -->
		</div>
		<!-- END gallery-content-container -->
	</div>
	<!-- END d-flex -->
@endsection
