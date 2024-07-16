document.querySelectorAll('.toggle-visibility').forEach(button => {
    button.onclick = function() {
        const index = this.getAttribute('data-index');
        const maskedText = this.parentNode.querySelector('.masked-text');
        const actualText = this.parentNode.querySelector('.actual-text');
        const icon = this.querySelector('i');

        if (actualText.style.display === "none") {
            actualText.style.display = "inline";
            maskedText.style.display = "none";
            icon.classList.remove('bi-eye-fille');
            icon.classList.add('bi-eye-slash-fill');
        } else {
            actualText.style.display = "none";
            maskedText.style.display = "inline";
            icon.classList.remove('bi-eye-slash-fill');
            icon.classList.add('bi-eye-fille');
        }
    };
});
