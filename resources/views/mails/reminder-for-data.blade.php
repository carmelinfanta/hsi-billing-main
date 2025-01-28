@extends('layouts.email_template')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 30px 50px;">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td>
                <div style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">

                    <p style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">Dear <b>{{$name}},</b></p>

                    <p style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">Thank you for signing up with us! In order to complete your company set up, please <a href="{{ env('APP_URL') }}/login" style="color: #007bff; text-decoration: none;">log in to your account</a> and complete the information in the Provider Data and Company Info sections. You will need the following: </p>
                    
                    <ul style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">
                        <li>Logo file to upload</li>
                        <li>Company name to display on site</li>
                        <li>Landing Page URL (The page where you want customers to land on your site.)</li>
                        <li>
                            Zip list availability using 
                            <a href="{{ env('APP_URL') }}/assets/sample/zip_list_template.csv" style="color: #007bff; text-decoration: none;">
                                this template
                            </a>
                        </li>
                    </ul>
                    <p style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">We appreciate your interest, and we’re excited to have you on board.</p>
                    
                    


                </div>
            </td>
        </tr>
    </table>
</div>
@endsection