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

document.addEventListener('click', function (e) {
    const element = e.target;

    if (element.tagName === 'BUTTON' && element.classList.contains('matches__match__more')) {
        e.preventDefault();

        const parent = element.closest('.matches__match');

        if (parent) {
            const details = parent.querySelector('.match__match__details');

            if (details) {
                if (details.style.display === 'none') {
                    details.style.display = 'block';
                    element.textContent = 'less';
                } else {
                    details.style.display = 'none';
                    element.textContent = 'more';
                }
            }
        }
    }
});
