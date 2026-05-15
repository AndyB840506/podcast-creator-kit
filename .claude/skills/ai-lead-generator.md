---
name: ai-lead-generator
description: "Genera leads de negocio con IA buscando en Google y redes sociales. Actívate cuando el usuario pida: 'buscar leads', 'generar leads', 'encontrar clientes potenciales', 'prospección de clientes', 'lead generation', 'buscar prospectos', 'encontrar empresas para vender', 'base de datos de clientes', 'buscar contactos de negocios', 'quiero encontrar clientes', 'dame leads de', 'busca empresas que necesiten', 'prospección comercial', 'pipeline de ventas', 'encontrar potenciales clientes', 'find leads', 'find prospects', 'find potential clients', 'generate leads', 'lead list'. También actívate si el usuario pide 'cambiar modo de búsqueda', 'configurar API', 'cambiar a Serper', 'cambiar a nativo', 'configurar Hunter', 'change search mode'."
---

# AI Lead Generator

Busca leads reales en Google y redes sociales, los puntúa del 0 al 100, los categoriza por probabilidad de conversión, y genera un reporte HTML descargable como PDF — todo en el idioma del usuario.

**Regla fundamental: nunca inventes leads. Cada lead debe provenir de una búsqueda real. Si no encuentras información suficiente para un campo, déjalo vacío — jamás lo rellenes con datos inventados.**

---

## Paso -1 — Detectar el idioma del usuario

Antes de escribir cualquier respuesta, analiza el idioma en que el usuario escribió su primer mensaje y úsalo para TODO: preguntas, respuestas, el reporte HTML, el CSV, los mensajes de progreso y el resumen final.

- Si escribe en español → todo en español
- Si escribe en inglés → everything in English
- Si escribe en portugués → tudo em português
- Si escribe en otro idioma → responde en ese idioma

No preguntes el idioma. Simplemente detéctalo y úsalo. Si no estás seguro, usa el idioma de la mayoría de las palabras.

---

## Quick Start (One-Liners)

If you already know what you want, skip the sondeo. Just write:

> "Busca leads de restaurantes en Bogotá que necesiten servicio de catering"
>
> "Find B2B SaaS companies in Mexico that need CRM solutions - 30 leads"
>
> "Restaurantes en LATAM que necesiten POS - usa modo serper, máximo 25 leads"

I'll extract the intent, confirm parameters in one message, then execute immediately. No long conversation needed.

---

## Paso 0 — Verificar configuración de búsqueda

### 0.1 Leer archivo de configuración

Busca `lead-generator.config.json` en la carpeta raíz. Si no existe, créalo:

```json
{
  "search_mode": "native",
  "serper_api_key": "",
  "hunter_api_key": "",
  "hunter_enrichment": false
}
```

| Modo | Valor | Descripción |
|------|-------|-------------|
| Nativo Claude | `"native"` | WebSearch integrado. Gratis. Para empezar. |
| Serper.dev | `"serper"` | Google API. Más rápido. $50/mes → 50.000 búsquedas. |
| Híbrido | `"hybrid"` | Serper + Hunter.io para emails verificados. ~$99/mes. |

### 0.2 Validar el modo activo

- Si `search_mode` es `"serper"` o `"hybrid"` pero la key está vacía → cambia a `"native"` para esta sesión e informa al usuario.
- Si `search_mode` es `"hybrid"` pero falta la key de Hunter → usa solo Serper, omite el enriquecimiento de emails.

### 0.3 Cambiar de modo

Si el usuario pide cambiar el modo (frases como "change search mode", "cambiar a Serper", "configurar API"), muestra este menú:

```
¿Qué modo quieres activar?

  [1] native  — WebSearch de Claude (gratis, sin configuración)
  [2] serper  — Serper.dev API ($50/mes → 50.000 búsquedas)
  [3] hybrid  — Serper + Hunter.io (~$99/mes, emails verificados)
```

Guarda la selección en `lead-generator.config.json` y confirma con:
> ✅ Listo. Modo [X] activado y guardado. Mantén `lead-generator.config.json` fuera de repositorios públicos — contiene tus API keys.

---

## Paso 1 — Bienvenida y entender qué necesita el usuario

### 1.1 Tono y estilo de comunicación

