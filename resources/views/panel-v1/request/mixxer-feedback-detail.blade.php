@extends('panel-v1.layouts.base')
@section('title', 'Mixxer Feedback Detail')
@section('main', 'Mixxer Feedback Detail Management')
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
                            Mixxer Feedback
                        </h5>

                    </div>
                    <div class="card-body">
                        <h5>Title : {{ $find->mixxer->title }}</h5>
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
                                    What was the highlight of the Mixxer for you?
                                </h5>
                                <p>{{ $find->highlights }}</p>
                            </li>

                            <li>
                                <h5>
                                    Did this experience encourage you to attend or host more Mixxers with a similar theme?
                                </h5>
                                <p>{{ $find->experience_encourage }}</p>
                            </li>
                            <li>
                                <h5>
                                    Is there anything that could have made your Mixxer experience even better?
                                </h5>
                                <p>{{ $find->improvement }}</p>
                            </li>
                            <li>
                                <h5>
                                    What were you expecting from this Mixxer, and how did it not meet those expectations?
                                </h5>
                                <p>{{ $find->expecting }}</p>
                            </li>
                            <li>
                                <h5>
                                    Did you have fun with the people who showed up? Did the group vibe well together? </h5>
                                <p>{{ $find->have_fun }}</p>
                            </li>
                            <li>
                                <h5>
                                    What made the group fun or not fun for you?
                                </h5>
                                <p>{{ $find->group_fun }}</p>
                            </li>
                            <li>
                                <h5>
                                    How would you rate the venue or virtual environment?
                                </h5>
                                <p>{{ $find->rate_the_venue }}</p>
                            </li>
                            <li>
                                <h5>
                                    Would you choose this venue or virtual setting again for a future Mixxer? Why or why
                                    not?
                                </h5>
                                <p>{{ $find->virtual_setting }}</p>
                            </li>
                            <li>
                                <h5>
                                    Is there anything else you’d like to share about your Mixxer experience?
                                </h5>
                                <p>{{ $find->additional_comment }}</p>
                            </li>

                        </ul>
                    </div>



                </div>
            </div>
        @endsection
