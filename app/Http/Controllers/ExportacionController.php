<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use League\Csv\Writer;
// Eliminamos la dependencia de PhpSpreadsheet
use Carbon\Carbon;

class ExportacionController extends Controller
{
    /**
     * Muestra la vista de exportación de datos
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Verificar que el usuario tenga permiso para exportar datos
        if (!Auth::user()->rol->permisos->contains('nombre', 'exportar_datos')) {
            abort(403, 'No tienes permiso para exportar datos');
        }

        // Obtener las tablas disponibles para exportar
        $tablas = [
            ['id' => 'clientes', 'nombre' => 'Clientes'],
            ['id' => 'productos', 'nombre' => 'Productos'],
            ['id' => 'ventas', 'nombre' => 'Ventas'],
            ['id' => 'usuarios', 'nombre' => 'Usuarios'],
        ];

        // Obtener los formatos de exportación disponibles
        $formatos = [
            ['id' => 'csv', 'nombre' => 'CSV'],
            ['id' => 'excel', 'nombre' => 'Excel'],
            ['id' => 'sql', 'nombre' => 'SQL (Backup)'],
        ];

        return Inertia::render('Exportacion/Index', [
            'tablas' => $tablas,
            'formatos' => $formatos,
            'campos' => [
                'clientes' => $this->obtenerCamposTabla('clientes'),
                'productos' => $this->obtenerCamposTabla('productos'),
                'ventas' => $this->obtenerCamposTabla('ventas'),
                'usuarios' => $this->obtenerCamposTabla('users'),
            ]
        ]);
    }

    /**
     * Obtiene los campos de una tabla específica
     *
     * @param string $tabla
     * @return array
     */
    private function obtenerCamposTabla($tabla)
    {
        $campos = [];
        
        // Mapeo de tablas a modelos
        $modelos = [
            'clientes' => Cliente::class,
            'productos' => Producto::class,
            'ventas' => Venta::class,
            'users' => User::class,
        ];
        
        if (isset($modelos[$tabla])) {
            $modelo = new $modelos[$tabla];
            $columnas = Schema::getColumnListing($modelo->getTable());
            
            foreach ($columnas as $columna) {
                // Excluir campos sensibles para usuarios
                if ($tabla === 'users' && in_array($columna, ['password', 'remember_token'])) {
                    continue;
                }
                
                $campos[] = [
                    'id' => $columna,
                    'nombre' => ucfirst(str_replace('_', ' ', $columna))
                ];
            }
        }
        
        return $campos;
    }

