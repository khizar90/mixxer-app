@extends('panel-v1.layouts.base')
@section('title', 'Dashborad')
@section('main', 'Analytics Management')
@section('link')
    <link rel="stylesheet" href="/panel-v1/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <!-- Statistics -->
                <div class="col-lg-12 mb-4 col-md-12">
                    <div class="card card1">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title mb-0 fw-bold">Mixxer Analytics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4 mb-4">
                                <div class="col-sm-6 col-xl-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="content-left">
                                                    <span>Total Mixxer</span>
                                                    <div class="d-flex align-items-center my-1">
                                                        <h4 class="mb-0 me-2">{{ $total }}</h4>
                                                    </div>
                                                </div>
                                              
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            

                                <div class="col-sm-6 col-xl-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="content-left">
                                                    <span>Up Coming</span>
                                                    <div class="d-flex align-items-center my-1">
                                                        <h4 class="mb-0 me-2">{{ $upComing }}</h4>
                                                    </div>
                                                </div>
                                              
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-xl-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="content-left">
                                                    <span>Completed</span>
                                                    <div class="d-flex align-items-center my-1">
                                                        <h4 class="mb-0 me-2">{{ $complete }}</h4>
                                                    </div>
                                                </div>
                                              
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    @endsection
