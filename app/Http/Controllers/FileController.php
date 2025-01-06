<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Form;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,doc,docx,pdf|max:15360',
            'form_id' => 'required',
            'question_id' => 'required',
            'answer_id' => 'required', 
        ]);

        if ($request->hasFile('file')) {
            $validated['path'] = $request->file->store($request->form_id, 'public');
        }

        $form = Form::find($request->form_id);
        if(empty($form)) {
            return response()->json(['success' => false, 'message' => 'Form not found']);
        }

        $question = Question::find($request->question_id);
        if(empty($question)) {
            return response()->json(['success' => false, 'message' => 'Question not found']);
        }

        $question->column_name;
        $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->first();

        if(empty($data)) {
            DB::table($form->table_name)->insert([
                'id' => Str::uuid(),
                'user_id' => auth()->user()->id
            ]);
            $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->first();
        }
        
        if(empty($data->{$question->column_name})) {
            $dataUpdate = [basename($validated['path'])];
        } else {
            $dataUpdate = json_decode($data->{$question->column_name}, true);
            $dataUpdate[] = basename($validated['path']);
        }
        
        DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->update([
            $question->column_name => json_encode($dataUpdate)
        ]);

        $validated['user_id'] = auth()->user()->id;
        $validated['name'] = basename($validated['path']);
        $validated['extension'] = $request->file->getClientOriginalExtension();
        $validated['mime_type'] = $request->file->getMimeType();
        $validated['size'] = $request->file->getSize();
        
        File::create($validated);
        return response()->json(['success' => true, 'message' => 'File uploaded successfully']);
    }
}
