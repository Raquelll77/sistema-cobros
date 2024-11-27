<?php

namespace Model;

class ClientesPrestamos extends ActiveRecord
{
    protected static $tabla = 'SIFCO.CrPrestamos';
    protected static $columnasDB = ['ClReferencia', 'PreNombre', 'ClNumID', 'PreNumero', 'PreFecAprobacion', 'PreSalCapital', 'PreComentario'];

    const ClReferencia = 'ClReferencia';

    public function __construct($args = [])
    {
        $this->ClReferencia = $args['ClReferencia'] ?? null;
        $this->PreNombre = $args['PreNombre'] ?? '';
        $this->ClNumID = $args['ClNumID'] ?? '';
        $this->PreNumero = $args['PreNumero'] ?? '';
        $this->PreFecAprobacion = $args['PreFecAprobacion'] ?? '';
        $this->PreSalCapital = $args['PreSalCapital'] ?? '';
        $this->PreComentario = $args['PreComentario'] ?? '';
    }

    public static function buscarCreditosClientes($dni = null, $nombre = null, $prenumero = null)
    {
        self::useSQLSrv2();

        $sql = "SELECT ClReferencia AS ClReferencia, PreNombre AS PreNombre, ClNumID AS ClNumID, 
            PreNumero AS PreNumero, FORMAT(PreFecAprobacion, 'dd-MM-yyyy') AS PreFecAprobacion,
            CASE WHEN PreSalCapital = 0 THEN 'Cancelado' ELSE 'Vigente' END AS PreSalCapital, 
            PreComentario AS PreComentario
            FROM " . static::$tabla . " as cp
            INNER JOIN SIFCO.ClClientes as cc ON cp.PreCliCod = cc.ClCliCod
            WHERE 1=1";

        $params = [];
        if ($dni) {
            $sql .= " AND ClNumID = :dni";
            $params[':dni'] = $dni;
        }
        if ($nombre) {
            $sql .= " AND PreNombre LIKE :nombre";
            $params[':nombre'] = '%' . $nombre . '%';
        }
        if ($prenumero) {
            $sql .= " AND PreNumero = :prenumero";
            $params[':prenumero'] = $prenumero;
        }

        return self::consultarSQL($sql, $params);
    }

    public static function getInfoClientes($identidad, $fechaAprobacion)
    {
        // Cambiar a la conexión de la base de datos donde se encuentra el procedimiento almacenado
        self::useMySQL();

        // Definir el llamado al procedimiento almacenado con `CALL` y utilizar `?` para los parámetros
        $sql = "CALL sp_ObtenerInformacionPorIdentidadYFecha(?, ?)";
        $params = [$identidad, $fechaAprobacion];

        // Llamar a consultarSQL indicando que es un procedimiento almacenado
        return self::consultarSQL($sql, $params, true);
    }

    public static function getSaldoClientes($prenumero)
    {

        self::useSQLSrv2();

        $sql = "EXEC spSaldoCuentaDia @prenumero =  ?";
        $params = [$prenumero];

        return self::consultarSQL($sql, $params, true);
    }

    public static function ObtenerPagosCliente($prenumero)
    {

        self::useSQLSrv2();

        $sql = " EXEC ObtenerPagosCliente @PreNumero = ?";
        $params = [$prenumero];

        return self::consultarSQL($sql, $params, true);
    }

    public static function obtenerPrestamosPorGestor($usuario)
    {

        self::useSQLSrv();

        $sql = "EXEC [spObtenerPrestamos] @usuarioCobros = ? ";
        $params = [$usuario];

        return self::consultarSQL($sql, $params, true);

    }

    public static function obtenerPagosGeneral($fechaInicial, $fechaFinal)
    {

        self::useSQLSrv();

        $sql = "EXEC sp_ObtenerPagos @fechaInicial = ? , @fechaFinal = ?";
        $params = [$fechaInicial, $fechaFinal];

        return self::consultarSQL($sql, $params, true);

    }

    public static function pagosXGestor($fechaInicial, $fechaFinal, $usuarioGestor)
    {

        self::useSQLSrv();

        $sql = "EXEC sp_ObtenerPagos @fechaInicial = ? , @fechaFinal = ?,  @usuariogestor = ?";
        $params = [$fechaInicial, $fechaFinal, $usuarioGestor];

        return self::consultarSQL($sql, $params, true);

    }




}
