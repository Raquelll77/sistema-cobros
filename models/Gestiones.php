<?php
namespace Model;

class Gestiones extends ActiveRecord
{
    protected static $tabla = 'Gestiones';
    protected static $columnasDB = [
        'id',
        'prenumero',
        'codigo_resultado',
        'fecha_revision',
        'fecha_promesa',
        'numero_contactado',
        'comentario',
        'creado_por',
        'fecha_creacion',
        'montoPromesa'
    ];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->prenumero = $args['prenumero'] ?? '';
        $this->codigo_resultado = $args['codigo_resultado'] ?? '';
        $this->fecha_revision = $args['fecha_revision'] ?? null;
        $this->fecha_promesa = $args['fecha_promesa'] ?? null;
        $this->numero_contactado = $args['numero_contactado'] ?? '';
        $this->comentario = $args['comentario'] ?? '';
        $this->creado_por = $args['creado_por'] ?? '';
        $this->fecha_creacion = $args['fecha_creacion'] ?? null;
        $this->montoPromesa = $args['monto_promesa'] ?? null;
    }

    public static function ObtenerGestionesPorFechas($fecha)
    {

        self::useSQLSrv();

        $sql = "EXEC ObtenerGestionesPorFechas @fecha =  ?";
        $params = [$fecha];

        return self::consultarSQL($sql, $params, true);

    }

    /**
     * Procesar datos de gestiones para cálculos
     */
    public static function procesarDatosGestiones($gestiones)
    {
        // Inicializar totales y agrupaciones
        $totales = array_map(function ($gestion) {
            return $gestion['total'];
        }, $gestiones);

        $gestores = array_map(function ($gestion) {
            return $gestion['gestor'];
        }, $gestiones);

        // Número de gestiones esperadas por gestor
        $gestionesEsperadasPorGestor = 60;

        // Calcular porcentaje realizado por cada gestor
        $porcentajesPorGestor = [];
        foreach ($totales as $index => $total) {
            $porcentajesPorGestor[] = round(($total / $gestionesEsperadasPorGestor) * 100, 2);
        }

        // Inicializar códigos de resultado
        $codigoResultados = [
            'PP' => 0,
            'CF' => 0,
            'DEC' => 0,
            'PRP' => 0,
            'DAR' => 0,
            'RLL' => 0,
            'SMS' => 0
        ];

        // Sumar códigos de resultado
        foreach ($gestiones as $gestion) {
            $codigoResultados['PP'] += isset($gestion['PRONTO PAGO']) ? $gestion['PRONTO PAGO'] : 0;
            $codigoResultados['CF'] += isset($gestion['CF']) ? $gestion['CF'] : 0;
            $codigoResultados['DEC'] += isset($gestion['DEC']) ? $gestion['DEC'] : 0;
            $codigoResultados['PRP'] += isset($gestion['PRP']) ? $gestion['PRP'] : 0;
            $codigoResultados['DAR'] += isset($gestion['DAR']) ? $gestion['DAR'] : 0;
            $codigoResultados['RLL'] += isset($gestion['RLL']) ? $gestion['RLL'] : 0;
            $codigoResultados['SMS'] += isset($gestion['SMS']) ? $gestion['SMS'] : 0;
        }

        // Calcular totales y porcentajes
        $totalCodigos = array_sum($codigoResultados);
        $porcentajesCodigos = $totalCodigos > 0
            ? array_map(function ($valor) use ($totalCodigos) {
                return round(($valor / $totalCodigos) * 100, 2);
            }, $codigoResultados)
            : array_fill(0, count($codigoResultados), 0);

        return [
            'gestores' => $gestores,
            'totales' => $totales,
            'porcentajesPorGestor' => $porcentajesPorGestor,
            'codigoResultados' => $codigoResultados,
            'porcentajesCodigos' => $porcentajesCodigos,
            'totalGestiones' => array_sum($totales)
        ];
    }


    //cumplimiento de promesas 
    public static function obtenerTodasLasPromesas()
    {
        self::useSQLSrv();
        $sql = "EXEC sp_ConsultarPromesas @tipo_busqueda = ?";
        $params = [1]; // 1: Consultar todo
        return self::consultarSQL($sql, $params, true);
    }

    public static function obtenerPromesasPorGestor($gestor)
    {
        self::useSQLSrv();
        $sql = "EXEC sp_ConsultarPromesas @tipo_busqueda = ?, @gestor = ?";
        $params = [2, $gestor]; // 2: Filtrar por gestor
        return self::consultarSQL($sql, $params, true);
    }

    public static function obtenerPromesasPorCliente($prenumero)
    {
        self::useSQLSrv();
        $sql = "EXEC sp_ConsultarPromesas @tipo_busqueda = ?, @prenumero = ?";
        $params = [3, $prenumero]; // 3: Filtrar por cliente
        return self::consultarSQL($sql, $params, true);
    }



}
