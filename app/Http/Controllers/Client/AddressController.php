<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Address\StoreAddressRequest;
use App\Http\Requests\Client\Address\UpdateAddressRequest;
use App\Http\Requests\Client\Address\DestroyAddressRequest;
use App\Repositories\Client\Interfaces\AddressRepositoryInterface as AddressRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        // show addresses page (pass user addresses)
        $addresses = [];
        if (Auth::check()) {
            $addresses = app(AddressRepo::class)->getAddressesByUserId(Auth::id());
        }
        return view('client.pages.dashboard.addresses', ['addresses' => $addresses]);
    }

    public function store(StoreAddressRequest $request, AddressRepo $addressRepository)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $address = $addressRepository->create($data);

        if (!empty($data['is_default'])) {
            $addressRepository->setDefaultAddress(Auth::id(), $address->id);
            $address = $addressRepository->findById($address->id);
        }

        if ($request->expectsJson()) {
            return response()->json(['data' => $address], 201);
        }

        return redirect()->back()->with('success', 'Address added successfully.');
    }

    public function update(UpdateAddressRequest $request, AddressRepo $addressRepository)
    {
        $id = $request->input('id') ?? $request->address_id ?? null;
        if (!$id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Address id missing'], 422);
            }
            return redirect()->route('dashboard.addresses')->with('error', 'Address id missing');
        }

        $data = $request->validated();
        $address = $addressRepository->update((int) $id, $data);

        if (!empty($data['is_default'])) {
            $addressRepository->setDefaultAddress(Auth::id(), (int) $id);
            $address = $addressRepository->findById((int) $id);
        }

        if ($request->expectsJson()) {
            return response()->json(['data' => $address]);
        }

        return redirect()->route('dashboard.addresses')->with('success', 'Address updated successfully.');
    }

    public function destroy(DestroyAddressRequest $request, AddressRepo $addressRepository, $id = null)
    {
        $id = $id ?? $request->input('id') ?? $request->route('id');
        if (!$id) {
            return response()->json(['message' => 'Address id missing'], 422);
        }

        $deleted = $addressRepository->delete((int) $id);
        if ($request->expectsJson()) {
            return response()->json(['success' => $deleted]);
        }

        if ($deleted) {
            return redirect()->route('dashboard.addresses')->with('success', 'Address deleted');
        }

        return redirect()->route('dashboard.addresses')->with('error', 'Failed to delete address');
    }

    public function setDefault(Request $request, AddressRepo $addressRepository, $id)
    {
        $userId = Auth::id();
        if (!$id) {
            return response()->json(['message' => 'Address id missing'], 422);
        }

        $address = $addressRepository->findById((int) $id);
        if (!$address || ($address->user_id ?? null) != $userId) {
            return response()->json(['message' => 'Address not found or unauthorized'], 404);
        }

        $addressRepository->setDefaultAddress($userId, (int) $id);

        $address = $addressRepository->findById((int) $id);

        if ($request->expectsJson()) {
            return response()->json(['data' => $address]);
        }

        return redirect()->route('dashboard.addresses')->with('success', 'Default address updated.');
    }
}
