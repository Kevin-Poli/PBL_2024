<?php
// app/Http/Controllers/KegiatanController.php

namespace App\Http\Controllers;

use App\Models\KegiatanModel;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_kegiatan_id' => 'required|exists:m_kategori_kegiatan,kategori_kegiatan_id',
            'nama_kegiatan' => 'required|string|max:200',
            'pic' => 'required|string|max:100',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'deadline' => 'required|date|after:waktu_mulai',
            'progres' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|max:20',
            'deskripsi' => 'nullable|string',
        ]);

        $Kegiatan = KegiatanModel::create([
            'user_id' => auth()->id(), 
            'kategori_kegiatan_id' => $validated['kategori_kegiatan_id'],
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'pic' => $validated['pic'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'deadline' => $validated['deadline'],
            'progres' => $validated['progres'],
            'status' => $validated['status'],
            'deskripsi' => $validated['deskripsi'],
        ]);

        return redirect()->route('Kegiatans.index')
            ->with('success', 'Kegiatan created successfully.');
    }

    public function index()
    {
        $kegiatan = KegiatanModel::with(['anggota', 'agenda'])->get();
        $user = auth()->user();

        return view('kegiatan.index', compact('kegiatan', 'user'));
    }

    public function create()
    {
        return view('Kegiatans.form');
    }

    public function edit(KegiatanModel $Kegiatan)
    {
        return view('Kegiatans.form', compact('Kegiatan'));
    }

    public function destroy(KegiatanModel $Kegiatan)
    {
        $Kegiatan->delete();
        return redirect()->route('Kegiatan.index')
            ->with('success', 'Kegiatan deleted successfully');
    }
    public function storeAgenda(Request $request, $id)
    {
        Agenda::create([
            'kegiatan_id' => $id,
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return back()->with('success', 'Agenda berhasil ditambahkan.');
    }

    public function updateAgenda(Request $request, $id, $agendaId)
    {
        $agenda = Agenda::where('kegiatan_id', $id)->find($agendaId);
        $agenda->update($request->all());

        return back()->with('success', 'Agenda berhasil diperbarui.');
    }

    public function destroyAgenda($id, $agendaId)
    {
        $agenda = Agenda::where('kegiatan_id', $id)->find($agendaId);
        $agenda->delete();

        return back()->with('success', 'Agenda berhasil dihapus.');
    }

    // CRUD Anggota (Khusus PIC)
    public function storeAnggota(Request $request, $id)
    {
        Anggota::create([
            'kegiatan_id' => $id,
            'user_id' => $request->user_id,
        ]);

        return back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function destroyAnggota($id, $anggotaId)
    {
        Anggota::where('kegiatan_id', $id)->where('id', $anggotaId)->delete();

        return back()->with('success', 'Anggota berhasil dihapus.');
    }
}

