document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn   = document.getElementById('toggle-detail-form');
    const detailForm  = document.getElementById('detail-form');

    if (toggleBtn && detailForm) {
        toggleBtn.addEventListener('click', () => {
            detailForm.classList.toggle('hidden');
        });
    }
});
