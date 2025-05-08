<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permisos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Get the roles for the permiso.
     */
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_permiso');
    }

    /**
     * Get the users that have this permiso.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permiso')
                    ->withPivot('habilitado')
                    ->withTimestamps();
    }
}
