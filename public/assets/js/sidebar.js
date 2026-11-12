(function(){
    var wrap = document.querySelector('.dash-wrap');
    var btn = document.getElementById('sidebarToggle');
    var mobileMenuBtn = document.getElementById('mobileMenuToggle');
    if(!wrap) return;
    
    // Check if we're on mobile (below 980px)
    var isMobile = function() {
        return window.innerWidth <= 980;
    };
    
    // Initialize sidebar state based on screen size
    var initializeSidebar = function() {
        if(isMobile()){
            // On mobile: ensure sidebar is hidden and collapsed state is removed
            wrap.classList.remove('sidebar-open');
            wrap.classList.remove('collapsed');
        } else {
            // On desktop: load collapsed state from localStorage
            wrap.classList.remove('sidebar-open');
            if(localStorage.getItem('sidebar-collapsed') === '1'){
                wrap.classList.add('collapsed');
            } else {
                wrap.classList.remove('collapsed');
            }
        }
    };
    
    // Apply initial state
    try{
        initializeSidebar();
    }catch(e){}
    
    if(btn){
        btn.addEventListener('click', function(){
            // On mobile: close the sidebar overlay
            if(isMobile()){
                wrap.classList.remove('sidebar-open');
            } 
            // On desktop: toggle collapsed state
            else {
                wrap.classList.toggle('collapsed');
                try{
                    localStorage.setItem('sidebar-collapsed', wrap.classList.contains('collapsed') ? '1' : '0');
                }catch(e){}
            }
        });
    }
    
    // Mobile menu toggle
    if(mobileMenuBtn){
        mobileMenuBtn.addEventListener('click', function(){
            wrap.classList.toggle('sidebar-open');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e){
        if(wrap.classList.contains('sidebar-open')){
            var sidebar = document.querySelector('.sidebar');
            var isClickInSidebar = sidebar && sidebar.contains(e.target);
            var isClickInMenuBtn = mobileMenuBtn && mobileMenuBtn.contains(e.target);
            if(!isClickInSidebar && !isClickInMenuBtn){
                wrap.classList.remove('sidebar-open');
            }
        }
    });
    
    // Handle window resize with debouncing
    var resizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function(){
            initializeSidebar();
        }, 100);
    });
    
    // Ensure menu labels are wrapped so they can be hidden when collapsed
    var menuLinks = document.querySelectorAll('.menu a');
    menuLinks.forEach(function(a){
        var icon = a.querySelector('i');
        if(icon){
            // Find any trailing text node and wrap it into span
            for(var i=0;i<a.childNodes.length;i++){
                var n = a.childNodes[i];
                if(n.nodeType === Node.TEXT_NODE){
                    var trimmed = n.textContent.trim();
                    if(trimmed.length){
                        var s = document.createElement('span');
                        s.textContent = trimmed;
                        a.replaceChild(s, n);
                    }
                }
            }
        }
    });

    // Wrap logout link text in span for collapsed state
    var logout = document.querySelector('.logout');
    if(logout){
        for(var j=0;j<logout.childNodes.length;j++){
            var m = logout.childNodes[j];
            if(m.nodeType === Node.TEXT_NODE){
                var t = m.textContent.trim();
                if(t.length){
                    var sp = document.createElement('span');
                    sp.textContent = t;
                    logout.replaceChild(sp, m);
                }
            }
        }
    }

    // Auto-dismiss success notifications (global): any .qa containing a check-circle icon
    try{
        var successNotices = document.querySelectorAll('.qa');
        successNotices.forEach(function(el){
            if(el.querySelector('.fa-check-circle')){
                setTimeout(function(){
                    el.style.transition = 'opacity .3s ease, transform .3s ease';
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-4px)';
                    setTimeout(function(){
                        el.style.display = 'none';
                    }, 320);
                }, 1500);
            }
        });
    }catch(e){}
    
    // Mark layout as ready so transitions are enabled only after initial state
    // Use requestAnimationFrame to ensure DOM styles are applied before reveal
    try{
        (window.requestAnimationFrame || setTimeout)(function(){
            wrap.setAttribute('data-ready','1');
        }, 0);
    }catch(e){}
})();


