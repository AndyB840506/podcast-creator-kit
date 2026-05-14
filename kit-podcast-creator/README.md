# 🎙️ Podcast Creator Kit

Un kit completo de producción de podcasts para **Claude Code**. Guía automática desde la identidad de tu podcast hasta la publicación y distribución en todas las plataformas.

**Soporta:** Solo · Entrevistas · Co-hosting · Múltiples idiomas (español e inglés)

---

## ¿Qué hace este kit?

Automatiza todo el ciclo de producción de podcast:

| Workflow | Genera |
|---|---|
| **Setup** | Identidad del podcast, tagline, intro/outro, guía de tono, prompts de artwork |
| **Script** | Guion completo palabra por palabra, adaptado a tu formato |
| **Grabación** | Checklists + guía de edición + recomendaciones de software |
| **Artwork** | Prompts listos para IA (Google Flow, Midjourney, etc.) en 3 formatos |
| **Social Media** | Copy para 3 días de lanzamiento en 5 plataformas + kit de cross-promotion |
| **Show Notes** | Descripciones optimizadas para SEO en Spotify, Apple Podcasts y web |
| **Exportar HTML** | Paquete de producción interno + página pública del episodio |

---

## ⚡ Empezar en 3 pasos

### 1️⃣ Instalar

**Opción A — Desde terminal:**
```bash
git clone https://github.com/AndyB840506/podcast-creator-kit.git
cd podcast-creator-kit/kit-podcast-creator
claude
```

**Opción B — En Claude Code:**
```
Archivo → Abrir carpeta → selecciona podcast-creator-kit/kit-podcast-creator/
```

**Opción C — En VS Code:**
Abre la carpeta en VS Code y usa la extensión de Claude Code.

### 2️⃣ Escribe cualquier cosa

El kit se activa automáticamente con tu primer mensaje:
- En **español:** escribe "hola"
- En **inglés:** escribe "hello"

### 3️⃣ Sigue el flujo

El kit te guía paso a paso. Primera sesión: ~15 minutos para configurar tu identidad.

---

## 💡 Características principales

### Detección automática de idioma
Responde en español o inglés según tu primer mensaje. Mantiene el idioma durante toda la sesión.

### Formato flexible por episodio
Tu podcast puede ser principalmente en formato *solo*, pero cada episodio puede cambiar:
- **Solo** → script que lees palabra por palabra
- **Entrevista** → genera cuestionario + briefing para invitado + kit de cross-promotion
- **Co-hosting** → genera script con diálogos + documento de sincronía pre-grabación

### Expectativas realistas
El kit te prepara desde el inicio:
- **Primeros 3 episodios:** 8-15 horas cada uno (curva de aprendizaje normal)
- **Episodios 4-10:** 5-8 horas
- **Experiencia:** 3-5 horas

No es que hagas algo mal — es que estás aprendiendo las herramientas. **Esto es normal.**

### Cadencia semanal sostenible
Flujo recomendado:
- **Lunes:** escribe/revisa script
- **Miércoles:** graba
- **Jueves:** edita
- **Viernes:** artwork + social media + show notes
- **Siguiente miércoles:** publica

### Guía de herramientas incluida
El kit recomienda software específico para cada tarea:
- **Grabación:** Audacity (gratis), Descript ($12/mes), Adobe Audition ($20/mes)
- **Entrevistas remotas:** Riverside.fm, Zencastr
- **Transcripción:** TurboScribe, Otter.ai
- **Imágenes IA:** Google Flow (gratis), Midjourney, DALL-E 3
- **Hosting:** Spotify for Creators (gratis), Buzzsprout, Transistor
- **Música:** YouTube Audio Library, Incompetech, Epidemic Sound

---

## 📁 Estructura del kit

```
kit-podcast-creator/
│
├── CLAUDE.md                           ← Configuración (no editar)
├── INSTRUCCIONES.md                    ← Guía completa de uso
├── README.md                           ← Este archivo
│
├── podcast-profile.json                ← Tu identidad (se genera)
│
├── episodio-[NNN]-[tema].md            ← Scripts de episodios
├── grabacion-ep[NNN].md                ← Planes de grabación
├── artwork-ep[NNN].md                  ← Prompts de IA para imágenes
├── social-ep[NNN].md                   ← Copy de social media
├── shownotes-ep[NNN].md                ← Show notes y metadatos
│
├── production-ep[NNN].html             ← Paquete interno (descargable)
├── shownotes-ep[NNN].html              ← Página pública del episodio
│
├── docs/
│   └── recursos.md                     ← Referencia de herramientas
│
└── .claude/
    └── skills/
        └── podcast-creator/
            ├── SKILL.md                ← Routing del kit
            └── workflows/
                ├── 00-setup.md         ← Identidad
                ├── 01-episodio.md      ← Script
                ├── 02-grabacion.md     ← Plan de grabación
                ├── 03-artwork.md       ← Prompts de imagen
                ├── 04-social-media.md  ← Plan de lanzamiento
                ├── 05-show-notes.md    ← Show notes
                └── 06-html-export.md   ← Exportar HTML
```

