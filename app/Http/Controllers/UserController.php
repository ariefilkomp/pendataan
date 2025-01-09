<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function table()
    {
        $columns = [
            "id","name", "email", "email_verified_at"
        ];

        $orderBy = empty(request()->input("order.0.column")) || empty($columns[request()->input("order.0.column")]) ? 'id' : $columns[request()->input("order.0.column")];
        $ord = empty(request()->input("order.0.dir")) ? 'desc' : request()->input("order.0.dir");
        
        $data = User::with('roles')->select('*');

        if(request()->input('search.value')) {
            $data = $data->where( function($query) use ($columns) {
                foreach($columns as $col) {
                    if($col == 'id' ) continue;
                    $query->orWhereRaw($col.' LIKE ?', ['%'.request()->input('search.value').'%']);
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

    public function updateRole(Request $request) {
        $request->validate([
            'id' => 'required',
            'role' => 'required|in:admin,opd,umum'
        ]);
        $user = User::findOrFail($request->id);
        $user->syncRoles([$request->role]);
        $user->save();

        return redirect()->back()->with('status', 'role-updated');
    }
}
