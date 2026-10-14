# ARQUITECTURA DE SOFTWARE - PROYECTO CARPINTERSOFT
## Sistema Web para Diseño y Compra de Muebles en 3D

---

## 1. DEFINICIÓN DE DOMINIOS Y SUBDOMINIOS (Domain-Driven Design - DDD)

Estructuración basada en **Domain-Driven Design (DDD)** y descomposición modular para garantizar la escalabilidad, mantenibilidad y desacoplamiento de las capacidades del negocio.

```
+-----------------------------------------------------------------------------------+
|                                 CARPINTERSOFT                                     |
+-----------------------------------------------------------------------------------+
| CORE DOMAINS (Ventaja Competitiva)                                                |
|  ├── [Diseño 3D & Modelado con IA]    ── WebGL / Three.js + Asistente IA          |
|  └── [Cotizador & Costos en Tiempo Real] ── Volumetría, Despiece, Desperdicio      |
+-----------------------------------------------------------------------------------+
| SUPPORTING DOMAINS (Operación del Negocio)                                        |
|  ├── [Gestión de Pedidos & Proyectos] ── Trazabilidad 7 Fases Taller / Kanban    |
|  └── [Inventario & Abastecimiento]    ── Maderas, Herrajes, Alertas Stock Mínimo |
+-----------------------------------------------------------------------------------+
| GENERIC SUBDOMAINS (Infraestructura Transversal)                                  |
|  ├── [Identidad & Acceso - IAM]        ── Auth JWT, 2FA OTP, Roles (RBAC)         |
|  ├── [Pagos & Facturación]             ── Pasarelas (Stripe/MercadoPago), Factura |
|  └── [Notificaciones & Auditoría]      ── Webhooks, Emails, Logs Inmutables       |
+-----------------------------------------------------------------------------------+
```

---

### 1.1 Dominios Principales (Core Domains)

* **Diseño 3D y Modelado Asistido por IA (`RF05`, `RNF02`, `RNF03`):**
  * Creación paramétrica de muebles (alto, ancho, fondo, divisiones interiores).
  * Visualización y manipulación interactiva de geometrías tridimensionales mediante **WebGL y Three.js**.
  * Gestión avanzada de texturas (vetas de roble, melaminas, acabados lacados, vidrios y metales).
  * Renderizado ligero con framerate optimizado (`≥ 60 FPS`).
  * Integración con asistente inteligente (**CarpinterIA**) para sugerencias automatizadas de muebles y tolerancias constructivas según requerimientos del cliente.

* **Cotización y Cálculo de Costos en Tiempo Real (`RF06`, `RF07`, `RF09`):**
  * Motor de cálculo dinámico volumétrico de materiales (pies tablares en macizos, planchas de melamina de 2.44x1.83m).
  * Algoritmo de optimización de corte y cálculo de porcentaje de desperdicio.
  * Presupuestación automática de herrajes (correderas telescópicas, bisagras cierre suave, tiradores).
  * Desglose de costos operativos: mano de obra de ebanista, ensamble, empaque, flete e instalación.
  * Generación instantánea de presupuestos imprimibles y exportables en PDF.

---

### 1.2 Dominios de Soporte (Supporting Domains)

* **Gestión de Pedidos y Proyectos (`RF12`, `RF14`, `RF16`, `RF21`):**
  * Orquestación del ciclo de vida del pedido a través de 7 fases estandarizadas:
    1. `Cotización`
    2. `Diseño & Planos`
    3. `Corte de Tableros`
    4. `Ensamble de Módulos`
    5. `Acabado & Pintura / Lacado`
    6. `Listo para Despacho`
    7. `Instalado & Entregado`
  * Tablero Kanban operativo para maestros carpinteros y supervisores.
  * Trazabilidad cronológica de avances y control de calidad.

* **Inventario y Abastecimiento (`RF08`, `RF10`, `RF11`, `RF15`):**
  * Administración de existencias de materias primas categorizadas: maderas macizas, tableros melamínicos, herrajes mecánicos y químicos/adhesivos.
  * Registro de proveedores con costos unitarios e historial de compras.
  * Alertas automáticas de stock mínimo para prevenir cuellos de botella en taller.
  * Solicitudes automatizadas de reposición y valorización total del almacén.

