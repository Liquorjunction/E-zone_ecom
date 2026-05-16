@extends('client.emails.layout')

@section('title', config('app.name') . ' - Compte Créé')
@section('header_title', 'Bienvenue sur ' . config('app.name'))
@section('header_subtitle', 'Votre compte a été créé avec succès')

@section('content')
    <!-- Greeting -->
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 22px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Hello {{ $user->name }},</p>

    <!-- Main Message -->
    <p style="margin: 0 0 16px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
       Nous sommes ravis de vous compter parmi nous ! Votre compte a été créé avec succès sur<strong>{{ config('app.name') }}</strong>. Il vous suffit d'un clic pour découvrir nos produits et passer votre première commande.
    </p>

    <!-- CTA Button -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="border-radius: 8px; background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; box-shadow: 0 4px 12px rgba(0, 191, 255, 0.3);">
                            <a href="{{ $loginUrl ?? url('/login') }}" target="_blank" style="display: inline-block; padding: 16px 48px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 700; font-family: Arial, Helvetica, sans-serif; border-radius: 8px; letter-spacing: 0.5px;">Connectez-vous à votre compte</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- What You Can Do Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 28px 0; background-color: #f0f9ff; border: 2px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 24px;">

                <p style="margin: 0 0 18px 0; padding: 0; color: #1a3a52; font-size: 15px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Que pouvez-vous faire maintenant ?</p>

                <!-- Item 1 -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 14px;">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; color: #ffffff; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">✓</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Parcourez et achetez parmi notre large gamme de produits</p>
                        </td>
                    </tr>
                </table>

                <!-- Item 2 -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 14px;">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; color: #ffffff; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">✓</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Suivez vos commandes et livraisons en temps réel</p>
                        </td>
                    </tr>
                </table>

                <!-- Item 3 -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; color: #ffffff; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">✓</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Gérez votre profil et vos préférences depuis votre tableau de bord</p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

    <!-- Security Warning Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0 0 0; background-color: #fff8f0; border-left: 4px solid #ff9800; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 18px;">
                <p style="margin: 0; padding: 0; color: #8b5a00; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>Rappel de sécurité :</strong> Si vous n'avez pas créé ce compte, veuillez contacter immédiatement notre équipe d'assistance pour sécuriser vos informations.
                </p>
            </td>
        </tr>
    </table>
@endsection
