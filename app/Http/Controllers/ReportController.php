<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use App\Models\SaleItem;
use App\Models\ActivityLog;

class ReportController extends Controller
{
    /**
     * Display dashboard report page
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->format('Y-m-d'));

        // Sales Statistics
        $totalSales = Sale::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
        $totalRevenue = Sale::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->sum('total') ?? 0;
        $averageOrderValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Product Statistics
        $topProducts = SaleItem::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(quantity * price) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Daily Sales Data
        $dailySales = Sale::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User Activity
        $totalUsers = User::count();
        $activeUsers = ActivityLog::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->distinct('user_id')
            ->count('user_id');

        // Recent Transactions
        $recentSales = Sale::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('saleItems')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Monthly Revenue
        $monthlyRevenue = Sale::whereBetween('created_at', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total) as total')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->get() ?? collect([]);

        // Product Performance
        $productCount = Product::count();
        $totalItemsSold = SaleItem::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->sum('quantity') ?? 0;

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'totalSales',
            'totalRevenue',
            'averageOrderValue',
            'topProducts',
            'dailySales',
            'totalUsers',
            'activeUsers',
            'recentSales',
            'monthlyRevenue',
            'productCount',
            'totalItemsSold'
        ));
    }

    /**
     * Export sales report
     */
    public function exportSalesReport(Request $request)
    {
        $startDate = $request->query('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->format('Y-m-d'));

        $sales = Sale::with('saleItems.product')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->get() ?? collect([]);

        $csvContent = "Sales Report - " . now()->format('Y-m-d H:i:s') . "\n";
        $csvContent .= "Period: $startDate to $endDate\n\n";
        $csvContent .= "Sale ID,Date,Time,Total Amount,Items Count\n";

        foreach ($sales as $sale) {
            $date = $sale->created_at->format('Y-m-d');
            $time = $sale->created_at->format('H:i:s');
            $itemCount = $sale->saleItems ? $sale->saleItems->count() : 0;
            $csvContent .= "{$sale->id},\"$date\",\"$time\",\"" . number_format($sale->total, 2) . "\",{$itemCount}\n";
        }

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales-report-' . now()->format('Y-m-d-H-i-s') . '.csv"',
        ]);
    }

    /**
     * Export product report
     */
    public function exportProductReport(Request $request)
    {
        $startDate = $request->query('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->format('Y-m-d'));

        $products = SaleItem::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(quantity * price) as total_revenue')
            ->groupBy('product_id')
            ->get() ?? collect([]);

        $csvContent = "Product Report - " . now()->format('Y-m-d H:i:s') . "\n";
        $csvContent .= "Period: $startDate to $endDate\n\n";
        $csvContent .= "Product Name,Quantity Sold,Total Revenue\n";

        foreach ($products as $item) {
            $productName = $item->product ? $item->product->name : 'Unknown';
            $csvContent .= "\"$productName\",{$item->total_quantity},\"" . number_format($item->total_revenue, 2) . "\"\n";
        }

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product-report-' . now()->format('Y-m-d-H-i-s') . '.csv"',
        ]);
    }
}
