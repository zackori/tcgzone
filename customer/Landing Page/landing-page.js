    /* ==========================================================
    tcg.name — custom JS
    Bootstrap's own bundle handles the navbar collapse toggler
    and dropdown menus automatically (data-bs-* attributes in
    the HTML). Everything below is custom behaviour.
    ========================================================== */

    document.addEventListener('DOMContentLoaded', function () {

    /* ------------------------------------------------------
        1) GENERIC ARROW SLIDERS
        Any button with [data-slider] scrolls the matching
        track (#id) left/right by one "page" of visible width.
        Used by: Featured Cards, Collections.
    ------------------------------------------------------ */
    document.querySelectorAll('[data-slider]').forEach(function (btn) {
        btn.addEventListener('click', function () {
        var track = document.getElementById(btn.getAttribute('data-slider'));
        if (!track) return;
        var dir = parseInt(btn.getAttribute('data-dir'), 10) || 1;
        var firstItem = track.querySelector(':scope > *');
        var step = firstItem ? (firstItem.getBoundingClientRect().width + 20) : track.clientWidth * 0.8;
        track.scrollBy({ left: dir * step * 2, behavior: 'smooth' });
        });
    });

    /* ------------------------------------------------------
        2) UPCOMING PACKS — SYNCED IMAGE + DESCRIPTION SLIDER
        One data source drives both the image panel and the
        text panel, so every slide shows its own matching
        description, title and release date.
    ------------------------------------------------------ */
    var packs = [
        {
        img: '/tcgzone/image14.jpg',
        title: 'Obsidian Flames Booster Box',
        desc: "Charizard ex unleashes fiery dark powers as the Terastal phenomenon transforms Pokémon like Tyranitar, Eiscue, and Vespiquen into new types. Dragonite ex and Greedent ex also rise to the challenge in this action-packed expansion.",
        date: 'June 28, 2026'
        },
        {
        img: '/tcgzone/image13.jpg',
        title: 'Chaos Rising Booster Bundle',
        desc: "Chaos erupts as Mega Floette ex threatens the city. Mega Greninja ex joins forces with Mega Pyroar ex and Mega Dragalge ex to fight back in the Pokémon TCG: Mega Evolution—Chaos Rising expansion.",
        date: 'July 19, 2026'
        },
        {
        img: '/tcgzone/image15.jpg',
        title: '30th Celebration Elite Trainer Box',
        desc: 'Commemorate 30 years of joy with fan-favorite Pokémon spanning three dazzling decades! Celebrate this magnificent milestone with the mighty presences of Mewtwo ex and Mew ex, followed by a wealth of allies like Umbreon ex, Salamence ex, and Greninja ex.',
        date: 'September 3, 2026'
        },
        {
        img: '/tcgzone/image16.jpg',
        title: 'Evolving Skies Booster Box',
        desc: "Each Sword & Shield—Evolving Skies Booster Box includes 36 booster packs. Featuring sought-after alternate-art cards like Rayquaza VMAX, Dragonite V, and the Eeveelution VMAXs, it's one of the most popular sets in the Sword & Shield series.",
        date: 'December 23, 2026'
        }
    ];

    var packImage = document.getElementById('pack-image');
    var packTitle = document.getElementById('pack-title');
    var packDesc  = document.getElementById('pack-desc');
    var packDate  = document.getElementById('pack-date');
    var packDots  = document.getElementById('pack-dots');
    var packPrev  = document.getElementById('pack-prev');
    var packNext  = document.getElementById('pack-next');

    var packIndex = 0;

    function buildDots() {
        packDots.innerHTML = '';
        packs.forEach(function (_, i) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'pack-dot' + (i === packIndex ? ' active' : '');
        dot.setAttribute('aria-label', 'Show pack ' + (i + 1));
        dot.addEventListener('click', function () {
            packIndex = i;
            renderPack();
        });
        packDots.appendChild(dot);
        });
    }

    function renderPack() {
        var current = packs[packIndex];

        // Swap the art class instead of using an <img>, so the
        // whole tile is styled purely through CSS gradients.
        packImage.style.backgroundImage = 'url(' + current.img + ')';

        // Fade the text panel out, swap content, fade back in —
        // this is what makes the description feel tied to the slide.
        packTitle.style.opacity = 0;
        packDesc.style.opacity = 0;
        packDate.style.opacity = 0;

        setTimeout(function () {
        packTitle.textContent = current.title;
        packDesc.textContent = current.desc;
        packDate.textContent = current.date;
        packTitle.style.transition = 'opacity .25s ease';
        packDesc.style.transition = 'opacity .25s ease';
        packDate.style.transition = 'opacity .25s ease';
        packTitle.style.opacity = 1;
        packDesc.style.opacity = 1;
        packDate.style.opacity = 1;
        }, 150);

        buildDots();
    }

    if (packImage && packPrev && packNext) {
        packPrev.addEventListener('click', function () {
        packIndex = (packIndex - 1 + packs.length) % packs.length;
        renderPack();
        });
        packNext.addEventListener('click', function () {
        packIndex = (packIndex + 1) % packs.length;
        renderPack();
        });
        renderPack();
    }

    /* ------------------------------------------------------
        3) NEWSLETTER FORM (footer)
        Prevents a real page navigation in this demo and gives
        lightweight confirmation feedback.
    ------------------------------------------------------ */
    var newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var input = newsletterForm.querySelector('input[type="email"]');
        var btn = newsletterForm.querySelector('button');
        var originalLabel = btn.textContent;
        btn.textContent = 'Subscribed!';
        btn.disabled = true;
        setTimeout(function () {
            btn.textContent = originalLabel;
            btn.disabled = false;
            input.value = '';
        }, 2000);
        });
    }

    /* ------------------------------------------------------
        4) NAVBAR: close the mobile menu after a link is tapped
        (Bootstrap doesn't do this automatically for in-page
        anchors, so the collapsed menu would otherwise stay open).
    ------------------------------------------------------ */
    var navCollapseEl = document.getElementById('mainNav');
    if (navCollapseEl) {
        var bsCollapse = new bootstrap.Collapse(navCollapseEl, { toggle: false });
        navCollapseEl.querySelectorAll('.nav-link, .dropdown-item').forEach(function (link) {
        link.addEventListener('click', function () {
            if (navCollapseEl.classList.contains('show') && window.innerWidth < 992) {
            bsCollapse.hide();
            }
        });
        });
    }

    });
