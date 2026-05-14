<?php
// ============================================================
// AI LEAD GENERATOR  |  Configuration
// ============================================================

define('MOCK_MODE',      false);
define('SERPER_API_KEY', '');
define('HUNTER_API_KEY', '');

define('LEADS_DIR',      __DIR__ . '/leads/');
define('DB_PATH',        __DIR__ . '/db/leads.sqlite');
define('SETTINGS_FILE',  __DIR__ . '/data/settings.json');
define('ADMIN_PASSWORD', _leadSetting('admin_password', 'admin2024'));

// LLM — fixed models, only API key is configurable
define('LLM_API_KEY',  _leadSetting('anthropic_api_key', ''));
define('LLM_MODEL',    'claude-sonnet-4-6');
define('LLM_API_URL',  'https://api.anthropic.com/v1/messages');

function _leadSetting(string $key, mixed $default = ''): mixed {
    static $st = null;
    if ($st === null) {
        $f = __DIR__ . '/data/settings.json';
        $st = file_exists($f) ? (json_decode(file_get_contents($f), true) ?? []) : [];
    }
    return $st[$key] ?? $default;
}

// ── Database ─────────────────────────────────────────────────

function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    initDB($pdo);
    return $pdo;
}

function initDB(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS clients (
            id               INTEGER PRIMARY KEY AUTOINCREMENT,
            name             TEXT UNIQUE NOT NULL,
            propuesta_valor  TEXT DEFAULT '',
            created_at       DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS reports (
            id           INTEGER PRIMARY KEY AUTOINCREMENT,
            client_id    INTEGER NOT NULL,
            nicho        TEXT NOT NULL,
            pais         TEXT NOT NULL,
            cantidad     INTEGER NOT NULL,
            archivo_html TEXT,
            archivo_csv  TEXT,
            leads_count  INTEGER DEFAULT 0,
            duplicates   INTEGER DEFAULT 0,
            search_mode  TEXT DEFAULT 'knowledge',
            tokens_input  INTEGER DEFAULT 0,
            tokens_output INTEGER DEFAULT 0,
            costo_usd     REAL DEFAULT 0,
            created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS leads (
            id               INTEGER PRIMARY KEY AUTOINCREMENT,
            client_id        INTEGER NOT NULL,
            report_id        INTEGER,
            empresa          TEXT NOT NULL,
            dominio          TEXT,
            nicho            TEXT,
            pais             TEXT,
            score            INTEGER DEFAULT 0,
            categoria        TEXT DEFAULT 'COLD',
            sector           TEXT,
            ciudad           TEXT,
            empleados_est    TEXT,
            decisor_nombre   TEXT,
            decisor_cargo    TEXT,
            decisor_linkedin TEXT,
            telefono         TEXT,
            email_contacto   TEXT,
            website          TEXT,
            linkedin_empresa TEXT,
            descripcion      TEXT,
            buy_signals      TEXT,
            status           TEXT DEFAULT 'nuevo',
            notas            TEXT,
            created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        INSERT OR IGNORE INTO clients (name) VALUES ('Demo');

        CREATE TABLE IF NOT EXISTS users (
            id           INTEGER PRIMARY KEY AUTOINCREMENT,
            client_id    INTEGER NOT NULL UNIQUE,
            token        TEXT UNIQUE NOT NULL,
            plan_nombre  TEXT DEFAULT 'Básico',
            plan_leads   INTEGER DEFAULT 100,
            leads_usados INTEGER DEFAULT 0,
            activo       INTEGER DEFAULT 1,
            fecha_reset  TEXT DEFAULT (strftime('%Y-%m-01', 'now')),
            pais_default TEXT DEFAULT 'Colombia',
            created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Migraciones de columnas
    $cols = array_column($pdo->query("PRAGMA table_info(clients)")->fetchAll(), 'name');
    if (!in_array('propuesta_valor', $cols)) {
        $pdo->exec("ALTER TABLE clients ADD COLUMN propuesta_valor TEXT DEFAULT ''");
    }

    $rCols = array_column($pdo->query("PRAGMA table_info(reports)")->fetchAll(), 'name');
    foreach (['tokens_input','tokens_output','costo_usd'] as $col) {
        if (!in_array($col, $rCols)) {
            $type = $col === 'costo_usd' ? 'REAL DEFAULT 0' : 'INTEGER DEFAULT 0';
            $pdo->exec("ALTER TABLE reports ADD COLUMN {$col} {$type}");
        }
    }
}

// ── LLM call ─────────────────────────────────────────────────

function callLLM(array $messages, string $system, int $maxTokens = 8000): ?array {
    $key = LLM_API_KEY;
    if (!$key) return null;

    $ch = curl_init(LLM_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode([
            'model'      => LLM_MODEL,
            'max_tokens' => $maxTokens,
            'system'     => $system,
            'messages'   => $messages,
        ], JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'x-api-key: ' . $key,
            'anthropic-version: 2023-06-01',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 180,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_FRESH_CONNECT  => true,
    ]);
    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err || $code !== 200) {
        error_log("LLM ERROR: HTTP={$code} ERR={$err} " . substr($raw, 0, 300));
        return null;
    }

    $data = json_decode($raw, true);
    $text = $data['content'][0]['text'] ?? null;
    if (!$text) return null;

    $tokensIn  = $data['usage']['input_tokens']  ?? 0;
    $tokensOut = $data['usage']['output_tokens'] ?? 0;
    // claude-sonnet-4-6 pricing: $3/$15 per 1M tokens
    $cost = ($tokensIn * 0.003 + $tokensOut * 0.015) / 1000;

    return [
        'text'     => $text,
        'provider' => ['nombre' => 'Claude Sonnet 4.6', 'model' => LLM_MODEL, 'id' => null],
        'tokens_input'  => $tokensIn,
        'tokens_output' => $tokensOut,
        'costo_usd'     => $cost,
    ];
}

// ── Helpers de usuarios ───────────────────────────────────────

function getUserByToken(string $token): ?array {
    $db   = getDB();
    $stmt = $db->prepare("
        SELECT u.*, c.name, c.propuesta_valor
        FROM users u
        JOIN clients c ON c.id = u.client_id
        WHERE u.token = ?
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if (!$user) return null;

    $mesActual = date('Y-m-01');
    if ($user['fecha_reset'] < $mesActual) {
        $db->prepare("UPDATE users SET leads_usados=0, fecha_reset=? WHERE id=?")
           ->execute([$mesActual, $user['id']]);
        $user['leads_usados'] = 0;
        $user['fecha_reset']  = $mesActual;
    }
    return $user;
}

function leadsDisponibles(array $user): int {
    return max(0, $user['plan_leads'] - $user['leads_usados']);
}

function generarToken(): string {
    return bin2hex(random_bytes(16));
}

// ── Helpers generales ─────────────────────────────────────────

function extractDomain(string $url): string {
    if (!$url || $url === 'N/D') return '';
    $host = parse_url($url, PHP_URL_HOST) ?: $url;
    return strtolower(preg_replace('/^www\./', '', $host));
}

function extractJSON(string $text): ?array {
    $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
    $text = preg_replace('/\s*```\s*$/m', '', $text);
    $text = trim($text);
    $start = strpos($text, '{');
    $end   = strrpos($text, '}');
    if ($start === false || $end === false) return null;
    $decoded = json_decode(substr($text, $start, $end - $start + 1), true);
    return is_array($decoded) ? $decoded : null;
}

function sanitizeFilename(string $str): string {
    $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str) ?: $str;
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9\-_]/', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
}

function categoryColor(string $cat): array {
    return match(strtoupper($cat)) {
        'PREMIUM' => ['bg' => '#7c3aed', 'label' => '🥇 PREMIUM'],
        'HOT'     => ['bg' => '#dc2626', 'label' => '🔥 HOT'],
        'WARM'    => ['bg' => '#d97706', 'label' => '🌡️ WARM'],
        default   => ['bg' => '#2563eb', 'label' => '❄️ COLD'],
    };
}

function statusLabel(string $status): string {
    return match($status) {
        'revisado'   => 'Revisado',
        'contactado' => 'Contactado',
        'en_proceso' => 'En proceso',
        'cerrado'    => 'Cerrado',
        default      => 'Nuevo',
    };
}
