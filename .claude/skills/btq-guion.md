---
name: btq-guion
description: >
  Crea guiones completos para episodios de Behind the Queue (BTQ) en formato anchor points.
  Aplica automáticamente todas las reglas de tono, estructura y marca del podcast.
  Triggers: guión BTQ, script episodio BTQ, guion para grabar, crear episodio, nuevo guión,
  escribir guión BTQ, guión de Frieren, guión Bohemian Rhapsody, guión God of War,
  episodio nuevo, EP nuevo, quiero escribir el guión, ayúdame con el guión,
  estructura del episodio, Behind the Queue guión, guion podcast, episodio BTQ.
version: "1.0.0"
author: "Andrés Bermúdez Rodríguez (Andy)"
---

# BTQ Guion Creator — Guiones para Behind the Queue

Crea el guión completo de un episodio de BTQ en formato anchor points, aplicando
automáticamente todas las reglas de tono, estructura y marca. Andy habla; tú estructuras.

**Regla fundamental: el guión no reemplaza a Andy — le da los bloques. Andy decide cómo conectarlos.**

---

## Paso 1 — Confirmar los datos del episodio

Antes de escribir, reúne esta información. Si ya fue compartida en la conversación, úsala directamente sin preguntar de nuevo.

### Datos necesarios:

**Obligatorios:**
- Número de episodio (EP.0XX)
- Referencia cultural (personaje, canción, película, videojuego, anime)
- Título tentativo del episodio
- Tema central (el concepto de experiencia/liderazgo que se va a explorar)
- Frase de cierre del personaje (la que Andy usa en el TM final)

**Opcionales pero valiosos:**
- Fuentes de datos preferidas (si no se especifican, usar: Gallup, McKinsey, Deloitte, HBR, SHRM)
- Segmento variable deseado (Minuto de Acción, roles, consecuencias — ver Paso 3)
- Duración objetivo (si Andy tiene preferencia; de lo contrario ignorar)
- Notas específicas del episodio

### Si falta información:

Pregunta en UN solo bloque conversacional, no en interrogatorio:

> "Para armar el guión necesito confirmar algunos datos. ¿Me confirmas el número de episodio, la referencia cultural y la frase de cierre del personaje que vas a usar?"

Si el usuario dice "usa el roadmap" o menciona el personaje sin más datos, busca en el SKILL.md
del proyecto BTQ (sección 7 — Roadmap) y completa con lo que corresponde.

---

## Paso 2 — Diseñar la arquitectura del episodio

Antes de escribir el guión completo, presenta la arquitectura para validación:

```
EP.0XX — [TÍTULO]
Referencia cultural: [personaje/obra]
Tema central: [concepto]

ARQUITECTURA — 7 SEGMENTOS:

1. HOOK            — [idea del gancho sin revelar el personaje]
2. EL PUENTE       — [cómo se conecta con operaciones/experiencia]
3. LOS DATOS       — [fuentes y estadísticas a usar, máx. 1 por bloque]
4. MITO O REALIDAD — [3 rondas propuestas]
5. [SEGMENTO VAR.] — [Minuto de Acción / Roles / Consecuencias / lo que pida el episodio]
6. CIERRE          — [2 audiencias: líderes (3 preguntas) → agentes/supervisores]
7. TEASER          — [episodio siguiente] + TM de cierre
```

Pregunta si la arquitectura está bien o si quiere ajustar algo antes de desarrollar.

---

## Paso 3 — Escribir el guión en formato anchor points

### Reglas de formato anchor points (EP.008+):

Cada segmento tiene:
- **Titular de entrada** en negrita (lo que arranca el bloque)
- Bullets de datos/ideas cortos — Andy los desarrolla en su propia voz
- Recordatorios de anécdota como notas internas `[NOTA: Andy cuenta...]` — nunca el texto completo
- `[REMATE]` al cierre de cada bloque — entregado completo, sin suavizar
- Sin transiciones entre bloques — Andy decide cómo conectar

### Marcadores de script:

| Marcador | Duración | Uso |
|---|---|---|
| `[PAUSA]` | 1–2 seg | Después de una idea importante |
| `[PAUSA LARGA]` | 3–4 seg | Que la idea aterrice |
| `[REMATE]` | — | Última línea de cada bloque, dicha completa |
| `[NOTA]` | — | Recordatorio para Andy, no se lee en voz alta |
| *cursiva* | — | Énfasis de voz |

### Reglas de datos:

