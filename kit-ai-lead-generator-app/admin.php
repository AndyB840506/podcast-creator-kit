<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
require_once __DIR__ . '/config.php';

// ── Auth ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
    } else {
        $loginError = 'Contraseña incorrecta';
    }
}
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: admin.php'); exit;
}

$authed = !empty($_SESSION['admin']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — AI Lead Generator</title>
<style>
:root {
  --bg:#0f1117; --bg2:#161b27; --bg3:#1e2535; --accent:#6366f1;
  --text:#e2e8f0; --text-muted:#64748b; --border:#2d3748;
  --green:#22c55e; --red:#ef4444; --yellow:#f59e0b;
}
* { box-sizing:border-box; margin:0; padding:0; }
body { background:var(--bg); color:var(--text); font-family:system-ui,sans-serif; min-height:100vh; }

/* Login */
.login-wrap { display:flex; align-items:center; justify-content:center; min-height:100vh; }
.login-box { background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:40px; width:340px; text-align:center; }
.login-box h2 { font-size:20px; margin-bottom:24px; }
.login-box input { width:100%; padding:10px 14px; background:var(--bg3); border:1px solid var(--border); color:var(--text); border-radius:8px; font-size:14px; margin-bottom:12px; }
.login-box button { width:100%; padding:10px; background:var(--accent); color:#fff; border:none; border-radius:8px; font-size:14px; cursor:pointer; font-weight:600; }
.error { color:var(--red); font-size:13px; margin-bottom:10px; }

/* Admin layout */
.admin-header { background:var(--bg2); border-bottom:1px solid var(--border); padding:14px 24px; display:flex; align-items:center; justify-content:space-between; }
.admin-header h1 { font-size:16px; font-weight:700; }
.admin-header span { color:var(--text-muted); font-size:13px; }
.btn { padding:8px 16px; border-radius:8px; border:none; cursor:pointer; font-size:13px; font-weight:600; }
.btn-primary { background:var(--accent); color:#fff; }
.btn-ghost  { background:transparent; color:var(--text-muted); border:1px solid var(--border); }
.btn-sm     { padding:5px 10px; font-size:12px; border-radius:6px; border:none; cursor:pointer; font-weight:600; }
.btn-danger { background:#7f1d1d; color:#fca5a5; }
.btn-success{ background:#14532d; color:#86efac; }
.btn-warn   { background:#78350f; color:#fcd34d; }

.admin-body { padding:24px; max-width:1200px; margin:0 auto; }

/* Stats bar */
.stats-bar { display:flex; gap:16px; margin-bottom:24px; flex-wrap:wrap; }
.stat-card { background:var(--bg2); border:1px solid var(--border); border-radius:10px; padding:16px 20px; flex:1; min-width:140px; }
.stat-card .num { font-size:24px; font-weight:700; color:var(--accent); }
.stat-card .lbl { font-size:12px; color:var(--text-muted); margin-top:2px; }

/* Tabla */
.section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
.section-header h2 { font-size:15px; font-weight:700; }
.table-wrap { background:var(--bg2); border:1px solid var(--border); border-radius:10px; overflow:hidden; }
table { width:100%; border-collapse:collapse; font-size:13px; }
th { background:var(--bg3); padding:10px 14px; text-align:left; color:var(--text-muted); font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.05em; }
td { padding:12px 14px; border-top:1px solid var(--border); vertical-align:middle; }
tr:hover td { background:rgba(99,102,241,.04); }

.badge { display:inline-block; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:700; }
.badge-on  { background:#14532d; color:#86efac; }
.badge-off { background:#450a0a; color:#fca5a5; }

.progress-bar { background:var(--bg3); border-radius:4px; height:6px; width:120px; overflow:hidden; display:inline-block; vertical-align:middle; margin-left:6px; }
.progress-fill { height:100%; border-radius:4px; background:var(--accent); transition:width .3s; }
.progress-fill.warn  { background:var(--yellow); }
.progress-fill.danger{ background:var(--red); }

.url-cell { font-family:monospace; font-size:11px; color:var(--text-muted); max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

/* Modal */
.modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:100; align-items:center; justify-content:center; }
.modal { background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:28px; width:480px; max-width:95vw; max-height:90vh; overflow-y:auto; }
.modal h3 { font-size:16px; font-weight:700; margin-bottom:20px; }
.form-group { margin-bottom:14px; }
.form-group label { display:block; font-size:12px; color:var(--text-muted); margin-bottom:5px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; }
.form-group input, .form-group select, .form-group textarea {
  width:100%; padding:9px 12px; background:var(--bg3); border:1px solid var(--border);
  color:var(--text); border-radius:8px; font-size:13px; font-family:inherit;
}
.form-group textarea { resize:vertical; }
.modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
.plan-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }

.toast { position:fixed; bottom:24px; right:24px; background:var(--bg3); border:1px solid var(--border); color:var(--text); padding:12px 18px; border-radius:8px; font-size:13px; z-index:200; display:none; }
.toast.success { border-color:var(--green); color:var(--green); }
.toast.error   { border-color:var(--red);   color:var(--red); }

.admin-tab { background:transparent; color:var(--text-muted); border:none; border-bottom:2px solid transparent; padding:10px 18px; font-size:13px; font-weight:600; cursor:pointer; margin-bottom:-1px; }
.admin-tab.active { color:var(--accent); border-bottom-color:var(--accent); }
.badge-default { background:#1e3a5f; color:#60a5fa; padding:2px 7px; border-radius:4px; font-size:11px; font-weight:700; }
</style>
</head>
<body>

<?php if (!$authed): ?>
<!-- ── Login ── -->
<div class="login-wrap">
  <div class="login-box">
    <h2>⚡ Admin Panel</h2>
    <?php if (!empty($loginError)): ?>
      <div class="error"><?= htmlspecialchars($loginError) ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="password" name="password" placeholder="Contraseña" autofocus required>
      <button type="submit">Entrar</button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- ── Admin UI ── -->
<div class="admin-header">
  <h1>⚡ AI Lead Generator — Admin</h1>
  <div style="display:flex;align-items:center;gap:12px">
    <span id="headerStats"></span>
    <form method="POST" style="display:inline">
      <button name="logout" class="btn btn-ghost" style="padding:6px 12px">Salir</button>
    </form>
  </div>
</div>

<div class="admin-body">

  <!-- Stats -->
  <div class="stats-bar" id="statsBar">
    <div class="stat-card"><div class="num" id="sTotal">—</div><div class="lbl">Clientes activos</div></div>
    <div class="stat-card"><div class="num" id="sLeads">—</div><div class="lbl">Leads generados este mes</div></div>
    <div class="stat-card"><div class="num" id="sPlans">—</div><div class="lbl">Leads contratados / mes</div></div>
    <div class="stat-card"><div class="num" id="sCosto" style="color:#22c55e">$—</div><div class="lbl">Costo API este mes</div></div>
  </div>

  <!-- API Key -->
  <div style="background:var(--bg2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:20px">
    <div style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:14px">Anthropic API Key — Claude Sonnet 4.6</div>
    <div style="display:flex;gap:10px;align-items:center">
      <input type="password" id="apiKeyInput" placeholder="sk-ant-api03-..." style="flex:1;padding:9px 12px;background:var(--bg3);border:1px solid var(--border);color:var(--text);border-radius:8px;font-size:13px;font-family:inherit">
      <button class="btn btn-primary" onclick="saveApiKey()">Guardar</button>
      <button class="btn btn-ghost" onclick="toggleApiKey()">Mostrar</button>
    </div>
    <div id="apiKeyStatus" style="font-size:12px;color:var(--text-muted);margin-top:8px"></div>
  </div>

  <!-- Tabs -->
  <div style="display:flex;gap:4px;margin-bottom:20px;border-bottom:1px solid var(--border);padding-bottom:0">
    <button class="admin-tab active" onclick="switchAdminTab('clientes',this)">👥 Clientes</button>
    <button class="admin-tab" onclick="switchAdminTab('costos',this)">💰 Costos del mes</button>
  </div>

  <!-- Tab: Clientes -->
  <div id="tab-clientes">
  <div class="section-header">
    <h2>Clientes</h2>
    <button class="btn btn-primary" onclick="openCreate()">+ Nuevo cliente</button>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Cliente</th>
          <th>Plan</th>
          <th>Uso mensual</th>
          <th>URL cliente</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="usersTable"></tbody>
    </table>
  </div>
  </div>

  <!-- Tab: Costos -->
  <div id="tab-costos" style="display:none">
  <div class="section-header"><h2>Costos del mes</h2></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Modelo</th><th>Reportes</th><th>Leads</th><th>Tokens entrada</th><th>Tokens salida</th><th>Costo estimado</th></tr></thead>
      <tbody id="costsTable"></tbody>
    </table>
  </div>
  </div>
</div>

<!-- Modal crear -->
<div id="createModal" class="modal-backdrop" onclick="closeOnBackdrop(event)">
  <div class="modal">
    <h3>Nuevo cliente</h3>
    <div class="form-group">
      <label>Nombre del cliente</label>
      <input type="text" id="cName" placeholder="Ej: Larecom AI">
    </div>
    <div class="form-group">
      <label>¿Qué vende / ofrece?</label>
      <textarea id="cPropuesta" rows="3" placeholder="Ej: Bots de voz IA para cobranza, agendamiento y soporte..."></textarea>
    </div>
    <div class="plan-grid">
      <div class="form-group">
        <label>Plan</label>
        <select id="cPlan" onchange="updatePlanLeads()">
          <option value="Básico|100">Básico — 100 leads/mes</option>
          <option value="Estándar|300">Estándar — 300 leads/mes</option>
          <option value="Pro|500">Pro — 500 leads/mes</option>
          <option value="Enterprise|1000">Enterprise — 1000 leads/mes</option>
          <option value="Custom|0">Personalizado</option>
        </select>
      </div>
      <div class="form-group">
        <label>Leads / mes</label>
        <input type="number" id="cLeads" value="100" min="10" max="10000">
      </div>
    </div>
    <div class="form-group">
      <label>País por defecto</label>
      <select id="cPais">
        <option>Colombia</option><option>México</option><option>Argentina</option>
        <option>Chile</option><option>Perú</option><option>Ecuador</option>
        <option>España</option><option>Estados Unidos</option>
      </select>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeModal('createModal')">Cancelar</button>
      <button class="btn btn-primary" onclick="createUser()">Crear cliente</button>
    </div>
  </div>
</div>

<!-- Modal editar -->
<div id="editModal" class="modal-backdrop" onclick="closeOnBackdrop(event)">
  <div class="modal">
    <h3>Editar cliente</h3>
    <input type="hidden" id="eId">
    <div class="form-group">
      <label>Nombre</label>
      <input type="text" id="eName" disabled style="opacity:.5">
    </div>
    <div class="form-group">
      <label>¿Qué vende / ofrece?</label>
      <textarea id="ePropuesta" rows="3"></textarea>
    </div>
    <div class="plan-grid">
      <div class="form-group">
        <label>Plan</label>
        <input type="text" id="ePlanNombre">
      </div>
      <div class="form-group">
        <label>Leads / mes</label>
        <input type="number" id="ePlanLeads" min="10">
      </div>
    </div>
    <div class="form-group">
      <label>País por defecto</label>
      <select id="ePais">
        <option>Colombia</option><option>México</option><option>Argentina</option>
        <option>Chile</option><option>Perú</option><option>Ecuador</option>
        <option>España</option><option>Estados Unidos</option>
      </select>
    </div>
    <div class="form-group">
      <label>Estado</label>
      <select id="eActivo">
        <option value="1">✅ Activo</option>
        <option value="0">🚫 Suspendido</option>
      </select>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeModal('editModal')">Cancelar</button>
      <button class="btn btn-warn" onclick="resetLeads()" style="margin-right:auto">↺ Reset leads</button>
      <button class="btn btn-primary" onclick="saveUser()">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal proveedor -->
<div id="providerModal" class="modal-backdrop" onclick="closeOnBackdrop(event)">
  <div class="modal">
    <h3 id="providerModalTitle">Nuevo proveedor LLM</h3>
    <input type="hidden" id="pId">
    <div class="form-group">
      <label>Plantilla rápida</label>
      <select onchange="applyProviderTemplate(this.value)">
        <option value="">— Seleccionar plantilla —</option>
        <optgroup label="── Anthropic ──">
          <option value="anthropic|claude-haiku-4-5-20251001|https://api.anthropic.com/v1/messages|0.00025|0.00125">Claude Haiku 4.5 — $0.00025/$0.00125 ⚡ Más rápido</option>
          <option value="anthropic|claude-sonnet-4-6|https://api.anthropic.com/v1/messages|0.003|0.015">Claude Sonnet 4.6 — $0.003/$0.015 ⚖️ Equilibrado</option>
          <option value="anthropic|claude-opus-4-7|https://api.anthropic.com/v1/messages|0.015|0.075">Claude Opus 4.7 — $0.015/$0.075 🧠 Más inteligente</option>
        </optgroup>
        <optgroup label="── OpenAI ──">
          <option value="openai|gpt-4o-mini|https://api.openai.com/v1/chat/completions|0.00015|0.0006">GPT-4o Mini — $0.00015/$0.0006 ⚡ Económico</option>
          <option value="openai|gpt-4o|https://api.openai.com/v1/chat/completions|0.005|0.015">GPT-4o — $0.005/$0.015 ⚖️ Equilibrado</option>
          <option value="openai|o1-mini|https://api.openai.com/v1/chat/completions|0.003|0.012">o1-mini — $0.003/$0.012 🧠 Razonamiento</option>
          <option value="openai|o1|https://api.openai.com/v1/chat/completions|0.015|0.06">o1 — $0.015/$0.06 🧠 Razonamiento premium</option>
        </optgroup>
        <optgroup label="── Groq (velocidad extrema) ──">
          <option value="groq|llama-3.1-8b-instant|https://api.groq.com/openai/v1/chat/completions|0.00005|0.00008">Groq Llama 3.1 8B — $0.00005/$0.00008 ⚡ Ultra rápido</option>
          <option value="groq|llama-3.1-70b-versatile|https://api.groq.com/openai/v1/chat/completions|0.00059|0.00079">Groq Llama 3.1 70B — $0.00059/$0.00079</option>
          <option value="groq|llama-3.3-70b-versatile|https://api.groq.com/openai/v1/chat/completions|0.00059|0.00079">Groq Llama 3.3 70B — $0.00059/$0.00079</option>
          <option value="groq|mixtral-8x7b-32768|https://api.groq.com/openai/v1/chat/completions|0.00024|0.00024">Groq Mixtral 8x7B — $0.00024/$0.00024</option>
          <option value="groq|deepseek-r1-distill-llama-70b|https://api.groq.com/openai/v1/chat/completions|0.00075|0.00099">Groq DeepSeek R1 70B — $0.00075/$0.00099 🧠</option>
        </optgroup>
        <optgroup label="── Google ──">
          <option value="openai|gemini-1.5-flash|https://generativelanguage.googleapis.com/v1beta/openai/chat/completions|0.000075|0.0003">Gemini 1.5 Flash — $0.000075/$0.0003 ⚡</option>
          <option value="openai|gemini-1.5-pro|https://generativelanguage.googleapis.com/v1beta/openai/chat/completions|0.00125|0.005">Gemini 1.5 Pro — $0.00125/$0.005</option>
          <option value="openai|gemini-2.0-flash|https://generativelanguage.googleapis.com/v1beta/openai/chat/completions|0.0001|0.0004">Gemini 2.0 Flash — $0.0001/$0.0004 ⚡</option>
        </optgroup>
        <optgroup label="── Local (gratis) ──">
          <option value="ollama|llama3.2|http://localhost:11434/api/chat|0|0">Ollama Llama 3.2 — $0 🏠 Local</option>
          <option value="ollama|mistral|http://localhost:11434/api/chat|0|0">Ollama Mistral — $0 🏠 Local</option>
          <option value="ollama|qwen2.5|http://localhost:11434/api/chat|0|0">Ollama Qwen 2.5 — $0 🏠 Local</option>
        </optgroup>
      </select>
    </div>
    <div class="plan-grid">
      <div class="form-group">
        <label>Nombre</label>
        <input type="text" id="pNombre" placeholder="Ej: Claude Haiku">
      </div>
      <div class="form-group">
        <label>Proveedor</label>
        <select id="pProveedor">
          <option value="anthropic">Anthropic</option>
          <option value="openai">OpenAI</option>
          <option value="groq">Groq</option>
          <option value="ollama">Ollama (local)</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label>Modelo</label>
      <input type="text" id="pModel" placeholder="Ej: claude-haiku-4-5-20251001">
    </div>
    <div class="form-group">
      <label>API URL</label>
      <input type="text" id="pUrl" placeholder="https://api.anthropic.com/v1/messages">
    </div>
    <div class="form-group">
      <label>API Key <span style="color:var(--text-muted);font-weight:400">(dejar vacío para no cambiar en edición)</span></label>
      <input type="password" id="pApiKey" placeholder="sk-...">
    </div>
    <div class="plan-grid">
      <div class="form-group">
        <label>Costo /1K tokens input (USD)</label>
        <input type="number" id="pCostoIn" step="0.00001" min="0" value="0">
      </div>
      <div class="form-group">
        <label>Costo /1K tokens output (USD)</label>
        <input type="number" id="pCostoOut" step="0.00001" min="0" value="0">
      </div>
    </div>
    <div class="form-group" id="pActivoGroup">
      <label>Estado</label>
      <select id="pActivo">
        <option value="1">✅ Activo</option>
        <option value="0">🚫 Inactivo</option>
      </select>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeModal('providerModal')">Cancelar</button>
      <button class="btn btn-primary" id="providerSaveBtn" onclick="saveProvider()">Crear</button>
    </div>
  </div>
</div>

<div id="toast" class="toast"></div>

<script>
let users = [];
let providers = [];

async function loadUsers() {
  const res  = await fetch('api/admin.php?action=list');
  const data = await res.json();
  users = data.users || [];
  renderTable();
  renderStats();
}

function renderStats() {
  const activos  = users.filter(u => u.activo == 1).length;
  const usados   = users.reduce((s,u) => s + parseInt(u.leads_usados||0), 0);
  const contrat  = users.reduce((s,u) => s + parseInt(u.plan_leads||0), 0);
  document.getElementById('sTotal').textContent = activos;
  document.getElementById('sLeads').textContent = usados.toLocaleString();
  document.getElementById('sPlans').textContent = contrat.toLocaleString();
}

function renderTable() {
  const tbody = document.getElementById('usersTable');
  if (!users.length) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px">No hay clientes aún</td></tr>';
    return;
  }

  tbody.innerHTML = users.map(u => {
    const pct     = u.plan_leads > 0 ? Math.round((u.leads_usados / u.plan_leads) * 100) : 0;
    const fillCls = pct >= 90 ? 'danger' : pct >= 70 ? 'warn' : '';
    const baseUrl = window.location.origin + window.location.pathname.replace('admin.php','');
    const url     = baseUrl + 'c/' + u.token;

    return `<tr>
      <td>
        <div style="font-weight:600">${esc(u.name)}</div>
        <div style="font-size:11px;color:var(--text-muted);margin-top:2px">${esc(u.propuesta_valor||'').substring(0,60)}${(u.propuesta_valor||'').length>60?'…':''}</div>
      </td>
      <td>
        <div style="font-weight:600">${esc(u.plan_nombre)}</div>
        <div style="font-size:11px;color:var(--text-muted)">${u.plan_leads} leads/mes</div>
      </td>
      <td>
        <div style="font-size:13px">${u.leads_usados} / ${u.plan_leads}</div>
        <div style="display:flex;align-items:center;gap:6px;margin-top:4px">
          <div class="progress-bar"><div class="progress-fill ${fillCls}" style="width:${pct}%"></div></div>
          <span style="font-size:11px;color:var(--text-muted)">${pct}%</span>
        </div>
      </td>
      <td>
        <div class="url-cell" title="${url}">${url}</div>
        <button class="btn-sm btn-ghost" style="margin-top:4px;background:var(--bg3);color:var(--text-muted);border:1px solid var(--border)" onclick="copyUrl('${url}')">📋 Copiar</button>
      </td>
      <td><span class="badge ${u.activo==1?'badge-on':'badge-off'}">${u.activo==1?'Activo':'Suspendido'}</span></td>
      <td style="display:flex;gap:6px;flex-wrap:wrap">
        <button class="btn-sm btn-success" onclick="editUser(${u.id})">✏️ Editar</button>
        <button class="btn-sm ${u.activo==1?'btn-danger':'btn-success'}" onclick="toggleUser(${u.id})">${u.activo==1?'🚫 Suspender':'✅ Activar'}</button>
      </td>
    </tr>`;
  }).join('');
}

function openCreate() {
  document.getElementById('cName').value     = '';
  document.getElementById('cPropuesta').value= '';
  document.getElementById('cLeads').value    = '100';
  document.getElementById('createModal').style.display = 'flex';
  setTimeout(() => document.getElementById('cName').focus(), 100);
}

function updatePlanLeads() {
  const val = document.getElementById('cPlan').value.split('|');
  if (val[1] !== '0') document.getElementById('cLeads').value = val[1];
}

async function createUser() {
  const name          = document.getElementById('cName').value.trim();
  const propuesta_valor= document.getElementById('cPropuesta').value.trim();
  const planVal       = document.getElementById('cPlan').value.split('|');
  const plan_nombre   = planVal[0];
  const plan_leads    = parseInt(document.getElementById('cLeads').value);
  const pais_default  = document.getElementById('cPais').value;

  if (!name) { showToast('Nombre requerido', 'error'); return; }

  const res  = await fetch('api/admin.php', {
    method: 'POST', headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ action:'create', name, propuesta_valor, plan_nombre, plan_leads, pais_default }),
  });
  const data = await res.json();
  if (data.error) { showToast(data.error, 'error'); return; }

  closeModal('createModal');
  showToast(`Cliente creado · URL: c/${data.token}`, 'success');
  loadUsers();
}

function editUser(id) {
  const u = users.find(x => x.id == id);
  if (!u) return;
  document.getElementById('eId').value         = u.id;
  document.getElementById('eName').value        = u.name;
  document.getElementById('ePropuesta').value   = u.propuesta_valor || '';
  document.getElementById('ePlanNombre').value  = u.plan_nombre;
  document.getElementById('ePlanLeads').value   = u.plan_leads;
  document.getElementById('eActivo').value      = u.activo;
  document.getElementById('ePais').value        = u.pais_default || 'Colombia';
  document.getElementById('editModal').style.display = 'flex';
}

async function saveUser() {
  const id = document.getElementById('eId').value;
  const res = await fetch('api/admin.php', {
    method: 'POST', headers: {'Content-Type':'application/json'},
    body: JSON.stringify({
      action:         'update',
      id:             parseInt(id),
      propuesta_valor: document.getElementById('ePropuesta').value.trim(),
      plan_nombre:    document.getElementById('ePlanNombre').value.trim(),
      plan_leads:     parseInt(document.getElementById('ePlanLeads').value),
      activo:         parseInt(document.getElementById('eActivo').value),
      pais_default:   document.getElementById('ePais').value,
    }),
  });
  const data = await res.json();
  if (data.error) { showToast(data.error, 'error'); return; }
  closeModal('editModal');
  showToast('Guardado', 'success');
  loadUsers();
}

async function resetLeads() {
  const id = document.getElementById('eId').value;
  if (!confirm('¿Resetear el contador de leads a 0?')) return;
  await fetch('api/admin.php', {
    method: 'POST', headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ action: 'reset_leads', id: parseInt(id) }),
  });
  closeModal('editModal');
  showToast('Leads reseteados', 'success');
  loadUsers();
}

async function toggleUser(id) {
  const res  = await fetch('api/admin.php', {
    method: 'POST', headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ action: 'toggle', id }),
  });
  const data = await res.json();
  showToast(data.activo == 1 ? 'Cliente activado' : 'Cliente suspendido', 'success');
  loadUsers();
}

function copyUrl(url) {
  navigator.clipboard.writeText(url);
  showToast('URL copiada', 'success');
}

function closeModal(id) { document.getElementById(id).style.display = 'none'; }
function closeOnBackdrop(e) { if (e.target === e.currentTarget) e.currentTarget.style.display = 'none'; }

function showToast(msg, type='') {
  const t = document.getElementById('toast');
  t.textContent = msg; t.className = `toast ${type}`; t.style.display = 'block';
  clearTimeout(t._t); t._t = setTimeout(() => t.style.display='none', 3500);
}

function esc(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

loadUsers();

// ── Admin tabs ────────────────────────────────────────────────
function switchAdminTab(name, btn) {
  document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-clientes').style.display = name === 'clientes' ? 'block' : 'none';
  document.getElementById('tab-costos').style.display   = name === 'costos'   ? 'block' : 'none';
  if (name === 'costos') loadCosts();
}

// ── API Key ───────────────────────────────────────────────────
async function saveApiKey() {
  const key = document.getElementById('apiKeyInput').value.trim();
  if (!key) { showToast('Ingresa una API key', 'error'); return; }
  const res  = await fetch('api/admin.php', {
    method: 'POST', headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ action: 'save_api_key', api_key: key }),
  });
  const data = await res.json();
  if (data.error) { showToast(data.error, 'error'); return; }
  document.getElementById('apiKeyInput').value = '';
  document.getElementById('apiKeyStatus').textContent = '✓ API key guardada';
  showToast('API key guardada', 'success');
}

