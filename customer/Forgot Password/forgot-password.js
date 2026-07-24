const feedbackMessage = document.getElementById('feedback-message');
const feedbackText = document.getElementById('feedback-text');
const emailStep = document.getElementById('email-step');
const codeStep = document.getElementById('code-step');
const resetStep = document.getElementById('reset-step');
const successMessage = document.getElementById('success-message');
const demoCode = document.getElementById('demo-code');
const toast = document.getElementById('toast');

let userEmail = '';
let verificationCode = '';

function showFeedback(message, isError = true) {
    feedbackMessage.classList.remove('hidden');
    feedbackText.textContent = message;
    feedbackMessage.classList.toggle('login-error', isError);
    feedbackMessage.classList.toggle('success-message', !isError);
    if (!isError) {
        successMessage.classList.add('hidden');
    }
}

function showToast(message, type = 'success') {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.remove('success', 'error');
    toast.classList.add('show', type);
    clearTimeout(toast.hideTimeout);
    toast.hideTimeout = setTimeout(() => {
        toast.classList.remove('show');
    }, 2600);
}

function hideFeedback() {
    feedbackMessage.classList.add('hidden');
}

function createCode() {
    return Math.floor(100000 + Math.random() * 900000).toString();
}

function showStep(step) {
    emailStep.classList.toggle('hidden', step !== 'email');
    codeStep.classList.toggle('hidden', step !== 'code');
    resetStep.classList.toggle('hidden', step !== 'reset');
}

function setSuccessMessage(message) {
    successMessage.textContent = message;
    successMessage.classList.remove('hidden');
}

function setErrorMessage(message) {
    feedbackMessage.classList.remove('hidden');
    feedbackText.textContent = message;
    feedbackMessage.classList.add('login-error');
    feedbackMessage.classList.remove('success-message');
}

async function postJson(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });
    return response.json();
}

const emailForm = document.getElementById('email-form');
const codeForm = document.getElementById('code-form');
const resetForm = document.getElementById('reset-form');

emailForm.addEventListener('submit', async function (event) {
    event.preventDefault();
    hideFeedback();

    const emailInput = document.getElementById('email');
    const emailValue = emailInput.value.trim();
    if (!emailValue) {
        setErrorMessage('Please enter your email address.');
        return;
    }

    let result;
    try {
        result = await postJson('forgot-password-process.php', { action: 'check_email', email: emailValue });
    } catch (error) {
        setErrorMessage('Unable to verify your email right now. Please try again.');
        return;
    }
    if (!result || result.status !== 'ok') {
        setErrorMessage(result?.message || 'Unable to verify email right now.');
        return;
    }

    userEmail = emailValue;
    verificationCode = createCode();
    demoCode.textContent = verificationCode;
    showStep('code');
    showFeedback('Verification code generated. Enter it to continue.', false);
});

codeForm.addEventListener('submit', function (event) {
    event.preventDefault();
    hideFeedback();

    const codeInput = document.getElementById('verification-code');
    const codeValue = codeInput.value.trim();
    if (codeValue !== verificationCode) {
        setErrorMessage('The verification code is incorrect.');
        return;
    }

    showStep('reset');
    showFeedback('Code verified. Set your new password below.', false);
});

resetForm.addEventListener('submit', async function (event) {
    event.preventDefault();
    hideFeedback();

    const passwordInput = document.getElementById('new-password');
    const confirmInput = document.getElementById('confirm-password');
    const passwordValue = passwordInput.value;
    const confirmValue = confirmInput.value;

    if (passwordValue.length < 8) {
        setErrorMessage('Password must be at least 8 characters.');
        return;
    }

    if (passwordValue !== confirmValue) {
        setErrorMessage('Passwords do not match.');
        return;
    }

    const result = await postJson('forgot-password-process.php', {
        action: 'reset_password',
        email: userEmail,
        password: passwordValue
    });

    if (!result || result.status !== 'ok') {
        setErrorMessage(result?.message || 'Unable to reset password right now.');
        return;
    }

    showToast('Your password has been changed. Redirecting to login...', 'success');
    showStep('email');
    emailForm.reset();
    codeForm.reset();
    resetForm.reset();
    verificationCode = '';

    setTimeout(function () {
        window.location.href = '/tcgzone/customer/Login Page/login.html';
    }, 2200);
});

showStep('email');
