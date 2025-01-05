<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:15360',
            'form_id' => 'required',
            'question_id' => 'required',
            'answer_id' => 'required', 
        ]);
        if ($request->hasFile('file')) {
            $validated['path'] = $request->file->store('public/'.$request->form_id);
        }

        $validated['user_id'] = auth()->user()->id;
        $validated['name'] = basename($validated['path']);
        $validated['extension'] = $request->file->getClientOriginalExtension();
        $validated['mime_type'] = $request->file->getMimeType();
        $validated['size'] = $request->file->getSize();
        
        File::create($validated);
        return response()->json($validated);
    }
}
