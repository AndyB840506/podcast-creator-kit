// ── State ─────────────────────────────────────────────────────
let currentClientId   = null;
let allLeadsCache     = [];
let progressInterval  = null;

// ── Init ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  // País custom
  document.getElementById('pais').addEventListener('change', function () {
    const custom = document.getElementById('paisCustom');
    custom.style.display = this.value === 'otro' ? 'block' : 'none';
    if (this.value === 'otro') custom.focus();
  });
  loadClients();
});

// ── Clients ───────────────────────────────────────────────────
async function loadClients() {
  const res  = await fetch('api/clients.php');
  const data = await res.json();
  if (!data.success) return;

  const sel = document.getElementById('clientSelect');
  sel.innerHTML = '';

  data.clients.forEach(c => {
    const opt = document.createElement('option');
    opt.value       = c.id;
    opt.textContent = `${c.name} (${c.leads_count} leads)`;
    sel.appendChild(opt);
  });

  if (data.clients.length > 0) {
    currentClientId = data.clients[0].id;
    sel.value = currentClientId;
    loadClientData(currentClientId);
  }
}

function switchClient(id) {
  currentClientId = parseInt(id);
  loadClientData(currentClientId);
}

async function loadClientData(clientId) {
  await Promise.all([
    loadHistory(clientId),
    loadLeads(clientId),
  ]);

  // Refresh stats
  const res  = await fetch('api/clients.php');
  const data = await res.json();
  const c    = data.clients?.find(x => x.id == clientId);
  if (c) {
    document.getElementById('statTotal').textContent   = c.leads_count;
    document.getElementById('statReports').textContent = c.reports_count;
    document.getElementById('statPremium').textContent = c.premium || 0;
    document.getElementById('statHot').textContent     = c.hot     || 0;
    document.getElementById('statsBlock').style.display = 'block';
  }
}

// ── History tab ───────────────────────────────────────────────
async function loadHistory(clientId) {
  const res  = await fetch(`api/history.php?client_id=${clientId}`);
  const data = await res.json();
  const list = document.getElementById('historyList');

  if (!data.success || !data.reports.length) {
    list.innerHTML = '<div class="empty-state"><p>Aún no hay búsquedas para este cliente.</p></div>';
    return;
  }

  list.innerHTML = data.reports.map(r => {
    const fecha = new Date(r.created_at).toLocaleDateString('es', { day:'2-digit', month:'short', year:'numeric' });
    return `
    <div class="history-card">
      <div class="history-info">
        <h4>${escHtml(r.nicho)} — ${escHtml(r.pais)}</h4>
        <div class="history-meta">
          <span>📅 ${fecha}</span>
          <span>🔍 ${r.search_mode}</span>
          ${r.duplicates > 0 ? `<span>♻️ ${r.duplicates} duplicados omitidos</span>` : ''}
        </div>
      </div>
      <div class="history-badges">
        <span class="badge badge-total">${r.leads_count} leads</span>
        ${r.cnt_premium > 0 ? `<span class="badge badge-premium">🥇 ${r.cnt_premium}</span>` : ''}
        ${r.cnt_hot     > 0 ? `<span class="badge badge-hot">🔥 ${r.cnt_hot}</span>`         : ''}
        ${r.cnt_warm    > 0 ? `<span class="badge badge-warm">🌡️ ${r.cnt_warm}</span>`       : ''}
        ${r.cnt_cold    > 0 ? `<span class="badge badge-cold">❄️ ${r.cnt_cold}</span>`       : ''}
      </div>
      <div class="history-actions">
        ${r.html_url ? `<a href="${r.html_url}" target="_blank" class="btn-xs">📄 HTML</a>` : ''}
        ${r.csv_url  ? `<a href="${r.csv_url}"  target="_blank" class="btn-xs">📊 CSV</a>`  : ''}
      </div>
    </div>`;
  }).join('');
}

// ── Leads tab ─────────────────────────────────────────────────
async function loadLeads(clientId) {
  const res  = await fetch(`api/leads.php?client_id=${clientId}`);
  const data = await res.json();

  allLeadsCache = data.leads || [];
  renderLeadsTable(allLeadsCache);
}

