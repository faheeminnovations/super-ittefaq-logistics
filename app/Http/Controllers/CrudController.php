<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\Controller as BaseController;

class CrudController extends BaseController
{
    protected string $table;

    public function __construct()
    {
        // Require authentication for all CRUD operations
        $this->middleware('auth');

        // Ensure the authenticated user has the admin role
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || ! method_exists($user, 'hasRole') || ! $user->hasRole('admin')) {
                abort(403, 'Access denied');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $table = $this->table;
        $columns = Schema::getColumnListing($table);
        $items = DB::table($table)->paginate(15);
        return view('admin.crud.index', compact('items','columns','table'));
    }

    public function create()
    {
        $table = $this->table;
        $columns = Schema::getColumnListing($table);
        return view('admin.crud.create', compact('columns','table'));
    }

    public function store(Request $request)
    {
        $table = $this->table;
        $data = $request->except(['_token']);
        // Remove id and timestamps if present
        unset($data['id']);
        unset($data['created_at']);
        unset($data['updated_at']);
        
        // Remove empty string values that should be null
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        DB::table($table)->insert($data);
        return redirect()->route($table . '.index')->with('success', ucfirst($table) . ' created.');
    }

    public function show($id)
    {
        $table = $this->table;
        $item = DB::table($table)->where('id', $id)->first();
        if (! $item) abort(404);
        $columns = Schema::getColumnListing($table);
        return view('admin.crud.show', compact('item','columns','table'));
    }

    public function edit($id)
    {
        $table = $this->table;
        $item = DB::table($table)->where('id', $id)->first();
        if (! $item) abort(404);
        $columns = Schema::getColumnListing($table);
        return view('admin.crud.edit', compact('item','columns','table'));
    }

    public function update(Request $request, $id)
    {
        $table = $this->table;
        $data = $request->except(['_token','_method','id','created_at','updated_at']);
        
        // Remove empty string values that should be null
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
        
        DB::table($table)->where('id', $id)->update($data);
        return redirect()->route($table . '.index')->with('success', ucfirst($table) . ' updated.');
    }

    public function destroy($id)
    {
        $table = $this->table;
        DB::table($table)->where('id', $id)->delete();
        return redirect()->route($table . '.index')->with('success', ucfirst($table) . ' deleted.');
    }
}
