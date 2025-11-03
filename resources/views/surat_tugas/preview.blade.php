@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📜 Preview Surat Tugas</h3>
        <a href="{{ route('surat_tugas.downloadPdf', $suratTugas->id) }}" class="btn btn-primary">
            ⬇️ Download PDF
        </a>
    </div>

    <div class="card shadow-sm p-4">
        <div class="text-center mb-4">
            <h5><strong>SURAT TUGAS</strong></h5>
            <p>Nomor: {{ $suratTugas->nomor_surat ?? '—' }}</p>
        </div>

        <p>Yang bertanda tangan di bawah ini menugaskan kepada:</p>

        <table class="table table-borderless w-75 mx-auto">
            <tr>
                <td width="35%">Nama Petugas</td>
                <td>: {{ $suratTugas->nama_petugas ?? '—' }}</td>
            </tr>
            <tr>
                <td>Tanggal Pelaksanaan</td>
                <td>: 
                    @if($suratTugas->tanggal_pelaksanaan)
                        {{ \Carbon\Carbon::parse($suratTugas->tanggal_pelaksanaan)->translatedFormat('d F Y') }}
                    @else
                        —
                    @endif
                </td>
            </tr>
            <tr>
                <td>Tujuan Tugas</td>
                <td>: {{ $suratTugas->tujuan_tugas ?? '—' }}</td>
            </tr>
        </table>

        <div class="mt-5 text-end">
            <p><strong>Kepala Biro</strong></p>
            <br><br><br>
            <p>( {{ $suratTugas->kepala_biro ?? '_________________' }} )</p>
        </div>
    </div>


    <a href="{{ route('dashboard') }}" class="btn btn-dark mt-3">⬅️ Kembali</a>

</div>
@endsection
