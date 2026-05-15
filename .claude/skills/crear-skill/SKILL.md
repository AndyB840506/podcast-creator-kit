---
name: crear-skill
description: "Asiste al usuario para crear sus propias skills de Claude Code personalizadas en español o inglés, automatizar flujos de trabajo, crear comandos o convertir procesos manuales en automáticos. Triggers: 'crea una skill', 'quiero una skill', 'skill personalizada', 'automatizar esto como skill', 'crear comando Claude Code', 'convertir en skill', 'hacer esto automático', 'create a skill', 'I want a skill', 'custom skill', 'automate this as a skill'."
---

# Crear Skill — Crea tus propias skills

Le describes un proceso que quieres automatizar y Claude genera una skill completa lista para usar en español o inglés. Es la herramienta que crea herramientas.

Las skills de Claude Code son archivos `.md` que le enseñan a Claude a hacer tareas específicas. Cualquier proceso que hagas de forma repetitiva puede convertirse en una skill. Puedes crear skills en tu idioma preferido.

---

## Paso 1 — Entender qué necesita el usuario

Pregunta de forma conversacional:

- **¿En qué idioma quieres la skill?** — Español o English
- **¿Qué quieres que haga Claude automáticamente?** — describe el resultado que esperas
- **¿Qué información necesita recibir?** — URL, texto, carpeta, datos, archivo...
- **¿Qué debe generar?** — HTML, informe, archivo, código, dashboard...
- **¿Lo vas a usar tú o se lo vas a dar a otras personas?**

Si el usuario ya describió suficiente (ej: "una skill que lea un CSV de productos y genere fichas de producto en HTML"), diseña directamente.

Si no sabe qué skill crear, proponle ideas:

**Para negocios:**
- Generador de propuestas comerciales (datos del cliente → propuesta PDF/HTML profesional)
- Calculadora de presupuestos (servicio + horas → presupuesto detallado)
- Generador de contratos (datos → contrato personalizado)
- Creador de presentaciones de ventas (producto → slides HTML)
- Onboarding de clientes (datos → carpeta + emails + documentos)

**Para marketing:**
- Generador de copy para ads (producto + público → variantes de anuncios)
- Planificador de contenido (nicho → calendario de 30 días con ideas)
- Creador de emails de venta (producto → secuencia de emails)
- Generador de posts para redes (tema → posts para IG, LinkedIn, X)

**Para desarrollo:**
- Generador de APIs (modelo de datos → API completa)
- Documentador de código (repositorio → documentación)
- Generador de tests (código → suite de tests)
- Scaffolding de proyectos (tipo de proyecto → estructura completa)

**Para productividad:**
- Resumidor de documentos (PDF → resumen ejecutivo)
- Transcriptor de reuniones (notas → acta formal)
- Generador de SOPs (proceso → documento de procedimiento paso a paso)
- Analizador de datos (CSV → dashboard con insights)

---

## Paso 2 — Diseñar la skill

Antes de escribir, planifica la estructura:

1. **Input** — qué recibe la skill (qué pregunta al usuario)
2. **Proceso** — qué pasos sigue (en orden)
3. **Herramientas** — qué necesita usar (WebFetch, Bash, Playwright, Read, Write, herramientas nativas de Claude Code)
4. **Output** — qué genera y en qué formato
5. **Experiencia de usuario** — cómo se siente usarla (mensajes amigables, flujo conversacional)

### Principios de diseño de skills (aprendidos de nuestras 9 skills anteriores)

Estos principios son los que hacen que una skill sea realmente buena:

**PRIMERO: Expectativas realistas es honestidad**

No prometas velocidad si la tarea es lenta por naturaleza. No ocultes la curva de aprendizaje — prepárala.

**Ejemplo:**
- ❌ "Genera un podcast en 2 horas"
- ✅ "Primer episodio: 8-15 horas (curva de aprendizaje). Luego: 5-8 horas. Experiencia: 3-5 horas"

El usuario que sabe qué esperar no abandona en la hora 3. Es mejor ser honesto upfront que tener un usuario decepcionado después.

---

Estos principios son los que hacen que una skill sea realmente buena:

**1. No inventes datos** — si la skill necesita información del usuario (servicios, precios, contacto, testimonios), pregúntala. Nunca la inventes. Si algo no está disponible, usa placeholders visibles o omite la sección.

**2. Datos reales primero, preguntas después** — si la skill puede obtener datos automáticamente (scraping, WebFetch, WebSearch), hazlo primero. Solo pregunta lo que no puedes encontrar solo.

**3. Auto-instalación de dependencias** — si necesita Playwright, npm packages o cualquier herramienta, la skill debe instalarlas automáticamente. Avisa al usuario con un mensaje amigable ("Estoy preparando las herramientas, tarda 30 segundos la primera vez").

