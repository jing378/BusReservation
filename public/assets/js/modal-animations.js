// Minimal, reusable modal open/close animations (no dependency)
// Usage: animateModalOpen('#id' | element), animateModalClose('#id' | element)

(function () {
	function resolveElement(target) {
		if (!target) return null;
		if (typeof target === 'string') return document.querySelector(target);
		return target.nodeType === 1 ? target : null;
	}

	function getContentElement(modalEl) {
		// Try common modal content class names used in the app
		return modalEl.querySelector('.modal-content, .confirm-modal-content, .cancel-modal-content, .delete-modal-content, .modal-container') || modalEl.firstElementChild || modalEl;
	}

	function onAnimationEndOnce(el, cb, timeout = 400) {
		let called = false;
		const done = () => {
			if (called) return;
			called = true;
			el.removeEventListener('animationend', done);
			el.removeEventListener('transitionend', done);
			cb();
		};
		el.addEventListener('animationend', done, { once: true });
		el.addEventListener('transitionend', done, { once: true });
		// Fallback timeout in case no events fire
		setTimeout(done, timeout);
	}

	function resolveDisplayValue(modalEl) {
		if (!modalEl) return 'block';
		if (modalEl.dataset && modalEl.dataset.modalDisplay) {
			return modalEl.dataset.modalDisplay;
		}

		const inlineDisplay = modalEl.style && modalEl.style.display;
		if (inlineDisplay && inlineDisplay !== 'none') {
			if (modalEl.dataset) modalEl.dataset.modalDisplay = inlineDisplay;
			return inlineDisplay;
		}

		let computedDisplay = 'none';
		if (typeof window !== 'undefined' && window.getComputedStyle) {
			computedDisplay = window.getComputedStyle(modalEl).display;
		}
		if (computedDisplay && computedDisplay !== 'none') {
			if (modalEl.dataset) modalEl.dataset.modalDisplay = computedDisplay;
			return computedDisplay;
		}

		const className = (modalEl.className || '').toString();
		if (/\bmodal\b/i.test(className) || className.includes('-modal') || className.includes('modal-')) {
			if (modalEl.dataset) modalEl.dataset.modalDisplay = 'flex';
			return 'flex';
		}

		if (modalEl.dataset) modalEl.dataset.modalDisplay = 'block';
		return 'block';
	}

	function animateModalOpen(target) {
		const modalEl = resolveElement(target);
		if (!modalEl) return;
		modalEl.classList.remove('is-hiding');
		modalEl.style.display = resolveDisplayValue(modalEl);
		// Small reflow to ensure CSS animations can run
		// eslint-disable-next-line no-unused-expressions
		modalEl.offsetHeight;
		modalEl.classList.add('is-visible');
	}

	function animateModalClose(target) {
		const modalEl = resolveElement(target);
		if (!modalEl) return;
		
		// Get the content element for centered modals (confirm, delete, etc)
		const contentEl = getContentElement(modalEl);
		
		// Apply is-hiding to the modal wrapper
		modalEl.classList.remove('is-visible');
		modalEl.classList.add('is-hiding');
		
		// For delete-modal-content and confirm-modal-content, also apply is-animating-out
		if (contentEl && contentEl !== modalEl) {
			if (contentEl.classList.contains('delete-modal-content') || 
			    contentEl.classList.contains('confirm-modal-content') ||
			    contentEl.classList.contains('cancel-modal-content')) {
				contentEl.classList.add('is-animating-out');
			}
		}
		
		onAnimationEndOnce(modalEl, () => {
			modalEl.style.display = 'none';
			modalEl.classList.remove('is-hiding');
			if (contentEl && contentEl !== modalEl) {
				contentEl.classList.remove('is-animating-out');
			}
		}, 300);
	}

	window.animateModalOpen = animateModalOpen;
	window.animateModalClose = animateModalClose;
})();


