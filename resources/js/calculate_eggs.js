const trayQuantityInput = document.getElementById('trayQuantity');
const eggUnitsInput = document.getElementById('eggUnits');
const totalEggsInput = document.getElementById('totalEggs');

function calculateTotalEggs() {
    const trayQuantity = parseInt(trayQuantityInput.value) || 0;
    const eggUnits = parseInt(eggUnitsInput.value) || 0;
    const total = (trayQuantity * 30) + eggUnits;
    totalEggsInput.value = total;
}

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
}