function toggleApiKey() {
  const el = document.getElementById('apiKeyInput');
  const btn = el.nextElementSibling.nextElementSibling;
  if (el.type === 'password') { el.type = 'text'; btn.textContent = 'Ocultar'; }
  else { el.type = 'password'; btn.textContent = 'Mostrar'; }
}

// ── Costs ─────────────────────────────────────────────────────
async function loadCosts() {
  const res  = await fetch('api/admin.php?action=costs_summary');
  const data = await res.json();
  const tbody = document.getElementById('costsTable');
  const summary = data.summary || [];
  let totalCosto = 0;

  if (!summary.length || !summary[0].reports) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:16px">Sin datos este mes</td></tr>';
    return;
  }

  tbody.innerHTML = summary.map(s => {
    totalCosto += parseFloat(s.costo_total || 0);
    return `<tr>
      <td style="font-weight:600">${esc(s.nombre)}</td>
      <td style="font-family:monospace;font-size:12px">${esc(s.model)}</td>
      <td>${s.reports || 0}</td>
      <td>${(s.leads || 0).toLocaleString()}</td>
      <td style="font-size:12px;color:var(--text-muted)">${((s.tokens_in||0)+(s.tokens_out||0)).toLocaleString()}</td>
      <td style="font-weight:700;color:#22c55e">$${parseFloat(s.costo_total||0).toFixed(4)}</td>
    </tr>`;
  }).join('');

  document.getElementById('sCosto').textContent = '$' + totalCosto.toFixed(4);
}
</script>
<?php endif; ?>
</body>
</html>
