<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Clearlink ISP Partner Program</title>
    <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" type="image/x-icon" />
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap"
        rel="stylesheet" />

    <link href="{{ asset('assets/css/partner.css') }}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        (function(c, l, a, r, i, t, y) {
            c[a] = c[a] || function() {
                (c[a].q = c[a].q || []).push(arguments)
            };
            t = l.createElement(r);
            t.async = 1;
            t.src = "https://www.clarity.ms/tag/" + i;
            y = l.getElementsByTagName(r)[0];
            y.parentNode.insertBefore(t, y);
        })(window, document, "clarity", "script", "n8x5ekx79q");
    </script>

</head>

<body>

    @if (Session::has('success'))
        <div id="alert-success"
            class="position-absolute end-0 alert alert-success alert-dismissible centered-alert fade show"
            role="alert">{{ Session::get('success') }} <button type="button" id="alert-close" class="close btn-close"
                data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif

    @if (Session::has('fail'))
        <div id="alert"
            class="position-absolute end-0 alert alert-danger alert-dismissible centered-alert fade show"
            role="alert">{{ Session::get('fail') }} <button type="button" id="alert-close" class="close btn-close"
                data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif

    <div id="overlay"></div>
    <div class="scrollable blurred-bg">
        <nav class="navbar p-0 m-0 ">
            <div class="logo_item ">
                <div class="d-flex flex-row ms-3 mt-2 justify-content-between">
                    <div class="ms-1 pb-2">
                        <a href="#" class="mb-2">
                            <img width="150" height="33" src="{{ asset('assets/images/cl_logo.svg') }}" /></a>
                    </div>
                    <div class="me-3 mt-1">
                        <i class="fa-solid fa-bars" id="sidebarOpen"></i>
                    </div>
                </div>
            </div>
            <div class="navbar_content">
                <i class="bi bi-grid"></i>
                <i class="bx bx-sun" id="darkLight"></i>
                <i class="bx bx-bell"></i>
            </div>
        </nav>
        <div class="wrapper">
            <aside id="sidebar">
                <div class="d-flex flex-row justify-content-between sidebar-sm-header">
                    <a href="#" class="mb-1 ms-1 text-start">
                        <img width="150" height="33" src="{{ asset('assets/images/cl_logo.svg') }}" /></a>
                    <i class="fa-solid fa-xmark " id="sidebarClose"></i>
                </div>
                <div class="d-flex">
                    <div class="sidebar-logo">
                        <a href="#">
                            <img class="mt-3 ms-3" width="150" height="33" alt="Clearlink Logo"
                                src="{{ asset('assets/images/cl_logo.svg') }}" /></a>
                    </div>
                </div>
                <ul class="sidebar-nav">
                    <li class="sidebar-item">
                        <a href="/provider-info" id="sidebarMenu-provider"
                            class="{{ Request::is('provider-info') ? 'sidebar-link active-link' : 'sidebar-link' }}">
                            <span>Provider Data</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/company-info" id="sidebarMenu-provider"
                            class="{{ Request::is('company-info') || Request::is('company-info') ? 'sidebar-link active-link' : 'sidebar-link' }}">
                            <span>Company Info</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/clicks-report" id="sidebarMenu-clicks"
                            class="{{ Request::is('clicks-report') ? 'sidebar-link active-link' : 'sidebar-link' }}">
                            <span>Usage Reports</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/profile" id="sidebarMenu-profile"
                            class="{{ Request::is('profile') ? 'sidebar-link active-link' : 'sidebar-link' }}">
                            <span>Profile</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                            data-bs-target="#auth" aria-expanded="false" aria-controls="auth">
                            <span>Plan Management</span><span class="ms-1"><i
                                    class="fa-solid fa-angle-down"></i></span>
                        </a>

                        <ul id="auth" class="ms-5 sidebar-dropdown list-unstyled collapse show"
                            data-bs-parent="#sidebar">
                            @if ($hasLiveSubscription)
                                <li class="sidebar-item ">
                                    <a href="/" id="sidebarMenu-plans"
                                        class="{{ Request::is('/') ? 'sidebar-link active-link' : 'sidebar-link' }}">
                                        - Plan Options</a>
                                </li>
                            @elseif(!$isPlanSelected)
                                <li class="sidebar-item ">
                                    <a href="/select-plans" id="sidebarMenu-plans"
                                        class="{{ Request::is('select-plans') ? 'sidebar-link active-link' : 'sidebar-link' }}">
                                        - Select Plans</a>
                                </li>
                            @endif
                            <li class="sidebar-item">
                                <a href="/subscription" id="sidebarMenu-subscriptions"
                                    class="{{ Request::is('subscription') ? 'sidebar-link active-link' : 'sidebar-link' }}">
                                    -Plan Subscriptions</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="/invoices" id="sidebarMenu-invoices"
                                    class="{{ Request::is('invoices') ? 'sidebar-link active-link' : 'sidebar-link' }}">
                                    - Invoices</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="/creditnotes" id="sidebarMenu-creditnotes"
                                    class="{{ Request::is('creditnotes') ? 'sidebar-link active-link' : 'sidebar-link' }}">
                                    - Credit Notes</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="/support" id="sidebarMenu-support"
                                    class="{{ Request::is('support') ? 'sidebar-link active-link' : 'sidebar-link' }}">
                                    - Support Ticket</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a href="https://socxly.co/clearlink-isp-partner-portal" id="sidebarMenu-appguide"
                            class="sidebar-link" target="_blank">
                            <span>Portal Walkthrough</span>
                        </a>
                    </li>
                </ul>
                <div class="bottom-footer">
                    <hr class="line mt-0" />
                    <div>
                        <a class="sidebar-footer p-0 m-0 mt-3 mb-2 ms-4">
                            <span class="text-dark fw-bold"><strong>Welcome!</strong></span>
                        </a>
                        <a class="sidebar-footer p-0 m-0 mb-2 ms-4">
                            <span class="text-dark">{{ Session::get('loginPartner') }}</span>
                        </a>
                        <a href="/logout" class="sidebar-footer text-center p-0 m-0 mb-4 ms-4 logout">
                            <span class="btn fw-bold  text-primary ">Logout</span>
                        </a>
                    </div>

                    <div class="footer p-0 m-0 mt-3">
                        <a href="#" class="sidebar-footer footer">
                            <p class="text-dark small text-wrap p-0 mb-4">
                                <span class="text-dark p-0 mb-4">@ Clearlink Technologies 2024</span>
                            </p>
                        </a>
                    </div>
                </div>
            </aside>
            <div class="sm-footer fw-bold">
                Signed in as {{ Session::get('loginPartner') }}
            </div>



            <div id="content" style="box-sizing: border-box; margin-left:300px; width:100%" class="p-3">

                @yield('content')

            </div>
            @if (!$hasLiveSubscription)
                @if (request()->is('subscription') ||
                        request()->is('provider-info') ||
                        request()->is('company-info') ||
                        request()->is('clicks-report') ||
                        request()->is('profile') ||
                        request()->is('invoices') ||
                        request()->is('creditnotes') ||
                        request()->is('support'))
                    <div class="modal fade" id="showAlertSubModal" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-popup">
                                <div class="modal-header d-flex justify-content-between border-0 bg-popup">
                                    <h5 class="fw-bold message"> Please complete the following to create a Subscription
                                    </h5>
                                    <button type="button" id="close" class="close border-0 bg-popup"
                                        data-bs-dismiss="modal" aria-label="Close"><i
                                            class="fa-solid text-dark fa-xmark fs-3 mb-4"></i></button>
                                </div>
                                @include('layouts.show-alert-modal')
                            </div>
                        </div>
                    </div>
                @endif
            @endif
            @if (!$hasLiveSubscription)
                @if (request()->is('subscription') ||
                        request()->is('provider-info') ||
                        request()->is('company-info') ||
                        request()->is('clicks-report') ||
                        request()->is('profile') ||
                        request()->is('invoices') ||
                        request()->is('creditnotes') ||
                        request()->is('support'))
                    <div class="modal fade" id="showAlertSuccessModal" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-popup">
                                <div class="modal-header d-flex justify-content-between border-0 bg-popup">
                                    <h5 class="fw-bold message text-success pt-3 "> Thank you for
                                        completing the required
                                        information. You
                                        will be notified by email when your set up is completed.
                                    </h5>
                                    <button type="button" id="closeSuccess" class="close border-0 bg-popup"
                                        data-bs-dismiss="modal" aria-label="Close"><i
                                            class="fa-solid text-dark fa-xmark fs-2 mb-5 ms-5"></i></button>
                                </div>
                                @include('layouts.show-alert-modal')
                            </div>
                        </div>
                    </div>
                @endif
            @endif


            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

            <script type="text/javascript" src="{{ asset('assets/js/partner.js') }}"></script>

            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
            @if (request()->is('subscription') ||
                    request()->is('provider-info') ||
                    request()->is('company-info') ||
                    request()->is('clicks-report') ||
                    request()->is('profile') ||
                    request()->is('invoices') ||
                    request()->is('creditnotes') ||
                    request()->is('support'))
                <script>
                    var myModal = new bootstrap.Modal(document.getElementById('showAlertSubModal'));
                    var successModal = new bootstrap.Modal(document.getElementById('showAlertSuccessModal'));
                    @if ($company_info === null || ($availability_data === null || $paymentmethod === null))
                        // Show the modal
                        myModal.show();
                        localStorage.removeItem('modalShown');
                    @endif
                    if (localStorage.getItem('modalShown') === null) {
                        @if ($company_info && $availability_data && $paymentmethod)
                            successModal.show();
                            localStorage.setItem('modalShown', 'true');
                        @endif

                    }
                    document.getElementById("close").addEventListener("click", function() {
                        myModal.hide();
                    });
                    document.getElementById("closeSuccess").addEventListener("click", function() {
                        successModal.hide();
                    });
                </script>
            @endif


            <script>
                $(document).ready(function() {
                    $('#openModal').on('click', function() {
                        const fileInput = $('#csvFileInput')[0];
                        if (fileInput.files.length > 0) {
                            const fileName = fileInput.files[0].name;
                            $('#fileName').text(fileName);
                            $('#confirmModal').modal('show');
                        } else {
                            $('#errorText').text('Please select a CSV file first.');
                        }
                    });

                    $('#confirmUpload').on('click', function() {
                        $(this).prop('disabled', true);
                        $(this).text('Processing...');
                        $('#csvForm').submit();
                    });
                });
            </script>

            @yield('scripts')

</body>

</html>
