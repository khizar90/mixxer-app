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
                        <h5>Reported By</h5>
                        <div class="d-flex justify-content-start align-finds-center user-name mb-3">
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
                        <h5>Reported User</h5>
                        <div class="d-flex justify-content-start align-finds-center user-name mb-3">
                            @if ($find->report->profile_image)
                                <div class="avatar-wrapper">
                                    <div class="avatar avatar-sm me-3"><img
                                            src="{{ asset($find->report->profile_image != '' ? $find->report->profile_image : 'user.png') }}"
                                            alt="Avatar" class="rounded-circle">
                                    </div>
                                </div>
                            @else
                                <div class="avatar-wrapper">
                                    <div class="avatar avatar-sm me-3"><span
                                            class="avatar-initial rounded-circle bg-label-danger">
                                            {{ strtoupper(substr($find->report->first_name, 0, 2)) }}</span>
                                    </div>
                                </div>
                            @endif



                            <div class="d-flex flex-column"><a href="#" class="text-body text-truncate"><span
                                        class="fw-semibold user-name-text">{{ $find->report->first_name }}
                                        {{ $find->report->last_name }}
                                    </span></a>
                                <small class="text-muted">&#64;{{ $find->report->email }}</small>
                            </div>
                        </div>

                        <ul>
                            <li>
                                <h5>
                                    Reason for Reporting.
                                </h5>
                                <p>{{ $find->category }}</p>
                            </li>
                            <li>
                                <h5>
                                    Sepcify Other Reason
                                </h5>
                                <p>{{ $find->reason }}</p>
                            </li>
                            <li>
                                <h5>
                                    Provide additional details about why you are reporting this profile
                                </h5>
                                <p>{{ $find->detail }}</p>
                            </li>
                            <li>
                                <h5>
                                    How did this person affect your experience on Mixxer?
                                </h5>
                                <p>{{ $find->affect }}</p>
                            </li>
                            <li>
                                <h5>
                                    Would you like to be contacted about the outcome of this report?
                                </h5>
                                <p>{{ $find->contacted }}</p>
                            </li>
                            @if (count($find->media) > 0)
                                <li>
                                    <h5>
                                        Attach any screenshots or files that support your report.
                                    </h5>


                                    <div class="row mb-3">
                                        <h6>Images</h6>
                                        @foreach ($find->media as $index => $photo)
                                            <div class="col-2">
                                                <img src="{{ $photo }}"
                                                    class="rounded image{{ $index }} photos"
                                                    data-id="{{ $index }}" alt="">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="row">
                                        <h6>Doc</h6>

                                        @foreach ($find->doc as $doc)
                                            <div class="col-2">
                                                <a href="{{ $doc }}" target="_blank">
                                                    <img src="{{ '/pdf.png' }}" class="rounded docPhotos"
                                                        alt="">
                                                </a>

                                            </div>
                                        @endforeach
                                    </div>
                                </li>
                            @endif

                        </ul>
                    </div>



                </div>
            </div>
            <div class="modal fade imageModal" id="imageModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog  modal-dialog-centered modal-simple modal-upgrade-plan modal-lg">
                    <div class="modal-content  p-0 bg-transparent">
                        <div class="modal-body p-0">
                            <img id="selected-image" src="#" style="width: 100%">
                        </div>
                    </div>
                </div>
            </div>
        @endsection
        @section('script')
            <script>
                $('.photos').on('click', function() {
                    itemId = $(this).data('id');
                    var imageUrl = $('.image' + itemId).attr('src');
                    $('#selected-image').attr('src', imageUrl);
                    $('#imageModal').modal('show');
                });
            </script>
        @endsection
