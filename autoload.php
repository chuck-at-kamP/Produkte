<?php

spl_autoload_register(function (string $class): void {
    $prefix = 'MK\\';
    $baseDir = '/var/www/mk-php-modules/MK/src/';

    if (!str_starts_with($class, $prefix)) { return; } // Nur Klassen mit diesem Namespace behandeln.

    // Namespace-Präfix entfernen und Namespace-Trenner in Verzeichnistrenner umwandeln.
    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) { require $file; }
});
