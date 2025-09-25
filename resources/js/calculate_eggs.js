const trayQuantityInput = document.getElementById('trayQuantity');
const eggUnitsInput = document.getElementById('eggUnits');
const totalEggsInput = document.getElementById('totalEggs');
const harvestForm = document.getElementById('harvest-form');

function calculateTotalEggs() {
    const trayQuantity = parseInt(trayQuantityInput.value) || 0;
    const eggUnits = parseInt(eggUnitsInput.value) || 0;
    const total = (trayQuantity * 30) + eggUnits;
    totalEggsInput.value = total;
};

trayQuantityInput.addEventListener('input', calculateTotalEggs);
eggUnitsInput.addEventListener('input', calculateTotalEggs);

// Alerta de éxito que desaparece después de 5 segundos

const successAlert = document.getElementById('success-alert');
if (successAlert) {
    setTimeout(() => {
        successAlert.classList.add('opacity-0');
        setTimeout(() => {
            successAlert.remove();
        }, 300);
    }, 5000);
};

// Confirmación antes de enviar el formulario
harvestForm.addEventListener('submit', function (event) {
    event.preventDefault(); // Prevenir el envío inmediato del formulario
    if (harvestForm) {
        harvestForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío inmediato del formulario

            // validacion
            const trayQuantity = trayQuantityInput.value;
            const eggUnits = eggUnitsInput.value;

            if (trayQuantity < 0 || eggUnits < 0 || eggUnits > 29 || trayQuantity === '' || eggUnits === '') {
                Swal.fire({
                    title: 'Error',
                    text: 'Por favor, verifica los datos ingresados.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }

            Swal.fire({
                title: 'Confirmar',
                text: '¿Estás seguro de que deseas guardar estos datos?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    harvestForm.submit(); // Enviar el formulario si se confirma
                }
            });
        });
    }
});

