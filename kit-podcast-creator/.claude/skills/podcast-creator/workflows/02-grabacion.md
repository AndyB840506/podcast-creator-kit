# Workflow 02 — Plan de Grabación

Genera una guía completa para el día de grabación del episodio, adaptada al formato del podcast.

**Regla fundamental: Lee `podcast-profile.json` y el script del episodio si existe. No inventes datos de duración o segmentos.**

---

## ⚠️ NOTA PARA EL PRIMER EPISODIO

Si este es uno de tus primeros 3 episodios, **espera que el proceso completo (grabación + edición) tome entre 3-6 veces más tiempo del estimado**. Esto no significa que estés haciendo algo mal — es la curva de aprendizaje normal cuando estás configurando equipo, software y procedimientos por primera vez.

Con cada episodio el proceso se vuelve más rápido. Al episodio 4-5, el tiempo se normaliza significativamente.

**No te desanimes. El tiempo invertido en aprender ahora vale la pena para los 50 episodios que vienen después.**

---

## Paso 0 — Cargar datos

1. Lee `podcast-profile.json`: formato, duración promedio, host(s).
2. Busca el script del episodio más reciente (`episodio-*.md`). Si hay varios, pregunta cuál.
3. Si el script existe, extrae: número de episodio, segmentos y duración estimada.
4. Si no hay script, pregunta: "¿Para qué número de episodio es el plan? ¿Cuántos minutos aproximadamente?"

---

## Paso 1 — Generar plan de grabación completo

Genera el documento de una vez. Adapta cada sección al formato del perfil.

---

### SECCIÓN A: Checklist pre-grabación (30 min antes)

**Equipo técnico:**
- [ ] Micrófono conectado y testeado — nivel de entrada entre -12dB y -6dB
- [ ] Auriculares puestos (no grabar con bocinas — riesgo de feedback)
- [ ] Software de grabación abierto y configurado en modo RECORD
- [ ] Carpeta de destino de grabación verificada y con espacio suficiente
- [ ] Espacio acústico preparado: puertas cerradas, ventiladores apagados, teléfono en silencio y modo avión
- [ ] Botella de agua cerca (fuera del campo del micrófono)
- [ ] Script abierto en pantalla o impreso en tamaño legible

**[Si formato = `interview`]**
- [ ] Link de sesión enviado al invitado con mínimo 24h de anticipación
- [ ] Briefing del invitado enviado: nombre del podcast, duración, 3 temas a cubrir, instrucciones de audio
- [ ] Plataforma de grabación configurada con pistas separadas por participante (Riverside.fm o Zencastr recomendados)
- [ ] Plan B establecido: número de teléfono del invitado en caso de problemas de conexión
- [ ] Preguntas del script revisadas y marcadas en orden de prioridad

**[Si formato = `co-host`]**
- [ ] Confirmación de disponibilidad de todos los hosts con al menos 2h de anticipación
- [ ] Asignación de segmentos revisada y confirmada con los co-hosts
- [ ] Canal de comunicación paralelo abierto (ej. WhatsApp) para señales durante la grabación

---

### SECCIÓN B: Estructura de tiempos

Tabla de segmentos con tiempos estimados (extraída del script si existe, o calculada por duración del perfil):

| # | Segmento | Tiempo estimado | Notas de producción |
|---|----------|-----------------|---------------------|
| 1 | Intro + Hook | 1-2 min | Empezar fuerte, no titubear |
| 2 | [Segmentos del episodio...] | [X min] | |
| N | Outro + CTA | 1-2 min | No olvidar mencionar las plataformas |
| — | **Total estimado** | **[X] min** | Margen de ±15% es normal |

> Graba siempre 10 segundos de silencio completo al inicio (antes del intro) — es el "room tone" para la edición.

---

### SECCIÓN C: Durante la grabación

**Manejo de errores en vivo:**
- Si te equivocas: haz una `[PAUSA]` de 2 segundos, di "corte" en voz baja, y repite la frase desde el inicio del párrafo
- No edites en tiempo real — sigue adelante y anota el timestamp del error en el script
- Toma de agua solo entre segmentos, nunca mientras estás grabando

**Control de calidad en vivo:**
- Cada 10-15 minutos: revisar el medidor de nivel de audio brevemente
- Al cambiar de segmento: verificar que el archivo sigue grabando

**[Si formato = `interview`]**
- Escucha activa: deja que el invitado termine completamente antes de hablar
- Si la respuesta se desvía: "Qué interesante, y volviendo a [tema específico]..."
- La pregunta más profunda o personal va en el segmento 3-4, cuando el invitado ya está cómodo
- Si el invitado da una respuesta muy corta: "¿Puedes contarme más sobre eso?"

**[Si formato = `co-host`]**
- Señal visual acordada para "paso la palabra" (ej. mirar a la cámara o señal de mano)
- Si hablan al mismo tiempo: pausa de 2 segundos y cede al otro

---

### SECCIÓN D: Checklist post-grabación

