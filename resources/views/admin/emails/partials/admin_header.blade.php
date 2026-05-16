<!-- Admin Header -->
<tr>
    @php
        $imgpath = config('app.url') . '/icon/Ezone-Logo-New-2025-Website.png';
    @endphp
    <td style="background: linear-gradient(135deg, #3b4fd8 0%, #4361ee 50%, #3a0ca3 100%); background-color: #4361ee; padding: 32px 35px; border-radius: 12px 12px 0 0; border-bottom: 4px solid #4cc9f0;">

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <!-- Logo -->
                <td valign="middle" width="52">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td style="background-color: #ffffff; padding: 8px 12px; border-radius: 8px;">
                                <img src="{{ $imgpath }}" alt="{{ config('app.name') }}" width="90"
                                    style="max-width: 90px; height: auto; display: block; border: 0;">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Title -->
        <p style="margin: 24px 0 6px 0; padding: 0; color: #ffffff; font-size: 24px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif; letter-spacing: -0.3px; text-align: left;">@yield('header_title')</p>
        <p style="margin: 0; padding: 0; color: #a5b4fc; font-size: 13px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif; text-align: left;">@yield('header_subtitle')</p>

    </td>
</tr>
