<?php
ignore_user_abort(true);
set_time_limit(0);
header('Content-Type: application/json; charset=utf-8');

// Capture PHP errors as JSON instead of HTML
set_exception_handler(function($e) {
    echo json_encode(['error' => $e->getMessage() . ' (línea ' . $e->getLine() . ')']);
    exit;
});
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo json_encode(['error' => $errstr . ' (línea ' . $errline . ')']);
    exit;
});

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']); exit;
}

// ── Input ─────────────────────────────────────────────────────
$input    = json_decode(file_get_contents('php://input'), true) ?? [];
$clientId  = intval($input['client_id'] ?? 0);
$nicho     = trim($input['nicho']    ?? '');
$pais      = trim($input['pais']     ?? '');
$cantidad  = intval($input['cantidad'] ?? 20);
$modo      = $input['modo'] ?? 'knowledge';
$token     = trim($input['token'] ?? '');

if (!$clientId || !$nicho || !$pais) {
    echo json_encode(['error' => 'Faltan parámetros requeridos']); exit;
}

$cantidad = max(10, min(60, $cantidad));

$db = getDB();

// Verify client exists
$client = $db->prepare("SELECT * FROM clients WHERE id = ?");
$client->execute([$clientId]);
$clientRow = $client->fetch();
if (!$clientRow) {
    echo json_encode(['error' => 'Cliente no encontrado']); exit;
}

// ── Verificar límite si viene de vista cliente ─────────────────
$userRow = null;
if ($token) {
    $userRow = getUserByToken($token);
    if (!$userRow) {
        echo json_encode(['error' => 'Token inválido']); exit;
    }
    if (!$userRow['activo']) {
        echo json_encode(['error' => 'Cuenta suspendida. Contacta a tu proveedor.']); exit;
    }
    $disponibles = leadsDisponibles($userRow);
    if ($disponibles <= 0) {
        echo json_encode(['error' => 'Sin leads disponibles este mes. Contacta a tu proveedor para recargar.']); exit;
    }
    // Limitar cantidad a lo disponible
    $cantidad = min($cantidad, $disponibles);
}

// ── Generate leads ─────────────────────────────────────────────
$leadsData   = null;
$usedProvider = null;

$propuesta = trim($clientRow['propuesta_valor'] ?? '');

if (MOCK_MODE) {
    $leadsData = generateMock($nicho, $pais, $cantidad);
} elseif ($modo === 'serper' && SERPER_API_KEY) {
    $leadsData = generateWithSerper($nicho, $pais, $cantidad, $propuesta);
    $usedProvider = getDefaultProvider();
} else {
    $leadsData = generateWithKnowledge($nicho, $pais, $cantidad, $propuesta);
    $usedProvider = getDefaultProvider();
}

if (!$leadsData || empty($leadsData['leads'])) {
    echo json_encode(['error' => 'No se pudieron generar leads. Intenta de nuevo.']); exit;
}

// ── Deduplication & save to DB ────────────────────────────────
$newLeads   = [];
$duplicates = 0;
$reportId   = null;

