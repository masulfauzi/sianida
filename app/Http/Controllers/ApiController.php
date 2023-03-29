<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Users\Models\Users;
use Illuminate\Support\Facades\DB;


class ApiController extends Controller
{
    public function login(Request $request)
    {
        return DB::table('users')
                    ->where('username', $request->input('username'))
                    ->where('ids', $request->input('password'))
                    ->first();
    }

    
}