function renderLeadsTable(leads) {
  const tbody = document.getElementById('leadsTableBody');
  const empty = document.getElementById('leadsEmpty');

  if (!leads.length) {
    tbody.innerHTML = '';
    empty.style.display = 'block';
    return;
  }

  empty.style.display = 'none';

  const catColors = {
    PREMIUM: { bg: '#7c3aed', text: '#fff' },
    HOT:     { bg: '#dc2626', text: '#fff' },
    WARM:    { bg: '#d97706', text: '#fff' },
    COLD:    { bg: '#2563eb', text: '#fff' },
  };

  const catEmojis = { PREMIUM: '🥇', HOT: '🔥', WARM: '🌡️', COLD: '❄️' };

  tbody.innerHTML = leads.map(l => {
    const cat    = (l.categoria || 'COLD').toUpperCase();
    const colors = catColors[cat] || catColors.COLD;
    const score  = parseInt(l.score) || 0;
    const scoreC = score >= 76 ? '#a78bfa' : score >= 51 ? '#f87171' : score >= 26 ? '#fbbf24' : '#60a5fa';

    return `<tr>
      <td>
        <div class="empresa-name">${escHtml(l.empresa)}</div>
        <div class="empresa-city">${escHtml(l.ciudad || '')} ${l.pais ? '· ' + escHtml(l.pais) : ''}</div>
      </td>
      <td>${escHtml(l.nicho || '')}</td>
      <td>
        <span style="font-size:16px;font-weight:700;color:${scoreC}">${score}</span>
      </td>
      <td>
        <span class="cat-badge" style="background:${colors.bg};color:${colors.text}">
          ${catEmojis[cat] || ''} ${cat}
        </span>
      </td>
      <td>
        ${l.decisor_nombre && l.decisor_nombre !== 'N/D'
          ? `<div style="font-size:12px;font-weight:600">${escHtml(l.decisor_nombre)}</div><div style="font-size:11px;color:var(--text-muted)">${escHtml(l.decisor_cargo || '')}</div>`
          : '<span style="color:var(--text-muted);font-size:12px">N/D</span>'
        }
      </td>
      <td>
        ${l.telefono && l.telefono !== 'N/D'
          ? `<div style="font-size:12px">📞 ${escHtml(l.telefono)}</div>`
          : ''
        }
        ${l.email_contacto && l.email_contacto !== 'N/D'
          ? `<div style="font-size:11px;color:var(--text-muted)">📧 ${escHtml(l.email_contacto)}</div>`
          : ''
        }
        ${!l.telefono || l.telefono === 'N/D' && (!l.email_contacto || l.email_contacto === 'N/D')
          ? '<span style="color:var(--text-muted);font-size:12px">N/D</span>'
          : ''
        }
      </td>
      <td>
        <select class="status-select" onchange="updateStatus(${l.id}, this.value)">
          <option value="nuevo"       ${l.status==='nuevo'       ?'selected':''}>⬜ Nuevo</option>
          <option value="revisado"    ${l.status==='revisado'    ?'selected':''}>👁️ Revisado</option>
          <option value="contactado"  ${l.status==='contactado'  ?'selected':''}>📞 Contactado</option>
          <option value="en_proceso"  ${l.status==='en_proceso'  ?'selected':''}>⚙️ En proceso</option>
          <option value="cerrado"     ${l.status==='cerrado'     ?'selected':''}>✅ Cerrado</option>
        </select>
      </td>
      <td>
        <button class="btn-xs" onclick="viewLead(${l.id})">Ver</button>
        ${l.website && l.website !== 'N/D'
          ? `<a href="${escHtml(l.website)}" target="_blank" class="btn-xs" style="text-decoration:none;margin-left:4px">🌐</a>`
          : ''
        }
        ${l.decisor_linkedin && l.decisor_linkedin !== 'N/D'
          ? `<a href="${escHtml(l.decisor_linkedin)}" target="_blank" class="btn-xs" style="text-decoration:none;margin-left:4px">💼</a>`
          : ''
        }
      </td>
    </tr>`;
  }).join('');
}

// ── Filters ───────────────────────────────────────────────────
function filterLeads() {
  const q    = document.getElementById('searchLeads').value.toLowerCase();
  const cat  = document.getElementById('filterCategoria').value.toUpperCase();
  const stat = document.getElementById('filterStatus').value;

  const filtered = allLeadsCache.filter(l => {
    const matchQ   = !q   || l.empresa.toLowerCase().includes(q) || (l.nicho||'').toLowerCase().includes(q) || (l.ciudad||'').toLowerCase().includes(q);
    const matchCat = !cat || (l.categoria||'').toUpperCase() === cat;
    const matchSt  = !stat || l.status === stat;
    return matchQ && matchCat && matchSt;
  });

  renderLeadsTable(filtered);
}

