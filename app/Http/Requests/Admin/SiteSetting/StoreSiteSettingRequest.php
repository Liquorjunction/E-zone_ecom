<?php

namespace App\Http\Requests\Admin\SiteSetting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSiteSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('site_status.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'infotext' => 'nullable|array',
            'infotext.text' => 'nullable|array',
            'infotext.text.en' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (! is_string($value)) {
                        return;
                    }

                    $trimmed = trim($value);
                    if ($trimmed === '') {
                        return;
                    }

                    $wordCount = preg_match_all('/\S+/u', $trimmed);
                    if ($wordCount > 30) {
                        $fail('The English info text may not be greater than 30 words.');
                    }
                },
            ],
            'infotext.text.fr' => 'nullable|string',
            'infotext.text.ar' => 'nullable|string',
            'infotext.url' => 'nullable|url|max:1000',
            'site_status' => 'nullable|in:active,inactive',
        ];
    }

    protected function prepareForValidation()
    {
        $rawInfoText = $this->input('infotext', $this->input('infotext[]'));

        $infotext = [
            'text' => ['en' => '', 'fr' => '', 'ar' => ''],
            'url' => '',
        ];

        if (is_string($rawInfoText)) {
            $infotext['text']['en'] = trim($rawInfoText);
        } elseif (is_array($rawInfoText)) {
            if (array_key_exists('text', $rawInfoText) || array_key_exists('url', $rawInfoText)) {
                $rawText = $rawInfoText['text'] ?? '';

                if (is_array($rawText)) {
                    $infotext['text']['en'] = trim((string) ($rawText['en'] ?? ''));
                    $infotext['text']['fr'] = trim((string) ($rawText['fr'] ?? ''));
                    $infotext['text']['ar'] = trim((string) ($rawText['ar'] ?? ''));
                } elseif (is_string($rawText)) {
                    $infotext['text']['en'] = trim($rawText);
                }

                $infotext['url'] = trim((string) ($rawInfoText['url'] ?? ''));
            } else {
                foreach ($rawInfoText as $value) {
                    if (is_string($value) && trim($value) !== '') {
                        $infotext['text']['en'] = trim($value);
                        break;
                    }
                }
            }
        }

        $hasAnyText = $infotext['text']['en'] !== '' || $infotext['text']['fr'] !== '' || $infotext['text']['ar'] !== '';
        if (! $hasAnyText && $infotext['url'] === '') {
            $infotext = [];
        }

        $this->merge([
            'infotext' => $infotext,
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The site name is required.',
            'name.string' => 'The site name must be a string.',
            'name.max' => 'The site name may not be greater than 255 characters.',
            'phone.string' => 'The phone number must be a string.',
            'phone.max' => 'The phone number may not be greater than 20 characters.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address may not be greater than 500 characters.',
            'facebook.url' => 'The facebook url must be a valid url.',
            'facebook.max' => 'The facebook url may not be greater than 255 characters.',
            'twitter.url' => 'The twitter url must be a valid url.',
            'twitter.max' => 'The twitter url may not be greater than 255 characters.',
            'instagram.url' => 'The instagram url must be a valid url.',
            'instagram.max' => 'The instagram url may not be greater than 255 characters.',
            'infotext.text.array' => 'The info text translations must be a valid structure.',
            'infotext.text.en.string' => 'The English info text must be a string.',
            'infotext.text.fr.string' => 'The French info text must be a string.',
            'infotext.text.ar.string' => 'The Arabic info text must be a string.',
            'infotext.url.url' => 'The info text URL must be a valid URL.',
            'infotext.url.max' => 'The info text URL may not be greater than 1000 characters.',
            'site_status.in' => 'The site status must be active or inactive.',
        ];
    }
}
