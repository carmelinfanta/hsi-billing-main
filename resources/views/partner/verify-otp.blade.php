@extends('layouts.outer_template')

@section('title', 'Verify OTP')

@section('content')

    <div class="container d-flex justify-content-center align-items-center flex-column pt-5">
        <h3 class="text-primary mt-5 mb-3">Clearlink ISP Partner Program OTP Verification</h3>
        <div class="card p-4 w-100 shadow-sm border-0 rounded login-card bg-clearlink">
            <div class="rounded p-2">

                <form action="{{ route('verify.otp', [], false) }}" method="post">
                    @csrf
                    <div class="form-group mb-3">

                        <label for="enter_otp" class="form-label">Email ID</label>

                        <input type="email" name="email" value="{{ session('email') }}"
                            class="form-control border-0 shadow-none" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="otp" class="form-label">Enter OTP</label>
                        <div class="border border-dark rounded d-flex flex-row align-items-center">
                            <input type="text" name="otp" id="otp" class="form-control border-0 shadow-none"
                                required>
                        </div>
                    </div>
                    <div class="form-group mb-3 d-flex justify-content-start align-items-center">
                        <button class="btn btn-primary mr-2" type="submit">Verify OTP</button>

                        <a href="{{ route('resend.otp', [], false) }}" class="btn btn-link">Resend OTP</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@section('footer')
    <footer
        class="footer p-4 bg-clearlink w-100 d-flex justify-content-center position-fixed position-absolute text-center bottom-0 align-items-center ">
        @ Clearlink Technologies 2024
    </footer>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        @php
            $user = DB::table('partner_users')->where('email', session('email'))->first();
            $provider_data = DB::table('provider_data')
                ->where('zoho_cust_id', $user->zoho_cust_id)
                ->first();
            $availability_data = DB::table('provider_availability_data')
                ->where('zoho_cust_id', $user->zoho_cust_id)
                ->first();
            $payment_method = DB::table('payment_methods')
                ->where('zoho_cust_id', $user->zoho_cust_id)
                ->first();
            $all_data_found = $provider_data && $availability_data && $payment_method;
        @endphp
        if (@json($all_data_found)) {
            localStorage.setItem('modalShown', 'true');
        } else {
            localStorage.removeItem('modalShown');
        }
    });
</script>
@endsection
