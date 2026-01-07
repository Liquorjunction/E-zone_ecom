<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cards\DestroyCardRequest;
use App\Http\Requests\Admin\Cards\EditCardRequest;
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
            // 1. Get the validated data (Includes arrays for title, description, etc.)
            $data = $request->validated();

            // 2. Smart Auto-Translate Logic
            // Loop through the translatable fields
            foreach (['title', 'description', 'link_text'] as $field) {

                // If French is empty, auto-translate from English
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
                }

                // If Arabic is empty, auto-translate from English
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
                }
            }

            // 3. Handle Status (Checkbox sends 'active' or null)
            $data['status'] = $request->input('status', 'inactive');

            // 4. Send the PROCESSED data to the Repository
            // Note: We pass $request (for image file) AND $data (for text)
            $cardRepo->storeCard($request, $data);

            return redirect()->route('admin.cards.index')->with('success', 'Card created successfully.');

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->route('admin.cards.index')->with('error', $e->getMessage());
        }
    }

    public function edit(EditCardRequest $request)
    {
        $id = $request->input('id');
        $card = Card::findOrFail($id);

        return view('admin.pages.cards.edit', compact('card'));
    }

    public function update(UpdateCardRequest $request, CardRepo $cardRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['title', 'description', 'link_text'] as $field) {
                // Ensure English exists and is not empty
                if (! empty($data[$field]['en'])) {
                    // Fill French if empty
                    if (empty($data[$field]['fr'])) {
                        $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
                    }
                    // Fill Arabic if empty
                    if (empty($data[$field]['ar'])) {
                        $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
                    }
                }
            }
            // ----------------------------------

            // Pass the modified $data to the repository
            $cardRepo->updateCard($request, $id, $data);

            return redirect()->route('admin.cards.index')->with('success', 'Card updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
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

    public function status(EditCardRequest $request, CardRepo $cardRepo)
    {
        try {
            $cardRepo->updateStatus($request->input('id'));

            return response()->json([
                'success' => true,
                'message' => 'Card status updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function data(ReadCardRequest $request, CardRepo $cardRepo)
    {
        try {
            $cards = $cardRepo->getDataForDataTable($request->all());
            $getTable = DataTables::of($cards)
                ->addIndexColumn()
                ->addColumn('title', function ($card) {
                    // Fetch the title in english
                    $title = $card->getTranslation('title', 'en');

                    // Limit the title to 50 characters
                    return strlen($title) > 50 ? substr($title, 0, 50).'...' : $title;
                })
                ->addColumn('description', function ($card) {
                    // Fetch the description in english
                    $description = $card->getTranslation('description', 'en');

                    // Limit the description to 50 characters
                    return strlen($description) > 50 ? substr($description, 0, 50).'...' : $description;
                })
                ->addColumn('image', function ($card) {
                    if ($card->image && Storage::disk('public')->exists($card->image)) {
                        $url = asset('storage/'.$card->image);

                        return '<img src="'.$url.'" alt="image" class="img-thumbnail" width="50">';
                    }

                    return 'No Image';
                })
                ->addColumn('status', function ($card) use ($request) {
                    $data['toggle_url'] = $request->user()->can('card.update') ? route('admin.cards.toggleStatus', ['id' => $card->id]) : '';
                    $data['status'] = $card->status;

                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($card) use ($request) {
                    $data['edit_url'] = $request->user()->can('card.update') ? route('admin.cards.edit', ['id' => $card->id]) : '';
                    $data['delete_url'] = $request->user()->can('card.delete') ? route('admin.cards.destroy', ['id' => $card->id]) : '';

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);

            return $getTable;
        } catch (\Exception $e) {
            Log::error('Error in CardController@getDataForDataTable: '.$e->getMessage());
            throw $e;
        }
    }
}
