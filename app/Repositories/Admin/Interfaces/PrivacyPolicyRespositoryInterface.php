<?php

namespace App\Repositories\Admin\Interfaces;

interface PrivacyPolicyRespositoryInterface
{
    public function getPrivacyPolicy();
    public function getActivePrivacyPolicy();
    public function storePrivacyPolicy($request);
    public function updatePrivacyPolicy($request, $id);
    public function destroyPrivacyPolicy($id);
    public function updatePrivacyPolicyStatus($id);
    public function getDataForDataTable();
}
