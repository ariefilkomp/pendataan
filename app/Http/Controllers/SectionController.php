<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public function store(Request $request) {
        $validated = $request->validateWithBag('addSection', [
            'form_id' => 'required',
            'name' => 'required',
            'description' => 'required',
        ]);

        $order = Section::where('form_id', $request->form_id)->orderBy('order', 'desc')->first()->order;
        
        $section = new Section();
        $section->form_id = $request->form_id;
        $section->name = $request->name;
        $section->description = $request->description;
        $section->order = $order + 1;
        $section->save();

        return redirect()->route('edit-form-section', [ "id" => $request->form_id, "section_id" => $section->id])->with('status', 'section-created');
    }

    public function destroy(Request $request) {
        $request->validateWithBag('sectionDeletion', [
            'section_id' => ['required', 'exists:sections,id'],
        ]);

        $section = Section::find($request->section_id);
        $formId = $section->form_id;
        if($section->questions->count() > 0) {
            foreach($section->questions as $question) {
                DB::statement("ALTER TABLE `{$question->table_name}` DROP IF EXISTS COLUMN `question_id`");
                $question->delete();
            }
        }
        
        $section->delete();

        Section::where('order', '>', $section->order)->decrement('order');

        return redirect()->route('edit-form', [ "id" => $formId])->with('status', 'section-deleted');
    }
}
