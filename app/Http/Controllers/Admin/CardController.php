<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cards\DestroyCardRequest;
use App\Http\Requests\Admin\Cards\ReadCardRequest;
use App\Http\Requests\Admin\Cards\StoreCardRequest;
use App\Http\Requests\Admin\Cards\UpdateCardRequest;
use App\Models\Card;
use App\Repositories\Admin\Interfaces\CardRepositoryInterface as CardRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CardController extends Controller
{
    public function index()
    {
        return view('admin.pages.cards.index');
    }

    public function create()
    {
        return view('admin.pages.cards.create');
    }

    public function store(StoreCardRequest $request, CardRepo $cardRepo)
    {
        try {
            $cardRepo->storeCard($request->all());
            return redirect()->route('admin.cards.index')->with('success', 'Card created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.cards.index')->with('error', $e->getMessage());
        }
    }

    public function edit(UpdateCardRequest $request)
    {
        $id = $request->query('id');
        $card = Card::findOrFail($id);
        return view('admin.pages.cards.edit', compact('card'));
    }

    public function update(UpdateCardRequest $request, CardRepo $cardRepo)
    {
        try {
            $id = $request->input('id');
            $cardRepo->updateCard($request, $id);
            return redirect()->route('admin.cards.index')->with('success', 'Card updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.cards.index')->with('error', $e->getMessage());
        }
    }

    public function destroy(DestroyCardRequest $request, CardRepo $cardRepo)
    {
        try {
            $cardRepo->deleteCard($request->input('id'));
            return redirect()->route('admin.cards.index')->with('success', 'Card deleted successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.cards.index')->with('error', $e->getMessage());
        }
    }

    public function status(UpdateCardRequest $request, CardRepo $cardRepo)
    {
        try {
            $cardRepo->updateStatus($request->input('id'));
            return redirect()->route('admin.cards.index')->with('success', 'Card status updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.cards.index')->with('error', $e->getMessage());
        }
    }

    public function data(ReadCardRequest $request, CardRepo $cardRepo)
    {
        try {
            $cards = $cardRepo->getDataForDataTable($request->all());
            $getTable = DataTables::of($cards)
                ->addIndexColumn()
                ->addColumn('image', function ($card) {
                    if ($card->image && Storage::disk('public')->exists($card->image)) {
                        return asset('storage/' . $card->image);
                    }
                })
                ->addColumn('is_active', function ($card) use ($request) {
                    $data['toggle_url'] = $request->user()->can('card.update') ? route('admin.cards.toggleStatus', ['id' => $card->id]) : '';
                    $data['status'] = $card->is_active;
                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($card) use ($request) {
                    $data['edit_url'] = $request->user()->can('card.update') ? route('admin.cards.edit', ['id' => $card->id]) : '';
                    $data['delete_url'] = $request->user()->can('card.delete') ? route('admin.cards.destroy', ['id' => $card->id]) : '';
                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['action', 'image', 'is_active'])
                ->make(true);

            return $getTable;
        } catch (\Exception $e) {
            Log::error('Error in CardController@getDataForDataTable: '.$e->getMessage());
            throw $e;
        }
    }

}
