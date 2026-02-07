<?php

namespace App\Repositories\Admin\Interfaces;

interface SiteSettingsRepositoryInterface
{
    public function getFirstSiteSetting();
    public function getSiteSettingById($id);
    public function updateSiteSetting($id, array $data);
    public function createSiteSetting(array $data);
    public function updateSiteStatus($id, $status);
}
