<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Address\StoreAddressRequest;
use App\Http\Requests\Client\Address\UpdateAddressRequest;
use App\Http\Requests\Client\Address\DestroyAddressRequest;
use App\Repositories\Client\Interfaces\AddressRepositoryInterface as AddressRepo;
use App\Models\city as CityModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        // show addresses page (pass user addresses)
        $addresses = collect([]);
        $shipping = collect([]);
        $billing = collect([]);
        $cities = collect([]);

        if (Auth::check()) {
            $addressRepo = app(AddressRepo::class);
            $addresses = $addressRepo->getAddressesByUserId(Auth::id()) ?: collect([]);
            $cities = $addressRepo->getActiveCities() ?: collect([]);

            // Normalize to collection
            if (! ($addresses instanceof \Illuminate\Support\Collection)) {
                $addresses = collect($addresses);
            }

            if (! ($cities instanceof \Illuminate\Support\Collection)) {
                $cities = collect($cities);
            }

            // Prepare sorted lists: default addresses first, then newest (id desc)
            $prepare = function($col, $type) {
                $arr = $col->where('type', $type)->values()->all();
                usort($arr, function($a, $b){
                    $da = !empty($a->is_default) ? 1 : 0;
                    $db = !empty($b->is_default) ? 1 : 0;
                    if ($da === $db) {
                        return (($b->id ?? 0) <=> ($a->id ?? 0));
                    }
                    return ($db <=> $da);
                });
                return collect($arr);
            };

            $shipping = $prepare($addresses, 'shipping');
            $billing = $prepare($addresses, 'billing');
        }

        return view('client.pages.dashboard.addresses', [
            'addresses' => $addresses,
            'shipping' => $shipping,
            'billing' => $billing,
            'cities' => $cities,
        ]);
    }

    public function store(StoreAddressRequest $request, AddressRepo $addressRepository)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        if (!empty($data['city_id'])) {
            $cityModel = CityModel::find($data['city_id']);
            $data['city'] = $cityModel ? $cityModel->getTranslation('city_name', app()->getLocale()) : '';
        }

        $address = $addressRepository->create($data);

        if (!empty($data['is_default'])) {
            $addressRepository->setDefaultAddress(Auth::id(), $address->id);
            $address = $addressRepository->findById($address->id);
        }

        // return server-rendered HTML for AJAX
        if ($request->expectsJson()) {
            $html = view('client.pages.dashboard._address_card', ['address' => $address])->render();
            return response()->json(['data' => $address, 'html' => $html], 201);
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

        if (!empty($data['city_id'])) {
            $cityModel = CityModel::find($data['city_id']);
            $data['city'] = $cityModel ? $cityModel->getTranslation('city_name', app()->getLocale()) : '';
        }

        $address = $addressRepository->update((int) $id, $data);

        if (!empty($data['is_default'])) {
            $addressRepository->setDefaultAddress(Auth::id(), (int) $id);
            $address = $addressRepository->findById((int) $id);
        }

        if ($request->expectsJson()) {
            $html = view('client.pages.dashboard._address_card', ['address' => $address])->render();
            return response()->json(['data' => $address, 'html' => $html]);
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

    public function setDefault(Request $request, AddressRepo $addressRepository, $id = null)
    {
        $id = $id ?? $request->input('id') ?? $request->route('id');
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

    /**
     * Return a single address for AJAX edit forms.
     */
    public function show(Request $request, AddressRepo $addressRepository, $id = null)
    {
        $id = $id ?? $request->input('id') ?? $request->route('id');
        $address = $addressRepository->findById((int) $id);
        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        return response()->json(['data' => $address]);
    }
}
