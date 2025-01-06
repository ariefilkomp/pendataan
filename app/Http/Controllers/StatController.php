<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatController extends Controller
{
    public function show($form_id)
    {
        $form = Form::findOrFail($form_id);

        return view('stat.show', [
            'form' => $form,
        ]);
    }

    public function table(Request $request)
    {
        $form = Form::findOrFail($request->id);
        $columns = [];
        foreach($form->questions->sortBy('created_at') as $question) {
            $columns[] = $question->column_name;
        }
    
        $orderBy = empty(request()->input("order.0.column")) ? 'id' : (isset($columns[request()->input("order.0.column")]) ? $columns[request()->input("order.0.column")] : 'id');
        $ord = empty(request()->input("order.0.dir")) ? 'desc' : request()->input("order.0.dir");
        
        $data = DB::table($form->table_name)
            ->join('users', 'users.id', '=', 'user_id')
            ->select($form->table_name.'.*', 'users.name as user_name')
            ->whereNotNull('submitted_at');

        if(request()->input('search.value')) {
            $data = $data->where( function($query) use ($form) {
                foreach($form->questions->sortBy('created_at') as $question) {
                    $query->orWhereRaw($question->column_name.' LIKE ?', ['%'.request()->input('search.value').'%']);
                }
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
}