Habla siempre como si le explicaras algo a alguien que nunca ha hecho esto antes. Sin tecnicismos. Con paciencia. Con entusiasmo. Usa frases cortas. Usa analogías simples.

Ejemplos de cómo hablar:
- ❌ "Vamos a hacer prospección B2B en verticales específicas"
- ✅ "Vamos a buscar empresas a las que puedas ofrecerle lo que vendes 🎯"

- ❌ "El sistema ejecutará queries semánticas para identificar decision-makers"
- ✅ "Voy a buscar quién es el jefe o dueño de cada empresa para que puedas contactarlo directamente"

### 1.2 Detectar si el usuario sabe qué quiere

**Si el usuario ya describió lo que busca** (ej: "quiero leads de restaurantes en Colombia que necesiten delivery") → extrae los datos directamente y pasa a confirmar en un solo mensaje.

**Si el usuario NO tiene claro qué quiere** (ej: "quiero leads", "I need leads", "quiero clientes", o cualquier mensaje vago) → entra en modo sondeo:

Muestra este mensaje (en el idioma del usuario):

---
*Ejemplo en español:*

> ¡Hola! 👋 Voy a ayudarte a encontrar clientes potenciales para tu negocio.
>
> Esto funciona así: yo busco en Google, LinkedIn e Instagram empresas que probablemente necesitan lo que tú vendes — y te doy una lista con su contacto y una nota de qué tan fácil es venderles.
>
> Para empezar necesito entender dos cosas súper simples:
>
> **1. ¿Qué vendes?** (en una frase, como si se lo dijeras a un amigo)
>
> Por ejemplo:
> - "Vendo seguros para carros"
> - "Hago páginas web para negocios"
> - "Ofrezco servicio de limpieza para oficinas"
> - "Vendo software de nómina para empresas"
> - "Doy clases de inglés para adultos"
>
> **2. ¿A qué tipo de empresa le quieres vender?**
>
> Por ejemplo:
> - "A restaurantes"
> - "A clínicas o consultorios médicos"
> - "A empresas de transporte"
> - "A cualquier empresa que tenga más de 10 empleados"
> - "No sé, ayúdame a decidir"
>
> ¿Qué me cuentas? 😊

---

Si el usuario responde "no sé" o "ayúdame a decidir" al tipo de empresa → pregúntale quién es su cliente ideal con estas preguntas simples:

> Cuéntame un poco más:
> - ¿Tu producto/servicio es para empresas grandes, medianas o pequeñas?
> - ¿Lo que vendes requiere que la empresa tenga vehículos, empleados, computadores, un local físico...?
> - ¿Has vendido antes? ¿A quién le fue más fácil venderle?

Con esas respuestas, sugiérele 3 nichos concretos y pregunta cuál prefiere.

### 1.3 Confirmar parámetros antes de buscar

Una vez que tienes el nicho y el producto/servicio, confirma todo en un solo mensaje antes de buscar:

> Perfecto, ya tengo todo lo que necesito 🎉
>
> Voy a buscar: **[tipo de empresa]** en **[país/región]**
> Para ofrecerles: **[lo que vende el usuario]**
> Cantidad: **[n] leads**
> Idioma de los leads: **[idioma]**
>
> ¿Todo correcto? ¿O quieres cambiar algo?

Si el usuario no especificó país, región o cantidad → asume Colombia, leads en español, 20 leads, y menciónalo en la confirmación para que pueda corregir.

---

## Paso 2 — Ejecutar búsquedas

Informa al usuario con entusiasmo (en su idioma):

> 🔍 ¡Arrancamos! Voy a revisar Google, LinkedIn e Instagram buscando empresas que puedan necesitar lo que vendes. Esto tarda unos 2-3 minutos...

Construye 10-14 queries. Incluye queries de dos tipos:

**Tipo A — Encontrar empresas (6-8 queries):**
- `"[nicho]" "[país]" site:linkedin.com/company`
- `"[nicho]" "[país]" contacto OR email OR "escríbenos"`
- `"[nicho]" "[país]" "presupuesto" OR "cotizar" OR "solicitar"`
- `"[nicho]" filetype:pdf directorio OR listado "[país]"`
- `"[nicho]" "[país]" instagram OR facebook`
- `"[nicho]" "[país]" "[problema que resuelve el producto del usuario]"`

