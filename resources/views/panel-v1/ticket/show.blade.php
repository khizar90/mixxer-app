@extends('panel-v1.layouts.base')
@section('title', 'Tickets')
@section('main', 'Ticket Chat')
@section('link')
    <link rel="stylesheet" href="/panel-v1/assets/vendor/css/pages/app-chat.css" />
    <style>
        #textInput {
            resize: none;
            overflow-y: auto;
            max-height: 150px;
        }

        .chat-history-body {
            height: 100%;
            overflow-y: auto;
            overflow-x: hidden;
            /* Prevent horizontal scrolling */
        }

        .chat-history-wrapper {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .content-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .app-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-history-footer {
            margin-bottom: 20px !important;
            display: flex;
            align-items: flex-end;
        }

        .message-actions {
            /* width: 105px; */
            margin-left: 10px;
            /* Adjust margin as needed */
        }

        .message-actions button {
            /* width: 100%; */
            height: 43px;
        }

        textarea.message-input {
            resize: none;
            /* Prevent manual resizing */
            overflow-y: hidden;
            /* Hide scrollbars */
            flex-grow: 1;
            /* Allow textarea to expand */
        }

        textarea.message-input::-webkit-scrollbar {
            width: 8px;
            /* Width of the scrollbar */
        }

        textarea.message-input::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;

            /* Background color of the track */
        }

        textarea.message-input::-webkit-scrollbar-thumb {
            background: #888;
            /* Color of the scroll thumb */
            border-radius: 4px;
            /* Rounded corners for the thumb */
        }

        textarea.message-input::-webkit-scrollbar-thumb:hover {
            background: #555;
            /* Color of the thumb when hovered */
        }
    </style>

