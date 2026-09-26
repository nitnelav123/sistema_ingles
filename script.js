document.addEventListener("DOMContentLoaded", function () {

  const toggle = document.getElementById("modoToggle");

  if (toggle) {
    // 1. Al cargar la página, revisamos si había una preferencia guardada
    const modoGuardado = localStorage.getItem("modo");

    if (modoGuardado === "claro") {
      document.body.classList.add("modo-claro");
      toggle.checked = true;
    }

    // 2. Cada vez que se cambia el toggle, guardamos la preferencia
    toggle.addEventListener("change", function () {
      document.body.classList.toggle("modo-claro");

      if (document.body.classList.contains("modo-claro")) {
        localStorage.setItem("modo", "claro");
      } else {
        localStorage.setItem("modo", "oscuro");
      }
    });
  }

});


// Función genérica para cambiar el estado de activar/desactivar
function confirmarToggleGenerico(id, nombre, estaActivo, urlPHP, tipoEntidad = 'registro') {
    const accionText = estaActivo ? 'dar de baja' : 'reactivar';
    const confirmButtonText = estaActivo ? 'Sí, dar de baja' : 'Sí, reactivar';

    Swal.fire({
        title: `¿Deseas ${accionText} a ${nombre}?`,
        text: `El/la ${tipoEntidad} cambiará de estado.`,
        icon: 'warning',
        showCancelButton: true,
        confirmColor: '#3085d6',
        cancelColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`${urlPHP}?id=${encodeURIComponent(id)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta de la red');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Ajustado!',
                            text: data.message || `El/la ${tipoEntidad} ha sido actualizado/a.`,
                            icon: 'success'
                        }).then(() => {
                            location.reload(); // Recarga para refrescar la tabla
                        });
                    } else {
                        Swal.fire(
                            'Error',
                            data.message || 'No se pudo realizar la acción.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error AJAX:', error);
                    Swal.fire(
                        'Error',
                        'No se pudo procesar la solicitud.',
                        'error'
                    );
                });
        }
    });
}

// Función genérica para eliminar un registro permanentemente
function confirmarEliminacionGenerica(id, detalle, urlPhp, nombreEntidad = 'registro') {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Vas a eliminar el ${nombreEntidad}: "${detalle}". Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`${urlPhp}?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: data.message || `El ${nombreEntidad} ha sido eliminado.`,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar.', 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Error de conexión con el servidor.', 'error'));
        }
    });
}

// Función para interceptar envíos de formularios mediante AJAX
function manejarEnvioFormulario(idFormulario, urlRedireccion, mensajeExito = "Los cambios se guardaron correctamente") {
    const inicializar = () => {
        const form = document.getElementById(idFormulario);
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Guardado!',
                        text: mensajeExito,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = urlRedireccion;
                    });
                } else {
                    Swal.fire('Error', data.message || 'No se pudieron guardar los cambios', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Ocurrió un error al procesar el formulario.', 'error');
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }
}


