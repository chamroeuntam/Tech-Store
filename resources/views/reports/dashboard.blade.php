@extends('layouts.app')


<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body { background: #f8fafc; }
    .dashboard-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff; padding: 2rem; border-radius: 1rem; margin-bottom: 2rem; text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .stats-grid {
        display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;
    }
    .stat-card {
        background: #fff; padding: 1.2rem 2rem; border-radius: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center; flex: 1;
        min-width: 180px; display: flex; flex-direction: column; align-items: center;
    }
    .icon { font-size: 2.2rem; margin-bottom: .5rem; color: #667eea; }
    .value { font-size: 1.6rem; font-weight: bold; }
    .report-section {
        background: #fff; padding: 1.2rem 2rem; border-radius: 1rem; margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .section-title {
        font-weight: bold; border-bottom: 2px solid #eee; margin-bottom: 1rem; padding-bottom: 6px;
        font-size: 1.2rem;
    }
    .table { width: 100%; border-collapse: collapse; background: #fff; }
    .table th, .table td { padding: 12px; border-bottom: 1px solid #eee; }
    .table th { background: #f4f6fb; }
    .table tbody tr:hover { background: #f8f9fa; }
    .btn { min-width: 120px; }
    .form-control { min-width: 140px; }
    .filter-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 1rem; }
    .loading { text-align: center; padding: 1rem; color: #667eea; }
</style>


@section('content')
<!-- ✅ HEADER -->  
<div class="dashboard-header">
    <h1 class="text-white"><i class="fas fa-chart-line text-white"></i> Sales Reports Dashboard</h1>
    <p class="text-white">Live analytics, filters & export system</p>
</div>

<!-- ✅ FILTER -->
<div class="report-section">
    <h2 class="section-title"><i class="fa-solid fa-filter"></i> Filter Reports</h2>
    <div class="filter-row">
        <select id="preset" class="form-control">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly" selected>Monthly</option>
        </select>
        <input type="date" id="date" class="form-control" value="{{ now()->toDateString() }}">
        <button onclick="loadReport()" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Apply</button>
        <a id="excelBtn" class="btn btn-success" href="#"><i class="fa-solid fa-file-excel"></i> <span id="excelText">Export Excel</span> <span id="excelSpinner" style="display:none"><i class="fa fa-spinner fa-spin"></i></span></a>
        <a id="pdfBtn" class="btn btn-secondary" href="#"><i class="fa-solid fa-file-pdf"></i> <span id="pdfText">Export PDF</span> <span id="pdfSpinner" style="display:none"><i class="fa fa-spinner fa-spin"></i></span></a>
    </div>
</div>

<!-- ✅ CHART -->
<div class="report-section">
    <h2 class="section-title"><i class="fa-solid fa-chart-bar"></i> Revenue Chart</h2>
    <canvas id="salesChart"></canvas>
</div>

<!-- ✅ STATS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="icon"><i class="fa-solid fa-dollar-sign"></i></div>
        <div class="value" id="revenue">0</div>
        <div class="label">Revenue</div>
    </div>
    <div class="stat-card">
        <div class="icon"><i class="fa-solid fa-cart-shopping"></i></div>
        <div class="value" id="orders">0</div>
        <div class="label">Orders</div>
    </div>
    <div class="stat-card">
        <div class="icon"><i class="fa-solid fa-chart-pie"></i></div>
        <div class="value" id="avg">0</div>
        <div class="label">Average</div>
    </div>
</div>

<!-- ✅ TOP PRODUCTS -->
<div class="report-section">
    <h2 class="section-title"><i class="fa-solid fa-star"></i> Top Products</h2>
    <table class="table" id="productTable">
        <thead>
            <tr><th>Product</th><th>Sold</th><th>Revenue</th></tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@endsection


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ...existing code for Excel/PDF export...

    const ctx = document.getElementById('salesChart');
    let chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Revenue',
                    data: [],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40,167,69,0.1)',
                    pointRadius: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Orders',
                    data: [],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    pointRadius: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Average',
                    data: [],
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255,193,7,0.1)',
                    pointRadius: 2,
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: {
                    grid: { display: true },
                    ticks: {
                        callback: function(value, index, ticks) {
                            const label = this.getLabelForValue(value);
                            if (!label) return '';
                            const d = new Date(label);
                            if (isNaN(d)) return label;
                            let preset = document.getElementById('preset')?.value || 'monthly';
                            if (preset === 'daily') {
                                return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                            } else if (preset === 'monthly' || preset === 'weekly') {
                                // Format as day/mm/yyyy
                                let day = String(d.getDate()).padStart(2, '0');
                                let month = String(d.getMonth() + 1).padStart(2, '0');
                                let year = d.getFullYear();
                                return `${day}/${month}/${year}`;
                            } else if (preset === 'yearly') {
                                return d.toLocaleString('default', { month: 'short', year: 'numeric' });
                            }
                            return d.toLocaleString();
                        }
                    }
                },
                y: { beginAtZero: true, grid: { display: true } }
            }
        }
    });

    function updateOrderReport() {
        let preset = document.getElementById('preset').value;
        let date = document.getElementById('date').value;
        // Update summary cards and export links
        fetch(`/admin/reports/order-report?type=${preset}&date=${date}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('revenue').innerText = data.total_revenue ?? 0;
                document.getElementById('orders').innerText = data.total_orders ?? 0;
                document.getElementById('avg').innerText = data.average_order_value ?? 0;
                // Update export button hrefs
                const excelBtn = document.getElementById('excelBtn');
                const pdfBtn = document.getElementById('pdfBtn');
                if (data.report_id) {
                    // Match route: /export/excel/{id} and /export/pdf/{id}
                    excelBtn.href = `/admin/reports/export/excel/${data.report_id}?type=${preset}`;
                    pdfBtn.href = `/admin/reports/export/pdf/${data.report_id}?type=${preset}`;
                    excelBtn.classList.remove('disabled');
                    pdfBtn.classList.remove('disabled');
                } else {
                    excelBtn.href = '#';
                    pdfBtn.href = '#';
                    excelBtn.classList.add('disabled');
                    pdfBtn.classList.add('disabled');
                }
            });
        // Update chart
        fetch(`/admin/reports/order-chart?type=${preset}&date=${date}`)
            .then(res => res.json())
            .then(data => {
                chart.data.labels = data.labels;
                chart.data.datasets[0].data = data.revenue;
                chart.data.datasets[1].data = data.orders;
                chart.data.datasets[2].data = data.average;
                chart.update();
            });
        // Update top products
        fetch(`/admin/reports/order-top-products?type=${preset}&date=${date}`)
            .then(res => res.json())
            .then(data => {
                let rows = '';
                (data.products || []).forEach(p => {
                    rows += `<tr>
                        <td>${p.product_name}</td>
                        <td>${p.quantity_sold}</td>
                        <td>$${p.total_revenue}</td>
                    </tr>`;
                });
                const tableBody = document.querySelector('#productTable tbody');
                if (tableBody) {
                    tableBody.innerHTML = rows || `<tr><td colspan="3" class="loading">No products found.</td></tr>`;
                }
            });
    }

    document.getElementById('preset').addEventListener('change', updateOrderReport);
    document.getElementById('date').addEventListener('change', updateOrderReport);
    updateOrderReport();

    // Force navigation for export buttons
    document.getElementById('excelBtn').addEventListener('click', function(e) {
        if (this.classList.contains('disabled') || this.href === '#') {
            e.preventDefault();
        } else {
            window.location.href = this.href;
        }
    });
    document.getElementById('pdfBtn').addEventListener('click', function(e) {
        if (this.classList.contains('disabled') || this.href === '#') {
            e.preventDefault();
        } else {
            window.location.href = this.href;
        }
    });
});
</script>

