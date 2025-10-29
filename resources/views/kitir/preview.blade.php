<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu Kalibrasi - {{ $kitir->cal_request_no }}</title>
    <style>
        @page { size: A5 landscape; margin: 10mm; }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
        }   
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .logo {
            width: 45px;
            height: auto;
        }
        .header-text {
            flex: 1;
            text-align: center;
            font-size: 11px;
            line-height: 1.3;
            font-weight: bold;
        }
        .info {
            margin-bottom: 6px;
        }
        .info p {
            margin: 0;
            font-size: 9.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.8px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
        }
        th {
            background: #f3f3f3;
            font-weight: bold;
            text-align: center;
        }
        td {
            vertical-align: top;
            text-align: center;
        }
        td:first-child {
            width: 20px;
        }
        td:nth-child(2) {
            text-align: left;
            width: 160px;
        }
        th[colspan="3"] {
            background: #f3f3f3;
        }
        .catatan {
            margin-top: 8px;
            font-size: 9px;
        }
        .catatan-box {
            border: 1px solid #555;
            min-height: 30px;
            padding: 4px;
        }
        .download {
            text-align: right;
            margin-top: 10px;
        }
        .btn {
            background: #007bff;
            color: #fff;
            text-decoration: none;
            padding: 5px 12px;
            border-radius: 3px;
            font-size: 9.5px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <img src="{{ public_path('images/logo_pall.png') }}" alt="Logo PT PAL" class="logo">
        <div class="header-text">
            KARTU PENGENDALIAN KALIBRASI ALAT UKUR <br>
            MILIK PT. PAL INDONESIA
        </div>
    </div>

    {{-- INFO --}}
    <div class="info">
        <p><strong>Cal. Request NO. :</strong> {{ $kitir->cal_request_no ?? '-' }}</p>
        <p><strong>TGL. PENYELESAIAN KALIBRASI :</strong> {{ $kitir->tgl_penyelesaian ?? '-' }}</p>
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">PROSES</th>
                <th colspan="3">DISERAHKAN</th>
                <th colspan="3">DITERIMA</th>
            </tr>
            <tr>
                <th style="width:55px;">Tanggal</th>
                <th style="width:45px;">Waktu</th>
                <th style="width:65px;">Paraf</th>
                <th style="width:55px;">Tanggal</th>
                <th style="width:45px;">Waktu</th>
                <th style="width:65px;">Paraf</th>
            </tr>
        </thead>
        <tbody>
            @foreach($steps as $no => $pair)
                @php
                    $y1 = $pair->where('posisi', 'Y1')->first();
                    $y2 = $pair->where('posisi', 'Y2')->first();
                @endphp
                <tr>
                    <td>{{ $no }}</td>
                    <td>{{ $proses[$no] ?? '-' }}</td>
                    <td>{{ $y1->tanggal ?? '-' }}</td>
                    <td>{{ $y1->waktu ?? '-' }}</td>
                    <td>{{ $y1->paraf ?? '-' }}</td>
                    <td>{{ $y2->tanggal ?? '-' }}</td>
                    <td>{{ $y2->waktu ?? '-' }}</td>
                    <td>{{ $y2->paraf ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- CATATAN --}}
    <div class="catatan">
        <strong>Catatan :</strong>
        <div class="catatan-box">
            {!! nl2br(e($kitir->catatan ?? '')) !!}
        </div>
    </div>

    {{-- Tombol hanya muncul di preview, tidak di PDF --}}
@if(empty($isPdf))
    <div class="download">
        <a href="{{ route('kitir.download', $kitir->id) }}" class="btn">Download PDF</a>
    </div>

    <div class="mt-8 flex justify-between items-center border-t pt-4">
        <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">← Kembali</a>
    </div>
@endif  

</body>
</html>
