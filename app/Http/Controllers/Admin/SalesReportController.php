<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SalesReport;
use App\Models\ProductSalesReport;
use App\Models\CustomerSalesReport;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
class SalesReportController extends Controller
    
{

    // API: Top products for dashboard filter
    public function orderTopProducts(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->input('date', now()->toDateString());
        $carbonDate = \Carbon\Carbon::parse($date);

        if ($type === 'daily') {
            $start = $carbonDate->copy()->startOfDay();
            $end = $carbonDate->copy()->endOfDay();
        } elseif ($type === 'weekly') {
            $start = $carbonDate->copy()->startOfWeek();
            $end = $carbonDate->copy()->endOfWeek();
        } elseif ($type === 'monthly') {
            $start = $carbonDate->copy()->startOfMonth();
            $end = $carbonDate->copy()->endOfMonth();
        } else {
            $start = $carbonDate->copy()->startOfDay();
            $end = $carbonDate->copy()->endOfDay();
        }

        $orderIds = \App\Models\Order::whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->pluck('id');

        $products = \App\Models\OrderItem::select('product_id',
                DB::raw('SUM(quantity) as quantity_sold'),
                DB::raw('SUM(quantity * price) as total_revenue')
            )
            ->whereIn('order_id', $orderIds)
            ->groupBy('product_id')
            ->orderByDesc('quantity_sold')
            ->with('product')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'product_name' => $item->product->name ?? '',
                    'quantity_sold' => $item->quantity_sold,
                    'total_revenue' => $item->total_revenue,
                ];
            });

        return response()->json(['products' => $products]);
    }
    // API: Filtered chart data for dashboard
    public function orderChart(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->input('date', now()->toDateString());
        $carbonDate = \Carbon\Carbon::parse($date);

        if ($type === 'daily') {
            $start = $carbonDate->copy()->startOfDay();
            $end = $carbonDate->copy()->endOfDay();
            $interval = 'hour';
        } elseif ($type === 'weekly') {
            $start = $carbonDate->copy()->startOfWeek();
            $end = $carbonDate->copy()->endOfWeek();
            $interval = 'day';
        } elseif ($type === 'monthly') {
            $start = $carbonDate->copy()->startOfMonth();
            $end = $carbonDate->copy()->endOfMonth();
            $interval = 'day';
        } else {
            $start = $carbonDate->copy()->startOfDay();
            $end = $carbonDate->copy()->endOfDay();
            $interval = 'hour';
        }

        $orders = \App\Models\Order::whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->get();

        $labels = [];
        $revenue = [];
        $orderCounts = [];
        $averages = [];

        if ($interval === 'hour') {
            for ($i = 0; $i < 24; $i++) {
                $label = $start->copy()->addHours($i)->format('Y-m-d H:00');
                $labels[] = $label;
                $filtered = $orders->filter(function($order) use ($start, $i) {
                    return $order->created_at->format('Y-m-d H') === $start->copy()->addHours($i)->format('Y-m-d H');
                });
                $orderCounts[] = $filtered->count();
                $rev = $filtered->sum('total_amount');
                $revenue[] = $rev;
                $avg = $filtered->count() > 0 ? $rev / $filtered->count() : 0;
                $averages[] = number_format($avg, 3, '.', '');
            }
        } else if ($interval === 'day') {
            $period = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $label = $date->format('Y-m-d');
                $labels[] = $label;
                $filtered = $orders->filter(function($order) use ($date) {
                    return $order->created_at->format('Y-m-d') === $date->format('Y-m-d');
                });
                $orderCounts[] = $filtered->count();
                $rev = $filtered->sum('total_amount');
                $revenue[] = $rev;
                $avg = $filtered->count() > 0 ? $rev / $filtered->count() : 0;
                $averages[] = number_format($avg, 3, '.', '');
            }
        }

        return response()->json([
            'labels' => $labels,
            'revenue' => $revenue,
            'orders' => $orderCounts,
            'average' => $averages,
        ]);
    }
    // API: Order-based report with filter
    public function orderReport(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->input('date', now()->toDateString());
        $carbonDate = \Carbon\Carbon::parse($date);

        if ($type === 'daily') {
            $start = $carbonDate->copy()->startOfDay();
            $end = $carbonDate->copy()->endOfDay();
        } elseif ($type === 'weekly') {
            $start = $carbonDate->copy()->startOfWeek();
            $end = $carbonDate->copy()->endOfWeek();
        } elseif ($type === 'monthly') {
            $start = $carbonDate->copy()->startOfMonth();
            $end = $carbonDate->copy()->endOfMonth();
        } else {
            $start = $carbonDate->copy()->startOfDay();
            $end = $carbonDate->copy()->endOfDay();
        }

        $orders = \App\Models\Order::whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->get();

        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_amount');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $averageOrderValue = number_format($averageOrderValue, 3, '.', '');

        // Find the report matching the filter and date range
        $report = \App\Models\SalesReport::where('report_type', $type)
            ->where('start_date', $start->toDateString())
            ->where('end_date', $end->toDateString())
            ->first();

        return response()->json([
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'average_order_value' => $averageOrderValue,
            'report_id' => $report ? $report->id : null,
        ]);
    }
    // Show Report List Page
    public function index()
    {
        $this->ensureTodayReportExists();
        $reports = SalesReport::latest()->paginate(15);
        return view('reports.dashboard', compact('reports'));
    }

    // Generate Sales Report
    public function generate(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after_or_equal:start_date',
                'type'       => 'nullable|string'
            ]);

            $start = Carbon::parse($request->start_date)->startOfDay();
            $end   = Carbon::parse($request->end_date)->endOfDay();

            $orders = Order::whereBetween('created_at', [$start, $end])
                ->where('status', 'completed')
                ->get();

            $totalOrders  = $orders->count();
            $totalRevenue = $orders->sum('total_amount');

            $totalItems = OrderItem::whereIn('order_id', $orders->pluck('id'))
                ->sum('quantity');

            $averageOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

            // Create or Update Main Report
            $report = SalesReport::updateOrCreate(
                [
                    'report_type' => $request->type ?? 'custom',
                    'start_date'  => $start->toDateString(),
                    'end_date'    => $end->toDateString(),
                ],
                [
                    'total_orders'        => $totalOrders,
                    'total_revenue'       => $totalRevenue,
                    'total_items_sold'    => $totalItems,
                    'average_order_value'=> $averageOrder,
                    'generated_by'        => Auth::id(),
                    'is_cached'           => true,
                ]
            );

            // Clear old data before regenerating
            ProductSalesReport::where('sales_report_id', $report->id)->delete();
            CustomerSalesReport::where('sales_report_id', $report->id)->delete();

            // Product Sales Report
            $orderIds = $orders->pluck('id');
            Log::info('Report Generation - Completed Order IDs:', $orderIds->toArray());

            $products = OrderItem::select(
                    'product_id',
                    DB::raw('SUM(quantity) as total_qty'),
                    DB::raw('SUM(quantity * price) as total_revenue')
                )
                ->whereIn('order_id', $orderIds)
                ->groupBy('product_id')
                ->get();
            Log::info('Report Generation - Product Sales:', $products->toArray());

            foreach ($products as $item) {
                ProductSalesReport::create([
                    'sales_report_id' => $report->id,
                    'product_id'      => $item->product_id,
                    'quantity_sold'   => $item->total_qty,
                    'total_revenue'   => $item->total_revenue,
                    'average_price'  => $item->total_qty > 0 
                                        ? $item->total_revenue / $item->total_qty 
                                        : 0,
                ]);
            }

            // Customer Sales Report
            $customers = $orders->groupBy('user_id');

            foreach ($customers as $userId => $userOrders) {
                CustomerSalesReport::create([
                    'sales_report_id'    => $report->id,
                    'user_id'            => $userId,  
                    'total_orders'       => $userOrders->count(),
                    'total_spent'        => $userOrders->sum('total_amount'),
                    'average_order_value'=> $userOrders->avg('total_amount'),
                ]);
            }

            return redirect()->back()->with('success', 'Sales Report Generated Successfully!');
        } catch (\Throwable $e) {
            Log::error('Report Generation Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'error' => 'Report generation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Chart API
    public function chart()
    {
        $data = SalesReport::orderBy('start_date')->get();
        $latest = $data->last();

        // Collect all products from all reports
        $allProducts = collect();
        foreach ($data as $report) {
            $allProducts = $allProducts->concat(
                $report->productSales()->with('product')->get()
            );
        }

        // Group by product_id and sum quantity_sold and total_revenue
        $products = $allProducts
            ->groupBy('product_id')
            ->map(function ($items) {
                $first = $items->first();
                return [
                    'product_name' => $first->product->name ?? '',
                    'quantity_sold' => $items->sum('quantity_sold'),
                    'total_revenue' => $items->sum('total_revenue'),
                ];
            })
            ->values();

        return response()->json([
            'labels'   => $data->pluck('start_date'),
            'revenue'  => $data->pluck('total_revenue'),
            'orders'   => $data->pluck('total_orders'),
            'average'  => $data->pluck('average_order_value'),
            'summary'  => $latest ? [
                'id' => $latest->id,
                'total_revenue' => $latest->total_revenue,
                'total_orders' => $latest->total_orders,
                'average_order_value' => $latest->average_order_value,
            ] : [
                'id' => '',
                'total_revenue' => 0,
                'total_orders' => 0,
                'average_order_value' => 0,
            ],
            'products' => $products,
        ]);
    }

    // Export Excel
    public function exportExcel($id)
    {
        $filterType = request('type', 'daily');
        Log::info('ExportExcel called', ['id' => $id, 'type' => $filterType]);
        $report = SalesReport::find($id);
        if (!$report) {
            Log::error('ExportExcel: Report not found', ['id' => $id]);
            abort(404, 'Report not found');
        }
        $export = new SalesReportExport($id, $filterType);
        $response = Excel::download(
            $export,
            'sales_report_' . now()->format('Y-m-d') . '_' . $filterType . '.xlsx'
        );
        Log::info('ExportExcel response headers', ['headers' => $response->headers->all()]);
        return $response;
    }

    // Export PDF
    public function exportPDF($id)
    {
        $filterType = request('type', 'daily');
        $report = SalesReport::with([
            'productSales.product',
            'customerSales.customer'
        ])->findOrFail($id);
        $user = Auth::user();

        $pdf = Pdf::loadView('reports.pdf', compact('report', 'user', 'filterType'))
                ->setPaper('a4', 'portrait');

        return $pdf->download(
            'sales_report_' . $report->start_date . '_to_' . $report->end_date . '_' . $filterType . '.pdf'
        );
    }
    // Auto-generate today's report if not exists
    protected function ensureTodayReportExists()
    {
        $today = now()->toDateString();
        $report = SalesReport::where('start_date', $today)->where('end_date', $today)->first();
        if (!$report) {
            $request = new Request([
                'start_date' => $today,
                'end_date' => $today,
                'type' => 'daily',
            ]);
            $this->generate($request);
        }
    }
}
