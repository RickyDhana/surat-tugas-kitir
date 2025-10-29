@extends('layouts.app')

@section('content')
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Kitir</h2>
            @auth
                @if(Auth::user()->role === 'admin')
                    <button id="openModal" 
                            class="mt-4 sm:mt-0 bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Buat Kitir Baru</span>
                    </button>
                @endif
            @endauth
        </div>

        @if(session('message'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4" role="alert">
                {{ session('message') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Cal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Dibuat</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Selesai</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($kitirs as $i => $k)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $k->cal_request_no }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $k->created_at->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $k->tgl_penyelesaian ? \Carbon\Carbon::parse($k->tgl_penyelesaian)->format('d-m-Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
    @php
        $status = strtolower(trim($k->status));
    @endphp

    @if($status == 'selesai')
        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
            Selesai
        </span>

    @elseif($status == 'in progress' || $status == 'in progres' || $status == 'in_progress' || $status == 'proses')
        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
            In Progress
        </span>

    @elseif($status == 'draft')
        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
            Draft
        </span>

    @else
        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
            {{ ucfirst($status) }}
        </span>
    @endif
</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                <a href="{{ route('kitir.show', $k->id) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                                <a href="{{ route('kitir.preview', $k->id) }}" class="text-gray-600 hover:text-gray-800 ml-4">Preview</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalKitir" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 m-4">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Buat Kitir Baru</h3>
        <form action="{{ route('kitir.store') }}" method="POST" id="formKitir">
            @csrf
            <div class="mb-4">
                <label for="cal_request_no" class="block text-sm font-medium text-gray-700 mb-1">Nomor Call Request</label>
                <input type="text" name="cal_request_no" id="cal_request_no" 
                       class="border border-gray-300 rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       placeholder="Masukkan nomor call request..." required>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" id="closeModal" 
                        class="bg-gray-200 text-gray-800 font-semibold px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" 
                        class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const openModal = document.getElementById('openModal');
    const closeModal = document.getElementById('closeModal');
    const modal = document.getElementById('modalKitir');

    // Cek jika tombol openModal ada (hanya untuk admin)
    if (openModal) {
        openModal.addEventListener('click', () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    }

    closeModal.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });
</script>
@endsection