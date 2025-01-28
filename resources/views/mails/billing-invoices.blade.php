@extends('layouts.email_template')

@section('content')
    <div style="max-width: 600px; margin: 0 auto; padding: 30px 50px;">
        <table cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td>
                    <div
                        style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">

                        <p
                            style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">
                            Dear {{ $partner_name }},</p>

                        <p
                            style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">
                            Thank you for your business.
                        </p>

                        <p
                            style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">
                            Payment for the invoice {{ $invoice_number }} has been successful. </p>
                        <p
                            style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">
                            Invoice Date : {{ $invoice_date }} </p>
                        <p
                            style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">
                           Invoice Amount : {{ $invoice_price }}</p>
                        <div style="text-align:center;margin:20px auto;display:block;"><a
                                style="background-color: #0d6efd;border-color:none;border-style:solid;padding: 10px;color:#ffffff;display:inline-block;letter-spacing:1px;max-width:300px;min-width:150px;text-align:center;text-decoration:none;text-transform:uppercase;margin: 20px auto;font-size: 16px;border-radius: 5px;font-family: Verdana, sans-serif;"
                                href="{{ $invoice_link }}">View Invoice</a>
                        </div>
                        <p
                            style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">
                            It was great working with you, looking forward to doing business again.</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
