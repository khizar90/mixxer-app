@extends('panel-v1.layouts.base')
@section('title', 'Feature Request Detail')
@section('main', 'Feature Request Detail Management')
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
                            Feature Request
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
                                    Can you describe the feature you have in mind?
                                </h5>
                                <p>{{ $find->feature }}</p>
                            </li>

                            <li>
                                <h5>
                                    What specific issue or frustration would this feature address?
                                </h5>
                                <p>{{ $find->issue }}</p>
                            </li>

                            <li>
                                <h5>
                                    How do you envision this feature working in the app?
                                </h5>
                                <p>{{ $find->envision }}</p>
                            </li>
                            <li>
                                <h5>
                                    On a scale of 1-5, how critical is this feature to you?
                                </h5>
                                <p>{{ $find->scale }}</p>
                            </li>
                            <li>
                                <h5>
                                    Have you encountered a similar feature in other apps? If so, which ones?
                                </h5>
                                <p>{{ $find->encountered }}</p>
                            </li>
                            <li>
                                <h5>
                                    Are there any workarounds you currently use to address this need?
                                </h5>
                                <p>{{ $find->workaround }}</p>
                            </li>
                            <li>
                                <h5>
                                    Is there a feature in our app that comes close to what you’re requesting? How does it
                                    fall short?
                                </h5>
                                <p>{{ $find->requesting }}</p>
                            </li>
                            <li>
                                <h5>
                                    Would you be willing to discuss this feature further with our team?
                                </h5>
                                <p>{{ $find->discuss }}</p>
                            </li>

                        </ul>
                    </div>



                </div>
            </div>
        @endsection
