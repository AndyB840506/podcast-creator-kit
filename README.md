# Skills para Claude Code

Colección de skills y kits para potenciar Claude Code. Cada proyecto es una skill completa con instrucciones, workflows y documentación.

## 📌 Skills disponibles

### 🎙️ Podcast Creator Kit

**Descripción:** Guía completa para producir podcasts desde cero: setup de identidad, scripts, grabación, artwork, social media, show notes y exportación HTML.

**Ubicación:** `/kit-podcast-creator/`

**Cómo usar:**
```bash
cd kit-podcast-creator
claude
```

Escribe "hola" o "hello" y el kit se activa automáticamente.

**Características:**
- ✅ Detección automática de idioma (español/inglés)
- ✅ Setup inteligente de podcast (identidad, tono, marca)
- ✅ Generación de scripts con formato flexible (solo/entrevista/co-host)
- ✅ Plan de grabación con recomendaciones de software
- ✅ Prompts para IA generativa de imágenes
- ✅ Plan de lanzamiento en redes sociales (3 días)
- ✅ Show notes optimizados para SEO
- ✅ Exportación a HTML (paquete de producción + página pública)

**Expectativas realistas:**
- Primeros 3 episodios: 8-15 horas
- Episodios 4-10: 5-8 horas
- Experiencia: 3-5 horas

**Plataformas soportadas:**
- Spotify for Creators
- Apple Podcasts
- YouTube
- iVoox
- Y más...

---

## 📚 Documentación

Cada skill incluye:
- **CLAUDE.md** — Configuración y comportamiento inicial
- **INSTRUCCIONES.md** — Guía paso a paso
- **README.md** — Descripción y características
- **docs/recursos.md** — Referencias de herramientas
- **Workflows** en `.claude/skills/*/workflows/` — Pasos específicos

---

## 🚀 Instalación

### Opción 1 — Clonar el repositorio
```bash
git clone https://github.com/AndyB840506/podcast-creator-kit.git
cd podcast-creator-kit/kit-podcast-creator
claude
```

### Opción 2 — Desde Claude Code
1. Abre Claude Code
2. Archivo → Abrir carpeta
3. Selecciona `kit-podcast-creator/`
4. ¡Listo!

---

## 🎯 Estructura del repositorio

```
podcast-creator-kit/
├── kit-podcast-creator/          ← Skill principal
│   ├── CLAUDE.md                 ← Entrada (edición inicial)
│   ├── INSTRUCCIONES.md          ← Guía de uso
│   ├── README.md                 ← Descripción detallada
│   ├── docs/
│   │   └── recursos.md           ← Referencia de herramientas
│   └── .claude/skills/podcast-creator/
│       ├── SKILL.md              ← Configuración de skill
│       └── workflows/            ← 6 workflows de producción
│           ├── 00-setup.md
│           ├── 01-episodio.md
│           ├── 02-grabacion.md
│           ├── 03-artwork.md
│           ├── 04-social-media.md
│           ├── 05-show-notes.md
│           └── 06-html-export.md
```

---

## 💡 Casos de uso

### Empezar un podcast desde cero
1. Abre el kit
2. Di "hola"
3. Sigue el setup (identidad, formato, tono)
4. Genera script, grabación, artwork, social media

### Producir episodios semanales
1. Usa el kit para generar scripts
2. Graba con la guía de checklists
3. Edita y exporta con el paquete HTML
4. Publica con el plan de social media

### Entrevistar a invitados
1. El kit genera cuestionario escalado
2. Briefing para enviar al invitado
3. Script con notas de escucha activa
4. Kit de cross-promotion para el invitado

---

## 🛠️ Herramientas recomendadas

El kit incluye referencias y guías para:
- **Grabación:** Audacity, GarageBand, Descript
- **Entrevistas remotas:** Riverside.fm, Zencastr
- **Edición:** Adobe Audition, Hindenburg Journalist
- **Transcripción:** TurboScribe, Otter.ai, Whisper
- **Imágenes IA:** Google Flow, Midjourney, DALL-E 3
- **Hosting:** Spotify for Creators, Buzzsprout, Transistor
- **Música:** YouTube Audio Library, Incompetech, Epidemic Sound

Ver `docs/recursos.md` para detalles completos.

---

## 📖 Flujo de producción típico

```
Lunes:     Escribir/revisar script
Miércoles: Grabar episodio
Jueves:    Editar audio
Viernes:   Crear artwork + social media + show notes
Siguiente miércoles: Publicar en todas las plataformas
```

---

## 🌍 Idiomas

El kit responde automáticamente en:
- **Español** — Si empiezas con "hola" o escribes en español
- **Inglés** — Si empiezas con "hello" o escribes en inglés

Mantiene el idioma durante toda la sesión.

---

## ✨ Características destacadas

✅ **Realismo desde el inicio** — Te prepara para que el primer episodio tome 8-15 horas
✅ **Formato flexible** — Cada episodio puede ser solo/entrevista/co-host
✅ **Herramientas integradas** — Recomendaciones concretas (no genéricas)
✅ **Documentos de apoyo** — Cuestionarios, briefings, kits de cross-promotion
✅ **Cadencia sostenible** — Diseñado para 1 episodio/semana
✅ **Multiidioma** — Español e inglés
✅ **Exportación HTML** — Paquete de producción + página pública

---

## 📝 Requisitos

- **Claude Code** (cualquier versión reciente)
- Nada más. El kit no necesita instalaciones adicionales.

---

## 🤝 Contribuciones

¿Mejoras o sugerencias? Abre un issue o pull request.

---

## 📄 Licencia

MIT License — Usa, modifica y comparte libremente.

---

## 🎙️ Próximas adiciones

- [ ] Video tutorials
- [ ] Ejemplos de podcasts generados con el kit
- [ ] Automatización de distribución multi-plataforma
- [ ] Integración con Spotify API para analytics

---

**¿Listo para crear tu podcast?** 

```bash
cd kit-podcast-creator
claude
```

Escribe "hola" y comienza. 🚀
