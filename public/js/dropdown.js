document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('menu-button');
    const dropdownMenu = document.getElementById('dropdown-menu');

    // Toggle dropdown visibility on button click
    menuButton.addEventListener('click', function() {
        dropdownMenu.classList.toggle('hidden');
    });

    // Hide dropdown when clicking outside of it
    document.addEventListener('click', function(event) {
        if (!menuButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });

    // Handle project selection
    document.querySelectorAll('.dropdown-project-item').forEach(item => {
        item.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default link behavior
            const projectId = this.getAttribute('data-project-id');

            // Set the hidden input value and submit the form
            document.getElementById('project-id').value = projectId;
            document.getElementById('filter-form').submit();
        });
    });
});
