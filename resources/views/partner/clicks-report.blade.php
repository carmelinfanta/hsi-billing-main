@extends('layouts.partner_template')

@section('content')
    @if ($current_subscription)
        <div class="container">
            <h2 class="mt-2">{{ $partner->company_name }} Clicks Report</h2>
            <!-- Filter Form -->
            <form method="GET" action="{{ route('partner.reports', [], false) }}" class="mb-4 mt-4">
                <div class="row">
                    <!-- Filter Dropdown -->
                    <div class="col-md-2">
                        <label for="filter" class="fw-bold">Filter:</label>
                        <select name="filter" class="form-control" id="filter">
                            <option value="mtd" @selected(request('filter') === 'mtd')>Month to Date</option>
                            <option value="last_12_months" @selected(request('filter') === 'last_12_months')>Last 12 Months</option>
                            <option value="last_6_months" @selected(request('filter') === 'last_6_months')>Last 6 Months</option>
                            <option value="last_3_months" @selected(request('filter') === 'last_3_months')>Last 3 Months</option>
                            <option value="last_1_month" @selected(request('filter') === 'last_1_month')>Last 1 Month</option>
                            <option value="last_month" @selected(request('filter') === 'last_month')>Last Month</option>
                            <option value="last_7_days" @selected(request('filter') === 'last_7_days')>Last 7 Days</option>
                        </select>
                    </div>

                    <!-- Data Split Dropdown -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="fw-bold" for="data_split">Show data by</label>

                            @if ($is_daily_plan)
                                <select name="data_split" id="data_split" class="form-control">
                                    <option value="monthly" @selected(request('data_split') === 'monthly')>Month</option>
                                    <option value="weekly" @selected(request('data_split') === 'weekly')>Week</option>
                                    <option value="daily" @selected(request('data_split') === 'daily')>Day</option>
                                </select>
                            @elseif($is_weekly_plan)
                                <select name="data_split" id="data_split" class="form-control">
                                    <option value="monthly" @selected(request('data_split') === 'monthly')>Month</option>
                                    <option value="weekly" @selected(request('data_split') === 'weekly')>Week</option>
                                </select>
                            @elseif($is_monthly_plan)
                                <select name="data_split" id="data_split" class="form-control">
                                    <option value="monthly" @selected(request('data_split') === 'monthly')>Month</option>
                                </select>
                            @endif

                        </div>
                    </div>


                    <!-- Apply Button -->
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn button-clearlink text-primary fw-bold">Apply</button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('partner.reports.export', ['id' => $partner->id, 'filter' => request('filter', 'mtd'), 'data_split' => request('data_split', 'daily')], false) }}"
                            class="btn btn-primary d-flex align-items-center">Download CSV</a>
                    </div>
                </div>
            </form>

            <!-- Metrics Section -->
            @if ($metrics)
                <div class="row mb-3">
                    @foreach ($metricsData as $data)
                        <div id="{{ $data['id'] }}" class="col-md-2 mb-2">
                            <div
                                class="card custom-card shadow {{ $data['bg_class'] ?? '' }} d-flex align-items-center text-center ">
                                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                    <h3
                                        class="card-title {{ $data['bg_class'] ? 'm-0' : '' }} {{ isset($data['additional_info']) ? 'm-0' : '' }}">
                                        {{ $data['value'] }}</h3>
                                    <p
                                        class="card-text {{ $data['bg_class'] ? 'm-0' : 'text-secondary' }}  {{ isset($data['additional_info']) ? 'm-0' : '' }}">
                                        {{ $data['label'] }}</p>

                                    @isset($data['additional_info'])
                                        <p class="body-text-small m-0 badge text-bg-warning">
                                            {{ $data['additional_info']['label'] }}: {{ $data['additional_info']['limit'] }}
                                        </p>
                                        <p class="body-text-small m-0 mt-1">
                                            {{ $data['additional_info']['est_cap_hit_label'] }}:
                                            {{ $data['additional_info']['est_cap_hit_value'] }}
                                        </p>
                                    @endisset
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>




                <!-- Date Range Section -->
                <div class="text-center mb-4">
                    <p>Data from: <strong>{{ $dateFrom->format('d M Y') }}</strong> to
                        <strong>{{ $dateTo->format('d M Y') }}</strong>
                    </p>
                </div>

                <!-- Chart Section -->
                <div class=" mb-5">
                    <canvas id="clicksChart"></canvas>
                </div>
        </div>
    @else
        <div style="margin-top: 200px;" class="d-flex justify-content-center align-items-center ">
            <h3>No Usage Reports Found</h3>
        </div>
    @endif
@else
    <div style="margin-top: 300px;" class="d-flex justify-content-center align-items-center ">
        <h3>No Usage Reports Found</h3>
    </div>
    @endif


@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <script>
        var chartData = @json($chartData);

        var labels = chartData.map(function(data) {
            return data.date;
        });
        var totalClicks = chartData.map(function(data) {
            return data.total_clicks;
        });


        var ctx = document.getElementById('clicksChart').getContext('2d');
        var clicksChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Clicks',
                    data: totalClicks,
                    borderColor: '#3498db',
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            // Get elements
            var filter = $('#filter');
            var dateRange = $('.date-range');
            var invoicePace = $('#invoicePace');
            var clicksPace = $('#clicksPace');
            var totalCost = $('#totalCost');

            // Function to update visibility for date range
            function updateDateRangeVisibility() {
                var selectedValue = filter.val();
                dateRange.css('display', selectedValue === 'custom' ? 'block' : 'none');
            }

            // Function to update visibility for card elements based on selected filter
            function updateCardVisibility() {
                var selectedValue = filter.val();
                console.log(selectedValue);
                var visible = ['mtd', 'this_month', 'last_1_month', 'last_7_days'].includes(selectedValue);
                console.log(visible);
                invoicePace.css('display', visible ? 'block' : 'none');
                clicksPace.css('display', visible ? 'block' : 'none');
                totalCost.css('display', visible ? 'block' : 'none');
            }

            // Initialize visibility
            updateDateRangeVisibility();
            updateCardVisibility();

            // Add event listener to filter dropdown
            filter.change(function() {
                updateDateRangeVisibility();
            });

            var urlParams = new URLSearchParams(window.location.search);
            var filterValueFromURL = urlParams.get('filter');
            if (filterValueFromURL === 'mtd' || 'this_month' || 'last_1_month' || 'last_7_days') {
                updateCardVisibility();
            }

        });
    </script>
@endsection
