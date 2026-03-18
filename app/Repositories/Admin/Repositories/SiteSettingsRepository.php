<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\SiteSetting;
use App\Repositories\Admin\Interfaces\SiteSettingsRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class SiteSettingsRepository implements SiteSettingsRepositoryInterface
{
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
            // Ensure infotext is always an array
            if (isset($data['infotext']) && !is_array($data['infotext'])) {
                $data['infotext'] = [$data['infotext']];
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
        // Ensure infotext is always an array
        if (isset($data['infotext']) && !is_array($data['infotext'])) {
            $data['infotext'] = [$data['infotext']];
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
