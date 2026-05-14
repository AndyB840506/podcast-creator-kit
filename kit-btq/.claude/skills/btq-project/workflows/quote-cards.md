# Workflow: Quote Cards BTQ

Extrae los quotes más potentes del guión y genera prompts de imagen para cada uno.

---

## Step 1 — Extraer quotes del guión (orden de prioridad)

1. **Líneas [REMATE]** — siempre las más fuertes; standalone por diseño
2. **Línea de apertura del hook** — la que engancha antes de revelar el personaje
3. **Cierre de Mito o Realidad** — "La realidad es..."
4. **Mensaje directo del cierre** — el dirigido a agentes/supervisores
5. **Frase TM del personaje** — siempre incluir como última card

Evitar: datos estadísticos aislados, transiciones, líneas de setup. Cada quote debe funcionar solo sin contexto.

**Target:** 4–6 quotes por episodio.

---

## Step 2 — Specs de la quote card

- Un quote por card — nunca combinar
- Texto grande, bold, alto contraste: off-white `#F5F2EC` sobre fondo oscuro
- Atribución abajo: **"— Andy · Behind the Queue · EP.0XX"** en gold `#C9A84C`
- Sin footer de íconos — usar "Behind the Queue · EP.0X" en gold bottom-center
- Fondo: derivado del artwork del episodio, oscurecido/blureado para legibilidad
- Formato: cuadrado 1:1, mínimo 1080×1080px

---

## Step 3 — Plantilla de prompt

```
Color cinematic quote card, 1:1 format. Background derived from [descripción del
artwork del episodio] — darkened and blurred for text readability, same cinematic
color palette, film grain, hyperrealistic depth.

Typography at top:
- Top center: five small gold #C9A84C dots
- "BEHIND THE QUEUE" — ultra bold white condensed sans-serif, very large, all caps
- "BTQ" — small gold #C9A84C below
- Below: "[Título/subtítulo del episodio]" — white text, bold, centered

QUOTE (centered, very large, bold, off-white #F5F2EC):
"[Texto del quote]"

Footer black bar at bottom (background #0A0A0A):
- Left: "Behind the Queue" white text
- CENTER: "EP.0XX" gold #C9A84C — prominent and large, with black silhouettes of 
  main characters or iconic visual from episode
- Right two rows of icons (white):
  Row 1: Facebook icon, Instagram icon, TikTok icon
  Row 2: Spotify icon, Apple Podcasts icon, Amazon Music icon

No cartoon style. No circuit boards. Cinematic film aesthetic.
Format: square 1080×1080px.
```

---

## Step 4 — Entrega

Presentar quotes seleccionados en tabla antes de generar prompts:

| # | Quote | Segmento origen | Por qué funciona |
|---|---|---|---|
| 1 | "[quote]" | [REMATE] Seg. X | Standalone, punchy, shareable |
| 2 | "[quote]" | Hook | Curiosidad sin spoiler |
| 3 | "[quote]" | TM personaje | Cierre memorable |

Pedir confirmación de Andy antes de generar los prompts de imagen.

El quote del Día 2 del plan de redes (el más shareable) se elige de esta lista.
