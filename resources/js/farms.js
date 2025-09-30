import Swal from "sweetalert2";

const farmsTableBody = document.querySelector("#farmsTableBody");

//elements for modal
const farmForm = document.querySelector("#farmForm");
const farmModal = document.querySelector("#FarmModal");
const openBtn = document.querySelector('#openModalBtn'); 
const closeBtn = document.querySelector('#closeModalBtn'); 
const cancelBtn = document.querySelector('#cancelModalBtn'); 
const modalBox = farmModal.querySelector('div');

//data form 
const farmName = document.querySelector("#farmName");
let currentFarmId = null;

let option = '';

function openModal() 
{ farmModal.classList.remove('hidden'); 
    setTimeout(() => { 
        modalBox.classList.remove('scale-95', 'opacity-0'); 
        modalBox.classList.add('scale-100', 'opacity-100'); }, 50); 
}

function closeModal() { 
    modalBox.classList.remove('scale-100', 'opacity-100'); 
    modalBox.classList.add('scale-95', 'opacity-0'); 
    setTimeout(() => farmModal.classList.add('hidden'), 200); 
    option = ''; 
    BatchForm.reset(); 
}

openBtn.addEventListener('click', () => {
    option = 'create';
    modalTitle.textContent = 'Nueva Granja';
    openModal();
});
closeBtn.addEventListener('click', closeModal); 
cancelBtn.addEventListener('click', closeModal);

document.addEventListener('DOMContentLoaded', () => {
    async function loadFarms() 
    {
        try {
            const response = await fetch('/farms-json');
            if(!response.ok)
            {
                throw new Error('Network response was not ok');
            }
            const farms = await response.json();

            farmsTableBody.innerHTML = '';

            farms.forEach(farm => {
                const row = document.createElement('tr');
                
                const idCell = document.createElement('td');
                idCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                idCell.textContent = farm.id;

                const nameCell = document.createElement('td');
                nameCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                nameCell.textContent = farm.farmName;

                const statesCell = document.createElement('td');
                statesCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                statesCell.textContent = farm.state_id;

                const actionsCell = document.createElement('td');
                actionsCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'font-medium', 'space-x-2');

                const editButton = document.createElement('button');
                editButton.classList.add('text-indigo-600', 'hover:text-indigo-900', 'editButton');
                editButton.textContent = 'Editar';

                const deleteButton = document.createElement('button');
                deleteButton.classList.add('text-red-600', 'hover:text-red-900', 'deleteButton');
                deleteButton.textContent = 'Eliminar';
                
                actionsCell.appendChild(editButton);
                actionsCell.appendChild(deleteButton);
                row.appendChild(idCell);
                row.appendChild(nameCell);
                row.appendChild(statesCell);
                row.appendChild(actionsCell);
                farmsTableBody.appendChild(row);
            });
        } catch (error) {
            console.error('Error fetching farms:', error);
        }    
    }

    loadFarms();


    //edit button and delete(logic to be implemented)
    farmsTableBody.addEventListener('click', async(e) => {
        if(e.target.classList.contains('deleteButton'))
        {
            currentFarmId = e.target.closest('tr').children[0].textContent;
            
            const confirmDelete = await Swal.fire({
                title: '¿Está seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, desactivar'
            })

            if(confirmDelete.isConfirmed)
            {
                try {
                    const response = await fetch(`/farms/${currentFarmId}/deactivate`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });

                    if(!response.ok)
                    {
                        throw new Error('Network response was not ok');
                    }

                    const result = await response.json();
                    if(result.success)
                    {
                        Swal.fire('Desactivado', 'La granja ha sido desactivada.', 'success');
                        loadFarms();
                    }else
                    {
                        Swal.fire('Error', 'Hubo un problema al desactivar la granja.', 'error');
                    }
                } catch (error) {
                    console.error('Error deactivating farm:', error);
                }
            }
        }

        if(e.target.classList.contains('editButton'))
        {
            option = 'edit';
            modalTitle.textContent = 'Editar Granja';
            const selectedRow = e.target.closest('tr');
            currentFarmId = selectedRow.children[0].textContent;
            farmName.value = selectedRow.children[1].textContent;
            openModal();
        }
    })

    //create farm

    async function createFarm()
    {
        try {
            const farmData = {
                farmName: farmName.value,
                state_id:1
            };
            if (farmData.farmName.trim() === '') {
                Swal.fire('Error', 'El nombre de la granja no puede estar vacío.', 'error');
                return;
            }


            const response = await fetch('/farms',{
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(farmData)
            })

            if(!response.ok)
            {
                throw new Error('Network response was not ok');
            }

            let result = await response.json();

            if (result.success) {
                Swal.fire('Éxito', 'Granja creado exitosamente', 'success');
                loadFarms();
                closeModal();
            } else {
                Swal.fire('Error', 'Hubo un problema al crear el usuario', 'error');
            }
        } catch (error) {
            
        }
    }

    //update farm

    async function updateFarm()
    {
        try {
            let farmData = {
                farmName: farmName.value,
                state_id:1
            }

            if (farmData.farmName.trim() === '') {
                Swal.fire('Error', 'El nombre de la granja no puede estar vacío.', 'error');
                return;
            }

            const response = await fetch(`/farms/${currentFarmId}`,{
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(farmData)}
            )

            const result = await response.json();
            if(result.success)
            {
                Swal.fire('Éxito', 'Granja actualizado exitosamente', 'success');
                loadFarms();
                closeModal();
            }else{
                Swal.fire('Error', 'Hubo un problema al actualizar la granja', 'error');
            }
        } catch (error) {
            
        }
    }

    farmForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if(option === 'create')
        {
            createFarm();
        }
        if(option === 'edit')
        {
            updateFarm();
        }
    })

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
    const table = document.querySelector('#farmsTable');
    if (table) {
        new DataTable(table, {
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json'
            }
        });
    }
})