import swal from 'sweetalert2';

const rolesTableBody = document.querySelector("#rolesTableBody");

//elements for modal
const RolForm = document.querySelector("#RolForm");
const RolModal = document.querySelector("#RolModal");
const openBtn = document.querySelector('#openModalBtn'); 
const closeBtn = document.querySelector('#closeModalBtn'); 
const cancelBtn = document.querySelector('#cancelModalBtn'); 
const modalBox = RolModal.querySelector('div');

//data form 
const roleName = document.querySelector("#rolName");
let currentRoleId = null;

let option = '';

function openModal() 
{ RolModal.classList.remove('hidden'); 
    setTimeout(() => { 
        modalBox.classList.remove('scale-95', 'opacity-0'); 
        modalBox.classList.add('scale-100', 'opacity-100'); }, 50); 
}

function closeModal() { 
    modalBox.classList.remove('scale-100', 'opacity-100'); 
    modalBox.classList.add('scale-95', 'opacity-0'); 
    setTimeout(() => RolModal.classList.add('hidden'), 200); 
}

openBtn.addEventListener('click', () => {
    option = 'create';
    modalTitle.textContent = 'Nuevo Rol';
    openModal();
});
closeBtn.addEventListener('click', closeModal); 
cancelBtn.addEventListener('click', closeModal);

document.addEventListener('DOMContentLoaded', () => {
    async function loadRoles()
    {
        try {
            const response = await fetch('/getRoles');
            if(!response.ok)
            {
                throw new Error('Network response was not ok');
            }

            const roles = await response.json();

            rolesTableBody.innerHTML = '';
            roles.forEach(rol => {
                const row = document.createElement('tr');

                const idCell = document.createElement('td');
                idCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                idCell.textContent = rol.id;

                const nameCell = document.createElement('td');
                nameCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900');
                nameCell.textContent = rol.rolName;

                const actionsCell = document.createElement('td');
                actionsCell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'font-medium');
                actionsCell.innerHTML = ``;

                const editButton = document.createElement('button');
                editButton.classList.add('text-indigo-600', 'hover:text-indigo-900', 'mr-2','editButton');
                editButton.textContent = 'Editar';

                const deleteButton = document.createElement('button');
                deleteButton.classList.add('text-red-600', 'hover:text-red-900', 'deleteButton');
                deleteButton.textContent = 'Eliminar';

                actionsCell.appendChild(editButton);
                actionsCell.appendChild(deleteButton);
                row.appendChild(idCell);
                row.appendChild(nameCell);
                row.appendChild(actionsCell);
                rolesTableBody.appendChild(row);
            });
        } catch (error) {
            console.error('Error fetching roles:', error);
        }
    }
    loadRoles();


    //edit button and delete button event delegation
    rolesTableBody.addEventListener('click', (e) => {
        if(e.target.classList.contains('editButton'))
        {
            const row = e.target.closest('tr');
            currentRoleId = row.children[0].textContent;
            roleName.value = row.children[1].textContent;
            option = 'edit';
            modalTitle.textContent = 'Editar Rol';
            openModal();
        }
    })

    //update
    async function updateRole()
    {
        try {
            let rolData = {
                rolName: roleName.value,
            }
            if(rolData.rolName.trim() === ''){
                swal.fire('Error', 'El nombre del rol no puede estar vacío.', 'error');
                return;
            }
            const response = await fetch(`/roles/${currentRoleId}`,{
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(rolData)
            })
            if(!response.ok)
            {
                throw new Error('Network response was not ok');
            }

            const result = await response.json();
            if(result.success){
                swal.fire('Éxito', 'Rol actualizado exitosamente.', 'success');
                RolForm.reset();
                closeModal();
                loadRoles();
            }else{
                swal.fire('Error', 'Hubo un problema al actualizar el rol.', 'error');
            }
        } catch (error) {
            console.error('Error updating role:', error);
        }
    }

    //create 
    async function createRole()
    {
        try {
            let rolData = {
                rolName: roleName.value,
            }
            if(rolData.rolName.trim() === ''){
                swal.fire('Error', 'El nombre del rol no puede estar vacío.', 'error');

            }
            const response = await fetch('/roles',{
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(rolData)
            })

            if(!response.ok)
            {
                throw new Error('Network response was not ok');
            }

            const result = await response.json();
            if(result.success){
                swal.fire('Éxito', 'Rol creado exitosamente.', 'success');
                RolForm.reset();
                closeModal();
                loadRoles();
            }else{
                swal.fire('Error', 'Hubo un problema al crear el rol.', 'error');
            }
        } catch (error) {
            console.error('Error creating role:', error);
        }
    }

    RolForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if(option === 'create')
        {
            createRole();
        }
        if(option === 'edit')
        {
            updateRole();
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
})