# Workflow 03 — Artwork del Episodio

Genera prompts listos para copiar en Google Flow (con Nani Banana Pro o Imagen 3) en 3 formatos. Los prompts van en inglés — los modelos de imagen responden mejor en inglés.

**Regla fundamental: Siempre preguntar si existe un prompt exitoso de un episodio anterior antes de crear uno nuevo. Reusar estructura probada > crear desde cero.**

---

## Paso 0 — Cargar datos

1. Lee `podcast-profile.json`: nombre del podcast, colores de marca, estilo visual, tono.
2. Busca archivos `artwork-ep*.md` en el directorio. Si existen, lista los disponibles: "Tengo prompts guardados del EP.001 y EP.002 — ¿quieres usar alguno como base?"
3. Lee el script del episodio si existe para extraer el tema y referencia cultural.

---

## Paso 1 — Datos del episodio visual

Pregunta en un solo mensaje:

> **Para el artwork del episodio:**
> 1. ¿Cuál es el número de episodio?
> 2. ¿El episodio tiene una referencia visual clara? (película, serie, personaje, persona real, lugar icónico, concepto abstracto...) Si no, ¿qué emoción o imagen debería transmitir la portada?
> 3. ¿Qué vibe quieres para este episodio? (dramático, energético, melancólico, cálido, oscuro, esperanzador, minimal...)
> 4. ¿Hay algún episodio anterior cuyo artwork funcionó bien y quieres usar como estructura base?

Si el usuario tiene un episodio anterior como referencia → lee ese archivo y úsalo como estructura, cambiando solo el contenido específico de este episodio (colores, sujeto, texto, expresión).

---

## Paso 2 — Construir los prompts

Para cada formato, genera un prompt completo en inglés siguiendo esta arquitectura:

### Línea de apertura — estilo y calidad
```
Cinematic photographic image. Hyperrealistic, captured on 35mm Kodak Portra film. [Warm/Cool/Moody] color grading. [Año o era si es relevante para el tema].
```

### Sujeto principal — descripción detallada
- **Si hay persona real:** nombre completo + edad aproximada + rasgos físicos en detalle. Usar CAPS para colores críticos: "DARK BROWN hair", "BLUE eyes", "wearing a RED jacket"
- **Si hay personaje de ficción:** nombre + descripción física detallada como si fuera una persona real fotografiada
- **Si es abstracto/conceptual:** descripción visual del concepto, metáfora visual, ambiente

### Iluminación y fotografía
```
Key light from [dirección: left/right/above]. [Warm golden/Cool blue/Neutral] tone. Soft shadows. [Referencia cinematográfica si aplica]. NOT a 3D render. NOT an illustration. Photorealistic only.
```

### Tipografía en imagen
```
Text overlay: '[TÍTULO DEL EPISODIO]' in [bold/ultra-bold] [color] [sans-serif/serif] at [top/center/bottom]. '[EP.NNN]' small [color] at [posición]. '[NOMBRE PODCAST]' ultra-bold [color] at bottom center.
```

### Footer de marca
```
Bottom bar: solid [color principal de marca] background. Brand colors throughout: [hex primario], [hex secundario], [hex acento].
```

### Restricción final
```
No extra text outside specified overlays. Highly detailed, award-winning photography style.
```

---

## Paso 3 — Generar los 3 formatos

| Formato | Dimensiones | Uso principal |
|---|---|---|
| **1:1** | 3000×3000px | Spotify, redes cuadradas, portada del episodio |
| **9:16** | 1080×1920px | Instagram Stories, TikTok, Reels |
| **16:9** | 1920×1080px | YouTube thumbnail, portada web, LinkedIn |

Genera el prompt completo para cada formato. El contenido es el mismo — lo que cambia es:
- La composición del sujeto (centrado para 1:1, posicionado arriba para 9:16, en un lado para 16:9)
- El espacio tipográfico (diferente en cada formato)
- La instrucción de dimensiones al inicio

