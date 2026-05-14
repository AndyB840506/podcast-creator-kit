# Cómo usar el AI Lead Generator

## Requisitos

1. **Visual Studio Code** instalado — https://code.visualstudio.com
2. **Extensión Claude Code** instalada desde el marketplace de VS Code
3. Claude Code con **WebSearch habilitado** (herramienta de búsqueda web)

## Paso 1 — Abrir el kit

1. Abre VS Code
2. Archivo → Abrir carpeta → selecciona `kit-ai-lead-generator`
3. Abre Claude Code desde el panel lateral

## Paso 2 — Iniciar la búsqueda

Escribe cualquier cosa para comenzar. Por ejemplo:

> "Quiero leads de agencias de publicidad en México"

> "Busca prospectos de clínicas dentales en España que puedan necesitar software de gestión"

> "Dame 30 leads de restaurantes en Colombia para ofrecerles servicio de delivery"

Claude te hará 2-3 preguntas rápidas y comenzará la búsqueda automáticamente.

## Paso 3 — Recibir el reporte

Cuando termina, obtienes dos archivos dentro de la carpeta `leads/`:

| Archivo | Para qué sirve |
|---------|---------------|
| `leads/leads-[nicho]-[fecha].html` | Reporte visual con filtros. Ábrelo en el navegador y usa el botón "Descargar PDF" |
| `leads/leads-[nicho]-[fecha].csv` | Importar a HubSpot, Notion, Airtable, Salesforce u otro CRM |

Todos los reportes que generes se acumulan en `leads/` — cada búsqueda crea un nuevo archivo con su fecha, sin sobreescribir los anteriores.

## Paso 4 — Descargar como PDF

1. Abre el archivo `.html` en Chrome o Edge
2. Haz clic en el botón **"Descargar PDF"** en el dashboard
3. En el diálogo de impresión: selecciona "Guardar como PDF", activa "Gráficos de fondo"
4. Los colores se mantienen exactamente igual que en pantalla

## Cómo se puntúan los leads

Cada lead recibe un score de 0-100 basado en:

| Criterio | Puntos |
|----------|--------|
| Website activo y funcional | 15 |
| LinkedIn con actividad reciente (< 60 días) | 15 |
| Email o contacto directo encontrado | 15 |
| Actividad en redes sociales < 30 días | 10 |
| Empresa con más de 10 empleados | 10 |
| Decision-maker identificado | 15 |
| Buy signal detectado | 20 |

## Categorías

| Categoría | Score | Qué significa |
|-----------|-------|---------------|
| 🥇 PREMIUM | 76–100 | Empresa establecida, contacto clave visible, señal de compra activa. Contactar primero. |
| 🔥 HOT | 51–75 | Señales de necesidad detectadas. Alta probabilidad de respuesta. |
| 🌡️ WARM | 26–50 | Presencia activa, sin señal directa. Requiere nurturing. |
| ❄️ COLD | 0–25 | Solo existe digitalmente. Prospecto a largo plazo. |

## Estructura del kit

```
kit-ai-lead-generator/
├── CLAUDE.md                          ← Claude lo lee automáticamente
├── INSTRUCCIONES.md                   ← Este archivo
├── lead-generator.config.json         ← Se crea en el primer uso
├── leads/                             ← Todos tus reportes aquí
│   ├── leads-[nicho]-[fecha].html
│   └── leads-[nicho]-[fecha].csv
└── .claude/
    └── skills/
        └── ai-lead-generator.md       ← La skill
```

## Cambiar el modo de búsqueda

La skill usa tres modos configurables. El modo activo se guarda en `lead-generator.config.json` (se crea automáticamente la primera vez).

| Modo | Cuándo usarlo | Costo mensual |
|------|--------------|---------------|
| `native` | Empezar, volumen bajo, validar | $0 |
| `serper` | Uso regular, resultados más estructurados | ~$50 (50K búsquedas) |
| `hybrid` | Máxima calidad, emails verificados incluidos | ~$99 (Serper + Hunter) |

**Para cambiar de modo**, escribe en Claude Code:

> "cambiar modo de búsqueda"

Claude te mostrará el menú y actualizará el config automáticamente.

**Para configurar manualmente**, edita `lead-generator.config.json`:

```json
{
  "search_mode": "serper",
  "serper_api_key": "tu-key-aqui",
  "hunter_api_key": "",
  "hunter_enrichment": false
}
```

**Dónde obtener las API keys:**
- Serper.dev: https://serper.dev → Sign up → Dashboard → API Key
- Hunter.io: https://hunter.io → Sign up → Dashboard → API Key

**Importante:** No subas `lead-generator.config.json` a repositorios públicos — contiene tus API keys.

---

## Preguntas frecuentes

**¿Puedo buscar en un idioma diferente?**
Sí, al iniciar indica el idioma de los leads que buscas.

**¿Los datos son en tiempo real?**
Sí, la búsqueda se ejecuta en el momento. Los datos provienen de lo que es públicamente accesible en ese instante.

**¿Puedo pedir más de 40 leads?**
Sí, aunque a partir de 50 la búsqueda puede tardar más. Se recomienda dividir en búsquedas por región o subsector.

**¿Funciona para cualquier nicho?**
Sí, funciona para cualquier tipo de empresa B2B o B2C. Los resultados son mejores en nichos con fuerte presencia digital.
