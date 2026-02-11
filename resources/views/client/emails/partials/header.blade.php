<!-- Header Section -->
<tr>
    @php
        $imgpath = config('app.url') . '/icon/Ezone-Logo-New-2025-Website.png';
    @endphp
    <td
        style="background:linear-gradient(135deg,#1a3a52 0%,#0d1f2d 100%);background-color:#1a3a52;padding:40px 30px;text-align:center;border-radius:12px 12px 0 0;border-bottom:4px solid #00bfff">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center"
            style="margin:0 auto 24px auto">
            <tbody>
                <tr>
                    <td style="background-color:#ffffff;padding:16px 32px;border-radius:12px">
                        <img src="{{ $imgpath }}" alt="{{ config('app.name') }}" width="200"
                            style="max-width: 200px; height: auto; display: block; margin: 0 auto; border: 0;">
                    </td>
                </tr>
            </tbody>
        </table>
        <h1
            style="margin: 24px 0 8px 0; padding: 0; color: #ffffff; font-size: 28px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif; letter-spacing: -0.5px;">
            @yield('header_title')</h1>
        <p
            style="margin: 0; padding: 0; color: #00BFFF; font-size: 14px; font-weight: 600; line-height: 1.5; letter-spacing: 0.5px; text-transform: uppercase;">
            @yield('header_subtitle')</p>
    </td>
</tr>
