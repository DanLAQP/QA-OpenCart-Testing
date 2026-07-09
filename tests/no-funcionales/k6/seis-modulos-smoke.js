import {
  loginYRegistro,
  catalogoYBusqueda,
  carritoDeCompras,
  checkoutYPago,
  gestionDeInventario,
  listadoAdminInventario,
  resenas,
} from './seis-modulos-carga.js';

// Smoke test: 1 iteracion de cada uno de los 6 modulos, para validar rutas/tokens
// antes de correr la prueba de carga completa (seis-modulos-carga.js).

export const options = {
  scenarios: {
    smoke_login_registro: { executor: 'shared-iterations', exec: 'loginYRegistro', vus: 1, iterations: 1 },
    smoke_catalogo_busqueda: { executor: 'shared-iterations', exec: 'catalogoYBusqueda', vus: 1, iterations: 1 },
    smoke_carrito: { executor: 'shared-iterations', exec: 'carritoDeCompras', vus: 1, iterations: 1 },
    smoke_checkout: { executor: 'shared-iterations', exec: 'checkoutYPago', vus: 1, iterations: 1 },
    smoke_inventario: { executor: 'shared-iterations', exec: 'gestionDeInventario', vus: 1, iterations: 1 },
    smoke_listado_admin: { executor: 'shared-iterations', exec: 'listadoAdminInventario', vus: 1, iterations: 1 },
    smoke_resenas: { executor: 'shared-iterations', exec: 'resenas', vus: 1, iterations: 1 },
  },
};

export {
  loginYRegistro,
  catalogoYBusqueda,
  carritoDeCompras,
  checkoutYPago,
  gestionDeInventario,
  listadoAdminInventario,
  resenas,
};
