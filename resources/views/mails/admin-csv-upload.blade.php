@extends('layouts.email_template')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 30px 50px;">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td>
                <div style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">

                    <p style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">Dear {{ $name }},</p>

                    <p style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;"><strong>Partner Data Has Been Submitted</strong></p>

                    <p style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">{{$partner_name}} have submitted the partner data.<br />Here are the details:</p>

                    <ul style="color: black; font-size: 16px; font-family: Verdana, sans-serif; font-weight: 400; text-align: left; line-height: 26px;">

                        <li>Partner Contact Person Name : {{$partner_name}}</li>

                        <li>Partner Contact Person Email : {{$partner_email}}</li>

                        <li>Partner Company Name: {{$partner_company}}</li>

                        @if($file_name)

                        <li>Partner AOA CSV File (Link Expires) : <a href="{{$presigned_url}}">{{$file_name}}</a></li>
                        @else

                        <li>Partner AOA CSV File (Link Expires) : not yet uploaded</li>
                        @endif
                        <li>Partner Logo Uploaded : <a href="{{$logo_presigned_url}}"> logo_link</a></li>

                        <li>Partner Landing Page Url : {{$landing_page_url}}</li>

            
                        @if($tune_link)
                            @php
                                $tuneLinks = json_decode($tune_link, true); 
                            @endphp

                            @if(is_array($tuneLinks) && count($tuneLinks) > 0)
                                <li>Partner Tune Links:</li>
                                <ul>
                                    @foreach($tuneLinks as $link)
                                        <li><a href="{{ $link }}" target="_blank" rel="noopener noreferrer">{{ $link }}</a></li>
                                    @endforeach
                                </ul>
                            @else
                                <li>No Tune Links Available</li>
                            @endif
                        @endif

                    </ul>
                </div>
            </td>
        </tr>
    </table>
</div>
@endsection