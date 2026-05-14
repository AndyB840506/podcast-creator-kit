<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Lead Generator</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ── Header ────────────────────────────────────────────── -->
<header class="app-header">
  <div class="header-brand">
    <span class="header-icon">⚡</span>
    <span class="header-title">AI Lead Generator</span>
  </div>
  <div class="header-client">
    <label>Cliente:</label>
    <select id="clientSelect" onchange="switchClient(this.value)"></select>
    <button class="btn-ghost" onclick="showNewClientModal()">+ Nuevo</button>
  </div>
</header>

<!-- ── Layout ─────────────────────────────────────────────── -->
<div class="app-layout">

  <!-- Sidebar ── Search form -->
  <aside class="sidebar">
    <div class="sidebar-section">
      <h3 class="section-title">Nueva búsqueda</h3>

      <form id="searchForm" onsubmit="submitSearch(event)">
        <div class="form-group">
          <label>Nicho / sector</label>
          <input type="text" id="nicho" name="nicho" placeholder="Ej: seguros de vehículos" required>
        </div>

        <div class="form-group">
          <label>País</label>
          <select id="pais" name="pais">
            <option value="Colombia">Colombia</option>
            <option value="México">México</option>
            <option value="Argentina">Argentina</option>
            <option value="Chile">Chile</option>
            <option value="Perú">Perú</option>
            <option value="Ecuador">Ecuador</option>
            <option value="Venezuela">Venezuela</option>
            <option value="Bolivia">Bolivia</option>
            <option value="Paraguay">Paraguay</option>
            <option value="Uruguay">Uruguay</option>
            <option value="España">España</option>
            <option value="Estados Unidos">Estados Unidos</option>
            <option value="otro">Otro (escribir abajo)</option>
          </select>
          <input type="text" id="paisCustom" name="paisCustom" placeholder="Escribir país..." style="display:none; margin-top:6px;">
        </div>

        <div class="form-group">
          <label>Cantidad de leads: <strong id="cantidadLabel">20</strong></label>
          <input type="range" id="cantidad" name="cantidad" min="10" max="60" step="10" value="20"
                 oninput="document.getElementById('cantidadLabel').textContent=this.value">
          <div class="range-marks"><span>10</span><span>20</span><span>30</span><span>40</span><span>50</span><span>60</span></div>
        </div>

        <div class="form-group">
          <label>Modo de búsqueda</label>
          <div class="radio-group">
            <label class="radio-option active" id="mode-knowledge">
              <input type="radio" name="modo" value="knowledge" checked onchange="updateModeUI()">
              <span class="radio-dot"></span>
              <div>
                <strong>Knowledge</strong>
                <small>Gratis · datos del modelo</small>
              </div>
            </label>
            <label class="radio-option" id="mode-serper">
              <input type="radio" name="modo" value="serper" onchange="updateModeUI()">
              <span class="radio-dot"></span>
              <div>
                <strong>Serper</strong>
                <small>~$50/mes · búsqueda en vivo</small>
              </div>
            </label>
          </div>
        </div>

        <button type="submit" class="btn-primary btn-full" id="generateBtn">
          <span id="btnText">⚡ Generar Leads</span>
        </button>
      </form>
    </div>

    <!-- Stats rápidas -->
    <div class="sidebar-section" id="statsBlock" style="display:none">
      <h3 class="section-title">Este cliente</h3>
      <div class="stat-grid">
        <div class="stat-item"><span class="stat-num" id="statTotal">0</span><span class="stat-lbl">Total leads</span></div>
        <div class="stat-item"><span class="stat-num" id="statReports">0</span><span class="stat-lbl">Búsquedas</span></div>
        <div class="stat-item"><span class="stat-num premium-color" id="statPremium">0</span><span class="stat-lbl">Premium</span></div>
        <div class="stat-item"><span class="stat-num hot-color" id="statHot">0</span><span class="stat-lbl">Hot</span></div>
      </div>
    </div>
  </aside>

  <!-- Main panel -->
  <main class="main-panel">

    <!-- Tabs -->
    <div class="tabs">
      <button class="tab active" onclick="switchTab('history', this)">📋 Historial</button>
      <button class="tab" onclick="switchTab('leads', this)">🎯 Todos los leads</button>
    </div>

    <!-- Progress bar (visible while generating) -->
    <div id="progressBlock" style="display:none" class="progress-block">
      <div class="progress-bar-wrap">
        <div class="progress-bar-fill" id="progressFill"></div>
      </div>
      <p id="progressMsg">Iniciando búsqueda...</p>
    </div>

    <!-- Tab: Historial -->
    <div id="tab-history" class="tab-content active">
      <div id="historyList" class="history-list">
        <div class="empty-state">
          <p>Selecciona un cliente y genera tu primera búsqueda.</p>
        </div>
      </div>
    </div>

    <!-- Tab: Todos los leads -->
    <div id="tab-leads" class="tab-content">

      <div class="leads-toolbar">
        <input type="text" id="searchLeads" placeholder="Buscar empresa..." oninput="filterLeads()">
        <select id="filterCategoria" onchange="filterLeads()">
          <option value="">Todas las categorías</option>
          <option value="PREMIUM">🥇 Premium</option>
          <option value="HOT">🔥 Hot</option>
          <option value="WARM">🌡️ Warm</option>
          <option value="COLD">❄️ Cold</option>
        </select>
        <select id="filterStatus" onchange="filterLeads()">
          <option value="">Todos los estados</option>
          <option value="nuevo">Nuevo</option>
          <option value="revisado">Revisado</option>
          <option value="contactado">Contactado</option>
          <option value="en_proceso">En proceso</option>
          <option value="cerrado">Cerrado</option>
        </select>
      </div>

      <div id="leadsTableWrap">
        <table class="leads-table" id="leadsTable">
          <thead>
            <tr>
              <th>Empresa</th>
              <th>Nicho</th>
              <th>Score</th>
              <th>Cat.</th>
              <th>Decisor</th>
              <th>Contacto</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="leadsTableBody"></tbody>
        </table>
        <div class="empty-state" id="leadsEmpty">
          <p>No hay leads para este cliente todavía.</p>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- ── Modal: nuevo cliente ──────────────────────────────── -->
<div id="newClientModal" class="modal-backdrop" style="display:none" onclick="closeModalOnBackdrop(event)">
  <div class="modal">
    <h3>Nuevo cliente</h3>
    <div class="form-group">
      <label>Nombre del cliente</label>
      <input type="text" id="newClientName" placeholder="Ej: Empresa ABC" autofocus>
    </div>
    <div class="modal-actions">
      <button class="btn-ghost" onclick="closeModal()">Cancelar</button>
      <button class="btn-primary" onclick="createClient()">Crear</button>
    </div>
  </div>
</div>

<!-- ── Modal: ver lead ────────────────────────────────────── -->
<div id="leadModal" class="modal-backdrop" style="display:none" onclick="closeModalOnBackdrop(event)">
  <div class="modal modal-wide">
    <div id="leadModalContent"></div>
    <div class="modal-actions">
      <button class="btn-ghost" onclick="closeLeadModal()">Cerrar</button>
    </div>
  </div>
</div>

<!-- ── Toast ─────────────────────────────────────────────── -->
<div id="toast" class="toast" style="display:none"></div>

<script src="assets/js/app.js"></script>
</body>
</html>
