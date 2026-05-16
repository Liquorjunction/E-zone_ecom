<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->select(['id', 'user_id', 'shipping_address', 'billing_address', 'shipping_address_id', 'billing_address_id'])
            ->orderBy('id')
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    $updates = [];

                    if (empty($order->shipping_address_id)) {
                        $shippingId = $this->resolveAddressId($order->shipping_address, $order->user_id, 'shipping');
                        if ($shippingId) {
                            $updates['shipping_address_id'] = $shippingId;
                        }
                    }

                    if (empty($order->billing_address_id)) {
                        $billingId = $this->resolveAddressId($order->billing_address, $order->user_id, 'billing');
                        if ($billingId) {
                            $updates['billing_address_id'] = $billingId;
                        }
                    }

                    if (!empty($updates)) {
                        DB::table('orders')->where('id', $order->id)->update($updates);
                    }
                }
            });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_address', 'billing_address']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->json('shipping_address')->nullable()->after('shipping_address_id');
            $table->json('billing_address')->nullable()->after('billing_address_id');
        });

        DB::table('orders')
            ->select(['id', 'shipping_address_id', 'billing_address_id'])
            ->orderBy('id')
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    $updates = [];

                    if (!empty($order->shipping_address_id)) {
                        $shipping = DB::table('addresses')->where('id', $order->shipping_address_id)->first();
                        if ($shipping) {
                            $updates['shipping_address'] = json_encode($this->addressRecordToArray($shipping));
                        }
                    }

                    if (!empty($order->billing_address_id)) {
                        $billing = DB::table('addresses')->where('id', $order->billing_address_id)->first();
                        if ($billing) {
                            $updates['billing_address'] = json_encode($this->addressRecordToArray($billing));
                        }
                    }

                    if (!empty($updates)) {
                        DB::table('orders')->where('id', $order->id)->update($updates);
                    }
                }
            });
    }

    private function resolveAddressId($rawAddress, $userId, string $type): ?int
    {
        $address = $this->decodeAddress($rawAddress);
        if (empty($address)) {
            return null;
        }

        $addressColumns = array_flip(Schema::getColumnListing('addresses'));

        if (!empty($address['id'])) {
            $existing = DB::table('addresses')->where('id', (int) $address['id'])->first();
            if ($existing) {
                return (int) $existing->id;
            }
        }

        $matchQuery = DB::table('addresses')->where('type', $type);
        if ($userId) {
            $matchQuery->where('user_id', $userId);
        } else {
            $matchQuery->whereNull('user_id');
        }

        foreach (['name', 'phone', 'street', 'city', 'state', 'postal_code', 'country'] as $field) {
            if (!isset($addressColumns[$field])) {
                continue;
            }
            $value = $address[$field] ?? null;
            if ($value === null || $value === '') {
                $matchQuery->whereNull($field);
            } else {
                $matchQuery->where($field, $value);
            }
        }

        $existing = $matchQuery->first();
        if ($existing) {
            return (int) $existing->id;
        }

        $now = Carbon::now();

        $insert = [
            'type' => $type,
            'is_default' => false,
            'user_id' => $userId ?: null,
            'address_label' => ucfirst($type),
            'name' => $address['name'] ?? '',
            'phone' => $address['phone'] ?? null,
            'street' => $address['street'] ?? '',
            'city' => $address['city'] ?? '',
            'city_id' => $address['city_id'] ?? null,
            'state' => $address['state'] ?? null,
            'postal_code' => $address['postal_code'] ?? null,
            'country' => $address['country'] ?? '',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $insert = array_intersect_key($insert, $addressColumns);

        return (int) DB::table('addresses')->insertGetId($insert);
    }

    private function decodeAddress($rawAddress): array
    {
        if (is_array($rawAddress)) {
            return $rawAddress;
        }

        if (empty($rawAddress)) {
            return [];
        }

        $decoded = json_decode($rawAddress, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function addressRecordToArray(object $address): array
    {
        return [
            'id' => $address->id,
            'user_id' => $address->user_id,
            'type' => $address->type,
            'is_default' => (bool) ($address->is_default ?? false),
            'address_label' => $address->address_label,
            'name' => $address->name,
            'phone' => $address->phone,
            'street' => $address->street,
            'city' => $address->city,
            'city_id' => $address->city_id,
            'state' => $address->state,
            'postal_code' => $address->postal_code,
            'country' => $address->country,
        ];
    }
};
