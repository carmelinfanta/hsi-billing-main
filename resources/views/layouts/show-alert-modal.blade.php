<div class="modal-body mb-5">

    <ul class="message">
        <li class="d-flex justify-content-between">
            @if (!$company_info)
                <a href="/company-info">Upload Logo (Company Info)</a>
            @endif
            @if ($company_info)
                <span>Upload Logo (Company Info)
                </span>
                <i class="fa-solid fa-check text-check fs-3"></i>
            @endif
        </li>
        <li class="d-flex justify-content-between">
            @if (!$company_info)
                <a href="/company-info">Add Company Name (Company Info)</a>
            @endif
            @if ($company_info)
                <span>Add Company Name (Company Info)
                </span>
                <i class="fa-solid fa-check text-check fs-3"></i>
            @endif
        </li>
        <li class="d-flex justify-content-between">
            @if (!$company_info)
                <a href="/company-info">Set Landing Page URL (Company
                    Info)</a>
            @endif
            @if ($company_info)
                <span>Set Landing Page URL (Company
                    Info)</span>
                <i class="fa-solid fa-check text-check fs-3"></i>
            @endif
        </li>
        <li class="d-flex justify-content-between">
            @if (!$availability_data)
                <a href="/provider-info">Upload Provider Data</a>
            @endif
            @if ($availability_data)
                <span>Upload Provider Data</span>
                <i class="fa-solid fa-check text-check fs-3"></i>
            @endif
        </li>
        <li class="d-flex justify-content-between">
            @if (!$paymentmethod)
                <a href="/select-plans">Add Payment Method</a>
            @endif
            @if ($paymentmethod)
                <span>Add Payment Method</span>
                <i class="fa-solid fa-check text-check fs-3"></i>
            @endif
        </li>

    </ul>

</div>
