<?php
namespace app\models\abstract;

use app\db\db;

/**
 * Classe abstrata controller é uma classe base para controladores.
 *
 * Esta classe fornece métodos utilitários comuns que podem ser usados por controladores específicos.
 */
abstract class model
{
    private static array $lastCount = [];

    protected static function setLastCount(db $db):void
    {
        $method = debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS,2)[1]['function'];
        $class = get_called_class();
        self::$lastCount[$class."::".$method] = $db->count();
    }

    public static function getLastCount(string $method):int
    {
        $class = get_called_class();
        return isset(self::$lastCount[$class."::".$method]) ? self::$lastCount[$class."::".$method] : 0;
    }
}
