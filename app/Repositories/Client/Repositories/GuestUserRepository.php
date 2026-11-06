<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Address;
use App\Models\GuestUser;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Client\Interfaces\GuestUserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GuestUserRepository implements GuestUserRepositoryInterface
{
    public function findByEmail(string $email): ?GuestUser
    {
        return GuestUser::where('email', strtolower(trim($email)))->first();
    }

    public function findByToken(string $token): ?GuestUser
    {
        return GuestUser::where('token', trim($token))->first();
    }

    public function findById(int $id): ?GuestUser
    {
        return GuestUser::find($id);
    }

    public function createOrUpdateGuest(array $data): GuestUser
    {
        $normalizedEmail = strtolower(trim($data['email'] ?? ''));

        $guest = GuestUser::where('email', $normalizedEmail)->first();

        if ($guest) {
            $guest->name = trim($data['name'] ?? $guest->name);
            if (isset($data['phone'])) {
                $guest->phone = trim($data['phone']);
            }
            if (empty($guest->token)) {
                $guest->token = Str::random(40);
            }
            $guest->increment('orders_count');
            $guest->update(['last_order_at' => now()]);
        } else {
            $guest = GuestUser::create([
                'email'         => $normalizedEmail,
                'name'          => trim($data['name'] ?? ''),
                'phone'         => isset($data['phone']) ? trim($data['phone']) : null,
                'token'         => Str::random(40),
                'orders_count'  => 1,
                'last_order_at' => now(),
            ]);
        }

        return $guest->fresh();
    }

    public function linkGuestOrdersToUser(User $user): int
    {
        // Strict security rule: email is the ONLY verified identity.
        // Orders are linked only if the user's email has been verified.
        if (empty($user->email_verified_at)) {
            Log::info('Skipped guest order linking: email is not verified.', ['user_id' => $user->id, 'email' => $user->email]);
            return 0;
        }

        $normalizedEmail = strtolower(trim($user->email));

        return DB::transaction(function () use ($user, $normalizedEmail) {
            $guest = GuestUser::where('email', $normalizedEmail)->first();

            if ($guest) {
                $guest->update([
                    'registered_user_id' => $user->id,
                ]);
            }

            // Find all unlinked guest orders matching this guest user or this verified email
            $ordersToLink = Order::where(function ($q) use ($guest, $normalizedEmail) {
                if ($guest) {
                    $q->where('guest_user_id', $guest->id);
                }
                $q->orWhere(function ($sub) use ($normalizedEmail) {
                    $sub->whereNull('user_id')
                        ->where('client_type', 'guest')
                        ->whereRaw('LOWER(email) = ?', [$normalizedEmail]);
                });
            })
            ->whereNull('user_id')
            ->get();

            if ($ordersToLink->isEmpty()) {
                return 0;
            }

            $orderIds = $ordersToLink->pluck('id');

            // Update orders to point to registered user
            Order::whereIn('id', $orderIds)->update([
                'user_id'     => $user->id,
                'client_type' => 'registered',
            ]);

            // Also link the addresses associated with these orders
            $shippingAddressIds = $ordersToLink->pluck('shipping_address_id')->filter();
            $billingAddressIds  = $ordersToLink->pluck('billing_address_id')->filter();
            $addressIds         = $shippingAddressIds->merge($billingAddressIds)->unique();

            if ($addressIds->isNotEmpty()) {
                Address::whereIn('id', $addressIds)
                    ->whereNull('user_id')
                    ->update(['user_id' => $user->id]);
            }

            // Also link any coupon usages from these guest orders
            \App\Models\CouponUsage::whereIn('order_id', $orderIds)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);

            Log::info('Successfully linked guest orders to registered user.', [
                'user_id'      => $user->id,
                'email'        => $normalizedEmail,
                'orders_count' => $orderIds->count(),
                'order_ids'    => $orderIds->toArray(),
            ]);

            return $orderIds->count();
        });
    }
}