---

### 1.3 Dominios Genéricos (Generic Subdomains)

* **Gestión de Identidad y Acceso - IAM (`RF01`, `RF02`, `RF03`, `RF04`, `RF22`):**
  * Autenticación centralizada con cifrado Argon2id / bcrypt.
  * Verificación en dos pasos (2FA OTP por correo electrónico).
  * Sesiones seguras mediante tokens firmados (JWT / cookies seguras `HttpOnly`).
  * Control de acceso basado en roles (**RBAC**): `Administrador`, `Maestro Carpintero`, `Vendedor`, `Cliente`.

* **Pagos y Facturación (`RF19`, `RF20`, `RNF06`):**
  * Procesamiento de anticipos y liquidaciones contra entrega mediante pasarelas externas bajo estándar PCI-DSS / TLS 1.3.
  * Registro de comprobantes de pago (transferencias, consignaciones, tarjetas).
  * Facturación electrónica estructurada y recibos de caja.

* **Notificaciones y Auditoría (`RF18`, `RF23`):**
  * Mensajería multicanal (Alertas in-app, correo transaccional y enlaces de actualización vía WhatsApp).
  * Registro inmutable de logs de auditoría (cambios de estado, ajustes manuales de inventario y transacciones).

---

## 2. PATRONES ARQUITECTÓNICOS Y DE DISEÑO

### 2.1 Patrones Arquitectónicos

1. **API Gateway Pattern:**
   * Punto de entrada unificado para clientes web (navegadores de escritorio y dispositivos móviles).
   * Enrutamiento dinámico, validación de autenticación JWT, rate limiting y CORS centralizado.

2. **Arquitectura Hexagonal (Ports & Adapters):**
   * Núcleo del dominio (lógica de cálculo de despiece, reglas de cubicaje de madera, validación de tolerancias) totalmente independiente de la base de datos o interfaces web.
   * *Puertos de entrada:* Casos de uso (CrearPedido, OptimizarCorte, CotizarMueble).
   * *Adaptadores de salida:* Repositorios MySQL, almacenamiento local de respaldo, generadores PDF y conectores de mensajería.

3. **Database per Service / Modular Persistence:**
   * Almacenamiento desacoplado por capacidad de dominio.
   * Base de datos relacional ACID (MySQL / MariaDB en XAMPP) para transacciones de pedidos, inventario y facturación.
   * Modelos JSON/NoSQL para estructuras paramétricas de piezas y esquemas tridimensionales 3D.

4. **Event-Driven Architecture (EDA):**
   * Comunicación desacoplada basada en eventos de dominio:
     * `PedidoCreado` → Dispara reserva en inventario y notificación al cliente.
     * `AbonoConfirmado` → Habilita el pase del proyecto a la fase de *Corte*.
     * `StockMinimoAlcanzado` → Emite alerta prioritaria al módulo de abastecimiento.

---

### 2.2 Patrones de Diseño (GoF)

| Patrón GoF | Ámbito de Aplicación en CarpinterSoft | Beneficio Técnico |
|---|---|---|
| **Strategy** | Motor de Cotización y Precios | Permite alternar algoritmos de cobro según tipo de material (macizo vs. melamina), margen comercial por tipo de cliente o complejidad de ensamble. |
| **Builder** | Ensamblador Paramétrico de Muebles y Despiece | Construye paso a paso la estructura del mueble agregando laterales, repisas, fondos, cajones, tapacantos y herrajes según medidas del usuario. |
| **Observer** | Máquina de Estados del Pedido | Cuando un pedido cambia de fase (ej: de *Ensamble* a *Acabado*), notifica a los suscriptores (registro de auditoría, panel de métricas y cliente). |
| **Adapter** | Servicios Externos e Integraciones | Homogeneiza pasarelas de pago (Stripe, MercadoPago) y proveedores de correo/notificaciones bajo interfaces estándar unificadas. |
| **Repository** | Capa de Acceso a Datos | Abstrae la persistencia en MySQL (`config/db.php`), permitiendo alternar entre consultas SQL preparadas y mecanismos de respaldo sin afectar la lógica de negocio. |

---

*Documento técnico de arquitectura integrado en el repositorio oficial de CarpinterSoft.*
