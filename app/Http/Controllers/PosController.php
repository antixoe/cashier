<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\ActivityLogService;

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

    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $sort = $request->query('sort');

        $productsQuery = Product::with('category');

        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        if ($sort === 'price_asc') {
            $productsQuery->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $productsQuery->orderBy('price', 'desc');
        } elseif ($sort === 'name_desc') {
            $productsQuery->orderBy('name', 'desc');
        } else {
            $productsQuery->orderBy('name', 'asc');
        }

        $products = $productsQuery->get();
        $categories = Category::orderBy('name')->get();

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
        return view('pos.index', compact('products', 'cartData', 'categories', 'search', 'categoryId', 'sort'));

    }

    public function history(Request $request)
    {
        $search = $request->query('search');
        $sort = $request->query('sort');

        $salesQuery = Sale::with('saleItems.product');

        if ($search) {
            $salesQuery = $salesQuery->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('saleItems.product', function ($q2) use ($search) {
                      $q2->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($sort === 'total_asc') {
            $salesQuery->orderBy('total', 'asc');
        } elseif ($sort === 'total_desc') {
            $salesQuery->orderBy('total', 'desc');
        } elseif ($sort === 'date_asc') {
            $salesQuery->orderBy('created_at', 'asc');
        } else {
            $salesQuery->orderBy('created_at', 'desc');
        }

        $sales = $salesQuery->paginate(15)->withQueryString();

        return view('pos.history', compact('sales', 'search', 'sort'));
    }

    public function addToCart(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::findOrFail($productId);
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

        // Log activity
        ActivityLogService::logAddToCart($product, 1);

        return response()->json(['success' => true]);
    }

    public function removeFromCart(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::findOrFail($productId);
        $cart = $this->getCart();
        $cartItem = $cart->cartItems()->where('product_id', $productId)->first();
        $quantity = $cartItem ? $cartItem->quantity : 0;
        if ($cartItem) {
            if ($cartItem->quantity > 1) {
                $cartItem->decrement('quantity');
            } else {
                $cartItem->delete();
            }
        }

        // Log activity
        ActivityLogService::logRemoveFromCart($product, $quantity);

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

        // Log activity
        ActivityLogService::logCheckout($sale, $total, $cartItems->count());

        $cart->cartItems()->delete(); // Clear cart

        $invoiceItems = $cartItems->map(function ($item) {
            return [
                'product' => $item->product->name,
                'price' => $item->product->price,
                'quantity' => $item->quantity,
                'line' => $item->product->price * $item->quantity,
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $total,
            'sale_id' => $sale->id,
            'items' => $invoiceItems,
            'date' => $sale->created_at->toDateTimeString(),
        ]);
    }
}
