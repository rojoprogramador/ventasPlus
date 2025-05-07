<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'usuario_id',
        'accion',
        'descripcion',
        'modelo',
        'modelo_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
