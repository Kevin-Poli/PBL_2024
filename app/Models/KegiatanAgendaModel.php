<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanAgendaModel extends Model
{
    protected $table = 't_kegiatan_agenda';
    protected $primaryKey = 'agenda_id';
    
    protected $fillable = [
        'kegiatan_id',
        'deadline',
        'lokasi', 
        'progres',
        'keterangan'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'progres' => 'decimal:2'
    ];

    // Relasi ke tabel t_kegiatan
    public function kegiatan()
    {
        return $this->belongsTo(KegiatanModel::class, 'kegiatan_id', 'kegiatan_id');
    }
}
