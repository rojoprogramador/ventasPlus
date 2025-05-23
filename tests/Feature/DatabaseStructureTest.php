<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseStructureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function productos_table_has_required_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('productos', [
                'id', 'codigo', 'nombre', 'descripcion', 'precio_compra',
                'precio_venta', 'stock', 'stock_minimo', 'categoria_id',
                'estado', 'permite_descuentos'
            ]),
            'La tabla productos no tiene todas las columnas requeridas'
        );
    }    /** @test */
    public function clientes_table_has_required_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('clientes', [
                'id', 'nombre', 'telefono', 'email', 'direccion',
                'documento', 'tipo_documento', 'estado'
            ]),
            'La tabla clientes no tiene todas las columnas requeridas'
        );
    }

    /** @test */
    public function ventas_table_has_required_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('ventas', [
                'id', 'cliente_id', 'usuario_id', 'total',
                'fecha', 'estado'
            ]),
            'La tabla ventas no tiene todas las columnas requeridas'
        );
    }

    /** @test */
    public function roles_table_has_required_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('roles', [
                'id', 'nombre', 'descripcion'
            ]),
            'La tabla roles no tiene todas las columnas requeridas'
        );
    }

    /** @test */
    public function permisos_table_has_required_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('permisos', [
                'id', 'nombre', 'descripcion'
            ]),
            'La tabla permisos no tiene todas las columnas requeridas'
        );
    }

    /** @test */
    public function foreign_keys_are_correctly_defined()
    {
        // Verificar que las claves foráneas estén definidas correctamente
        $this->assertTrue(
            $this->hasForeignKey('ventas', 'cliente_id', 'clientes', 'id'),
            'La clave foránea cliente_id en la tabla ventas no está correctamente definida'
        );

        $this->assertTrue(
            $this->hasForeignKey('productos', 'categoria_id', 'categorias', 'id'),
            'La clave foránea categoria_id en la tabla productos no está correctamente definida'
        );

        $this->assertTrue(
            $this->hasForeignKey('users', 'rol_id', 'roles', 'id'),
            'La clave foránea rol_id en la tabla users no está correctamente definida'
        );
    }

    /**
     * Verifica si existe una clave foránea entre dos tablas
     * 
     * @param string $table Tabla donde está definida la clave foránea
     * @param string $column Columna que es clave foránea
     * @param string $referencedTable Tabla referenciada
     * @param string $referencedColumn Columna referenciada
     * @return bool
     */
    private function hasForeignKey($table, $column, $referencedTable, $referencedColumn)
    {
        $conn = Schema::getConnection();
        $foreignKeys = $conn->getDoctrineSchemaManager()->listTableForeignKeys($table);
        
        foreach ($foreignKeys as $foreignKey) {
            $columns = $foreignKey->getLocalColumns();
            
            if (count($columns) !== 1 || $columns[0] !== $column) {
                continue;
            }
            
            $foreignTable = $foreignKey->getForeignTableName();
            $foreignColumns = $foreignKey->getForeignColumns();
            
            if ($foreignTable === $referencedTable && 
                count($foreignColumns) === 1 && 
                $foreignColumns[0] === $referencedColumn) {
                return true;
            }
        }
        
        return false;
    }
}
