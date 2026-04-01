@extends('layout.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')

    <main class="main-content">
        <section class="glass-card" style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
            <div>
                <h1>Sales Report</h1>
                <p>Comprehensive sales analytics and insights.</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('reports.export-sales', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-secondary" title="Export Sales">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Sales CSV
                </a>
                <a href="{{ route('reports.export-products', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-secondary" title="Export Products">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Products CSV
                </a>
            </div>
        </section>

        <!-- Date Filter -->
        <section class="glass-card">
            <h3 style="margin-top: 0;">Filter by Date Range</h3>
            <form method="GET" action="{{ route('reports.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                <div style="flex: 1; min-width: 150px;">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="glass-input" value="{{ $startDate }}" style="height: 40px;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="glass-input" value="{{ $endDate }}" style="height: 40px;">
                </div>
                <button type="submit" class="btn">Apply Filter</button>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">Reset</a>
            </form>
        </section>

        <!-- Key Metrics -->
        <section class="glass-card">
            <h2 style="margin-top:0;">Key Metrics</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div style="background: rgba(239, 68, 68, 0.2); padding: 20px; border-radius: 12px; border-left: 4px solid rgba(239, 68, 68, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 8px; font-weight: 600;">TOTAL SALES</div>
                    <div style="font-size: 32px; font-weight: 800; color: #fde68a;">{{ $totalSales }}</div>
                    <div style="font-size: 11px; color: #9ca3af; margin-top: 4px;">transactions</div>
                </div>

                <div style="background: rgba(34, 197, 94, 0.2); padding: 20px; border-radius: 12px; border-left: 4px solid rgba(34, 197, 94, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 8px; font-weight: 600;">TOTAL REVENUE</div>
                    <div style="font-size: 32px; font-weight: 800; color: #fde68a;">Rp {{ number_format($totalRevenue, 0) }}</div>
                    <div style="font-size: 11px; color: #9ca3af; margin-top: 4px;">total amount</div>
                </div>

                <div style="background: rgba(59, 130, 246, 0.2); padding: 20px; border-radius: 12px; border-left: 4px solid rgba(59, 130, 246, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 8px; font-weight: 600;">AVG ORDER VALUE</div>
                    <div style="font-size: 32px; font-weight: 800; color: #fde68a;">Rp {{ number_format($averageOrderValue, 0) }}</div>
                    <div style="font-size: 11px; color: #9ca3af; margin-top: 4px;">per order</div>
                </div>

                <div style="background: rgba(168, 85, 247, 0.2); padding: 20px; border-radius: 12px; border-left: 4px solid rgba(168, 85, 247, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 8px; font-weight: 600;">ITEMS SOLD</div>
                    <div style="font-size: 32px; font-weight: 800; color: #fde68a;">{{ $totalItemsSold ?? 0 }}</div>
                    <div style="font-size: 11px; color: #9ca3af; margin-top: 4px;">total units</div>
                </div>

                <div style="background: rgba(245, 158, 11, 0.2); padding: 20px; border-radius: 12px; border-left: 4px solid rgba(245, 158, 11, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 8px; font-weight: 600;">PRODUCTS</div>
                    <div style="font-size: 32px; font-weight: 800; color: #fde68a;">{{ $productCount }}</div>
                    <div style="font-size: 11px; color: #9ca3af; margin-top: 4px;">in catalog</div>
                </div>

                <div style="background: rgba(99, 102, 241, 0.2); padding: 20px; border-radius: 12px; border-left: 4px solid rgba(99, 102, 241, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 8px; font-weight: 600;">ACTIVE USERS</div>
                    <div style="font-size: 32px; font-weight: 800; color: #fde68a;">{{ $activeUsers }}</div>
                    <div style="font-size: 11px; color: #9ca3af; margin-top: 4px;">users (period)</div>
                </div>
            </div>
        </section>

        <!-- Charts/Diagrams -->
        <section class="glass-card">
            <h2 style="margin-top:0;">Visual Reports</h2>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div style="background: rgba(255, 255, 255, 0.12); padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.12);">
                    <h3 style="margin-bottom: 12px; font-size: 16px;">Revenue Trend</h3>
                    <canvas id="revenueTrendChart" height="220"></canvas>
                </div>
                <div style="background: rgba(255, 255, 255, 0.12); padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.12);">
                    <h3 style="margin-bottom: 12px; font-size: 16px;">Top Products by Quantity</h3>
                    <canvas id="topProductsChart" height="220"></canvas>
                </div>
            </div>
        </section>

        <!-- Top Products -->
        <section class="glass-card">
            <h2 style="margin-top:0;">Top 10 Products</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity Sold</th>
                            <th>Unit Price</th>
                            <th>Total Revenue</th>
                            <th>% of Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $item)
                            <tr>
                                <td><strong>{{ $item->product->name ?? 'Unknown' }}</strong></td>
                                <td style="text-align: center;">{{ $item->total_quantity }}</td>
                                <td>Rp {{ number_format($item->price, 0) }}</td>
                                <td style="font-weight: 600;">Rp {{ number_format($item->total_revenue, 0) }}</td>
                                <td>
                                    <span style="display:inline-block;background:rgba(34,197,94,0.35);color:#fef9c3;border-radius:999px;padding:4px 12px;font-size:12px;font-weight:600;">
                                        {{ $totalRevenue > 0 ? number_format(($item->total_revenue / $totalRevenue) * 100, 1) : 0 }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:#fde68a;">No sales data available for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Daily Sales Summary -->
        <section class="glass-card">
            <h2 style="margin-top:0;">Daily Sales Summary</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Transactions</th>
                            <th>Total Revenue</th>
                            <th>Avg Order Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailySales as $daily)
                            <tr>
                                <td><strong>{{ \Carbon\Carbon::parse($daily->date)->format('Y-m-d (l)') }}</strong></td>
                                <td style="text-align: center;">{{ $daily->count }}</td>
                                <td>Rp {{ number_format($daily->total, 0) }}</td>
                                <td>Rp {{ number_format($daily->count > 0 ? $daily->total / $daily->count : 0, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; color:#fde68a;">No sales data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Recent Transactions -->
        <section class="glass-card">
            <h2 style="margin-top:0;">Recent Transactions</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Sale ID</th>
                            <th>Date & Time</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                            <tr>
                                <td><strong>#{{ $sale->id }}</strong></td>
                                <td>{{ $sale->created_at->format('Y-m-d H:i:s') }}</td>
                                <td style="text-align: center;">
                                    <span style="display:inline-block;background:rgba(99,102,241,0.35);color:#fef9c3;border-radius:999px;padding:2px 8px;font-size:12px;">
                                        {{ $sale->saleItems ? $sale->saleItems->count() : 0 }} items
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: #fef9c3;">Rp {{ number_format($sale->total, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; color:#fde68a;">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Monthly Revenue Trend -->
        @if($monthlyRevenue && $monthlyRevenue->count() > 0)
        <section class="glass-card">
            <h2 style="margin-top:0;">Monthly Revenue Trend (Last 12 Months)</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Revenue</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $lastRevenue = null; @endphp
                        @foreach($monthlyRevenue as $item)
                            @php
                                $currentRevenue = $item->total;
                                $trend = $lastRevenue ? (($currentRevenue - $lastRevenue) / $lastRevenue) * 100 : 0;
                                $lastRevenue = $currentRevenue;
                                $monthName = \Carbon\Carbon::createFromDate($item->year, $item->month, 1)->format('M Y');
                            @endphp
                            <tr>
                                <td><strong>{{ $monthName }}</strong></td>
                                <td>Rp {{ number_format($currentRevenue, 0) }}</td>
                                <td>
                                    @if($trend > 0)
                                        <span style="display:inline-block;background:rgba(34,197,94,0.35);color:#fef9c3;border-radius:999px;padding:4px 12px;font-size:12px;font-weight:600;">
                                            ↑ {{ number_format($trend, 1) }}%
                                        </span>
                                    @elseif($trend < 0)
                                        <span style="display:inline-block;background:rgba(239,68,68,0.35);color:#fef9c3;border-radius:999px;padding:4px 12px;font-size:12px;font-weight:600;">
                                            ↓ {{ number_format($trend, 1) }}%
                                        </span>
                                    @else
                                        <span style="display:inline-block;background:rgba(99,102,241,0.35);color:#fef9c3;border-radius:999px;padding:4px 12px;font-size:12px;font-weight:600;">
                                            — No change
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const monthlyLabels = @json($monthlyRevenue->map(fn($item) => \Carbon\Carbon::createFromDate($item->year, $item->month, 1)->format('M Y')));
            const monthlyValues = @json($monthlyRevenue->map(fn($item) => (float)$item->total));

            const topProductLabels = @json($topProducts->map(fn($item) => $item->product->name ?? 'Unknown'));
            const topProductValues = @json($topProducts->map(fn($item) => (int)$item->total_quantity));

            const ctxRevenue = document.getElementById('revenueTrendChart').getContext('2d');
            new Chart(ctxRevenue, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Revenue',
                        data: monthlyValues,
                        borderColor: 'rgba(75, 192, 192, 0.9)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => 'Rp ' + Number(value).toLocaleString(),
                                color: '#fef9c3'
                            }
                        },
                        x: {
                            ticks: { color: '#fef9c3' }
                        }
                    },
                    plugins: {
                        legend: { labels: { color: '#fef9c3' } }
                    }
                }
            });

            const ctxTop = document.getElementById('topProductsChart').getContext('2d');
            new Chart(ctxTop, {
                type: 'bar',
                data: {
                    labels: topProductLabels,
                    datasets: [{
                        label: 'Units Sold',
                        data: topProductValues,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#fef9c3'
                            }
                        },
                        x: {
                            ticks: { color: '#fef9c3' }
                        }
                    },
                    plugins: {
                        legend: { labels: { color: '#fef9c3' } }
                    }
                }
            });
        </script>

    </main>

@endsection
