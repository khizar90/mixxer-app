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
            <div class="card">

                <div class="card-body">
                    <div class="d-flex">
                        @if ($mixxer->cover != '')
                            <img src="{{ $mixxer->cover }}" class="detailImage mb-3" alt="" id="image-preview">
                        @else
                            <img src="{{ asset('mixxerPlaceHolder.png') }}" class="detailImage mb-3" alt="">
                        @endif
                        <div class="card-links">

                            @if ($mixxer->status != 2)
                                <a href="#" class="card-link delete-item" data-id="{{ $mixxer->id }}"><i
                                        class="ti ti-trash ti-sm"></i></a>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <h5 class="mt-0 mb-2">{{ $mixxer->title }}</h5>

                        <div class="d-flex gap-2 align-items-center">
                            <i class="ti ti-calendar ti-sm "></i>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($mixxer->start_date)->format('F jS, Y') }} -
                                {{ $mixxer->start_time }} - {{ $mixxer->is_all_day == 0 ? $mixxer->end_time : 'All Day' }}
                            </p>
                        </div>
                        <div class="d-flex gap-2 align-items-center mt-2">
                            <i class="ti ti-map-pin ti-sm "></i>
                            <p class="mb-0">{{ $mixxer->address }}</p>
                        </div>
                        <div class="d-flex gap-2 align-items-center mt-2">
                            <i class="ti ti-cpu ti-sm "></i>
                            <p class="mb-0">{{ $mixxer->type }}</p>
                        </div>
                        <div class="d-flex gap-2 align-items-center mt-2">
                            <i class="ti ti-ankh ti-sm "></i>
                            <p class="mb-0">{{ $mixxer->age_limit == 100 ? 'All Age' : $mixxer->age_limit . '+' }}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <hr>
                        <div>
                            <h6>Mixxer Hosted By</h6>
                            <div class="d-flex justify-content-start align-items-center user-name">
                                @if ($mixxer->user->profile_picture)
                                    <div class="avatar-wrapper">
                                        <div class="avatar avatar-sm me-3"><img src="{{ $mixxer->user->profile_picture }}"
                                                alt="Avatar" class="rounded-circle">
                                        </div>
                                    </div>
                                @else
                                    <div class="avatar-wrapper">
                                        <div class="avatar avatar-sm me-3"><span
                                                class="avatar-initial rounded-circle bg-label-danger">
                                                {{ strtoupper(substr($mixxer->user->first_name, 0, 2)) }}</span>
                                        </div>
                                    </div>
                                @endif



                                <div class="d-flex flex-column"><span
                                        class="fw-semibold user-name-text">{{ $mixxer->user->first_name }}
                                        {{ $mixxer->user->last_name }}
                                    </span>
                                    <small class="text-muted">{{ $mixxer->user->location }}</small>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <h6 class="mb-2">Mixxer Description</h6>
                        <p>{{ $mixxer->description }}</p>
                    </div>
                    @if (count($mixxer->photos) > 0)
                        <div class="row">
                            <h6 class="mb-2">Mixxer Photo</h6>
                            @foreach ($mixxer->photos as $photo)
                                <div class="col-2">
                                    <img src="{{ $photo->media }}" class="rounded image{{ $photo->id }} photos"
                                        data-id="{{ $photo->id }}" alt="">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (count($mixxer->doc) > 0)
                        <div class="row mt-3">
                            <h6 class="mb-2">Mixxer Attachments</h6>
                            @foreach ($mixxer->doc as $doc)
                                <div class="col-2">
                                    <a href="{{ $doc->media }}" target="_blank">
                                        <img src="{{ asset('pdf.png') }}" class="rounded" alt="" width="100%"
                                            height="100">
                                    </a>
                                </div>
                            @endforeach

                        </div>
                    @endif
                    <div class="d-flex gap-3 mt-4">
                        @if (count($users) > 0)
                            <a href="#" data-bs-toggle="modal" data-bs-target="#addNewBus">
                                <h6>Participant</h6>
                            </a>
                        @else
                            <h6>Participant</h6>
                        @endif
                        <h6>{{ $mixxer->participant_count }}</h6>
                    </div>
                    <div class="modal fade" data-bs-backdrop='static' id="addNewBus" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalCenterTitle">Participant List</h5>
                                    <button type="button" class="btn modalRemove" data-bs-dismiss="modal"
                                        id="closeButtonadd"><i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <hr>

                                <div class="modal-body pt-0">
                                    @foreach ($users as $user)
                                        <div class="d-flex justify-content-start align-items-center user-name mb-2">
                                            @if ($user->profile_picture)
                                                <div class="avatar-wrapper">
                                                    <div class="avatar avatar-sm me-3"><img
                                                            src="{{ asset($user->profile_picture != '' ? $user->profile_picture : 'user.png') }}"
                                                            alt="Avatar" class="rounded-circle">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="avatar-wrapper">
                                                    <div class="avatar avatar-sm me-3"><span
                                                            class="avatar-initial rounded-circle bg-label-danger">
                                                            {{ strtoupper(substr($user->first_name, 0, 2)) }}</span>
                                                    </div>
                                                </div>
                                            @endif



                                            <div class="d-flex flex-column"><span
                                                    class="fw-semibold user-name-text">{{ $user->first_name }}
                                                    {{ $user->last_name }}
                                                </span>
                                                <small class="text-muted">&#64;{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
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
            <div class="modal fade" data-bs-backdrop='static' id="deleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                    <div class="modal-content deleteModal verifymodal">
                        <div class="modal-header">
                            <div class="modal-title" id="modalCenterTitle">Are you sure you
                                want to delete
                                this mixxer?
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="body">After deleting the mixxer user will not able to use
                                this
                            </div>
                        </div>
                        <hr class="hr">

                        <div class="container">
                            <div class="row">
                                <div class="first">
                                    <a href="" class="btn" data-bs-dismiss="modal"
                                        style="color: #a8aaae">Cancel</a>
                                </div>
                                <div class="second">
                                    <a href="" class="btn text-center" id="deleteButton">
                                        <span id="deleteText">Delete</span>
                                        <span class="align-middle" id="deleteLoader" role="status"
                                            style="display: none;">
                                            <span class="spinner-border" style="color: #ffffff" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endsection
        @section('script')
            <script>
                $('#image-preview').on('click', function() {
                    var imageUrl = $('#image-preview').attr('src');
                    $('#selected-image').attr('src', imageUrl);
                    $('#imageModal').modal('show');
                });
            </script>
            <script>
                $('.photos').on('click', function() {
                    itemId = $(this).data('id');
                    var imageUrl = $('.image' + itemId).attr('src');
                    $('#selected-image').attr('src', imageUrl);
                    $('#imageModal').modal('show');
                });
            </script>
            <script>
                var itemId = $(this).data('id');

                $('.delete-item').click(function(e) {
                    $('#deleteModal').modal('show');
                    itemId = $(this).data('id');
                });
                $('#deleteButton').click(function(e) {
                    e.preventDefault();
                    $('#deleteLoader').show();
                    $('#deleteText').hide();
                    $('.first').hide();

                    $('.second').css('width', '100%');
                    $.ajax({
                        url: '/dashboard/mixxer/delete/' + itemId,
                        type: 'get',
                        success: function(response) {
                            $('#deleteLoader').hide();
                            $('#deleteText').show();
                            $('#deleteModal').modal('hide');
                            $('.first').show();
                            $('.second').css('width', '50%');
                            $('.delete' + itemId).remove();
                            window.location.href = "/dashboard/mixxer/up-coming";
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                });
            </script>
        @endsection
