# Instrucciones de uso — Smart Recruiter

## ¿Qué es esto?
Un entrevistador de IA que evalúa candidatos y genera reportes en PDF automáticamente.
Funciona en español, inglés, o detecta el idioma del candidato automáticamente.

---

## Primer uso — Configuración inicial

Antes de empezar a recibir candidatos, configura el sistema en el panel admin.

### 1. Accede al panel admin
```
/admin/
```
Contraseña por defecto: `KumaAdmin2026` (cámbiala inmediatamente en Settings)

### 2. Configura en Settings → API Key
1. Ve a **Settings**
2. Ingresa tu **Anthropic API Key** (consíguelo en console.anthropic.com)
3. Configura los datos SMTP para el envío de emails
4. Ingresa el email del reclutador (donde llegan los reportes)
5. Asegúrate que **DEV mode esté desactivado** para producción
6. Guarda

### 3. Crea los jobs en el panel admin
1. Ve a **Jobs → New Job**
2. Completa los campos:
   - **Código** — identificador único (ej. KT-001). Los candidatos lo usan para acceder.
   - **Título** — nombre del cargo
   - **Descripción** — pega el job description completo (más detalle = mejor entrevista)
   - **Nivel** — ajusta el tono de la conversación:
     - `Agent` → tono conversacional y cercano (agentes, BPO, frontline)
     - `Professional` → tono profesional estándar (managers, coordinadores)
     - `Executive` → tono formal peer-to-peer (VPs, directores, C-level)
   - **Evaluación de inglés** — si el cargo es bilingüe, el AI evalúa el nivel real del candidato cambiando a inglés durante la entrevista (sin avisarle)
   - **Activo** — controla si el código puede ser usado por candidatos

---

## Para el candidato

Comparte este link con el candidato:
```
https://tu-dominio.com/?code=KT-001
```
(El código se pre-llena automáticamente)

1. El candidato ingresa su nombre y el código del cargo
2. La entrevista empieza automáticamente — es una conversación de 20-30 minutos
3. Al terminar, se genera el reporte PDF y se envía por email al reclutador

> No requiere cuenta, instalación ni preparación especial. Solo un navegador.

---

## Panel de administración (`/admin/`)

| Sección | Qué puedes hacer |
|---|---|
| **Jobs** | Crear, editar, activar/desactivar, eliminar cargos |
| **Settings → API** | Configurar la API Key de Anthropic |
| **Settings → Email** | Configurar SMTP, email del reclutador, sender name |
| **Settings → App** | Nombre de la empresa, modo DEV/producción |
| **Settings → Admin** | Cambiar la contraseña del panel |

---

## Qué genera al final de cada entrevista

### Reporte PDF
Enviado automáticamente al email del reclutador con:
- Veredicto y score general (0–10)
- Tabla de dimensiones con evidencia
- Gráfico de barras por dimensión
- Fortalezas clave
- Gaps y preocupaciones
- Citas textuales del candidato
- Recomendación narrativa para el equipo
- Evaluación de inglés (si aplica) — nivel detectado + análisis

### Veredictos posibles
| Veredicto | Score | Significado |
|---|---|---|
| **Strong Fit** | 8–10 | Avanzar con prioridad |
| **Possible Fit** | 5–7.9 | Avanzar con reservas |
| **Not a Fit** | 0–4.9 | No recomendar |

---

## Notas importantes
- El candidato **nunca ve** los criterios de evaluación ni el job description
- El código del cargo es lo único que el candidato ingresa — la descripción del cargo es invisible
- Si se activa la evaluación de inglés, el AI cambia de idioma naturalmente durante la entrevista sin avisar que está evaluando
- Los reportes PDF quedan guardados en `/reports/` en el servidor
- El sistema siempre funciona en producción — no hay modo demo

---

## Requisitos de servidor
- PHP 8.1+
- Composer (para Dompdf y PHPMailer)
- Cuenta Anthropic (API Key)
- SMTP disponible (Hostinger, Gmail, SendGrid, etc.)