// Create report record first
$rStmt = $db->prepare("
    INSERT INTO reports (client_id, nicho, pais, cantidad, search_mode)
    VALUES (?, ?, ?, ?, ?)
");
$rStmt->execute([$clientId, $nicho, $pais, $cantidad, $modo]);
$reportId = $db->lastInsertId();

// Process each lead
foreach ($leadsData['leads'] as $lead) {
    $dominio = extractDomain($lead['website'] ?? '');

    // Check duplicate: same domain or same company name for this client
    if ($dominio) {
        $dupCheck = $db->prepare("SELECT id FROM leads WHERE client_id = ? AND dominio = ? AND dominio != ''");
        $dupCheck->execute([$clientId, $dominio]);
        if ($dupCheck->fetch()) { $duplicates++; continue; }
    }

    $buySignals = is_array($lead['buy_signals'] ?? null)
        ? implode(' | ', $lead['buy_signals'])
        : ($lead['buy_signals'] ?? '');

    $ins = $db->prepare("
        INSERT INTO leads (
            client_id, report_id, empresa, dominio, nicho, pais,
            score, categoria, sector, ciudad, empleados_est,
            decisor_nombre, decisor_cargo, decisor_linkedin,
            telefono, email_contacto, website, linkedin_empresa,
            descripcion, buy_signals
        ) VALUES (
            ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?
        )
    ");

    $ins->execute([
        $clientId,
        $reportId,
        $lead['empresa']          ?? 'N/D',
        $dominio,
        $nicho,
        $pais,
        intval($lead['score']     ?? 0),
        $lead['categoria']        ?? 'COLD',
        $lead['sector']           ?? '',
        $lead['ciudad']           ?? '',
        $lead['empleados_est']    ?? '',
        $lead['decisor_nombre']   ?? 'N/D',
        $lead['decisor_cargo']    ?? 'N/D',
        $lead['decisor_linkedin'] ?? 'N/D',
        $lead['telefono']         ?? 'N/D',
        $lead['email_contacto']   ?? 'N/D',
        $lead['website']          ?? 'N/D',
        $lead['linkedin_empresa'] ?? 'N/D',
        $lead['descripcion']      ?? '',
        $buySignals,
    ]);

    $lead['id'] = $db->lastInsertId();
    $newLeads[] = $lead;
}

if (empty($newLeads)) {
    // All were duplicates — still keep the report record but mark it
    $db->prepare("UPDATE reports SET leads_count=0, duplicates=? WHERE id=?")
       ->execute([$duplicates, $reportId]);
    echo json_encode([
        'success'    => true,
        'leads_new'  => 0,
        'duplicates' => $duplicates,
        'message'    => "Todos los leads ya existían para este cliente.",
    ]);
    exit;
}

// ── Generate files ─────────────────────────────────────────────
$clientDir = LEADS_DIR . sanitizeFilename($clientRow['name']) . '/';
if (!is_dir($clientDir)) mkdir($clientDir, 0755, true);

$slug     = sanitizeFilename($nicho . '-' . $pais) . '-' . date('Y-m-d-His');
$htmlFile = $clientDir . $slug . '.html';
$csvFile  = $clientDir . $slug . '.csv';

generateHTMLReport($newLeads, $leadsData['resumen'] ?? [], $htmlFile, $nicho, $pais, $clientRow['name']);
generateCSVReport($newLeads, $csvFile);

// Estimar costo (tokens aproximados: ~500 input + 150 output por lead)
$costoUsd = 0;
if ($usedProvider) {
    $tInput  = count($newLeads) * 500;
    $tOutput = count($newLeads) * 150;
    $costoUsd = round(
        ($tInput  / 1000 * $usedProvider['costo_per_1k_input']) +
        ($tOutput / 1000 * $usedProvider['costo_per_1k_output']),
        6
    );
}

// Update report record
$db->prepare("UPDATE reports SET leads_count=?, duplicates=?, archivo_html=?, archivo_csv=?, tokens_input=?, tokens_output=?, costo_usd=? WHERE id=?")
   ->execute([count($newLeads), $duplicates, basename($htmlFile), basename($csvFile),
              $tInput ?? 0, $tOutput ?? 0, $costoUsd, $reportId]);

// Descontar leads usados si viene de vista cliente
if ($userRow) {
    $db->prepare("UPDATE users SET leads_usados = leads_usados + ? WHERE id=?")
       ->execute([count($newLeads), $userRow['id']]);
}

// Return response
$leadsNuevos = count($newLeads);
echo json_encode([
    'success'          => true,
    'leads_new'        => $leadsNuevos,
    'duplicates'       => $duplicates,
    'report_id'        => $reportId,
    'html_url'         => 'leads/' . sanitizeFilename($clientRow['name']) . '/' . basename($htmlFile),
    'csv_url'          => 'leads/' . sanitizeFilename($clientRow['name']) . '/' . basename($csvFile),
    'summary'          => $leadsData['resumen'] ?? [],
    'leads_disponibles'=> $userRow ? max(0, leadsDisponibles($userRow) - $leadsNuevos) : null,
]);

// ── Lead generation: Knowledge mode ──────────────────────────
function generateWithKnowledge(string $nicho, string $pais, int $cantidad, string $propuesta = ''): ?array {
    $fecha = date('Y-m-d');

    $system = "Responde SOLO con JSON válido. Sin texto, sin markdown.";

    $propuestaCtx = $propuesta ? " El vendedor ofrece: \"{$propuesta}\". Buy signals = necesidades que este producto resuelve." : '';

    $user = "Lista {$cantidad} empresas reales de \"{$nicho}\" en {$pais} que sean buenos prospectos B2B.{$propuestaCtx} Campos desconocidos = \"N/D\". Score 0-100 (76+=PREMIUM,51-75=HOT,26-50=WARM,0-25=COLD). JSON exacto:\n{\"leads\":[{\"empresa\":\"\",\"website\":\"\",\"score\":0,\"categoria\":\"\",\"sector\":\"\",\"ciudad\":\"\",\"pais\":\"{$pais}\",\"empleados_est\":\"\",\"decisor_nombre\":\"\",\"decisor_cargo\":\"\",\"decisor_linkedin\":\"\",\"telefono\":\"\",\"email_contacto\":\"\",\"descripcion\":\"\",\"buy_signals\":[\"\"],\"linkedin_empresa\":\"\"}],\"resumen\":{\"total\":{$cantidad},\"premium\":0,\"hot\":0,\"warm\":0,\"cold\":0,\"nicho\":\"{$nicho}\",\"pais\":\"{$pais}\",\"fecha\":\"{$fecha}\",\"modo\":\"knowledge\"}}";

    $llm = callLLM([['role' => 'user', 'content' => $user]], $system, 8000);

    if (!$llm) return null;

    $text = $llm['text'];
    $data = extractJSON($text);

    // If JSON incomplete (truncated), retry with fewer leads
    if (!$data && $cantidad > 10) {
        $cantidad = intval($cantidad * 0.6);
        return generateWithKnowledge($nicho, $pais, $cantidad, $propuesta);
    }

    // Auto-fix resumen counts if Claude didn't fill them
    if ($data && !empty($data['leads'])) {
        $counts = ['PREMIUM' => 0, 'HOT' => 0, 'WARM' => 0, 'COLD' => 0];
        foreach ($data['leads'] as $l) {
            $cat = strtoupper($l['categoria'] ?? 'COLD');
            if (isset($counts[$cat])) $counts[$cat]++;
        }
        $data['resumen']['premium'] = $counts['PREMIUM'];
        $data['resumen']['hot']     = $counts['HOT'];
        $data['resumen']['warm']    = $counts['WARM'];
        $data['resumen']['cold']    = $counts['COLD'];
        $data['resumen']['total']   = count($data['leads']);
    }

    return $data;
}

// ── Lead generation: Serper mode ──────────────────────────────
function generateWithSerper(string $nicho, string $pais, int $cantidad, string $propuesta = ''): ?array {
    $searchResults = [];
    $queries = [
        "empresas {$nicho} {$pais}",
        "compañías {$nicho} {$pais} directorio",
        "CEO director {$nicho} {$pais} LinkedIn",
        "{$nicho} {$pais} PyMEs contacto",
        "mejores empresas {$nicho} {$pais}",
    ];

    foreach ($queries as $query) {
        $result = serperSearch($query);
        if ($result) $searchResults[] = $result;
    }

    if (empty($searchResults)) return generateWithKnowledge($nicho, $pais, $cantidad, $propuesta);

    $fecha    = date('Y-m-d');
    $rawData  = json_encode($searchResults, JSON_UNESCAPED_UNICODE);
    $propuestaContext = $propuesta
        ? "\nCONTEXTO DEL VENDEDOR: Ofrece \"{$propuesta}\". Los buy signals deben reflejar necesidades que este producto resuelve.\n"
        : '';

    $system = "Eres un analista de inteligencia de mercado. Analiza los resultados de búsqueda y extrae leads estructurados. Retorna ÚNICAMENTE JSON válido.";

    $user = <<<PROMPT
Analiza estos resultados de búsqueda web y extrae {$cantidad} leads de empresas reales del sector "{$nicho}" en {$pais}.
{$propuestaContext}
RESULTADOS DE BÚSQUEDA:
{$rawData}

Extrae empresas reales de los resultados. Para campos no encontrados usa "N/D".

Calcula score 0-100:
- Website encontrado: +15
- LinkedIn empresa: +15
- Email/contacto directo: +15
- Redes sociales: +10
- 10+ empleados: +10
- Decisor nombrado: +15
- Buy signal: +20

Retorna SOLO JSON en este formato:
{
  "leads": [{ "empresa":"...", "website":"...", "score":0, "categoria":"PREMIUM|HOT|WARM|COLD", "sector":"...", "ciudad":"...", "pais":"{$pais}", "empleados_est":"...", "decisor_nombre":"...", "decisor_cargo":"...", "decisor_linkedin":"...", "telefono":"...", "email_contacto":"...", "descripcion":"...", "buy_signals":[], "linkedin_empresa":"..." }],
  "resumen": { "total":{$cantidad}, "premium":0, "hot":0, "warm":0, "cold":0, "nicho":"{$nicho}", "pais":"{$pais}", "fecha":"{$fecha}", "modo":"serper" }
}
PROMPT;

    $llm = callLLM([['role' => 'user', 'content' => $user]], $system, 8192);

    if (!$llm) return null;
    return extractJSON($llm['text']);
}

function serperSearch(string $query): ?array {
    $ch = curl_init('https://google.serper.dev/search');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode(['q' => $query, 'hl' => 'es', 'num' => 10]),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'X-API-KEY: ' . SERPER_API_KEY],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);
    return $raw ? json_decode($raw, true) : null;
}

