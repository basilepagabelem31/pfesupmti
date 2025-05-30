@extends('layout.empty')

@section('title', 'Customer Order')

@push('css')
	<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet">
	<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet">
@endpush

@push('js')
	<script src="/assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
	<script src="/assets/js/demo/pos-table-booking.demo.js"></script>
@endpush

@section('content')
  <!-- BEGIN pos -->
	<div class="pos pos-vertical pos-with-header" id="pos">
		<!-- BEGIN pos-container -->
		<div class="pos-container">
			<!-- BEGIN pos-header -->
			<div class="pos-header">
				<div class="logo">
					<a href="/pos/counter-checkout">
						<div class="logo-img"><i class="fa fa-bowl-rice" style="font-size: 1.5rem;"></i></div>
						<div class="logo-text">Pine & Dine</div>
					</a>
				</div>
				<div class="time" id="time">00:00</div>
				<div class="nav">
					<div class="nav-item">
						<a href="/pos/kitchen-order" class="nav-link">
							<i class="far fa-clock nav-icon"></i>
						</a>
					</div>
					<div class="nav-item">
						<a href="/pos/table-booking" class="nav-link">
							<i class="far fa-calendar-check nav-icon"></i>
						</a>
					</div>
					<div class="nav-item">
						<a href="/pos/menu-stock" class="nav-link">
							<i class="fa fa-chart-pie nav-icon"></i>
						</a>
					</div>
				</div>
			</div>
			<!-- END pos-header -->
		
			<!-- BEGIN pos-content -->
			<div class="pos-content">
				<div class="pos-content-container h-100 p-4">
					<div class="d-md-flex align-items-center mb-4">
						<div class="flex-1">
							<div class="fs-20px mb-1">Available Table (8/20)</div>
							<div class="mb-2 mb-md-0 d-flex">
								<div class="d-flex align-items-center me-3">
									<i class="fa fa-circle fa-fw text-body text-opacity-25 fs-9px me-1"></i> Completed
								</div>
								<div class="d-flex align-items-center me-3">
									<i class="fa fa-circle fa-fw text-warning fs-9px me-1"></i> Upcoming
								</div>
								<div class="d-flex align-items-center me-3">
									<i class="fa fa-circle fa-fw text-theme fs-9px me-1"></i> In-progress
								</div>
							</div>
						</div>
						<div>
							<div class="input-group date mb-0" data-render="datepicker" data-date-format="dd-mm-yyyy" data-date-start-date="Date.default">
								<input type="text" class="form-control" placeholder="Today's">
								<span class="input-group-text input-group-addon">
									<i class="far fa-calendar fa-lg"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="row gx-4">
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">01</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="pe-1 text-theme">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Reserved by Sean</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
											<div class="info">Reserved by Irene Wong (4pax)</div>
											<div class="status upcoming"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">Reserved by Irene Wong (4pax)</div>
											<div class="status upcoming"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">02</div>
												<div class="desc">max 8 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Reserved by John (8pax)</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">Reserved by Terry (6pax)</div>
											<div class="status upcoming"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">03</div>
												<div class="desc">max 8 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Walk in lunch</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">Reserved by Lisa (8pax)</div>
											<div class="status upcoming"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">Reserved by Lisa (8pax)</div>
											<div class="status upcoming"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">Reserved by Terry</div>
											<div class="status upcoming"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">04</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Walk in lunch</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">Reserved by Richard (4pax)</div>
											<div class="status upcoming"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">Reserved by Richard (4pax)</div>
											<div class="status upcoming"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">Reserved by Paul (3pax)</div>
											<div class="status upcoming"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">Reserved by Paul (3pax)</div>
											<div class="status upcoming"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">Reserved by Paul (3pax)</div>
											<div class="status upcoming"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">05</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Walk in lunch</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">06</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="text-theme">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">07</div>
												<div class="desc">max 6 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Walk in lunch</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">08</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Walk in lunch</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">09</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="text-theme">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">10</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Walk in lunch</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">11</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="text-theme">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">12</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="text-theme">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">13</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="text-theme">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">14</div>
												<div class="desc">max 6 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Walk in lunch</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">15</div>
												<div class="desc">max 6 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Walk in lunch</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">16</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="text-theme">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">17</div>
												<div class="desc">max 4 pax</div>
											</div>
											<div class="text-theme">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">18</div>
												<div class="desc">max 6 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Walk in lunch</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">19</div>
												<div class="desc">max 6 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Walk in lunch</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<a href="#" data-bs-toggle="modal" data-bs-target="#modalPosBooking" class="pos-table-booking">
								<div class="pos-table-booking-container">
									<div class="pos-table-booking-header">
										<div class="d-flex align-items-center">
											<div class="flex-1">
												<div class="title">Table</div>
												<div class="no">20</div>
												<div class="desc">max 6 pax</div>
											</div>
											<div class="text-body text-opacity-25">
												<i class="far fa-check-circle fa-3x"></i>
											</div>
										</div>
									</div>
									<div class="pos-table-booking-body">
										<div class="booking">
											<div class="time">08:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">09:00am</div>
											<div class="info">Walk in breakfast</div>
											<div class="status completed"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">10:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">11:00am</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking highlight">
											<div class="time">12:00pm</div>
											<div class="info">Walk in lunch</div>
											<div class="status in-progress"><i class="fa fa-circle"></i></div>
										</div>
										<div class="booking">
											<div class="time">01:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">02:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">03:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">04:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">05:00pm</div>
												<div class="info-title">-</div>
												<div class="info-desc"></div>
										</div>
										<div class="booking">
											<div class="time">06:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">07:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">08:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">09:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
										<div class="booking">
											<div class="time">10:00pm</div>
											<div class="info">-</div>
											<div class="status"></div>
										</div>
									</div>
								</div>
							</a>
						</div>
					</div>
				</div>
			</div>
			<!-- END pos-content -->
		</div>
		<!-- END pos-container -->
	</div>
	<!-- END pos -->
	
	<!-- BEGIN #modalPosBooking -->
	<div class="modal modal-pos fade" id="modalPosBooking">
		<!-- BEGIN modal-dialog -->
		<div class="modal-dialog modal-lg">
			<!-- BEGIN modal-content -->
			<div class="modal-content border-0">
				<!-- BEGIN card -->
				<div class="card">
					<!-- BEGIN card-body -->
					<div class="card-body p-0">
						<!-- BEGIN modal-header -->
						<div class="modal-header align-items-center">
							<h5 class="modal-title d-flex align-items-end">
								Table 01 
								<small class="fs-14px ms-2 text-body text-opacity-50 fw-semibold">max 4 pax</small>
							</h5>
							<a href="#" data-bs-dismiss="modal" class="ms-auto btn-close"></a>
						</div>
						<!-- END modal-header -->
						<!-- BEGIN modal-body -->
						<div class="modal-body">
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">08:00am</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">09:00am</div>
											<input type="text" class="form-control" placeholder="" value="Reserved by Sean">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">10:00am</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">11:00am</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">12:00pm</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">01:00pm</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">02:00pm</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">03:00pm</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">04:00pm</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">05:00pm</div>
											<input type="text" class="form-control" placeholder="" value="Reserved by Irene Wong (4pax)">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">06:00pm</div>
											<input type="text" class="form-control" placeholder="" value="Reserved by Irene Wong (4pax)">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">07:00pm</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">08:00pm</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">09:00pm</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
									<div class="form-group mb-2">
										<div class="input-group">
											<div class="input-group-text fw-semibold w-90px fs-13px">10:00pm</div>
											<input type="text" class="form-control" placeholder="">
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- END modal-body -->
						<!-- BEGIN modal-footer -->
						<div class="modal-footer">
							<a href="#" class="btn btn-default fs-13px fw-semibold w-100px" data-bs-dismiss="modal">Cancel</a>
							<button type="submit" class="btn btn-theme fs-13px fw-semibold w-100px">Book</button>
						</div>
						<!-- END modal-footer -->
					</div>
					<!-- END card-body -->
				</div>
				<!-- END card -->
			</div>
			<!-- END modal-content -->
		</div>
		<!-- END modal-dialog -->
	</div>
	<!-- END #modalPosBooking -->
@endsection