@endsection
@section('content')

    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="app-chat card overflow-hidden">
                <div class="row g-0">
                    <!-- Chat History -->
                    <div class="col app-chat-history bg-body">
                        <div class="chat-history-wrapper">
                            <div class="chat-history-header border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex overflow-hidden align-items-center">

                                        @if ($findUser)
                                            <div class="flex-shrink-0 avatar">
                                                <img src="{{ $findUser->image != '' ? $findUser->image : asset('placeholder.png') }}"
                                                    alt="Avatar" class="rounded-circle" data-bs-toggle="sidebar"
                                                    data-overlay data-target="#app-chat-sidebar-right" />
                                            </div>
                                            <div class="chat-contact-info flex-grow-1 ms-2">
                                                <h6 class="m-0">{{ $findUser->first_name }} </h6>
                                                <small class="user-status text-muted">{{ $findUser->email }}</small>
                                            </div>
                                        @else
                                            <div class="flex-shrink-0 avatar">
                                                <img src="placeholder.png" alt="Avatar" class="rounded-circle"
                                                    data-bs-toggle="sidebar" data-overlay
                                                    data-target="#app-chat-sidebar-right" />
                                            </div>
                                            <div class="chat-contact-info flex-grow-1 ms-2">
                                                <h6 class="m-0">NO User Found</h6>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($ticket->status === 0)
                                        <a href="#" class="btn btn-primary bg-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal">close</a>
                                        <div class="modal fade" data-bs-backdrop='static' id="deleteModal" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                <div class="modal-content deleteModal verifymodal">
                                                    <div class="modal-header">
                                                        <div class="modal-title" id="modalCenterTitle">Are you
                                                            sure you want to close
                                                            this ticket?
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="body">After closing this ticket user cannot
                                                            send message in this ticket</div>
                                                    </div>
                                                    <hr class="hr">

                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="first">
                                                                <a href="" class="btn" data-bs-dismiss="modal"
                                                                    style="color: #a8aaae ">Cancel</a>
                                                            </div>
                                                            <div class="second">
                                                                <a class="btn text-center"
                                                                    href="{{ route('dashboard-ticket-close-ticket', $ticket->id) }}">Close</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="chat-history-body bg-body" id="chat-history-body">
                                <ul class="list-unstyled chat-history" id="list-unstyled">
                                    @foreach ($conversation as $message)

                                        @if ($message->from == '')
                                            <li class="chat-message chat-message-right">
                                                <div class="d-flex overflow-hidden">
                                                    <div class="chat-message-wrapper flex-grow-1">
                                                        @if ($message->attachment != '' && $message->message != '')
                                                            <div class="imagetext">

                                                                <img src="{{ $message->attachment }}" alt=""
                                                                    class="rounded image-preview" width="200"
                                                                    height="200">
                                                                <div class="chat-message-text">
                                                                    <p class="mb-0" style="color: #fff">
                                                                        {!! nl2br(e($message->message)) !!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        @else
                                                            @if ($message->attachment != '')
                                                                <img src="{{ $message->attachment }}" alt=""
                                                                    class="rounded image-preview" width="200"
                                                                    height="200">
                                                            @endif
                                                            @if ($message->message != '')
                                                                <div class="chat-message-text">
                                                                    <p class="mb-0" style="color: #fff">
                                                                        {!! nl2br(e($message->message)) !!}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                        @endif



                                                        <div class="text-muted mt-1">
                                                            <small>{{ $message->time }}</small>
                                                        </div>
                                                    </div>

                                                </div>
                                            </li>
                                        @else
                                            <li class="chat-message">
                                                <div class="d-flex overflow-hidden">
                                                    <div class="user-avatar flex-shrink-0 me-3">
                                                        <div class="avatar avatar-sm">
                                                            @if ($findUser)
                                                                <img src="{{ $findUser->image != '' ? $findUser->image : asset('placeholder.png') }}"
                                                                    alt="Avatar" class="rounded-circle" />
                                                            @else
                                                                <img src="placeholder.png" alt="Avatar"
                                                                    class="rounded-circle" />
                                                            @endif

                                                        </div>
                                                    </div>
                                                    <div class="chat-message-wrapper flex-grow-1">
                                                        @if ($message->attachment != '' && $message->message != '')
                                                            <div class="imagetext">
                                                                <img src="{{ $message->attachment }}" alt=""
                                                                    class="rounded image-preview" width="200"
                                                                    height="200">
                                                                <div class="chat-message-text">

                                                                    <p class="mb-0" style="color: #000000">
                                                                        {{ $message->message }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        @else
                                                            @if ($message->attachment != '')
                                                                <img src="{{ $message->attachment }}" alt=""
                                                                    class="rounded image-preview" width="200"
                                                                    height="200">
                                                            @endif
                                                            @if ($message->message != '')
                                                                <div class="chat-message-text">
                                                                    <p class="mb-0" style="color: #000000">
                                                                        {{ $message->message }}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                        @endif



                                                        <div class="text-muted mt-1">
                                                            <small>{{ $message->time }}</small>
                                                        </div>

                                                    </div>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            @if ($ticket->status == 0)
                                <div class="chat-history-footer shadow-sm d-flex align-items-end">
                                    <form class="d-flex flex-grow-1 align-items-end" id="messageForm"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @if ($findUser)
                                            <input type="hidden" name="user_id" value="{{ $findUser->uuid }}">
                                        @endif
                                        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                                        <label for="attach-doc" id="messageImageIcon" class="form-label mb-0 me-2">
                                            <i class="ti ti-photo cursor-pointer"></i>
                                            <input type="file" name="attachment" id="attach-doc" accept="image/*"
                                                hidden="">
                                        </label>
                                        <label class="form-label mb-0 me-2" id="messageImage" style="display: none">
                                            <div style="position: relative; display: inline-block;">
                                                <img id="selectedimage" src="" alt="Selected Image"
                                                    class="rounded image-preview">
                                                <span id="removeImage"><i class="fas fa-times"></i></span>
                                            </div>
                                        </label>
                                        <textarea class="form-control message-input border-0 shadow-none flex-grow-1" placeholder="Type your message here"
                                            rows="1" name="message" id="textInput"></textarea>

                                        <div class="message-actions d-flex align-items-center ms-2">
                                            <button type="submit" class="btn btn-primary d-flex send-msg-btn"
                                                id="sendMessage">
                                                <i class="ti ti-send me-md-1 me-0" id="sendicon"></i>
                                                <span class="align-middle" id="sending">Send</span>
                                                <span class="align-middle spinner-border"
                                                    style="display: none;color: #fff" id="loader" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>


                    <div class="app-overlay"></div>
                </div>
            </div>
        </div>
        <div class="modal fade imageModal" id="imageModal1" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered modal-simple modal-upgrade-plan modal-lg">
                <div class="modal-content  p-0 bg-transparent">
                    <div class="modal-body p-0">
                        <img id="selected-image1" src="#" style="width: 100%">
                    </div>
                </div>
            </div>
        </div>
        <!-- / Content -->
    @endsection

    @section('script')


        <!-- Page JS -->

        <!-- Page JS -->
        <script src="/panel-v1/assets/js/app-chat.js"></script>
        <script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
        <script>
            $('.image-preview').on('click', function() {
                var imageUrl = $(this).attr('src'); // Use $(this) to reference the clicked image
                $('#selected-image1').attr('src', imageUrl);
                $('#imageModal1').modal('show');
            });
        </script>
        <script>
            function scrollToBottom() {
                var chatHistory = document.getElementById('chat-history-body');
                chatHistory.scrollTop = chatHistory.scrollHeight;

            }
            $(document).ready(function() {
                $(document).on('submit', '#messageForm', function(e) {
                    e.preventDefault();
                    var loader = $('#loader');
                    var sending = $('#sending');
                    var sendicon = $('#sendicon');

                    var messageInputValue = $('.message-input').val().trim();
                    var imageInputValue = $('#attach-doc')[0].files.length === 0;
                    if (messageInputValue === '' && imageInputValue) {
                        // Both inputs are empty
                        return;
                    }



                    loader.show()
                    sendicon.hide();
                    sending.hide();

                    var formData = new FormData(this);

                    $.ajax({
                        type: "POST",
                        url: '{{ route('dashboard-ticket-send-message') }}',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            loader.hide()
                            sendicon.show();
                            sending.show();
                            console.log(response);
                            var messageContent = response.message.replace(/\r\n|\r|\n/g, '<br>');
                            if (response.attachment && response.message) {
                                var newMessage = `
                            <li class="chat-message chat-message-right">
                                    <div class="d-flex overflow-hidden">
                                        <div class="chat-message-wrapper flex-grow-1">
                                           <div class="imagetext">
                                            ${response.attachment ? `<img src="${response.attachment}" alt="" class="rounded image-preview"  width="200" height="200" style="object-fit: cover;
                                                                                                                                                                                                                        object-position: center;">` : ''}
                                            ${messageContent ? `<div class="chat-message-text" style="width: #000000"><p class="mb-0" style="color: #fff">${messageContent}</p></div>` : ''}
                                            </div>
                                            <div class="text-end text-muted mt-1">
                                                <small>Just now</small>
                                            </div>
                                        </div> 
                                      
                                    </div>
                                 </li>
                                `;
                            } else {
                                var newMessage = `
                            <li class="chat-message chat-message-right">
                                    <div class="d-flex overflow-hidden">
                                        <div class="chat-message-wrapper flex-grow-1">
                                            ${response.attachment ? `<img src="${response.attachment}" alt="" class="rounded image-preview"  width="200" height="200" style="object-fit: cover;
                                                                                                                                                                                                                                object-position: center;">` : ''}
                                            ${messageContent ? `<div class="chat-message-text" style="width: #000000"><p class="mb-0" style="color: #fff">${messageContent}</p></div>` : ''}
                                            <div class="text-end text-muted mt-1">
                                                <small>Just now</small>
                                            </div>
                                        </div> 
                                        
                                    </div>
                                 </li>
                                `;
                            }

                            $('#list-unstyled').append(newMessage);
                            scrollToBottom();
                            $('.message-input').val('');
                            $('#attach-doc').val('');
                            $('#messageImageIcon').show();
                            $('#messageImage').hide();
                            $('.message-input').height(31);
                            $('.image-preview').on('click', function() {
                                var imageUrl = $(this).attr(
                                    'src');
                                $('#selected-image1').attr('src', imageUrl);
                                $('#imageModal1').modal('show');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#attach-doc').on('change', function(event) {
                    var file = event.target.files[0];
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#selectedimage').attr('src', e.target.result);
                            $('#messageImage').show();
                            $('#messageImageIcon').hide();
                        };
                        reader.readAsDataURL(file);
                    }
                });

                $('#removeImage').on('click', function() {
                    $('#selectedimage').attr('src', '');
                    $('#attach-doc').val('');
                    $('#messageImage').hide();
                    $('#messageImageIcon').show();
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const textarea = document.getElementById('textInput');
                const chatHistoryBody = document.getElementById('chat-history-body');

                textarea.addEventListener('input', () => {
                    // Reset textarea height to auto to calculate new height
                    textarea.style.height = 'auto';
                    // Set textarea height to scrollHeight
                    textarea.style.height = `${textarea.scrollHeight}px`;

                    // Adjust chat history body scroll
                    chatHistoryBody.scrollTop = chatHistoryBody.scrollHeight;
                });

                function scrollToBottom() {
                    chatHistoryBody.scrollTop = chatHistoryBody.scrollHeight;
                }
                scrollToBottom();
            });
        </script>
    @endsection