**4. Libertad creativa en diseño** — si genera HTML/dashboards, no dictes CSS rígido. Describe el resultado visual deseado y deja que Claude diseñe libremente. Esto produce resultados más bonitos y únicos.

**5. Adaptación al contexto** — si la skill sirve para diferentes sectores/tipos (como la de web que se adapta a restaurante vs gimnasio), incluye una guía de adaptación.

**6. Flujo conversacional** — la skill debe funcionar como una conversación natural, no como un formulario. Agrupa preguntas en 2-3 bloques, no hagas interrogatorios largos.

**7. Fallbacks amigables** — si algo falla (scraping, instalación, etc.), no te bloquees. Ofrece alternativa y sigue adelante.

**8. Mensaje de bienvenida** — si la skill va en un kit independiente con CLAUDE.md, incluye un mensaje de bienvenida que se active con cualquier input del usuario.

**9. Sin precios sugeridos** — no incluir "como servicio" ni precios al final del output.

**10. Presentar resultado claro** — al terminar, mostrar qué se generó, qué datos se usaron, qué falta por completar, y preguntar si quiere ajustar algo.

**11. Modelos fijos, solo API key configurable** — los proyectos que usan IA no deben exponer la elección del modelo al usuario final. Fijar siempre el mejor modelo disponible para cada tarea (ej: Sonnet para chat, Opus para reportes/análisis pesados). Lo único configurable en el admin debe ser la API key. Esto evita errores de configuración y garantiza calidad consistente.

**12. Tono conversacional progresivo** — si la skill genera un agente conversacional (entrevistador, asistente, formulario interactivo), el tono debe empezar cálido y empático. Solo debe volverse más directo o exigente si detecta patrones que lo justifican (respuestas evasivas, inconsistencias, etc.). Nunca empieza frío o cortante.

**13. Nombre humano para agentes** — si la skill crea un agente conversacional, darle un nombre humano rotativo por sesión. Humaniza la interacción y hace que parezca que hay un equipo detrás.

**14. En kits multi-workflow, presentar extras propuestos explícitamente** — si el diseño incluye componentes que el usuario no mencionó (ej. archivo de perfil persistente, intro/outro templates, reusabilidad de prompts), listarlos con justificación breve y pedir confirmación antes de incluirlos en el plan. Evita scope creep silencioso.

### Validation checkpoints en Paso 2

Después de diseñar la estructura, identifica 2-3 puntos críticos donde el usuario DEBE validar antes de que sigas adelante:

**Orden de corrección:**
1. **Scope primero** — ¿Qué entra y qué no? ¿Cuál es el límite de la skill?
2. **Flujo segundo** — ¿Cuáles son los pasos? ¿En qué orden?
3. **Herramientas tercero** — ¿Qué genera exactamente? ¿En qué formato?

**Meta:** Si el usuario te corrige en 3+ puntos después de este paso, el diseño fue incompleto. La validación temprana ahorra rework.

### Anti-patterns a evitar en proyectos PHP/backend

**Estados de fin de flujo:** Cuando un flujo llega a su límite (ej: máximo de mensajes en entrevista), devolver siempre un campo semántico claro (`interview_done: true`, `flow_complete: true`). Nunca devolver `error` — el frontend lo interpreta como fallo recuperable y entra en loop infinito.

**Dependencias de Composer:** Siempre verificar que `vendor/` existe antes de asumir que las dependencias están instaladas. Si no existe, correr `composer install` primero. Las librerías como `dompdf` (PDF) o `phpmailer` no vienen incluidas en el repo.

**Fuzzy matching de códigos:** Para campos donde el usuario escribe un código (job code, ID, referencia), usar `levenshtein()` de PHP con tolerancia ≤ 1 para absorber typos. Normalizar primero con `strtoupper(preg_replace('/[^A-Z0-9]/i', '', $code))` para ignorar guiones, espacios y mayúsculas/minúsculas antes de comparar.

### Comportamiento conocido de herramientas externas (Canva)

Si la skill usa Canva MCP, aplicar siempre estas reglas:

- **Canva siempre genera 4 candidatos** — no se puede controlar el número. Reconocerlo al usuario y pedirle que elija uno antes de guardar.
- **Las URLs de thumbnail no se renderizan en el chat** — siempre compartir el link directo de edición `/d/<id>` para que el usuario lo abra en el navegador.
- **Siempre incluir Brand Kit de Kuma Talent** en cada llamada a `generate-design`, `generate-design-structured` o `request-outline-review`: `brand_kit_id: "kAG_6mWtSVg"`, `brand_kit_name: "Kuma Talent"`.

---

## Paso 3 — Escribir la skill

Genera el archivo `.md` con esta estructura:

