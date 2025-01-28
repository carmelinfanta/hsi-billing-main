@extends('layouts.email_template')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 30px 50px;">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td>
                <div style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">

                    <p style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">Dear <b>{{$name}},</b></p>

                    <p style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">Thank you for signing up with us and submitting your company information! We noticed that you haven't yet selected a plan. To get started, please <a href="{{ env('APP_URL') }}/login" style="color: #007bff; text-decoration: none;">log in to your account </a> and choose from the available plans.</p>

                    <p style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">Before finalizing, please ensure that your payment method is ready. For the best value, we recommend using the Bank Account (ACH) option to avoid any additional service fees associated with credit card payments. </p>

                    <p style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">We appreciate your interest, and we’re excited to have you on board.</p>

                </div>
            </td>
        </tr>
    </table>
</div>
@endsection