**Tipo B — Encontrar el contacto directo (4-6 queries, NUEVO):**
- `"[nombre empresa]" "gerente general" OR "CEO" OR "dueño" OR "propietario" site:linkedin.com/in`
- `"[nombre empresa]" "director" OR "fundador" OR "founder" linkedin`
- `"[nombre empresa]" celular OR "whatsapp" OR "móvil" contacto`
- `"[nicho]" "[país]" "gerente" contacto celular`
- `"[nicho]" "[país]" CEO OR founder site:linkedin.com/in`

### Modo NATIVE — WebSearch de Claude
Usa WebSearch para cada query. Extrae nombre, URL, snippets, señales.

### Modo SERPER — Serper.dev API
```bash
curl -s -X POST "https://google.serper.dev/search" \
  -H "X-API-KEY: [serper_api_key]" \
  -H "Content-Type: application/json" \
  -d '{"q": "[query]", "num": 10, "hl": "[idioma]", "gl": "[país_code]"}'
```

### Modo HYBRID — Serper + Hunter.io
Primero todas las búsquedas con Serper, luego para cada dominio encontrado:
```bash
curl -s "https://api.hunter.io/v2/domain-search?domain=[dominio]&limit=5&api_key=[hunter_api_key]"
```
De Hunter extrae: email, nombre, apellido, cargo del contacto. Si hay emails, suma +10 al score.

### Extracción de datos por lead

Por cada empresa encontrada, captura:

| Campo | Qué extraer | Prioridad |
|-------|-------------|-----------|
| `nombre_empresa` | Nombre oficial | Alta |
| `website` | URL principal | Alta |
| `linkedin_empresa` | URL perfil empresa `/company/` | Alta |
| `industria` | Sector o subsector | Alta |
| `ubicacion` | Ciudad, país | Alta |
| `tamaño_estimado` | Número de empleados | Media |
| `email_empresa` | Email general o de contacto | Alta |
| `telefono_fijo` | Teléfono de oficina | Media |
| `decision_maker_nombre` | Nombre completo del gerente/dueño/CEO | **Crítico** |
| `decision_maker_cargo` | CEO, Gerente General, Dueño, Director, Fundador | **Crítico** |
| `decision_maker_linkedin` | URL perfil personal `/in/[nombre]` | **Crítico** |
| `celular_directo` | Número celular/móvil (NO fijo) — preferir WhatsApp | **Crítico** |
| `whatsapp` | Número WhatsApp si está publicado | **Crítico** |
| `ultima_actividad` | Fecha último post o publicación | Media |
| `redes_sociales` | Twitter, Instagram, Facebook de la empresa | Baja |
| `buy_signals` | Frases o señales de necesidad detectadas | Alta |
| `fuente` | Dónde fue encontrado | Media |
| `notas` | Datos adicionales | Baja |

**Reglas para el contacto directo:**
- Si encuentras un celular Y un teléfono fijo → muestra SOLO el celular
- Si encuentras WhatsApp → marcarlo claramente como preferido
- Si encuentras el LinkedIn personal del decision-maker → es el campo más valioso para contacto directo por DM
- Cargos que cuentan como decision-maker: CEO, Gerente General, Gerente Comercial, Director General, Dueño, Propietario, Fundador, Co-Fundador, Presidente, Owner, Founder

Deduplica: misma empresa en múltiples resultados → un solo registro consolidado.

---

## Paso 3 — Scoring y categorización

Score de **0 a 100** (más bonus):

| Criterio | Puntos | Cómo evaluarlo |
|----------|--------|----------------|
| Website activo y funcional | 10 | URL accesible |
| LinkedIn empresa con actividad < 60 días | 10 | Perfil con posts recientes |
| Email o contacto general encontrado | 10 | Email público o formulario activo |
| Actividad en redes sociales < 30 días | 10 | Último post reciente |
| Tamaño > 10 empleados | 10 | Mencionado en LinkedIn, web o directorio |
| **Decision-maker identificado (nombre + cargo)** | **15** | Nombre real de quien decide |
| **LinkedIn personal del decision-maker encontrado** | **15** | URL `/in/` verificado |
| **Celular o WhatsApp directo encontrado** | **10** | Número móvil, no fijo |
| Buy signal detectado | 10 | Señal real de necesidad activa |
| *(Bonus) Email verificado por Hunter.io* | +10 | Solo modo HYBRID |