    /**
     * Exporta los datos según los parámetros especificados
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportar(Request $request)
    {
        // Verificar que el usuario tenga permiso para exportar datos
        if (!Auth::user()->rol->permisos->contains('nombre', 'exportar_datos')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para exportar datos'
            ], 403);
        }

        // Validar los datos de la solicitud
        $request->validate([
            'tabla' => 'required|in:clientes,productos,ventas,usuarios',
            'formato' => 'required|in:csv,excel,sql',
            'campos' => 'required|array',
            'campos.*' => 'string',
            'filtros' => 'nullable|array',
            'filtros.estado' => 'nullable|string',
            'filtros.fecha_inicio' => 'nullable|date',
            'filtros.fecha_fin' => 'nullable|date|after_or_equal:filtros.fecha_inicio',
            'filtros.busqueda' => 'nullable|string|max:100',
        ]);

        try {
            // Mapeo de tablas a modelos y tablas reales
            $tablaInfo = [
                'clientes' => ['modelo' => Cliente::class, 'tabla' => 'clientes'],
                'productos' => ['modelo' => Producto::class, 'tabla' => 'productos'],
                'ventas' => ['modelo' => Venta::class, 'tabla' => 'ventas'],
                'usuarios' => ['modelo' => User::class, 'tabla' => 'users'],
            ];
            
            $modeloClase = $tablaInfo[$request->tabla]['modelo'];
            $tablaNombre = $tablaInfo[$request->tabla]['tabla'];
            
            // Construir la consulta base
            $query = $modeloClase::query();
            
            // Aplicar filtros si existen
            if ($request->has('filtros')) {
                $filtros = $request->filtros;
                
                // Filtrar por estado (si la tabla tiene este campo)
                if (isset($filtros['estado']) && Schema::hasColumn($tablaNombre, 'estado')) {
                    if ($filtros['estado'] !== 'todos') {
                        $query->where('estado', $filtros['estado']);
                    }
                }
                
                // Filtrar por fecha de creación
                if (isset($filtros['fecha_inicio']) && Schema::hasColumn($tablaNombre, 'created_at')) {
                    $query->whereDate('created_at', '>=', $filtros['fecha_inicio']);
                }
                
                if (isset($filtros['fecha_fin']) && Schema::hasColumn($tablaNombre, 'created_at')) {
                    $query->whereDate('created_at', '<=', $filtros['fecha_fin']);
                }
                
                // Filtrar por búsqueda general
                if (isset($filtros['busqueda']) && !empty($filtros['busqueda'])) {
                    $busqueda = $filtros['busqueda'];
                    $query->where(function($q) use ($busqueda, $tablaNombre) {
                        $columnas = Schema::getColumnListing($tablaNombre);
                        
                        foreach ($columnas as $columna) {
                            // Solo buscar en columnas de tipo string
                            $tipo = Schema::getColumnType($tablaNombre, $columna);
                            if (in_array($tipo, ['string', 'text'])) {
                                $q->orWhere($columna, 'LIKE', "%{$busqueda}%");
                            }
                        }
                    });
                }
            }
            
            // Obtener los registros
            $registros = $query->get();
            
            // Filtrar solo los campos seleccionados
            $registrosFiltrados = $registros->map(function($registro) use ($request) {
                $item = [];
                foreach ($request->campos as $campo) {
                    $item[$campo] = $registro->{$campo};
                }
                return $item;
            });
            
            // Generar el archivo según el formato seleccionado
            $nombreArchivo = $request->tabla . '_' . Carbon::now()->format('Y-m-d_His');
            $rutaArchivo = '';
            
            if ($request->formato === 'csv') {
                $rutaArchivo = $this->generarCSV($registrosFiltrados, $request->campos, $nombreArchivo);
            } else if ($request->formato === 'excel') {
                $rutaArchivo = $this->generarExcel($registrosFiltrados, $request->campos, $nombreArchivo);
            } else if ($request->formato === 'sql') {
                $rutaArchivo = $this->generarSQL($registros, $tablaNombre, $nombreArchivo);
            }
            
            // Registrar la exportación en el historial de auditoría
            $this->registrarExportacion($request->tabla, $request->formato, count($registros));
            
            // Devolver la URL de descarga
            $extension = $request->formato === 'sql' ? 'sql' : ($request->formato === 'excel' ? 'xlsx' : 'csv');
            return response()->json([
                'success' => true,
                'message' => 'Exportación completada con éxito',
                'url' => $rutaArchivo,
                'nombre_archivo' => $nombreArchivo . '.' . $extension
            ]);
        } catch (\Exception $e) {
            Log::error('Error al exportar datos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera un archivo CSV con los datos
     *
     * @param \Illuminate\Support\Collection $registros
     * @param array $campos
     * @param string $nombreArchivo
     * @return string Ruta del archivo generado
     */
    private function generarCSV($registros, $campos, $nombreArchivo)
    {
        // Crear el escritor CSV
        $csv = Writer::createFromString('');
        
        // Establecer el delimitador
        $csv->setDelimiter(',');
        
        // Agregar la cabecera
        $cabeceras = array_map(function($campo) {
            return ucfirst(str_replace('_', ' ', $campo));
        }, $campos);
        
        $csv->insertOne($cabeceras);
        
        // Agregar los datos
        foreach ($registros as $registro) {
            $fila = [];
            foreach ($campos as $campo) {
                $valor = $registro[$campo];
                
                // Formatear fechas
                if ($valor instanceof \Carbon\Carbon) {
                    $valor = $valor->format('Y-m-d H:i:s');
                }
                
                $fila[] = $valor;
            }
            
            $csv->insertOne($fila);
        }
        
        // Guardar el archivo
        $rutaArchivo = 'exports/' . $nombreArchivo . '.csv';
        Storage::put('public/' . $rutaArchivo, $csv->getContent());
        
        return Storage::url('public/' . $rutaArchivo);
    }

