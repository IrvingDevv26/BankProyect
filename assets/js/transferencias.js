// assets/js/transferencias.js

document.addEventListener('DOMContentLoaded', function() {

    const formTransferencia = document.getElementById('form-transferencia');

    // Verificamos si el formulario existe en esta página para evitar errores
    if (formTransferencia) {

        formTransferencia.addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('destinatario').value;
            const monto = document.getElementById('monto').value;
            const btnSubmit = this.querySelector('button[type="submit"]');

            if(!confirm(`¿Estás seguro de transferir $${monto} a ${email}?`)) return;

            // Feedback visual
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Enviando...';

            // Petición al Backend
            fetch('index.php?action=api_transferir', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email, monto: monto })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        alert('✅ ' + data.msg);

                        const saldoDisplay = document.getElementById('saldo-display');
                        // Formatear moneda (simula el number_format de PHP)
                        saldoDisplay.innerText = parseFloat(data.nuevo_saldo).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                        saldoDisplay.classList.add('saldo-update');
                        setTimeout(() => saldoDisplay.classList.remove('saldo-update'), 1000);

                        document.getElementById('form-transferencia').reset();

                        setTimeout(() => location.reload(), 1500);
                    } else {
                        alert('❌ Error: ' + data.msg);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error de conexión.');
                })
                .finally(() => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = 'Enviar Dinero';
                });
        });
    }
});