- [ ] Archivo de audio guardado con nombre: `[PODCAST]-EP[NNN]-[FECHA]-RAW.wav` o `.mp3`
- [ ] Backup inmediato en segunda ubicación (nube o disco externo)
- [ ] **[Si interview]** Archivo del invitado descargado por separado antes de cerrar la sesión
- [ ] Notas de edición escritas: timestamps de errores, cortes sugeridos, momentos destacados
- [ ] Script actualizado con cambios realizados durante la grabación (improvisaciones que funcionaron)
- [ ] Duración real del archivo verificada (comparar con estimado)

---

### SECCIÓN E: Guía de edición básica

**Puntos de corte naturales (buscar estos):**
- Respiraciones profundas antes de cambios de tema
- Silencios de más de 1.5 segundos no intencionales
- Muletillas repetitivas: "ehhh", "osea", "básicamente", "¿no?"
- Ruidos externos identificados durante la grabación

**Checklist de edición antes de publicar:**
- [ ] Limpieza de ruido de fondo aplicada (Auphonic, Adobe Audition, o similar)
- [ ] Normalización de volumen: **-16 LUFS** para Spotify, -19 LUFS para Apple Podcasts
- [ ] Música de intro añadida con fade in/out
- [ ] Música de outro añadida
- [ ] Transiciones entre segmentos con música de fondo a bajo volumen
- [ ] Duración final dentro del rango esperado para este podcast
- [ ] Escucha completa del episodio editado antes de subir

---

### SECCIÓN F: Software recomendado para grabación y edición

**GRABACIÓN (elige uno según tu SO):**

| Software | Plataforma | Costo | Para quién |
|---|---|---|---|
| **Audacity** | Windows / Mac / Linux | Gratis | Empezar. Sencillo pero funcional. |
| **GarageBand** | Mac (incluido en OS) | Gratis | Mac: más intuitivo que Audacity. |
| **Descript** | Windows / Mac | Freemium / $12/mes | Edita audio como documento de texto. Transcripción automática. |
| **Adobe Audition** | Windows / Mac | $20/mes (Creative Cloud) | Profesional, muchas herramientas. |
| **Hindenburg Journalist** | Windows / Mac | $95 USD (una sola vez) | Especialmente para podcasters. Excelente para entrevistas. |

**GRABACIÓN REMOTA (para entrevistas):**

| Plataforma | Costo | Características |
|---|---|---|
| **Riverside.fm** | Freemium / $15/mes | Grabación en alta calidad, pistas separadas, fácil de usar. |
| **Zencastr** | Freemium / $18/mes | Similar a Riverside, muy buena estabilidad. |
| **SquadCast** | Freemium / $12/mes | Alternativa más económica. |

**Recomendación para empezar:** 
- Windows/Linux: **Audacity** (gratis) o **Descript** (freemium).
- Mac: **GarageBand** (gratis, incluido en el SO).
- Para entrevistas: **Riverside.fm** o **Zencastr** (freemium es suficiente para empezar).

---

### SECCIÓN G: Transcripción automática

Después de editar, transcribir el episodio te ayuda a: (1) crear show notes, (2) mejorar SEO, (3) extraer quotes para redes sociales.

| Herramienta | Costo | Precisión | Nota |
|---|---|---|---|
| **Whisper (OpenAI)** | Gratis (requiere Python) | Excelente | Open source, descarga local. |
| **TurboScribe** | Freemium / $10/mes | Muy buena | Interfaz fácil, rápido. |
| **Descript** | Incluido si usas para editar | Muy buena | Ya tienes el audio en la plataforma. |
| **Otter.ai** | Freemium / $17/mes | Bueno (especialmente para entrevistas) | Colaborativo, puede compartir con invitado. |

**Recomendación para empezar:** **Descript** (si ya lo usas) o **TurboScribe** (si no).

---

### SECCIÓN H: Cadencia semanal recomendada

Para mantener una consistencia 1 episodio/semana sin agotarte:

```
LUNES
├─ Revisar script del próximo episodio o escribir uno nuevo
└─ Tiempo: 1-2 horas

MIÉRCOLES
├─ Grabar episodio
└─ Tiempo: duración del episodio × 1.5 a 2 veces

JUEVES
├─ Editar + exportar en alta calidad
└─ Tiempo: duración del episodio × 2 a 3 veces (primeros episodios)

VIERNES
├─ Generar artwork del episodio
├─ Crear copy de social media para 3 días
├─ Escribir show notes
└─ Tiempo: 2-3 horas

SIGUIENTE MIÉRCOLES
├─ Publicar episodio en plataforma principal
├─ Distribuir en todas las plataformas
└─ Programar publicación de redes sociales para los 3 días
```

**Buffer de seguridad:** Mantén siempre al menos 1 episodio grabado pero no publicado. Si la semana se complica, tienes margen para no faltar a tu cadencia de publicación.

---

## Paso 2 — Guardar y presentar

1. Guarda como `grabacion-ep[NNN].md` en el directorio actual.
2. Muestra resumen:

```
══════════════════════════════════════════
  Plan de grabación generado ✓
══════════════════════════════════════════
  Episodio:    EP.[NNN]
  Formato:     [formato]
  Duración:    ~[X] minutos estimados
  Checklists:  [N] ítems en total
  Archivo:     grabacion-ep[NNN].md
══════════════════════════════════════════
```

3. Pregunta: "¿Continuamos con el artwork del episodio o el plan de social media?"
