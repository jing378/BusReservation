/**
 * BusReserve landing page interactions.
 * Everything here is progressive enhancement: the page is fully readable without it.
 */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    var $ = function (selector, root) { return (root || document).querySelector(selector); };
    var $$ = function (selector, root) { return Array.prototype.slice.call((root || document).querySelectorAll(selector)); };
    var peso = function (value) {
        return value.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    /* ---------- Scroll reveal ---------- */
    function revealDone(element) {
        // Drop the reveal classes once the entrance finishes so hover effects can take over.
        var delay = parseInt(getComputedStyle(element).getPropertyValue('--delay'), 10) || 0;
        setTimeout(function () { element.classList.remove('reveal', 'reveal-scale', 'is-visible'); }, 900 + delay);
    }

    function showAll(elements) {
        elements.forEach(function (element) { element.classList.add('is-visible'); });
    }

    var revealItems = $$('.reveal');

    if (reduceMotion || !('IntersectionObserver' in window)) {
        showAll(revealItems);
    } else {
        var revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
                revealDone(entry.target);

                $$('[data-count]', entry.target).forEach(countUp);
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

        revealItems.forEach(function (element) { revealObserver.observe(element); });
    }

    /* ---------- Count-up numbers ---------- */
    function countUp(element) {
        var target = parseInt(element.getAttribute('data-count'), 10) || 0;

        if (reduceMotion || target === 0) {
            element.textContent = target;
            return;
        }

        var start = performance.now();
        var duration = 1400;

        (function tick(now) {
            var progress = Math.min(1, (now - start) / duration);
            var eased = 1 - Math.pow(1 - progress, 3);
            element.textContent = Math.round(target * eased);

            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        })(start);
    }

    /* ---------- Nav: scrolled state, progress bar, active section, back to top ---------- */
    var nav = $('.nav');
    var progress = $('#scrollProgress');
    var toTop = $('#toTop');
    var ticking = false;

    function onScroll() {
        var scrolled = window.scrollY;
        var max = document.documentElement.scrollHeight - window.innerHeight;

        nav.classList.toggle('is-scrolled', scrolled > 10);
        toTop.classList.toggle('is-visible', scrolled > 600);

        if (progress) {
            progress.style.transform = 'scaleX(' + (max > 0 ? scrolled / max : 0) + ')';
        }

        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (!ticking) {
            requestAnimationFrame(onScroll);
            ticking = true;
        }
    }, { passive: true });
    onScroll();

    toTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });

    var navLinks = $$('.nav-links a');

    if ('IntersectionObserver' in window) {
        var sectionObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    navLinks.forEach(function (link) {
                        link.classList.toggle('is-active', link.getAttribute('href') === '#' + entry.target.id);
                    });
                }
            });
        }, { rootMargin: '-45% 0px -50% 0px' });

        navLinks.forEach(function (link) {
            var section = $(link.getAttribute('href'));
            if (section) {
                sectionObserver.observe(section);
            }
        });
    }

    /* ---------- Mobile menu ---------- */
    var navToggle = $('#navToggle');
    var navMenu = $('#navLinks');

    function setMenu(open) {
        navMenu.classList.toggle('open', open);
        navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        navToggle.innerHTML = open ? '<i class="fas fa-xmark"></i>' : '<i class="fas fa-bars"></i>';
    }

    navToggle.addEventListener('click', function () { setMenu(!navMenu.classList.contains('open')); });
    navMenu.addEventListener('click', function (event) {
        if (event.target.closest('a')) {
            setMenu(false);
        }
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            setMenu(false);
        }
    });

    /* ---------- Rotating destination in the headline ---------- */
    var rotator = $('#rotator');

    if (rotator && !reduceMotion) {
        var words = [];
        try { words = JSON.parse(rotator.getAttribute('data-words')) || []; } catch (error) { words = []; }

        if (words.length > 1) {
            var wordIndex = 0;

            setInterval(function () {
                if (document.hidden) {
                    return;
                }
                rotator.classList.add('is-out');

                setTimeout(function () {
                    wordIndex = (wordIndex + 1) % words.length;
                    rotator.textContent = words[wordIndex];
                    rotator.classList.remove('is-out');
                    rotator.classList.add('is-in');
                    void rotator.offsetWidth; // restart the transition from below
                    rotator.classList.remove('is-in');
                }, 350);
            }, 2600);
        }
    }

    /* ---------- Ticket: 3D tilt and seat picker ---------- */
    var tiltArea = $('#ticketTilt');
    var ticket = tiltArea ? $('.ticket', tiltArea) : null;

    if (ticket && canHover && !reduceMotion) {
        tiltArea.addEventListener('mousemove', function (event) {
            var rect = ticket.getBoundingClientRect();
            var x = (event.clientX - rect.left) / rect.width;
            var y = (event.clientY - rect.top) / rect.height;

            ticket.classList.add('is-tilting');
            ticket.style.setProperty('--ry', ((x - 0.5) * 14).toFixed(2) + 'deg');
            ticket.style.setProperty('--rx', ((0.5 - y) * 12).toFixed(2) + 'deg');
            ticket.style.setProperty('--mx', (x * 100).toFixed(1) + '%');
            ticket.style.setProperty('--my', (y * 100).toFixed(1) + '%');
        });

        tiltArea.addEventListener('mouseleave', function () {
            ticket.classList.remove('is-tilting');
            ticket.style.setProperty('--rx', '0deg');
            ticket.style.setProperty('--ry', '0deg');
        });
    }

    var seatPreview = $('#seatPreview');
    var seatLabel = $('#seatLabel');

    if (seatPreview) {
        seatPreview.addEventListener('click', function (event) {
            var seat = event.target.closest('.seat');
            if (!seat) {
                return;
            }

            if (seat.classList.contains('seat-taken')) {
                seat.classList.remove('is-shake');
                void seat.offsetWidth;
                seat.classList.add('is-shake');
                return;
            }

            $$('.seat-mine', seatPreview).forEach(function (mine) {
                mine.classList.remove('seat-mine');
                mine.setAttribute('aria-pressed', 'false');
            });
            seat.classList.add('seat-mine');
            seat.setAttribute('aria-pressed', 'true');

            seatLabel.textContent = 'S' + seat.getAttribute('data-seat');
            seatLabel.classList.remove('is-pop');
            void seatLabel.offsetWidth;
            seatLabel.classList.add('is-pop');
        });
    }

    /* ---------- Cursor spotlight on cards ---------- */
    if (canHover) {
        document.addEventListener('pointermove', function (event) {
            var card = event.target.closest && event.target.closest('.spotlight');
            if (!card) {
                return;
            }
            var rect = card.getBoundingClientRect();
            card.style.setProperty('--sx', (event.clientX - rect.left) + 'px');
            card.style.setProperty('--sy', (event.clientY - rect.top) + 'px');
        });
    }

    /* ---------- Button ripple ---------- */
    if (!reduceMotion) {
        document.addEventListener('pointerdown', function (event) {
            var button = event.target.closest('.btn');
            if (!button) {
                return;
            }
            var rect = button.getBoundingClientRect();
            var size = Math.max(rect.width, rect.height);
            var ripple = document.createElement('span');

            ripple.className = 'ripple';
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (event.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (event.clientY - rect.top - size / 2) + 'px';
            button.appendChild(ripple);
            setTimeout(function () { ripple.remove(); }, 650);
        });
    }

    /* ---------- Fare calculator ---------- */
    var calc = $('#fareCalc');

    if (calc) {
        var routes = [];
        try { routes = JSON.parse(calc.getAttribute('data-routes')) || []; } catch (error) { routes = []; }

        var discountPercent = parseFloat(calc.getAttribute('data-discount')) || 0;
        var routeSelect = $('#calcRoute');
        var segments = $$('.segment', calc);
        var pill = $('.segment-pill', calc);
        var countOutput = $('#calcCount');
        var countEcho = $('#calcCountEcho');
        var minus = $('#calcMinus');
        var plus = $('#calcPlus');
        var totalEl = $('#calcTotal');
        var totalBox = totalEl.parentNode;
        var baseEl = $('#calcBase');
        var discountEl = $('#calcDiscount');
        var discountRow = $('#calcDiscountRow');
        var state = { route: 0, discounted: false, passengers: 1 };
        var shownTotal = 0;
        var animation = null;

        function animateTotal(to) {
            if (animation) {
                cancelAnimationFrame(animation);
            }
            if (reduceMotion) {
                shownTotal = to;
                totalEl.textContent = peso(to);
                return;
            }

            var from = shownTotal;
            var start = performance.now();

            totalBox.classList.remove('is-bump');
            void totalBox.offsetWidth;
            totalBox.classList.add('is-bump');

            (function tick(now) {
                var progress = Math.min(1, (now - start) / 500);
                var eased = 1 - Math.pow(1 - progress, 3);
                shownTotal = from + (to - from) * eased;
                totalEl.textContent = peso(shownTotal);

                if (progress < 1) {
                    animation = requestAnimationFrame(tick);
                }
            })(start);
        }

        function render() {
            var route = routes[state.route] || { price: 0 };
            var base = route.price;
            var discount = state.discounted ? Math.round(base * discountPercent) / 100 : 0;
            var total = (base - discount) * state.passengers;

            baseEl.textContent = '₱' + peso(base);
            discountEl.textContent = '−₱' + peso(discount);
            discountRow.classList.toggle('is-hidden', !state.discounted);
            countOutput.textContent = state.passengers;
            countEcho.textContent = state.passengers;
            minus.disabled = state.passengers <= 1;
            plus.disabled = state.passengers >= 10;

            animateTotal(total);
        }

        routeSelect.addEventListener('change', function () {
            state.route = parseInt(routeSelect.value, 10) || 0;
            render();
        });

        segments.forEach(function (segment, index) {
            segment.addEventListener('click', function () {
                segments.forEach(function (other) {
                    other.classList.toggle('is-active', other === segment);
                    other.setAttribute('aria-checked', other === segment ? 'true' : 'false');
                });
                pill.style.transform = 'translateX(calc(' + index + ' * (100% + 4px)))';
                state.discounted = segment.getAttribute('data-discounted') === '1';
                render();
            });
        });

        minus.addEventListener('click', function () {
            state.passengers = Math.max(1, state.passengers - 1);
            render();
        });
        plus.addEventListener('click', function () {
            state.passengers = Math.min(10, state.passengers + 1);
            render();
        });

        render();
    }

    /* ---------- Route search & "show all" ---------- */
    var routeList = $('#routeList');
    var routeSearch = $('#routeSearch');

    if (routeList && routeSearch) {
        var rows = $$('.route-row', routeList);
        var noMatch = $('#routeNoMatch');
        var queryEcho = $('#routeQuery');
        var moreButton = $('#routeMore');

        rows.forEach(function (row) {
            var strong = $('.route-name strong', row);
            row.originalLabel = strong.textContent;
        });

        function escapeHtml(text) {
            return text.replace(/[&<>"']/g, function (char) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char];
            });
        }

        routeSearch.addEventListener('input', function () {
            var query = routeSearch.value.trim().toLowerCase();
            var visible = 0;

            routeList.classList.toggle('is-searching', query !== '');

            rows.forEach(function (row) {
                var strong = $('.route-name strong', row);
                var matches = query === '' || row.getAttribute('data-search').indexOf(query) !== -1;

                row.classList.toggle('is-filtered', !matches);
                row.classList.remove('reveal', 'is-visible');

                if (matches && query !== '') {
                    var label = row.originalLabel;
                    var start = label.toLowerCase().indexOf(query);
                    strong.innerHTML = start === -1
                        ? escapeHtml(label)
                        : escapeHtml(label.slice(0, start)) + '<mark>' + escapeHtml(label.slice(start, start + query.length)) + '</mark>' + escapeHtml(label.slice(start + query.length));
                } else {
                    strong.textContent = row.originalLabel;
                }

                if (matches) {
                    visible++;
                }
            });

            noMatch.hidden = visible !== 0;
            queryEcho.textContent = routeSearch.value.trim();

            if (moreButton) {
                moreButton.parentNode.hidden = query !== '';
            }
        });

        if (moreButton) {
            var moreLabel = moreButton.innerHTML;

            moreButton.addEventListener('click', function () {
                var expanded = routeList.classList.toggle('is-expanded');
                moreButton.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                moreButton.innerHTML = expanded ? 'Show fewer routes <i class="fas fa-chevron-down"></i>' : moreLabel;

                if (expanded) {
                    $$('.route-row.is-extra', routeList).forEach(function (row) { row.classList.add('is-visible'); revealDone(row); });
                }
            });
        }
    }
})();
