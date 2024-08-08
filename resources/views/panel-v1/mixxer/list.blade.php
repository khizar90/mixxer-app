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
                @foreach ($list as $item)
                    <div class="col-md-4 col-lg-2 mb-3 delete{{ $item->id }}">
                        <div class="card h-100 Category-Card package-card">
                            @if ($item->cover == '')
                                <img class="card-img-top" src="{{ asset('mixxerPlaceHolder.png') }}" alt="">
                            @else
                                <img class="card-img-top" src="{{ $item->cover }}" alt="" />
                            @endif
                            <a href="{{ route('dashboard-mixxer-detail', $item->id) }}">
                                <div class="overlay" id="">
                                </div>
                            </a>

                            <div class="card-body p-3 pb-2">
                                <h5 class="card-title mb-2"><a
                                        href="{{ route('dashboard-mixxer-detail', $item->id) }}">{{ $item->title }}</a>
                                </h5>
                            </div>


                            <div class="card-links">

                                @if ($item->status != 2)
                                    <a href="#" class="card-link delete-item" data-id="{{ $item->id }}"><i
                                            class="ti ti-trash ti-sm"></i></a>
                                @endif
                            </div>
                        </div>


                    </div>
                @endforeach
            </div>
            <div id="paginationContainer">
                <div class="row mt-2">
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">Showing
                            {{ $list->firstItem() }} to
                            {{ $list->lastItem() }}
                            of
                            {{ $list->total() }} entries</div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_paginate paging_simple_numbers" id="paginationLinks">
                            {{-- <h1>{{ @json($data) }}</h1> --}}
                            @if ($list->hasPages())
                                {{ $list->links('pagination::bootstrap-4') }}
                            @endif


                        </div>
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
                                <a href="" class="btn" data-bs-dismiss="modal" style="color: #a8aaae">Cancel</a>
                            </div>
                            <div class="second">
                                <a href="" class="btn text-center" id="deleteButton">
                                    <span id="deleteText">Delete</span>
                                    <span class="align-middle" id="deleteLoader" role="status" style="display: none;">
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
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        </script>
    @endsection
