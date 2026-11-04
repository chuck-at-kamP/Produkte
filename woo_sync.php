<?php
/**
 * Synchronisiert die Tabelle produkte.items nach WooCommerce (REST API v3).
 *
 * Aufruf:  php woo_sync.php [--dry-run] [--id=123]
 *
 * Konfiguration per Umgebungsvariablen:
 *   DB_HOST, DB_NAME (produkte), DB_USER, DB_PASS
 *   WC_URL             z.B. https://shop.example.de
 *   WC_KEY, WC_SECRET  WooCommerce > Einstellungen > Erweitert > REST-API (Lesen/Schreiben)
 *   IMAGE_BASE_URL     öffentliche URL, unter der die Bilder aus items.image erreichbar sind,
 *                      z.B. https://produkte.example.de/uploads/
 *
 * Produkte werden über die SKU ("item-<id>") erkannt: vorhanden -> Update, sonst -> Neu anlegen.
 */

if (PHP_SAPI !== 'cli') {
    exit("Nur per Kommandozeile ausführen.\n");
}

$opts   = getopt('', ['dry-run', 'id::']);
$dryRun = isset($opts['dry-run']);
$onlyId = isset($opts['id']) ? (int)$opts['id'] : null;

function cfg(string $k, ?string $default = null): string
{
    $v = getenv($k);
    if ($v === false || $v === '') {
        if ($default === null) {
            fwrite(STDERR, "Fehlende Umgebungsvariable: $k\n");
            exit(1);
        }
        return $default;
    }
    return $v;
}

$wcUrl   = rtrim(cfg('WC_URL'), '/');
$wcKey   = cfg('WC_KEY');
$wcSec   = cfg('WC_SECRET');
$imgBase = rtrim(cfg('IMAGE_BASE_URL', ''), '/');

function wc(string $method, string $path, array $query = [], ?array $body = null): array
{
    global $wcUrl, $wcKey, $wcSec;
    $url = $wcUrl . '/wp-json/wc/v3/' . $path . ($query ? '?' . http_build_query($query) : '');
    $ch  = curl_init($url);
    $opt = [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => "$wcKey:$wcSec",
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 120,
    ];
    if ($body !== null) {
        $opt[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_UNICODE);
    }
    curl_setopt_array($ch, $opt);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($res === false) {
        throw new RuntimeException('cURL: ' . curl_error($ch));
    }
    curl_close($ch);
    $data = json_decode($res, true);
    if ($code >= 400 || !is_array($data)) {
        throw new RuntimeException("HTTP $code bei $method $path: " . substr($res, 0, 300));
    }
    return $data;
}

// Kategorie (ggf. mit Elternkategorie) suchen oder anlegen, Ergebnis wird gecacht.
function categoryId(string $name, int $parent = 0): int
{
    global $dryRun;
    static $cache = [];
    $key = "$parent|$name";
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    $found = wc('GET', 'products/categories', ['search' => $name, 'per_page' => 100, 'parent' => $parent]);
    foreach ($found as $c) {
        if (mb_strtolower(html_entity_decode($c['name'])) === mb_strtolower($name)) {
            return $cache[$key] = (int)$c['id'];
        }
    }
    if ($dryRun) {
        return $cache[$key] = 0;
    }
    $new = wc('POST', 'products/categories', [], ['name' => $name, 'parent' => $parent]);
    return $cache[$key] = (int)$new['id'];
}

$pdo = new PDO(
    sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', cfg('DB_HOST', 'localhost'), cfg('DB_NAME', 'produkte')),
    cfg('DB_USER'),
    cfg('DB_PASS'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

$sql  = 'SELECT * FROM items' . ($onlyId ? ' WHERE id = ?' : '') . ' ORDER BY id';
$stmt = $pdo->prepare($sql);
$stmt->execute($onlyId ? [$onlyId] : []);

$stats = ['created' => 0, 'updated' => 0, 'failed' => 0];

foreach ($stmt as $it) {
    $sku = 'item-' . $it['id'];
    try {
        $cats = [];
        if (!empty($it['category'])) {
            $parent = categoryId($it['category']);
            $cats[] = ['id' => $parent];
            if (!empty($it['subcategory'])) {
                $cats[] = ['id' => categoryId($it['subcategory'], $parent)];
            }
        }

        $meta = [];
        foreach (['barcode', 'state', 'gender', 'cost', 'location_id'] as $f) {
            if ($it[$f] !== null && $it[$f] !== '') {
                $meta[] = ['key' => '_produkte_' . $f, 'value' => (string)$it[$f]];
            }
        }

        $product = [
            'name'              => $it['name'],
            'type'              => 'simple',
            'status'            => 'publish',
            'sku'               => $sku,
            'regular_price'     => number_format((float)$it['price'], 2, '.', ''),
            'short_description' => $it['short'],
            'description'       => $it['description'],
            'manage_stock'      => true,
            'stock_quantity'    => (int)$it['stock'],
            'categories'        => array_values(array_filter($cats, fn($c) => $c['id'] > 0)),
            'meta_data'         => $meta,
        ];

        $existing = wc('GET', 'products', ['sku' => $sku]);
        $id = $existing[0]['id'] ?? null;

        // Bild nur beim Neuanlegen oder wenn noch keines gesetzt ist hochladen.
        if ($imgBase !== '' && $it['image'] !== '' && ($id === null || empty($existing[0]['images']))) {
            $product['images'] = [['src' => $imgBase . '/' . rawurlencode($it['image'])]];
        }

        if ($dryRun) {
            echo ($id ? 'UPDATE' : 'CREATE') . " $sku: {$it['name']}\n";
            continue;
        }

        if ($id) {
            wc('PUT', "products/$id", [], $product);
            $stats['updated']++;
        } else {
            wc('POST', 'products', [], $product);
            $stats['created']++;
        }
        echo ($id ? 'Aktualisiert' : 'Angelegt') . ": $sku {$it['name']}\n";
    } catch (Throwable $e) {
        $stats['failed']++;
        fwrite(STDERR, "FEHLER $sku: " . $e->getMessage() . "\n");
    }
}

printf("Fertig: %d angelegt, %d aktualisiert, %d fehlgeschlagen\n", $stats['created'], $stats['updated'], $stats['failed']);
exit($stats['failed'] ? 2 : 0);