// ── Status update ─────────────────────────────────────────────
async function updateStatus(leadId, status) {
  const res  = await fetch('api/leads.php', {
    method:  'POST',
    headers: { 'Content-Type': 'application/json' },
    body:    JSON.stringify({ action: 'update_status', id: leadId, status }),
  });
  const data = await res.json();
  if (data.success) {
    const lead = allLeadsCache.find(l => l.id == leadId);
    if (lead) lead.status = status;
    showToast('Estado actualizado', 'success');
  }
}

// ── View lead modal ───────────────────────────────────────────
function viewLead(leadId) {
  const l = allLeadsCache.find(x => x.id == leadId);
  if (!l) return;

  const signals = (l.buy_signals || '').split(' | ').filter(Boolean);
  const sigHtml = signals.map(s => `<span class="buy-signal-tag">${escHtml(s)}</span>`).join('');

  const field = (label, val, link) => {
    const display = val && val !== 'N/D' ? escHtml(val) : '<span style="color:var(--text-muted)">N/D</span>';
    const content = link && val && val !== 'N/D'
      ? `<a href="${escHtml(val)}" target="_blank">${display} ↗</a>`
      : display;
    return `<div class="lead-detail-field"><label>${label}</label><p>${content}</p></div>`;
  };

  const catColors = { PREMIUM:'#7c3aed', HOT:'#dc2626', WARM:'#d97706', COLD:'#2563eb' };
  const cat = (l.categoria || 'COLD').toUpperCase();

  document.getElementById('leadModalContent').innerHTML = `
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;gap:16px">
      <div>
        <h3 style="font-size:18px;font-weight:700">${escHtml(l.empresa)}</h3>
        <p style="color:var(--text-muted);font-size:13px;margin-top:4px">${escHtml(l.sector||'')} · ${escHtml(l.ciudad||'')} · ${escHtml(l.pais||'')}</p>
      </div>
      <div style="text-align:right;flex-shrink:0">
        <span style="background:${catColors[cat]||'#2563eb'};color:#fff;padding:4px 10px;border-radius:4px;font-size:12px;font-weight:700">${cat}</span>
        <div style="font-size:22px;font-weight:700;color:#a78bfa;margin-top:4px">${l.score}/100</div>
      </div>
    </div>
    <p style="font-size:13px;color:#94a3b8;margin-bottom:16px;line-height:1.6">${escHtml(l.descripcion||'')}</p>
    <div class="lead-detail-grid">
      <div class="lead-detail-field lead-detail-full"><label>EMPRESA</label></div>
      ${field('Website', l.website, true)}
      ${field('LinkedIn empresa', l.linkedin_empresa, true)}
      ${field('Empleados', l.empleados_est, false)}
      ${field('Nicho', l.nicho, false)}
      <div class="lead-detail-field lead-detail-full" style="margin-top:8px"><label>CONTACTO DIRECTO</label></div>
      ${field('Nombre', l.decisor_nombre, false)}
      ${field('Cargo', l.decisor_cargo, false)}
      ${field('LinkedIn personal', l.decisor_linkedin, true)}
      ${field('Teléfono / WhatsApp', l.telefono, false)}
      ${field('Email', l.email_contacto, false)}
    </div>
    ${signals.length ? `<div style="margin-top:14px"><p style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Buy Signals</p><div class="buy-signals-list">${sigHtml}</div></div>` : ''}
    ${l.notas ? `<div style="margin-top:14px"><p style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Notas</p><p style="font-size:13px">${escHtml(l.notas)}</p></div>` : ''}
  `;

  document.getElementById('leadModal').style.display = 'flex';
}

function closeLeadModal() {
  document.getElementById('leadModal').style.display = 'none';
}

