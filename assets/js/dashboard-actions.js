document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('toggle-upload-form');
    const formContainer = document.getElementById('upload-form-container');

    if (toggleButton && formContainer) {
        toggleButton.addEventListener('click', () => {
            formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
        });
    }
});
