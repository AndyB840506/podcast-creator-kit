# Workflow 01 — Guion Completo del Episodio

Genera un script completo, palabra por palabra, para un episodio específico. Se adapta al formato del podcast (solo, entrevista, co-host).

**Regla fundamental: Lee `podcast-profile.json` primero. Si no existe, lanza `00-setup.md` antes de continuar.**

---

## Paso 0 — Cargar identidad del podcast

Lee `podcast-profile.json`. Extrae y ten disponible:
- Nombre del podcast, host(s), formato
- Tono y reglas de escritura (`reglas_tono`)
- Intro y outro templates
- Convención de numeración
- Duración promedio (`duracion_min`)
- Estilo de narración y word count target (`estilo_narración`, `word_count_target`)

Calcula el word count target para este episodio basado en la duración que el usuario confirme en Paso 1.

---

## Paso 0.5 — Detectar formato de ESTE episodio (nuevo)

El perfil del podcast tiene un formato default, pero cada episodio puede ser diferente.

Pregunta:

> El perfil de tu podcast es **[formato del perfil]**, pero cada episodio puede variar.
> ¿Cómo es **este episodio específico**?
> 1. **Solo** — solo tú hablas
> 2. **Con co-host** — [mostrar nombres de co-hosts del perfil si existen]
> 3. **Entrevista** — tienes un invitado especial

Espera la respuesta. Guarda `formato_episodio: "solo" | "interview" | "co-host"` como override del formato del perfil para este episodio solamente.

---

## Paso 1 — Datos del episodio

Pregunta en un solo mensaje adaptado al formato del perfil:

> **Para este episodio:**
> 1. ¿Cuál es el número de episodio? (ej: 001, 014...)
> 2. ¿Cuál es el tema o título tentativo?
> 3. ¿Cuál es el ángulo único — qué perspectiva o enfoque vas a tomar que no hace todo el mundo?
> 4. ¿Hay alguna referencia cultural, historia, persona o evento que sirva como hilo conductor?
> 5. ¿Cuál es el takeaway principal — qué debe llevarse el oyente al terminar?

**Si formato = `interview`**, agrega al mismo mensaje:
> 6. Nombre del invitado y cargo o descripción en una línea
> 7. ¿De qué 3 temas quieres hablar con el invitado?
> 8. ¿Hay algún tema tabú o pregunta que NO debes hacer?

**Si formato = `co-host`**, agrega:
> 6. ¿Quién abre el episodio hoy?
> 7. ¿Hay dinámicas especiales entre los hosts para este episodio?

---

## Paso 1.5 — Generar cuestionario de entrevista y briefing (si es interview)

**Solo si `formato_episodio = "interview"`:**

### A. Cuestionario de entrevista (10-15 preguntas escaladas)

Genera un documento con estructura clara:

```
CUESTIONARIO DE ENTREVISTA — EP.[NNN]
═════════════════════════════════════════════════════════
  Invitado: [nombre]
  Tema principal: [tema]
  Duración estimada: ~[X] minutos
═════════════════════════════════════════════════════════

BLOQUE DE BIENVENIDA (2-3 preguntas — fáciles, warm-up)
──────────────────────────────────────────────────────
1. ¿Quién eres y qué haces? [OBLIGATORIA]
2. ¿Cómo llegaste a esto? [OBLIGATORIA]
3. ¿Cómo te describes en 3 palabras? [OPCIONAL]

BLOQUE CENTRAL — TEMA PRINCIPAL (5-7 preguntas — escaladas)
──────────────────────────────────────────────────────
4. [Primera pregunta exploradora sobre tema 1]
5. [Pregunta de profundidad sobre tema 1 — "¿por qué crees que...?"]
6. [Pregun­ta sobre tema 2 — cambio de enfoque]
7. [Pregunta provocadora — desafiar creencia o perspectiva]
8. [Pregunta sobre tema 3 — síntesis]
[notas de escucha activa: si el invitado menciona X, profundizar con "¿y cómo afectó eso...?"]

BLOQUE DE CIERRE (2-3 preguntas)
──────────────────────────────────────────────────────
N-2. ¿Qué consejo le darías a alguien que está empezando en...? [OBLIGATORIA]
N-1. ¿Cuál es tu visión para el futuro de...? [OBLIGATORIA]
N. ¿Dónde nos pueden encontrar tus oyentes? [redes, web, email] [OBLIGATORIA]

PREGUNTAS DE RESERVA (si queda tiempo o necesitas reemplazar respuestas cortas)
──────────────────────────────────────────────────────
R1. [Pregunta alternativa profunda]
R2. [Pregunta sobre dato sorprendente que el invitado mencionó]
```