// ── Search form ───────────────────────────────────────────────
async function submitSearch(e) {
  e.preventDefault();

  if (!currentClientId) { showToast('Selecciona un cliente primero', 'error'); return; }

  const nicho = document.getElementById('nicho').value.trim();
  const paisSel = document.getElementById('pais').value;
  const pais = paisSel === 'otro'
    ? document.getElementById('paisCustom').value.trim()
    : paisSel;
  const cantidad = parseInt(document.getElementById('cantidad').value);
  const modo = document.querySelector('input[name="modo"]:checked').value;

  if (!nicho || !pais) { showToast('Completa nicho y país', 'error'); return; }

  // Show progress
  const btn = document.getElementById('generateBtn');
  btn.disabled = true;
  document.getElementById('btnText').textContent = '⏳ Generando...';
  showProgress();

  try {
    const res = await fetch('api/generate.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ client_id: currentClientId, nicho, pais, cantidad, modo }),
    });

    const data = await res.json();
    hideProgress();

    if (data.error) {
      showToast('Error: ' + data.error, 'error');
      return;
    }

    // Refresh client data
    await loadClientData(currentClientId);
    await loadClients();

    // Switch to history tab
    switchTab('history', document.querySelector('.tab'));

    let msg = `✅ ${data.leads_new} leads generados`;
    if (data.duplicates > 0) msg += ` (${data.duplicates} duplicados omitidos)`;
    showToast(msg, 'success');

    // Open HTML report
    if (data.html_url) {
      setTimeout(() => window.open(data.html_url, '_blank'), 500);
    }

  } catch (err) {
    hideProgress();
    showToast('Error de conexión. Verifica que Laragon esté corriendo.', 'error');
  } finally {
    btn.disabled = false;
    document.getElementById('btnText').textContent = '⚡ Generar Leads';
  }
}

// ── Progress animation ────────────────────────────────────────
const PROGRESS_MSGS = [
  'Analizando el mercado...',
  'Identificando empresas del sector...',
  'Buscando decisores clave...',
  'Calculando scores...',
  'Verificando información de contacto...',
  'Generando reporte...',
];

function showProgress() {
  const block = document.getElementById('progressBlock');
  const fill  = document.getElementById('progressFill');
  const msg   = document.getElementById('progressMsg');
  block.style.display = 'block';
  let pct = 0, step = 0;

  progressInterval = setInterval(() => {
    pct = Math.min(pct + Math.random() * 8, 90);
    fill.style.width = pct + '%';
    msg.textContent  = PROGRESS_MSGS[step % PROGRESS_MSGS.length];
    step++;
  }, 2500);
}

function hideProgress() {
  clearInterval(progressInterval);
  const fill = document.getElementById('progressFill');
  fill.style.width = '100%';
  setTimeout(() => {
    document.getElementById('progressBlock').style.display = 'none';
    fill.style.width = '0%';
  }, 500);
}

// ── Tabs ──────────────────────────────────────────────────────
function switchTab(name, clickedBtn) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

  const targetTab = clickedBtn || document.querySelector(`.tab[onclick*="${name}"]`);
  if (targetTab) targetTab.classList.add('active');

  const content = document.getElementById(`tab-${name}`);
  if (content) content.classList.add('active');
}

// ── Mode UI ───────────────────────────────────────────────────
function updateModeUI() {
  document.querySelectorAll('.radio-option').forEach(opt => {
    opt.classList.toggle('active', opt.querySelector('input').checked);
  });
}

// ── New client modal ──────────────────────────────────────────
function showNewClientModal() {
  document.getElementById('newClientModal').style.display = 'flex';
  document.getElementById('newClientName').value = '';
  setTimeout(() => document.getElementById('newClientName').focus(), 100);
}

function closeModal() {
  document.getElementById('newClientModal').style.display = 'none';
}

function closeModalOnBackdrop(e) {
  if (e.target === e.currentTarget) {
    e.currentTarget.style.display = 'none';
  }
}

async function createClient() {
  const name = document.getElementById('newClientName').value.trim();
  if (!name) return;

  const res  = await fetch('api/clients.php', {
    method:  'POST',
    headers: { 'Content-Type': 'application/json' },
    body:    JSON.stringify({ name }),
  });
  const data = await res.json();

  if (data.error) { showToast(data.error, 'error'); return; }

  closeModal();
  await loadClients();

  // Switch to the new client
  document.getElementById('clientSelect').value = data.id;
  currentClientId = data.id;
  loadClientData(data.id);
  showToast(`Cliente "${name}" creado`, 'success');
}

// ── Toast ─────────────────────────────────────────────────────
function showToast(msg, type = '') {
  const t = document.getElementById('toast');
  t.textContent  = msg;
  t.className    = `toast ${type}`;
  t.style.display = 'block';
  clearTimeout(t._timeout);
  t._timeout = setTimeout(() => { t.style.display = 'none'; }, 3500);
}

// ── Utils ─────────────────────────────────────────────────────
function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}
