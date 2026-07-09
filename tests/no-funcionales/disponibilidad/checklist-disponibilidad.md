# Checklist de Disponibilidad — Recuperación ante caída de dependencias

Evaluación de cómo se comporta OpenCart (`upload/`) cuando una dependencia crítica (base de
datos) deja de estar disponible, y qué tan rápido se recupera cuando vuelve. Complementa las
pruebas de rendimiento (que miden velocidad bajo carga normal) evaluando **resiliencia ante
fallas**, no solo velocidad.

**Ambiente evaluado**: `http://localhost/QA-OpenCart-Testing/upload` (XAMPP local)
**Fecha**: 2026-07-09

---

## Escenario 1: Caída de MySQL

### Procedimiento

1. Confirmar baseline: el sitio responde 200 normal en tienda y admin.
2. Detener el servicio MySQL (XAMPP Control Panel → Stop en MySQL, o `taskkill /F /IM mysqld.exe`
   si el script `mysql_stop.bat` de XAMPP no funciona — ver nota abajo).
3. Repetir peticiones a rutas clave (`common/home`, `product/product`, `admin/common/login`,
   `api/order`) y registrar: código HTTP, tiempo de respuesta, contenido de la respuesta.
4. Reiniciar MySQL (XAMPP Control Panel → Start).
5. Repetir las mismas peticiones inmediatamente y medir el tiempo hasta que vuelvan a responder
   con contenido normal.

> **Nota de entorno**: en esta instalación de XAMPP, `mysql_stop.bat` está roto — referencia
> una variable de instalador (`@@BITROCK_INSTALLDIR@@`) que nunca se resolvió, por lo que no
> mata el proceso `mysqld.exe`. Se detuvo manualmente desde el XAMPP Control Panel.

### Resultados observados

| Verificación | Con MySQL activo (baseline) | Con MySQL caído | Tras reiniciar MySQL |
|---|---|---|---|
| `common/home` — HTTP status | 200 | **200** (⚠️ debería ser 503) | 200 |
| `common/home` — tiempo de respuesta | ~0.12s | **~2.05s** (timeout de conexión) | ~0.12s (recuperación inmediata) |
| `product/product` — HTTP status | 200 | 200 (⚠️) | 200 |
| `admin/common/login` — HTTP status | 200 | 200 (⚠️) | 200 |
| `api/order&call=cart` — HTTP status | 200 | 200 (⚠️) | 200 |
| Contenido de la respuesta | HTML normal | **Stack trace de PHP con rutas absolutas del servidor** | HTML normal |
| Consistencia del timeout (3 intentos) | — | 2.03s / 2.06s / 2.04s (muy consistente) | — |

### Hallazgos

- ✅ **Recuperación automática e inmediata**: en cuanto MySQL volvió a responder, el primer
  request de la aplicación ya funcionó con normalidad (sin necesidad de reiniciar Apache,
  limpiar caché, ni ningún paso manual adicional). No hay estado "colgado" tras la
  reconexión.
- ⚠️ **Hallazgo — ver [INC-DISP-001](incident-reports/INC-DISP-001-error-no-controlado-sin-bd.md)**:
  durante la caída, el sitio devuelve **HTTP 200** (no 503 Service Unavailable) con un mensaje
  de error de PHP crudo, exponiendo rutas absolutas del servidor (`C:\xampp\htdocs\...`) y la
  estructura interna del framework (clases, archivos, números de línea).
- ⚠️ El timeout de conexión a MySQL es de **~2 segundos** por request — bajo una caída real con
  tráfico concurrente, esto podría agotar el pool de conexiones/workers de PHP-FPM o Apache
  (cada request queda "colgado" 2s en vez de fallar rápido), degradando la capacidad de
  respuesta del servidor incluso para health-checks.

---

## Escenario 2: Caída de Apache (pendiente)

No ejecutado en este ciclo — al detener Apache, el propio servidor HTTP deja de responder, por
lo que no hay nada que medir vía HTTP (el "tiempo de recuperación" pasa a ser un problema de
infraestructura/orquestación, no de la aplicación). Si se requiere cubrir este caso, documentar
en su lugar:
- Tiempo que tarda el proceso `httpd.exe` en reiniciarse tras un `apache_stop.bat` + `apache_start.bat`.
- Si existe algún mecanismo de auto-restart (supervisor, systemd, Windows Service) configurado
  para producción — en XAMPP local no aplica.

## Escenario 3: Timeout / lentitud de MySQL (no caída total) — pendiente

No ejecutado en este ciclo. Sería relevante simular una MySQL que responde lento (no caída, pero
degradada) para ver si OpenCart tiene algún timeout propio más agresivo que el de PHP/mysqli, o
si hereda el mismo comportamiento de 2s por conexión observado en el Escenario 1.

---

## Resumen de hallazgos

| ID | Severidad | Resumen |
|---|---|---|
| [INC-DISP-001](incident-reports/INC-DISP-001-error-no-controlado-sin-bd.md) | Alta | Sin BD disponible, el sitio devuelve HTTP 200 con stack trace de PHP expuesto, en vez de una página de error controlada (503) |

## Conclusión

La **recuperación** de OpenCart tras restablecer la base de datos es excelente (inmediata, sin
pasos manuales). El punto débil está en el **manejo del error durante la caída**: no hay una
página de mantenimiento amigable, el código HTTP es incorrecto (200 en vez de 503), y se expone
información interna del servidor que facilita reconnaissance a un atacante — esto conecta este
hallazgo también con la categoría de Seguridad (A05: Security Misconfiguration /
A09: Logging and Monitoring Failures, ya que un error de esta naturaleza debería registrarse
para alertar a un operador, no solo mostrarse al usuario).