---

## Paso 4 — Plataformas de IA y opciones

Antes de usar, presenta al usuario las opciones disponibles para generar imágenes:

| Plataforma | Costo | Nivel | Mejor para |
|---|---|---|---|
| **Google Flow / Imagen 3** | Gratis (con cuenta Google) | Excelente | Retratos fotorrealistas, portadas limpias |
| **Kling AI** | Freemium / desde ~$10/mes | Muy bueno | Composiciones cinematográficas |
| **Midjourney** | ~$10/mes básico | El más artístico | Estilos consistentes y únicos |
| **Adobe Firefly** | Incluido en Creative Cloud / ~$5/mes | Muy bueno | Integración con Photoshop, seguridad comercial |
| **DALL-E 3** | Incluido en ChatGPT Plus $20/mes | Excelente | Sigue instrucciones detalladas de texto |
| **Stable Diffusion** | Gratis (instalación local) | Máximo control | Control total, curva de aprendizaje alta |

> **⚠️ Advertencia de costos:** Los planes gratuitos tienen límite de créditos/tokens. Una sesión con varias iteraciones puede consumir rápido los créditos. Para uso constante (1 episodio/semana), evalúa si la suscripción mensual se justifica.

**Recomendación:** Empieza con **Google Flow** (gratis) que produce excelentes resultados. Los prompts generados son optimizados para este modelo.

---

## Paso 5 — Instrucciones de uso

Después de los 3 prompts, añade:

```
──────────────────────────────────────────────────────
  ESPECIFICACIONES TÉCNICAS PARA SPOTIFY
──────────────────────────────────────────────────────
  Formato 1:1 (portada principal del episodio):
  ├─ Dimensiones: 3000 × 3000 px (cuadrado exacto)
  ├─ Formato: JPEG o PNG
  ├─ Tamaño de archivo: máximo 500 KB
  ├─ Color: RGB (NO CMYK)
  ├─ Resolución: 72 DPI mínimo
  
  ⚠️ IMPORTANTE:
  Los modelos de IA generan PNG de alta resolución (100+ MB).
  ANTES de subir a Spotify:
  1. Convierte a JPEG
  2. Comprime con squoosh.app (es gratis)
  3. Verifica que quede bajo 500 KB
──────────────────────────────────────────────────────
  CÓMO USAR ESTOS PROMPTS EN GOOGLE FLOW
──────────────────────────────────────────────────────
  1. Abre: labs.google/fx/tools/image-fx
  2. Selecciona "Imagen 3" o "Nani Banana Pro" (si disponible)
  3. Pega el prompt del formato que necesitas
  4. Genera — el modelo produce 2-4 variantes
  5. Selecciona la mejor por formato
  6. Descarga cada variante
  
  Para la portada principal (formato 1:1):
  7. Convierte PNG a JPEG en squoosh.app
  8. Comprime hasta que esté bajo 500 KB
  9. Sube a Spotify
  
  Si el resultado no cumple:
  → Ajusta la descripción del sujeto (más detalles físicos)
  → Añade al final: "highly detailed, sharp focus, professional photography"
  → Cambia la referencia de iluminación
  → Si hay texto mal renderizado: especifica la fuente más (ej. "Helvetica Bold")
──────────────────────────────────────────────────────
```

---

## Paso 7 — Guardar prompts

1. Guarda los 3 prompts como `artwork-ep[NNN].md` en el directorio actual.
2. Muestra resumen:

```
══════════════════════════════════════════
  Prompts de artwork generados ✓
══════════════════════════════════════════
  Episodio:  EP.[NNN]
  Formatos:  1:1 · 9:16 · 16:9
  Archivo:   artwork-ep[NNN].md
══════════════════════════════════════════
  → Guarda este archivo: el próximo episodio
    te preguntará si quieres reusar la estructura.
══════════════════════════════════════════
```

3. Pregunta: "¿Continuamos con el plan de social media?"
