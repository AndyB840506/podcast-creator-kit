---
name: prompt-reviewer
description: "Evalúa y mejora prompts, skills, y instrucciones. Encuentra problemas de claridad, completitud, y efectividad. Propone arreglos específicos. Triggers: 'revisa este prompt', 'evalúa esta skill', 'mejora esta instrucción', 'review my prompt', 'evaluate this skill', 'improve these instructions', 'prompt audit', 'skill review', 'instruction check', 'this is unclear', 'is this good prompt', 'fix my prompt', 'mejorar el prompt', 'revisar instrucción', 'está claro esto'."
---

# Prompt Reviewer — Evalúa y mejora prompts y skills

Analiza cualquier prompt, skill, instrucción o documentación. Encuentra problemas de claridad, completitud y efectividad. Propone mejoras específicas con razonamiento. Mejor y más rápido que las herramientas genéricas.

**Regla fundamental: Devolver mejoras concretas y ejecutables, no crítica vaga. Cada hallazgo debe incluir el problema exacto, por qué es un problema, y la solución propuesta.**

---

## Paso 1 — Entender qué se va a revisar

### 1.1 Detectar el tipo de contenido

El usuario puede pasar:
- Un **prompt** (instrucciones para IA)
- Una **skill** (archivo `.md` con estructura BTQ-style)
- Una **instrucción** (procedimiento paso a paso)
- Una **documentación** (manual, guía, README)
- Un **fragmento de código comentado**

No preguntes. Analiza el contenido y adapta el análisis:
- Si es skill → evalúa estructura, triggers, reglas fundamentales, flujo
- Si es prompt → evalúa claridad, contexto, restricciones, ejemplos
- Si es instrucción → evalúa secuencia, completitud, ambigüedad
- Si es documentación → evalúa navegabilidad, ejemplos, exactitud

### 1.2 Ofrecer profundidad de análisis

Si el usuario NO especifica → pregunta:

> Perfecto, voy a revisar esto. ¿Quieres que sea:
>
> **[1] RÁPIDO** — 2-3 min · encuentra lo más importante (ambigüedad, falta de ejemplos, errores de lógica)
>
> **[2] PROFUNDO** — 5-10 min · análisis completo: claridad, completitud, edge cases, flujo, redundancias, inconsistencias
>
> ¿Cuál prefieres?

Si el usuario dice "rápido" o no responde → va a RÁPIDO. Si dice "profundo" o "todo" → va a PROFUNDO.

---

## Paso 2 — Análisis RÁPIDO (por defecto, 2-3 minutos)

Busca solo las 3-5 cosas que más impacto tienen:

### Checklist RÁPIDO:

- ❌ ¿Hay frases ambiguas o contradictorias?
- ❌ ¿Falta un ejemplo clave?
- ❌ ¿Hay un paso faltante o fuera de orden?
- ❌ ¿El objetivo está claro o es vago?
- ❌ ¿Hay terminología inconsistente?

Por cada problema encontrado:
1. **Qué es** — la línea o sección exacta
2. **Por qué es problema** — cómo afecta la ejecución
3. **Solución propuesta** — el texto mejorado

Presenta como tabla compacta:

| # | Problema | Línea/Sección | Impacto | Solución |
|---|----------|---------------|--------|----------|
| 1 | [texto problemático] | [ubicación] | [cómo afecta] | [reescritura] |
| 2 | ... | ... | ... | ... |

