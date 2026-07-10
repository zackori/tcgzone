/* ============================================================
    ACCOUNT TAB LOGIC
    - Wires up ONLY the navbar element marked data-nav="account".
        Sign In, cart, and every other nav link are left untouched.
    - Username comes from what was set on the Sign In tab and is locked.
    - Email / Phone / Date of Birth stay locked until "Change" is
        clicked; clicking "Cancel" while editing reverts that field back
        to whatever it was before Change was clicked (fixes: no way to
        back out of a misclick).
    - The ONLY way to close this tab is clicking "Save".
    - On Save: values are stored, then the user is redirected to
        index.html.
   ============================================================ */

(function () {
    const overlay = document.getElementById("profileOverlay");
    if (!overlay) return; // account tab markup isn't on this page

    const form = document.getElementById("profileForm");
    const usernameValueEl = document.getElementById("usernameValue");
    const nameInput = document.getElementById("nameInput");

    // Prefill from whatever was saved previously (sign in tab / a prior save)
    function loadProfile() {
        const saved = JSON.parse(localStorage.getItem("tcgzone_profile") || "{}");

        // Username is set once at sign in and never edited here
        const username = saved.username || localStorage.getItem("tcgzone_username") || "lleve";
        usernameValueEl.textContent = username;

        nameInput.value = saved.name || "";

        setFieldValue("email", saved.email || "lleve@gmail.com");
        setFieldValue("phone", saved.phone || "09XX-XXX-XXXX");
        setFieldValue("dob", saved.dob || "2005-02-12", saved.dobDisplay || "12-02-2005");
        setAddressValue(saved);

        if (saved.gender) {
            const radio = form.querySelector(`input[name="gender"][value="${saved.gender}"]`);
            if (radio) radio.checked = true;
        }
    }

    function setFieldValue(field, inputValue, displayValue) {
        const wrapper = form.querySelector(`.editable-field[data-field="${field}"]`);
        const staticEl = wrapper.querySelector(`[data-static="${field}"]`);
        const inputEl = wrapper.querySelector(`[data-input="${field}"]`);
        inputEl.value = inputValue;
        staticEl.textContent = displayValue || inputValue;
        wrapper.classList.remove("editing"); // always start locked
    }

    // Shipping Address is four separate inputs sharing one Change/Cancel
    // toggle. The static text is assembled from whichever parts are set.
    function setAddressValue(saved) {
        const wrapper = form.querySelector('.editable-field[data-field="address"]');
        const staticEl = wrapper.querySelector('[data-static="address"]');

        const details = saved.addressDetails || "";
        const city = saved.addressCity || "";
        const province = saved.addressProvince || "";
        const zip = saved.addressZip || "";

        wrapper.querySelector('[data-input="addressDetails"]').value = details;
        wrapper.querySelector('[data-input="addressCity"]').value = city;
        wrapper.querySelector('[data-input="addressProvince"]').value = province;
        wrapper.querySelector('[data-input="addressZip"]').value = zip;

        const parts = [details, city, province, zip].filter(Boolean);
        staticEl.textContent = parts.length ? parts.join(", ") : "No address on file";

        wrapper.classList.remove("editing"); // always start locked
    }

    // "Change" reveals the input(s) and snapshots their current values so
    // Cancel can restore them exactly. Works for both single-input fields
    // (email, phone, dob) and multi-input groups (address).
    form.querySelectorAll(".change-link").forEach((link) => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            const field = link.dataset.target;
            const wrapper = form.querySelector(`.editable-field[data-field="${field}"]`);
            const inputs = wrapper.querySelectorAll("[data-input]");

            const revertData = {};
            inputs.forEach((input) => {
                revertData[input.dataset.input] = input.value;
            });
            wrapper.dataset.revert = JSON.stringify(revertData);

            wrapper.classList.add("editing");
            if (inputs[0]) inputs[0].focus();
        });
    });

    // "Cancel" undoes an in-progress edit — restores every input in the
    // field's wrapper to its pre-edit value and locks it again.
    form.querySelectorAll(".cancel-link").forEach((link) => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            const field = link.dataset.target;
            const wrapper = form.querySelector(`.editable-field[data-field="${field}"]`);

            if (wrapper.dataset.revert) {
                const revertData = JSON.parse(wrapper.dataset.revert);
                Object.keys(revertData).forEach((key) => {
                    const input = wrapper.querySelector(`[data-input="${key}"]`);
                    if (input) input.value = revertData[key];
                });
            }
            wrapper.classList.remove("editing");
        });
    });

    function openProfileTab() {
        loadProfile();
        overlay.style.display = "flex";
        document.body.style.overflow = "hidden";
    }

    // Deliberately no closeProfileTab() tied to backdrop/Escape —
    // per spec this tab only closes via Save.

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const email = form.querySelector('[data-input="email"]').value.trim();
        const phone = form.querySelector('[data-input="phone"]').value.trim();
        const dobRaw = form.querySelector('[data-input="dob"]').value;
        const addressDetails = form.querySelector('[data-input="addressDetails"]').value.trim();
        const addressCity = form.querySelector('[data-input="addressCity"]').value.trim();
        const addressProvince = form.querySelector('[data-input="addressProvince"]').value.trim();
        const addressZip = form.querySelector('[data-input="addressZip"]').value.trim();
        const gender = form.querySelector('input[name="gender"]:checked').value;

        const profile = {
            username: usernameValueEl.textContent.trim(),
            name: nameInput.value.trim(),
            email: email,
            phone: phone,
            dob: dobRaw,
            dobDisplay: formatDateForDisplay(dobRaw),
            addressDetails: addressDetails,
            addressCity: addressCity,
            addressProvince: addressProvince,
            addressZip: addressZip,
            gender: gender
        };

        localStorage.setItem("tcgzone_profile", JSON.stringify(profile));

        // Redirect to the landing page — this is the only exit point.
        // Absolute path so it resolves the same from any page (Shop,
        // Login, etc.), not just from within index.html's own folder.
        window.location.href = "/tcgzone/customer/Landing Page/index.html";
    });

    function formatDateForDisplay(isoDate) {
        if (!isoDate) return "";
        const [year, month, day] = isoDate.split("-");
        return `${day}-${month}-${year}`;
    }

    // Only the account icon opens this tab — every other nav link
    // (Sign In, cart, sub-links) keeps whatever behavior it already has.
    const accountTrigger = document.querySelector('[data-nav="account"]');
    if (accountTrigger) {
        accountTrigger.addEventListener("click", (e) => {
            e.preventDefault();
            openProfileTab();
        });
    }

    window.openProfileTab = openProfileTab;
})();