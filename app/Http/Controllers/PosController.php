<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Cart;
use App\Models\CartItem;

class PosController extends Controller
{
    private function getCart()
    {
        $sessionId = session()->getId();
        $cart = Cart::where('session_id', $sessionId)->first();
        if (!$cart) {
            $cart = Cart::create(['session_id' => $sessionId]);
        }
        return $cart;
    }

    public function index()
    {
        $products = Product::all();
        $cart = $this->getCart();
        $cartItems = $cart->cartItems()->with('product')->get();
        $cartData = [];
        foreach ($cartItems as $item) {
            $cartData[$item->product_id] = [
                'name' => $item->product->name,
                'price' => $item->product->price,
                'quantity' => $item->quantity,
            ];
        }
        return view('pos.index', compact('products', 'cartData'));
    }

    public function addToCart(Request $request)
    {
        $productId = $request->product_id;
        $cart = $this->getCart();
        $cartItem = $cart->cartItems()->where('product_id', $productId)->first();
        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => 1,
            ]);
        }
        return response()->json(['success' => true]);
    }

    public function removeFromCart(Request $request)
    {
        $productId = $request->product_id;
        $cart = $this->getCart();
        $cartItem = $cart->cartItems()->where('product_id', $productId)->first();
        if ($cartItem) {
            if ($cartItem->quantity > 1) {
                $cartItem->decrement('quantity');
            } else {
                $cartItem->delete();
            }
        }
        return response()->json(['success' => true]);
    }

    public function checkout(Request $request)
    {
        $cart = $this->getCart();
        $cartItems = $cart->cartItems()->with('product')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Cart is empty']);
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }

        $sale = Sale::create(['total' => $total]);

        foreach ($cartItems as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        $cart->cartItems()->delete(); // Clear cart
        return response()->json(['success' => true, 'total' => $total]);
    }
}
