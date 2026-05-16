<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name') . ' Admin')</title>
</head>

<body style="margin: 0; padding: 0; background-color: #eef0f7; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0; padding: 0; background-color: #eef0f7;">
        <tr>
            <td style="padding: 40px 20px;">

                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="620" align="center"
                    style="margin: 0 auto; max-width: 620px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 24px rgba(67, 97, 238, 0.10); border: 1px solid #e0e4f0;">

                    @include('admin.emails.partials.admin_header')

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 36px 35px;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="border-top: 1px solid #e0e4f0;"></td>
                    </tr>

                    @include('admin.emails.partials.admin_footer')

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
