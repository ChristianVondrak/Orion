import $ from 'jquery';
import 'daterangepicker';

document.addEventListener('DOMContentLoaded', () => {
    const backdrop     = document.getElementById('global-backdrop');
    const dropdownMenu = document.getElementById('dropdown-menu');
    const menuButton   = document.getElementById('menu-button');
    const dateInput    = $('#kt_daterangepicker_4');
    const rateForm     = document.getElementById('rate-form');
    const modalWrapper = rateForm.querySelector('.modal-wrapper');
    const closeButtons = rateForm.querySelectorAll('[data-close-modal]');

    const showBackdrop = () => backdrop.style.display = 'block';
    const hideBackdrop = () => backdrop.style.display = 'none';

    // Toggle dropdown de Proyectos
    menuButton.addEventListener('click', () => {
        const opened = dropdownMenu.classList.toggle('hidden') === false;
        opened ? showBackdrop() : hideBackdrop();
    });

    // Click en backdrop cierra dropdown y modal
    backdrop.addEventListener('click', () => {
        dropdownMenu.classList.add('hidden');
        rateForm.classList.add('hidden');
        hideBackdrop();
    });

    // Inicializar datepicker con backdrop
    dateInput.daterangepicker();
    dateInput.on('show.daterangepicker', showBackdrop);
    dateInput.on('hide.daterangepicker cancel.daterangepicker apply.daterangepicker', hideBackdrop);

    // Abrir modal de tarifas
    document.getElementById('toggle-rate-form').addEventListener('click', () => {
        rateForm.classList.remove('hidden');
        showBackdrop();
    });

    // Cerrar modal con botones de cierre
    closeButtons.forEach(btn => btn.addEventListener('click', () => {
        rateForm.classList.add('hidden');
        hideBackdrop();
    }));
});

// Función global para alternar inputs de tarifa
window.toggleRateInputs = function(projectId) {
    const hourly = document.getElementById(`hourly-rate-${projectId}`);
    const flat   = document.getElementById(`flat-rate-${projectId}`);
    const type   = document.querySelector(`input[name="payment_types[${projectId}]"]`).value;
    if (type === 'hourly') {
        hourly.classList.remove('hidden');
        flat.classList.add('hidden');
    } else {
        flat.classList.remove('hidden');
        hourly.classList.add('hidden');
    }
};
