<style>
body { font-family: DejaVu Sans, Arial, sans-serif; color: #222; }
.pdf-header {
    text-align: center;
    margin-bottom: 18px;
    border-bottom: 2px solid #7b2ff2;
    padding-bottom: 8px;
}
.pdf-title { font-size: 2rem; font-weight: bold; color: #7b2ff2; margin-bottom: 4px; }
.pdf-meta { font-size: 1rem; color: #555; margin-bottom: 10px; }
.pdf-summary {
    margin: 18px 0 18px 0;
    background: #f3f3fd;
    border-radius: 8px;
    padding: 12px 18px;
    font-size: 1.1rem;
    color: #333;
}
.pdf-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 18px;
}
.pdf-table th {
    background: #7b2ff2;
    color: #fff;
    font-weight: bold;
    padding: 10px 6px;
    border: 1px solid #ddd;
    font-size: 1rem;
}
.pdf-table td {
    padding: 8px 6px;
    border: 1px solid #ddd;
    font-size: 0.98rem;
    background: #fff;
}
.pdf-table tr:nth-child(even) td { background: #f7f7fb; }
.pdf-footer {
    text-align: right;
    font-size: 0.95rem;
    color: #888;
    margin-top: 24px;
    border-top: 1px solid #eee;
    padding-top: 8px;
}
</style>

<div class="pdf-header">
    <div class="pdf-title">IT-Store Sales Report ({{ ucfirst($filterType ?? 'daily') }})</div>
    <div class="pdf-meta">Period: <strong>{{ $report->start_date }}</strong> to <strong>{{ $report->end_date }}</strong></div>
</div>

<div class="pdf-summary">
    <strong>Summary:</strong><br>
    Total Revenue: <strong>${{ number_format($report->total_revenue, 2) }}</strong><br>
    Total Orders: <strong>{{ $report->total_orders }}</strong><br>
    Average Order Value: <strong>${{ number_format($report->average_order_value, 2) }}</strong>
</div>

<table class="pdf-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity Sold</th>
            <th>Total Revenue</th>
        </tr>
    </thead>
    <tbody>
    @if($report->productSales->count() > 0)
        @foreach($report->productSales as $row)
            <tr>
                <td>{{ $row->product->name }}</td>
                <td>{{ $row->quantity_sold }}</td>
                <td>${{ number_format($row->total_revenue, 2) }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="3" style="text-align:center;">No product sales data for this report.</td>
        </tr>
    @endif
    </tbody>
</table>

<div class="pdf-footer">
    Generated on {{ date('Y-m-d H:i:s') }} by {{ $user->username }}
</div>