```markdown
---
name: nombre-en-kebab-case
description: "Descripción completa de qué hace y cuándo activarse. Incluir múltiples frases trigger variadas. Ser específico pero cubrir sinónimos y formas diferentes de pedir lo mismo."
---

# Nombre de la Skill

Una línea describiendo qué hace en lenguaje simple.

**Regla fundamental: [la regla más importante de esta skill]**

---

## Paso 1 — [Recoger información / Entender qué necesita]

[Flujo conversacional para obtener los datos necesarios]
[Qué intentar obtener automáticamente primero]
[Qué preguntar si falta]

---

## Paso 2 — [Procesar / Analizar / Investigar]

[La lógica principal de la skill]
[Qué herramientas usar y cómo]
[Adaptación según contexto si aplica]

---

## Paso 3 — [Generar el resultado]

[Qué formato tiene el output]
[Estructura del contenido]
[Libertad creativa en diseño si es HTML]

---

## Paso 4 — [Guardar y presentar]

[Cómo nombrar el archivo]
[Abrirlo automáticamente si es HTML]
[Resumen de lo generado]
[Preguntar si quiere ajustar]
```

### Reglas del archivo generado

**Frontmatter:**
- `name` en kebab-case, sin espacios ni mayúsculas
- `description` con al menos 5-8 frases trigger diferentes
- La description debe cubrir sinónimos y variaciones

**Instrucciones:**
- Escritas en imperativo (haz, pregunta, genera)
- Auto-suficientes — funcionar sin que el usuario sepa programar
- Si necesita dependencias, incluir comando exacto de instalación
- Si necesita APIs, explicar cómo obtener la key

**Herramientas:**
- Preferir herramientas nativas de Claude Code (Read, Write, WebFetch, WebSearch, Bash)
- Evitar dependencias externas cuando sea posible
- Si necesita Python/Node, que sea lo mínimo y con auto-instalación

---

## Paso 3.5 — Plan de distribución

Antes de instalar y compartir, responde estas preguntas (afectan la estructura final):

1. **¿Dónde vive esta skill?**
   - En repo del usuario (privado)
   - En repo GitHub público
   - En skills.sh (marketplace)
   - En multiple locations

2. **¿Quién la descubre?**
   - Solo el usuario la usa (instalación manual)
   - Equipo interno (shared en Slack/Wiki)
   - Público en GitHub
   - Listada en skills.sh

3. **¿Cómo se instala?**
   - `git clone` + copiar archivo
   - `npx skills add owner/repo`
   - Copiar/pegar archivo `.md`
   - Download desde skills.sh

**Estas decisiones afectan:**
- Necesidad de README
- Metadatos en frontmatter (para skills.sh)
- Estructura de carpetas (kit vs standalone)
- Instrucciones de instalación

**Recomendación:** Si es público o para compartir, crea un kit completo (Paso 5) con README + INSTRUCCIONES.

---

## Paso 4 — Instalar la skill

Después de generarla, instálala automáticamente:

```bash
mkdir -p .claude/skills
cp [nombre-skill].md .claude/skills/
```

Si el usuario quiere que la skill esté disponible en todos sus proyectos (no solo en esta carpeta):

```bash
mkdir -p ~/.claude/skills
cp [nombre-skill].md ~/.claude/skills/
```

---

## Paso 5 — Crear el kit (si el usuario quiere compartirla)

Si la skill va a ser usada por otras personas, genera un kit completo:

```
kit-[nombre]/
├── CLAUDE.md                    ← Mensaje de bienvenida + qué hace
├── INSTRUCCIONES.md             ← Guía paso a paso para instalar y usar
├── .claude/
│   └── skills/
│       └── [nombre].md          ← La skill
└── [carpetas extra si necesita] ← assets/, facturas/, etc.
```

**CLAUDE.md** debe incluir:
- Sección "Comportamiento al iniciar" con mensaje de bienvenida
- Qué hace la skill
- Qué necesita del usuario
- Que no necesita nada instalado (si es el caso)

**INSTRUCCIONES.md** debe incluir:
- Requisitos (Claude Code + lo que necesite)
- Pasos numerados desde abrir la carpeta hasta ver el resultado
- Estructura de archivos

---

## Paso 6 — Testear

Después de instalar:

1. Simula que eres un usuario nuevo y escribe una frase que debería activar la skill
2. Verifica que las instrucciones son claras y completas
3. Si genera archivos, verifica que funcionan
4. Ajusta si algo no fluye bien

---

## Paso 7 — Presentar al usuario

Muestra:
1. Nombre y ruta del archivo generado
2. Frases que la activan
3. Qué input necesita y qué output genera
4. Si se creó kit, listar los archivos del kit
5. Instrucciones para usarla
6. Pregunta si quiere ajustar algo

No muestres precios sugeridos ni consejos de venta.

---

## Reference & Best Practices

For detailed guidance on skill structure, patterns, and advanced topics, see **[docs/skill-best-practices.md](docs/skill-best-practices.md)**. Topics include:

- Self-contained skill structure
- Progressive disclosure pattern
- Workflow organization
- Naming conventions
- CLI tools (Python)
- Skill collision analysis
- And more...
