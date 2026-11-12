document.addEventListener('DOMContentLoaded', function () {
    var passwordToggle = document.getElementById('passwordToggle');
    var passwordInput = document.getElementById('password');
    var loginForm = document.getElementById('loginForm');
    var loginBtn = document.getElementById('loginBtn');
    var messageContainer = document.getElementById('messageContainer');
    var message = document.getElementById('message');
    var usernameInput = document.getElementById('username');

    // Helper: get nearest input container
    function getContainer(el) {
        return el ? el.closest('.input-container') : null;
    }

    // Helper: set container error/ok styles and status toast
    function setStatus(type, text) {
        if (!message || !messageContainer) return;
        message.classList.remove('success', 'error', 'show');
        message.classList.add(type);
        var textEl = message.querySelector('.message-text');
        if (textEl) textEl.textContent = text;
        requestAnimationFrame(function () { message.classList.add('show'); });
        clearTimeout(setStatus._t);
        // Change timeout to 1000ms (1 second)
        setStatus._t = setTimeout(function () { 
            message.classList.remove('show'); 
        }, 1000);
    }

    function mark(el, ok) {
        var c = getContainer(el);
        if (!c) return;
        if (ok === true) {
            c.style.borderColor = 'rgba(16,185,129,0.6)'; // green
            c.style.boxShadow = '0 0 0 3px rgba(16,185,129,0.15)';
        } else if (ok === false) {
            c.style.borderColor = 'rgba(239,68,68,0.7)'; // red
            c.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.18)';
        } else {
            c.style.borderColor = 'rgba(255,255,255,0.08)';
            c.style.boxShadow = 'none';
        }
    }

    // Lambda handler (demo)
    var lambdaHandler = function(event) {
        console.log('%cLambda: Login attempt detected!', 'color: #ff8c00; font-weight: bold;');
        return true;
    };

    // Show/hide password toggle only when there is input
    function bindToggleVisibility(input, toggleBtn) {
        if (!input || !toggleBtn) return;
        // Hide toggle initially and set transform
        toggleBtn.style.display = 'none';
        toggleBtn.style.transform = 'translateY(-50%)'; // Always set transform for proper positioning
        
        function update() {
            var has = input.value && input.value.length > 0;
            // Use multiple methods to ensure visibility
            if (has) {
                toggleBtn.classList.add('show');
                // Use setProperty with important flag as fallback
                toggleBtn.style.setProperty('display', 'inline-flex', 'important');
                toggleBtn.style.setProperty('visibility', 'visible', 'important');
                toggleBtn.style.setProperty('opacity', '1', 'important');
                // Always ensure transform is set for centering
                toggleBtn.style.setProperty('transform', 'translateY(-50%)', 'important');
            } else {
                toggleBtn.classList.remove('show');
                toggleBtn.style.setProperty('display', 'none', 'important');
                toggleBtn.style.setProperty('visibility', 'hidden', 'important');
                // Keep transform even when hidden for when it shows again
                toggleBtn.style.setProperty('transform', 'translateY(-50%)', 'important');
            }
        }
        update();
        input.addEventListener('input', update);
        input.addEventListener('blur', update);
        input.addEventListener('focus', update);
        // Also listen for change event
        input.addEventListener('change', update);
    }

    if (passwordToggle && passwordInput) {
        // Ensure positioning and transform are set immediately when button is created
        passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
        passwordToggle.style.setProperty('position', 'absolute', 'important');
        passwordToggle.style.setProperty('right', '12px', 'important');
        passwordToggle.style.setProperty('top', '50%', 'important');
        
        // Set up click handler for toggle
        passwordToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            passwordToggle.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
            // Ensure transform is preserved after icon change
            passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
            // Refocus input after toggle
            passwordInput.focus();
        });
        
        // Bind visibility toggle
        bindToggleVisibility(passwordInput, passwordToggle);
        
        // Also check on page load in case there's already text (e.g., browser autofill)
        setTimeout(function() {
            if (passwordInput.value && passwordInput.value.length > 0) {
                passwordToggle.classList.add('show');
                passwordToggle.style.setProperty('display', 'inline-flex', 'important');
                passwordToggle.style.setProperty('visibility', 'visible', 'important');
                passwordToggle.style.setProperty('opacity', '1', 'important');
                passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
            }
        }, 100);
        
        // Check multiple times for autofill (browsers fill at different times)
        var autofillCheckInterval = setInterval(function() {
            if (passwordInput.value && passwordInput.value.length > 0) {
                passwordToggle.classList.add('show');
                passwordToggle.style.setProperty('display', 'inline-flex', 'important');
                passwordToggle.style.setProperty('visibility', 'visible', 'important');
                passwordToggle.style.setProperty('opacity', '1', 'important');
                passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
                // Clear interval after autofill is detected
                clearInterval(autofillCheckInterval);
            }
        }, 200);
        
        // Clear interval after 5 seconds to avoid infinite checking
        setTimeout(function() {
            clearInterval(autofillCheckInterval);
        }, 5000);
        
        // Also listen for paste events
        passwordInput.addEventListener('paste', function() {
            setTimeout(function() {
                if (passwordInput.value && passwordInput.value.length > 0) {
                    passwordToggle.classList.add('show');
                    passwordToggle.style.setProperty('display', 'inline-flex', 'important');
                    passwordToggle.style.setProperty('visibility', 'visible', 'important');
                    passwordToggle.style.setProperty('opacity', '1', 'important');
                    passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
                }
            }, 10);
        });
        
        // Ensure transform is always set (even when hidden)
        passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
        
        // Add keydown listener as additional backup
        passwordInput.addEventListener('keydown', function() {
            setTimeout(function() {
                if (passwordInput.value && passwordInput.value.length > 0) {
                    passwordToggle.classList.add('show');
                    passwordToggle.style.setProperty('display', 'inline-flex', 'important');
                    passwordToggle.style.setProperty('visibility', 'visible', 'important');
                    passwordToggle.style.setProperty('opacity', '1', 'important');
                    passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
                } else {
                    passwordToggle.classList.remove('show');
                    passwordToggle.style.setProperty('display', 'none', 'important');
                    passwordToggle.style.setProperty('visibility', 'hidden', 'important');
                }
            }, 0);
        });
        
        // Add compositionend for IME input
        passwordInput.addEventListener('compositionend', function() {
            if (passwordInput.value && passwordInput.value.length > 0) {
                passwordToggle.classList.add('show');
                passwordToggle.style.setProperty('display', 'inline-flex', 'important');
                passwordToggle.style.setProperty('visibility', 'visible', 'important');
                passwordToggle.style.setProperty('opacity', '1', 'important');
                passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
            }
        });
    } else {
        console.error('Password toggle elements not found:', {passwordToggle: !!passwordToggle, passwordInput: !!passwordInput});
    }

    // Input constraints & live validation
    if (usernameInput) {
        // KeyPress: allow username characters (a-z, A-Z, 0-9, ., _, -) OR email characters (including @ and +)
        usernameInput.addEventListener('keypress', function (e) {
            var ch = e.key;
            if (ch.length !== 1) return; // allow control keys
            // Allow username chars OR email-specific chars (@, +)
            if (!/[a-zA-Z0-9._-@+]/.test(ch)) {
                e.preventDefault();
                setStatus('error', 'Username or email can contain letters, numbers, . _ - @ +');
            }
        });

        // TextChanged: live feedback - validate as username or email
        usernameInput.addEventListener('input', function () {
            var v = usernameInput.value.trim();
            if (v === '') { mark(usernameInput, null); return; }
            
            // Check if it looks like an email (contains @)
            var isEmail = v.includes('@');
            var isValid = false;
            
            if (isEmail) {
                // Basic email validation: has @ and at least one character before and after @
                var emailParts = v.split('@');
                isValid = emailParts.length === 2 && 
                         emailParts[0].length > 0 && 
                         emailParts[1].length > 0 && 
                         emailParts[1].includes('.') &&
                         v.length >= 5; // Minimum email length
            } else {
                // Username validation: at least 3 characters
                isValid = v.length >= 3;
            }
            
            mark(usernameInput, isValid);
        });

        // KeyDown navigation
        usernameInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (passwordInput) passwordInput.focus();
            } else if (e.key === 'Escape') {
                usernameInput.value = '';
                mark(usernameInput, null);
            }
        });

        // Mouse enter/leave hints
        var uCont = getContainer(usernameInput);
        if (uCont) {
            uCont.addEventListener('mouseenter', function(){ uCont.style.background = 'rgba(255,255,255,0.06)'; });
            uCont.addEventListener('mouseleave', function(){ uCont.style.background = 'rgba(255,255,255,0.04)'; });
            uCont.title = 'Username (3+ chars) or Email address';
        }
    }

    if (passwordInput) {
        // TextChanged: live strength hint 
        passwordInput.addEventListener('input', function () {
            var v = passwordInput.value;
            if (v === '') { 
                mark(passwordInput, null); 
                return; 
            }
            // Show green when password length is 8 or more
            var ok = v.length >= 8;
            mark(passwordInput, ok);
        });

        // Mouse interactions
        var pCont = getContainer(passwordInput);
        if (pCont) {
            pCont.addEventListener('mouseenter', function(){ 
                pCont.style.background = 'rgba(255,255,255,0.06)'; 
            });
            pCont.addEventListener('mouseleave', function(){ 
                pCont.style.background = 'rgba(255,255,255,0.04)'; 
            });
            pCont.title = 'Password (8+ characters required)';
            pCont.addEventListener('dblclick', function(){
                // Double-click to toggle visibility quickly
                if (passwordInput.getAttribute('type') === 'password') {
                    passwordInput.setAttribute('type', 'text');
                } else {
                    passwordInput.setAttribute('type', 'password');
                }
            });
        }
    }

    function showToast(type, text) {
        if (!message || !messageContainer) return;
        message.classList.remove('success', 'error', 'show');
        message.classList.add(type);
        message.querySelector('.message-text').textContent = text;
        requestAnimationFrame(function () {
            message.classList.add('show');
        });
        // Change timeout to 1000ms
        setTimeout(function () { 
            message.classList.remove('show'); 
        }, 1000);
    }

    if (loginForm) {
        // Mouse click on button: hint
        if (loginBtn) {
            loginBtn.addEventListener('mouseenter', function(){ loginBtn.style.opacity = '0.92'; });
            loginBtn.addEventListener('mouseleave', function(){ loginBtn.style.opacity = '1'; });
            loginBtn.addEventListener('dblclick', function(e){ e.preventDefault(); setStatus('error', 'Please click once to sign in.'); });
        }

        loginForm.addEventListener('submit', function (e) {
            // Validate username/password
            var username = (usernameInput ? usernameInput.value.trim() : '').toString();
            var password = (passwordInput ? passwordInput.value.trim() : '').toString();

            if (username === '' || password === '') {
                e.preventDefault();
                mark(usernameInput, false);
                mark(passwordInput, false);
                setStatus('error', 'Please enter both username/email and password.');
                return false;
            }
            
            // Validate as username or email
            var isEmail = username.includes('@');
            var isValid = false;
            
            if (isEmail) {
                // Basic email validation
                var emailParts = username.split('@');
                isValid = emailParts.length === 2 && 
                         emailParts[0].length > 0 && 
                         emailParts[1].length > 0 && 
                         emailParts[1].includes('.') &&
                         username.length >= 5;
                if (!isValid) {
                    e.preventDefault();
                    mark(usernameInput, false);
                    setStatus('error', 'Please enter a valid email address.');
                    return false;
                }
            } else {
                // Username validation
                if (username.length < 3) {
                    e.preventDefault();
                    mark(usernameInput, false);
                    setStatus('error', 'Username must be at least 3 characters.');
                    return false;
                }
            }
            if (password.length < 8) {
                e.preventDefault();
                mark(passwordInput, false);
                setStatus('error', 'Password must be at least 8 characters.');
                return false;
            }

            // Audit log
            var timestamp = new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            console.log('%cAudit log: login attempt at ' + timestamp, 'color: #ff8c00; font-weight: bold;');
            lambdaHandler(e);

            // Loading state on button
            if (loginBtn) {
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
                setTimeout(function () {
                    loginBtn.disabled = false;
                    loginBtn.classList.remove('loading');
                }, 1500);
            }
        });
    }

    // Auto-hide PHP messages after 1 second
    const phpMessage = document.querySelector('.php-message');
    if (phpMessage) {
        setTimeout(function() {
            phpMessage.style.opacity = '0';
            phpMessage.style.transition = 'opacity 0.3s ease';
            setTimeout(() => phpMessage.remove(), 300);
        }, 1000);
    }
});