- **Máximo 1 dato por bloque** — rodeado de narrativa de Andy, nunca aislado
- **Nunca estadísticas consecutivas** — si hay 2 datos, van en bloques distintos
- Fuentes preferidas: Gallup, McKinsey, Deloitte, HBR, SHRM, Gartner, PwC, WEF, Frost & Sullivan, IDC LATAM, DANE/MinTIC Colombia

### Reglas de contenido:

- Anécdotas SIEMPRE en tercera persona: "conozco un caso", "conocí una operación" — nunca con nombres identificables
- Andy nunca es referenciado en segunda persona dentro de su propio guión
- No back-to-back statistics — punto duro

---

## Paso 4 — Aplicar el Tone Master (TM) — Inmovible

### Apertura — SIEMPRE completa, NUNCA abreviada:

```
Buenas y santas. Feliz día, feliz tarde o feliz noche.
Cualquier día que éste sea.
```

### Intro del episodio — siempre con número:

```
Esto es Behind the Queue, episodio [NÚMERO].
```

### Cierre (EP.010+):

```
Esto fue Behind the Queue, episodio [NÚMERO].
Yo soy Andy. Y como diría [personaje]: [frase del personaje].
```

### Prohibiciones absolutas — cero excepciones:

- ❌ "obviamente"
- ❌ "pero bueno"
- ❌ Cualquier muletilla de relleno
- ❌ Andy referenciado en segunda persona en su propio guión
- ❌ Anécdotas con nombres identificables

Si al revisar el guión aparece alguna de estas palabras, elimínala antes de entregar.

### Velocidad de grabación:

Andy graba más rápido de lo que los marcadores de tiempo asumen. La energía y
fluidez tienen prioridad sobre el tiempo objetivo. Ignorar duraciones si se piden.

---

## Paso 5 — Estructura detallada de los 7 segmentos

### Segmento 1 — Hook

- Gancho emocional SIN revelar el personaje todavía
- Pregunta o situación que conecta con la experiencia del oyente
- Revelar el nombre del personaje/obra
- "Para el que no lo conoce..." → 3–4 líneas accesibles sobre la serie/juego/película
- `[REMATE]` de apertura

### Segmento 2 — El Puente

- Transición natural de la referencia cultural a la operación real
- Identifica el paralelo central (qué tiene en común el personaje con un líder/supervisor/agente)
- `[REMATE]` que ancle la conexión

### Segmento 3 — Los Datos

- Evidencia con fuentes (1 dato por bloque, máximo)
- Andy desarrolla la implicación del dato en sus propias palabras
- `[NOTA]` para recordar de dónde viene cada fuente
- Si hay más de 1 dato importante, distribuirlos en sub-bloques separados
- `[REMATE]` que conecte el dato con la realidad operativa

### Segmento 4 — Mito o Realidad

Mínimo 3 rondas. Formato:

```
MITO: "[Afirmación común que se va a cuestionar]"
[PAUSA]
[Andy responde con evidencia o perspectiva]
[REMATE]: La realidad es [afirmación directa].
```

Diseñar mitos relevantes al tema del episodio, no genéricos.

### Segmento 5 — Segmento Variable

Elige según lo que necesita el episodio (preguntar si no se especifica):

**Minuto de Acción** — 1 cosa concreta que el oyente puede hacer esta semana:
```
[NOTA: Andy presenta como reto específico y alcanzable]
[REMATE]: No mañana. Esta semana.
```

**Roles** — cómo aplica el tema según el rol del oyente:
```
Si eres agente: [comportamiento específico]
Si eres supervisor: [acción específica]
Si eres líder: [decisión específica]
```

**Consecuencias** — qué pasa si no se aplica lo del episodio:
```
[Consecuencia a corto plazo]
[Consecuencia a largo plazo]
[REMATE] duro
```

### Segmento 6 — Cierre

Siempre dos audiencias, en este orden:

**Líderes primero — 3 preguntas de reflexión:**
```
¿Cuándo fue la última vez que [acción relacionada al tema]?
¿Qué pasaría si [situación del episodio] ocurriera en tu operación mañana?
¿Qué va a cambiar en tu equipo después de escuchar esto?
[PAUSA LARGA]
```

**Agentes y supervisores — mensaje directo:**
```
[Validación del rol] + [acción concreta que está en sus manos]
[REMATE] de cierre
```

### Segmento 7 — Teaser + TM final

```
[NOTA: Andy menciona el próximo episodio con intriga, sin spoilear]
[Frase que conecta el episodio actual con el siguiente]

[PAUSA LARGA]

Esto fue Behind the Queue, episodio [NÚMERO].
Yo soy Andy. Y como diría [personaje]: [frase de cierre del personaje].
```

---

## Paso 6 — Revisión de calidad antes de entregar

Antes de presentar el guión final, verifica:

