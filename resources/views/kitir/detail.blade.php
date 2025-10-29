@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    $role = Auth::user()->role;

    // Hak akses
    $allowed = [
        'admin' => [
            ['step' => 1, 'pos' => 'Y1'], ['step' => 1, 'pos' => 'Y2'],
            ['step' => 6, 'pos' => 'Y2'],
            ['step' => 7, 'pos' => 'Y1'], ['step' => 7, 'pos' => 'Y2'],
        ],
        'kepala_biro' => [
            ['step' => 2, 'pos' => 'Y1'],
            ['step' => 5, 'pos' => 'Y2'],
            ['step' => 6, 'pos' => 'Y1'],
        ],
        'penera' => [
            ['step' => 2, 'pos' => 'Y2'],
            ['step' => 3, 'pos' => 'Y1'], ['step' => 3, 'pos' => 'Y2'],
            ['step' => 4, 'pos' => 'Y1'], ['step' => 4, 'pos' => 'Y2'],
            ['step' => 5, 'pos' => 'Y1'],
        ],
    ];

    if (!function_exists('canFillStep')) {
        function canFillStep($role, $stepNo, $pos, $allowed) {
            foreach ($allowed[$role] ?? [] as $rule) {
                if ($rule['step'] == $stepNo && $rule['pos'] == $pos) return true;
            }
            return false;
        }
    }
@endphp

<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-6 text-gray-800">Detail Kitir: {{ $kitir->cal_request_no }}</h1>

    {{-- flash --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 text-green-700 px-4 py-2 rounded">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-100 text-red-700 px-4 py-2 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow rounded-xl p-6">
        <table class="w-full border border-gray-200 text-sm text-left">
            <thead class="bg-gray-100">
                <tr class="text-center">
                    <th class="px-3 py-2 border w-10">No.</th>
                    <th class="px-3 py-2 border">Proses</th>
                    <th class="px-3 py-2 border">DISERAHKAN</th>
                    <th class="px-3 py-2 border">DITERIMA</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($steps as $no => $pair)
                    @php
                        $y1 = $pair->where('posisi', 'Y1')->first();
                        $y2 = $pair->where('posisi', 'Y2')->first();

                        // apakah previous step sudah selesai (untuk mengaktifkan Y1)
                        $prevStepDone = true;
                        if ($no > 1) {
                            $prevY2 = $steps[$no - 1]->where('posisi', 'Y2')->first();
                            if (!$prevY2 || !$prevY2->tanggal) {
                                $prevStepDone = false;
                            }
                        }

                        // apakah currentY1 sudah selesai (untuk mengaktifkan Y2)
                        $currentY1Done = ($y1 && $y1->tanggal !== null);
                    @endphp

                    <tr class="border-b text-center">
                        <td class="border px-3 py-2">{{ $no }}</td>
                        <td class="border px-3 py-2 text-left">{{ $proses[$no] ?? '-' }}</td>

                        {{-- DISERAHKAN (Y1) --}}
                        <td class="border px-3 py-2">
                            @if ($y1 && $y1->tanggal)
                                <div class="text-xs text-green-700">
                                    <div>{{ $y1->tanggal }}</div>
                                    <div>{{ $y1->paraf }}</div>
                                </div>
                            @elseif (canFillStep($role, $no, 'Y1', $allowed))
                                {{-- Y1 hanya bisa diisi jika prevStepDone = true --}}
                                <form method="POST" action="{{ route('kitir.step.store', $kitir->id) }}">
                                    @csrf
                                    <input type="hidden" name="step_id" value="{{ $y1->id }}">
                                    <button type="submit"
                                        @if(!$prevStepDone) disabled @endif
                                        class="px-3 py-1 rounded text-xs font-medium
                                            {{ $prevStepDone ? 'bg-blue-500 hover:bg-blue-600 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
                                        Selesai
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        {{-- DITERIMA (Y2) --}}
                        <td class="border px-3 py-2">
                            @if ($y2 && $y2->tanggal)
                                <div class="text-xs text-green-700">
                                    <div>{{ $y2->tanggal }}</div>
                                    <div>{{ $y2->paraf }}</div>
                                </div>
                            @elseif (canFillStep($role, $no, 'Y2', $allowed))
                                {{-- Y2 hanya aktif jika currentY1Done = true --}}
                                <form method="POST" action="{{ route('kitir.step.store', $kitir->id) }}">
                                    @csrf
                                    <input type="hidden" name="step_id" value="{{ $y2->id }}">
                                    <button type="submit"
                                        @if(!$currentY1Done) disabled @endif
                                        class="px-3 py-1 rounded text-xs font-medium
                                            {{ $currentY1Done ? 'bg-green-500 hover:bg-green-600 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
                                        Selesai
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Catatan tetap (semua user boleh isi) --}}
        <div class="mt-8 border-t pt-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Catatan</h2>
            <form method="POST" action="{{ route('kitir.catatan.store', $kitir->id) }}">
                @csrf
                <textarea name="catatan" rows="4"
                    class="w-full border rounded-lg p-3 text-sm focus:ring focus:ring-blue-200"
                    placeholder="Tulis catatan tambahan di sini...">{{ old('catatan', $kitir->catatan) }}</textarea>
                <div class="mt-3 text-right">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">
                        Simpan Catatan
                    </button>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="mt-8 flex justify-between items-center border-t pt-4">
            <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">← Kembali</a>
            <div>
                <span class="text-gray-700">Status:</span>
                <span class="font-semibold {{ $kitir->status === 'selesai' ? 'text-green-600' : 'text-yellow-600' }}">
                    {{ ucfirst($kitir->status) }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
