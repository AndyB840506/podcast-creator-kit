# Workflow: Registro de Episodio BTQ

Genera los datos listos para pegar en Safe Creative y Spotify for Podcasters.

Cuando Andy diga "dame los datos para registrar el episodio" → entregar ambos bloques juntos.

---

## 18A — Safe Creative

**Campos fijos (nunca cambian):**
- Categoría: **Audio**
- Tipo de obra: **Podcast**
- Declaración AI: **"I do not want to declare about AI tools in the creative process"**
- Checkbox "real events": **marcado**

**Campos variables — generar por episodio:**

**Título:**
```
Behind the Queue · EP.0XX · [Título del episodio]
```

**Descripción (2–3 oraciones, formal, tercera persona):**
```
Episodio [NÚMERO] del podcast Behind the Queue, conducido por Andrés Ricardo Bermúdez Rodríguez.
En este episodio se analiza [tema central] a través de [referencia cultural], explorando principios
de [experiencia / liderazgo / cultura operativa]. Producción original en español para audiencias
de operaciones, servicio al cliente y liderazgo en Latinoamérica.
```

**Tags (comma-separated) — base + específicos del episodio:**

Base (siempre incluir):
```
Behind the Queue, podcast, español, liderazgo, experiencia, cultura, BPO, contact center,
servicio al cliente, operaciones, LATAM, Colombia, Andrés Bermúdez
```

Agregar según tipo de episodio:
- Videojuego: `videojuegos, gaming, [título del juego], [nombre del personaje]`
- Música: `música, [banda/artista], [canción/álbum]`
- Anime: `anime, manga, [título de la serie], [nombre del personaje]`
- Película: `cine, películas, [título], [nombre del personaje]`
- Temáticos: `liderazgo, coaching, engagement, rotación de talento, desarrollo de equipos` (seleccionar relevantes)

---

## 18B — Spotify for Podcasters

**Título (máx. 200 chars — apuntar a ≤80 para mobile):**
```
EP.0XX: [Título del episodio] | Behind the Queue
```

**Descripción (máx. 4000 chars — activar HTML toggle):**

```html
<p>[Párrafo de apertura — la pregunta o situación que engancha, sin revelar todo el episodio]</p>

<p>En este episodio de Behind the Queue exploramos <strong>[referencia cultural]</strong> como
punto de entrada para hablar de <strong>[tema central]</strong>: [1 oración describiendo la
idea core].</p>

<p><strong>En este episodio:</strong></p>
<ul>
<li>[Punto clave 1]</li>
<li>[Punto clave 2]</li>
<li>[Punto clave 3]</li>
<li>Mito o Realidad: [teaser del segmento]</li>
<li>[Insight o acción de cierre]</li>
</ul>

<p><strong>Fuentes mencionadas:</strong> [Fuente 1], [Fuente 2], [Fuente 3]</p>

<p>─────────────────────────────</p>
<p>🎙 <strong>Behind the Queue</strong> — Experiencia · Cultura · Liderazgo</p>
<p>Conducido por <strong>Andrés Bermúdez Rodríguez</strong></p>
<p>📧 andy@behind-thequeue.com</p>
<p>🌐 behind-thequeue.com</p>
<p>📱 @behindthequeue84 (Instagram) · @behind.the.queue (TikTok)</p>
<p>💼 linkedin.com/company/behind-the-queue</p>
```

**Arte del episodio:** Usar portada 1:1 generada en `workflows/artwork.md`.

**"In this episode" shortcuts:** Solo agregar si la referencia cultural tiene presencia en Spotify (episodios de música: link al álbum/canción). Para anime, videojuegos y películas — dejar vacío.

---

## Formato de entrega (ambas plataformas juntas)

```
═══════════════════════════════════════
REGISTRO EP.0XX — [TÍTULO]
═══════════════════════════════════════

── SAFE CREATIVE ──────────────────────
Categoría: Audio
Tipo de obra: Podcast
Título: Behind the Queue · EP.0XX · [título]

Descripción:
[descripción generada]

Tags:
[string completo de tags, comma-separated]

AI declaration: I do not want to declare about AI tools in the creative process
Checkbox "real events": marcado

── SPOTIFY FOR PODCASTERS ─────────────
Título (campo Title):
[título — máx. 80 chars]

Descripción (activar HTML toggle):
[HTML completo]

Arte: portada 1:1 del episodio
In this episode: [link si es episodio musical, vacío si no]

═══════════════════════════════════════
```
