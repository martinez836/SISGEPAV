/* import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css'; */

const batchTableBody = document.querySelector('#batchTableBody');
document.addEventListener('DOMContentLoaded', () => {
    // Reloj
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        function updateClock() {
            const now = new Date();
            const day = String(now.getDate()).padStart(2, '0');
            const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            const month = monthNames[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            timeElement.textContent = `Hoy es: ${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
        }
        updateClock();
        setInterval(updateClock, 1000);
    }

    async function cargarHuevosHoy() {
        try {
            const response = await fetch('/huevos-hoy');
            if (!response.ok) throw new Error('Error en la petición');

            const data = await response.json();

            // Seleccionar el <p> dentro del div y actualizarlo
            document.querySelector('#huevosHoy').textContent = data.totalHuevos.toLocaleString();
        } catch (error) {
            console.error('Error cargando huevos de hoy:', error);
        }
    }

    // Llamar la función al cargar la página
    cargarHuevosHoy();

    // Sidebar
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (sidebarToggle && sidebar && overlay) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    }
     async function loadBatches()
    {
        try {
            const response = await fetch('/batches-json');
            if(!response.ok)
            {
                throw new Error('Network response was not ok');
            }
            const batches = await response.json();
            batchTableBody.innerHTML = '';
            batches.forEach(batch => {
                const row = document.createElement('tr');

                const codeCell = document.createElement('td');
                codeCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                codeCell.textContent = batch.batchName;

                const dateCell = document.createElement('td');
                dateCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                dateCell.textContent = new Date(batch.created_at).toLocaleDateString('es-CO', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                });

                const totalCell = document.createElement('td');
                totalCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                totalCell.textContent = batch.totalBatch;

                const stateCell = document.createElement('td');
                stateCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                stateCell.textContent = batch.batch_state;

                row.appendChild(codeCell);
                row.appendChild(dateCell);
                row.appendChild(totalCell);
                row.appendChild(stateCell);
                batchTableBody.appendChild(row);
            });
        } catch (error) {
            console.error('Error fetching batches:', error);
        }
    }
    loadBatches();

    // User dropdown
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');

    if (userMenuButton && userDropdown) {
        userMenuButton.addEventListener('click', function (event) {
            event.stopPropagation();
            toggleDropdown();
        });

        document.addEventListener('click', function (event) {
            if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
                closeDropdown();
            }
        });

        function toggleDropdown() {
            if (userDropdown.classList.contains('hidden')) {
                openDropdown();
            } else {
                closeDropdown();
            }
        }

        function openDropdown() {
            userDropdown.classList.remove('hidden');
            setTimeout(() => {
                userDropdown.classList.remove('scale-95', 'opacity-0');
                userDropdown.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeDropdown() {
            userDropdown.classList.remove('scale-100', 'opacity-100');
            userDropdown.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                userDropdown.classList.add('hidden');
            }, 200);
        }
    }

    // DataTable
    const table = document.getElementById('batchTable');
    if (table) {
        new DataTable(table, {
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json'
            }
        });
    }
 
})
    