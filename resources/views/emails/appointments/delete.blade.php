@extends('emails.layouts.master')

@section('body')
    <!-- Email Body -->
    <tr>
        <td class="email-body" width="570" cellpadding="0" cellspacing="0"
            style="word-break: break-word; margin: 0; padding: 0; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; width: 100%; -premailer-width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0;">
            <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation"
                style="width: 570px; -premailer-width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; background-color: #FFFFFF; margin: 0 auto; padding: 0;"
                bgcolor="#FFFFFF">
                <!-- Body content -->
                <tr>
                    <td class="content-cell"
                        style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 45px;">
                        <div class="f-fallback">
                            <h1 style="margin-top: 0; color: #333333; font-size: 22px; font-weight: bold; text-align: left;"
                                align="left">Hey, {{ $user['name'] }}!</h1>
                            <p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">You
                                just cancelled your appointment, We hope we can serve you better. We encourage you to visit
                                our create appointment endpoint to book a new appointment with us.
                                <br><br><br><br><br>Thanks for choosing us.
                            </p>
                            <!-- Action -->
                            <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0"
                                role="presentation"
                                style="width: 100%; -premailer-width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; text-align: center; margin: 30px auto; padding: 0;">
                                <tr>
                                    <td align="center"
                                        style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px;">

                                    </td>
                                </tr>

                                <!-- Sub copy -->

                        </div>
                    </td>
                </tr>
            </table>
        </td>
        <yeah>
    </tr>
@endsection
