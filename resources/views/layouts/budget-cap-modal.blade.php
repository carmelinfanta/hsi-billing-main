<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Budget Cap Settings</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Toggle for Clicks Pace -->
                <div class="d-flex justify-content-between align-items-center">
                    <label for="clicks_pace" class="fw-bold">Show Clicks Pace to partner</label>
                    <span class="text-primary fs-3 ms-3 cursor-pointer toggle-switch" data-toggle="clicks_pace_toggle">
                        <i
                            class="{{ optional($budget_cap)->clicks_pace_toggle ? 'fa-solid fa-toggle-on' : 'fa-solid fa-toggle-off' }}"></i>
                    </span>
                </div>

                <!-- Toggle for Invoice Pace -->
                <div class="d-flex justify-content-between align-items-center">
                    <label for="invoice_pace" class="fw-bold">Show Invoice Pace to partner</label>
                    <span class="text-primary fs-3 ms-3 cursor-pointer toggle-switch" data-toggle="invoice_pace_toggle">
                        <i
                            class="{{ optional($budget_cap)->invoice_pace_toggle ? 'fa-solid fa-toggle-on' : 'fa-solid fa-toggle-off' }}"></i>
                    </span>
                </div>

                <!-- Toggle for Budget Cap -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label for="budget_cap" class="fw-bold">Show Budget Cap to partner</label>
                    <span class="text-primary fs-3 ms-3 cursor-pointer toggle-switch" data-toggle="budget_cap_toggle">
                        <i
                            class="{{ optional($budget_cap)->budget_cap_toggle ? 'fa-solid fa-toggle-on' : 'fa-solid fa-toggle-off' }}"></i>
                    </span>
                </div>

                <!-- Form to update limits -->
                <form action="/update-limit" method="post">
                    @csrf
                    <div class="row">
                        <!-- Hidden inputs for toggles -->
                        <input type="hidden" id="clicks_pace_toggle" name="clicks_pace_toggle"
                            value="{{ optional($budget_cap)->clicks_pace_toggle ? '1' : '0' }}">
                        <input type="hidden" id="invoice_pace_toggle" name="invoice_pace_toggle"
                            value="{{ optional($budget_cap)->invoice_pace_toggle ? '1' : '0' }}">
                        <input type="hidden" id="budget_cap_toggle" name="budget_cap_toggle"
                            value="{{ optional($budget_cap)->budget_cap_toggle ? '1' : '0' }}">

                        <!-- Budget Cap -->
                        <div class=" mb-3">
                            <label for="cost_limit" class="form-label fw-bold">Budget Cap</label>
                            @if ($budget_cap)
                                @if ($budget_cap->plan_type === 'flat')
                                    <input name="cost_limit"
                                        value="{{ isset($budget_cap->cost_limit) ? $budget_cap->cost_limit : $budgetLimit }}"
                                        class=" form-control" placeholder=" Budget Cap">
                                    <input name="click_limit" value="{{ $clicksLimit }}" hidden />
                                @else
                                    <input name="cost_limit"
                                        value="{{ isset($budget_cap->cost_limit) ? $budget_cap->cost_limit : $budgetLimit }}"
                                        class=" form-control" placeholder=" Budget Cap">
                                    <input name="click_limit" value="{{ $clicksLimit }}" hidden />
                                @endif
                            @else
                                <input name="cost_limit" class=" form-control" placeholder=" Budget Cap"
                                    value="{{ $budgetLimit }}">
                                <input name="click_limit" value="{{ $clicksLimit }}" hidden />
                            @endif
                        </div>
                    </div>
                    @if ($currentPlan)
                        <div class="row">
                            <div class="mb-3 col-lg-6">
                                <p class="fw-bold">Plan Name: {{ $currentPlan->plan_name }}</p>
                            </div>
                            <div class="mb-3 col-lg-6">
                                <p class="fw-bold">Plan Price: ${{ $currentPlan->price }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($currentAddon)
                        <div class="row">
                            <div class="mb-3 col-lg-6">
                                <p class="fw-bold">Addon Name: {{ $currentAddon->name }}</p>
                            </div>
                            <div class="mb-3 col-lg-6">
                                <p class="fw-bold"> Addon Price: {{ $currentAddon->addon_price }} </p>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        @if ($currentPlanType)
                            <input name="plan_type" type="hidden" value="{{ $currentPlanType }}">
                            <p class="fw-bold">Plan Type: {{ $currentPlanType }}</p>
                        @else
                            <label for="plan_type" class="form-label fw-bold">Plan Type*</label>
                            <select name="plan_type" class="form-select" required>
                                <option value="">Select Plan Type*</option>
                                <option value="flat">Flat</option>
                                <option value="cpc">CPC</option>
                            </select>
                        @endif
                    </div>
            </div>


            <input name="partner_id" value="{{ $partner->id }}" hidden />
            <input name="plan_price" value="{{ optional($currentPlan)->price }}" hidden />

            <button type="submit" class="btn btn-primary">Save changes</button>
            </form>
        </div>
    </div>
</div>
</div>
