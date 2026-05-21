<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class TranslationController extends Controller
{
  public function translateSnippet(Request $request)
{
    $request->validate([
        'text' => 'required|string',
        'note_id' => 'required|integer|exists:notes,id',
    ]);

    $textToTranslate = $request->text;
    $note = Note::findOrFail($request->note_id);

    try {
        // Send request to translation microservice or Google Translate engine
        $response = Http::get('https://translate.googleapis.com/translate_a/single', [
            'client' => 'gtx',
            'sl'     => 'en',
            'tl'     => 'am',
            'dt'     => 't',
            'q'      => $textToTranslate,
        ]);

        if ($response->successful()) {
            $translatedText = $response->json()[0][0][0] ?? '';
            
            // Append the translated snippet cleanly onto the end of your existing Amharic block
            $currentAmharic = $note->amharic_content ? $note->amharic_content . "\n" : "";
            $note->update([
                'amharic_content' => $currentAmharic . $translatedText
            ]);
        }
    } catch (\Exception $e) {
        // Safe fallback log tracking
        Log::error("Translation execution crash: " . $e->getMessage());
    }

    return redirect()->back();
}
}