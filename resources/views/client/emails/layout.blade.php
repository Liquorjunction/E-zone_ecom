<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f5f7fa; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <!-- Outer Table for Background -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="margin: 0; padding: 0; background-color: #f5f7fa;">
        <tr>
            <td style="padding: 40px 20px;">

                <!-- Main Container Table -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" align="center"
                    style="margin: 0 auto; max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 164, 214, 0.08); border: 1px solid #e5e7eb;">

                    @include('client.emails.partials.header')

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 40px 35px;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="border-top: 1px solid #e5e7eb;"></td>
                    </tr>

                    @include('client.emails.partials.footer')

                </table>
                <!-- End Main Container -->

            </td>
        </tr>
    </table>
    <!-- End Outer Table -->
</body>

</html>
