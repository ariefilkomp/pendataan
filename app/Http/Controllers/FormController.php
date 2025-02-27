<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Message;
use App\Models\RejectMessage;
use App\Models\Section;
use Illuminate\Support\Facades\Http;
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
            'short_url' => 'nullable|numeric',
            'multi_entry' => 'nullable|numeric',
            'published' => 'nullable|numeric',
            'for_role' => 'required'
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

        $shortSlug = null;
        $shortId = null;
        if($request->short_url) {
            $response = Http::acceptJson()->withBody(json_encode(['long_url' => url('/'.$request->slug), 'title' => $request->name]))->withHeaders([
                'X-Auth-Id' =>  env('SID_AUTH_ID'),
                'X-Auth-Key' => env('SID_AUTH_KEY'), 
           ])->post('https://api.s.id/v1/links');
        
           if($response->successful()) {
                $shortSlug = $response->json()['data']['short'];
                $shortId = $response->json()['data']['id'];
           }
        }

        $form = new Form();
        $form->id = $uuid;
        $form->user_id = auth()->user()->id;
        $form->name = $request->name;
        $form->description = $request->description;
        $form->table_name = $request->table_name;
        $form->slug = $request->slug;
        $form->published = $request->published == '1' ? 1 : 0;
        $form->multi_entry = $request->multi_entry == '1' ? 1 : 0;
        $form->for_role = $request->for_role;
        $form->short_slug = $shortSlug;
        $form->short_id = $shortId;

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
        if($form->user_id != auth()->user()->id && !auth()->user()->hasRole('admin')) {
            return abort(403);
        }

        $rejectMessages = collect([]);
        if($form->status == 'rejected') {
            $rejectMessages = RejectMessage::where('form_id', $id)->get();
        }

        return view('form.update-mainform', [
            'form' => $form,
            'section_id' => $form->sections?->sortBy('order', SORT_NUMERIC)->first()?->id,
            'optType' => $this->optType,
            'rejectMessages' => $rejectMessages
        ]);
    }

    public function editWithSection($id, $section_id){
        $form = Form::findOrFail($id);
        if($form->user_id != auth()->user()->id && !auth()->user()->hasRole('admin')) {
            return abort(403);
        }

        return view('form.update-mainform', [
            'form' => $form,
            'section_id' => $section_id,
            'optType' => $this->optType
        ]);
    }

    public function update(Request $request){
        $form = Form::findOrFail($request->id);
        $oldTableName = $form->table_name;
        $oldSlug = $form->slug;
        
        if($oldTableName != $request->table_name) {
            $form->table_name = Str::snake(trim(strtolower($request->table_name)));
            DB::statement('RENAME TABLE `' . $oldTableName . '` TO `' . $form->table_name .'`');
        }

        if($oldSlug != $request->slug) {
            $newSlug = Str::slug($request->slug,'-');

            /* 
                idealnya, 
                bisa update short urlnya, tapi karena s.id api update tidak support untuk akun free, 
                maka ya mau gak mau bikin lagi urlnya..
                dibawah ini buat update short url
            */ 

            /*
                $form->slug = $newSlug;
                $response = Http::acceptJson()->withBody(json_encode(
                    [
                        'long_url' => url('/'.$newSlug),
                        'short' => $form->short_slug,
                        'title' => $request->name
                    ]))->withHeaders([
                    'X-Auth-Id' =>  env('SID_AUTH_ID'),
                    'X-Auth-Key' => env('SID_AUTH_KEY'), 
                ])->post('https://api.s.id/v1/links/'.$form->short_id);

                var_dump($response->body());
                var_dump($response->json());
                dd($response);
            */

            $form->slug = $newSlug;
            if(isset($form->short_id)) {
                /* bikin short url lagi */
                $response = Http::acceptJson()->withBody(json_encode(['long_url' => url('/'.$newSlug), 'title' => $request->name]))->withHeaders([
                    'X-Auth-Id' =>  env('SID_AUTH_ID'),
                    'X-Auth-Key' => env('SID_AUTH_KEY'), 
                ])->post('https://api.s.id/v1/links');
            
                if($response->successful()) {
                    $form->short_slug = $response->json()['data']['short'];
                    $form->short_id = $response->json()['data']['id'];
                }
            }
        }

        $form->name = $request->name;
        $form->description = $request->description;
        $form->published = $request->published == '1' ? 1 : 0;
        $form->multi_entry = $request->multi_entry == '1' ? 1 : 0;
        $form->for_role = $request->for_role;

        $form->save();
        return redirect()->back()->with('status', 'form-updated');
    }

    public function show($slug){
        $form = Form::where('slug', $slug)->firstOrFail();
        if($form->status != 'approved' || $form->published != 1) {
            return view('form.unapproved', ['form' => $form]);
        }

        if(!$form->multi_entry) {
            $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNotNull('submitted_at')->first();
            if($data){
                return view('form.duplicate-entry');
            }
        }

        if(auth()->guest()) {
            session()->put('url.intended', url($form->slug));
            return view('form.must-login',['form' => $form]);
        }

        if($form->for_role == 'opd' && !auth()->user()->hasAnyRole(['opd', 'admin'])) {
            return abort(404);
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
            'section' => $form->sections?->sortBy('order', SORT_NUMERIC)->first(),
            'optType' => $this->optType,
            'data' => $data
        ]);
    }

    public function showWithSection($slug, $section_id){
        $form = Form::where('slug', $slug)->where('published', 1)->where('status','approved')->firstOrFail();
        $section = $form->sections?->where('id', $section_id)->firstOrFail();
        if(!$form->multi_entry) {
            $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNotNull('submitted_at')->first();
            if($data){
                return view('form.duplicate-entry');
            }
        }

        if(auth()->guest()) {
            session()->put('url.intended', url($form->slug));
            return view('form.must-login');
        }

        if($form->for_role == 'opd' && !auth()->user()->hasAnyRole(['opd', 'admin'])) {
            return abort(404);
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
            $filter = $question->is_required && $question->type != 'file' ? 'required' : 'nullable';
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
            return view('form.submitted', ['formUrl' => url($form->slug)]);
        } else {
            $nextSection = $form->sections->where('order', $section->order + 1)->first();
            return redirect('/'.$form->slug.'/'.$nextSection->id)->with('status', 'form-submitted');
        }
    }

    public function forms() {
        return view('form.forms');
    }

    public function gettable(Request $request) {
        $columns = array(
            "id",
            "table_name",
            "name",
            "description",
        );
    
        $orderBy = empty(request()->input("order.0.column")) ? 'id' : (isset($columns[request()->input("order.0.column")]) ? $columns[request()->input("order.0.column")] : 'id');
        $ord = empty(request()->input("order.0.dir")) ? 'desc' : request()->input("order.0.dir");
        
        $data = Form::with('sections');

        // if(!auth()->user()->hasAnyRole(['admin', 'opd'])) {
        //     $data = $data->where('for_role', 'umum');
        // }

        if(!auth()->user()->hasAnyRole(['admin'])) {
            $data = $data->where('user_id', auth()->user()->id);
        }

        if(request()->input('search.value')) {
            $data = $data->where( function($query) {
                $query->orWhereRaw('table_name LIKE ?', ['%'.request()->input('search.value').'%'])
                    ->orWhereRaw('name LIKE ?', ['%'.request()->input('search.value').'%'])
                    ->orWhereRaw('description LIKE ?', ['%'.request()->input('search.value').'%']);
            });
        }

        $recordsFiltered = $data->get()->count();
       
        $data = $data->skip(request()->input('start'))
            ->take(request()->input('length'))
            ->orderBy($orderBy, $ord)
            ->get();

        $recordsTotal = $data->count();

        return response()->json([
            'draw' => request()->input('draw'),
            'recordsTotal' => (int)$recordsTotal,
            'recordsFiltered' => (int)$recordsFiltered,
            'data' => $data
        ]);
    }

    public function formApproval(Request $request) {
        $request->validate([
            'id' => 'required',
            'status' => 'required|in:draft,submitted,approved,rejected',
            'message' => 'required_if:status,rejected',
            'published' => 'nullable|numeric'
        ]);

        $form = Form::findOrFail($request->id);
        $form->status = $request->status;
        $form->published = $request->published ? 1 : 0;
        if($form->save() && $request->status == 'rejected') {
            RejectMessage::create([
                'form_id' => $form->id,
                'message' => $request->message,
                'user_id' => auth()->user()->id
            ]);
        }
        return redirect()->back()->with('status', 'form-approval');
    }

    public function submitToAdmin(Request $request) {
        $request->validate([
            'id' => 'required',
        ]);

        $form = Form::findOrFail($request->id);
        $form->status = 'submitted';
        if($form->save()) {
             Message::create([
                'to' => env('NO_WA_ADMIN','088802462823'),
                'message' => 'Form Baru diajukan oleh '.$form->user->name.' - Judul form : '.$form->name.' - Link : '.url('edit-form/'.$form->slug),
                'form_id' => $form->id,
            ]);
        }
        return redirect()->back()->with('status', 'form-submit-to-admin');
    }

    public function delete(Request $request) {    
        $form = Form::findOrFail($request->id);
        $form->delete();
        return redirect('/dashboard')->with('status', 'form-deleted');
    }

    public function preview($slug){
        $form = Form::where('slug', $slug);
        if(!auth()->user()->hasRole('admin')) {
            $form = $form->where('user_id', auth()->user()->id);
        }

        $form = $form->firstOrFail();

        $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->first();
        if(empty($data)){
            DB::table($form->table_name)->insert([
                'id' => Str::uuid(),
                'user_id' => auth()->user()->id
            ]);
            $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->first();
        }
        return view('form.preview', [
            'form' => $form,
            'section' => $form->sections?->sortBy('order', SORT_NUMERIC)->first(),
            'optType' => $this->optType,
            'data' => $data
        ]);
    }

    public function previewWithSection($slug, $section_id){
        $form = Form::where('slug', $slug);
        if(!auth()->user()->hasRole('admin')) {
            $form = $form->where('user_id', auth()->user()->id);
        }

        $form = $form->firstOrFail();

        $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->first();
        if(empty($data)){
            DB::table($form->table_name)->insert([
                'id' => Str::uuid(),
                'user_id' => auth()->user()->id
            ]);
            $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->first();
        }
        $section = $form->sections?->where('id', $section_id)->firstOrFail();
        
        $data = DB::table($form->table_name)->where('user_id', auth()->user()->id)->whereNull('submitted_at')->first();
        return view('form.preview', [
            'form' => $form,
            'section' => $section,
            'optType' => $this->optType,
            'data' => $data
        ]);
    }

}
