@extends('layouts.app')

{{-- Asumsi: layouts.app Anda sudah memuat Tailwind CSS --}}

@section('content')

{{-- Style tambahan untuk media print, agar tombol tidak ikut ter-print --}}
<style>
    @media print {
        .no-print {
            display: none;
        }
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .page-container {
            box-shadow: none;
            margin: 0;
            max-width: none;
            padding: 0;
        }
        .page-background {
            background-color: white;
            padding: 0;
        }
    }
    /* Style untuk table-layout-fixed agar kolom rapi */
    .table-fixed-layout {
        table-layout: fixed;
    }
</style>

{{-- Latar belakang halaman --}}
<div class="bg-gray-100 p-4 sm:p-8 page-background">

    {{-- Kontainer utama, dibuat mirip kertas A4 --}}
    <div class="max-w-4xl mx-auto bg-white p-6 sm:p-10 shadow-lg rounded-lg page-container">

        <!-- 1. HEADER: Logo, Judul, Info Kanan -->
        <div class="flex flex-col sm:flex-row justify-between items-start mb-4 border-b-4 border-black pb-4">

            <!-- Logo Placeholder -->
            <div class="w-24 h-24 border-2 border-black flex items-center justify-center text-gray-600 font-semibold mb-4 sm:mb-0">
                [Logo PAL]
            </div>

            <!-- Judul -->
            <div class="text-center mx-4">
                <h4 class="text-2xl font-bold uppercase">KARTU TUGAS</h4>
            </div>

            <!-- Info Kanan (Nomor Pesanan, Tgl, No. KT) -->
            <div class="text-sm border border-black p-2 min-w-[200px]">
                <div class="flex justify-between">
                    <span>Nomor Pesanan</span>
                    <span>: {{ $surat->nomor_pesanan }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tanggal</span>
                    <span>: {{ $surat->tanggal ? \Carbon\Carbon::parse($surat->tanggal)->format('d-m-Y') : '' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Nomor K. T.</span>
                    <span>: {{ $surat->nomor_kt }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Uraian Pekerjaan -->
        <div class="border border-black mb-4 rounded-md">
            <p class="p-2 border-b border-black font-semibold bg-gray-50 rounded-t-md">Jenis & Uraian Pekerjaan:</p>
            <div class="p-2 min-h-[6rem] text-sm">
                {{ $surat->uraian_pekerjaan }}
            </div>
        </div>

        <!-- 3. Tabel Penera (Bagian 1: Tanggal 1-5) -->
        <table class="w-full border-collapse border border-black text-xs mb-4 table-fixed-layout">
            <thead class="bg-gray-100 text-center align-middle">
                <tr>
                    <td class="border border-black p-1 w-[5%]" rowspan="2">No</td>
                    <td class="border border-black p-1 w-[20%]" rowspan="2">Nama Penera</td>
                    <td class="border border-black p-1 w-[10%]" rowspan="2">NIP</td>
                    <td class="border border-black p-1" colspan="10">Realisasi Tanggal</td>
                </tr>
                <tr>
                    {{-- Loop untuk Tanggal 1-5 --}}
                    @for ($i = 1; $i <= 5; $i++)
                        <td class="border border-black p-1" colspan="2">
                            @php
                                // Ambil tanggal dari penera pertama yang punya isi
                                $tgl = $peneraTugas->firstWhere("realisasi_tgl_b$i", '!=', null)?->{"realisasi_tgl_b$i"};
                            @endphp
                            Tgl: {{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d-m-Y') : '...' }}
                        </td>
                    @endfor
                </tr>
            </thead>
            <tbody>
                <!-- Sub-header N / L -->
                <tr class="bg-gray-50 text-center font-semibold">
                    <td class="border border-black p-1" colspan="3"></td>
                    @for ($i = 1; $i <= 5; $i++)
                        <td class="border border-black p-1">N</td>
                        <td class="border border-black p-1">L</td>
                    @endfor
                </tr>

                <!-- Data Penera untuk Tanggal 1-5 -->
                @forelse($peneraTugas as $index => $p)
                <tr class="text-center align-top">
                    <td class="border border-black p-1">{{ $index + 1 }}</td>
                    <td class="border border-black p-1 text-left">{{ $p->nama_penera }}</td>
                    <td class="border border-black p-1">{{ $p->nip }}</td>
                    @for ($i = 1; $i <= 5; $i++)
                        {{-- Asumsi: 'N' adalah realisasi_bX_c1 dan 'L' adalah realisasi_bX_c2 --}}
                        @php
                            $prefixMap = [
                                'Pak Candra' => 'c',
                                'Pak Rizqi' => 'r',
                                'Pak Rino' => 'd',
                            ];
                            $prefix = $prefixMap[$p->nama_penera] ?? 'c';
                        @endphp
<td class="border border-black p-1">{{ $p["realisasi_b{$i}_{$prefix}1"] ?? '' }}</td>
<td class="border border-black p-1">{{ $p["realisasi_b{$i}_{$prefix}2"] ?? '' }}</td>

                    @endfor
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="text-center p-4 border border-black">Tidak ada data penera tugas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 4. Tabel Penera (Bagian 2: Tanggal 6-10) -->
        <table class="w-full border-collapse border border-black text-xs mb-4 table-fixed-layout">
            <thead class="bg-gray-100 text-center align-middle">
                <tr>
                    <td class="border border-black p-1 w-[5%]" rowspan="2">No</td>
                    <td class="border border-black p-1 w-[20%]" rowspan="2">Nama Penera</td>
                    <td class="border border-black p-1 w-[10%]" rowspan="2">NIP</td>
                    <td class="border border-black p-1" colspan="10">Realisasi Tanggal</td>
                </tr>
                <tr>
                    {{-- Loop untuk Tanggal 6-10 --}}
                    @for ($i = 6; $i <= 10; $i++)
                        <td class="border border-black p-1" colspan="2">
                             @php
                                $tgl = $peneraTugas->firstWhere("realisasi_tgl_b$i", '!=', null)?->{"realisasi_tgl_b$i"};
                            @endphp
                            Tgl: {{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d-m-Y') : '...' }}
                        </td>
                    @endfor
                </tr>
            </thead>
            <tbody>
                <!-- Sub-header N / L -->
                <tr class="bg-gray-50 text-center font-semibold">
                    <td class="border border-black p-1" colspan="3"></td>
                    @for ($i = 6; $i <= 10; $i++)
                        <td class="border border-black p-1">N</td>
                        <td class="border border-black p-1">L</td>
                    @endfor
                </tr>

                <!-- Data Penera untuk Tanggal 6-10 -->
                 @forelse($peneraTugas as $index => $p)
                 <tr class="text-center align-top">
                    <td class="border border-black p-1">{{ $index + 1 }}</td>
                    <td class="border border-black p-1 text-left">{{ $p->nama_penera }}</td>
                    <td class="border border-black p-1">{{ $p->nip }}</td>
                    @for ($i = 6; $i <= 10; $i++)
                        {{-- Asumsi: 'N' adalah realisasi_bX_c1 dan 'L' adalah realisasi_bX_c2 --}}
                        <td class="border border-black p-1">{{ $p["realisasi_b{$i}_c1"] }}</td>
                        <td class="border border-black p-1">{{ $p["realisasi_b{$i}_c2"] }}</td>
                    @endfor
                </tr>
                @empty
                 <tr>
                    <td colspan="13" class="text-center p-4 border border-black">Tidak ada data penera tugas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 5. Catatan Pelaksanaan -->
        <div class="border border-black mb-4 rounded-md">
            <p class="p-2 border-b border-black font-semibold bg-gray-50 rounded-t-md">Catatan Pelaksanaan/Kendala/Progres/DLL:</p>
            <div class="p-2 min-h-[8rem] text-sm space-y-1">
                {{-- Menampilkan catatan dari setiap penera --}}
                @foreach($peneraTugas as $p)
                    @if($p->catatan)
                    <p><strong>{{ $p->nama_penera }}:</strong> {{ $p->catatan }}</p>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- 6. Summary (Rencana/Realisasi) & Tanda Tangan -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">

            <!-- Kolom 1: Tabel Rencana vs Realisasi -->
            <table class="w-full border-collapse border border-black text-center">
                <thead class="bg-gray-100 font-semibold">
                    <tr>
                        <td class="border border-black p-1">&nbsp;</td>
                        <td class="border border-black p-1">Rencana</td>
                        <td class="border border-black p-1">Realisasi</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black p-1 text-left font-semibold">Jam Orang</td>
                        <td class="border border-black p-1">{{ $surat->rencana_jam_orang }}</td>
                        <td class="border border-black p-1">
                            {{ $peneraTugas->firstWhere('realisasi_jam_orang', '!=', null)?->realisasi_jam_orang ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-left font-semibold">Mulai</td>
                        <td class="border border-black p-1">
                            {{ $surat->rencana_mulai ? \Carbon\Carbon::parse($surat->rencana_mulai)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="border border-black p-1">
                            {{ $peneraTugas->firstWhere('realisasi_mulai', '!=', null)?->realisasi_mulai
                                ? \Carbon\Carbon::parse($peneraTugas->firstWhere('realisasi_mulai', '!=', null)->realisasi_mulai)->format('d-m-Y')
                                : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black p-1 text-left font-semibold">Selesai</td>
                        <td class="border border-black p-1">
                            {{ $surat->rencana_selesai ? \Carbon\Carbon::parse($surat->rencana_selesai)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="border border-black p-1">
                            {{ $peneraTugas->firstWhere('realisasi_selesai', '!=', null)?->realisasi_selesai
                                ? \Carbon\Carbon::parse($peneraTugas->firstWhere('realisasi_selesai', '!=', null)->realisasi_selesai)->format('d-m-Y')
                                : '-' }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Kolom 2: Spacer -->
            <div class="hidden sm:block">&nbsp;</div>

            <!-- Kolom 3: Tanda Tangan Kabiro -->
            <div class="text-center mt-4 sm:mt-0">
                <p>Kabiro Kalibrasi</p>
                <div class="h-20"></div> {{-- Spacer untuk Tanda Tangan --}}
                <p class="font-semibold underline">{{ $surat->kabiro_kalibrasi }}</p>
            </div>
        </div>

    </div>

    <!-- Tombol Aksi (di luar kertas) -->
    <div class="mt-6 text-center no-print">
        <a href="{{ route('surat_tugas.preview', ['id' => $surat->id, 'download' => 'true']) }}"
           class="inline-block bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:bg-green-700 transition duration-300">
            ⬇ Download PDF
        </a>
        <a href="{{ route('dashboard') }}"
           class="inline-block bg-gray-500 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:bg-gray-600 transition duration-300 ml-4">
            ⬅ Kembali
        </a>
    </div>

</div>
@endsection