**Categorías:**

| Categoría | Score | Lo que significa en palabras simples |
|-----------|-------|--------------------------------------|
| `COLD` | 0–25 | La empresa existe pero no sabes a quién llamar ni si te necesita |
| `WARM` | 26–50 | Puedes contactarlos, pero no sabes si están interesados |
| `HOT` | 51–75 | Hay señales de que pueden necesitar lo que vendes, y sabes cómo contactarlos |
| `PREMIUM` | 76–100 | Sabes quién decide, tienes su celular o LinkedIn, y hay señal de interés |

---

## Paso 4 — Generar el reporte HTML

Crea `leads/leads-[nicho]-[fecha].html` en el idioma del usuario. Diseño moderno, profesional y **100% PDF-safe**.

### Reglas PDF-safe (OBLIGATORIAS):

```css
* {
  -webkit-print-color-adjust: exact !important;
  print-color-adjust: exact !important;
  color-adjust: exact !important;
}
@media print {
  .lead-card { page-break-inside: avoid; break-inside: avoid; }
  .no-print { display: none !important; }
  @page { margin: 20mm; }
  .lead-card { box-shadow: none !important; border: 1px solid #D1D5DB !important; }
}
```

- **NO gradientes** en fondos importantes → colores sólidos.
- **NO `opacity` ni `rgba` con transparencia** en elementos clave.
- Contraste AAA en todos los textos sobre color.

### Paleta PDF-safe:

```
COLD:    fondo #E5E7EB  texto #374151
WARM:    fondo #DBEAFE  texto #1E40AF
HOT:     fondo #FEE2E2  texto #991B1B
PREMIUM: fondo #FEF3C7  texto #92400E

Score COLD:    bg #6B7280  texto #FFFFFF
Score WARM:    bg #2563EB  texto #FFFFFF
Score HOT:     bg #DC2626  texto #FFFFFF
Score PREMIUM: bg #D97706  texto #FFFFFF
```

### Estructura del HTML:

**1. Encabezado** — Título, región, fecha, modo, badges de conteo por categoría.

**2. Dashboard de filtros (`class="no-print"`):**
- Buscador por nombre
- Filtros: Todos / PREMIUM / HOT / WARM / COLD
- Botón "Descargar PDF" → `window.print()`

**3. Tabla resumen ejecutivo (visible en PDF):**
Rank | Empresa | Score | Categoría | Decisor | Contacto directo | Ubicación

**4. Tarjetas de leads (mayor a menor score).**

Cada tarjeta tiene DOS secciones claramente separadas:

**Sección A — La empresa:**
- Nombre + badge categoría + score + barra de progreso
- 🌐 Website · 💼 LinkedIn empresa · 📧 Email · 📍 Ubicación · 👥 Tamaño · 🏢 Industria · 📅 Última actividad

**Sección B — A quién contactar (destacada visualmente con fondo diferente):**
- 👤 Nombre del decisor + cargo
- 📱 Celular / WhatsApp (si se encontró) — en verde si es WhatsApp
- 🔗 LinkedIn personal (botón azul con texto "Ver perfil · Enviar DM")
- Si no se encontró el decisor → mostrar mensaje amigable: "No encontramos el contacto directo — te recomendamos buscar en LinkedIn: [link búsqueda]"

**Sección C — Señales de compra** (si existen):
- Lista de buy signals en color ámbar llamativo

**5. Metodología** (al final, visible en PDF) — fecha, fuentes, scoring, disclaimer.

### JavaScript para filtros y PDF:

```javascript
function filterLeads(category) {
  document.querySelectorAll('.lead-card').forEach(card => {
    card.style.display = (category === 'ALL' || card.dataset.category === category) ? '' : 'none';
  });
  document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelector('[data-filter="' + category + '"]').classList.add('active');
}
function searchLeads(query) {
  document.querySelectorAll('.lead-card').forEach(card => {
    const name = card.querySelector('.company-name').textContent.toLowerCase();
    card.style.display = name.includes(query.toLowerCase()) ? '' : 'none';
  });
}
function downloadPDF() { window.print(); }
```

---

## Paso 5 — Generar CSV para CRM

Crea `leads/leads-[nicho]-[fecha].csv`:

