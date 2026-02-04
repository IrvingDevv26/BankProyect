document.addEventListener('DOMContentLoaded', function() {

    // Inicializar Modales (Solo si existen en el HTML)
    const depositModalEl = document.getElementById('depositModal');
    const newUserModalEl = document.getElementById('newUserModal'); // Bootstrap lo maneja nativo, pero por si acaso
    const editUserModalEl = document.getElementById('editUserModal');

    let depositModalInstance = depositModalEl ? new bootstrap.Modal(depositModalEl) : null;
    let editUserModalInstance = editUserModalEl ? new bootstrap.Modal(editUserModalEl) : null;


    // =========================================================
    // 1. DELEGACIÓN DE EVENTOS (BOTONES DENTRO DE LA TABLA)
    // =========================================================
    // Usamos delegación (document.body) porque los botones están en una tabla
    document.body.addEventListener('click', function(e) {

        // --- A) BLOQUEAR / DESBLOQUEAR USUARIO ---
        const btnBlock = e.target.closest('.btn-toggle-block');
        if (btnBlock) {
            const userId = btnBlock.dataset.id;
            const row = btnBlock.closest('tr');
            const badge = row.querySelector('.badge-status');

            if (!confirm('¿Seguro que deseas cambiar el estado de este usuario?')) return;

            btnBlock.disabled = true;

            fetch('index.php?action=api_bloquear_usuario', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: userId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        if (data.estado === 'bloqueado') {
                            badge.className = 'badge-status badge bg-danger bg-opacity-10 text-danger rounded-pill px-3';
                            badge.innerHTML = '<i class="bi bi-lock-fill"></i> Bloqueado';
                            btnBlock.innerHTML = '<i class="bi bi-unlock"></i>';
                            btnBlock.title = "Desbloquear Usuario";
                            btnBlock.classList.replace('text-danger', 'text-success');
                        } else {
                            badge.className = 'badge-status badge bg-success bg-opacity-10 text-success rounded-pill px-3';
                            badge.innerHTML = 'Activo';
                            btnBlock.innerHTML = '<i class="bi bi-slash-circle"></i>';
                            btnBlock.title = "Bloquear Usuario";
                            btnBlock.classList.replace('text-success', 'text-danger');
                        }
                    } else {
                        alert('Error: ' + data.msg);
                    }
                })
                .catch(err => console.error(err))
                .finally(() => btnBlock.disabled = false);
        }

        // --- B) ABRIR MODAL DEPÓSITO (CAJERO) ---
        const btnDeposit = e.target.closest('.btn-deposit');
        if (btnDeposit && depositModalInstance) {
            document.getElementById('deposit-user-id').value = btnDeposit.dataset.id;
            document.getElementById('modal-user-name').textContent = btnDeposit.dataset.name;
            document.getElementById('deposit-amount').value = '';
            depositModalInstance.show();
        }

        // --- C) ABRIR MODAL EDITAR ---
        const btnEdit = e.target.closest('.btn-edit-user');
        if (btnEdit && editUserModalInstance) {
            document.getElementById('edit-user-id').value = btnEdit.dataset.id;
            document.getElementById('edit-user-name').value = btnEdit.dataset.name;
            document.getElementById('edit-user-email').value = btnEdit.dataset.email;
            editUserModalInstance.show();
        }
    });


    // =========================================================
    // 2. ENVÍO DE FORMULARIOS (AJAX)
    // =========================================================

    // --- FORMULARIO DE DEPÓSITO ---
    const formDeposit = document.getElementById('form-deposit');
    if (formDeposit) {
        formDeposit.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Procesando...';

            const formData = new FormData(this);
            const data = {
                id: formData.get('user_id'),
                monto: formData.get('monto')
            };

            fetch('index.php?action=api_admin_depositar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
                .then(res => res.json())
                .then(resp => {
                    if (resp.ok) {
                        alert('✅ Depósito realizado con éxito');
                        location.reload();
                    } else {
                        alert('❌ Error: ' + resp.msg);
                    }
                })
                .catch(err => alert('Error de conexión'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    depositModalInstance.hide();
                });
        });
    }

    // --- FORMULARIO NUEVO USUARIO ---
    const formNewUser = document.getElementById('form-new-user');
    if (formNewUser) {
        formNewUser.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = 'Creando...';

            const formData = new FormData(this);

            fetch('index.php?action=api_crear_usuario', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        alert('✅ Usuario creado exitosamente');
                        location.reload();
                    } else {
                        alert('❌ Error: ' + data.msg);
                    }
                })
                .catch(err => alert('Error de conexión'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = 'Crear Usuario y Cuenta';
                });
        });
    }

    // --- FORMULARIO EDITAR USUARIO ---
    const formEditUser = document.getElementById('form-edit-user');
    if (formEditUser) {
        formEditUser.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;

            const formData = new FormData(this);

            fetch('index.php?action=api_editar_usuario', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        alert('✅ Datos actualizados');
                        location.reload();
                    } else {
                        alert('❌ Error: ' + data.msg);
                    }
                })
                .catch(err => alert('Error de conexión'))
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

});