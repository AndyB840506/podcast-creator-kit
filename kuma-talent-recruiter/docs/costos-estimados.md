# HireSignal by Kuma Talent — Análisis de Costos Estimados
*Mayo 2026 — Para revisión con socio*

> ⚠️ Los costos de API son estimaciones preliminares. Se validarán con datos reales tras las pruebas de producción en Laragon.

---

## 1. Hosting — Hostinger

| Plan | Uso recomendado | Precio/mes |
|------|----------------|-----------|
| Business Web Hosting | MVP / pruebas piloto | ~$7.99/mes |
| Cloud Startup | Producción inicial ✅ | ~$7.99/mes |
| Cloud Enterprise | Escala multi-cliente | ~$29.99/mes |
| VPS KVM (entrada) | Control total | desde $2.99/mes |

**Recomendación de arranque:** Cloud Startup — soporta sesiones PHP concurrentes para entrevistas en tiempo real.

---

## 2. Claude API — Anthropic

| Modelo | Uso | Input / 1M tokens | Output / 1M tokens |
|--------|-----|-------------------|--------------------|
| Claude Opus 4.7 | Entrevistas adaptativas | $5.00 | $25.00 |
| Claude Sonnet 4.6 | Reportes PDF + JD Parser | $3.00 | $15.00 |

### Estimación por candidato
- Entrevista completa (Opus 4.7): **~$0.39 USD**
- Reporte (Sonnet 4.6): **~$0.07 USD**
- **Total por candidato: ~$0.46 USD**
- Con Prompt Caching activado: **~$0.15–0.20 USD**

### Proyección por volumen mensual
| Volumen | Costo API entrevistas | Costo API reportes | Total API |
|---------|----------------------|-------------------|-----------|
| 50 entrevistas | ~$19.50 | ~$3.50 | ~$23/mes |
| 200 entrevistas | ~$78.00 | ~$14.00 | ~$92/mes |
| 500 entrevistas | ~$195.00 | ~$35.00 | ~$230/mes |

---

## 3. Lead Generator — Claude API

| Modelo | Costo por run | 100 runs/mes | 500 runs/mes |
|--------|--------------|-------------|-------------|
| Sonnet 4.6 | ~$0.05–0.10 | ~$7/mes | ~$35/mes |

---

## 4. Herramientas y Licencias

| Herramienta | Plan | Precio |
|------------|------|--------|
| Canva | Pro anual (1 usuario) | $10/mes ($119.99/año) |
| Gamma | Pro anual | $15/mes |
| Anthropic API | Pago por uso | Sin mensualidad fija |
| Hostinger | Cloud Startup | $7.99/mes |

---

## 5. Resumen Total Mensual

| Concepto | Arranque (50 entrev/mes) | Crecimiento (200 entrev/mes) |
|----------|--------------------------|------------------------------|
| Hostinger Cloud Startup | $7.99 | $7.99 |
| Claude API (entrevistas + reportes) | ~$23 | ~$92 |
| Lead Generator API | ~$7 | ~$20 |
| Canva Pro | $10 | $10 |
| Gamma Pro | $15 | $15 |
| **TOTAL** | **~$63/mes** | **~$145/mes** |

---

## Puntos clave para el socio

1. **Costo por candidato ~$0.46 USD** — con cualquier precio de venta razonable el margen es muy alto
2. **Prompt Caching puede reducir API hasta 60–70%** — activar desde el inicio
3. **Sin mensualidades de licencia de software** — solo infraestructura y uso real de API
4. **Escalabilidad directa** — el costo sube solo cuando hay más uso real del sistema
5. **Modelo de venta por implementación** — el cliente paga una vez, soporte gratuito 2 meses

---

> ⚠️ **Pendiente:** Validar tokens reales por entrevista tras pruebas en Laragon (mañana).
> Los números de API se actualizarán con datos reales de producción.
