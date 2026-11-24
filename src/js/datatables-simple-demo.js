window.addEventListener('DOMContentLoaded', event => {
    // Variável global
    window.datatable = null;

    const datatablesSimple = document.getElementById('datatablesSimple');
    if (datatablesSimple) {
        window.datatable = new simpleDatatables.DataTable(datatablesSimple);
    }

    const datatablesSimple2 = document.getElementById('datatablesSimple2');
    if (datatablesSimple2) {
        new simpleDatatables.DataTable(datatablesSimple2);
    }

    const datatablesSimple3 = document.getElementById('datatablesSimple3');
    if (datatablesSimple3) {
        new simpleDatatables.DataTable(datatablesSimple3);
    }
});
