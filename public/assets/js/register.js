document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('registerForm');
    var btn = document.getElementById('registerBtn');
    var pw1 = document.getElementById('password');
    var pw2 = document.getElementById('confirm');
    var t1 = document.getElementById('passwordToggle1');
    var t2 = document.getElementById('passwordToggle2');
    var message = document.getElementById('message');

    var fullName = document.getElementById('fullName');
    var email = document.getElementById('email');
    var phone = document.getElementById('phone');
    var username = document.getElementById('username');
    var userType = document.getElementById('userType');
    var terms = document.getElementById('terms');

    // Helpers
    function getContainer(el) { return el ? el.closest('.input-container') : null; }
    function setStatus(type, text) {
        if (!message) return;
        message.classList.remove('success', 'error', 'show');
        message.classList.add(type);
        var textEl = message.querySelector('.message-text');
        if (textEl) textEl.textContent = text;
        requestAnimationFrame(function () { message.classList.add('show'); });
        clearTimeout(setStatus._t);
        setStatus._t = setTimeout(function () { message.classList.remove('show'); }, 2000);
    }
    function mark(el, ok) {
        var c = getContainer(el);
        if (!c) return;
        if (ok === true) {
            c.style.borderColor = 'rgba(16,185,129,0.6)';
            c.style.boxShadow = '0 0 0 3px rgba(16,185,129,0.15)';
        } else if (ok === false) {
            c.style.borderColor = 'rgba(239,68,68,0.7)';
            c.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.18)';
        } else {
            c.style.borderColor = 'rgba(255,255,255,0.08)';
            c.style.boxShadow = 'none';
        }
    }

    // Toggle eye buttons only when input has content
    function bindToggleVisibility(input, toggleBtn) {
        if (!input || !toggleBtn) return;
        // Hide toggle initially
        toggleBtn.style.display = 'none';
        
        function update() {
            var has = input.value && input.value.length > 0;
            // Use class-based approach with absolute positioning
            if (has) {
                toggleBtn.classList.add('show');
                toggleBtn.style.setProperty('display', 'inline-flex', 'important');
                toggleBtn.style.setProperty('visibility', 'visible', 'important');
                toggleBtn.style.setProperty('opacity', '1', 'important');
                // Preserve transform for centering
                if (!toggleBtn.style.transform || !toggleBtn.style.transform.includes('translateY')) {
                    toggleBtn.style.transform = 'translateY(-50%)';
                }
            } else {
                toggleBtn.classList.remove('show');
                toggleBtn.style.setProperty('display', 'none', 'important');
                toggleBtn.style.setProperty('visibility', 'hidden', 'important');
            }
        }
        update();
        input.addEventListener('input', update);
        input.addEventListener('blur', update);
        input.addEventListener('focus', update);
    }

    function toggle(toggleBtn, input) {
        if (!toggleBtn || !input) return;
        toggleBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var isPw = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPw ? 'text' : 'password');
            toggleBtn.innerHTML = isPw ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
            // Refocus input after toggle
            input.focus();
        });
    }

    toggle(t1, pw1);
    toggle(t2, pw2);
    bindToggleVisibility(pw1, t1);
    bindToggleVisibility(pw2, t2);

    // Field constraints and live validation
    if (fullName) {
        fullName.addEventListener('input', function(){
            var ok = fullName.value.trim().length >= 3;
            mark(fullName, ok || fullName.value.trim()==='');
        });
        fullName.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); email.focus(); }});
        var c = getContainer(fullName); if (c) { c.addEventListener('mouseenter', function(){ c.style.background='rgba(255,255,255,0.06)';}); c.addEventListener('mouseleave', function(){ c.style.background='rgba(255,255,255,0.04)';}); c.title='Your full name (min 3 chars)'; }
    }

    if (email) {
        email.addEventListener('input', function(){
            var ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim());
            mark(email, ok || email.value.trim()==='');
        });
        email.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); phone.focus(); }});
        var c = getContainer(email); if (c) { c.addEventListener('mouseenter', function(){ c.style.background='rgba(255,255,255,0.06)';}); c.addEventListener('mouseleave', function(){ c.style.background='rgba(255,255,255,0.04)';}); c.title='Valid email like name@example.com'; }
    }

    if (phone) {
        phone.addEventListener('keypress', function(e){
            var ch = e.key; if (ch.length !== 1) return;
            if (!/[0-9]/.test(ch)) { 
                e.preventDefault(); 
                setStatus('error','Phone: digits only'); 
            }
        });
        
        phone.addEventListener('input', function(){
            var v = phone.value.replace(/[^0-9]/g, '');
            var ok = v.length === 11;
            
            if (v.length > 11) {
                phone.value = v.substring(0, 11);
                setStatus('error', 'Phone number must be 11 digits');
                // Add timeout to show green after error
                if (v.substring(0, 11).length === 11) {
                    setTimeout(function() {
                        mark(phone, true);
                    }, 1000); // 1 second delay
                }
            } else {
                phone.value = v;
                if (v.length === 11) {
                    mark(phone, true);
                } else if (v.length === 0) {
                    mark(phone, null); // neutral state for empty
                } else {
                    mark(phone, false); // red for incomplete
                }
            }
        });
        
        phone.addEventListener('keydown', function(e){ 
            if (e.key === 'Enter') { 
                e.preventDefault(); 
                username.focus(); 
            }
        });
        
        var c = getContainer(phone); 
        if (c) { 
            c.addEventListener('mouseenter', function(){ 
                c.style.background='rgba(255,255,255,0.06)';
            }); 
            c.addEventListener('mouseleave', function(){ 
                c.style.background='rgba(255,255,255,0.04)';
            }); 
            c.title='Phone number (11 digits only)'; 
        }
    }

    if (username) {
        username.addEventListener('keypress', function(e){
            var ch = e.key; if (ch.length !== 1) return;
            if (!/[a-zA-Z0-9._-]/.test(ch)) { e.preventDefault(); setStatus('error','Username: letters, numbers, . _ -'); }
        });
        username.addEventListener('input', function(){
            var ok = username.value.trim().length >= 4;
            mark(username, ok || username.value.trim()==='');
        });
        username.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); userType.focus(); }});
        var c = getContainer(username); if (c) { c.addEventListener('mouseenter', function(){ c.style.background='rgba(255,255,255,0.06)';}); c.addEventListener('mouseleave', function(){ c.style.background='rgba(255,255,255,0.04)';}); c.title='Username (min 4 chars). Allowed: letters, numbers, . _ -'; }
    }

    if (userType) {
        userType.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); pw1.focus(); }});
        var c = getContainer(userType); if (c) { c.addEventListener('mouseenter', function(){ c.style.background='rgba(255,255,255,0.06)';}); c.addEventListener('mouseleave', function(){ c.style.background='rgba(255,255,255,0.04)';}); c.title='Select your user type'; }
    }

    if (pw1) {
        pw1.addEventListener('input', function(){
            var v = pw1.value;
            var ok = v.length >= 8; // Changed from 6 to 8 to match PHP validation
            mark(pw1, ok || v.length === 0);
        });
        pw1.addEventListener('keydown', function(e){ 
            if (e.key === 'Enter') { 
                e.preventDefault(); 
                pw2.focus(); 
            } else if (e.key === 'Escape'){ 
                pw1.value=''; 
                mark(pw1, null);
            } 
        });
        var c = getContainer(pw1); 
        if (c) { 
            c.addEventListener('mouseenter', function(){ 
                c.style.background='rgba(255,255,255,0.06)';
            }); 
            c.addEventListener('mouseleave', function(){ 
                c.style.background='rgba(255,255,255,0.04)';
            }); 
            c.title='Password (min 8 characters)'; 
        }
    }

    if (pw2) {
        pw2.addEventListener('input', function(){
            var ok = pw2.value === pw1.value && pw2.value.length >= 8; // Changed from 6 to 8
            mark(pw2, ok || pw2.value.length === 0);
        });
        pw2.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); terms.focus(); } else if (e.key==='Escape'){ pw2.value=''; mark(pw2,null);} });
        var c = getContainer(pw2); if (c) { c.addEventListener('mouseenter', function(){ c.style.background='rgba(255,255,255,0.06)';}); c.addEventListener('mouseleave', function(){ c.style.background='rgba(255,255,255,0.04)';}); c.title='Repeat your password'; c.addEventListener('dblclick', function(){ pw2.setAttribute('type', pw2.getAttribute('type')==='password'?'text':'password'); }); }
    }

    // Submit validation
    if (form) {
        // Button hover/double click guards
        if (btn) {
            btn.addEventListener('mouseenter', function(){ btn.style.opacity = '0.92'; });
            btn.addEventListener('mouseleave', function(){ btn.style.opacity = '1'; });
            btn.addEventListener('dblclick', function(e){ e.preventDefault(); setStatus('error', 'Please click once to submit.'); });
        }

        form.addEventListener('submit', function (e) {
            var full = fullName ? fullName.value.trim() : '';
            var mail = email ? email.value.trim() : '';
            var ph = phone ? phone.value.trim() : '';
            var user = username ? username.value.trim() : '';
            var type = userType ? userType.value : '';
            var agree = terms ? terms.checked : false;
            var newsletter = document.getElementById('newsletter') ? document.getElementById('newsletter').checked : false;

            if (!full || full.length < 3) { e.preventDefault(); mark(fullName, false); setStatus('error', 'Enter your full name.'); return; }
            if (!mail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail)) { e.preventDefault(); mark(email, false); setStatus('error', 'Enter a valid email.'); return; }
            if (!ph || ph.replace(/[^0-9]/g,'').length < 7) { e.preventDefault(); mark(phone, false); setStatus('error', 'Enter a valid phone number.'); return; }
            if (!user || user.length < 4) { e.preventDefault(); mark(username, false); setStatus('error', 'Username too short.'); return; }
            if (!pw1.value || pw1.value.length < 6) { e.preventDefault(); mark(pw1, false); setStatus('error', 'Password too short.'); return; }
            if (pw1.value !== pw2.value) { e.preventDefault(); mark(pw2, false); setStatus('error', 'Passwords do not match.'); return; }
            if (!type) { e.preventDefault(); setStatus('error', 'Select a user type.'); return; }
            if (!newsletter) { e.preventDefault(); setStatus('error', 'Please subscribe to our newsletter to continue.'); return; }
            if (!agree) { e.preventDefault(); setStatus('error', 'Accept the terms to continue.'); return; }

            if (btn) {
                btn.classList.add('loading');
                btn.disabled = true;
            }
            
            // Submit the form normally and let PHP handle the submission
            // The success message will trigger our redirect
        });
    }

    // Check for success message on page load
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.querySelector('.php-message.success');
        if (successMessage) {
            // If there's a success message, set up redirect
            setTimeout(function() {
                // Show loading state on button if it exists
                const btn = document.getElementById('registerBtn');
                if (btn) {
                    btn.classList.add('loading');
                    btn.disabled = true;
                }
                
                // Redirect to login page
                window.location.href = '/login';
            }, 2000); // 2 second delay
        }
    });
});