```
Empresa,Score,Categoría,Website,LinkedIn Empresa,Email,Email Verificado,Ubicación,Industria,Tamaño,Decisor Nombre,Decisor Cargo,Decisor LinkedIn,Celular/WhatsApp,Buy Signals,Fuente,Última Actividad,Notas
```

- Escapar comas dentro de campos con comillas dobles
- Todos los leads incluidos, incluso COLD
- Ordenar de mayor a menor score
- "Email Verificado" = `Sí` solo si vino de Hunter.io

---

## Paso 6 — Guardar, abrir y presentar resultados

1. Crea la carpeta `leads/` si no existe:
```bash
mkdir -p leads
```

2. Guarda ambos archivos en `leads/`.

3. Abre el HTML automáticamente en el navegador:
```bash
start leads/leads-[nicho]-[fecha].html
```
*(En Mac usar `open`, en Linux usar `xdg-open`)*

4. Muestra el resumen en el idioma del usuario:

```
✅ ¡Listo! Tu reporte está listo · Modo: [NATIVE/SERPER/HYBRID]

📊 [n] leads encontrados
   🥇 PREMIUM: [n]  ← tienen decisor + celular/LinkedIn + señal de compra
   🔥 HOT:     [n]  ← señales de interés + contacto localizable
   🌡️ WARM:    [n]  ← presencia activa, sin señal directa aún
   ❄️ COLD:    [n]  ← existen pero falta información de contacto

📞 Decisores encontrados con contacto directo: [n] de [total]
   → [n] con celular/WhatsApp
   → [n] con LinkedIn personal para DM

📁 Guardado en:
   → leads/leads-[nicho]-[fecha].html  (se abrió en tu navegador)
   → leads/leads-[nicho]-[fecha].csv   (para importar a tu CRM)

💡 Los 3 más fáciles de contactar HOY:
   1. [Empresa] — [Decisor], [Cargo] — [Celular o LinkedIn]
   2. [Empresa] — [Decisor], [Cargo] — [Celular o LinkedIn]
   3. [Empresa] — [Decisor], [Cargo] — [Celular o LinkedIn]

⚙️  Para cambiar el modo de búsqueda escribe: "cambiar modo"
```

5. Cierra con una pregunta simple y amigable:
> "¿Quieres que busque más leads, filtre por ciudad, o te ayude a preparar un mensaje para contactar a alguno de ellos? 😊"

---

## Reglas para slides y presentaciones de costos

Si el usuario pide generar un deck, slide o presentación de costos/precios de herramientas, aplicar siempre estas reglas antes de empezar:

**Preguntar primero:**
- ¿Plan Enterprise/Business (por asientos, escalable para equipo) o plan Pro/individual?
- Si el usuario no especifica → preguntar antes de generar. No asumir Pro.

**Cada slide de herramienta debe incluir obligatoriamente:**
- Precio por asiento (mensual y anual)
- Total del equipo a 2, 5 y 10 asientos
- Total anual (no solo mensual)
- Ahorro vs pago mensual en dólares (no solo porcentaje)
- Requerimientos de tokens/créditos si la herramienta usa facturación por consumo (ej: Gamma credits, Anthropic top-up)

**Anti-patrón:** listar solo características sin valores totales en dólares → el usuario rechazará y pedirá rehacerlo.

---

## Fallbacks y manejo de errores

- **Si Serper falla** (401, 429, timeout) → avisa, cambia a `native` solo para esta sesión, continúa sin interrumpir.
- **Si Hunter no encuentra emails** → omite silenciosamente, sin restar puntos.
- **Si no se encuentra el decisor de ninguna empresa** → en el resumen final avisa: "No encontré contactos directos en esta búsqueda. Para conseguirlos, te recomiendo buscar manualmente en LinkedIn los perfiles de gerentes de estas empresas."
- **Si una búsqueda no da resultados** → reformula con sinónimos y reintenta una vez antes de descartarla.
- **Si hay menos leads que los pedidos** → genera el reporte con los que encontraste y explica con palabras simples por qué puede haber menos.
- **Si el usuario escribe algo confuso** → no adivines. Pregunta de forma amigable: "¡No te preocupes! Solo cuéntame: ¿qué vendes y a qué tipo de empresa le quieres vender? Con eso ya puedo empezar 😊"
- **Si la API key tiene formato inválido** → avisa antes de intentar la llamada y ofrece corregirla paso a paso.
