<?php
// Puente hacia el autoload real de Composer, cuyo vendor-dir esta configurado
// en composer.json como upload/system/storage/vendor/.
require_once __DIR__ . '/../upload/system/storage/vendor/autoload.php';

// Autoload manual para las clases del motor y librerias de OpenCart
// (Opencart\System\Engine\* y Opencart\System\Library\*), que normalmente carga
// el propio framework en runtime, no Composer.
spl_autoload_register(function ($class) {
    $prefix = 'Opencart\\System\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $parts = explode('\\', $relative);
    $className = array_pop($parts);
    $subdir = strtolower(implode('/', $parts));

    $base = __DIR__ . '/../opencart/system/' . $subdir . '/';
    $candidates = [$base . $className . '.php', $base . strtolower($className) . '.php'];

    foreach ($candidates as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
