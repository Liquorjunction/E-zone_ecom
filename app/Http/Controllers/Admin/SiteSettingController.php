<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteSetting\AddMapSiteSettingRequest;
use App\Http\Requests\Admin\SiteSetting\AddOpeningHoursSiteSettingRequest;
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
            // Ensure infotext is always an array
            if (isset($data['infotext']) && !is_array($data['infotext'])) {
                $data['infotext'] = [$data['infotext']];
            }
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
            // Ensure infotext is always an array
            if (isset($data['infotext']) && !is_array($data['infotext'])) {
                $data['infotext'] = [$data['infotext']];
            }
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

    public function AddOpeningHours(AddOpeningHoursSiteSettingRequest $request, SiteSettingsRepo $siteSettingsRepository)
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
            $siteSetting = $siteSettingsRepository->updateSiteSetting($id, ['opening_hours' => $data['opening_hours']]);

            if (!$siteSetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site settings not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Opening hours updated successfully',
                'data' => $siteSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update opening hours: ' . $e->getMessage()
            ], 500);
        }
    }

     public function AddMap(AddMapSiteSettingRequest $request, SiteSettingsRepo $siteSettingsRepository)
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
            $siteSetting = $siteSettingsRepository->updateSiteSetting($id, ['map_iframe' => $data['map_iframe']]);

            if (!$siteSetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site settings not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Map iframe updated successfully',
                'data' => $siteSetting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update map iframe: ' . $e->getMessage()
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
