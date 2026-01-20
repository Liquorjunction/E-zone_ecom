<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AdminHelper
{
    /**
     * Get the authenticated admin user
     *
    * @return \App\Models\Admin|null
     */
    public static function user()
    {
        return Auth::guard('admin')->user();
    }

    /**
     * Check if admin is authenticated
     *
     * @return bool
     */
    public static function check()
    {
        return Auth::guard('admin')->check();
    }

    /**
     * Get admin user id
     *
     * @return int|null
     */
    public static function id()
    {
        return Auth::guard('admin')->id();
    }

    /**
     * Get admin user name
     *
     * @return string|null
     */
    public static function name()
    {
        $user = self::user();
        return $user ? $user->name : null;
    }

    /**
     * Get admin user email
     *
     * @return string|null
     */
    public static function email()
    {
        $user = self::user();
        return $user ? $user->email : null;
    }

    /**
     * Get admin user role
     *
     * @return string|null
     */
    public static function role()
    {
        $user = self::user();
        return $user ? $user->getRoleNames()->first() : null;
    }

    /**
     * Get admin profile image
     *
     * @return string|null
     */
    public static function profileImage()
    {
        $user = self::user();
        return $user ? $user->profile_image : null;
    }

    /**
     * Get admin profile image URL
     *
     * @return string
     */
    public static function profileImageUrl()
    {
        $image = self::profileImage();
        return $image ? asset('storage/avatars/' . $image) : asset('admin_assets/images/avatars/01.png');
    }
}
