# Workflow 05 — Show Notes y Metadatos de Distribución

Genera las notas del episodio optimizadas para SEO, listas para publicar en Spotify, Apple Podcasts y web.

**Regla fundamental: El título de Spotify no puede superar 80 caracteres. Verificar el conteo explícitamente antes de entregar.**

---

## Paso 0 — Cargar datos

1. Lee `podcast-profile.json`: nombre del podcast, host(s), links de distribución, categoría, tagline.
2. Busca el script del episodio (`episodio-[NNN]-*.md`). Si existe, lee y extrae:
   - Tema central y ángulo
   - 4-5 puntos principales cubiertos en el episodio
   - Recursos, libros, herramientas o personas mencionadas
   - Nombre del invitado si aplica (formato interview)
3. Si no hay script, ve al Paso 1 para preguntar.

---

## Paso 1 — Datos adicionales

Pregunta solo lo que no encontraste en los archivos existentes. Si el script ya tiene toda la información, salta directamente al Paso 2.

> 1. ¿Cuál es el número y título completo del episodio?
> 2. ¿Hay links, libros, herramientas o personas que mencionaste? (lista rápida — pueden ser sin URL)
> 3. ¿Tienes timestamps? (ej: 00:03:20 — Contexto histórico) — si no, omitimos esa sección
> 4. [Si interview] ¿Cómo pueden los oyentes encontrar al invitado? (redes, web, email público)

---

## Paso 2 — Generar metadatos completos

### A. Títulos del episodio

**Título completo:**
`[NOMBRE PODCAST] — EP.[NNN]: [Título descriptivo del episodio]`

**Título para Spotify** (máx 80 chars):
`EP.[NNN]: [Título optimizado]`
**→ Conteo: [N] caracteres** ✓ (verde si ≤80) / ✗ (rojo si >80, proponer versión corta)

**Título para Apple Podcasts** (máx 255 chars, mismas reglas):
`EP.[NNN]: [Título completo o igual al de Spotify]`

---

### B. Descripción corta (para Apple Podcasts)

Máximo 150 caracteres. Debe incluir el beneficio concreto para el oyente.

`[Descripción de exactamente 150 chars o menos]`
**→ Conteo: [N] caracteres**

---

### C. Descripción completa para Spotify (HTML)

```html
<p><strong>[Primera frase que engancha — el hook del episodio en 1-2 líneas]</strong></p>

<p>En este episodio de <em>[Nombre del Podcast]</em>, [descripción del tema y ángulo en 2-3 líneas. Qué hace especial a este episodio.]</p>

<p><strong>En este episodio:</strong></p>
<ul>
  <li>✦ [Punto 1 concreto que aprenderán]</li>
  <li>✦ [Punto 2]</li>
  <li>✦ [Punto 3]</li>
  <li>✦ [Punto 4 si aplica]</li>
</ul>

[Si hay invitado:]
<p><strong>Invitado:</strong> [Nombre] — [Bio de 1-2 líneas]. Síguelo en [links].</p>

[Si hay recursos:]
<p><strong>Recursos mencionados:</strong></p>
<ul>
  <li>[Recurso o libro] — [descripción breve si aplica]</li>
</ul>

<p>Síguenos en <a href="[spotify_link]">Spotify</a>[, <a href="[apple_link]">Apple Podcasts</a>][, <a href="[youtube_link]">YouTube</a>] para no perderte ningún episodio.</p>

<p>[hashtags_base separados por espacios]</p>
```

---

### D. Show Notes en Markdown (para web o blog)

```markdown
## EP.[NNN]: [Título completo]

[Descripción de 2-3 párrafos más detallada que la de Spotify. Incluir el contexto del tema y por qué importa ahora.]

### Escucha el episodio

[Spotify]([link]) | [Apple Podcasts]([link]) | [YouTube]([link])

### En este episodio

- [Punto 1 desarrollado en 1-2 líneas]
- [Punto 2]
- [Punto 3]
- [Punto 4]

[Si hay timestamps:]
### Timestamps

| Tiempo | Tema |
|--------|------|
| 00:00 | Intro y presentación |
[añadir según datos del usuario]

[Si hay invitado:]
### Sobre [nombre del invitado]

[Bio expandida de 3-4 líneas. Links a redes y web.]

### Recursos mencionados

- [Recurso] — [descripción]
[repetir por cada recurso]

---

*[Nombre del Podcast] — [tagline del perfil]. Síguenos en [plataformas].*
```

---

### E. Tags / Keywords SEO

Lista de 10-15 keywords para mejorar la descubribilidad del episodio. Mezcla:
- Keywords del tema específico del episodio (3-5)
- Keywords de la categoría general del podcast (3-4)
- Keywords de la audiencia target (2-3)
- Nombre del podcast y variantes (2)

`[keyword1], [keyword2], [keyword3], ...`

---

## Paso 3 — Guardar y presentar

1. Guarda como `shownotes-ep[NNN].md` en el directorio actual.
2. Muestra verificación de longitudes:

```
══════════════════════════════════════════
  Show Notes generadas ✓
══════════════════════════════════════════
  Episodio:           EP.[NNN]
  Título Spotify:     [N] chars [✓/✗]
  Descripción corta:  [N] chars [✓/✗]
  Keywords SEO:       [N] terms
  Archivo:            shownotes-ep[NNN].md
══════════════════════════════════════════
```

3. Si algún conteo está fuera de límite, propone versión corregida antes de guardar.
4. Pregunta: "¿Exportamos todo el paquete en HTML?"
