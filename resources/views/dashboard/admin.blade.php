@extends('layouts.app')

@section('content')
  <div class="page-header compact-dashboard-header">
    <form method="GET" class="filter-form compact-filter-form">
      <input type="date" name="date" value="{{ $date }}" class="form-control compact-field">
      <button type="submit" class="btn btn-primary compact-button">Filter</button>
    </form>
  </div>

  <div class="metric-grid">
    <article class="metric-card">
      <div class="label">
        <span>Total Pengiriman</span>
        <span class="trend">+12.4%</span>
      </div>
      <div class="value">{{ $totalPengiriman }}</div>
    </article>

    <article class="metric-card">
      <div class="label">
        <span>Driver Aktif</span>
        <span class="trend">Live</span>
      </div>
      <div class="value">{{ $driverAktif }}</div>
    </article>

    <article class="metric-card">
      <div class="label">
        <span>On-Time Rate</span>
        <span class="trend">{{ $onTimeRate }}%</span>
      </div>
      <div class="value">{{ $onTimeRate }}%</div>
    </article>

    <article class="metric-card">
      <div class="label">
        <span>Jumlah Status</span>
        <span class="trend">{{ count($statusCounts) }}</span>
      </div>
      <div class="value">{{ array_sum(is_array($statusCounts) ? $statusCounts : $statusCounts->toArray()) }}</div>
    </article>

    <article class="metric-card">
      <div class="label">
        <span>Total Surat Masuk</span>
        <span class="trend">No Rec</span>
      </div>
      <div class="value">{{ $totalSuratMasuk }}</div>
    </article>
  </div>

  <div class="stats-grid">
    <section class="panel section-panel">
      <div class="section-header">
        <h3>Jadwal Pengiriman</h3>
        <div class="pill-group" role="group" aria-label="Filter jadwal pengiriman">
          <a href="{{ route('dashboard', ['jadwal_filter' => 'hari_ini', 'period' => request('period'), 'date' => request('date')]) }}" class="btn btn-sm {{ $jadwalFilter === 'hari_ini' ? 'btn-primary' : 'btn-outline-secondary' }}">Hari Ini</a>
          <a href="{{ route('dashboard', ['jadwal_filter' => 'besok', 'period' => request('period'), 'date' => request('date')]) }}" class="btn btn-sm {{ $jadwalFilter === 'besok' ? 'btn-primary' : 'btn-outline-secondary' }}">Besok</a>
          <a href="{{ route('dashboard', ['jadwal_filter' => 'kemarin', 'period' => request('period'), 'date' => request('date')]) }}" class="btn btn-sm {{ $jadwalFilter === 'kemarin' ? 'btn-primary' : 'btn-outline-secondary' }}">Kemarin</a>
          <a href="{{ route('dashboard', ['jadwal_filter' => 'semua', 'period' => request('period'), 'date' => request('date')]) }}" class="btn btn-sm {{ $jadwalFilter === 'semua' ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</a>
        </div>
      </div>

      <div class="dashboard-schedule-scroll">
        <table class="table-modern">
          <thead>
            <tr>
              <th>#</th>
              <th>Tanggal</th>
              <th>Driver</th>
              <th>Tujuan</th>
              <th>Material</th>
              <th>Bobot</th>
              <th>Progress</th>
            </tr>
          </thead>
          <tbody>
            @forelse($items as $it)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $it->tanggal }}</td>
                <td>{{ $it->driver?->nama_driver }}</td>
                <td>{{ $it->tujuan?->nama_tujuan }}</td>
                <td>{{ $it->material?->nama_material }}</td>
                <td>{{ number_format($it->nilai_bobot, 2) }}</td>
                <td>
                  <span class="status-pill info">{{ $it->progress_tujuan_persen }}%</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7">
                  <div class="empty-state">
                    <div>
                      <strong>Belum ada jadwal hari ini</strong>
                      <span>Data pengiriman akan muncul setelah jadwal dibuat.</span>
                    </div>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-2 px-3 d-flex justify-content-between align-items-center border-top bg-light-subtle" style="font-size: 13px;">
        <span class="text-muted">Menampilkan {{ count($items) }} jadwal prioritas</span>
        <a href="{{ route('jadwal.index') }}" class="btn btn-sm btn-outline-primary" style="display: inline-flex; align-items: center; gap: 4px;">
          Buka Semua Jadwal &rarr;
        </a>
      </div>
    </section>

    <section class="panel section-panel">
      <div class="section-header">
        <h3>Statistik Pengiriman</h3>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-6">
          <div class="metric-card kpi-card">
            <div class="label">
              <span>Top Material</span>
            </div>
            <div class="value" style="font-size:1.45rem;">{{ $materialLabels[0] ?? 'N/A' }}</div>
          </div>
        </div>
        <div class="col-6">
          <div class="metric-card kpi-card">
            <div class="label">
              <span>On-Time</span>
            </div>
            <div class="value" style="font-size:1.45rem;">{{ $onTimeRate }}%</div>
          </div>
        </div>
      </div>
      <div class="chart-wrap">
        <canvas id="topMaterialChart" height="220"></canvas>
      </div>
      @if (array_sum($timbanganData) > 0)
        <div class="chart-wrap dashboard-chart-spaced">
          <canvas id="topTimbanganChart" height="220"></canvas>
        </div>
      @else
        <div class="empty-state dashboard-chart-spaced">
          <strong>Belum ada data timbangan</strong>
          <span>Grafik timbangan akan muncul setelah data tersedia.</span>
        </div>
      @endif
    </section>
  </div>

  <div class="stats-grid dashboard-chart-grid">
    <section class="panel section-panel">
      <div class="section-header">
        <h3>Tren Volume & Timbangan</h3>
      </div>
      <div class="chart-wrap">
        <canvas id="volumeTimbanganTrendChart" height="240"></canvas>
      </div>
    </section>

    <section class="panel section-panel">
      <div class="section-header">
        <h3>Status Pengiriman</h3>
      </div>
      <div class="chart-wrap dashboard-pie-wrap">
        <canvas id="statusPieChart" height="240"></canvas>
      </div>
    </section>
  </div>

  <script>
    (function renderDashboardCharts() {
      if (!window.Chart) {
        window.setTimeout(renderDashboardCharts, 50);
        return;
      }

      const materialLabels = {!! json_encode($materialLabels) !!};
      const materialData = {!! json_encode($materialData) !!};
      const timbanganLabels = {!! json_encode($timbanganLabels) !!};
      const timbanganData = {!! json_encode($timbanganData) !!};
      const trendLabels = {!! json_encode($trendLabels) !!};
      const volumeTrendData = {!! json_encode($volumeTrendData) !!};
      const timbanganTrendData = {!! json_encode($timbanganTrendData) !!};
      const statusLabels = {!! json_encode($statusLabels) !!};
      const statusData = {!! json_encode($statusData) !!};

      ['topMaterialChart', 'topTimbanganChart', 'volumeTimbanganTrendChart', 'statusPieChart'].forEach(function (id) {
        const canvas = document.getElementById(id);
        const chart = canvas && window.Chart.getChart(canvas);
        if (chart) chart.destroy();
      });

    new Chart(document.getElementById('topMaterialChart'), {
      type: 'bar',
      data: {
        labels: materialLabels,
        datasets: [{
          label: 'Top Material',
          data: materialData,
          borderRadius: 8,
          backgroundColor: ['#0B1220', '#334155', '#F5A524', '#0EA5E9', '#16A34A']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#E2E8F0' },
            ticks: { color: '#475569' }
          },
          x: {
            grid: { display: false },
            ticks: { color: '#475569' }
          }
        }
      }
    });

    @if (array_sum($timbanganData) > 0)
    new Chart(document.getElementById('topTimbanganChart'), {
      type: 'bar',
      data: {
        labels: timbanganLabels,
        datasets: [{
          label: 'Timbangan CV SR',
          data: timbanganData,
          borderRadius: 8,
          backgroundColor: '#0EA5A4'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: '#E2E8F0' }, ticks: { color: '#475569' } },
          x: { grid: { display: false }, ticks: { color: '#475569' } }
        }
      }
    });
    @endif

    new Chart(document.getElementById('volumeTimbanganTrendChart'), {
      type: 'line',
      data: {
        labels: trendLabels,
        datasets: [
          { label: 'Volume', data: volumeTrendData, borderColor: '#0EA5E9', backgroundColor: 'rgba(14,165,233,0.12)', fill: true, tension: 0.35 },
          { label: 'Timbangan', data: timbanganTrendData, borderColor: '#0F766E', backgroundColor: 'rgba(15,118,110,0.08)', fill: true, tension: 0.35 }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, color: '#475569' } } },
        scales: {
          y: { beginAtZero: true, grid: { color: '#E2E8F0' }, ticks: { color: '#475569' } },
          x: { grid: { display: false }, ticks: { color: '#475569', maxRotation: 45 } }
        }
      }
    });

    new Chart(document.getElementById('statusPieChart'), {
      type: 'doughnut',
      data: {
        labels: statusLabels,
        datasets: [{
          data: statusData,
          backgroundColor: ['#0F766E', '#F59E0B', '#DC2626', '#0EA5E9', '#64748B'],
          borderColor: '#FFFFFF',
          borderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '62%',
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, color: '#475569' } } }
      }
    });
    }());
  </script>
@endsection
