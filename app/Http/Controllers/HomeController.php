<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $cartData = session()->get('cart', []);
        $roles = Schema::hasTable('roles') ? Role::all() : collect();
        $sales = Schema::hasTable('sales') ? Sale::orderByDesc('created_at')->limit(10)->get() : collect();
        $users = Schema::hasTable('users') ? User::all() : collect();
        $currentUser = auth()->user(); // Get authenticated user if logged in
        $openLogin = $request->query('openLogin', 0);

        return view('home', compact('cartData', 'roles', 'sales', 'users', 'currentUser', 'openLogin'));
    }
}
