# Kit BTQ — Instrucciones de uso

## Requisitos

- Claude Code instalado (CLI o extensión VS Code)
- Acceso a Flow / Google Veo para imágenes
- Acceso a Gemini / Nani Banana 2 para artwork
- Acceso a Kling AI para animaciones

## Cómo usar este kit

1. Abre la carpeta `kit-btq` en Claude Code
2. Escribe cualquier cosa — Laswell se activa solo
3. Dile qué quieres hacer: guión, artwork, quote cards, plan de redes, o registro

## Flujos disponibles

### Guión de episodio
Dile: *"guión para el EP.013"* o *"vamos con el guión de Doc Brown"*
Laswell pide los datos que falten, presenta la arquitectura de 7 segmentos para validar,
y genera el guión completo en formato anchor points con todas las reglas de tono aplicadas.

### Artwork (prompts para Flow / Nani Banana 2)
Dile: *"prompt para el artwork del EP.013"* o *"imagen del capítulo"*
Laswell genera el prompt completo listo para pegar en Flow o Nani Banana 2,
en los 3 formatos: 1:1 (portada), 9:16 (TikTok/Stories), 16:9 (LinkedIn).

### Quote cards
Dile: *"extrae los quotes del guión"* o *"quote cards del EP.013"*
Laswell identifica las 4–6 frases más potentes del guión y genera un prompt
de imagen por cada una, lista para Nani Banana 2.

### Plan de lanzamiento en redes — 3 días
Dile: *"plan de redes para el EP.013"* o *"dame el plan de lanzamiento"*
Laswell genera copy por plataforma y prompts de imagen para:
- Domingo (grabación): Intriga
- Lunes: Contenido
- Miércoles (lanzamiento): Conversión directa

### Registro del episodio
Dile: *"dame los datos para registrar el episodio"* o *"ficha Safe Creative y Spotify"*
Laswell genera en un solo bloque:
- Safe Creative: título, descripción, tags completos, campos pre-seleccionados
- Spotify for Podcasters: título formateado, descripción HTML lista para pegar

## Estructura del kit

```
kit-btq/
├── CLAUDE.md                        ← Bienvenida y comportamiento de Laswell
├── INSTRUCCIONES.md                 ← Este archivo
└── .claude/
    └── skills/
        └── btq-project.md           ← Skill completo con todo el contexto BTQ
```

## Episodios activos (Mayo 2026)

| EP | Referencia | Estado |
|---|---|---|
| EP.011 | Frieren | Guión listo · Pendiente artwork → grabación → publicación |
| EP.012 | Bohemian Rhapsody | Guión v3.0 FINAL · Inicia post-lanzamiento EP.011 |
| EP.013+ | Ver roadmap en btq-project.md | — |
