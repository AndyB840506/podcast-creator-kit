# Workflow 00 — Setup de Identidad del Podcast

Crea o actualiza `podcast-profile.json` con la identidad completa del podcast. Este archivo alimenta todos los demás workflows.

---

## Paso 0 — Verificar si ya existe perfil

Busca `podcast-profile.json` en el directorio actual.

- Si **existe**: muéstrale al usuario los datos actuales en formato legible y pregunta qué sección quiere actualizar. Salta al bloque correspondiente.
- Si **no existe**: di "Vamos a configurar tu podcast desde cero. Te haré algunas preguntas en bloques cortos — no debería tomarte más de 15 minutos." y continúa al Paso 1.

---

## Paso 1 — Concepto del podcast (Bloque 1 de 4)

Pregunta en un solo mensaje:

> **Sobre el concepto:**
> 1. ¿Cómo se llama tu podcast? (si no tienes nombre aún, descríbelo y te propongo opciones)
> 2. En 2 líneas: ¿de qué trata? ¿qué hace único a este podcast?
> 3. ¿Cuál es la categoría o nicho? (tecnología, emprendimiento, entretenimiento, true crime, salud, etc.)
> 4. ¿A quién va dirigido? Describe a tu oyente ideal: edad aproximada, qué le interesa, qué problema tiene.

Escucha las respuestas. Si el nombre no está definido, genera 3 opciones con justificación **SEO-friendly** (para cada opción, explica):
- Opción 1 (directa/descriptiva): Por qué incluye keywords que busca tu audiencia
- Opción 2 (evocadora/poética): Cómo es memorable sin sacrificar descubribilidad
- Opción 3 (corta/memorable): Cómo es corta (1-3 palabras), fácil de pronunciar, diferenciada

Justificación de cada opción debe incluir: palabra clave principal que incluye, por qué es memorable, por qué es SEO-friendly. Espera que el usuario elija antes de continuar.

---

## Paso 2 — Formato y tono (Bloque 2 de 4)

Pregunta en un solo mensaje:

> **Sobre el formato:**
> 1. ¿Cómo es el formato? (solo — solo yo hablo / entrevista — tengo invitados / co-host — somos dos o más presentadores)
> 2. ¿Cuánto duran los episodios aproximadamente? (15 min, 30 min, 45 min, 1 hora...)
> 3. ¿Con qué frecuencia vas a publicar? (semanal, quincenal, mensual)
> 4. ¿Cómo describes el tono? (formal, casual, académico, entretenido, provocador, inspiracional... puede ser una mezcla)
> 5. ¿Hay algún podcast que te inspire o con el que te identifiques?
> 6. ¿Cómo vas a usar el script? (lo leo palabra por palabra / lo uso como guía e improviso / mezcla de ambos)

Guarda el formato detectado como `solo`, `interview` o `co-host`. Si hay co-host, pregunta los nombres de todos los presentadores.

**Después de recoger duración y estilo de narración**, muestra esta tabla para que el usuario entienda el tamaño esperado del script:

```
WORD COUNT TARGET POR EPISODIO
══════════════════════════════════════════════════════════════
| Duración | Solo (leo) | Solo (improviso) | Interview | Co-host |
|----------|------------|------------------|-----------|---------|
| 15 min   | ~2,300     | ~1,500           | ~900      | ~1,150  |
| 30 min   | ~4,500     | ~3,000           | ~1,800    | ~2,250  |
| 45 min   | ~6,800     | ~4,500           | ~2,700    | ~3,400  |
| 60 min   | ~9,000     | ~6,000           | ~3,600    | ~4,500  |
══════════════════════════════════════════════════════════════

Basado en tu respuesta: Duración [X] min + Estilo [estilo]
→ Word count target: ~[N] palabras por episodio
```

Di: "Esto es el tamaño esperado del script. Cada episodio que generes tendrá este como referencia para no quedar corto o demasiado largo."

Guarda en el JSON: `estilo_narración: "leo" | "improviso" | "mixto"` y `word_count_target: N`

---

## Paso 3 — Marca visual (Bloque 3 de 4)

Pregunta en un solo mensaje:

