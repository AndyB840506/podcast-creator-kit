# Workflow 04 — Plan de Lanzamiento Social Media (3 días)

Genera copy listo para publicar en 3 días, adaptado al episodio y a las plataformas del podcast.

**Regla fundamental: El copy debe sonar como el host, no como una marca corporativa. Aplicar las `reglas_tono` del perfil antes de escribir.**

---

## Paso 0 — Cargar datos

1. Lee `podcast-profile.json`: nombre del podcast, host(s), tono, `reglas_tono`, `hashtags_base`, plataformas, links.
2. Busca el script del episodio (`episodio-[NNN]-*.md`). Si existe, lee el script completo y extrae:
   - La frase más impactante o memorable (para el Día 2)
   - El dato más sorprendente del episodio
   - El takeaway principal
   - Nombre del invitado si es una entrevista
3. Si no hay script, pregunta al usuario: "¿Cuál es la frase más memorable del episodio? (la usaremos para el post del Día 2)"

**Detecta el formato del episodio:**
- Si es **interview**: Busca el nombre del invitado. Si no está en el script, pregunta: "¿Cuál es el nombre del invitado y en qué redes tiene presencia?"
- Si es **co-host**: Extrae los nombres de todos los co-hosts participantes en este episodio.

---

## Paso 1 — Datos del lanzamiento

Pregunta en un solo mensaje:

> 1. ¿Cuál es el número y título del episodio?
> 2. ¿Cuál es el link del episodio en Spotify (o la plataforma principal)? (si no está disponible aún, escribe "pendiente")
> 3. ¿Cuándo publicas el episodio? (día y hora aproximada — necesito calcular los 3 días del plan)
> 4. ¿Hay algún dato sorprendente, controversia o ángulo que quieras destacar en el lanzamiento?

Calcula automáticamente:
- **Día 1 (Intriga):** 2 días antes de la fecha de publicación
- **Día 2 (Contenido):** 1 día antes
- **Día 3 (Lanzamiento):** día y hora de publicación

---

## Paso 2 — Generar plan completo

Genera todo el copy de una vez. Usa el tono y `reglas_tono` del perfil. No uses lenguaje genérico de marketing.

**Adaptaciones por formato:**

**Si es interview:**
- **Día 1 (Intriga):** Menciona al invitado con misterio: "Alguien que [logro/expertise relevante] viene a..." — NO reveles el nombre completo todavía
- **Día 2 (Contenido):** La quote es preferiblemente DEL INVITADO (algo que dijo durante la entrevista que es impactante)
- **Día 3 (Lanzamiento):** Etiqueta al invitado en TODAS las plataformas donde tenga cuenta (usa @[handle] si está disponible)

**Si es co-host:**
- Alterna quién "firma" el post de cada día para aprovechar las audiencias de ambos hosts
- Ejemplo: Día 1 firma [Host1], Día 2 firma [Host2], Día 3 firma [Host1]
- La dinámica especial entre co-hosts es parte del copy (ej: "Hoy [Host1] vs [Host2] sobre..." o "Especial de co-hosting")

---

### DÍA 1 — INTRIGA (2 días antes)
**Objetivo:** Generar curiosidad sin revelar el tema completo.

**Instagram / Facebook (caption):**
```
[Copy de 3-5 líneas. Hook misterioso que genere pregunta en la mente del oyente.
No revela el tema directamente. Termina con una pregunta provocadora o afirmación que incomode.]

[hashtags_base + 3-5 hashtags del tema del episodio]
```

**Stories (texto para imagen o video):**
```
[Máx 10 palabras + emoji. Diseñado para poner sobre una imagen o video corto.]
```

**LinkedIn (si está en plataformas):**
```
[Copy más reflexivo. Mismo hook pero con contexto profesional. 2-3 párrafos cortos.
Termina con una pregunta que invite a comentar.]
```

**X / Twitter:**
```
[Máx 280 caracteres. Directo al hueso. Sin hashtags o máximo 1-2.]
```

**TikTok / Reels (hook para video corto):**
```
[Primera línea gancho para video de 15-30 seg. Formatos que funcionan:
"POV: descubres que [dato inesperado]..."
"Nadie habla de esto pero [tema del episodio]..."
"Lo que [referencia] no te cuenta sobre [tema]..."]
```

---

### DÍA 2 — CONTENIDO (día anterior)
**Objetivo:** Dar valor real con una quote o fragmento del episodio.

**Quote seleccionada del episodio:**
> "[Frase más impactante extraída del script o indicada por el usuario]"
> — [Nombre del Podcast], EP.[NNN]

**Instagram / Facebook (caption):**
```
[La quote como centro del post. 3-4 líneas de contexto sobre por qué importa.
CTA suave al final: "Mañana el episodio completo en [plataforma principal] ↑ link en bio"]

[hashtags]
```

