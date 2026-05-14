# Workflow: Artwork Prompts BTQ

Genera prompts listos para Flow / Nani Banana 2 en los 3 formatos del episodio.

---

## Step 0 — Verificar si existe un prompt de referencia [MANDATORY]

**ANTES de escribir el prompt**, preguntar: "¿Tienes un prompt de episodio anterior como referencia para este tipo de obra (anime / videojuego / película / música)?"

Si Andy comparte uno → usarlo como plantilla base y adaptar al nuevo episodio. **No reescribir desde cero.**
Si no existe → usar la plantilla de esta sección y adaptar.

El prompt del EP.012 (Bohemian Rhapsody / Queen) es la referencia canónica para episodios musicales con personajes reales.

**Por qué**: Prompts probados producen resultados consistentes. Recriar versiones ultra-densas desde cero tiende a generar inconsistencia visual con Nani Banana Pro.

## Herramienta: Google Flow + Nani Banana Pro

**BTQ usa Flow (Google Labs) con Nani Banana Pro para generar artwork.** Nani Banana Pro responde mejor a **prompts híbridos** (nombres de actores reales + especificaciones detalladas) que a descripciones ultra-densas y genéricas.

**Metodología hybrid (EP.013+):**
- Nombres de actores reales cuando aplique (e.g., "Christopher Lloyd, Michael J Fox")
- Especificaciones ultra-detalladas: edad, herencia, tono de piel, COLOR DE CABELLO EN MAYÚSCULAS, color de ojos, ropa, expresión, postura
- Dirección de iluminación específica + temperatura de color + tratamiento de sombras
- Referencia estética explícita (35mm Kodak Portra, Spielberg-era, etc.)
- **Siempre incluir: "NOT a 3D render", "Cinematic film aesthetic", "Hyperrealistic skin texture"**

**Anti-pattern**: Ultra-densidad sin estructura (descripción genérica pura) genera inconsistencia visual y renders aparentes. Evitar.

**Flujo de trabajo:**
1. Verificar Step 0 (referencia previa) — es mandatory
2. Copia los 3 prompts (1:1, 9:16, 16:9) a Flow
3. Genera 2 iteraciones por formato (2 versiones cada uno)
4. Revisa y elige 1 por formato (el que más se acerca)
5. Ajusta iterativamente basado en resultados de Nani Banana Pro

---

## Estrategia de Reusabilidad

**Core principle**: Un prompt que funcionó bien en un episodio anterior es más confiable que una versión nueva ultra-densa.

- Si existe un prompt similar de un episodio anterior (mismo tipo de personajes, misma época, mismo estilo visual) → **reusar la estructura y adaptar detalles específicos al nuevo episodio.**
- Esto aplica especialmente a:
  - Películas clásicas (Back to the Future, Ghostbusters, The Matrix)
  - Anime con personajes reales (Frieren, Dragon Ball, Maomao)
  - Épocas cinematográficas similares
- **No recriar prompts desde cero si existe una estructura probada.** Nani Banana Pro es más confiable con patrones conocidos.

Ejemplo: EP.013 reutilizó estructura de EP.012 (personajes reales + actor names) con adaptaciones específicas para Doc/Marty.

---

## Reglas core — no negociables

- **Siempre en color** — cinematic, photographic, warm. Nunca monocromo ni desaturado
- **4K cinematic** — hyperrealistic skin texture, film grain, 35mm Kodak aesthetic
- **Nunca PCB/circuits** a menos que el episodio sea explícitamente sobre AI o tech
- **Nunca texto en esquina inferior derecha** — watermark de Veo/Kling vive ahí
- **EP number SIEMPRE al centro del footer** — nunca esquina derecha
- **Headset cuando aparezca:** boom microphone arm desde el ear cup hacia la cara — contact center style, NUNCA music headphones
- **Nani Banana 2 siempre genera 4 candidatos** — pedir a Andy que elija uno antes de guardar

---

## Tipografía master (todos los formatos)

```
Top center: five small gold #C9A84C dots
"BEHIND THE QUEUE" — ultra bold white condensed sans-serif, large
"BTQ" — small gold #C9A84C below
Below figures: [episode subtitle] — white text, bold
```

## Footer master (todos los formatos)

