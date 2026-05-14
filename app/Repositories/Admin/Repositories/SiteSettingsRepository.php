<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\SiteSetting;
use App\Repositories\Admin\Interfaces\SiteSettingsRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class SiteSettingsRepository implements SiteSettingsRepositoryInterface
{
    private function normalizeInfoText($rawInfoText): array
    {
        $infotext = ['text' => '', 'url' => ''];

        if (is_string($rawInfoText)) {
            $infotext['text'] = trim($rawInfoText);
        } elseif (is_array($rawInfoText)) {
            if (array_key_exists('text', $rawInfoText) || array_key_exists('url', $rawInfoText)) {
                $infotext['text'] = trim((string) ($rawInfoText['text'] ?? ''));
                $infotext['url'] = trim((string) ($rawInfoText['url'] ?? ''));
            } else {
                foreach ($rawInfoText as $value) {
                    if (is_string($value) && trim($value) !== '') {
                        $infotext['text'] = trim($value);
                        break;
                    }
                }
            }
        }

        return ($infotext['text'] === '' && $infotext['url'] === '') ? [] : $infotext;
    }

    public function getFirstSiteSetting()
    {
        return SiteSetting::first();
    }

    public function getSiteSettingById($id)
    {
        return SiteSetting::find($id);
    }

    public function updateSiteSetting($id, array $data)
    {
        $siteSetting = SiteSetting::find($id);
        if ($siteSetting) {
            if (array_key_exists('infotext', $data)) {
                $data['infotext'] = $this->normalizeInfoText($data['infotext']);
            }
            $siteSetting->update($data);

            Cache::forget('site_status');
            Cache::forget('site_settings');

            return $siteSetting;
        }
        return null;
    }

    public function createSiteSetting(array $data)
    {
        if (array_key_exists('infotext', $data)) {
            $data['infotext'] = $this->normalizeInfoText($data['infotext']);
        }
        $setting = SiteSetting::create($data);

        Cache::forget('site_status');
        Cache::forget('site_settings');

        return $setting;
    }

    public function updateSiteStatus($id, $status)
    {
        $siteSetting = SiteSetting::find($id);
        if ($siteSetting) {
            $siteSetting->site_status = $status;
            $siteSetting->save();

            Cache::forget('site_status');
            Cache::forget('site_settings');

            return $siteSetting;
        }
        return null;
    }
}
