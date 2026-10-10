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
function manejarEnvioFormulario(idFormulario, urlRedireccion, mensajeExito = "Los cambios se guardaron correctamente", pregunta = null) {
    const inicializar = () => {
        const form = document.getElementById(idFormulario);
        if (!form) return;

        const enviar = () => {
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const redireccionFinal = data.redirect || urlRedireccion;
                    
                    // Si viene desde login, directamente redirigimos o mostramos confirmación
                    if (redireccionFinal && idFormulario === 'formLogin') {
                        window.location.href = redireccionFinal;
                    } else {
                        Swal.fire({
                            title: '¡Guardado!',
                            text: mensajeExito,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            if (redireccionFinal) {
                                window.location.href = redireccionFinal;
                            }
                        });
                    }
                } else {
                    // SweetAlert para errores / usuario inactivo / datos incorrectos
                    Swal.fire({
                        title: data.title || 'Atención',
                        text: data.message || 'Contraseña o Email incorrectos.',
                        icon: data.icon || 'error',
                        confirmButtonText: 'Volver',
                        confirmButtonColor: '#3085d6'
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    title: 'Error de conexión',
                    text: 'Ocurrió un error al procesar la solicitud.',
                    icon: 'error',
                    confirmButtonText: 'Volver',
                    confirmButtonColor: '#3085d6'
                });
            });
        };

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!pregunta) {
                enviar();
                return;
            }

            Swal.fire({
                title: pregunta,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Volver',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    enviar();
                }
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }
}

// Cancelar o reactivar una inscripción
function confirmarAccionInscripcion(id, detalle, accion) {
    const esCancelar = accion === 'cancelar';
    const urlPHP = esCancelar ? 'admin-cancelar-inscripcion.php' : 'admin-reactivar-inscripcion.php';

    Swal.fire({
        title: esCancelar ? '¿Cancelar la inscripción?' : '¿Reactivar la inscripción?',
        text: detalle,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: esCancelar ? '#dc2626' : '#479f98',
        cancelButtonColor: '#64748b',
        confirmButtonText: esCancelar ? 'Sí, cancelar' : 'Sí, reactivar',
        cancelButtonText: 'Volver',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`${urlPHP}?id=${encodeURIComponent(id)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Listo!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo realizar la acción.', 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Error de conexión con el servidor.', 'error'));
        }
    });
}

// Conexión automática entre el HTML y las funciones de arriba
document.addEventListener('DOMContentLoaded', function () {

    // Botones de inscripciones (Cancelar / Reactivar)
    document.querySelectorAll('.btn-inscripcion').forEach(function (boton) {
        boton.addEventListener('click', function () {
            confirmarAccionInscripcion(boton.dataset.id, boton.dataset.detalle, boton.dataset.accion);
        });
    });

    // Formularios por AJAX: <form data-ajax data-pregunta="..." data-exito="..." data-redireccion="...">
    document.querySelectorAll('form[data-ajax]').forEach(function (form) {
        manejarEnvioFormulario(
            form.id,
            form.dataset.redireccion || null,
            form.dataset.exito || undefined,
            form.dataset.pregunta || null
        );
    });
});