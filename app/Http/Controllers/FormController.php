<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Section;
use Illuminate\Support\Str;

class FormController extends Controller
{
    public function create(){
        return view('form.create', [
            'user' => auth()->user(),]);
    }

    public function store(Request $request){
        $uuid = Str::uuid();
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'table_name' => 'nullable',
            'slug' => 'nullable',
        ]);

        if(empty($request->table_name)){
            $request->table_name = strtolower(Str::random(10));
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
                'order' => 0
            ]);
            $str = 'CREATE TABLE `' . $form->table_name . '` (id char(36) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
            DB::statement($str);
        }
        
        return redirect()->route('edit-form', ['id' => $uuid])->with('status', 'form-created');
    }

    public function edit($id){
        return view('form.update-mainform', [
            'form' => Form::findOrFail($id),
        ]);
    }

    public function update(Request $request){
        $form = new Form();
        $form->user_id = auth()->user()->id;
        $form->name = $request->name;
        $form->save();
        return redirect()->route('edit-form');
    }
}
