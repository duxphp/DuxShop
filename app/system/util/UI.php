<?php

/**
 * UI构建器
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\system\util;

class UI {

    public static function layoutTable() {
        return self::init('table');
    }

    public static function layoutForm() {
        return self::init('table');
    }

    public static function init($type) {
        $class = '\\app\\system\\util\\ui\\' . $type . '\\UI';
        return new $class;

    }

}