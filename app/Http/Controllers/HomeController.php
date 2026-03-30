<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Role;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $users = User::with('roles')->paginate(10);
        $roles = Role::with('users')->get();
        $sales = Sale::with('saleItems', 'user')->latest()->paginate(10);

        // Get cart data from session
        $sessionId = session()->getId();
        $cartData = session()->get('cart', []);

        return view('home', compact(
            'products',
            'users',
            'roles',
            'sales',
            'cartData'
        ));
    }
}