Termina con:
> **Score rápido:** X/10 — está [claro/confuso]. Lo más urgente es [problema #1].
>
> ¿Quieres que profundice más o que apliquemos estas mejoras?

---

## Paso 3 — Análisis PROFUNDO (5-10 minutos)

Análisis exhaustivo en 5 dimensiones:

### 3.1 CLARIDAD (10 puntos)
- ¿Cada frase tiene UNA idea?
- ¿Hay palabras vagas ("mayormente", "generalmente", "probablemente")?
- ¿Está activo o pasivo? (activo es mejor)
- ¿Audiencia clara? ¿Nivel técnico apropiado?
- ¿Definiciones de términos no obvios?

Hallazgos: lista de frases confusas con reescrituras

### 3.2 COMPLETITUD (10 puntos)
- ¿Falta un paso previo o una precondición?
- ¿Qué pasa si X, Y o Z? ¿Se cubre?
- ¿Hay un camino feliz vs. camino de error?
- ¿Todas las herramientas/dependencias mencionadas?
- ¿Outputs claramente definidos?

Hallazgos: lista de gaps + cómo llenarlos

### 3.3 ESTRUCTURA Y FLUJO (10 puntos)
- ¿El orden lógico tiene sentido?
- ¿Hay saltos entre ideas?
- ¿Secciones claramente separadas?
- ¿Navegación (índice, links internos) funcionando?
- ¿Repeticiones innecesarias?

Hallazgos: reorganización propuesta si es necesaria

### 3.4 CONSISTENCIA (10 puntos)
- ¿Mismo término usado igual siempre? (no "usuario" vs "actor")
- ¿Formato de ejemplos consistente?
- ¿Tone (formal/casual) consistente?
- ¿Markdown/tipografía aplicada uniformemente?

Hallazgos: tabla de inconsistencias + estandarización

### 3.5 ACTIONABILIDAD (10 puntos)
- ¿Alguien sin conocimiento previo podría ejecutar esto?
- ¿Hay decisiones sin criterio claro? ("elige lo mejor")
- ¿Ejemplos concretos o genéricos?
- ¿Comandos exactos vs. "haz algo parecido"?
- ¿Hay blockers ocultos?

Hallazgos: lista de partes no-ejecutables + cómo hacerlas ejecutables

### Presentación PROFUNDO:

```
═══════════════════════════════════════
ANÁLISIS PROFUNDO — [Nombre del prompt/skill]
═══════════════════════════════════════

PUNTUACIÓN GENERAL: X/50

── CLARIDAD (X/10) ──────────────────────
[Hallazgos por número]

── COMPLETITUD (X/10) ───────────────────
[Hallazgos por número]

── ESTRUCTURA (X/10) ────────────────────
[Hallazgos por número]

── CONSISTENCIA (X/10) ──────────────────
[Hallazgos por número]

── ACTIONABILIDAD (X/10) ─────────────────
[Hallazgos por número]

═══════════════════════════════════════

TOP 3 PRIORIDADES:
1. [Problema más crítico + cómo arreglarlo]
2. [Segundo más crítico]
3. [Tercero]

¿Quieres que aplique estas mejoras automáticamente?
```

---

## Paso 4 — Proponer y aplicar correcciones

Si el usuario dice "sí, aplica" o "arregla esto":

### 4.1 Mostrar correcciones lado a lado

Para cada corrección:
```
────────────────────
ANTES:
[texto original]

DESPUÉS:
[texto mejorado]

RAZONAMIENTO:
[Por qué es mejor]
────────────────────
```

### 4.2 Aplicar automáticamente

Usa Edit tool para cada sección del archivo.

Si son muchos cambios (5+):
- Aplica en 2-3 bloques paralelos
- Espera a que terminen
- Verifica que el archivo quedó coherente

### 4.3 Verificar coherencia post-edición

Después de aplicar edits:
- Revisar saltos entre secciones editadas
- Verificar que los números/referencias internas siguen siendo válidos
- Si algo se rompió, corregir

---

## Paso 5 — Flujo de conversación final

Después de análisis (rápido o profundo):

> **Hallazgos:**
> [tabla o lista]
>
> **Siguiente paso — elige uno:**
> - "Aplica las mejoras" → automatizo los cambios
> - "Profundiza más" → análisis completo
> - "Enfócate en X" → re-analizo solo esa sección
> - "Déjalo así" → fin

---

## Modos especiales

### Modo SKILL AUDIT (si el contenido es un `.md` de skill)

Además de los 5 análisis:
- Verificar **triggers** (¿cubren sinónimos?)
- Verificar **frontmatter** (name en kebab-case? description completa?)
- Verificar **regla fundamental** (¿está clara?)
- Verificar **estructura de pasos** (¿tiene lógica BTQ?)
- Verificar **prohibiciones** (¿están explícitas?)
- Verificar **ejemplos** (¿reales o genéricos?)

Resulta en una tabla extra: **SKILL CHECKLIST**

### Modo RÁPIDO-RÁPIDO (una línea)

Si el usuario escribe algo tipo "is this clear?" o "¿está bien?" sin contenido específico → responde en UNA sola línea:

> "Sí, está claro" — o — "No, porque [razón]. Cambia X a Y."

---

## Reglas generales

1. **Nunca inventes problemas que no existan** — revisa de verdad antes de reportar
2. **Sé específico** — "es vago" no ayuda; "La frase 'usa lo mejor' no dice cómo decidir" sí
3. **Propón mejoras ejecutables** — no "mejora la claridad"; propón el texto exacto mejorado
4. **Preserva la voz original** — si es casual, no lo hagas formal (a menos que el usuario lo pida)
5. **Detecta el idioma** — si está en español, analiza en español; si está en inglés, en inglés
6. **Sé rápido en MODO RÁPIDO** — máximo 2 minutos, máximo 5 problemas
7. **No seas pedante** — los detalles de Oxford comma o espacios post-punto importan menos que la claridad

---

## Ejemplos de antes/después

### Ejemplo 1 — Prompt vago
**ANTES:**
> "Genera un resumen ejecutivo del documento. Asegúrate de que sea breve pero completo."

**DESPUÉS:**
> "Genera un resumen ejecutivo de máximo 250 palabras que cubra: (1) problema identificado, (2) solución propuesta, (3) impacto esperado. Omite detalles técnicos."

**POR QUÉ:** "breve pero completo" es contradictorio; "máximo 250 palabras" es medible; bullets definen exactamente qué incluir.

### Ejemplo 2 — Instrucción con paso faltante
**ANTES:**
> "1. Abre el archivo
> 2. Edita la sección de datos
> 3. Guarda"

**DESPUÉS:**
> "1. Abre el archivo (`archivo.xlsx`)
> 2. Ve a la pestaña 'Datos' (abajo a la izquierda)
> 3. Edita solo las celdas blancas — nunca las grises (protegidas)
> 4. Verifica que no haya errores (celdas rojas = error)
> 5. Guarda (`Ctrl+S`)"

**POR QUÉ:** Faltaban pasos clave; era no-ejecutable sin conocimiento previo.

---

## Lo que esta skill hace MEJOR que herramientas genéricas

1. **Propone el texto exacto mejorado** — no solo "esto está vago"
2. **Detecta el contexto** (skill BTQ vs. prompt genérico vs. código) — análisis específico
3. **Rápido Y profundo** — elige según urgencia
4. **Auto-aplicable** — puede hacer los cambios directamente
5. **Conversacional** — es un feedback loop, no un reporte de 100 líneas
6. **Sin jerga** — explica problemas en lenguaje simple

---

*Prompt Reviewer v1.0 · Mayo 2026 · Better than generic linters and AI evaluation tools*
