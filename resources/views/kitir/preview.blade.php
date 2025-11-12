<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu Kalibrasi - {{ $kitir->cal_request_no }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @page {
            size: A4 landscape; /* dari A5 → A4 agar tampilan lebih lebar */
            margin: 12mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px; /* naik dari 10px */
            color: #000;
            margin: 0;
            background: #f8fafc;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .logo {
            width: 60px; /* lebih besar dari 45px */
            height: auto;
        }
        .header-text {
            flex: 1;
            text-align: center;
            font-size: 14px; /* naik dari 11px */
            line-height: 1.5;
            font-weight: bold;
        }
        .info {
            margin-bottom: 10px;
        }
        .info p {
            margin: 2px 0;
            font-size: 12px; /* lebih besar */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px; /* dari 8.8px → lebih besar */
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 4px; /* lebih longgar dari 3px */
        }
        th {
            background: #f3f3f3;
            font-weight: bold;
            text-align: center;
        }
        td {
            vertical-align: middle;
            text-align: center;
        }
        td:first-child {
            width: 30px; /* kolom nomor lebih lebar */
        }
        td:nth-child(2) {
            text-align: left;
            width: 220px; /* proses lebih lebar */
        }
        th[colspan="3"] {
            background: #f3f3f3;
        }
        .catatan {
            margin-top: 12px;
            font-size: 11px;
        }
        .catatan-box {
            border: 1px solid #555;
            min-height: 50px; /* dari 30px → lebih tinggi */
            padding: 8px;
            background: #fafafa;
        }
    </style>
</head>

<body class="bg-gray-100">

    {{-- Layout utama --}}
    <div class="max-w-7xl mx-auto p-6 sm:p-10">

        <div class="bg-white shadow-lg rounded-lg p-8">

            {{-- HEADER --}}
            <div class="header">
                <img src="{{ public_path('images/logo_pall.png') }}" class="logo">
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
                        <th style="width:75px;">Tanggal</th>
                        <th style="width:65px;">Waktu</th>
                        <th style="width:80px;">Paraf</th>
                        <th style="width:75px;">Tanggal</th>
                        <th style="width:65px;">Waktu</th>
                        <th style="width:80px;">Paraf</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($steps as $no => $pair)
                        @php
                            $y1 = $pair->where('posisi', 'Y1')->first();
                            $y2 = $pair->where('posisi', 'Y2')->first();

                            $tglY1 = $y1?->tanggal ? \Carbon\Carbon::parse($y1->tanggal)->format('d-m-Y') : '-';
                            $tglY2 = $y2?->tanggal ? \Carbon\Carbon::parse($y2->tanggal)->format('d-m-Y') : '-';
                        @endphp
                        <tr>
                            <td>{{ $no }}</td>
                            <td>{{ $proses[$no] ?? '-' }}</td>

                            {{-- DISERAHKAN --}}
                            <td>{{ $tglY1 }}</td>
                            <td>{{ $y1->waktu ?? '-' }}</td>
                            <td><strong>{{ $y1->paraf ?? '-' }}</strong></td>

                            {{-- DITERIMA --}}
                            <td>{{ $tglY2 }}</td>
                            <td>{{ $y2->waktu ?? '-' }}</td>
                            <td><strong>{{ $y2->paraf ?? '-' }}</strong></td>
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

        </div>

        @if(empty($isPdf))
            <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <a href="{{ route('dashboard') }}" class="w-full sm:w-auto bg-gray-600 hover:bg-gray-700 text-white px-5 py-3 rounded-md text-base font-medium shadow-md transition-colors text-center">
                    ← Kembali
                </a>
                <a href="{{ route('kitir.download', $kitir->id) }}" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-md text-base font-medium shadow-md transition-colors text-center">
                    Download PDF
                </a>
            </div>
        @endif

    </div>
</body>
</html>
