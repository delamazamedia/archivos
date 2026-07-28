# Tareas del proyecto

Espejo de la base de datos [Notion — Tareas del Proyecto](https://app.notion.com/p/74bed74515c94873ac4040a774e45533)
al 2026-07-28. El tablero de Notion es la fuente de verdad para el estado en
vivo de cada tarea; actualizar ahí, no aquí.

## Fase 0 — Correcciones Web

| Tarea | Categoría | Prioridad | Estado | Notas |
|---|---|---|---|---|
| Publicar el valor de la sesión en la página de agenda | Mejora Web | Alta | Sin empezar | El precio no está publicado en ninguna parte del sitio. Definir con el cliente si se publica valor exacto o rango. Colocarlo visible en /agenda-tu-consulta-en-linea/ antes del widget de reserva. |
| Ampliar disponibilidad horaria visible en la agenda | Mejora Web | Alta | Sin empezar | El sistema muestra solo 2 horarios (19:00 y 20:00, mar-jue). Coordinar con Nicolás para ampliar slots o mostrar más disponibilidad. |
| Crear página Sobre Nicolás con foto, historia y formación | Mejora Web | Alta | Sin empezar | Acción de mayor impacto en confianza. Incluir foto profesional, enfoque terapéutico, formación académica, años de experiencia y por qué eligió el enfoque humanista transpersonal. |
| Agregar botón flotante de WhatsApp en todo el sitio | WordPress | Alta | Sin empezar | Instalar plugin (ej. WP Social Chat o Chaty) con el número +56 9 2099 8387. Visible en mobile y desktop, en todas las páginas. |
| Definir estrategia de keyword geográfica: Concepción vs Chile | SEO Autopilot | Alta | Sin empezar | El title actual dice "Psicólogo en Concepción" pero el servicio es 100% online. Resolver con el cliente antes de ejecutar la Fase 2. |
| Agregar testimonios o reseñas en la home | Mejora Web | Media | Sin empezar | No hay ninguna prueba social en el sitio. Solicitar 2-3 testimonios (anonimizados si el cliente lo prefiere). Ubicarlos entre servicios y el CTA de agenda. |
| Implementar FAQ Schema en página de Ayuda | WordPress | Media | Sin empezar | La página /ayuda-y-preguntas-frecuentes/ tiene 4 preguntas sin schema markup. Agregar FAQPage (schema.org) vía Rank Math o Yoast. |
| Reescribir títulos y meta descriptions del blog con intención de búsqueda | SEO Autopilot | Media | Sin empezar | Los 6 artículos existentes tienen títulos genéricos. Ejecutar después de Fase 2 para no crear conflictos con las keywords nuevas. |
| Corregir textos en plural ("nuestros psicólogos") a singular | WordPress | Baja | Sin empezar | La meta description de /agenda-tu-consulta-en-linea/ dice "nuestros psicólogos profesionales" en plural. Corregir también otras referencias. |

## Fase 1 — Brand DNA

| Tarea | Skill | Prioridad | Estado | Notas |
|---|---|---|---|---|
| Ejecutar brand-voice-capture para generar Brand DNA | `brand-voice-capture` | Alta | Sin empezar | Analizar home, servicios, blog y contacto. Extraer tono, registro (tú/usted), audiencia ideal, dolor principal, diferenciadores, canales. Output: JSON Brand DNA. |

## Fase 2 — Keywords

| Tarea | Skill | Prioridad | Estado | Notas |
|---|---|---|---|---|
| Ejecutar seo-keyword-research con el Brand DNA | `seo-keyword-research` | Alta | Sin empezar | 30-50 keywords priorizadas: ~40% informacionales, ~30% comerciales, ~20% transaccionales, ~10% geográficas. Auditar los 6 posts existentes antes de finalizar para evitar canibalización. |

## Fase 3 — Clusters

| Tarea | Skill | Prioridad | Estado | Notas |
|---|---|---|---|---|
| Ejecutar topic-cluster-architect con la lista de keywords | `topic-cluster-architect` | Alta | Sin empezar | Clusters esperados: (1) Terapia Online Chile, (2) Ansiedad, (3) Autoestima, (4) Crisis vital/depresión, (5) Terapia humanista transpersonal, (6) Geográfico Concepción (si aplica). |

## Fase 4 — Artículos

| Tarea | Skill | Prioridad | Estado | Notas |
|---|---|---|---|---|
| Escribir artículos SEO con seo-article-writer — Pillar Pages primero | `seo-article-writer` | Alta | Sin empezar | Pillar Pages antes que cluster posts. 2000-3000 palabras para Pillar, 1200-1800 para satélite. Incluir FAQ Schema y CTA hacia agenda. |

## Fase 5 — Publicación

| Tarea | Skill | Prioridad | Estado | Notas |
|---|---|---|---|---|
| Publicar artículos en WordPress con wordpress-seo-publisher | `wordpress-seo-publisher` | Media | Sin empezar | Mapear campos Yoast/RankMath. Verificar URL canónica, meta title ≤60 caracteres, meta description ≤155 caracteres, categorías y tags. |