```
Footer black bar at bottom:
- Left: "Behind the Queue" white
- CENTER: "EP.0XX" gold #C9A84C — prominent, with character silhouettes
- Right two rows:
  Row 1: Facebook icon, Instagram icon, TikTok icon
  Row 2: Spotify icon, Apple Podcast icon, Amazon Music icon
Do NOT place any text in the bottom-right corner.
```

---

## Plantilla de prompt base

```
Color cinematic photographic image 4k. [Descripción de estilo y época específica
a la referencia cultural del episodio], rendered in full warm cinematic color.
[Composición — formación, fondo negro vacío, dirección del spotlight].

[BLOQUE DE PERSONAJE — uno por personaje]:
[NOMBRE]: [Edad, herencia, tono de piel, COLOR DE CABELLO EN MAYÚSCULAS SI ES CRÍTICO,
color de ojos, ropa, expresión, qué lo hace visualmente icónico]

LIGHTING & COLOR PALETTE: [Dirección y temperatura del spotlight], [tratamiento de sombras],
[estética de película — 35mm Kodak], [referencia de color grading si aplica].
Cinematic film grain, hyperrealistic skin texture with visible pores.

Typography:
- Top center: five small gold #C9A84C dots
- "BEHIND THE QUEUE" — ultra bold white condensed sans-serif, large
- "BTQ" — small gold below
- Below figures: "[Título/subtítulo del episodio]" — white text, bold

Footer black bar at bottom:
- Left: "Behind the Queue" white
- CENTER: "EP.0XX" gold #C9A84C — prominent with [siluetas de personajes]
- Right two rows:
  Row 1: Facebook icon, Instagram icon, TikTok icon
  Row 2: Spotify icon, Apple Podcast icon, Amazon Music icon

No circuit boards. No cartoon style. Cinematic.
Do NOT place any text in the bottom-right corner.
Format: [tamaño].
```

---

## Formatos por episodio

| Formato | Tamaño | Uso | Composición |
|---|---|---|---|
| 1:1 | 3000×3000px | Portada de plataforma, feed posts | PRIMARY — composición central |
| 9:16 | 1080×1920px | TikTok, Stories | Stack vertical de personajes |
| 16:9 | 1920×1080px | LinkedIn | Spread horizontal |

Generar los 3 formatos por episodio. Adaptar composición de personajes por formato.

---

## Guía de adaptación por tipo de episodio

**Anime** (Frieren, Dragon Ball, Saint Seiya, Maomao):
- Interpretación live-action cinematic de personajes animados
- Especificar colores de cabello y ojos EN MAYÚSCULAS — rasgos anime son no-realistas, ser explícito
- Referenciar el estudio de animación (Madhouse, Toei, etc.)

**Videojuego** (God of War, Metal Gear, The Last of Us):
- Referenciar la estética de cutscenes cinematográficas del juego
- Rendering hiperrealista — estos personajes ya tienen diseños high-fidelity
- Incluir props/armas icónicos como anclas visuales

**Película** (Back to the Future, Ghostbusters, The Matrix):
- Referenciar el color grading y la época específica del film
- Castear como los actores del film cuando son ampliamente reconocibles
- Incluir props o vestuario icónicos

**Música** (Queen, Pink Floyd):
- Referenciar la estética del video/álbum icónico y la fotografía de la era
- Incluir siluetas de instrumentos en el footer

---

## Notas críticas de personajes — actualizar con cada episodio

| Episodio | Personaje | Specs críticos |
|---|---|---|
| EP.011 | Himmel (Frieren) | BLUE HAIR — NOT blonde. ALL CAPS en el prompt. |
| EP.011 | Eisen (Frieren) | SHORT AND STOCKY — shorter than Frieren. NOT tall. |
| EP.012 | Freddie Mercury | Thick dark mustache (later era), jet-black short hair, warm olive skin |
| EP.012 | Brian May | MASSIVE halo of long CURLY DARK BROWN hair — his signature feature |
| EP.012 | Roger Taylor | Long wavy BLONDE shoulder-length hair, sharp angular features, blue eyes |
| EP.012 | John Deacon | Short DARK BROWN hair, clean-cut, youngest, most reserved |

Agregar a esta tabla cada vez que Andy corrija un detalle de personaje después de la generación.
