@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="text-center mb-4">
        <h4><strong>KARTU TUGAS</strong></h4>
        <p>Form Code: {{ $surat->form_code ?? 'F.02 (2.LB.022)' }}</p>
        <hr>
    </div>

    <div class="mb-3">
        <table class="table table-bordered">
            <tr><th>Nomor Pesanan</th><td>{{ $surat->nomor_pesanan }}</td></tr>
            <tr><th>Nomor KT</th><td>{{ $surat->nomor_kt }}</td></tr>
            <tr><th>Tanggal</th><td>{{ $surat->tanggal }}</td></tr>
            <tr><th>Uraian Pekerjaan</th><td>{{ $surat->uraian_pekerjaan }}</td></tr>
            <tr><th>Rencana Jam Orang</th><td>{{ $surat->rencana_jam_orang }}</td></tr>
            <tr><th>Mulai</th><td>{{ $surat->rencana_mulai }}</td></tr>
            <tr><th>Selesai</th><td>{{ $surat->rencana_selesai }}</td></tr>
        </table>
    </div>

    <h5>Daftar Penera</h5>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Nama Penera</th>
                <th>NIP</th>
                <th>Catatan</th>
                <th>Realisasi Jam</th>
                <th>Mulai</th>
                <th>Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peneraTugas as $p)
                <tr>
                    <td>{{ $p->nama_penera }}</td>
                    <td>{{ $p->nip }}</td>
                    <td>{{ $p->catatan }}</td>
                    <td>{{ $p->realisasi_jam_orang }}</td>
                    <td>{{ $p->realisasi_mulai }}</td>
                    <td>{{ $p->realisasi_selesai }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 text-center">
        <a href="{{ route('surat_tugas.preview', ['id' => $surat->id, 'download' => 'true']) }}" class="btn btn-success">
            ⬇ Download PDF
        </a>
        <a href="{{ route('surat_tugas.index') }}" class="btn btn-secondary">
            ⬅ Kembali
        </a>
    </div>
</div>
@endsection
