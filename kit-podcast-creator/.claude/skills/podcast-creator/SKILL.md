---
name: podcast-creator
description: "Crea un podcast desde cero: setup de identidad, guiones completos, plan de grabación, artwork, social media, show notes y exportación HTML. Triggers: 'crear podcast', 'nuevo podcast', 'empezar podcast', 'quiero un podcast', 'guion de podcast', 'script de episodio', 'plan de lanzamiento podcast', 'show notes', 'artwork podcast', 'producir episodio', 'podcast desde cero', 'create podcast', 'podcast script', 'episode script', 'podcast from scratch', 'podcast workflow', 'podcast production', 'producción de podcast', 'lanzar podcast'."
---

# Podcast Creator — Producción completa de podcast

Guía todo el ciclo de producción de un podcast: desde la identidad de marca hasta el episodio publicado.

**Regla fundamental: Lee siempre `podcast-profile.json` antes de cualquier workflow. Si no existe, lanza `00-setup.md` primero.**

---

## Routing

| Si el usuario dice... | Lanza |
|---|---|
| "setup", "crear mi podcast", "empezar desde cero", "configure", "identidad", "perfil", "configurar" | `workflows/00-setup.md` |
| "guion", "script", "episodio", "episode", "escribir episodio", "nuevo episodio" | `workflows/01-episodio.md` |
| "grabar", "grabación", "recording", "plan de grabación", "checklist grabación", "día de grabación" | `workflows/02-grabacion.md` |
| "artwork", "portada", "imagen del episodio", "cover", "arte del episodio", "prompts de imagen" | `workflows/03-artwork.md` |
| "social", "redes", "social media", "plan de lanzamiento", "posts", "instagram", "linkedin" | `workflows/04-social-media.md` |
| "show notes", "notas del episodio", "descripción episodio", "descripción spotify", "metadatos" | `workflows/05-show-notes.md` |
| "html", "exportar", "descargar", "production packet", "paquete de producción", "exportar todo" | `workflows/06-html-export.md` |

---

## Datos del podcast (`podcast-profile.json`)

Este archivo se genera en `00-setup.md` y lo leen todos los workflows. Estructura esperada:

```json
{
  "nombre": "",
  "tagline": "",
  "descripcion_corta": "",
  "descripcion_larga": "",
  "formato": "solo | interview | co-host",
  "host": [{ "nombre": "", "bio": "" }],
  "audiencia": {
    "demografico": "",
    "intereses": "",
    "dolor_principal": ""
  },
  "tono": "",
  "categoria": "",
  "duracion_min": 30,
  "cadencia": "",
  "plataformas": [],
  "colores": {
    "principal": "",
    "secundario": "",
    "acento": ""
  },
  "logo_descripcion": "",
  "numeracion": "EP.001",
  "intro_template": "",
  "outro_template": "",
  "reglas_tono": [],
  "hashtags_base": [],
  "links": {
    "spotify": "",
    "apple": "",
    "youtube": "",
    "rss": ""
  }
}
```

---

## Flujo de producción recomendado

```
00-setup → 01-episodio → 02-grabacion → 03-artwork → 04-social-media → 05-show-notes → 06-html-export
```

Puedes saltar a cualquier workflow — solo asegúrate de tener el perfil creado primero.
