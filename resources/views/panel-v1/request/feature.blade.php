@extends('panel-v1.layouts.base')
@section('title', 'Feature Request')
@section('main', 'Feature Request Management')
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
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible mt-1" role="alert">
                            {{ session()->get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible mt-1" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session()->has('delete'))
                        <div class="alert alert-danger alert-dismissible mt-1" role="alert">
                            {{ session()->get('delete') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>
                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <table class="table border-top dataTable" id="usersTable">
                            <thead class="">
                                <tr>
                                    <th>User</th>
                                    <th>experience</th>
                                    <th>about</th>
                                    <th>feature</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($list as $item)
                                    <tr class="odd">
                                        <td class="sorting_1">
                                            <div class="d-flex justify-content-start align-items-center user-name">
                                                @if ($item->user->profile_image)
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3"><img
                                                                src="{{ asset($item->user->profile_image != '' ? $item->user->profile_image : 'user.png') }}"
                                                                alt="Avatar" class="rounded-circle">
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3"><span
                                                                class="avatar-initial rounded-circle bg-label-danger">
                                                                {{ strtoupper(substr($item->user->first_name, 0, 2)) }}</span>
                                                        </div>
                                                    </div>
                                                @endif



                                                <div class="d-flex flex-column"><a href="#"
                                                        class="text-body text-truncate"><span
                                                            class="fw-semibold user-name-text">{{ $item->user->first_name }}
                                                            {{ $item->user->last_name }}
                                                        </span></a>
                                                    <small class="text-muted">&#64;{{ $item->user->email }}</small>
                                                </div>
                                            </div>

                                        </td>
                                        <td>
                                            @if ($item->experience == 0)
                                                <img src="/panel-v1/assets/img/ex0.png" alt="" width="50">
                                            @endif
                                            @if ($item->experience == 1)
                                                <img src="/panel-v1/assets/img/ex2.png" alt=""  width="50">
                                            @endif
                                            @if ($item->experience == 2)
                                                <img src="/panel-v1/assets/img/ex3.png" alt=""  width="50">
                                            @endif
                                            @if ($item->experience == 3)
                                                <img src="/panel-v1/assets/img/ex4.png" alt=""  width="50">
                                            @endif
                                            @if ($item->experience == 4)
                                                <img src="/panel-v1/assets/img/ex5.png" alt=""  width="50">
                                            @endif

                                        </td>
                                        <td>{{ $item->about }}</td>
                                        <td>{{ $item->feature }}</td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="paginationContainer">
                            <div class="row mx-2">
                                <div class="col-sm-12 col-md-6">
                                    <div class="dataTables_info" id="DataTables_Table_0_info" role="status"
                                        aria-live="polite">Showing {{ $list->firstItem() }} to
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
                </div>
            </div>
        </div>
    @endsection
