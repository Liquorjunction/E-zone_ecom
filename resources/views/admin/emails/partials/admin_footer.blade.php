<!-- Admin Footer -->
<tr>
    <td style="padding: 24px 35px 28px 35px; background-color: #f8f9fe; border-radius: 0 0 12px 12px;">

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 16px 0;">
            <tr>
                <td style="border-top: 1px solid #e0e4f0;"></td>
            </tr>
        </table>

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <!-- Left: branding -->
                <td valign="middle">
                    <p style="margin: 0; padding: 0; color: #1a1a2e; font-size: 13px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">{{ config('app.name') }}</p>
                    <p style="margin: 2px 0 0 0; padding: 0; color: #9ca3af; font-size: 11px; font-family: Arial, Helvetica, sans-serif;">Admin Notification System</p>
                </td>
                <!-- Right: timestamp -->
                <td valign="middle" align="right">
                    <p style="margin: 0; padding: 0; color: #9ca3af; font-size: 11px; font-family: Arial, Helvetica, sans-serif;">{{ now()->format('d M Y, h:i A') }}</p>
                    <p style="margin: 2px 0 0 0; padding: 0; color: #9ca3af; font-size: 11px; font-family: Arial, Helvetica, sans-serif;">Server Time</p>
                </td>
            </tr>
        </table>
    </td>
</tr>
