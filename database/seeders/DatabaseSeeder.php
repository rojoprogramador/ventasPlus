<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */    public function run()
    {
        $this->call([
            PermisosSeeder::class,
            RolesSeeder::class,
            RolPermisoSeeder::class,
            RolesYUsuariosSeeder::class,
            ConfiguracionSeeder::class,
            CategoriaSeeder::class,
            ProductoSeeder::class,
            // ProductosSeeder::class,  // Comentado para evitar conflictos con códigos duplicados
            ClientesSeeder::class,
            // ExportarDatosPermisoSeeder::class  // Comentado porque el permiso ya está en PermisosSeeder
        ]);
    }
}
