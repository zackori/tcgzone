const sellForm = document.getElementById('sellRequestForm');

function showNotification(message, isError = false) {
    let notification = document.querySelector('.notification');

    if (!notification) {
        notification = document.createElement('div');
        notification.className = 'notification';
        document.body.appendChild(notification);
    }

    notification.textContent = message;
    notification.classList.toggle('error', isError);
    notification.classList.add('show');

    clearTimeout(notification.hideTimer);
    notification.hideTimer = setTimeout(() => {
        notification.classList.remove('show');
    }, 2800);
}

sellForm?.addEventListener('submit', async function (event) {
    event.preventDefault();

    if (!isLoggedIn) {
        window.location.href = '/tcgzone/customer/Login Page/login.html';
        return;
    }

    const formData = new FormData(sellForm);
    formData.append('action', 'submit_sell_request');

    try {
        const response = await fetch('submit_sell_request.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (!result || result.status !== 'ok') {
            showNotification(result?.message || 'Unable to submit your request now.', true);
            return;
        }

        showNotification('Sell request submitted successfully. Admins will review it soon.');
        sellForm.reset();
    } catch (error) {
        console.error(error);
        showNotification('Something went wrong. Please try again.', true);
    }
});
