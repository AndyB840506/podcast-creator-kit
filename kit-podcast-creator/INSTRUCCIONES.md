# Instrucciones — Kit de Producción de Podcast

## Qué hace este kit

Guía todo el ciclo de producción de un podcast: desde la identidad de marca hasta el episodio publicado y distribuido.

| Workflow | Qué genera |
|---|---|
| **Setup** | Perfil del podcast, tagline, intro/outro, guía de tono, prompt de portada |
| **Guion** | Script completo palabra por palabra, adaptado a tu formato |
| **Grabación** | Checklists de pre/durante/post grabación + guía de edición |
| **Artwork** | Prompts para generar imágenes en Google Flow (3 formatos) |
| **Social Media** | Copy para 3 días de lanzamiento en 5 plataformas |
| **Show Notes** | Descripción SEO para Spotify, Apple Podcasts y web |
| **Exportar HTML** | Paquete de producción interno + página pública del episodio |

Soporta todos los formatos: **solo**, **entrevista con invitados**, y **co-host**.

---

## Requisitos

- **[Claude Code](https://claude.ai/code)** instalado

Nada más. El kit no necesita instalaciones adicionales.

---

## Cómo empezar

### 1. Abre esta carpeta en Claude Code

**Opción A — Desde terminal:**
```bash
cd ruta/a/kit-podcast-creator
claude
```

**Opción B — Desde Claude Code:**
Archivo → Abrir carpeta → selecciona `kit-podcast-creator`

**Opción C — Desde VS Code:**
Abre la carpeta en VS Code y usa la extensión de Claude Code

### 2. Escribe cualquier cosa

El kit se activa automáticamente con tu primer mensaje.

### 3. Primera vez (~15 min)

El kit te guiará en 4 bloques de preguntas para configurar la identidad de tu podcast. Al terminar, genera `podcast-profile.json` con todo tu perfil.

### 4. Próximas sesiones

El kit reconoce tu podcast y te muestra el menú de workflows. Dile qué quieres hacer:

```
"guion para el episodio 3 sobre productividad"
"plan de grabación para el lunes"
"social media para el episodio de esta semana"
"exporta todo en HTML"
```

---

## Flujo de producción recomendado

```
Setup (una vez)
    ↓
Guion del episodio
    ↓
Plan de grabación
    ↓
[grabas el episodio]
    ↓
Artwork del episodio
    ↓
Social Media (3 días)
    ↓
Show Notes
    ↓
Exportar HTML
    ↓
[publicas]
```

Puedes saltar a cualquier paso — el kit siempre lee los archivos generados anteriormente para no repetirte preguntas.

---

## Antes de publicar — Setup de plataformas

Antes de lanzar tu primer episodio, necesitas elegir dónde alojarlo. Aquí está el flujo paso a paso.

### Paso 1: Elige tu plataforma de hosting (RSS)

El podcast necesita un "host" — un servicio que almacena tus archivos de audio y genera el RSS feed que usan todas las plataformas para distribuir tu podcast automáticamente.

| Plataforma | Costo | Para quién | Distribución automática |
|---|---|---|---|
| **Spotify for Creators** (antes Anchor) | Gratis | Ideal para empezar. Interfaz sencilla. | Spotify automático + otras plataformas |
| **Buzzsprout** | Gratis limitado / $12/mes | Más control, analytics detallados | Todas las plataformas |
| **Podbean** | Gratis limitado / $9/mes | Buena interfaz, larga trayectoria | Todas las plataformas |
| **Transistor** | $19/mes | Para podcasters serios, múltiples podcasts | Todas las plataformas |
| **Captivate** | $17/mes | Muy buenas herramientas de crecimiento | Todas las plataformas |

**Recomendación para empezar:** **Spotify for Creators** — gratis, fácil, distribución automática a muchas plataformas, y tiene herramientas decentes para ver cómo crece tu podcast.

### Paso 2: Registra en Spotify for Creators

1. Ve a **creators.spotify.com**
2. Crea cuenta con tu email (o inicia sesión si tienes Spotify)
3. Sigue el wizard de setup: nombre del podcast, categoría, idioma, descripción, portada
4. Sube tu primer episodio
5. La aprobación tarda 24-72 horas

### Paso 3: Registra en Apple Podcasts

1. Ve a **podcastsconnect.apple.com**
2. Inicia sesión con tu Apple ID
3. Añade el RSS feed de tu plataforma de hosting (Spotify o la que hayas elegido)
4. La aprobación tarda 1-5 días hábiles

### Paso 4: Otros directorios (opcional pero recomendado)

Una vez aprobado en Spotify y Apple, considera:

- **Amazon Music / Audible** (music.amazon.com/podcasts/submit) — audiencia grande
- **iVoox** (ivoox.com) — especialmente importante si tu audiencia es hispanohablante
- **Pocket Casts**, **Overcast**, **RadioPublic** — se alimentan del RSS automáticamente una vez lo registras

**Nota:** Muchos de estos directorios se alimentan automáticamente del RSS de tu hosting, así que no necesitas subir manualmente a cada uno.

---

## Expectativas realistas de tiempo

Estos son tiempos reales por episodio según tu experiencia:

| Nivel de experiencia | Script | Grabación | Edición | Arte + Social | Show Notes | **TOTAL** |
|---|---|---|---|---|---|---|
| **Primeros 3 episodios** | 3-5h | 2-3× duración | 3-4× duración | 2-3h | 1-2h | **8-15 horas** |
| **Episodios 4-10** | 2-3h | 1.5× duración | 2× duración | 1-2h | 1h | **5-8 horas** |
| **Episodio 20+** | 1-2h | 1× duración | 1-1.5× duración | 1h | 30-45 min | **3-5 horas** |

**Para un episodio de 30 minutos:**
- Primeros 3: espera **4-6 horas totales**
- Episodios 4-10: espera **2.5-3.5 horas totales**
- Experiencia: espera **1.5-2.5 horas totales**

El primer episodio es siempre el más lento — estás aprendiendo el flujo, el equipo, el software. **Esto es normal. No quiere decir que estés haciendo algo mal.**

---

## El episodio trailer

Antes de lanzar tu primer episodio completo, considera grabar un **episodio trailer** (1-2 minutos de duración):

```
Un trailer explica:
- De qué trata tu podcast (tagline)
- A quién va dirigido
- Por qué debería escucharlo
- Cuándo salen los episodios (cadencia)
- Call to action: suscribirse
```

**Por qué es importante:**

Spotify y Apple Podcasts muestran el trailer a potenciales nuevos oyentes como su **primera impresión** de tu podcast, incluso antes del Episodio 1. Un buen trailer:

- Atrapa en los primeros 15 segundos
- Crea urgencia o curiosidad
- Invita a suscribirse
- Define claramente el nicho

**Estructura sugerida (90 segundos):**

```
0:00-0:15  Presentación energética (nombre + hook)
0:15-0:45  Qué es el podcast + para quién (2-3 líneas)
0:45-1:10  Ventaja única / por qué escuchar (1-2 líneas)
1:10-1:30  Cuándo salen episodios + CTA
1:30+      Música de cierre
```

**No necesita ser perfecto — puede ser solo tú hablando directamente a la cámara.** Lo importante es que sea auténtico.

---

## Estructura de archivos

```
kit-podcast-creator/
│
├── CLAUDE.md                          ← Configuración del kit (no modificar)
├── INSTRUCCIONES.md                   ← Este archivo
│
├── podcast-profile.json               ← Se genera en el setup (tu identidad)
│
├── episodio-001-primer-tema.md        ← Script de cada episodio
├── grabacion-ep001.md                 ← Plan de grabación por episodio
├── artwork-ep001.md                   ← Prompts de artwork por episodio
├── social-ep001.md                    ← Copy de social media por episodio
├── shownotes-ep001.md                 ← Show notes por episodio
│
├── production-ep001.html              ← Paquete de producción (HTML, descargable)
├── shownotes-ep001.html               ← Página pública del episodio (HTML)
│
└── .claude/
    └── skills/
        └── podcast-creator/
            ├── SKILL.md               ← Routing y estructura del kit
            └── workflows/
                ├── 00-setup.md        ← Identidad del podcast
                ├── 01-episodio.md     ← Guion completo
                ├── 02-grabacion.md    ← Plan de grabación
                ├── 03-artwork.md      ← Prompts de imagen
                ├── 04-social-media.md ← Plan de lanzamiento
                ├── 05-show-notes.md   ← Show notes y metadatos
                └── 06-html-export.md  ← Exportar HTML
```

---

## Ejemplo de sesión completa

```
Tú:  "hola"
Kit: → Detecta que no hay perfil → lanza setup

Tú:  [responde las preguntas del setup]
Kit: → Genera podcast-profile.json + tagline + intro/outro

Tú:  "guion para el episodio 1 sobre inteligencia artificial para no técnicos"
Kit: → Lee el perfil → pregunta el ángulo → presenta arquitectura → escribe script completo

Tú:  "ok, ahora el plan de grabación"
Kit: → Lee el perfil y el script → genera checklists adaptados al formato

Tú:  "social media"
Kit: → Lee el script → extrae la frase más impactante → genera 15 posts para 5 plataformas en 3 días

Tú:  "exporta todo en html"
Kit: → Lee todos los archivos del episodio → genera production-ep001.html y shownotes-ep001.html → los abre en el browser
```

---

## Preguntas frecuentes

**¿Puedo usar el kit para varios podcasts?**
El kit está pensado para un podcast por carpeta. Para un segundo podcast, copia la carpeta y empieza el setup de nuevo.

**¿Qué pasa si quiero cambiar el nombre o el formato del podcast?**
Di "actualizar perfil" y el kit te mostrará los datos actuales para modificarlos.

**¿El kit genera las imágenes directamente?**
No — genera los prompts listos para copiar en Google Flow (labs.google/fx/tools/image-fx). Tú los pegas ahí y descargas las imágenes.

**¿Funciona en español e inglés?**
El kit responde en el idioma del usuario. Los prompts de artwork se generan en inglés (los modelos de imagen funcionan mejor así).
