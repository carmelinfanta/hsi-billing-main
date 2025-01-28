@extends('layouts.view-partner-template')

@section('child-content')
    <div style="width:80%" class="d-flex flex-row justify-content-between mt-5">
        <div>
            <h5 class="fw-bold ">Select Plans for Partner Display</h5>
        </div>
    </div>
    <div class="mb-3 mt-2">
        <label for="planType" class="fw-bold">Select Plan Type: </label>
        <input type="radio" name="planType" id="flat" value="flat" class="ms-2 plan-toggle" checked />
        Flat
        <input type="radio" name="planType" id="cpc" value="cpc" class="ms-2 plan-toggle" /> CPC
    </div>

    <div class="tables w-100">
        <form method="post" id="yourForm" action="/add-selected-plans">
            @csrf
            <table class="text-center mt-2 table table-bordered partner-card mb-4">
                <thead class="bg-clearlink fw-bold">
                    <tr>
                        <th>S.No</th>
                        <th>Plan Name</th>
                        <th>Price</th>
                        <th>Select</th>
                    </tr>
                </thead>

                <tbody id="planTableBody">
                    <!-- Table rows will be dynamically inserted here -->
                </tbody>

            </table>
        </form>
    </div>


    <script>
        $(document).ready(function() {
            // Passing the PHP array to JavaScript
            var selectedPlans = @json($selected_plans);
            var currentPlan = @json($current_plan);
            var enterprisePlan = @json($is_enterprise_plan);
            var partnerId = @json($partner->id);
            var cpcPlan = @json($cpc_plan);

            $('input[name="planType"]').prop('checked', false);

            // Select the radio button based on the selected plan type
            if (cpcPlan) {
                $('#cpc').prop('checked', true);
            } else {
                $('#flat').prop('checked', true);
            }

            $('input[name="planType"]').change(function() {
                var selectedPlanType = $("input[name='planType']:checked").val();

                $.ajax({
                    url: '/get-plans',
                    type: 'GET',
                    data: {
                        planType: selectedPlanType
                    },
                    success: function(response) {
                        var tableBody = $('#planTableBody');
                        tableBody.empty(); // Clear previous rows
                        if (selectedPlanType === 'cpc') {
                            var customPlanChecked = Array.isArray(selectedPlans) &&
                                selectedPlans.includes('custom-cpc') ? 'checked' : '';
                        } else {
                            var customPlanChecked = Array.isArray(selectedPlans) &&
                                selectedPlans.includes('custom') ? 'checked' : '';
                        }

                        var customPlanDisabled = enterprisePlan ? 'disabled' : '';
                        var customPlanValue = selectedPlanType === 'cpc' ? 'custom-cpc' :
                            'custom';
                        var planName = selectedPlanType === 'flat' ?
                            'Custom Enterprise' : 'CPC Custom Enterprise';
                        var row = `<tr>
                                <td>1</td>
                                <td>${planName}</td>
                                <td>Contact Us</td>
                                <td><input type="checkbox" name="options[]" value="${customPlanValue}" class="form-check-input select-plans-input" ${customPlanChecked} ${customPlanDisabled} /></td>
                            </tr>`;
                        tableBody.append(row);

                        // Loop through plans and append rows
                        response.plans.forEach(function(plan, index) {
                            // Check if the plan is in selected_plans and if it's the current plan
                            var isChecked = selectedPlans && selectedPlans.includes(plan
                                .plan_id) ? 'checked' : '';
                            var isDisabled = currentPlan && currentPlan.plan_id === plan
                                .plan_id ? 'disabled' : '';

                            var row = `<tr>
                                    <td>${index + 2}</td>
                                    <td>${plan.plan_name}</td>
                                    <td>${plan.price}</td>
                                    <td>
                                        <input type="checkbox" name="options[]" value="${plan.plan_id}"
                                            class="form-check-input select-plans-input"
                                            ${isChecked} ${isDisabled} />
                                    </td>
                                </tr>`;
                            tableBody.append(row);
                        });

                        var submitRow = `<tr>
                                   <td></td>
                        <td></td>
                        <td><input name="partner_id" value="${partnerId}" hidden /></td>
                        <td><input type="submit" class="btn btn-primary btn-sm" value="Submit" /></td>
                    </tr>`;
                        tableBody.append(submitRow);
                    },
                    error: function() {
                        alert("An error occurred while fetching the plans.");
                    }
                });
            });

            $('#yourForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission for testing purposes

                // Add additional validation or processing if needed
                console.log('Form submitted');

                // Optionally, submit the form using AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {

                        // Update the alert text directly
                        $('#alert-msg').html(
                            'Selected Plans added Successfully <button type="button" id="alert-close" class="close btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                        );

                        // Show the alert component
                        $('#alert-msg').removeClass('d-none').addClass('show');

                        // Optionally hide the alert after 3 seconds
                        setTimeout(function() {
                            $('#alert-msg').removeClass('show').addClass('d-none');
                        }, 3000); // Hide after 3 seconds
                        console.log('Form Submitted Successfully');
                        location.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            });
            // Trigger change on page load to initialize
            $('input[name="planType"]:checked').trigger('change');
        });
    </script>
@endsection
