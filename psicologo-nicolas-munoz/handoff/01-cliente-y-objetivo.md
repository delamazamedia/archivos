# Cliente y objetivo del proyecto

Fuente: [Notion — Nicolás Muñoz Rojas](https://app.notion.com/p/38b376e13c5481618e01f49d5b868646)

## Cliente

- **Nombre:** Nicolás Muñoz Rojas
- **Profesión:** Psicólogo — Terapia humanista transpersonal
- **Sitio web:** https://nicolasmunozps.com/
- **Servicio principal:** Terapia psicológica online para adultos
- **Ubicación:** Concepción, Chile (atención 100% online, cobertura nacional)
- **Contacto:** info@nicolasmunozps.com · +56 9 2099 8387 (WhatsApp)

Las credenciales de las cuentas de correo del proyecto (pagos@, info@, contacto@,
web@, paula@nicolasmunozps.com) están guardadas en la página de Notion del
proyecto — no se replican aquí por seguridad.

## Objetivo del proyecto

Aumentar la captación de pacientes online mediante dos líneas de trabajo paralelas:

1. **Correcciones al sitio web actual** — problemas de conversión, UX y confianza
   que están frenando que los visitantes actuales agenden una sesión.
2. **Estrategia SEO Autopilot** — posicionar el sitio en Google para keywords
   relevantes de psicología online en Chile, usando el pipeline completo de skills.

## Pipeline SEO Autopilot — orden de ejecución

Cada skill depende del output de la anterior.

### Fase 1 — Brand DNA
- **Skill:** `brand-voice-capture`
- **Input:** URL del sitio (https://nicolasmunozps.com/)
- **Output:** JSON con el ADN de marca — tono, audiencia, voz, nicho, canales.
- **Propósito:** base de todo el pipeline. Sin Brand DNA, el contenido no suena como Nicolás.

### Fase 2 — Investigación de keywords
- **Skill:** `seo-keyword-research`
- **Input:** Brand DNA (output Fase 1)
- **Output:** lista priorizada de 30–50 keywords con intención, dificultad y territorio semántico.
- **Propósito:** identificar las búsquedas reales de pacientes potenciales en Chile.

### Fase 3 — Arquitectura de clusters
- **Skill:** `topic-cluster-architect`
- **Input:** lista de keywords (output Fase 2)
- **Output:** mapa de topic clusters con Pillar Pages, posts satélite y orden de publicación.
- **Propósito:** estructurar el contenido para prevenir canibalización y construir autoridad temática.

### Fase 4 — Redacción de artículos
- **Skill:** `seo-article-writer`
- **Input:** keyword específica + contexto del cluster + Brand DNA
- **Output:** artículo completo con H1/H2/H3, meta tags, FAQ con schema markup y CTA.
- **Propósito:** producir el contenido que posiciona, uno a la vez, en el orden de Fase 3.

### Fase 5 — Publicación en WordPress
- **Skill:** `wordpress-seo-publisher`
- **Input:** JSON del artículo (output Fase 4)
- **Output:** payload para la API REST de WordPress con campos Yoast/RankMath.
- **Propósito:** publicar el artículo directamente en el sitio sin reformateo manual.

## Decisiones estratégicas pendientes

Antes de ejecutar la Fase 2, resolver con el cliente (ver también
`02-pauta-reunion-tres-puntos.md`):

- **Cobertura geográfica:** ¿el sitio apunta solo a Concepción o se quiere
  posicionar para todo Chile? Actualmente el title dice "Psicólogo en Concepción"
  pero el servicio es 100% online. Afecta directamente la estrategia de keywords.
- **Precio de sesión:** ¿se publica en el sitio? Publicarlo aumenta conversión y
  filtra pacientes adecuados.
- **Primera sesión:** ¿tiene precio diferencial o es una consulta de evaluación
  gratuita?
- **Plug-in de agenda:** el sistema actual muestra muy pocos horarios disponibles.
  ¿se puede ampliar la disponibilidad o mostrar más slots?

## Notas técnicas

- El sitio corre en **WordPress** — todas las correcciones son implementables directamente.
- El diseño fue desarrollado por DelaMaza Media.
- El blog ya existe y tiene 6 artículos publicados — hay que auditarlos antes de
  producir contenido nuevo para evitar canibalización.
- Se detectó uso de **Site Kit by Google** — hay acceso a datos de Search Console y Analytics.
- El sistema de agenda usa un plugin externo embebido (aparentemente Calendly o similar).
