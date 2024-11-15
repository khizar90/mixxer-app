<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a class="app-brand-link">

            <span class="app-brand-text demo menu-text fw-bold"><img src="/panel-v1/assets/img/App logo.png"
                    alt=""></span>
        </a>

        {{-- <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a> --}}
    </div>
    <div class="brandborder">

    </div>

    {{-- <div class="menu-inner-shadow"></div> --}}




    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Dashboard</span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-') ? 'active' : '' }}">
            <a href="{{ route('dashboard-') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Statistics">Statistics</div>
            </a>
        </li>


        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">User Managements</span>
        </li>
        <li
            class="menu-item {{ Request::url() == route('dashboard-users') ? 'active' : '' }} ||  {{ Str::contains(Request::url(), 'dashboard/users/') ? 'active' : '' }}">
            <a href="{{ route('dashboard-users') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Users</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Mixxers</span>

        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-mixxer-') ? 'active' : '' }}">
            <a href="{{ route('dashboard-mixxer-') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Analytics</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('dashboard-mixxer-list', 'up-coming') ? 'active' : '' }} || {{ Str::contains(Request::url(), 'dashboard/mixxer/up-coming/') ? 'active' : '' }}">
            <a href="{{ route('dashboard-mixxer-list', 'up-coming') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Upcoming</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('dashboard-mixxer-list', 'complete') ? 'active' : '' }} || {{ Str::contains(Request::url(), 'dashboard/mixxer/complete/') ? 'active' : '' }}">
            <a href="{{ route('dashboard-mixxer-list', 'complete') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Complete</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Categories</span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-category-', 'interest') ? 'active' : '' }}">
            <a href="{{ route('dashboard-category-', 'interest') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Interest Categories</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Request Forum</span>
        </li>
        <li
            class="menu-item {{ Request::url() == route('dashboard-feature-request') ? 'active' : '' }} || {{ Str::contains(Request::url(), 'dashboard/feature/detail/') ? 'active' : '' }} ">
            <a href="{{ route('dashboard-feature-request') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div> Feature Request </div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('dashboard-feedback-') ? 'active' : '' }} || {{ Str::contains(Request::url(), 'dashboard/feedback/detail/') ? 'active' : '' }} ">
            <a href="{{ route('dashboard-feedback-') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div> App Feedback </div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('dashboard-feedback-mixxer') ? 'active' : '' }} || {{ Str::contains(Request::url(), 'dashboard/feedback/mixxer/detail') ? 'active' : '' }} ">
            <a href="{{ route('dashboard-feedback-mixxer') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div> Mixxer Feedback </div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('dashboard-feedback-mixxer-check-in') ? 'active' : '' }} ">
            <a href="{{ route('dashboard-feedback-mixxer-check-in') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div> Mixxer Check In Feedback </div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-category-', 'degree') ? 'active' : '' }}">
            <a href="{{ route('dashboard-category-', 'degree') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Degree Titles</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Reports</span>
        </li>

        <li
            class="menu-item {{ Request::url() == route('dashboard-report-user') ? 'active' : '' }} || {{ Str::contains(Request::url(), 'dashboard/report/user/') ? 'active' : '' }} ">
            <a href="{{ route('dashboard-report-user') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div> Reported Users </div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Help & Supports</span>
        </li>
        <li
            class="menu-item {{ Request::url() == route('dashboard-ticket-ticket', 'active') ? 'active' : '' }} || {{ Str::contains(Request::url(), 'dashboard/ticket/active/messages') ? 'active' : '' }}">
            <a href="{{ route('dashboard-ticket-ticket', 'active') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div> Active Tickets </div>

            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('dashboard-ticket-ticket', 'close') ? 'active' : '' }} || {{ Str::contains(Request::url(), 'dashboard/ticket/close/messages') ? 'active' : '' }}">
            <a href="{{ route('dashboard-ticket-ticket', 'close') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>

                <div>Closed Tickets</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-version-','android') ? 'active' : '' }} || {{ Str::contains(Request::url(), '/dashboard/version') ? 'active' : '' }}">
            <a href="{{ route('dashboard-version-','android') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n=" App Versions"> App Versions</div>
            </a>
        </li>

        <li class="menu-item {{ Request::url() == route('dashboard-faqs-') ? 'active' : '' }}">
            <a href="{{ route('dashboard-faqs-') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="FAQ'S">FAQ'S</div>
            </a>
        </li>

    </ul>
</aside>
