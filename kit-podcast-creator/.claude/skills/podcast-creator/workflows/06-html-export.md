# Workflow 06 — Exportar HTML

Genera dos archivos HTML por episodio: un paquete de producción interno y una página pública del episodio. Ambos se abren automáticamente en el browser al generarse.

**Regla fundamental: Leer todos los archivos del episodio existentes antes de generar. No inventar datos faltantes — mostrar secciones vacías con placeholder visible.**

---

## Paso 0 — Cargar todos los datos del episodio

Pregunta el número de episodio si no está claro en el contexto.

Lee los siguientes archivos (los que existan en el directorio):
1. `podcast-profile.json` — identidad del podcast (requerido)
2. `episodio-[NNN]-*.md` — script del episodio
3. `grabacion-ep[NNN].md` — checklists de grabación
4. `social-ep[NNN].md` — plan de social media
5. `shownotes-ep[NNN].md` — show notes y metadatos

Si falta algún archivo importante (script o show notes), informa al usuario:
> "No encontré [archivo]. ¿Quieres generarlo primero o incluyo esa sección como pendiente en el HTML?"

Espera respuesta antes de continuar.

---

## Paso 1 — Generar Archivo 1: Production Packet (interno)

**Nombre:** `production-ep[NNN].html`

**Diseño:** Dark mode. Tipografía monoespaciada para el script, sans-serif para el resto. Sidebar de navegación sticky. Imprimible con `@media print`.

### Estructura HTML completa

**Head:**
- Título: `[Nombre Podcast] — EP.[NNN] Production Packet`
- Estilos inline (no CDN — debe funcionar offline)
- Paleta: fondo `#0f0f0f`, cards `#1a1a1a`, texto `#e8e8e8`, acento = color principal del perfil

**Header:**
```
[Logo/nombre del podcast en grande]
EP.[NNN] — [Título del episodio]
[Formato] · ~[X] min · [Fecha si disponible]
[Barra de progreso visual: % de producción completado según archivos encontrados]
```

**Nav lateral (sticky):** links a cada sección del documento

**Sección 1 — Datos del episodio:**
Tabla con: número EP, título, formato, host(s), duración estimada, referencia cultural, takeaway principal. Si algún dato no existe, celda con "—".

**Sección 2 — Script completo:**
El script del episodio con estilos visuales aplicados:
- `[PAUSA]` → chip amarillo `#f5c518`
- `[ÉNFASIS]` → texto negrita + color de acento del perfil
- `[SFX: ...]` → chip azul `#4a9eff` con el contenido del SFX
- `[TRANSICIÓN]` → línea divisoria con icono ♪ y texto "TRANSICIÓN"
- `[NOTA: ...]` → caja `#2a2a2a` borde izquierdo amarillo, etiqueta "NOTA" — no imprimible (`@media print { display: none }`)
- Cada segmento en una card con número, título del segmento y duración estimada en el header de la card

Si no hay script: sección con mensaje "Script no generado — usa el workflow 01-episodio.md"

**Sección 3 — Checklist de grabación:**
Checkboxes HTML funcionales (`<input type="checkbox">`) organizados en subsecciones:
- Pre-grabación
- Durante la grabación
- Post-grabación
- Edición

Cada ítem del `grabacion-ep[NNN].md` como checkbox. Si no hay archivo de grabación: checklist genérico básico.

**Sección 4 — Plan Social Media:**
Tabs por día (Día 1, Día 2, Día 3) usando CSS puro (`:checked` trick). Dentro de cada tab, el copy por plataforma en `<textarea>` readonly con botón "Copiar" (JS inline). Fechas calculadas visibles sobre cada tab.

Si no hay archivo social: sección con mensaje "Social media no generado — usa el workflow 04-social-media.md"

**Sección 5 — Show Notes:**
Dos textareas con botón "Copiar": descripción HTML de Spotify y show notes en Markdown.

Si no hay archivo show notes: sección con mensaje "Show notes no generadas — usa el workflow 05-show-notes.md"

**Footer del documento:**
Lista de assets del episodio con estado: ✓ generado / ○ pendiente

---

## Paso 2 — Generar Archivo 2: Show Notes Page (pública)

**Nombre:** `shownotes-ep[NNN].html`

**Diseño:** Usa los colores del `podcast-profile.json`. Moderna, limpia, responsive (mobile-first). Pensada para compartir como página web del episodio.

### Estructura HTML completa

**Head:**
- Título: `EP.[NNN]: [Título] — [Nombre Podcast]`
- Meta description: descripción corta del episodio
- OG tags para compartir en redes (og:title, og:description, og:image)
- Estilos inline. Paleta = colores del perfil del podcast

**Sección Hero:**
- Fondo con color primario del podcast (gradiente si queda bien)
- Número de episodio grande, color acento: `EP.NNN`
- Título del episodio (h1, blanco, bold)
- Descripción corta del episodio (subtítulo, blanco/opacity)
- Botones de escucha: Spotify · Apple Podcasts · YouTube (solo los que tienen link en el perfil). Abren en nueva pestaña.

**Sección Quote destacada:**
La frase más impactante del episodio en tipografía grande, comillas estilizadas, color acento. Atribución: `— EP.[NNN], [Nombre Podcast]`. Si no hay quote disponible, omitir esta sección.

**Sección "En este episodio":**
Lista visual de los puntos cubiertos. Iconos ✦ o bullets estilizados con el color de acento.

**[Si hay timestamps] Sección "Navega el episodio":**
Tabla visual. Si los timestamps son solo texto (sin link), mostrarlos como tabla estática.

**[Si hay invitado] Sección "Invitado":**
Card con: avatar placeholder (inicial del nombre sobre fondo de color), nombre (h3), bio, links de redes como botones pequeños.

**Sección "Recursos mencionados":**
Cards o lista con los recursos del episodio. Si no hay recursos, omitir.

**Sección "Sobre el podcast":**
Mini bio del podcast: nombre, tagline, foto/avatar del host, bio corta del host, botones de todas las plataformas del perfil.

**Sección CTA:**
Fondo con color secundario del podcast.
Texto: "No te pierdas el próximo episodio"
Botones: seguir en Spotify / Apple / YouTube.

**Footer:**
Nombre del podcast · copyright · links de redes sociales del perfil.

---

## Paso 3 — Guardar y abrir

1. Escribe `production-ep[NNN].html`.
2. Escribe `shownotes-ep[NNN].html`.
3. Ábrelos en el browser:

```bash
start production-ep[NNN].html
start shownotes-ep[NNN].html
```

4. Muestra resumen final:

```
══════════════════════════════════════════════════════════
  Paquete de producción completo — EP.[NNN] ✓
══════════════════════════════════════════════════════════
  📦 production-ep[NNN].html   → Abierto en browser
  🌐 shownotes-ep[NNN].html    → Abierto en browser

  Incluye:
  [✓/○] Script con marcadores visuales
  [✓/○] Checklists de grabación interactivos
  [✓/○] Plan social media 3 días (copy copiable)
  [✓/○] Show notes listas para publicar
  [✓/○] Página pública del episodio
══════════════════════════════════════════════════════════
```

5. Pregunta: "¿Hay alguna sección que quieras ajustar o regenerar?"
