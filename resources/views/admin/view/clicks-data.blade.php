@extends('layouts.view-partner-template')

@section('child-content')

    <div class="container">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('view.partner.clicksdata', [$partner->id], false) }}" class="mb-1 mt-4">
            <div class="row">
                <!-- Filter Dropdown -->
                <div class="col-md-2 mb-3">
                    <label for="filter" class="fw-bold">Filter:</label>
                    <select name="filter" class="form-control" id="filter">
                        @foreach (['mtd' => 'Month to Date', 'last_12_months' => 'Last 12 Months', 'last_6_months' => 'Last 6 Months', 'last_3_months' => 'Last 3 Months', 'last_1_month' => 'Last 1 Month', 'last_month' => 'Last Month', 'last_7_days' => 'Last 7 Days', 'custom' => 'Custom Range'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('filter') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Custom Date Range -->
                <div class="col-md-2 mb-3 date-range" style="display: none;">
                    <label for="date_from" class="fw-bold">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                        value="{{ \Carbon\Carbon::parse($dateFrom)->format('Y-m-d') }}">
                </div>
                <div class="col-md-2 mb-3 date-range" style="display: none;">
                    <label for="date_to" class="fw-bold">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                        value="{{ \Carbon\Carbon::parse($dateTo)->format('Y-m-d') }}">
                </div>

                <!-- Data Split Dropdown -->
                <div class="col-md-2 mb-3">
                    <label for="data_split" class="fw-bold">Data Split:</label>
                    <select name="data_split" class="form-control" id="data_split">
                        @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('data_split') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Apply Button -->
                <div class="col-md-1 d-flex align-items-end mb-3">
                    <button type="submit" class="btn button-clearlink text-primary fw-bold">Apply</button>
                </div>

                <!-- Export Button -->
                <div class="col-md-1 d-flex align-items-end mb-3 me-4">
                    <a href="{{ route(
                        'view.partner.reports.export',
                        [
                            'id' => $partner->id,
                            'filter' => request('filter', 'mtd'),
                            'data_split' => request('data_split', 'daily'),
                            'date_from' => \Carbon\Carbon::parse($dateFrom)->format('Y-m-d'),
                            'date_to' => \Carbon\Carbon::parse($dateTo)->format('Y-m-d'),
                        ],
                        false,
                    ) }}"
                        class="btn btn-primary d-flex align-items-center">
                        Report <i class="ms-2 fa-solid fa-download"></i>
                    </a>
                </div>

                <!-- Budget Cap Button -->
                <div class="col-md-3 d-flex align-items-end mb-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Budget Cap Settings
                    </button>
                </div>
            </div>
        </form>

        <!-- Budget Cap Modal -->
        @include('layouts.budget-cap-modal', ['partner' => $partner, 'budget_cap' => $budget_cap])

        @if ($metrics)
            <!-- Metrics Section -->
            <div class="row mb-4">


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


            <!-- Date Range Display -->
            <div class="text-center mb-4">
                <p>Data from: <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</strong> to
                    <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong>
                </p>

            </div>

            <!-- Chart Section -->
            <div class="chart mb-5">
                <h2 class="fw-bold text-center">Clicks Chart</h2>
                <canvas id="clicksChart"></canvas>
            </div>
    </div>
@else
    <div style="margin-top: 200px;" class="d-flex justify-content-center align-items-center">
        <h3>No Clicks Data Found</h3>
    </div>
    @endif

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <script>
        var chartData = @json($chartData);
        var labels = chartData.map(data => data.date);
        var totalClicks = chartData.map(data => data.total_clicks);
        var domainWiseClicks = chartData.map(data => data.domain_clicks);

        var domainColors = {
            "cabletv.com": "#fc4146",
            "highspeedinternet.com": "#f39c12",
            "satelliteinternet.com": "#9b59b6",
            "reviews.org": "#2ecc71",
            "whistleout.com": "#FFB6C1",
        };

        var ctx = document.getElementById('clicksChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Total Clicks',
                        data: totalClicks,
                        borderColor: '#3498db',
                        fill: false,
                    },
                    ...Object.keys(domainWiseClicks[0]).map(function(domain) {
                        var domainData = domainWiseClicks.map(data => data[domain] || 0);
                        return {
                            label: domain,
                            data: domainData,
                            borderColor: domainColors[domain] || '#' + Math.floor(Math.random() * 16777215)
                                .toString(16),
                            fill: false,
                        };
                    })
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
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


    <script>
        $(document).ready(function() {
            // Toggle switch handler
            $('.toggle-switch').on('click', function() {
                var toggleId = $(this).data('toggle');
                var icon = $(this).find('i');
                var newState = icon.hasClass('fa-toggle-off') ? 1 : 0;

                // Update the icon and hidden input value
                icon.toggleClass('fa-toggle-on fa-toggle-off');
                $('#' + toggleId).val(newState);
            });
        });
    </script>
@endsection
@endsection