// ── HTML Report generator ──────────────────────────────────────
function generateHTMLReport(array $leads, array $resumen, string $path, string $nicho, string $pais, string $clientName): void {
    $fecha    = date('d/m/Y');
    $total    = count($leads);
    $premium  = $resumen['premium'] ?? 0;
    $hot      = $resumen['hot']     ?? 0;
    $warm     = $resumen['warm']    ?? 0;
    $cold     = $resumen['cold']    ?? 0;

    $cards = '';
    foreach ($leads as $lead) {
        $cat    = strtoupper($lead['categoria'] ?? 'COLD');
        $colors = categoryColor($cat);
        $score  = intval($lead['score'] ?? 0);
        $bg     = $colors['bg'];
        $label  = $colors['label'];

        $website  = ($lead['website']          && $lead['website']          !== 'N/D') ? "<a href='{$lead['website']}' target='_blank'>{$lead['website']}</a>" : 'N/D';
        $linkedin = ($lead['decisor_linkedin']  && $lead['decisor_linkedin'] !== 'N/D') ? "<a href='{$lead['decisor_linkedin']}' target='_blank'>Ver perfil ↗</a>" : 'N/D';
        $li_emp   = ($lead['linkedin_empresa']  && $lead['linkedin_empresa'] !== 'N/D') ? "<a href='{$lead['linkedin_empresa']}' target='_blank'>Ver empresa ↗</a>" : 'N/D';

        $buyHTML = '';
        $rawSignals = $lead['buy_signals'] ?? '';
        $buyArr = is_array($rawSignals) ? $rawSignals : explode(' | ', $rawSignals);
        foreach ($buyArr as $b) {
            if (trim($b)) $buyHTML .= "<span class='signal'>" . htmlspecialchars(trim($b)) . "</span>";
        }

        $cards .= <<<CARD
<div class='card' data-cat='{$cat}'>
  <div class='card-header' style='border-left: 4px solid {$bg}'>
    <div>
      <div class='card-company'>{$lead['empresa']}</div>
      <div class='card-meta'>{$lead['ciudad']} · {$lead['sector']} · {$lead['empleados_est']} empleados</div>
    </div>
    <div style='text-align:right'>
      <div class='cat-pill' style='background:{$bg}'>{$label}</div>
      <div class='score-num'>{$score}/100</div>
    </div>
  </div>
  <p class='card-desc'>{$lead['descripcion']}</p>
  <div class='card-grid'>
    <div class='card-section'>
      <div class='cs-title'>Empresa</div>
      <div class='cs-row'><span>🌐 Web</span><span>{$website}</span></div>
      <div class='cs-row'><span>💼 LinkedIn</span><span>{$li_emp}</span></div>
    </div>
    <div class='card-section'>
      <div class='cs-title'>A quién contactar</div>
      <div class='cs-row'><span>👤 Nombre</span><span>{$lead['decisor_nombre']}</span></div>
      <div class='cs-row'><span>💼 Cargo</span><span>{$lead['decisor_cargo']}</span></div>
      <div class='cs-row'><span>🔗 LinkedIn</span><span>{$linkedin}</span></div>
      <div class='cs-row'><span>📞 Teléfono</span><span>{$lead['telefono']}</span></div>
      <div class='cs-row'><span>📧 Email</span><span>{$lead['email_contacto']}</span></div>
    </div>
  </div>
  <div class='signals-wrap'>{$buyHTML}</div>
</div>
CARD;
    }

    $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Leads: {$nicho} — {$pais}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#0f1117;color:#e2e8f0;padding:24px}
.dashboard{max-width:1100px;margin:0 auto}
.report-header{background:#161b27;border:1px solid #2a3347;border-radius:12px;padding:24px 28px;margin-bottom:24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px}
.report-title{font-size:22px;font-weight:700}
.report-sub{color:#7b8caa;font-size:14px;margin-top:4px}
.btn-pdf{background:#6366f1;color:#fff;border:none;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer}
.stats-row{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px}
.stat-box{background:#161b27;border:1px solid #2a3347;border-radius:10px;padding:16px;text-align:center}
.stat-num{font-size:28px;font-weight:700;margin-bottom:4px}
.stat-label{font-size:12px;color:#7b8caa}
.filters{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap}
.filter-btn{background:#1e2535;border:1px solid #2a3347;color:#7b8caa;padding:7px 14px;border-radius:6px;cursor:pointer;font-size:13px;transition:all .15s}
.filter-btn.active,.filter-btn:hover{background:#6366f1;border-color:#6366f1;color:#fff}
.card{background:#161b27;border:1px solid #2a3347;border-radius:10px;padding:20px;margin-bottom:14px}
.card-header{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;padding-left:12px;margin-bottom:12px}
.card-company{font-size:16px;font-weight:700}
.card-meta{font-size:12px;color:#7b8caa;margin-top:4px}
.cat-pill{display:inline-block;color:#fff;font-size:11px;font-weight:700;padding:4px 10px;border-radius:4px}
.score-num{font-size:20px;font-weight:700;margin-top:4px;color:#a78bfa}
.card-desc{font-size:13px;color:#94a3b8;margin-bottom:14px;line-height:1.6}
.card-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.card-section{}
.cs-title{font-size:11px;font-weight:600;color:#6366f1;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px}
.cs-row{display:flex;justify-content:space-between;font-size:12px;padding:4px 0;border-bottom:1px solid #1e2535}
.cs-row span:first-child{color:#7b8caa;flex-shrink:0;margin-right:8px}
.cs-row a{color:#818cf8;text-decoration:none}
.signals-wrap{margin-top:12px;display:flex;flex-wrap:wrap;gap:6px}
.signal{background:rgba(16,185,129,.15);color:#34d399;font-size:11px;padding:3px 9px;border-radius:4px}
@media print{
  *{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important}
  body{background:#0f1117!important;color:#e2e8f0!important}
  .btn-pdf,.filters{display:none}
  .card{page-break-inside:avoid;border:1px solid #2a3347!important;background:#161b27!important}
  .stat-box{background:#161b27!important;border:1px solid #2a3347!important}
  .report-header{background:#161b27!important}
}
</style>
</head>
<body>
<div class="dashboard">
  <div class="report-header">
    <div>
      <div class="report-title">⚡ {$nicho} — {$pais}</div>
      <div class="report-sub">Cliente: {$clientName} · {$total} leads · {$fecha}</div>
    </div>
    <button class="btn-pdf" onclick="window.print()">📥 Descargar PDF</button>
  </div>

  <div class="stats-row">
    <div class="stat-box"><div class="stat-num" style="color:#a78bfa">{$total}</div><div class="stat-label">Total leads</div></div>
    <div class="stat-box"><div class="stat-num" style="color:#a78bfa">{$premium}</div><div class="stat-label">🥇 Premium</div></div>
    <div class="stat-box"><div class="stat-num" style="color:#f87171">{$hot}</div><div class="stat-label">🔥 Hot</div></div>
    <div class="stat-box"><div class="stat-num" style="color:#fbbf24">{$warm}</div><div class="stat-label">🌡️ Warm</div></div>
    <div class="stat-box"><div class="stat-num" style="color:#60a5fa">{$cold}</div><div class="stat-label">❄️ Cold</div></div>
  </div>

  <div class="filters">
    <button class="filter-btn active" onclick="filterCards('ALL',this)">Todos ({$total})</button>
    <button class="filter-btn" onclick="filterCards('PREMIUM',this)">🥇 Premium ({$premium})</button>
    <button class="filter-btn" onclick="filterCards('HOT',this)">🔥 Hot ({$hot})</button>
    <button class="filter-btn" onclick="filterCards('WARM',this)">🌡️ Warm ({$warm})</button>
    <button class="filter-btn" onclick="filterCards('COLD',this)">❄️ Cold ({$cold})</button>
  </div>

  <div id="cardsContainer">
    {$cards}
  </div>
</div>
<script>
function filterCards(cat,btn){
  document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.card').forEach(c=>{
    c.style.display=(cat==='ALL'||c.dataset.cat===cat)?'block':'none';
  });
}
</script>
</body>
</html>
HTML;

    file_put_contents($path, $html);
}

// ── CSV Report generator ───────────────────────────────────────
function generateCSVReport(array $leads, string $path): void {
    $headers = ['Empresa','Website','Score','Categoría','Sector','Ciudad','País','Empleados',
                'Decisor Nombre','Decisor Cargo','Decisor LinkedIn','Teléfono','Email',
                'LinkedIn Empresa','Descripción','Buy Signals'];

    $rows = [$headers];
    foreach ($leads as $l) {
        $rows[] = [
            $l['empresa']          ?? '',
            $l['website']          ?? '',
            $l['score']            ?? 0,
            $l['categoria']        ?? '',
            $l['sector']           ?? '',
            $l['ciudad']           ?? '',
            $l['pais']             ?? '',
            $l['empleados_est']    ?? '',
            $l['decisor_nombre']   ?? '',
            $l['decisor_cargo']    ?? '',
            $l['decisor_linkedin'] ?? '',
            $l['telefono']         ?? '',
            $l['email_contacto']   ?? '',
            $l['linkedin_empresa'] ?? '',
            $l['descripcion']      ?? '',
            is_array($l['buy_signals'] ?? '') ? implode(' | ', $l['buy_signals']) : ($l['buy_signals'] ?? ''),
        ];
    }

    $fp = fopen($path, 'w');
    fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel
    foreach ($rows as $row) fputcsv($fp, $row);
    fclose($fp);
}

// ── Mock mode (no API calls) ──────────────────────────────────
function generateMock(string $nicho, string $pais, int $cantidad): array {
    $categorias = ['PREMIUM', 'HOT', 'WARM', 'COLD'];
    $ciudades   = ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Madrid', 'Buenos Aires', 'Ciudad de México', 'Lima'];
    $cargos     = ['CEO', 'Director Comercial', 'Gerente General', 'Director de Marketing', 'VP de Ventas'];
    $leads = [];

    for ($i = 1; $i <= $cantidad; $i++) {
        $cat   = $categorias[array_rand($categorias)];
        $score = match($cat) {
            'PREMIUM' => rand(76, 100),
            'HOT'     => rand(51, 75),
            'WARM'    => rand(26, 50),
            default   => rand(0, 25),
        };
        $leads[] = [
            'empresa'          => "Empresa {$nicho} #{$i} [{$pais}]",
            'website'          => "https://empresa{$i}.com",
            'score'            => $score,
            'categoria'        => $cat,
            'sector'           => $nicho,
            'ciudad'           => $ciudades[array_rand($ciudades)],
            'pais'             => $pais,
            'empleados_est'    => rand(0,1) ? '10-50' : '50-200',
            'decisor_nombre'   => "Contacto Demo #{$i}",
            'decisor_cargo'    => $cargos[array_rand($cargos)],
            'decisor_linkedin' => "https://linkedin.com/in/demo{$i}",
            'telefono'         => '+57 300 000 ' . str_pad($i, 4, '0', STR_PAD_LEFT),
            'email_contacto'   => "demo{$i}@empresa{$i}.com",
            'descripcion'      => "Empresa de prueba #{$i} en el sector {$nicho}. Datos generados en modo MOCK — no consumió tokens de API.",
            'buy_signals'      => ["Interés en {$nicho}", "Presupuesto activo"],
            'linkedin_empresa' => "https://linkedin.com/company/empresa{$i}",
        ];
    }

    $counts = array_count_values(array_column($leads, 'categoria'));
    return [
        'leads'   => $leads,
        'resumen' => [
            'total'   => $cantidad,
            'premium' => $counts['PREMIUM'] ?? 0,
            'hot'     => $counts['HOT']     ?? 0,
            'warm'    => $counts['WARM']    ?? 0,
            'cold'    => $counts['COLD']    ?? 0,
            'nicho'   => $nicho,
            'pais'    => $pais,
            'fecha'   => date('Y-m-d'),
            'modo'    => 'mock',
        ],
    ];
}
