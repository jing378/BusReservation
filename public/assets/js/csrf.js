/**
 * Attaches Laravel's CSRF token to every POST the legacy pages make:
 * markup forms, forms built and submitted from JavaScript, fetch() and XMLHttpRequest.
 */
(function () {
    var meta = document.querySelector('meta[name="csrf-token"]');
    var token = meta ? meta.getAttribute('content') : '';

    if (!token) {
        return;
    }

    function isUnsafe(method) {
        method = (method || 'GET').toUpperCase();
        return method !== 'GET' && method !== 'HEAD' && method !== 'OPTIONS';
    }

    function addTokenField(form) {
        if (!form || !isUnsafe(form.method) || form.querySelector('input[name="_token"]')) {
            return;
        }
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = '_token';
        input.value = token;
        form.appendChild(input);
    }

    var nativeSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function () {
        addTokenField(this);
        return nativeSubmit.call(this);
    };

    document.addEventListener('submit', function (event) {
        addTokenField(event.target);
    }, true);

    if (window.fetch) {
        var nativeFetch = window.fetch;
        window.fetch = function (input, init) {
            init = init || {};
            var method = init.method || (input && input.method) || 'GET';
            if (isUnsafe(method)) {
                var headers = new Headers(init.headers || (input && input.headers) || {});
                if (!headers.has('X-CSRF-TOKEN')) {
                    headers.set('X-CSRF-TOKEN', token);
                }
                init.headers = headers;
            }
            return nativeFetch.call(this, input, init);
        };
    }

    var nativeOpen = XMLHttpRequest.prototype.open;
    var nativeSend = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.open = function (method) {
        this._csrfMethod = method;
        return nativeOpen.apply(this, arguments);
    };
    XMLHttpRequest.prototype.send = function () {
        if (isUnsafe(this._csrfMethod)) {
            this.setRequestHeader('X-CSRF-TOKEN', token);
        }
        return nativeSend.apply(this, arguments);
    };
})();