Marca explícitamente qué preguntas son OBLIGATORIAS vs OPCIONALES. Esto permite al host adaptar según el ritmo de la conversación.

### B. Documento de briefing para el invitado

Genera un documento listo para enviar al invitado 3-5 días antes de la grabación:

```
──────────────────────────────────────────
  INVITACIÓN A [NOMBRE PODCAST]
──────────────────────────────────────────
  Episodio:  EP.[NNN] — [Título del episodio]
  Host:      [nombre del host]
  Duración:  ~[X] minutos
  Fecha:     [fecha tentativa si la hay]
──────────────────────────────────────────

DE QUÉ VAMOS A HABLAR

[Descripción de 3-4 párrafos del episodio y los 3 temas principales]

SOBRE [NOMBRE PODCAST]

[Nombre] — [tagline]. 
Audiencia: [descripción del oyente ideal].
Escúchalo en: [links de plataformas del perfil]

REQUISITOS TÉCNICOS

✓ Micrófono USB o auriculares con micrófono (NO bocinas)
✓ Cuarto silencioso, puertas cerradas (apaga AC/calefacción si es ruidosa)
✓ Auriculares puestos durante TODA la grabación
✓ Conexión a internet estable (wifi o cable, no datos móviles)
✓ Plataforma: [Riverside.fm / Zoom / la que uses]
✓ Link de sesión: [PENDIENTE — se enviará 24h antes]

TEMAS QUE CUBRIREMOS (en orden aproximado)

· [Tema 1] — contexto breve
· [Tema 2] — contexto breve
· [Tema 3] — contexto breve

NOTA IMPORTANTE

La grabación se usará para el podcast [Nombre Podcast]. Si hay algún tema que prefieras omitir o no abordar, avísame al menos 24h antes de la grabación.

──────────────────────────────────────────
```

Presenta ambos documentos al usuario antes de Paso 2, con la pregunta: "¿Revisas el cuestionario y el briefing antes de que empecemos con la arquitectura?"

---

## Paso 1.6 — Generar documento de sincronía pre-grabación (si es co-host)

**Solo si `formato_episodio = "co-host"`:**

Genera un documento breve para que los co-hosts se sincronicen antes de grabar:

```
SYNC PRE-GRABACIÓN — EP.[NNN]
─────────────────────────────────────────
  Tema del episodio: [tema]
  Ángulo: [ángulo único]
─────────────────────────────────────────

ASIGNACIÓN DE SEGMENTOS

· [Host1] abre con: [hook específico — primera frase que dice]
· [Host2] cubre: [segmento específico — ej: "Bloque principal A"]
· [Host1] cubre: [segmento específico — ej: "Bloque principal B"]
· Cierre a cargo de: [Host que cierra]

PUNTOS DE DEBATE PLANIFICADOS

· [Tema donde los hosts pueden tener opiniones diferentes — dejar espacio para intercambio natural]
· [Otro tema si aplica]

DINÁMICA ESPECIAL PARA ESTE EPISODIO

[Si hay algo único: un formato especial, un segmento donde intercambian mucho, un juego, etc.]

COSAS A EVITAR EN ESTE EPISODIO

[Si hay temas sensibles, errores de episodios anteriores, o cosas que no cuadran con el contenido, listarlas]

─────────────────────────────────────────
TIEMPO ESTIMADO DE HABLA POR HOST
  [Host1]: ~[X]% del episodio
  [Host2]: ~[X]% del episodio
─────────────────────────────────────────
```

