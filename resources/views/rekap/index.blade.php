@extends('layouts.app')
@section('content')
<div class="d-none d-print-block print-header">
  <div class="skb-logo skb-logo--print" aria-label="SKB Trans logo print">
    <img src="{{ asset('images/logo.png') }}" alt="SKB Trans" />
  </div>
  <div class="print-meta">
    <strong>Keluar Masuk Material</strong>
    <span>{{ now()->translatedFormat('d F Y') }}</span>
    <span>{{ now()->translatedFormat('H:i:s') }} WIB</span>
  </div>
</div>

<div class="rekap-summary-row d-none d-print-none">
  <div class="rekap-tools-wrap">
    <div class="rekap-print-box">
      <a href="{{ route('rekap.export', request()->query()) }}" class="btn btn-icon btn-icon--success rekap-export-btn" title="Export Excel" aria-label="Export Excel">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h8"/></svg>
      </a>
      <a href="{{ route('rekap.pdf', request()->query()) }}" download class="btn btn-icon btn-outline-secondary rekap-pdf-btn" title="Export PDF" aria-label="Export PDF">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h9l5 5v15H6z"/><path d="M15 2v6h5"/><path d="M9 13h6M9 17h4"/></svg>
      </a>
      <button id="btnPrintOld" class="btn btn-icon btn-outline-secondary rekap-print-btn" title="Print" aria-label="Print">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 9V3h10v6"/><path d="M7 18H5a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M7 14h10v7H7z"/></svg>
      </button>
    </div>
  </div>
</div>

<form method="GET" id="filterForm" class="filter-panel rekap-filter-form">
    <div class="rekap-filter-field rekap-mode-group">
      <label for="modeSelect">Periode</label>
      <select id="modeSelect" class="form-control rekap-mode-select" aria-label="Pilih mode filter">
        <option value="day">Day</option>
        <option value="month">Month</option>
        <option value="year">Year</option>
      </select>
    </div>
    <div class="rekap-filter-field rekap-date-wrap" id="dateGroup">
      <label for="dateSingle">Tanggal</label>
      <input type="text" id="dateSingle" class="form-control rekap-date-input" value="{{ $date_from ? \Carbon\Carbon::parse($date_from)->format('d/m/Y') : '' }}" placeholder="dd/mm/yyyy" autocomplete="off">
      <button type="button" id="dateTrigger" class="rekap-date-trigger" aria-label="Pilih tanggal">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>
      </button>
      <input type="hidden" name="date_from" id="date_from" value="{{ $date_from ?? '' }}">
      <input type="hidden" name="date_to" id="date_to" value="{{ $date_to ?? '' }}">
      <input type="date" id="dateNative" class="d-none" value="{{ $date_from ?? '' }}">
    </div>
    <div class="rekap-filter-field rekap-month-wrap" id="monthGroup">
      <label for="monthNative">Bulan</label>
      <input type="month" id="monthNative" class="form-control rekap-month-input" value="{{ $year && $month ? sprintf('%04d-%02d', $year, $month) : '' }}">
      <select id="monthSelect" name="month" class="d-none">
        <option value="">--Month--</option>
        @for($i=1;$i<=12;$i++)
          <option value="{{ $i }}" @if(($month ?? '') == $i) selected @endif>{{ $i }}</option>
        @endfor
      </select>
    </div>
    <div class="rekap-filter-field rekap-year-wrap" id="yearGroup">
      <label for="yearSelect">Tahun</label>
      <select id="yearSelect" name="year" class="form-control rekap-select-input">
        <option value="">--Year--</option>
        @for($y = date('Y'); $y >= date('Y')-5; $y--)
          <option value="{{ $y }}" @if(($year ?? '') == $y) selected @endif>{{ $y }}</option>
        @endfor
      </select>
    </div>
    <div class="rekap-action-row">
      <button class="btn btn-sm btn-primary rekap-action-primary">Apply</button>
      <a href="{{ route('rekap.index') }}" class="btn btn-sm btn-outline-secondary rekap-reset-button">Reset</a>
    </div>
    <div class="rekap-search-wrap rekap-search-field">
      <label for="rekapSearch">Pencarian</label>
      <div class="rekap-search-control">
        <input id="rekapSearch" type="text" name="q" value="{{ $q ?? '' }}" placeholder="Material, driver, tujuan, no_rec" class="form-control rekap-search-input">
        <button type="submit" class="rekap-search-submit" title="Cari" aria-label="Cari"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></button>
      </div>
    </div>
    <div class="rekap-tools-wrap rekap-filter-actions">
      <div class="rekap-print-box">
        <a href="{{ route('rekap.export', request()->query()) }}" class="btn btn-icon btn-icon--success rekap-export-btn" title="Export Excel" aria-label="Export Excel">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h8"/></svg>
        </a>
        <a href="{{ route('rekap.pdf', request()->query()) }}" class="btn btn-icon btn-outline-secondary rekap-pdf-btn js-rekap-pdf-download" title="Export PDF" aria-label="Export PDF">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h9l5 5v15H6z"/><path d="M15 2v6h5"/><path d="M9 13h6M9 17h4"/></svg>
        </a>
        <button id="btnPrint" class="btn btn-icon btn-outline-secondary rekap-print-btn" title="Print" aria-label="Print">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 9V3h10v6"/><path d="M7 18H5a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M7 14h10v7H7z"/></svg>
        </button>
      </div>
    </div>
