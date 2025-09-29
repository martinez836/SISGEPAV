import Swal from 'sweetalert2';

// función global para exportar
export function initEggCalculator() {
    const trayQuantityInput = document.getElementById('trayQuantity');
    const eggUnitsInput = document.getElementById('eggUnits');
    const totalEggsInput = document.getElementById('totalEggs');
    const harvestForm = document.getElementById('harvest-form');
    const farmSelect = document.getElementById('farmSelect');
    const batchSelect = document.getElementById('batchSelect');

    function calculateTotalEggs() {
        const trayQuantity = parseInt(trayQuantityInput.value) || 0;
        const eggUnits = parseInt(eggUnitsInput.value) || 0;
        const total = (trayQuantity * 30) + eggUnits;
        totalEggsInput.value = total;
    }

    trayQuantityInput.addEventListener('input', calculateTotalEggs);
    eggUnitsInput.addEventListener('input', calculateTotalEggs);

    // Confirmación antes de enviar el formulario
    if (harvestForm) {
        harvestForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // validaciones 
            const trayQuantity = trayQuantityInput.value;
            const eggUnits = eggUnitsInput.value;
            const farmId = farmSelect.value;
            const batchId = batchSelect.value;

            // validación seleccion lote
            if(!batchId) {
                Swal.fire({
                    title: 'Error',
                    text: 'Debe seleccionar un lote!',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }

            // validación si no se ha seleccionado una granja
            if (!farmId) {
                Swal.fire({
                    title: 'Error',
                    text: 'Debe seleccionar una granja!',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }

            // validación si se el total de huevos es 0
            if(totalEggsInput.value < 1) {
                Swal.fire({
                    title: 'Error',
                    text: 'El total huevos no puede ser cero.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            if (trayQuantity < 0 || eggUnits < 0 || eggUnits > 29 || trayQuantity === '' || eggUnits === '') {
                Swal.fire({
                    title: 'Error',
                    text: 'Por favor, verifica los datos ingresados, debe ingresar valores válidos.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }

            // Mensaje de confirmación o éxito
            Swal.fire({
                title: 'Confirmar',
                text: '¿Estás seguro de que deseas guardar estos datos?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                // Si el usuario confirma, envía el formulario
                if (result.isConfirmed) {
                    harvestForm.submit();
                }
            });
        });
    }
}
