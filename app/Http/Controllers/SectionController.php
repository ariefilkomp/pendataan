<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function store(Request $request) {
        $validated = $request->validateWithBag('addSection', [
            'form_id' => 'required',
            'name' => 'required',
            'description' => 'required',
        ]);
        $section = new Section();
        $section->form_id = $request->form_id;
        $section->name = $request->name;
        $section->description = $request->description;
        $section->save();
        return redirect()->back()->with('status', 'section-created');
    }
}
