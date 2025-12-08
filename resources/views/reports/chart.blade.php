<canvas id="salesChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
fetch('/admin/report/chart')
.then(res => res.json())
.then(data => {
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                { label: 'Revenue', data: data.revenue },
                { label: 'Orders', data: data.orders }
            ]
        }
    });
});
</script>