Presenta el documento con la pregunta: "¿Revisas la asignación de segmentos? ¿Cambias algo antes de empezar?"

---

## Paso 2 — Presentar arquitectura para aprobación

**Antes de escribir el script**, calcula y muestra el word count target basado en:
- Duración del episodio confirmada en Paso 1
- Formato del episodio (`formato_episodio`)
- Estilo de narración (`estilo_narración`) del perfil

```
WORD COUNT TARGET PARA ESTE EPISODIO
════════════════════════════════════════
Duración: [X] minutos
Formato: [formato_episodio]
Estilo: [estilo_narración]
→ Word count target: ~[N] palabras

Esto es el tamaño esperado del script para evitar quedarse corto 
o demasiado largo.
════════════════════════════════════════
```

**Después de mostrar el word count**, presenta la arquitectura adaptada a ESTE episodio y espera aprobación explícita.

Calcula los tiempos según `duracion_min` del perfil:

| Duración total | Bloques principales | Tiempo por bloque |
|---|---|---|
| 15 min | 1 bloque | 6-7 min |
| 30 min | 2 bloques | 5-7 min c/u |
| 45 min | 3 bloques | 6-8 min c/u |
| 60 min | 3-4 bloques | 8-10 min c/u |

**Estructura base a presentar (con tiempos calculados y temas concretos):**

| # | Segmento | Duración | Contenido para ESTE episodio |
|---|----------|----------|------------------------------|
| 1 | Intro + Hook | 1-2 min | [hook específico del tema] |
| 2 | Bienvenida | 30 seg | [breve, no más] |
| 3 | Contexto del tema | 3-4 min | [por qué este tema ahora] |
| 4 | Bloque principal A | [X min] | [primer desarrollo] |
| 5 | [TRANSICIÓN] | — | música de transición |
| 6 | Bloque principal B | [X min] | [segundo desarrollo / giro] |
| [si entrevista] | Bloque de preguntas | 8-10 min | [3 temas del invitado] |
| N-1 | Reflexión / Takeaway | 3-4 min | [conclusión accionable] |
| N | Outro + CTA | 1-2 min | [cierre + call to action] |

Presenta esto con los temas reales del episodio en cada fila. **Espera "ok", "adelante", "perfecto" o similar antes de escribir el script.**

---

## Paso 3 — Escribir el script completo

Una vez aprobada la arquitectura, escribe el script palabra por palabra.

### Marcadores de producción

- `[PAUSA]` — silencio intencional de 0.5-1 segundo
- `[ÉNFASIS]` — palabra o frase que recibe énfasis vocal
- `[SFX: descripción]` — efecto de sonido o música sugerida
- `[TRANSICIÓN]` — cambio de segmento con música de fondo
- `[NOTA: texto]` — nota para el host, no se lee en voz alta

### Reglas de escritura

- Aplica las `reglas_tono` del `podcast-profile.json`
- Usa el `intro_template` del perfil como base (puede adaptarlo pero no cambiarlo radicalmente)
- Usa el `outro_template` del perfil
- Escribe como se habla, no como se escribe: contracciones, frases cortas, pausas naturales
- Cada segmento debe tener una frase de transición clara al siguiente

