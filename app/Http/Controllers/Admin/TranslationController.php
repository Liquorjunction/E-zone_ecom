<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\Interfaces\TranslationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TranslationController extends Controller
{
    protected $translationRepo;

    public function __construct(TranslationRepositoryInterface $translationRepo)
    {
        $this->translationRepo = $translationRepo;
    }

    // Single Field Translation
    public function translate(Request $request)
    {
        try {
            $request->validate(['text' => 'required|string']);
            $isHtml = $request->boolean('is_html', false);
            $translations = $this->translationRepo->translateAll($request->text, ['fr', 'ar'], $isHtml);
            return response()->json(['success' => true, 'translations' => $translations]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // NEW: Batch Field Translation
    public function translateBatch(Request $request)
    {
        try {
            $request->validate([
                'fields' => 'required|array', // Expects: ['title' => '...', 'description' => '...']
            ]);

            $translations = $this->translationRepo->translateBatch($request->fields, ['fr', 'ar']);

            return response()->json([
                'success' => true,
                'translations' => $translations
            ]);
        } catch (\Exception $e) {
            Log::error('Batch Translation Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
