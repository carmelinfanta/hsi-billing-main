@extends('layouts.admin_template')

@section('content')
    <div class="d-flex flex-row">
        <h5 class="fw-bold mt-1">{{ $partner->company_name }}</h5>
        @if ($partner->status === 'active')
            <span class="badge-warning p-1 status ms-3 mb-2">Setup In Progress</span>
        @elseif($partner->status === 'inactive')
            <span class="badge-fail p-1 status ms-3 mb-2">{{ $partner->status }}</span>
        @elseif($partner->status === 'Invited')
            <span class="badge-revoked p-1 status ms-3 mb-2">{{ $partner->status }}</span>
        @elseif($partner->status === 'completed')
            <span class="badge-success p-1 status ms-3 mb-2">Setup Completed</span>
        @endif
        @if ($partner->status !== 'completed')
            <a class="btn btn-primary btn-sm mb-2 ms-3" data-bs-toggle="modal" data-bs-target="#showAlertModal">Mark Setup As
                Completed</a>
        @endif
    </div>

    <nav class="navbar1 navbar-expand-lg border-bottom border-dark">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item m-1 me-5">
                    <a class="{{ request()->is('admin/view-partner/' . $partner->id) ? 'nav-link nav-active' : 'nav-link' }}"
                        href="/admin/view-partner/{{ $partner->id }}">Overview</a>
                </li>
                <li class="nav-item m-1 me-5">
                    <a class="{{ request()->is('admin/view-partner/' . $partner->id . '/subscriptions') ? 'nav-link nav-active' : 'nav-link' }}"
                        href="/admin/view-partner/{{ $partner->id }}/subscriptions">Subscriptions</a>
                </li>
                <li class="nav-item m-1 me-5">
                    <a class="{{ request()->is('admin/view-partner/' . $partner->id . '/invoices') ? 'nav-link nav-active' : 'nav-link' }}"
                        href="/admin/view-partner/{{ $partner->id }}/invoices">Invoices</a>
                </li>
                <li class="nav-item m-1 me-5">
                    <a class="{{ request()->is('admin/view-partner/' . $partner->id . '/creditnotes') ? 'nav-link nav-active' : 'nav-link' }}"
                        href="/admin/view-partner/{{ $partner->id }}/creditnotes">Credit Notes</a>
                </li>
                <li class="nav-item m-1 me-5">
                    <a class="{{ request()->is('admin/view-partner/' . $partner->id . '/refunds') ? 'nav-link nav-active' : 'nav-link' }}"
                        href="/admin/view-partner/{{ $partner->id }}/refunds">Refunds</a>
                </li>
                <li class="nav-item m-1 me-5">
                    <a class="{{ request()->is('admin/view-partner/' . $partner->id . '/provider-data') ? 'nav-link nav-active' : 'nav-link' }}"
                        href="/admin/view-partner/{{ $partner->id }}/provider-data">Provider Data</a>
                </li>
                <li class="nav-item m-1 me-5">
                    <a class="{{ request()->is('admin/view-partner/' . $partner->id . '/clicks-data') ? 'nav-link nav-active' : 'nav-link' }}"
                        href="/admin/view-partner/{{ $partner->id }}/clicks-data">Clicks Data</a>
                </li>
                <li class="nav-item m-1 me-5">
                    <a class="{{ request()->is('admin/view-partner/' . $partner->id . '/selected-plans') ? 'nav-link nav-active' : 'nav-link' }}"
                        href="/admin/view-partner/{{ $partner->id }}/selected-plans">Partner Plan View</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="modal fade" id="showAlertModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-popup">
                <div class="modal-header d-flex justify-content-between border-0 bg-popup">
                    <h5 class="fw-bold message">Please complete the following to create a Subscription</h5>
                    <button type="button" class="close border-0 bg-popup" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa-solid text-dark fa-xmark fs-3 mb-4"></i></button>
                </div>
                <div class="modal-body">
                    <ul class="message">
                        <li class="d-flex justify-content-between"><span>Upload Logo (Company Info)</span>
                            @if ($company_info)
                                <i class="fa-solid fa-check text-check fs-3"></i>
                            @endif
                        </li>
                        <li class="d-flex justify-content-between"><span>Add Company Name (Company Info)</span>
                            @if ($company_info)
                                <i class="fa-solid fa-check text-check fs-3"></i>
                            @endif
                        </li>
                        <li class="d-flex justify-content-between"><span>Set Landing Page URL (Company Info)</span>
                            @if ($company_info)
                                <i class="fa-solid fa-check text-check fs-3"></i>
                            @endif
                        </li>
                        <li class="d-flex justify-content-between"><span>Upload Provider Data</span>
                            @if ($availability_data)
                                <i class="fa-solid fa-check text-check fs-3"></i>
                            @endif
                        </li>
                        <li class="d-flex justify-content-between"><span>Add Payment Method</span>
                            @if ($paymentmethod)
                                <i class="fa-solid fa-check text-check fs-3"></i>
                            @endif
                        </li>
                    </ul>
                </div>
                <form action="/charge-subscription" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label for="advertiser_id" class="form-label fw-bold">Advertiser ID*</label>
                            <input name="advertiser_id" value="{{ $partner->isp_advertiser_id }}" class="form-control"
                                placeholder="Advertiser ID*" required>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label for="tune_link" class="form-label fw-bold">Tune Links</label>
                            <div id="tune-links-container">
                                @php
                                    $links = null;

                                    if (!empty($company_info->tune_link)) {
                                        $decoded = json_decode($company_info->tune_link, true);

                                        if (json_last_error() === JSON_ERROR_NONE) {
                                            $links = $decoded;
                                        }
                                    }

                                    $links = $links ?? [];
                                @endphp
                                @foreach ($links as $key => $link)
                                    <div class="link-field mb-2">
                                        <input type="text" name="tune_link[{{ $key }}]"
                                            value="{{ $link }}" class="form-control mb-2 " placeholder="Tune Link">
                                        <button type="button" class="btn btn-danger btn-sm remove-link">Remove</button>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-primary mt-2" id="add-more-links">Add More
                                Links</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row">
                            <label for="plan_code" class="fw-bold form-label mt-3 me-2">Select Plan @if ($selected_plan)
                                    <small class="ms-2 body-text-small fw-normal">(Selected Plan:
                                        {{ $selected_plan->plan_name }} - ${{ $selected_plan->price }})</small>
                                @endif
                            </label>
                        </div>
                        <select name="plan_code" class="form-control" required>
                            <option value="">Select Plan</option>
                            @if ($selected_partner_plans)
                                @foreach ($selected_partner_plans as $plan_id)
                                    @php
                                        $plan = DB::table('plans')->where('plan_id', $plan_id)->first();
                                    @endphp
                                    @if ($plan)
                                        <option value="{{ $plan->plan_code }}"
                                            {{ $plan->plan_code === optional($selected_plan)->plan_code ? 'selected' : '' }}>
                                            {{ $plan->plan_name }} - ${{ $plan->price }}
                                        </option>
                                    @endif
                                @endforeach
                            @else
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->plan_code }}">
                                        {{ $plan->plan_name }} - ${{ $plan->price }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="budget_cap" class="form-label mt-3 fw-bold">Budget Cap</label>
                        @if ($budget_cap)
                            <input name="cost_limit"
                                value="{{ isset($budget_cap->cost_limit) ? $budget_cap->cost_limit : $budgetLimit }}"
                                class="form-control" placeholder="Budget Cap">
                            <input name="click_limit" value="{{ $clicksLimit }}" hidden />
                        @else
                            <input name="cost_limit" class="form-control" placeholder="Budget Cap"
                                value="{{ $budgetLimit }}">
                            <input name="click_limit" value="{{ $clicksLimit }}" hidden />
                        @endif
                    </div>

                    <div class="row">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                @if ($paymentmethod)
                                    @if ($paymentmethod->type === 'bank_account')
                                        <label class="fw-bold">Bank Details</label>
                                    @elseif($paymentmethod->type === 'card')
                                        <label class="fw-bold">Card Details</label>
                                    @endif
                                @else
                                    <h4>Payment Details</h4>
                                @endif
                            </div>
                            <div>
                                @if ($paymentmethod === null)
                                    <a href="add-payment-method/{{ $partner->id }}"><i class="fa-solid fa-circle-plus"
                                            data-toggle="tooltip" title="Associate a payment method"></i></a>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg">
                            <div class="card w-100 border-1 rounded my-1 bg-white">
                                <div class="card-body d-flex flex-row justify-content-between">
                                    <div class="text-dark fw-bold">
                                        @if ($paymentmethod)
                                            @if ($paymentmethod->type === 'bank_account')
                                                <i class="fa-solid fa-building-columns text-primary me-3"></i>
                                            @elseif($paymentmethod->type === 'card')
                                                <i class="fa-regular fa-credit-card text-primary me-3"></i>
                                            @endif
                                            {{ $paymentmethod ? 'XXXX XXXX XXXX ' . $paymentmethod->last_four_digits : '-' }}
                                        @else
                                            <span class="fw-normal">No details found</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="text" value="{{ $partner->zoho_cust_id }}" name="partner_id" class="form-control"
                        hidden>
                    <input type="text" value="{{ $currentPlanType }}" name="plan_type" hidden />
                    @if ($paymentmethod && $company_info && $availability_data)
                        <button type="submit" class="btn btn-primary mt-3 mb-2">Create Subscription</button>
                    @else
                        <input value="save" name="save" hidden />
                        <button type="submit" class="btn btn-primary mt-3 mb-2">Save Changes</button>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div>
        @yield('child-content')
    </div>
@endsection
