import Swal from "sweetalert2";

const usersTableBody = document.querySelector('#usersTableBody');
const roleSelect = document.querySelector('#roleSelect');
const userForm = document.querySelector('#userForm');
const modalTitle = document.querySelector('#modalTitle');

//data form
const userName = document.querySelector('#userName');
const email = document.querySelector('#email');
const password = document.querySelector('#password');
let currentUserId = null;

//elements for modal
const modal = document.getElementById('userModal'); 
const openBtn = document.getElementById('openModalBtn'); 
const closeBtn = document.getElementById('closeModalBtn'); 
const cancelBtn = document.getElementById('cancelModalBtn'); 
const modalBox = modal.querySelector('div');

let option = '';

function openModal() 
{ modal.classList.remove('hidden'); 
    setTimeout(() => { 
        modalBox.classList.remove('scale-95', 'opacity-0'); 
        modalBox.classList.add('scale-100', 'opacity-100'); }, 50); 
}

function closeModal() { 
    modalBox.classList.remove('scale-100', 'opacity-100'); 
    modalBox.classList.add('scale-95', 'opacity-0'); 
    setTimeout(() => modal.classList.add('hidden'), 200); 
}

openBtn.addEventListener('click', () => {
    option = 'create';
    password.required = true;
    modalTitle.textContent = 'Nuevo Usuario';
    openModal();
});
closeBtn.addEventListener('click', closeModal); 
cancelBtn.addEventListener('click', closeModal);

document.addEventListener('DOMContentLoaded', () => {
    async function loadUsers()
    {
        try {
            
            const response = await fetch('/users-json');
            if(!response.ok) {
                throw new Error('Network response was not ok');
            }
            const users = await response.json();
            usersTableBody.innerHTML = '';
            users.forEach(user => {
                const row = document.createElement('tr');

                const idCol = document.createElement('td');
                idCol.classList.add('px-6', 'py-4', 'whitespace-nowrap');
                idCol.textContent = user.id;
                

                const nameCol = document.createElement('td');
                nameCol.classList.add('px-6', 'py-4', 'whitespace-nowrap');
                nameCol.textContent = user.name;

                const emailCol = document.createElement('td');
                emailCol.classList.add('px-6', 'py-4', 'whitespace-nowrap');
                emailCol.textContent = user.email;

                const roleCol = document.createElement('td');
                roleCol.classList.add('px-6', 'py-4', 'whitespace-nowrap');
                roleCol.textContent = user.role;

                const stateCol = document.createElement('td');
                stateCol.classList.add('px-6', 'py-4', 'whitespace-nowrap');
                stateCol.textContent = user.state ;

                const actionsCol = document.createElement('td');
                actionsCol.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'font-medium', 'space-x-2');
                actionsCol.classList.add('flex', 'gap-2');

                const editButton = document.createElement('button');
                editButton.classList.add('text-blue-600', 'hover:text-blue-900','editButton');
                editButton.textContent = 'Editar';
                actionsCol.appendChild(editButton);

                const deleteButton = document.createElement('button');
                deleteButton.classList.add('text-red-600', 'hover:text-red-900','deleteButton');
                deleteButton.textContent = 'Eliminar';
                actionsCol.appendChild(deleteButton);

                row.appendChild(idCol);
                row.appendChild(nameCol);
                row.appendChild(emailCol);
                row.appendChild(roleCol);
                row.appendChild(stateCol);
                row.appendChild(actionsCol);
                usersTableBody.appendChild(row);
            });
        } catch (error) {
            console.error('Error fetching users:', error);
        }
    }

    loadUsers();

    usersTableBody.addEventListener('click', async(event)=> {
        if (event.target.classList.contains('deleteButton')) 
            {
                const userId = event.target.closest('tr').children[0].textContent;

                const confirmDelete = await Swal.fire({
                    title: '¿Está seguro?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, desactivar'
                })

                if (confirmDelete.isConfirmed) 
                {
                    try {
                        const response = await fetch(`/users/${userId}/deactivate`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const result = await response.json();
                        if (result.success) {
                            Swal.fire('Desactivado', 'El usuario ha sido desactivado.', 'success');
                            loadUsers();
                        }else {
                            Swal.fire('Error', 'Hubo un problema al desactivar el usuario.', 'error');
                        }
                    } catch (error) {
                        console.error('Error deleting user:', error);
                    }
                }
            }
        if (event.target.classList.contains('editButton'))
        {
            option = 'edit';
            currentUserId = event.target.closest('tr').children[0].textContent;
            userName.value = event.target.closest('tr').children[1].textContent;
            email.value = event.target.closest('tr').children[2].textContent;
            password.value = '';
            roleSelect.value = Array.from(roleSelect.options).find(option => option.text === event.target.closest('tr').children[3].textContent)?.value || '';
            modalTitle.textContent = 'Editar Usuario';
            password.required = false;
            openModal();
            
        }
    })

    async function loadRoles() {
        try {
            const response = await fetch('/getRoles');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const roles = await response.json();
            roleSelect.innerHTML = '<option value="">Seleccione un rol</option>';
            roles.forEach(role => {
                const option = document.createElement('option');
                option.value = role.id;
                option.textContent = role.rolName;
                roleSelect.appendChild(option);
            })
        } catch (error) {
            console.error('Error fetching roles:', error);
        }
    }

    loadRoles();

    //create 

    async function createUser()
    {
        try {
            let userData = {
                name: userName.value,
                email: email.value,
                password: password.value,
                rol_id: roleSelect.value,
                state_id: 1
            };

            if (!userData.name || !userData.email || !userData.password || !userData.rol_id) {
                Swal.fire('Error', 'Por favor, complete todos los campos', 'error');
                return;
            }

            const response = await fetch('/users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(userData)
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            let result = await response.json();
            if (result.success) {
                Swal.fire('Éxito', 'Usuario creado exitosamente', 'success');
                loadUsers();
                closeModal();
            } else {
                Swal.fire('Error', 'Hubo un problema al crear el usuario', 'error');
            }
            
        } catch (error) {
            console.error('Error creating user:', error);
        }
    }

    async function updateUser()
    {
        try {
            let userData = {
                name: userName.value,
                email: email.value,
                password: password.value,
                rol_id: roleSelect.value
            };

            if (!userData.name || !userData.email || !userData.rol_id) {
                Swal.fire('Error', 'Por favor, complete todos los campos', 'error');
                return;
            }

            const response = await fetch(`/users/${currentUserId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(userData)
            });

            const result = await response.json();
            if (result.success) {
                Swal.fire('Éxito', 'Usuario actualizado exitosamente', 'success');
                loadUsers();
                closeModal();
            }else {
                Swal.fire('Error', 'Hubo un problema al actualizar el usuario', 'error');
            }
        } catch (error) {
            console.error('Error updating user:', error);
        }
    }

    userForm.addEventListener('submit', function(event) {
        event.preventDefault();
        if (option === 'create') {
            createUser();
        }
        if (option === 'edit') {
            updateUser();
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
    const table = document.getElementById('usersTable');
    if (table) {
        new DataTable(table, {
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json'
            }
        });
    }
});



function togglePassword() {
    const password = document.getElementById('password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');

    if (password.type === 'password') {
        password.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        password.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}

window.togglePassword = togglePassword;

