<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteSetting\StoreSiteSettingRequest;
use App\Http\Requests\Admin\SiteSetting\UpdateSiteSettingRequest;
use App\Repositories\Admin\Interfaces\SiteSettingsRepositoryInterface as SiteSettingsRepo;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function index(SiteSettingsRepo $siteSettingsRepository)
    {
        $siteSetting = $siteSettingsRepository->getFirstSiteSetting();
        return view('admin.pages.site-settings.index', compact('siteSetting'));
    }

    public function store(StoreSiteSettingRequest $request, SiteSettingsRepo $siteSettingsRepository)
    {
        try {
            $data = $request->validated();

            $data['site_status'] = $data['site_status'] ?? 'active';

            $siteSetting = $siteSettingsRepository->createSiteSetting($data);

            return response()->json([
                'success' => true,
                'message' => 'Site settings created successfully',
                'data' => $siteSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create site settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateSiteSettingRequest $request, SiteSettingsRepo $siteSettingsRepository)
    {
        try {
            $id = $request->query('id');
            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site setting ID is required'
                ], 400);
            }

            $data = $request->validated();
            $siteSetting = $siteSettingsRepository->updateSiteSetting($id, $data);

            if (!$siteSetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site settings not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Site settings updated successfully',
                'data' => $siteSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update site settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, SiteSettingsRepo $siteSettingsRepository)
    {
        try {
            $id = $request->query('id');
            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site setting ID is required'
                ], 400);
            }

            $request->validate([
                'status' => 'required|in:active,inactive'
            ]);

            $siteSetting = $siteSettingsRepository->updateSiteStatus($id, $request->status);

            if (!$siteSetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site settings not found'
                ], 404);
            }

            $message = $request->status === 'active'
                ? 'Site is now active and accessible to users'
                : 'Site is now in maintenance mode';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'site_status' => $siteSetting->site_status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update site status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStatus(SiteSettingsRepo $siteSettingsRepository)
    {
        try {
            $siteSetting = $siteSettingsRepository->getFirstSiteSetting();

            return response()->json([
                'success' => true,
                'data' => [
                    'site_status' => $siteSetting ? $siteSetting->site_status : 'active',
                    'message' => $siteSetting && $siteSetting->site_status === 'inactive'
                        ? 'Site is currently in maintenance mode'
                        : 'Site is active'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get site status: ' . $e->getMessage()
            ], 500);
        }
    }
}
