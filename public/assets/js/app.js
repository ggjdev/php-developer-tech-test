document.addEventListener('submit', function (e) {
    const element = e.target;

    if (element.tagName === 'FORM') {
        const submitButtons = element.querySelectorAll('input[type="submit"]');

        // Disable all submit buttons in the form
        submitButtons.forEach(function (button) {
            button.value = 'Submitting...';
            button.setAttribute('disabled', 'disabled');
        });
    }
});
