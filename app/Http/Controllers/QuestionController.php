<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validateWithBag('addQuestion', [
            'form_id' => 'required',
            'section_id' => 'required',
            'question' => 'required',
            'type' => 'required|in:short_answer,paragraph,multiple_choice,checkboxes,dropdown,file,date,time',
            'options' => 'required_if:type,checkboxes,dropdown,multiple_choice',
            'column_name' => 'nullable',
            'is_required' => 'nullable',
        ]);

        if(empty($request->column_name)) {
            $request->column_name = 'col_'.strtolower(Str::random(10));
        }

        $colStr = "";
        if($request->type == 'checkboxes' || $request->type == 'dropdown' || $request->type == 'multiple_choice' || $request->type == 'paragraph') {
            $colStr = "`{$request->column_name}` TEXT NULL DEFAULT NULL";
        }

        if($request->type == 'short_answer' || $request->type == 'file') {
            $colStr = "`{$request->column_name}` VARCHAR(255) NULL DEFAULT NULL";
        }

        if($request->type == 'date' ) {
            $colStr = "`{$request->column_name}` DATE NULL DEFAULT NULL";
        }

        if($request->type == 'time' ) {
            $colStr = "`{$request->column_name}` TIME NULL DEFAULT NULL";
        }

        $form = Form::findOrFail($request->form_id);
        
        $question = new Question();
        $question->form_id = $request->form_id;
        $question->section_id = $request->section_id;
        $question->question = $request->question;
        $question->column_name = $request->column_name;
        $question->is_required = $request->is_required ? 1 : 0;
        $question->type = $request->type;

        if($request->type == 'checkboxes' || $request->type == 'dropdown' || $request->type == 'multiple_choice') {
            $question->options = json_encode($request->options);
        }

        if($question->save()) {
            DB::statement("ALTER TABLE `{$form->table_name}` ADD COLUMN ".$colStr);
            return redirect()->route('edit-form-section', [ "id" => $request->form_id, "section_id" => $request->section_id])->with('status', 'question-created');
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validateWithBag('updateQuestion', [
            'form_id' => 'required',
            'question_id' => 'required',
            'question' => 'required',
            'type' => 'required|in:short_answer,paragraph,multiple_choice,checkboxes,dropdown,file,date,time',
            'options' => 'required_if:type,checkboxes,dropdown,multiple_choice',
            'column_name' => 'nullable',
            'is_required' => 'nullable',
        ]);

        if(empty($request->column_name)) {
            $request->column_name = 'col_'.strtolower(Str::random(10));
        } else {
            $request->column_name = Str::snake(trim(strtolower($request->column_name)));
        }

        $colStr = "";
        if($request->type == 'checkboxes' || $request->type == 'dropdown' || $request->type == 'multiple_choice' || $request->type == 'paragraph') {
            $colStr = "`{$request->column_name}` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL";
        }

        if($request->type == 'short_answer' || $request->type == 'file') {
            $colStr = "`{$request->column_name}` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL";
        }

        if($request->type == 'date' ) {
            $colStr = "`{$request->column_name}` DATE NULL DEFAULT NULL";
        }

        if($request->type == 'time' ) {
            $colStr = "`{$request->column_name}` TIME NULL DEFAULT NULL";
        }

        $form = Form::findOrFail($request->form_id);
        $question = Question::findOrFail($request->question_id);
        $oldName = $question->column_name;
        $oldType = $question->type;
        $question->section_id = $request->section_id;
        $question->question = $request->question;
        $question->column_name = $request->column_name;
        $question->is_required = $request->is_required ? 1 : 0;
        $question->type = $request->type;

        if($request->type == 'checkboxes' || $request->type == 'dropdown' || $request->type == 'multiple_choice') {
            $question->options = json_encode($request->options);
        }

        if($question->save() && ($oldName != $request->column_name || $oldType != $request->type)) {
            DB::statement("ALTER TABLE `{$form->table_name}` CHANGE `".$oldName."` ".$colStr);
        }

        return redirect()->route('edit-form-section', [ "id" => $request->form_id, "section_id" => $request->section_id])->with('status', 'question-updated');
    }
}
