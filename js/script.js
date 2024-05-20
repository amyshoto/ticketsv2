function limpiarFormulario() {
    document.getElementById("form_reporte").reset();
}

/* const hoy = new Date().toISOString().split('T')[0];
document.getElementById("fechaActual").value = hoy; */

document.addEventListener("DOMContentLoaded", function() {
    const hoy = new Date();
    const dia = hoy.getDate().toString().padStart(2, '0');
    const mes = (hoy.getMonth() + 1).toString().padStart(2, '0');
    const anio = hoy.getFullYear();
    const fechaLocal = `${anio}-${mes}-${dia}`;
    document.getElementById("fechaActual").value = fechaLocal;
});



