<!-- Footer Section -->
<tr>
    <td
        style="padding: 32px 35px; background-color: #f8fafb; text-align: center; border-radius: 0 0 12px 12px;">

        <!-- Company Name -->
        <p
            style="margin: 0 0 16px 0; padding: 0; color: #1a1a1a; font-size: 16px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">
            {{ config('app.name') }}
        </p>

        <!-- Tagline -->
        <p
            style="margin: 0 0 20px 0; padding: 0; color: #999999; font-size: 12px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
            Best Deals Ever
        </p>

        <!-- Trust Badges -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center"
            style="margin: 0 auto 24px auto;">
            <tr>
                <td style="padding: 0 12px;">
                    <p
                        style="margin: 0; padding: 0; color: #666666; font-size: 13px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                        Cryptage sécurisé</p>
                </td>
                <td style="padding: 0 12px;">
                    <p
                        style="margin: 0; padding: 0; color: #666666; font-size: 13px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                        Protection de la vie privée</p>
                </td>
            </tr>
        </table>

        <!-- Divider -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
            style="margin: 0 0 20px 0;">
            <tr>
                <td style="border-top: 1px solid #e5e7eb;"></td>
            </tr>
        </table>

        <!-- Social Media Links -->
        @php
            $socialMedia = [];
            $fbicon = config('app.url') . '/icon/fb.png';
            $instaicon = config('app.url') . '/icon/insta.png';

            if (!empty($siteContent->facebook)) {
                $socialMedia['facebook'] = [
                    'url' => $siteContent->facebook,
                    'icon' => '<img src="' . $fbicon . '" alt="Facebook" width="20" height="20">'
                ];
            }

            if (!empty($siteContent->instagram)) {
                $socialMedia['instagram'] = [
                    'url' => $siteContent->instagram,
                    'icon' => '<img src="' . $instaicon . '" alt="Instagram" width="20" height="20">'
                ];
            }

            if (!empty($siteContent->twitter)) {
                $twittericon = config('app.url') . '/icon/twitter.png';
                $socialMedia['twitter'] = [
                    'url' => $siteContent->twitter,
                    'icon' => '<img src="' . $twittericon . '" alt="Twitter" width="20" height="20">'
                ];
            }
        @endphp
        @if (count($socialMedia) > 0)
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center"
                style="margin: 0 auto 24px auto;">
                <tr>
                    @foreach ($socialMedia as $platform => $data)
                        <td style="padding: 0 12px;">
                            <a href="{{ $data['url'] }}" target="_blank"
                                title="{{ ucfirst($platform) }}"
                                style="color: #00BFFF; text-decoration: none; display: inline-block; line-height: 0;">
                                {!! $data['icon'] !!}
                            </a>
                        </td>
                    @endforeach
                </tr>
            </table>
        @endif

        <!-- Footer Links -->
        <p
            style="margin: 14px 0; padding: 0; color: #777777; font-size: 12px; line-height: 1.8; font-family: Arial, Helvetica, sans-serif;">
            <a href="{{ route('privacy-policy') }}"
                style="color: #00BFFF; text-decoration: none; font-weight: 600;">Politique
                de confidentialité</a>
            &nbsp;•&nbsp;
            <a href="{{ route('terms') }}"
                style="color: #00BFFF; text-decoration: none; font-weight: 600;">Conditions
                d'utilisation</a>
            &nbsp;•&nbsp;
            <a href="{{ route('contact') }}"
                style="color: #00BFFF; text-decoration: none; font-weight: 600;">Contacter
                l'assistance</a>
        </p>

        <!-- Copyright -->
        <p
            style="margin: 14px 0 0 0; padding: 0; color: #999999; font-size: 11px; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
            © {{ now()->year }} {{ config('app.name') }}. Tous droits réservés.
            <br>
            Ceci est un message automatique. Veuillez ne pas répondre à ce courriel.
        </p>

    </td>
</tr>
