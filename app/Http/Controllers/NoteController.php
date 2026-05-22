<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteController extends Controller
{
    // Render the dashboard view with notes
    // public function index()
    // {
    //     return Inertia::render('Welcome', [
    //         'notes' => Note::orderBy('updated_at', 'desc')->get(),
    //     ]);
    // }

public function index(Note $note = null)
{

$notes = Note::orderBy('updated_at', 'desc')->get();
    return inertia('Welcome', [
        'notes' => $notes,
        //'created_time_ago' => $note->created_at->diffForHumans(), 
        'activeNoteId' => $note ? $note->id : null, // Pass the shared note ID down
    ]);
}


    // Handle creating and updating notes seamlessly
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id'              => 'nullable|integer',
            'title'           => 'required|string|max:255',
            'content'         => 'nullable|string',
            'amharic_content' => 'nullable|string',
            'category'        => 'nullable|string',

        ]);

        Note::updateOrCreate(
            ['id' => $request->id],
            $validated
        );

        return redirect()->route('notes.index');
    }

    // Delete a note profile row
    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('notes.index');
    }
}