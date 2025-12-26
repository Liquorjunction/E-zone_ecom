// Simple global toast system
(function(){
    const isMobile = () => window.innerWidth < 640;

    function getContainer(){
        if (isMobile()){
            return document.getElementById('global-toasts-mobile-inner') || document.getElementById('global-toasts-inner');
        }
        return document.getElementById('global-toasts-inner') || document.getElementById('global-toasts-mobile-inner');
    }

    function createToast(opt){
        const id = `toast_${Date.now()}_${Math.floor(Math.random()*1000)}`;
        const type = opt.type || 'info';
        const title = opt.title || '';
        const message = opt.message || '';
        const duration = typeof opt.duration === 'number' ? opt.duration : (isMobile() ? 4500 : 4000);
        const actionText = opt.actionText || null;
        const action = typeof opt.action === 'function' ? opt.action : null;

        const wrapper = document.createElement('div');
        wrapper.className = 'toast';
        wrapper.id = id;
        wrapper.setAttribute('role','status');

        const desktop = !isMobile();

        const color = type === 'success' ? '#16a34a' : (type === 'error' ? '#ef4444' : (type === 'warning' ? '#d97706' : '#2563eb'));

        if (desktop){
            wrapper.classList.add('toast-desktop','toast-enter');
            wrapper.innerHTML = `
                <div class="toast-card bg-white/95 dark:bg-slate-800 rounded-lg p-3 shadow-lg ring-1 ring-slate-900/6 flex items-start gap-3 max-w-xs">
                    <div class="flex-shrink-0 mt-1">
                        <div class="w-9 h-9 rounded-md flex items-center justify-center" style="background: ${color}22">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 8v4l2 2" stroke="${color}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">${escapeHtml(title)}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-300 mt-1">${escapeHtml(message)}</div>
                        ${actionText ? `<div class="mt-2"><button class="toast-action text-sm font-medium hover:underline" style="color:${color}">${escapeHtml(actionText)}</button></div>` : ''}
                        <div class="toast-progress h-1 rounded mt-3" aria-hidden style="background: ${color}"></div>
                    </div>
                    <div class="ml-3 flex-shrink-0 flex items-start">
                        <button class="toast-close text-slate-400 hover:text-slate-600 focus:outline-none" aria-label="Close">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            `;
        } else {
            wrapper.classList.add('toast-mobile','toast-enter');
            wrapper.innerHTML = `
                <div class="toast-card w-full max-w-[720px] bg-slate-900 text-white rounded-full px-4 py-3 flex items-center gap-3 shadow-lg">
                    <div class="flex items-center justify-center w-9 h-9 rounded-md bg-white/5">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8v4l2 2" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold">${escapeHtml(title)}</div>
                        <div class="text-sm text-slate-200">${escapeHtml(message)}</div>
                        <div class="toast-progress h-1 rounded-full mt-2" aria-hidden style="background: linear-gradient(90deg, ${color}, rgba(255,255,255,0.6))"></div>
                    </div>
                    <div class="ml-3">
                        <button class="toast-close text-white/90 text-lg" aria-label="Close">✕</button>
                    </div>
                </div>
            `;
        }

        return { el: wrapper, id, duration, action };
    }

    function escapeHtml(unsafe){
        return String(unsafe)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function mountToast(toast){
        const container = getContainer();
        if(!container) return;
        // create wrapper element (desktop inner or mobile inner expects .toast direct children)
        container.appendChild(toast.el);

        // small async entrance
        requestAnimationFrame(()=>{
            toast.el.classList.remove('toast-enter');
            toast.el.classList.add('toast-enter-active');
        });

        const progress = toast.el.querySelector('.toast-progress');
        if(progress){
            // animate progress via transform
            progress.style.transition = `transform ${toast.duration}ms linear`;
            requestAnimationFrame(()=>{
                progress.style.transform = 'scaleX(1)';
            });
        }

        const closeBtn = toast.el.querySelector('.toast-close');
        if(closeBtn) closeBtn.addEventListener('click', ()=> removeToast(toast.el));

        const actionBtn = toast.el.querySelector('.toast-action');
        if(actionBtn && typeof toast.action === 'function'){
            actionBtn.addEventListener('click', (e)=>{ try{ toast.action(e); }catch(e){} removeToast(toast.el); });
        }

        // swipe to dismiss (mobile)
        if (toast.el.classList.contains('toast-mobile')){
            let startX = 0, currentX = 0, touching = false;
            const threshold = 60;
            const card = toast.el.querySelector('.toast-card') || toast.el;
            card.addEventListener('touchstart', (e)=>{ touching=true; startX = e.touches[0].clientX; card.style.transition = '' }, {passive:true});
            card.addEventListener('touchmove', (e)=>{
                if(!touching) return; currentX = e.touches[0].clientX - startX; card.style.transform = `translateX(${currentX}px)`; card.style.opacity = `${Math.max(0.25, 1 - Math.abs(currentX)/200)}`; }, {passive:true});
            card.addEventListener('touchend', ()=>{ touching=false; if(Math.abs(currentX) > threshold){ removeToast(toast.el); } else { card.style.transition = 'transform 160ms ease, opacity 160ms'; card.style.transform='translateX(0)'; card.style.opacity='1'; } currentX=0; });
        }

        // auto remove
        if(toast.duration > 0){
            toast._timeout = setTimeout(()=> removeToast(toast.el), toast.duration);
        }

        // pause on hover for desktop
        toast.el.addEventListener('mouseenter', ()=>{ if(toast._timeout) clearTimeout(toast._timeout); if(progress) progress.style.transition = ''; });
        toast.el.addEventListener('mouseleave', ()=>{ if(toast.duration > 0){ toast._timeout = setTimeout(()=> removeToast(toast.el), 2500); } if(progress){ progress.style.transition = `transform 2500ms linear`; requestAnimationFrame(()=> progress.style.transform = 'scaleX(1)'); } });
    }

    function removeToast(el){
        if(!el || !el.parentNode) return;
        el.classList.add('toast-leave-active');
        // give leave animation a moment then remove
        setTimeout(()=>{ if(el && el.parentNode) el.parentNode.removeChild(el); }, 260);
    }

    // expose API
    window.Toast = {
        show(opts){ const t = createToast(opts || {}); mountToast(t); return t.id; },
        success(opts){ return this.show(Object.assign({}, opts, {type:'success'})); },
        error(opts){ return this.show(Object.assign({}, opts, {type:'error'})); },
        info(opts){ return this.show(Object.assign({}, opts, {type:'info'})); },
        warning(opts){ return this.show(Object.assign({}, opts, {type:'warning'})); }
    };

    // ensure fallback container exists when scripts load early
    document.addEventListener('DOMContentLoaded', ()=>{
        if(!document.getElementById('global-toasts')){
            const wrapper = document.createElement('div'); wrapper.id='global-toasts'; wrapper.className='pointer-events-none fixed inset-0 z-50';
            wrapper.innerHTML = '<div id="global-toasts-mobile" class="sm:hidden pointer-events-none fixed inset-x-0 bottom-4 px-4 flex justify-center items-end"><div id="global-toasts-mobile-inner" class="w-full max-w-lg flex flex-col items-center space-y-3"></div></div><div id="global-toasts-desktop" class="hidden sm:flex pointer-events-none fixed top-4 right-4 items-start justify-end"><div id="global-toasts-inner" class="w-full flex flex-col items-end space-y-3 max-w-xs"></div></div>';
            document.body.appendChild(wrapper);
        }
    });
})();
