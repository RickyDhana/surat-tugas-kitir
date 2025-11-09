@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">

    {{-- 📋 BAGIAN KITIR --}}
    <section class="mb-12">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
            <h2 class="text-2xl font-bold text-blue-700">📋 Daftar Kitir</h2>
            @auth
                @if(Auth::user()->role === 'admin')
                    <button id="openModalKitir" 
                            class="mt-4 sm:mt-0 bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        + Buat Kitir Baru
                    </button>
                @endif
            @endauth
        </div>

        @if(session('kitir_message'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4">
                {{ session('kitir_message') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">Nomor Cal</th>
                        <th class="px-4 py-2 border">Tanggal Dibuat</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kitirs as $i => $k)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="border px-4 py-2">{{ $k->cal_request_no }}</td>
                            <td class="border px-4 py-2">{{ $k->created_at->format('d-m-Y') }}</td>
                            <td class="border px-4 py-2">
                                @php $status = strtolower(trim($k->status)); @endphp
                                @if($status == 'selesai')
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Selesai</span>
                                @elseif(in_array($status, ['proses', 'in progress', 'in_progress']))
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">In Progress</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">{{ ucfirst($status) }}</span>
                                @endif
                            </td>
                            <td class="border px-4 py-2 text-center">
                                <a href="{{ route('kitir.show', $k->id) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                                <a href="{{ route('kitir.preview', $k->id) }}" class="ml-3 text-gray-600 hover:text-gray-800">Preview</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border px-4 py-3 text-center text-gray-500">Belum ada data Kitir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- 📑 BAGIAN SURAT TUGAS --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
            <h2 class="text-2xl font-bold text-blue-700">📑 Daftar Surat Tugas</h2>
            @auth
                @if(Auth::user()->role === 'kepala_biro')
                    <button id="openModalSurat" 
                            class="mt-4 sm:mt-0 bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        + Buat Surat Tugas Baru
                    </button>
                @endif
            @endauth
        </div>

        @if(session('surat_message'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4">
                {{ session('surat_message') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">Nomor Pesanan</th>
                        <th class="px-4 py-2 border">Tanggal</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratTugas as $i => $s)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="border px-4 py-2">{{ $s->nomor_pesanan }}</td>
                            <td class="border px-4 py-2">
                                {{ $s->tanggal ? \Carbon\Carbon::parse($s->tanggal)->format('d-m-Y') : now()->format('d-m-Y') }}
                            </td>
                            <td class="border px-4 py-2">{{ ucfirst($s->status ?? '-') }}</td>
                            <td class="border px-4 py-2 text-center">
                            {{-- Semua role boleh buka Detail (admin tetap read-only di halaman detail) --}}
                            <a href="{{ route('surat_tugas.show', $s->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Detail</a>

                                {{-- Tombol Preview tampil untuk semua role --}}
                                <a href="{{ route('surat_tugas.preview', $s->id) }}"
                                class="ml-3 text-gray-600 hover:text-gray-800 font-semibold">Preview</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border px-4 py-3 text-center text-gray-500">Belum ada Surat Tugas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

{{-- MODAL BUAT KITIR --}}
<div id="modalKitir" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 m-4">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Buat Kitir Baru</h3>
        <form action="{{ route('kitir.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="cal_request_no" class="block text-sm font-medium text-gray-700 mb-1">Nomor Cal Request</label>
                <input type="text" name="cal_request_no" id="cal_request_no" 
                       class="border border-gray-300 rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" id="closeModalKitir" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL BUAT SURAT TUGAS --}}
<div id="modalSurat" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 m-4">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Buat Surat Tugas Baru</h3>
        <form action="{{ route('surat_tugas.store') }}" method="POST">
    @csrf
    <div class="mb-4">
        <label for="nomor_pesanan" class="block text-sm font-medium text-gray-700 mb-1">Nomor Pesanan</label>
        <input type="text" name="nomor_pesanan" id="nomor_pesanan"
               class="border border-gray-300 rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500"
               required>
    </div>

    <!-- tambah field lain sesuai kebutuhan seperti nomor_kt, uraian_pekerjaan, penera[] dsb -->

    <div class="flex justify-end space-x-3 mt-6">
        <button type="button" id="closeModalSurat" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">Batal</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Simpan</button>
    </div>
</form>
    </div>
</div>

<script>
    // === MODAL KITIR ===
    const openKitir = document.getElementById('openModalKitir');
    const closeKitir = document.getElementById('closeModalKitir');
    const modalKitir = document.getElementById('modalKitir');
    if (openKitir) openKitir.addEventListener('click', () => { modalKitir.classList.remove('hidden'); modalKitir.classList.add('flex'); });
    closeKitir.addEventListener('click', () => { modalKitir.classList.add('hidden'); modalKitir.classList.remove('flex'); });

    // === MODAL SURAT ===
    const openSurat = document.getElementById('openModalSurat');
    const closeSurat = document.getElementById('closeModalSurat');
    const modalSurat = document.getElementById('modalSurat');
    if (openSurat) openSurat.addEventListener('click', () => { modalSurat.classList.remove('hidden'); modalSurat.classList.add('flex'); });
    closeSurat.addEventListener('click', () => { modalSurat.classList.add('hidden'); modalSurat.classList.remove('flex'); });
</script>
@endsection
