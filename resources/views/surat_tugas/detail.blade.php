@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-xl font-bold mb-4">
        📄 Detail Surat Tugas
    </h2>

    {{-- Pesan sukses --}}
    @if(session('msg'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('msg') }}
        </div>
    @endif

{{-- ========================================================= --}}
{{-- FORM UNTUK KABIRO --}}
{{-- ========================================================= --}}
@if($role === 'kepala_biro')
    <form action="{{ isset($surat->id) ? route('surat_tugas.update', $surat->id) : route('surat_tugas.store') }}" method="POST">
        @csrf
        @if(isset($surat->id))
            @method('PUT')
        @endif

        <div class="bg-white shadow rounded p-5 mb-5">
            <h3 class="font-semibold mb-3 text-lg">🧾 Data Surat Tugas</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Nomor Pesanan</label>
                    <input type="text" name="nomor_pesanan"
                        value="{{ old('nomor_pesanan', $surat->nomor_pesanan ?? '') }}"
                        class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium">Nomor KT</label>
                    <input type="text" name="nomor_kt"
                        value="{{ old('nomor_kt', $surat->nomor_kt ?? '') }}"
                        class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium">Uraian Pekerjaan</label>
                    <input type="text" name="uraian_pekerjaan"
                        value="{{ old('uraian_pekerjaan', $surat->uraian_pekerjaan ?? '') }}"
                        class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium">Rencana Jam Orang</label>
                    <input type="text" name="rencana_jam_orang"
                        value="{{ old('rencana_jam_orang', $surat->rencana_jam_orang ?? '') }}"
                        class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium">Rencana Mulai</label>
                    <input type="date" name="rencana_mulai"
                        value="{{ old('rencana_mulai', $surat->rencana_mulai ?? '') }}"
                        class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium">Rencana Selesai</label>
                    <input type="date" name="rencana_selesai"
                        value="{{ old('rencana_selesai', $surat->rencana_selesai ?? '') }}"
                        class="w-full border rounded p-2">
                </div>
            </div>

            {{-- Pilih Penera --}}
            <div class="mt-3">
                <label class="block text-sm font-medium mb-2">Pilih Nama Penera (maksimal 3)</label>

                @php
                    $peneraList = ['Pak Candra', 'Pak Rizqi', 'Pak Rino'];
                    $selectedPenera = $peneraTugas ? $peneraTugas->pluck('nama_penera')->toArray() : [];
                @endphp

                <div class="flex flex-col gap-2">
                    @foreach($peneraList as $p)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="penera[]" value="{{ $p }}"
                                class="form-checkbox h-4 w-4 text-blue-600"
                                {{ in_array($p, $selectedPenera) ? 'checked' : '' }}>
                            <span class="ml-2">{{ $p }}</span>
                        </label>
                    @endforeach
                </div>

                <small class="text-gray-500">NIP akan otomatis diisi sesuai nama yang dipilih.</small>
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                💾 Simpan
            </button>
        </div>
    </form>
@endif


    {{-- ========================================================= --}}
    {{-- FORM UNTUK PENERA --}}
    {{-- ========================================================= --}}
    @if($role === 'penera')
        <form action="{{ route('surat_tugas.realisasi.update', $surat->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white shadow rounded p-5">
                <h3 class="font-semibold mb-3 text-lg text-center">🪪 Kartu Tugas</h3>

                {{-- 💡 Tabel Realisasi Tanggal --}}
                <div class="mt-4">
                    <h4 class="font-semibold mb-2 text-md">📆 Realisasi Tanggal dan Jam Orang</h4>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border text-center text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2">Tgl</th>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <th colspan="2" class="border p-2">B{{ $i }}</th>
                                    @endfor
                                </tr>
                                <tr>
                                    <th class="border p-1">N/L</th>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <th class="border p-1">N</th>
                                        <th class="border p-1">L</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $namaPeneraDipilih = collect($peneraTugas)->pluck('nama_penera')->toArray();
                                    $mapKode = [
                                        'Pak Candra' => 'Candra',
                                        'Pak Rizqi' => 'Rizqi',
                                        'Pak Rino' => 'Rino',
                                    ];
                                    $rows = [];
                                    foreach ($namaPeneraDipilih as $p) {
                                        if (isset($mapKode[$p])) $rows[$mapKode[$p]] = $mapKode[$p];
                                    }
                                @endphp
                                @foreach ($rows as $prefix)
                                    <tr>
                                        <td class="border p-1 font-semibold">{{ $prefix }}</td>
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td class="border p-1">
                                                <input type="text" name="realisasi_b{{ $i }}_{{ strtolower($prefix) }}1"
                                                    value="{{ old('realisasi_b'.$i.'_'.strtolower($prefix).'1', $peneraTugas->first()->{'realisasi_b'.$i.'_'.strtolower($prefix).'1'} ?? '') }}"
                                                    class="w-full border rounded p-1 text-center">
                                            </td>
                                            <td class="border p-1">
                                                <input type="text" name="realisasi_b{{ $i }}_{{ strtolower($prefix) }}2"
                                                    value="{{ old('realisasi_b'.$i.'_'.strtolower($prefix).'2', $peneraTugas->first()->{'realisasi_b'.$i.'_'.strtolower($prefix).'2'} ?? '') }}"
                                                    class="w-full border rounded p-1 text-center">
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="mt-4">
                    <label class="block text-sm">Catatan Pelaksanaan / Kendala / Progres / DLL</label>
                    <textarea name="catatan" rows="5" maxlength="700"
                        class="w-full border rounded p-2">{{ old('catatan', $peneraTugas->first()->catatan ?? '') }}</textarea>
                </div>

                {{-- Jam Orang + Mulai + Selesai --}}
                <div class="grid grid-cols-3 gap-4 mt-5">
                    <div>
                        <label class="block text-sm">Jam Orang</label>
                        <input type="text" name="realisasi_jam_orang"
                            value="{{ old('realisasi_jam_orang', $peneraTugas->first()->realisasi_jam_orang ?? '') }}"
                            class="w-full border rounded p-2" placeholder="Masukkan jumlah jam">
                    </div>

                    <div>
                        <label class="block text-sm">Mulai</label>
                        <input type="date" name="realisasi_mulai"
                            value="{{ old('realisasi_mulai', $peneraTugas->first()->realisasi_mulai ?? '') }}"
                            class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label class="block text-sm">Selesai</label>
                        <input type="date" name="realisasi_selesai"
                            value="{{ old('realisasi_selesai', $peneraTugas->first()->realisasi_selesai ?? '') }}"
                            class="w-full border rounded p-2">
                    </div>
                </div>

                {{-- Tombol Simpan --}}
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        💾 Simpan
                    </button>
                </div>
            </div>
        </form>
    @endif

</div>
@endsection
