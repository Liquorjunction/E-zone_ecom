<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Address\ReadAddressRequest;
use App\Http\Requests\Client\Address\StoreAddressRequest;
use App\Http\Requests\Client\Address\UpdateAddressRequest;
use App\Http\Requests\Client\Address\DestroyAddressRequest;
use App\Http\Requests\Client\Address\EditAddressRequest;
use App\Repositories\Client\Interfaces\AddressRepositoryInterface as AddressRepo;

class AddressController extends Controller
{
    public function index()
    {
        return view('client.pages.dashboard.addresses');
    }

    public function store(StoreAddressRequest $request, AddressRepo $addressRepository)
    {
        $addressRepository->create($request->validated());
        return redirect()->back()->with('success', 'Address added successfully.');
    }

    public function update(UpdateAddressRequest $request, AddressRepo $addressRepository)
    {
        $addressRepository->update($request->address_id, $request->validated());
        return redirect()->route('dashboard.addresses')->with('success', 'Address updated successfully.');
    }
}