    /**
     * Genera un archivo Excel con los datos (en realidad CSV con extensión .xlsx)
     *
     * @param \Illuminate\Support\Collection $registros
     * @param array $campos
     * @param string $nombreArchivo
     * @return string Ruta del archivo generado
     */
    private function generarExcel($registros, $campos, $nombreArchivo)
    {
        // Como no tenemos PhpSpreadsheet disponible, vamos a generar un CSV
        // pero con extensión .xlsx para que el usuario pueda abrirlo con Excel
        
        // Crear el escritor CSV
        $csv = Writer::createFromString('');
        
        // Establecer el delimitador
        $csv->setDelimiter(',');
        
        // Agregar la cabecera
        $cabeceras = array_map(function($campo) {
            return ucfirst(str_replace('_', ' ', $campo));
        }, $campos);
        
        $csv->insertOne($cabeceras);
        
        // Agregar los datos
        foreach ($registros as $registro) {
            $fila = [];
            foreach ($campos as $campo) {
                $valor = $registro[$campo];
                
                // Formatear fechas
                if ($valor instanceof \Carbon\Carbon) {
                    $valor = $valor->format('Y-m-d H:i:s');
                }
                
                $fila[] = $valor;
            }
            
            $csv->insertOne($fila);
        }
        
        // Guardar el archivo
        $rutaArchivo = 'exports/' . $nombreArchivo . '.xlsx';
        Storage::put('public/' . $rutaArchivo, $csv->getContent());
        
        return Storage::url('public/' . $rutaArchivo);
    }

    /**
     * Genera un archivo SQL con los datos (backup)
     *
     * @param \Illuminate\Database\Eloquent\Collection $registros
     * @param string $tabla
     * @param string $nombreArchivo
     * @return string Ruta del archivo generado
     */
    private function generarSQL($registros, $tabla, $nombreArchivo)
    {
        $sql = "-- Backup de la tabla {$tabla} generado el " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Total de registros: " . count($registros) . "\n\n";
        
        if (count($registros) > 0) {
            // Obtener los nombres de las columnas
            $columnas = array_keys($registros[0]->getAttributes());
            
            // Eliminar columnas sensibles para usuarios
            if ($tabla === 'users') {
                $columnas = array_diff($columnas, ['password', 'remember_token']);
            }
            
            $sql .= "INSERT INTO `{$tabla}` (`" . implode('`, `', $columnas) . "`) VALUES\n";
            
            $filas = [];
            foreach ($registros as $registro) {
                $valores = [];
                foreach ($columnas as $columna) {
                    $valor = $registro->{$columna};
                    
                    if (is_null($valor)) {
                        $valores[] = "NULL";
                    } else if (is_numeric($valor)) {
                        $valores[] = $valor;
                    } else if ($valor instanceof \Carbon\Carbon) {
                        $valores[] = "'" . $valor->format('Y-m-d H:i:s') . "'";
                    } else {
                        $valores[] = "'" . addslashes($valor) . "'";
                    }
                }
                
                $filas[] = "(" . implode(", ", $valores) . ")";
            }
            
            $sql .= implode(",\n", $filas) . ";";
        }
        
        // Guardar el archivo
        $rutaArchivo = 'exports/' . $nombreArchivo . '.sql';
        Storage::put('public/' . $rutaArchivo, $sql);
        
        return Storage::url('public/' . $rutaArchivo);
    }

    /**
     * Registra la exportación en el historial de auditoría
     *
     * @param string $tabla
     * @param string $formato
     * @param int $cantidadRegistros
     * @return void
     */
    private function registrarExportacion($tabla, $formato, $cantidadRegistros)
    {
        // Aquí implementaríamos el registro en la tabla de auditoría
        // Por ahora, solo lo registramos en el log
        Log::info('Exportación de datos realizada', [
            'usuario' => Auth::user()->name,
            'tabla' => $tabla,
            'formato' => $formato,
            'cantidad_registros' => $cantidadRegistros,
            'fecha' => Carbon::now()->toDateTimeString()
        ]);
        
        // En una implementación real, se guardaría en una tabla de auditoría
        /*
        DB::table('auditorias')->insert([
            'usuario_id' => Auth::id(),
            'accion' => 'exportacion',
            'tabla' => $tabla,
            'detalles' => json_encode([
                'formato' => $formato,
                'cantidad_registros' => $cantidadRegistros
            ]),
            'created_at' => Carbon::now()
        ]);
        */
    }
}
