@extends('panel-v1.layouts.base')
@section('title', 'App Feedback Detail')
@section('main', 'App Feedback Detail Management')
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
                        <h5 class="card-title mb-3">
                            Feedback Detail
                        </h5>

                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center user-name mb-3">
                            @if ($find->user->profile_image)
                                <div class="avatar-wrapper">
                                    <div class="avatar avatar-sm me-3"><img
                                            src="{{ asset($find->user->profile_image != '' ? $find->user->profile_image : 'user.png') }}"
                                            alt="Avatar" class="rounded-circle">
                                    </div>
                                </div>
                            @else
                                <div class="avatar-wrapper">
                                    <div class="avatar avatar-sm me-3"><span
                                            class="avatar-initial rounded-circle bg-label-danger">
                                            {{ strtoupper(substr($find->user->first_name, 0, 2)) }}</span>
                                    </div>
                                </div>
                            @endif



                            <div class="d-flex flex-column"><a href="#" class="text-body text-truncate"><span
                                        class="fw-semibold user-name-text">{{ $find->user->first_name }}
                                        {{ $find->user->last_name }}
                                    </span></a>
                                <small class="text-muted">&#64;{{ $find->user->email }}</small>
                            </div>
                        </div>

                        <ul>
                            <li>
                                <h5>
                                    How was your overall experience with this Mixxer?
                                </h5>
                                <p>{{ $find->experience }}</p>
                            </li>

                            <li>
                                <h5>
                                    What do you like most about using Mixxer?
                                </h5>
                                <p>{{ $find->like_most }}</p>
                            </li>

                            <li>
                                <h5>
                                    Are there any features that you find confusing or hard to use?
                                </h5>
                                <p>{{ $find->confusing }}</p>
                            </li>
                            <li>
                                <h5>
                                    Is there anything specific you’d like to see in future updates?
                                </h5>
                                <p>{{ $find->update }}</p>
                            </li>
                            <li>
                                <h5>
                                    How do you feel about the content provided within the app? Is there any content or
                                    information you’d like to see added?
                                </h5>
                                <p>{{ $find->content }}</p>
                            </li>
                            <li>
                                <h5>
                                    How often do you use the app, and what keeps you coming back?
                                </h5>
                                <p>{{ $find->use_app }}</p>
                            </li>

                            <li>
                                <h5>
                                    How do you feel about the app's notifications? Are they too frequent or not informative?
                                </h5>
                                <p>{{ $find->notification }}</p>
                            </li>

                            <li>
                                <h5>
                                    Have you encountered any bugs or issues while using the app?
                                </h5>
                                <p>{{ $find->bug }}</p>
                            </li>
                            <li>
                                <h5>
                                    Have you had to reach out for support? If so, how was your experience?
                                </h5>
                                <p>{{ $find->support }}</p>
                            </li>
                            <li>
                                <h5>
                                    What additional support would make your experience better?
                                </h5>
                                <p>{{ $find->additional_support }}</p>
                            </li>
                            <li>
                                <h5>
                                    Do you have any final thoughts or additional feedback?
                                </h5>
                                <p>{{ $find->final }}</p>
                            </li>
                            <li>
                                <h5>
                                    Would you be interested in participating in future feedback sessions? </h5>
                                <p>{{ $find->interested }}</p>
                            </li>


                        </ul>
                    </div>



                </div>
            </div>
        @endsection