> **Sobre la identidad visual:**
> 1. ¿Ya tienes colores definidos? (puedes dar nombres o hex codes como #FF5500)
> 2. ¿Tienes logo o imagen de marca? Descríbelo brevemente o di "todavía no".
> 3. ¿Qué estilo visual te imaginas? (minimalista, vibrante, oscuro/moody, cálido, profesional, underground, ilustrado...)

Si no tiene colores, propón una paleta de 3 colores basada en el tono y categoría del podcast. Explica brevemente el razonamiento detrás de cada color elegido.

---

## Paso 4 — Distribución (Bloque 4 de 4)

Pregunta en un solo mensaje:

> **Sobre la distribución:**
> 1. ¿En qué plataformas vas a publicar? (Spotify, Apple Podcasts, YouTube, iVoox, otros)
> 2. ¿Ya tienes links o aún no has creado las cuentas?
> 3. ¿Cómo quieres numerar los episodios? (EP.001, Episodio 1, S01E01, o sin número)
> 4. ¿Tienes hashtags que ya uses o quieras usar en redes?

---

## Paso 5 — Generar entregables

Con todos los datos recopilados, genera:

### A. `podcast-profile.json`
Completa el JSON con todos los campos recopilados. Si algún campo no fue dado, usa `""` (no inventes valores).

### B. Tagline (si no tenía uno)
3 opciones de tagline:
- Corto (5-7 palabras, directo)
- Descriptivo (10-12 palabras, explica el valor)
- Provocador (una pregunta o frase de impacto)

Espera que el usuario elija uno antes de guardarlo.

### C. Intro template
2 versiones de apertura del episodio (15-20 segundos leídos):
- **Versión A:** más formal/descriptiva — presenta el podcast y el host antes del tema
- **Versión B:** más casual/directa — entra al tema inmediatamente, presenta después

Usa el nombre del podcast, el nombre del host, y el tono detectado.

### D. Outro template
2 versiones de cierre (15-20 segundos leídos) con CTA a suscribirse/seguir en plataformas.

### E. Guía de tono (5 reglas)
Reglas de escritura específicas para ESTE podcast. No genéricas. Ejemplos del tipo de regla:
- "Habla siempre en segunda persona directa (tú, no usted)"
- "Usa analogías con [referencia relevante a la audiencia detectada]"
- "Evita tecnicismos sin explicarlos primero"
- "Cada episodio debe tener un dato o estadística sorprendente"

### F. Prompt de artwork general del podcast (portada)
Un prompt en inglés para generar la portada general del podcast (no de un episodio específico). Formato 1:1 @ 3000x3000px. Incluir: nombre del podcast en tipografía, paleta de colores de marca, estilo visual elegido, nombre del host si aplica.

### G. Música para el podcast
Presenta recomendaciones de fuentes de música sin derechos de autor:

> **Fuentes de música sin derechos de autor:**
> - **Epidemic Sound** (suscripción ~$15/mes) — la más profesional, catálogo enorme, soporte para podcasters
> - **Artlist** (suscripción ~$200/año) — ilimitado, muy usado en YouTube y podcasts, herramientas de edición incluidas
> - **Free Music Archive** (fma.bandcamp.com) — completamente gratis, Creative Commons
> - **YouTube Audio Library** (studio.youtube.com/channel/music) — gratis, alta calidad, sin crédito requerido
> - **Incompetech** (incompetech.com) — gratis, solo requiere crédito en show notes
> - **Pixabay Music** (pixabay.com/music) — gratis, sin crédito requerido
> - **ccMixter** (ccmixter.org) — Creative Commons, comunidad de músicos
>
> **Para un podcast nuevo:** Empieza con YouTube Audio Library o Pixabay (gratis, suficiente calidad). Upgrade a Epidemic Sound cuando el podcast crezca.
>
> **Necesitas generar/descargar:**
> - **Intro** (5-15 seg) — tema que se repite al inicio de cada episodio
> - **Outro** (10-30 seg) — tema que se repite al cierre de cada episodio
> - **Stinger** (2-3 seg) — sonido corto para transiciones dentro del episodio
> - **Música de fondo** (loop suave) — opcional, para segmentos específicos si aplica

### H. Protección de obra — Safe Creative
Antes de publicar, considera registrar tu podcast en **Safe Creative** (safecreative.org):

> - **Registro gratuito** para obra básica
> - **Genera certificado** de autoría con fecha verificada
> - **Protege tu contenido** ante plagios o disputas
> - **Especialmente importante** si compartes ideas originales, investigación o formatos únicos
>
> **Qué registrar:** el nombre del podcast, el guion del primer episodio, y la portada una vez que la tengas. Es rápido (5 min) y gratuito.

### I. Plataformas de IA para imágenes
Para generar la portada del podcast y las imágenes de cada episodio, aquí están las opciones principales:

| Plataforma | Costo | Nivel | Mejor para |
|---|---|---|---|
| **Google Flow / Imagen 3** | Gratis (con cuenta Google) | Excelente | Retratos fotorrealistas, portadas limpias |
| **Kling AI** (klingai.com) | Freemium / desde ~$10/mes | Muy bueno | Composiciones cinematográficas |
| **Midjourney** | ~$10/mes básico | El más artístico | Estilos consistentes y únicos |
| **Adobe Firefly** | Incluido en Creative Cloud / ~$5/mes | Muy bueno | Integración con Photoshop, seguridad comercial |
| **DALL-E 3** (ChatGPT) | Incluido en ChatGPT Plus $20/mes | Excelente | Sigue instrucciones detalladas de texto |
| **Stable Diffusion** | Gratis (instalación local) | Máximo control | Control total, curva de aprendizaje alta |

> **⚠️ Advertencia de costos:** Los planes gratuitos tienen límite de créditos/tokens. Una sesión de generación con varias iteraciones puede consumir rápido los créditos. Para uso constante (un episodio por semana), evalúa si la suscripción mensual se justifica.
>
> **Recomendación para empezar:** Google Flow es gratis, funciona excelente, y produce resultados profesionales. El kit genera los prompts optimizados para este modelo.

---

## Paso 6 — Guardar y presentar

1. Escribe `podcast-profile.json` en el directorio actual.
2. Muestra un resumen visual:

```
══════════════════════════════════════════════
  [NOMBRE DEL PODCAST] — Perfil creado ✓
══════════════════════════════════════════════
  Formato:     [solo/interview/co-host]
  Audiencia:   [descripción en 1 línea]
  Tono:        [tono]
  Colores:     [colores]
  Plataformas: [lista]
  Numeración:  [convención elegida]
══════════════════════════════════════════════
  Archivos generados:
  ✓ podcast-profile.json
══════════════════════════════════════════════
```

3. Pregunta: "¿Quieres ajustar algo, o empezamos con el primer episodio?"
4. Si dice "primer episodio" o similar → lanza `workflows/01-episodio.md`.

---

## Paso 7 — Roadmap piloto → 9 episodios

**⚠️ NOTA IMPORTANTE SOBRE EL PRIMER EPISODIO:**

Antes de empezar a grabar, el usuario debe entender una realidad clave: **el primer episodio siempre toma entre 3 y 6 veces más tiempo del estimado**. No porque esté haciendo algo mal, sino porque está aprendiendo las herramientas de grabación, edición y distribución simultáneamente. Es completamente normal.

Muestra este tabla de expectativas:

```
TIEMPO POR EPISODIO (experiencia real)
═══════════════════════════════════════════
 Nivel              Script   Grabación   Edición    Arte+Social  TOTAL
 Primeros 3 ep.     3-5h     2-3x dur.   3-4x dur.  2-3h         8-15h
 Episodios 4-10     2-3h     1.5x dur.   2x dur.    1-2h         5-8h
 Episodio 20+       1-2h     1x dur.     1-1.5x dur. 1h          3-5h
═══════════════════════════════════════════
(para un episodio de 30 minutos: 8-15 horas primeros 3, 
luego 5-8 horas, y finalmente 3-5 horas)
```

**Estrategia recomendada:**

1. **Produce el Episodio 1 como piloto** — grábalo, edítalo, súbelo. Aprende el proceso completo. **No importa si toma 10 horas; es tu inversión inicial de aprendizaje.**

2. **Después del piloto, diseña el roadmap** — cuando ya sabes cuánto tiempo te toma, planifica con realismo.

3. **Mantén una cadencia semanal sostenible** — consistencia > cantidad. Un episodio por semana publicado puntualmente vale mucho más que tres episodios publicados de golpe y luego silencio de tres meses.

Pregunta al usuario:

> ¿Cuánto tiempo por semana puedes dedicar al podcast?
> - 3-5 horas: 1 episodio cada 2-3 semanas
> - 5-10 horas: 1 episodio por semana (cadencia recomendada)
> - 10+ horas: 2 episodios por semana o más

Basado en su respuesta, generar una tabla de roadmap de 9 episodios (después del piloto):

```
ROADMAP — EPISODIOS 2-10
═════════════════════════════════════════════════════════════
 EP  │ Título tentativo           │ Pilar         │ Tipo    
─────┼────────────────────────────┼───────────────┼─────────
 001 │ [Piloto — tema a definir]  │ [pilar]       │ [tipo]
 002 │                            │               │ 
 003 │                            │               │ 
 004 │                            │               │ 
 005 │                            │               │ 
 006 │                            │               │ 
 007 │                            │               │ 
 008 │                            │               │ 
 009 │                            │               │ 
 010 │                            │               │ 
═════════════════════════════════════════════════════════════
```

Instrucciones para llenar el roadmap:

1. **Primero, define 3-4 "pilares de contenido"** — temas o formatos que el podcast rotará:
   - Ejemplo para podcast de negocios: "Historia founder + Táctica práctica + Entrevista + Tendencia del mercado"
   - Ejemplo para podcast de entretenimiento: "Top 5 + Entrevista + Análisis profundo + Cultural moment"

2. **Luego, asigna un pilar a cada episodio** — eso garantiza variedad y mantiene la estructura consistente.

3. **Tipo:** cada episodio puede ser solo/interview/co-host, incluso si el formato default es diferente. Varía si es posible.

Guarda los pilares y el roadmap en el JSON como:
```json
"content_pillars": ["Pilar 1", "Pilar 2", "Pilar 3", "Pilar 4"],
"roadmap_9_episodes": [
  { "ep": 2, "title": "...", "pillar": "...", "type": "solo" },
  ...
]
```

---

> **Nota sobre extras:** Si durante el setup surgen componentes adicionales útiles que el usuario no pidió explícitamente (ej. prompt de portada general, variantes del tagline), preséntalos como opcionales y espera confirmación antes de incluirlos.