</form>

<script>
  document.getElementById('btnPrint').addEventListener('click', function(){
    window.print();
  });

  document.querySelectorAll('.js-rekap-pdf-download').forEach(function (link) {
    link.addEventListener('click', async function (event) {
      event.preventDefault();
      link.setAttribute('aria-busy', 'true');
      link.classList.add('is-loading');

      try {
        const response = await fetch(link.href, {
          credentials: 'same-origin',
          headers: { 'Accept': 'application/pdf' }
        });
        const contentType = response.headers.get('Content-Type') || '';

        if (!response.ok || !contentType.toLowerCase().includes('application/pdf')) {
          throw new Error('Server tidak mengembalikan file PDF.');
        }

        const blob = await response.blob();
        const disposition = response.headers.get('Content-Disposition') || '';
        const filenameMatch = disposition.match(/filename="?([^";]+)"?/i);
        const filename = filenameMatch?.[1] || 'rekap_pengiriman.pdf';
        const downloadUrl = URL.createObjectURL(blob);
        const downloadLink = document.createElement('a');
        downloadLink.href = downloadUrl;
        downloadLink.download = filename;
        document.body.appendChild(downloadLink);
        downloadLink.click();
        window.setTimeout(function () {
          downloadLink.remove();
          URL.revokeObjectURL(downloadUrl);
        }, 60000);
      } catch (error) {
        window.alert('PDF gagal diunduh. Periksa data/filter lalu coba lagi.');
        console.error('Rekap PDF download failed:', error);
      } finally {
        link.removeAttribute('aria-busy');
        link.classList.remove('is-loading');
      }
    });
  });
</script>