---

## 🎬 Ejemplo de sesión completa

```
Tú:  "hola"
Kit: → Detecta español
     → Pregunta: "¿ya sabes de qué va tu podcast?"

Tú:  "No, todavía estoy explorando"
Kit: → Muestra 6 categorías con sub-ideas
     → Espera que elijas una

Tú:  "Entretenimiento, música y cultura pop"
Kit: → Lanza workflow 00-setup.md
     → Te hace 4 bloques de preguntas (~15 min)
     → Genera podcast-profile.json + archivos de marca

Tú:  "guion para el episodio 1"
Kit: → Lee el perfil
     → Pregunta tema, ángulo, duración
     → Genera script completo palabra por palabra

Tú:  "plan de grabación"
Kit: → Lee el script
     → Genera checklists adaptados
     → Recomienda software y cadencia semanal

Tú:  "social media"
Kit: → Extrae la frase más impactante
     → Genera 15 posts para 5 plataformas en 3 días

Tú:  "exporta todo en html"
Kit: → Genera production-ep001.html y shownotes-ep001.html
     → Los abre en el navegador
```

---

## 📚 Requisitos

- **Claude Code** instalado (versión reciente)
- Nada más. El kit no necesita instalaciones adicionales.

---

## 🚀 Flujo de producción recomendado

```
Setup (una vez)
    ↓
Guion del episodio 1
    ↓
Plan de grabación
    ↓
[GRABAS]
    ↓
Artwork
    ↓
Social Media (3 días)
    ↓
Show Notes
    ↓
Exportar HTML
    ↓
[PUBLICAS]
    ↓
[REPITES DESDE "Guion" para cada episodio]
```

**Puedes saltar pasos:** el kit siempre lee los archivos existentes para no repetirte preguntas.

---

## 🎯 Casos de uso

### Podcast solo
Tú hablas de tu tema. El kit genera scripts que lees palabra por palabra o usas como guía.

### Podcast de entrevistas
El kit genera:
- Cuestionario escalado de preguntas (10-15 preguntas)
- Briefing para enviar al invitado
- Kit de cross-promotion para que el invitado comparta en sus redes

### Podcast co-hosted
El kit genera:
- Script en formato diálogo con [HOST: Nombre] tags
- Documento de sincronía pre-grabación
- Asignación de segmentos y tiempo de habla por host

---

## 💬 Lenguaje

El kit responde en:
- **Español** — si empiezas con "hola" o tu primer mensaje está en español
- **Inglés** — si empiezas con "hello" o tu primer mensaje está en inglés
- **Bilingüe** — si no es claro, ofrece ambos idiomas

---

## 📖 Documentación completa

- **INSTRUCCIONES.md** — Guía paso a paso (en kit-podcast-creator/)
- **docs/recursos.md** — Referencia de herramientas (música, software, hosting, transcripción, IA)
- **Workflows individuales** — Cada archivo .md en .claude/skills/podcast-creator/workflows/

---

## ⚙️ Personalización

El kit se adapta a tu podcast a través de `podcast-profile.json`. Puedes cambiar:
- Nombre, tagline, descripción
- Formato (solo/entrevista/co-host)
- Duración y cadencia
- Tono y reglas de escritura
- Colores de marca
- Plataformas de distribución

Para actualizar: abre el kit en cualquier momento y di "actualizar perfil".

---

## 📝 Notas importantes

**⚠️ El primer episodio toma tiempo**

Los primeros 3 episodios toman entre 8-15 horas cada uno. Esto incluye:
- Escribir el script (3-5h)
- Grabar (2-3× la duración del episodio)
- Editar (3-4× la duración del episodio)
- Artwork + social media (2-3h)
- Show notes (1-2h)

**Esto es normal. No quiere decir que hagas algo mal.** Estás aprendiendo el flujo, el equipo y el software. Al episodio 4-5, el tiempo se reduce significativamente.

**Mantén cadencia, no cantidad**

Un episodio por semana publicado puntualmente vale más que 3 episodios en una semana y luego 2 meses de silencio.

---

## 🤝 Contribuciones

¿Mejoras al kit? Abre un pull request o escribe un issue.

---

## 📄 Licencia

MIT License — usa, modifica y comparte libremente.

---

## 🙋 ¿Preguntas?

Revisa **INSTRUCCIONES.md** o abre una issue en GitHub.

---

**¡Bienvenido a tu podcast!** 🎙️

Ahora abre Claude Code, abre la carpeta `kit-podcast-creator/` y escribe "hola" (o "hello"). El kit hace el resto.
