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
                            <img src="/panel-v1/assets/img/pages/profile-banner.jpg" alt="Banner image"
                                class="rounded-top" />
                        </div>
                        <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                            <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                                <img src="{{ $user->profile_picture != '' ? $user->profile_picture : asset('mixxerPlaceHolder.png') }}"
                                    alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img"
                                    width="100" id="image-preview"
                                    onclick="{{ $user->profile_picture != '' ? 'openModal()' : '' }}" />
                            </div>
                            <div class="flex-grow-1 mt-3 mt-sm-5">
                                <div
                                    class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                                    <div class="user-profile-info">
                                        <h4>{{ $user->first_name }} {{ $user->last_name }}</h4>
                                        <ul
                                            class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                            {{-- <li class="list-inline-item d-flex gap-1">
                                                <i class="ti ti-color-swatch"></i> {{ $user->language }}
                                            </li>
                                            <li class="list-inline-item d-flex gap-1">
                                                <i class="ti ti-phone"></i> #{{ $user->number }}
                                            </li> --}}
                                            <li class="list-inline-item d-flex gap-1">
                                                <i class="ti ti-calendar"></i> Joined
                                                {{ $user->created_at->format(' M Y') }}
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- User Profile Content -->
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <!-- About User -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <small class="card-text text-uppercase">About</small>
                            <ul class="list-unstyled mb-4 mt-3">
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-user"></i><span class="fw-bold mx-2">Full Name:</span>
                                    <span>{{ $user->first_name }} {{ $user->last_name }}</span>
                                </li>

                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-mail"></i><span class="fw-bold mx-2">Email:</span>
                                    <span>{{ $user->email }}</span>
                                </li>
                            </ul>
                            <small class="card-text text-uppercase">Others</small>
                            <ul class="list-unstyled mb-4 mt-3">
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-brand-instagram"></i><span class="fw-bold mx-2">Instagram
                                        Username:</span>
                                    <span>{{ $user->instagram_username }}</span>
                                </li>

                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-brand-instagram"></i><span class="fw-bold mx-2">Instagram
                                        Profile:</span>
                                    <span>{{ $user->instagram_profile }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-layout-cards"></i><span class="fw-bold mx-2">Bio:</span>
                                    <span>{{ $user->bio }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-old"></i><span class="fw-bold mx-2">Age:</span>
                                    <span>{{ $user->age }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-gender-genderqueer"></i><span class="fw-bold mx-2">Gender:</span>
                                    <span>{{ $user->gender }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="ti ti-directions"></i><span class="fw-bold mx-2">Relationship:</span>
                                    <span>{{ $user->relationship }}</span>
                                </li>
                            </ul>

                            <small class="card-text text-uppercase">Persona</small>
                            <div class="mt-3 px-1">
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/ethnicity.png" width="20" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->ethnicity }}
                                        </span>
                                        <small class="text-muted">Ethnicity</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/religion.png" width="20" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->religion }}
                                        </span>
                                        <small class="text-muted">Religion</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/zodiac_sign.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->zodiac_sign }}
                                        </span>
                                        <small class="text-muted">Zodiac Signs</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/season.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->season }}
                                        </span>
                                        <small class="text-muted">Season you identify with</small>
                                    </div>
                                </div>

                            </div>

                            <small class="card-text text-uppercase">Network</small>
                            <div class="mt-3 px-1">
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/hometown.png" width="20" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->hometown }}
                                        </span>
                                        <small class="text-muted">Hometown</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/occupation.png" width="20" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->occupation }}
                                        </span>
                                        <small class="text-muted">Occupation</small>
                                    </div>
                                </div>
                               
                            </div>


                            <small class="card-text text-uppercase">Education</small>
                            <div class="mt-3 px-1">
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/school.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->education_type }}
                                        </span>
                                        <small class="text-muted">College/University</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/graduate.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->degree }}
                                        </span>
                                        <small class="text-muted">Degree</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/school.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->school_name }}
                                        </span>
                                        <small class="text-muted">School Name</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/iwould.png" width="20" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->work_from_anywhere }}
                                        </span>
                                        <small class="text-muted">If you could work from anywhere, where would it be? </small>
                                    </div>
                                </div>

                            </div>

                            <small class="card-text text-uppercase">Lifestyle</small>
                            <div class="mt-3 px-1">
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/school.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->dietary_preferences }}
                                        </span>
                                        <small class="text-muted">Dietary Preferences</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/graduate.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->health_goals }}
                                        </span>
                                        <small class="text-muted">Health Goals</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/graduate.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->weekend_must_do  }}
                                        </span>
                                        <small class="text-muted">Weekend Must-Do </small>
                                    </div>
                                </div>
                               

                            </div>

                            <small class="card-text text-uppercase">Pets</small>
                            <div class="mt-3 px-1">
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/pet.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->pet_count }}
                                        </span>
                                        <small class="text-muted">How Many Pets?</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/pet.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->pet_type }}
                                        </span>
                                        <small class="text-muted">Pet Types</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/pet.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->unwind }}
                                        </span>
                                        <small class="text-muted">What's your favorite way to unwind?</small>
                                    </div>
                                </div>
                               

                            </div>
                            <small class="card-text text-uppercase">Favorites</small>
                            <div class="mt-3 px-1">
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/music.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->music_genres }}
                                        </span>
                                        <small class="text-muted">Music Genres </small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/movie.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->movie_genres }}
                                        </span>
                                        <small class="text-muted">Movie Genres </small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/drink.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->go_to_drinks  }}
                                        </span>
                                        <small class="text-muted">Go-To Drinks </small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/sport.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->sports_genres  }}
                                        </span>
                                        <small class="text-muted">Sports Genres </small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center user-name mb-2">

                                    <div class="avatar-wrapper">
                                        <img src="/panel-v1/assets/icon/eat.png" width="25" alt="Avatar" class="rounded me-3">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold user-name-text">{{ $user->eat_one_food }}
                                        </span>
                                        <small class="text-muted">If you could only eat one food for the rest of your life, what would it be?</small>
                                    </div>
                                </div>
                               

                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-xl-8 col-lg-7 col-md-7">

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
