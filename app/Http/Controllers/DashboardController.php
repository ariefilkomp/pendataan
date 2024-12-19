<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'forms' => Form::all(),
        ]);
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
}