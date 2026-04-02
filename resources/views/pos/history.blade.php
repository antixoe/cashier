@extends('layout.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
    <main class="main-content">
        <section class="glass-card">
            <h1>Sales History</h1>
            <p>Checkout records for sales processed by cashier users.</p>
        </section>

        <section class="glass-card">
            <form method="GET" action="{{ route('pos.history') }}" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; margin-bottom:16px;">
                <div style="flex:1; min-width:180px;">
                    <label style="display:block; margin-bottom:4px;">Search Sale / Product</label>
                    <input type="text" name="search" value="{{ old('search', $search ?? '') }}" placeholder="Sale ID or product name" style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;" />
                </div>
                <div style="flex:1; min-width:170px;">
                    <label style="display:block; margin-bottom:4px;">Sort</label>
                    <select name="sort" style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;">
                        <option value="date_desc" @if(($sort ?? '') === 'date_desc') selected @endif>Newest</option>
                        <option value="date_asc" @if(($sort ?? '') === 'date_asc') selected @endif>Oldest</option>
                        <option value="total_desc" @if(($sort ?? '') === 'total_desc') selected @endif>Total ↓</option>
                        <option value="total_asc" @if(($sort ?? '') === 'total_asc') selected @endif>Total ↑</option>
                    </select>
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn" style="padding:8px 16px;">Apply</button>
                    <a href="{{ route('pos.history') }}" class="btn" style="padding:8px 16px; background:#6b7280;">Clear</a>
                </div>
            </form>

            @if($sales->count() > 0)
                <div style="display:grid; gap:16px;">
                    @foreach($sales as $sale)
                        <div style="border:1px solid rgba(0,0,0,0.12); border-radius: 14px; background:#fff; padding: 18px; box-shadow: 0 8px 16px rgba(0,0,0,0.06);">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
                                <div>
                                    <strong style="font-size: 16px;">Sale #{{ $sale->id }}</strong><br>
                                    <span style="font-size: 13px; color:#666;">{{ $sale->created_at->format('Y-m-d H:i:s') }}</span>
                                </div>
                                <div style="text-align:right;">
                                    <span style="font-size: 14px; font-weight: 700; color: #dc2626;">Rp {{ number_format($sale->total, 2) }}</span>
                                    <div style="font-size: 12px; color: #666;">{{ $sale->saleItems->sum('quantity') }} items</div>
                                </div>
                            </div>
                            <div style="margin-top:8px; border-top:1px solid #eee; padding-top: 12px;">
                                <table style="width:100%; border-collapse: collapse; font-size: 14px;">
                                    <thead>
                                        <tr style="color:#444;">
                                            <th style="text-align:left; padding: 6px;">Product</th>
                                            <th style="text-align:right; padding: 6px;">Qty</th>
                                            <th style="text-align:right; padding: 6px;">Price</th>
                                            <th style="text-align:right; padding: 6px;">Line</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sale->saleItems as $item)
                                            <tr style="border-top:1px solid #f0f0f0;">
                                                <td style="padding: 6px;">{{ $item->product->name ?? 'Unknown' }}</td>
                                                <td style="padding: 6px; text-align: right;">{{ $item->quantity }}</td>
                                                <td style="padding: 6px; text-align: right;">Rp {{ number_format($item->price, 2) }}</td>
                                                <td style="padding: 6px; text-align: right;">Rp {{ number_format($item->price * $item->quantity, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top: 16px;">{{ $sales->links() }}</div>
            @else
                <p>No sales history available yet.</p>
            @endif
        </section>
    </main>
@endsection
