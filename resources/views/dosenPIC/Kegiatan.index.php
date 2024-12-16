@extends('layouts.app')

@section('content')
<h1>Daftar Kegiatan</h1>

@foreach ($kegiatan as $item)
    <div class="card">
        <h3>{{ $item->nama_kegiatan }}</h3>
        <p>Progress: {{ $item->progress }}</p>
        <p>Tanggal: {{ $item->tanggal_mulai }} - {{ $item->tanggal_selesai }}</p>
        <p>Tahun: {{ $item->tahun }}</p>

        <h4>Agenda</h4>
        @foreach ($item->agenda as $agenda)
            <p>{{ $agenda->name }} ({{ $agenda->start_date }} - {{ $agenda->end_date }})</p>
            
            @if ($user->role == 'pic')
                <form action="{{ route('agenda.destroy', [$item->id, $agenda->id]) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit">Hapus</button>
                </form>
            @endif
        @endforeach

        @if ($user->role == 'pic')
            <form action="{{ route('agenda.store', $item->id) }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Nama Agenda">
                <input type="date" name="start_date">
                <input type="date" name="end_date">
                <button type="submit">Tambah Agenda</button>
            </form>
        @endif
    </div>
@endforeach
@endsection
