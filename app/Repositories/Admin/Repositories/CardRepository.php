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

    public function getCardById($id)
    {
        return Card::find($id);
    }

    public function storeCard($request)
    {
        $data = $request->validated();

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('cards', 'public');
            $data['image'] = $imagePath;
        }

        return Card::create($data);
    }

    public function updateCard($request, $id)
    {
        $card = Card::findOrFail($id);
        $data = $request->validated();

        // Handle Image Update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($card->image && Storage::disk('public')->exists($card->image)) {
                Storage::disk('public')->delete($card->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('cards', 'public');
            $data['image'] = $imagePath;
        } else {
            // Remove image key from data if no new image uploaded so it doesn't overwrite with null
            unset($data['image']);
        }

        $card->update($data);

        return $card;
    }

    public function deleteCard($id)
    {
        $card = Card::findOrFail($id);

        // Delete Image from storage
        if ($card->image && Storage::disk('public')->exists($card->image)) {
            Storage::disk('public')->delete($card->image);
        }

        return $card->delete();
    }

    public function updateStatus($id)
    {
        $card = Card::findOrFail($id);
        $card->is_active = !$card->is_active;
        $card->save();

        return $card;
    }

    public function getDataForDataTable()
    {
        // Return query for Yajra DataTables
        return Card::query()->latest();
    }
}