**Si formato_episodio = `interview`:**
- Escribe las preguntas del host en orden de escalada: bienvenida → preguntas fáciles → profundas → cierre
- Usa el cuestionario generado en Paso 1.5 como estructura, pero adapta las palabras exactas para sonar natural
- Incluye notas de escucha activa: `[NOTA: si el invitado menciona X, profundiza con "¿y cómo afectó eso a...?"]`
- No escribas las respuestas del invitado — son improvisadas
- Incluye una pregunta de cierre: "¿qué le dirías a alguien que...?"
- Ejemplo de pregunta con nota:
```
[HOST: Tu nombre] ¿Qué aprendiste durante ese primer año?

[NOTA: Escucha atentamente. Si menciona un fracaso específico, pregunta:
"Cuéntame más de eso — ¿cómo lo superaste?"]
```

**Si formato_episodio = `co-host`:**
- Indica quién habla cada segmento: `[HOST: Nombre]`
- Usa el documento de sincronía (Paso 1.6) como guía para asignar segmentos
- Incluye momentos de intercambio marcados con `[INTERCAMBIO]` donde los hosts pueden comentar entre sí sin guion
- Equilibra el tiempo de habla entre los hosts (±20%)
- Ejemplo de segmento con intercambio:
```
[HOST: Nombre1] [lee segmento del script...]

[INTERCAMBIO]
[HOST: Nombre2] ¿Y tú qué opinás de eso?
[HOST: Nombre1] Yo creo que...

[ambos pueden dialogar naturalmente — el script solo marca dónde ocurre]
```

### Formato de entrega del script

```
══════════════════════════════════════════════════════
  [NOMBRE PODCAST] — [EP.NNN]: [TÍTULO DEL EPISODIO]
  Duración estimada: ~[X] minutos | Formato: [formato]
══════════════════════════════════════════════════════

─── SEGMENTO 1: INTRO + HOOK ─────────────────────────

[SFX: música de apertura, fade out a los 5 segundos]

[Texto del script aquí — primera frase que engancha al oyente antes de que pueda cambiar el episodio]

[PAUSA]

─── SEGMENTO 2: BIENVENIDA ───────────────────────────

[Texto del script — máximo 30 segundos, breve presentación]

─── SEGMENTO 3: CONTEXTO ─────────────────────────────

[Texto del script]

[... continúa para cada segmento aprobado ...]

─── SEGMENTO FINAL: OUTRO + CTA ──────────────────────

[Outro template adaptado]

[SFX: música de cierre, fade in]

══════════════════════════════════════════════════════
  FIN DEL SCRIPT — EP.[NNN]
══════════════════════════════════════════════════════
```

---

## Paso 4 — Checklist de calidad

Antes de entregar, verifica internamente cada punto:

- [ ] El hook de apertura genera intriga o sorpresa en los primeros 30 segundos
- [ ] El tono es consistente con las `reglas_tono` del perfil en todos los segmentos
- [ ] Los `[PAUSA]` están colocados en momentos de impacto emocional o reflexión
- [ ] El CTA del outro es claro, específico y accionable
- [ ] La duración estimada está dentro del rango del perfil (±15%)
- [ ] Intro y outro usan los templates del perfil como base
- [ ] El takeaway está explicitado claramente antes del outro
- [ ] No hay segmentos que superen el doble de la duración estimada de otro
- [ ] [Si interview] Las preguntas escalan de fácil a profundo
- [ ] [Si co-host] Los hosts tienen tiempos de habla equilibrados (±20%)

---

## Paso 5 — Guardar y transición

1. Guarda el script como `episodio-[NNN]-[slug-del-titulo].md` en el directorio actual.
   (slug = título en minúsculas con guiones, sin tildes: "inteligencia-artificial")

2. Muestra resumen:

```
══════════════════════════════════════════
  Script generado ✓
══════════════════════════════════════════
  Episodio:   [EP.NNN] — [Título]
  Formato:    [formato]
  Duración:   ~[X] minutos estimados
  Segmentos:  [número]
  Archivo:    episodio-[NNN]-[slug].md
══════════════════════════════════════════
```

3. Pregunta: "¿Continuamos con el plan de grabación, el artwork, o prefieres exportar el HTML ahora?"
