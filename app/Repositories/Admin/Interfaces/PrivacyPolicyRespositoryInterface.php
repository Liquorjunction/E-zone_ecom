<?php

namespace App\Repositories\Admin\Interfaces;

interface PrivacyPolicyRespositoryInterface
{
    public function getPrivacyPolicy();
    public function storePrivacyPolicy($request);
    public function updatePrivacyPolicy($request, $id);
    public function destroyPrivacyPolicy($request);
    public function getDataForDataTable();
}
