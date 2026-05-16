<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Card;
use App\Repositories\Admin\Interfaces\CardRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class CardRepository implements CardRepositoryInterface
{
    public function getAllCards()
    {
        return Card::all();
    }

    public function getActiveCards()
    {
        return Card::where('status', 'active')->get();
    }

    public function getCardById($id)
    {
        return Card::find($id);
    }

    public function storeCard($request, $data = null)
    {
        $data = $data ?? $request->validated();
        $data['status'] = $data['status'] ?? 'inactive';

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('cards', 'public');
            $data['image'] = $imagePath;
        }

        return Card::create($data);
    }

    public function updateCard($request, $id, $data = null)
    {
        $card = Card::findOrFail($id);
        $data = $data ?? $request->validated();

        if ($request->hasFile('image')) {
            if ($card->image && Storage::disk('public')->exists($card->image)) {
                Storage::disk('public')->delete($card->image);
            }

            $path = $request->file('image')->store('cards', 'public');
            $data['image'] = $path;
        }

        $card->update($data);

        return $card;
    }

    public function deleteCard($id)
    {
        $card = Card::findOrFail($id);

        if ($card->image && Storage::disk('public')->exists($card->image)) {
            Storage::disk('public')->delete($card->image);
        }

        return $card->delete();
    }

    public function updateStatus($id)
    {
        $card = Card::findOrFail($id);
        $card->status = ($card->status === 'active') ? 'inactive' : 'active';
        $card->save();

        return $card;
    }

    public function getDataForDataTable($request)
    {
        $query = Card::query()->latest();

        $status = is_array($request) ? ($request['status'] ?? null) : $request->input('status');

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status);
        }

        return $query;
    }
}
