@extends('panel-v1.layouts.base')
@section('title', 'User Profile')
@section('main', 'Profile Management')
@section('link')
    <link rel="stylesheet" href="/panel-v1/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="/panel-v1/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="/panel-v1/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="/panel-v1/assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="/panel-v1/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            {{-- <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">User Profile /</span> Profile</h4> --}}

            <!-- Header -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="user-profile-header-banner">
                            <img src="/panel-v1/assets/img/pages/profile-banner.png" alt="Banner image"
                                class="rounded-top" />
                        </div>
                        <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                            <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                                <img src="{{ $user->profile_picture != '' ? $user->profile_picture : asset('place_holder.png') }}"
                                    alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img"
                                    width="100" id="image-preview"
                                    onclick="{{ $user->profile_picture != '' ? 'openModal()' : '' }}" />
                            </div>
                            <div class="flex-grow-1 mt-3 mt-sm-5">
                                <div
                                    class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                                    <div class="user-profile-info">
                                        <h4>{{ $user->name }}</h4>
                                        <ul
                                            class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                            <li class="list-inline-item d-flex gap-1">
                                                <i class="ti ti-color-swatch"></i> {{ $user->language }}
                                            </li>
                                            <li class="list-inline-item d-flex gap-1">
                                                <i class="ti ti-phone"></i> #{{ $user->number }}
                                            </li>
                                            <li class="list-inline-item d-flex gap-1">
                                                <i class="ti ti-calendar"></i> Joined
                                                {{ $user->created_at->format(' M Y') }}
                                            </li>
                                        </ul>
                                    </div>
                                    {{-- <a href="javascript:void(0)" class="btn btn-primary">
                                        <i class="ti ti-user-check me-1"></i>Connected
                                    </a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Header -->

            <!-- Navbar pills -->
            {{-- <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-pills flex-column flex-sm-row mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" href="javascript:void(0);"><i
                                    class="ti-xs ti ti-user-check me-1"></i> Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages-profile-teams.html"><i class="ti-xs ti ti-users me-1"></i>
                                Teams</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages-profile-projects.html"><i
                                    class="ti-xs ti ti-layout-grid me-1"></i> Projects</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages-profile-connections.html"><i class="ti-xs ti ti-link me-1"></i>
                                Connections</a>
                        </li>
                    </ul>
                </div>
            </div> --}}
            <!--/ Navbar pills -->

            <!-- User Profile Content -->
            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-5">
                    <!-- About User -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <small class="card-text text-uppercase">About</small>
                            <ul class="list-unstyled mb-4 mt-3">
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-user"></i><span class="fw-bold mx-2">Full Name:</span>
                                    <span>{{ $user->name }}</span>
                                </li>
                                {{-- <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-check"></i><span class="fw-bold mx-2">Status:</span> <span>Active</span>
                                </li> --}}
                                {{-- <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-crown"></i><span class="fw-bold mx-2">Role:</span>
                                    <span>Developer</span>
                                </li> --}}
                                {{-- <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-flag"></i><span class="fw-bold mx-2">Country:</span> <span>USA</span>
                                </li> --}}
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-file-description"></i><span class="fw-bold mx-2">Languages:</span>
                                    <span>{{ $user->language }}</span>
                                </li>
                            </ul>
                            <small class="card-text text-uppercase">Contacts</small>
                            <ul class="list-unstyled mb-4 mt-3">
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-phone-call"></i><span class="fw-bold mx-2">Contact:</span>
                                    <span>{{ $user->number }}</span>
                                </li>
                                {{-- <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-brand-skype"></i><span class="fw-bold mx-2">Skype:</span>
                                    <span>john.doe</span>
                                </li> --}}
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-mail"></i><span class="fw-bold mx-2">Email:</span>
                                    <span>{{ $user->email != '' ? $user->email : 'No Email' }}</span>
                                </li>
                            </ul>
                            {{-- <small class="card-text text-uppercase">Teams</small> --}}
                            {{-- <ul class="list-unstyled mb-0 mt-3">
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-brand-angular text-danger me-2"></i>
                                    <div class="d-flex flex-wrap">
                                        <span class="fw-bold me-2">Backend Developer</span><span>(126 Members)</span>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center">
                                    <i class="ti ti-brand-react-native text-info me-2"></i>
                                    <div class="d-flex flex-wrap">
                                        <span class="fw-bold me-2">React Developer</span><span>(98 Members)</span>
                                    </div>
                                </li>
                            </ul> --}}
                        </div>
                    </div>
                    <!--/ About User -->
                    <!-- Profile Overview -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <p class="card-text text-uppercase">Overview</p>
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-point"></i><span class="fw-bold mx-2">Total Earned :</span>
                                    <span>{{ $user->total }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-point"></i><span class="fw-bold mx-2">Available for Withdraw :</span>
                                    <span>{{ $user->withdraw }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-point"></i><span class="fw-bold mx-2">Pending :</span>
                                    <span>{{ $user->pending }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-point"></i><span class="fw-bold mx-2">Cashed :</span>
                                    <span>{{ $user->cashed }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--/ Profile Overview -->
                </div>
                <div class="col-xl-8 col-lg-7 col-md-7">
                    <!-- Activity Timeline -->
                    {{-- <div class="card card-action mb-4">
                        <div class="card-header align-items-center">
                            <h5 class="card-action-title mb-0">Activity Timeline</h5>
                            <div class="card-action-element">
                                <div class="dropdown">
                                    <button type="button" class="btn dropdown-toggle hide-arrow p-0"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="javascript:void(0);">Share timeline</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a></li>
                                        <li>
                                            <hr class="dropdown-divider" />
                                        </li>
                                        <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pb-0">
                            <ul class="timeline ms-1 mb-0">
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-primary"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">Client Meeting</h6>
                                            <small class="text-muted">Today</small>
                                        </div>
                                        <p class="mb-2">Project meeting with john @10:15am</p>
                                        <div class="d-flex flex-wrap">
                                            <div class="avatar me-2">
                                                <img src="/panel-v1/assets/img/avatars/3.png" alt="Avatar"
                                                    class="rounded-circle" />
                                            </div>
                                            <div class="ms-1">
                                                <h6 class="mb-0">Lester McCarthy (Client)</h6>
                                                <span>CEO of Infibeam</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-success"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">Create a new project for client</h6>
                                            <small class="text-muted">2 Day Ago</small>
                                        </div>
                                        <p class="mb-0">Add files to new design folder</p>
                                    </div>
                                </li>
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-danger"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">Shared 2 New Project Files</h6>
                                            <small class="text-muted">6 Day Ago</small>
                                        </div>
                                        <p class="mb-2">
                                            Sent by Mollie Dixon
                                            <img src="/panel-v1/assets/img/avatars/4.png" class="rounded-circle me-3"
                                                alt="avatar" height="24" width="24" />
                                        </p>
                                        <div class="d-flex flex-wrap gap-2 pt-1">
                                            <a href="javascript:void(0)" class="me-3">
                                                <img src="/panel-v1/assets/img/icons/misc/doc.png" alt="Document image"
                                                    width="15" class="me-2" />
                                                <span class="fw-semibold text-heading">App Guidelines</span>
                                            </a>
                                            <a href="javascript:void(0)">
                                                <img src="/panel-v1/assets/img/icons/misc/xls.png" alt="Excel image"
                                                    width="15" class="me-2" />
                                                <span class="fw-semibold text-heading">Testing Results</span>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li class="timeline-item timeline-item-transparent border-0">
                                    <span class="timeline-point timeline-point-info"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">Project status updated</h6>
                                            <small class="text-muted">10 Day Ago</small>
                                        </div>
                                        <p class="mb-0">Woocommerce iOS App Completed</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div> --}}
                    <!--/ Activity Timeline -->
                    <div class="row">
                        <!-- Connections -->
                        <div class="col-lg-12 col-xl-12">
                            <div class="card card-action mb-4">
                                <div class="card-header align-items-center">
                                    <h5 class="card-action-title mb-0">Point History</h5>

                                </div>
                                <div class="card-body">
                                    <div class="text-center" id="spinner" style="display: none">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="nav-align-top mb-4" id="tabbutton">
                                        <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $type == 'pending' ? 'active' : '' }}"
                                                    id="{{ $type }}" data-bs-toggle="pill"
                                                    data-bs-target="#{{ $type }}" type="button" role="tab"
                                                    aria-controls="{{ $type }}" aria-selected="false"
                                                    data-type="pending">Pending</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button type="button"
                                                    class="nav-link {{ $type == 'approve' ? 'active' : '' }}"
                                                    role="tab" data-bs-toggle="tab" id="{{ $type }}"
                                                    data-bs-target="#{{ $type }}"
                                                    aria-controls="{{ $type }}" aria-selected="false"
                                                    data-type="approve" tabindex="-1">Approve
                                            </li>

                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="{{ $type }}"
                                                role="tabpanel">
                                                <ul class="list-unstyled mb-0">
                                                    @if (count($list) > 0)

                                                        @foreach ($list as $item)
                                                            <li class="mb-3">
                                                                <div class="d-flex align-items-start">
                                                                    <div class="d-flex align-items-start">
                                                                        <div class="avatar me-2">
                                                                            @if ($item->content_type == 'image')
                                                                                <a href="{{ $item->content->content }}"
                                                                                    target="_blank">

                                                                                    <img src="/panel-v1/assets/img/image.png"
                                                                                        alt="Avatar"
                                                                                        class="rounded-circle">
                                                                                </a>
                                                                            @endif
                                                                            @if ($item->content_type == 'youtube')
                                                                                <a href="{{ $item->content->content }}"
                                                                                    target="_blank">

                                                                                    <img src="/panel-v1/assets/img/youtube.png"
                                                                                        alt="Avatar"
                                                                                        class="rounded-circle">
                                                                                </a>
                                                                            @endif
                                                                            @if ($item->content_type == 'video')
                                                                                <a href="{{ $item->content->content }}"
                                                                                    target="_blank">

                                                                                    <img src="/panel-v1/assets/img/video.png"
                                                                                        alt="Avatar"
                                                                                        class="rounded-circle">
                                                                                </a>
                                                                            @endif
                                                                            @if ($item->content_type == 'text')
                                                                                <img src="/panel-v1/assets/img/text.png"
                                                                                    alt="Avatar" class="rounded-circle">
                                                                            @endif
                                                                        </div>
                                                                        <div class="me-2 ms-1">
                                                                            <h6 class="mb-0">ID :
                                                                                #{{ $item->content_id }} </h6>
                                                                            <small
                                                                                class="{{ $item->status == 'approve' ? 'text-success' : 'text-danger' }}">{{ $item->status == 'approve' ? 'Approved' : 'Pending' }}</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ms-auto">

                                                                        <a href="{{ $item->media }}" target="_blank">
                                                                            <img src="/panel-v1/assets/img/audio.png"
                                                                                height="40" width="40"
                                                                                alt="">
                                                                        </a>
                                                                        {{-- <button class="btn btn-label-primary btn-icon btn-sm waves-effect"><i
                                                                            class="ti ti-user-check ti-xs"></i></button> --}}
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    @else
                                                        <div class="text-center">Not Point History</div>
                                                    @endif
                                                </ul>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Connections -->
                        <!-- Teams -->
                        {{-- <div class="col-lg-12 col-xl-6">
                            <div class="card card-action mb-4">
                                <div class="card-header align-items-center">
                                    <h5 class="card-action-title mb-0">Teams</h5>
                                    <div class="card-action-element">
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle hide-arrow p-0"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Share teams</a>
                                                </li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-start">
                                                    <div class="avatar me-2">
                                                        <img src="/panel-v1/assets/img/icons/brands/react-label.png"
                                                            alt="Avatar" class="rounded-circle" />
                                                    </div>
                                                    <div class="me-2 ms-1">
                                                        <h6 class="mb-0">React Developers</h6>
                                                        <small class="text-muted">72 Members</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <a href="javascript:;"><span
                                                            class="badge bg-label-danger">Developer</span></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-start">
                                                    <div class="avatar me-2">
                                                        <img src="/panel-v1/assets/img/icons/brands/support-label.png"
                                                            alt="Avatar" class="rounded-circle" />
                                                    </div>
                                                    <div class="me-2 ms-1">
                                                        <h6 class="mb-0">Support Team</h6>
                                                        <small class="text-muted">122 Members</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <a href="javascript:;"><span
                                                            class="badge bg-label-primary">Support</span></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-start">
                                                    <div class="avatar me-2">
                                                        <img src="/panel-v1/assets/img/icons/brands/figma-label.png"
                                                            alt="Avatar" class="rounded-circle" />
                                                    </div>
                                                    <div class="me-2 ms-1">
                                                        <h6 class="mb-0">UI Designers</h6>
                                                        <small class="text-muted">7 Members</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <a href="javascript:;"><span
                                                            class="badge bg-label-info">Designer</span></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-start">
                                                    <div class="avatar me-2">
                                                        <img src="/panel-v1/assets/img/icons/brands/vue-label.png"
                                                            alt="Avatar" class="rounded-circle" />
                                                    </div>
                                                    <div class="me-2 ms-1">
                                                        <h6 class="mb-0">Vue.js Developers</h6>
                                                        <small class="text-muted">289 Members</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <a href="javascript:;"><span
                                                            class="badge bg-label-danger">Developer</span></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-start">
                                                    <div class="avatar me-2">
                                                        <img src="/panel-v1/assets/img/icons/brands/twitter-label.png"
                                                            alt="Avatar" class="rounded-circle" />
                                                    </div>
                                                    <div class="me-2 ms-1">
                                                        <h6 class="mb-0">Digital Marketing</h6>
                                                        <small class="text-muted">24 Members</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <a href="javascript:;"><span
                                                            class="badge bg-label-secondary">Marketing</span></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="text-center">
                                            <a href="javascript:;">View all teams</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div> --}}
                        <!--/ Teams -->
                    </div>
                    <!-- Projects table -->
                    {{-- <div class="card mb-4">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-projects table border-top">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Leader</th>
                                        <th>Team</th>
                                        <th class="w-px-200">Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div> --}}
                    <!--/ Projects table -->
                </div>
            </div>
            <!--/ User Profile Content -->
        </div>
        {{-- <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered modal-simple modal-upgrade-plan">
                <div class="modal-content  p-0 bg-transparent">
                    <div class="modal-body p-0">
                     
                        <div class="text-center">
                            <img src="{{ $user->profile_picture != '' ? $user->profile_picture : asset('place_holder.png') }}"
                                alt="user image" class="img-fluid rounded" />
                        </div>

                    </div>
                </div>
            </div>
        </div> --}}

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
            $('#image-preview').on('click', function() {
                var imageUrl = $('#image-preview').attr('src');
                $('#selected-image').attr('src', imageUrl);
                $('#imageModal').modal('show');
            });
        </script>

        <script>
            $(document).on('click', '.nav-link', function(e) {
                e.preventDefault();
                let type = $(this).data('type');
                var loader = $('#spinner');
                loader.show();
                let userId = '{{ $user->uuid }}'

                $('#tabbutton').hide();
                $.ajax({
                    type: 'GET',
                    url: '/dashboard/users/show/' + userId,
                    data: {
                        type: type
                    },
                    success: function(response) {
                        $("#tabbutton").html(response);
                        loader.hide();
                        $('#tabbutton').show();



                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            });
        </script>

    @endsection
