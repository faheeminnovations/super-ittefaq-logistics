<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CustomersController extends CrudController
{
    protected string $table = 'customers';

    public function index()
    {
        $customers = DB::table('customers')->paginate(15);
        return view('pages.customers', compact('customers'));
    }
}
