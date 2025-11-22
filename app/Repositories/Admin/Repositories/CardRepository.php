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

    // Update the signature to accept $data = null
    public function storeCard($request, $data = null)
    {
        // If data was passed from controller (with translations), use it.
        // Otherwise, fall back to the request's validated data.
        $data = $data ?? $request->validated();

        // Ensure status is set (default to inactive if missing)
        $data['status'] = $data['status'] ?? 'inactive';

        // Handle Image Upload (Must use $request to get the file)
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('cards', 'public');
            $data['image'] = $imagePath;
        }

        // Create the card
        // Spatie's HasTranslations trait will automatically convert the arrays
        // (title, description, etc.) into JSON for the database.
        return Card::create($data);
    }

    public function updateCard($request, $id)
    {
        $card = Card::findOrFail($id);

        // 1. Get the validated data (which now includes arrays for translations)
        $data = $request->validated();

        // 2. Handle Image Update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($card->image && Storage::disk('public')->exists($card->image)) {
                Storage::disk('public')->delete($card->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('cards', 'public');
            $data['image'] = $imagePath;
        } else {
            // Remove image key if not uploaded to prevent overwriting with null
            unset($data['image']);
        }

        // 3. Update the Card
        // Note: We removed the manual 'status' logic because the Request validation
        // now ensures $data['status'] is exactly 'active' or 'inactive'.
        // The Spatie trait automatically converts the title/desc arrays to JSON.
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
        $card->status = ($card->status === 'active') ? 'inactive' : 'active';
        $card->save();

        return $card;
    }

    public function getDataForDataTable()
    {
        // Return query for Yajra DataTables
        return Card::query()->latest();
    }
}
