@php
    $type = Session::get('app_type');
    $app_type = 'main';
    if ($type) {
        if ($type == 'affiliate') {
            $app_type = $type;
        }
    }
    if ($app_type == 'main') {
        $iosN = App\Models\AppSetting::where('name', 'ios-new-version')->first();
        $iosO = App\Models\AppSetting::where('name', 'ios-old-version')->first();
        $iosM = App\Models\AppSetting::where('name', 'ios-version-message')->first();
    } else {
        $iosN = App\Models\AppSetting::where('type', 'affiliate')->where('name', 'ios-new-version')->first();
        $iosO = App\Models\AppSetting::where('type', 'affiliate')->where('name', 'ios-old-version')->first();
        $iosM = App\Models\AppSetting::where('type', 'affiliate')->where('name', 'ios-version-message')->first();
    }
@endphp
@extends('panel-v1.layouts.base')
@section('title', 'Android Version')
@section('main', 'Android Version Management')
@section('link')
    <link rel="stylesheet" href="/panel-v1/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Users List Table -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div class="col-2">
                            <select id="defaultSelect" class="form-select form-control" name="status" required="">
                                <option value="android" {{ $status == 'android' ? 'selected' : '' }}>Android
                                </option>
                                <option value="iOS" {{ $status == 'iOS' ? 'selected' : '' }}>iOS
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="frame__container py-3 mb-5">
                    <div class="row px-4">
                        <h5 class="text-center">iOS Version</h5>
                        <form action="{{ route('dashboard-version-save' , 'iOS') }}" id="addForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body pt-0">

                                <div class="row mb-2">
                                    <div class="col">
                                        <label for="" class="form-label">iOS Old Version</label>
                                        <input type="text" required id="old-ver" class="form-control"
                                            name="ios-old-version" value="{{ $iosO->value ?? '' }}"
                                            placeholder="Enter old version" />

                                    </div>

                                </div>
                                <div class="row mb-2">
                                    <div class="col">
                                        <label for="" class="form-label">iOS New Version</label>
                                        <input type="text" required id="new-ver" class="form-control"
                                            name="ios-new-version" value="{{ $iosN->value ?? '' }}"
                                            placeholder="Enter new version" />

                                    </div>

                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="" class="form-label">Update Message</label>
                                        <input type="text" required id="new-ver" class="form-control"
                                            name="ios-version-message" value="{{ $iosM->value ?? '' }}"
                                            placeholder="Enter update message" />

                                    </div>

                                </div>



                                <div class="row">
                                    <div class="col">
                                        {{-- <button type="submit" class="btn btn-primary saveBtn">Save Category</button> --}}
                                        <button type="submit" value="Submit" class="btn btn-primary saveBtn"
                                            id="signinButton" onclick="showLoader()">
                                            <span id="btntext" style="display: block">Save</span>


                                            <span class="align-middle" id="loader" role="status" style="display: none;">
                                                <span class="spinner-border" style="color: #ffffff" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </span>
                                            </span>
                                            <span class="execution-status" id="executionStatus"
                                                style="display: none;">0%</span>
                                        </button>
                                    </div>

                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>


        @endsection
        @section('script')

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
            integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
            integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous">
        </script>
        <script>
            $("#defaultSelect").on("change", function() {
                var selectedValue = $(this).val(); // Get the selected value
                window.location.href = "/dashboard/version/" + selectedValue;
            });
        </script>

    @endsection