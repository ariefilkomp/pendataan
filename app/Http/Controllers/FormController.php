<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Section;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class FormController extends Controller
{
    private $optType = [
        'short_answer' => 'Short Answer',
        'paragraph' => 'Paragraph', 
        'multiple_choice' => 'Multiple Choice',
        'checkboxes' => 'Checkboxes',
        'dropdown' => 'Dropdown',
        'file' => 'File', 
        'date' => 'Date', 
        'time' => 'Time'
    ];

    public function create(){
        return view('form.create', [
            'user' => auth()->user(),]);
    }

    public function store(Request $request){
        $uuid = Str::uuid();
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'table_name' => 'nullable|unique:forms,table_name',
            'slug' => 'nullable|unique:forms,slug',
        ]);

        $routeCollection = collect(Route::getRoutes()->get())->unique();
        if($routeCollection->where('uri', $request->slug)->count() > 0) {
            return redirect()->route('create-form')->with('error', 'Slug already exists');
        }

        if(empty($request->table_name)){
            $request->table_name = 'tbl_'.strtolower(Str::random(10));
        } else {
            $request->table_name = Str::snake(trim(strtolower($request->table_name)));
        }

        if(empty($request->slug)){
            $request->slug = $uuid;
        } else {
            $request->slug = Str::slug($request->slug,'-');
        }

        $form = new Form();
        $form->id = $uuid;
        $form->user_id = auth()->user()->id;
        $form->name = $request->name;
        $form->description = $request->description;
        $form->table_name = $request->table_name;
        $form->slug = $request->slug;
        if($form->save()) {
            Section::create([
                'id' => Str::uuid(),
                'form_id' => $uuid,
                'name' => $form->name,
                'description' => $form->description,
            ]);
            $str = 'CREATE TABLE `' . $form->table_name . '` (id char(36) NOT NULL, user_id char(36) NOT NULL, submitted_at datetime DEFAULT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
            DB::statement($str);
        }
        
        return redirect()->route('edit-form', ['id' => $uuid])->with('status', 'form-created');
    }

    public function edit($id){
        $form = Form::findOrFail($id);
        return view('form.update-mainform', [
            'form' => $form,
            'section_id' => $form->sections->sortBy('order', SORT_NUMERIC)->first()->id,
            'optType' => $this->optType
        ]);
    }

    public function editWithSection($id, $section_id){
        $form = Form::findOrFail($id);
        return view('form.update-mainform', [
            'form' => $form,
            'section_id' => $section_id,
            'optType' => $this->optType
        ]);
    }

    public function update(Request $request){
        $form = Form::findOrFail($request->id);
        $oldTableName = $form->table_name;
        $form->fill($request->all());

        if($form->isDirty('table_name')) {
            $form->table_name = Str::snake(trim(strtolower($request->table_name)));
            DB::statement('RENAME TABLE `' . $oldTableName . '` TO `' . $form->table_name .'`');
        }

        $form->save();
        return redirect()->back()->with('status', 'form-updated');
    }

    public function show($slug){
        $form = Form::where('slug', $slug)->where('published', 1)->first();
        if(empty($form)){
            abort(404);  
        }
        $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->first();
        if(empty($data)){
            DB::table($form->table_name)->insert([
                'id' => Str::uuid(),
                'user_id' => auth()->user()->id
            ]);
            $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->first();
        }
        return view('form.show', [
            'form' => $form,
            'section' => $form->sections->sortBy('order', SORT_NUMERIC)->first(),
            'optType' => $this->optType,
            'data' => $data
        ]);
    }

    public function showWithSection($slug, $section_id){
        $form = Form::where('slug', $slug)->where('published', 1)->first();
        $section = $form->sections->where('id', $section_id)->first();
        if(empty($form) || empty($section)){
            abort(404);
        }
        $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->first();
        return view('form.show', [
            'form' => $form,
            'section' => $section,
            'optType' => $this->optType,
            'data' => $data
        ]);
    }
    
    public function submit(Request $request){
        $form = Form::findOrFail($request->id);
        $section = $form->sections->where('id', $request->section_id)->first();
        if(empty($section)){
            abort(404);
        }

        $questions = $section->questions->where('section_id', $section->id)->where('form_id', $form->id);
        $validation = [];
        $checkbox = [];
        $unsetFiles = [];
        foreach($questions as $question) {
            $filter = $question->is_required ? 'required' : 'nullable';
            $validation[$question->column_name] = $filter;
            if($question->type == 'checkboxes' ) {
                $checkbox[] = $question->column_name;
            }

            if($question->type == 'file') {
                $unsetFiles[] = $question->column_name;
            }

            if($question->type == 'date') {
                $filter .= '|date';
            }
        }

        $validated = $request->validateWithBag('submitForm', $validation);

        if(count($checkbox) > 0) {
            foreach($checkbox as $c) {
                $validated[$c] = json_encode($request->{$c});
            }
        }

        if(count($unsetFiles) > 0) {
            foreach($unsetFiles as $f) {
                unset($validated[$f]);
            }
        }

        $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->first();

        if(empty($data)) {
            DB::table($form->table_name)->insert(
                array_merge([
                'id' => Str::uuid(),
                'user_id' => auth()->user()->id
            ], $validated));
        } else {
            DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->update($validated);
        }

        if($form->sections->count() == 1 || $section->order == $form->sections->count()) {
            DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->update(['submitted_at' => now()]);
            return view('form.submitted');
        } else {
            $nextSection = $form->sections->where('order', $section->order + 1)->first();
            return redirect('/'.$form->slug.'/'.$nextSection->id)->with('status', 'form-submitted');
        }
    }
}