**Checklist TM:**
- [ ] Apertura completa y sin abreviaciones
- [ ] Número de episodio en intro Y en cierre
- [ ] Sin "obviamente" ni "pero bueno" en ningún punto
- [ ] Ningún [REMATE] suavizado con "generalmente" o "en muchos casos"
- [ ] Andy no es referenciado en segunda persona
- [ ] Ninguna anécdota con nombre identificable
- [ ] Máximo 1 dato por bloque, sin estadísticas consecutivas
- [ ] Segmento 4 tiene mínimo 3 rondas de Mito o Realidad
- [ ] Cierre tiene los 2 públicos en orden correcto (líderes primero)
- [ ] Frase TM del personaje en el cierre

**Checklist de formato:**
- [ ] Cada segmento tiene titular en negrita
- [ ] `[REMATE]` al final de cada bloque
- [ ] `[NOTA]` para anécdotas y recordatorios (no texto completo)
- [ ] `[PAUSA]` y `[PAUSA LARGA]` en los momentos correctos
- [ ] *cursiva* en palabras con énfasis de voz

---

## Paso 7 — Entrega

### Formato de entrega:

Presenta el guión completo en texto con toda la marca tipográfica de BTQ
(negritas, marcadores, notas). Indica claramente:

```
GUIÓN LISTO — EP.0XX — [TÍTULO]

Segmentos: 7
Referencia cultural: [obra]
Fuentes incluidas: [lista]
Frase TM: "[frase del personaje]"

[GUIÓN COMPLETO]

---
PENDIENTES DE COMPLETAR:
- [cualquier sección que necesite input de Andy]
- [datos que Andy debe validar antes de grabar]
```

### Si Andy pide el .docx:

Indica los colores BTQ para aplicar manualmente en Word/Google Docs:
- Fondo de headers: `#0A0A0A` (void black)
- Texto de REMATE: `#C9A84C` (signal gold)
- Cuerpo: `#F5F2EC` (off-white)
- Marcadores [NOTA]: gris claro, texto más pequeño

### Al finalizar:

Pregunta:
- ¿Quieres ajustar algún bloque?
- ¿Arrancamos con los prompts de artwork para este episodio?
- ¿Generamos el plan de lanzamiento en redes (3 días)?

---

## Referencia rápida — Roadmap BTQ Season 2

Si Andy menciona el número de episodio sin más datos, busca aquí:

| EP | Referencia | Tema central | Frase de cierre |
|---|---|---|---|
| 10 | God of War | Kratos: el líder que dejó de gritar para empezar a enseñar | "No te disculpes. Sé mejor." |
| 11 | Frieren | El costo de no valorar a tu equipo hasta que ya no está | "Lo que tienes hoy no va a estar para siempre. Valóralo antes de que se convierta en recuerdo." |
| 12 | Bohemian Rhapsody | La experiencia que nadie pidió y todos recordaron para siempre | "Las reglas son útiles. Romperlas con propósito es arte." |
| 13 | Back to the Future | Doc Brown y Marty: la sociedad mentor-aprendiz que cambió el futuro | "Tu futuro todavía no está escrito. Hazlo uno bueno." |
| 14 | Maomao (Boticaria) | Cuando la persona más competente es la que menos poder tiene | "No necesitas el título más alto para ser la persona más valiosa del cuarto." |
| 15 | Metal Gear | Solid Snake: cuando descubres que todo lo que te dijeron era mentira | "Piensa por ti mismo. Decide por ti mismo." |
| 16 | Pink Floyd | The Wall: anatomía del líder que se aisló hasta destruirse | "Cada muro que construyes para protegerte es el mismo muro que te aísla." |
| 17 | Dragon Ball | Vegeta: por qué el ego que aprende es más peligroso que el talento natural | "El orgullo que te empuja a mejorar te salva. El que te impide escuchar te destruye." |
| 18 | Ghostbusters | El equipo de misfits que construyó un negocio donde nadie creía | "Si nadie más va a hacerlo, hazlo tú." |
| 19 | The Last of Us | Joel: la decisión egoísta que todo líder entiende | "A veces la decisión correcta y la decisión egoísta son la misma." |
| 20 | Saint Seiya | Shiryu: el que siempre estuvo dispuesto a perder los ojos por su equipo | "Hay batallas que solo se ganan cuando estás dispuesto a perderlo todo." |
| 21 | The Matrix | Neo: la decisión que separa al que ve del que prefiere no saber | "La pastilla roja no te da respuestas. Te da la responsabilidad de buscarlas." |

---

*BTQ Guion Skill v1.0 · Mayo 2026 · Laswell configuration*