<script>
  function pad(n){return n<10? '0'+n: n}
  function toIsoDate(value){
    if(!value) return '';
    const match = value.match(/^\d{2}\/\d{2}\/\d{4}$/);
    if(match){
      const [d, m, y] = value.split('/');
      return y + '-' + pad(m) + '-' + pad(d);
    }
    return value;
  }
  function toDisplayDate(value){
    if(!value) return '';
    const date = new Date(value + 'T00:00:00');
    if(Number.isNaN(date.getTime())) return value;
    return pad(date.getDate()) + '/' + pad(date.getMonth()+1) + '/' + date.getFullYear();
  }
  function syncVisibleDate(value){
    const iso = toIsoDate(value || '');
    const display = toDisplayDate(iso);
    document.getElementById('dateSingle').value = display;
    document.getElementById('date_from').value = iso;
    document.getElementById('date_to').value = iso;
    document.getElementById('dateNative').value = iso;
  }
  function showMode(mode){
    // mode: day | month | year
    document.getElementById('dateGroup').style.display = (mode==='day')? 'flex' : 'none';
    document.getElementById('monthGroup').style.display = (mode==='month')? 'flex' : 'none';
    document.getElementById('yearGroup').style.display = (mode==='year')? 'flex' : 'none';
  }

  (function(){
    const df = document.getElementById('date_from').value;
    const m = document.querySelector('[name=month]').value;
    const y = document.querySelector('[name=year]').value;
    const modeSelect = document.getElementById('modeSelect');
    if(df){ modeSelect.value = 'day'; syncVisibleDate(df); showMode('day'); }
    else if(m){ modeSelect.value = 'month'; showMode('month'); }
    else if(y){ modeSelect.value = 'year'; showMode('year'); }
    else { modeSelect.value = 'day'; showMode('day'); }
  })();

  document.getElementById('dateSingle').addEventListener('blur', function(){
    const v = this.value.trim();
    const iso = toIsoDate(v);
    if(iso){ syncVisibleDate(iso); }
    else { document.getElementById('date_from').value = ''; document.getElementById('date_to').value = ''; document.getElementById('dateNative').value = ''; this.value = ''; }
  });

  document.getElementById('dateTrigger').addEventListener('click', function(){
    const native = document.getElementById('dateNative');
    if(native.showPicker) native.showPicker();
    native.click();
  });

  document.getElementById('dateNative').addEventListener('change', function(){
    syncVisibleDate(this.value);
    document.getElementById('filterForm').requestSubmit();
  });

  document.getElementById('monthNative').addEventListener('change', function(){
    const [selectedYear, selectedMonth] = this.value.split('-');
    document.getElementById('monthSelect').value = selectedMonth ? String(Number(selectedMonth)) : '';
    document.getElementById('yearSelect').value = selectedYear || '';
    document.getElementById('filterForm').requestSubmit();
  });

  document.getElementById('modeSelect').addEventListener('change', function(){
    const mode = this.value;
    const dateInput = document.getElementById('dateSingle');
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    const monthInput = document.querySelector('[name=month]');
    const yearInput = document.querySelector('[name=year]');
    const d = new Date();

    dateInput.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    monthInput.value = '';
    yearInput.value = '';

    if(mode === 'day'){
      const s = d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate());
      syncVisibleDate(s);
    } else if(mode === 'month'){
      monthInput.value = d.getMonth()+1;
      yearInput.value = d.getFullYear();
      document.getElementById('monthNative').value = d.getFullYear()+'-'+pad(d.getMonth()+1);
    } else {
      yearInput.value = d.getFullYear();
    }

    showMode(mode);
  });
</script>

<div class="panel section-panel rekap-table-panel">
  <div class="table-responsive rekap-table-responsive">
    <table class="table-modern rekap-table-modern" data-no-bulk-selection>
      <thead>
        <tr>
          <th>NO</th>
          <th>HARI/TANGGAL</th>
          <th>NAMA_ASAL</th>
          <th>NAMA_TUJUAN</th>
          <th>NAMA_DRIVER</th>
          <th>NO_POL</th>
          <th>NAMA_MATERIAL</th>
          <th>CODE_MATERIAL</th>
          <th>VOLUME</th>
          <th>TOTAL_VOLUME</th>
          <th>TIMBANGAN_CV_SR</th>
          <th>TOTAL_TIMBANGAN_CV_SR</th>
          <th>NO_REC</th>
          <th>JAM_BERANGKAT</th>
          <th>NAMA_TRANSPORTER</th>
        </tr>
      </thead>
      <tbody>
        @php($detailNumber = $detailNumberStart)
        @forelse($rows as $group)
          @foreach($group->details as $detailIndex => $r)
            <tr>
              <td>{{ $detailNumber++ }}</td>
              <td>{{ $r->tanggal }}</td>
              <td>{{ $r->nama_asal }}</td>
              <td>{{ $r->nama_tujuan }}</td>
              <td>{{ $r->nama_driver }}</td>
              <td>{{ $r->no_pol }}</td>
              <td>{{ $r->nama_material }}</td>
              <td>{{ $r->code_material }}</td>
              <td>{{ $r->vol_kg ?: '-' }}</td>
              @if($detailIndex === 0)
                <td rowspan="{{ $group->details->count() }}">{{ number_format($group->total_volume_kg, 0, ',', '.') }}</td>
              @endif
              <td>{{ $r->timbangan_cv_sr ?: '-' }}</td>
              @if($detailIndex === 0)
                <td rowspan="{{ $group->details->count() }}">{{ number_format($group->total_timbangan_cv_sr, 0, ',', '.') }}</td>
              @endif
              <td>{{ $r->no_rec }}</td>
              <td>{{ $r->jam_berangkat }}</td>
              <td>{{ $r->nama_transporter }}</td>
            </tr>
          @endforeach
        @empty
          <tr>
            <td colspan="15" class="text-center text-muted py-4">Belum ada data rekap untuk periode yang dipilih.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{ $rows->links() }}

@endsection