**Stories:**
```
[La quote formateada para card: texto grande y legible. Fondo simple.]
```

**LinkedIn:**
```
[Post expandido con reflexión sobre la quote. Por qué esta idea importa en el contexto
del nicho del podcast. 3-4 párrafos. Link directo al episodio si está disponible.]
```

**X / Twitter:**
```
[La quote con atribución. Menos de 280 chars. Sin reducir la frase — que impacte completa.]
```

---

### DÍA 3 — LANZAMIENTO (día de publicación)
**Objetivo:** Llevar tráfico al episodio. CTA directo y claro.

**Instagram / Facebook (caption):**
```
[Gancho fuerte en la primera línea (lo que se ve antes del "ver más").
De qué trata el episodio en 2-3 líneas.
Qué van a aprender o sentir los oyentes.
CTA: "Link en bio" o link directo si la plataforma lo permite.]

[hashtags completos: hashtags_base + tema + formato + plataforma]
```

**Stories:**
```
[Sticker de link al episodio. Texto: "YA DISPONIBLE 🎙️" + nombre corto del episodio.
O video corto (15 seg) con clip del momento más impactante.]
```

**LinkedIn:**
```
[Anuncio de lanzamiento profesional. 3 bullets de lo que aprenderán.
Por qué escuchar este episodio ahora. Link directo al episodio.]
```

**X / Twitter (thread de 3 tweets):**
```
Tweet 1: Hook — la idea más poderosa del episodio (máx 280 chars)
Tweet 2: Lo que aprenderán en el episodio — 3 puntos concretos
Tweet 3: Link al episodio + CTA
```

**TikTok / Reels:**
```
[Hook para clip del episodio o video de anuncio.
Primera frase: [la más impactante del episodio].
CTA final: "Episodio completo — link en bio"]
```

---

## Paso 3 — Resumen de assets necesarios

```
══════════════════════════════════════════════════
  Assets para el lanzamiento de EP.[NNN]
══════════════════════════════════════════════════
  ✓ Copy generado para 3 días × 5 plataformas

  Aún necesitas crear:
  □ Imagen cuadrada 1:1 del episodio → workflows/03-artwork.md
  □ Imagen vertical 9:16 para Stories → workflows/03-artwork.md
  □ Clip de audio 30-60 seg (momento más impactante)
  □ Card visual con la quote del Día 2
══════════════════════════════════════════════════
  Fechas del plan:
  Día 1 (Intriga):    [fecha calculada]
  Día 2 (Contenido):  [fecha calculada]
  Día 3 (Lanzamiento): [fecha y hora de publicación]
══════════════════════════════════════════════════
```

---

## Paso 3.5 — Kit de cross-promotion para el invitado (si es interview)

**Solo si es episodio de interview:**

Genera un documento separado con 3 posts listos para que el invitado comparta en sus propias redes. Estos posts son:
- **Más personalizados** (mencionan al host y al podcast)
- **Listos para copiar-pegar** (el invitado solo debe copiar, pegar y publicar)
- **Incluyen link del episodio**

```
KIT DE CROSS-PROMOTION — [INVITADO]
═════════════════════════════════════════════════════

GRACIAS por participar en [NOMBRE PODCAST], EP.[NNN]!

Aquí hay 3 posts que puedes compartir en tus redes —
copia el que prefieras:

─────────────────────────────────────────────────────
OPCIÓN 1 — PARA INSTAGRAM / FACEBOOK / LINKEDIN

[Frase de introducción + logro/valor del episodio]
Acabo de estar en @[nombre_podcast] hablando sobre [tema].
Escúchalo: [LINK]

[hashtags relevantes a la industria del invitado]
#[nombre_podcast]

─────────────────────────────────────────────────────
OPCIÓN 2 — PARA TWITTER / X

Honrado de estar en @[nombre_podcast] EP.[NNN].
[Una frase clave que dijiste en la entrevista]
Episodio completo → [LINK]

─────────────────────────────────────────────────────
OPCIÓN 3 — VERSIÓN LARGA PARA LINKEDIN

Me acaba de entrevistar [Host] de @[nombre_podcast] 
sobre [tema]. Fue una conversación fascinante sobre...
[2-3 líneas del contexto / por qué fue importante]

Si te interesa este tema, escúchalo: [LINK]

#Podcast #[Tema] #[TuExpErtise]

─────────────────────────────────────────────────────
```

Presenta este kit con la nota: "Puedes enviarle esto al invitado para que comparta en sus propias redes — así se amplifica la audiencia del episodio sin que tengas que pedírselo después."

---

## Paso 4 — Guardar

1. Guarda como `social-ep[NNN].md` en el directorio actual.
2. Pregunta: "¿Quieres generar las show notes o exportar todo el paquete en HTML?"
