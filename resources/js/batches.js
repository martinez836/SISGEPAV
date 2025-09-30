import swal from 'sweetalert2';
const batchTableBody = document.querySelector('#batchTableBody');

//elements for modal
const BatchForm = document.querySelector("#BatchForm");
const BatchModal = document.querySelector("#BatchModal");
const openBtn = document.querySelector('#openModalBtn'); 
const closeBtn = document.querySelector('#closeModalBtn'); 
const cancelBtn = document.querySelector('#cancelModalBtn'); 
const modalBox = BatchModal.querySelector('div');

//data form 
const batchCode = document.querySelector("#batchCode");
let currentBatchId = null;
let option = '';

function openModal() 
{ BatchModal.classList.remove('hidden'); 
    setTimeout(() => { 
        modalBox.classList.remove('scale-95', 'opacity-0'); 
        modalBox.classList.add('scale-100', 'opacity-100'); }, 50); 
}

function closeModal() { 
    modalBox.classList.remove('scale-100', 'opacity-100'); 
    modalBox.classList.add('scale-95', 'opacity-0'); 
    setTimeout(() => BatchModal.classList.add('hidden'), 200); 
    option = ''; 
    BatchForm.reset(); 
}

openBtn.addEventListener('click', () => {
    option = 'create';
    modalTitle.textContent = 'Nuevo Rol';
    openModal();
});
closeBtn.addEventListener('click', closeModal); 
cancelBtn.addEventListener('click', closeModal);


document.addEventListener('DOMContentLoaded', () => {
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

                const idCell = document.createElement('td');
                idCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                idCell.textContent = batch.id;

                const codeCell = document.createElement('td');
                codeCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                codeCell.textContent = batch.batchName;

                const totalCell = document.createElement('td');
                totalCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                totalCell.textContent = batch.totalBatch;

                const stateCell = document.createElement('td');
                stateCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                stateCell.textContent = batch.batch_state;

                const actionsCell = document.createElement('td');
                actionsCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'font-medium');
                actionsCell.innerHTML = ``;

                const editButton = document.createElement('button');
                editButton.classList.add('text-indigo-600', 'hover:text-indigo-900', 'mr-2','editButton');
                editButton.textContent = 'Editar';


                const harvestButton = document.createElement('button');
                harvestButton.classList.add('text-green-600', 'hover:text-green-900', 'harvestButton');
                harvestButton.textContent = 'Recolectado';


                actionsCell.appendChild(editButton);
                actionsCell.appendChild(harvestButton);
                row.appendChild(idCell);
                row.appendChild(codeCell);
                row.appendChild(totalCell);
                row.appendChild(stateCell);
                row.appendChild(actionsCell);
                batchTableBody.appendChild(row);
            });
        } catch (error) {
            console.error('Error fetching batches:', error);
        }
    }
    loadBatches();

    batchTableBody.addEventListener('click', async(e) => {
        if(e.target.classList.contains('editButton'))
        {
            const row = e.target.closest('tr');
            currentBatchId = row.children[0].textContent;
            const code = row.children[1].textContent;
            batchCode.value = code;
            option = 'edit';
            modalTitle.textContent = 'Editar Lote';
            openModal();
        }
        if(e.target.classList.contains('harvestButton'))
        {
            const row = e.target.closest('tr');
            currentBatchId = row.children[0].textContent;
            const confirmHarvest = await swal.fire({
                title: '¿Está seguro?',
                text: "Esta acción marcará el lote como recolectado.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, recolectar'
            });

            if(confirmHarvest.isConfirmed) 
            {
                try {
                    const resopnse = await fetch(`/batches/${currentBatchId}/markCollection`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })

                    if(!resopnse.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const result = await resopnse.json();
                    if(result.success) {
                        swal.fire('Éxito', 'Lote marcado como recolectado.', 'success');
                        loadBatches();
                    } else {
                        swal.fire('Error', 'Hubo un problema al marcar el lote como recolectado.', 'error');
                    }
                } catch (error) {
                    console.error('Error marking batch as harvested:', error);
                }    
            }
        }
    })

    //create 

    async function createBatch() 
    {
        try {
            let batchData = {
                batchName: batchCode.value,
                batch_state_id: 1
            };
            if(batchData.batchName === '') {
                swal.fire('Error', 'El campo de codigo no puede estar vacío.', 'error');
                return;
            }
            const response = await fetch('/batches', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(batchData)
            })
            if(!response.ok) {
                throw new Error('Network response was not ok');
            }
            const result = await response.json();
            if(result.success) {
                swal.fire('Éxito', 'Lote creado exitosamente.', 'success');
                BatchForm.reset();
                closeModal();
                loadBatches();
            } else {
                swal.fire('Error', 'Hubo un problema al crear el lote.', 'error');
            }
        } catch (error) {
            console.error('Error creating batch:', error);
        }
    }

    //update 

    async function updateBatch() {
        try {
            let batchData = {
                batchName: batchCode.value
            };
            if(batchData.batchName === '') {
                swal.fire('Error', 'El campo de codigo no puede estar vacío.', 'error');
                return;
            }
            const response = await fetch(`/batches/${currentBatchId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(batchData)
            })
            if(!response.ok) {
                throw new Error('Network response was not ok');
            }
            const result = await response.json();
            if(result.success) {
                swal.fire('Éxito', 'Lote actualizado exitosamente.', 'success');
                BatchForm.reset();
                closeModal();
                loadBatches();
            } else {
                swal.fire('Error', 'Hubo un problema al actualizar el lote.', 'error');
            }
        } catch (error) {
            console.error('Error updating batch:', error);
        }
    }

    BatchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if(option === 'create') {
            createBatch();
        }
        if(option === 'edit') {
            updateBatch();
        }
    })

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
    const table = document.getElementById('batchesTable');
    if (table) {
        new DataTable(table, {
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json'
            }
        });
    }
})