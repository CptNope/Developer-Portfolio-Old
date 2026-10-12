/**
 * Developer Portfolio - Theme JavaScript
 */

(function () {
	'use strict';

	/**
	 * Header scroll effect - add/remove background opacity on scroll.
	 */
	function initHeaderScroll() {
		const header = document.querySelector('.site-header');
		if (!header) return;

		const onScroll = () => {
			if (window.scrollY > 20) {
				header.classList.add('is-scrolled');
			} else {
				header.classList.remove('is-scrolled');
			}
		};

		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	/**
	 * Animate elements into view using IntersectionObserver.
	 */
	function initScrollAnimations() {
		const animatedElements = document.querySelectorAll(
			'.project-card, .blog-post-card, .tech-card, .stat-card, .cta-card, .timeline-item'
		);

		if (!animatedElements.length || !('IntersectionObserver' in window)) return;

		// Respect reduced motion preference
		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

		const observer = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			},
			{
				threshold: 0.1,
				rootMargin: '0px 0px -50px 0px',
			}
		);

		animatedElements.forEach((el) => {
			el.classList.add('animate-on-scroll');
			observer.observe(el);
		});
	}

	/**
	 * Rotating roles in the hero section.
	 */
	function initHeroRoleRotation() {
		const roleEl = document.querySelector('.hero-role-text .developer-portfolio-gradient-text');
		if (!roleEl) return;

		// Add aria-live so screen readers announce role changes
		const parent = roleEl.closest('.hero-role-text');
		if (parent) {
			parent.setAttribute('aria-live', 'polite');
			parent.setAttribute('aria-atomic', 'true');
		}

		// Don't animate if user prefers reduced motion
		const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		if (prefersReducedMotion) return;

		// Read roles from data attribute, fall back to defaults
		let roles = [];
		if (parent && parent.dataset.roles) {
			try { roles = JSON.parse(parent.dataset.roles); } catch (e) { roles = []; }
		}
		if (!roles.length) {
			roles = [
				'Full-Stack Developer',
				'WordPress Engineer',
				'AI Systems Builder',
				'Cybersecurity Specialist',
				'Cloud Architect',
			];
		}

		let index = 0;

		setInterval(() => {
			index = (index + 1) % roles.length;
			roleEl.style.opacity = '0';
			roleEl.style.transform = 'translateY(10px)';

			setTimeout(() => {
				roleEl.textContent = '{ ' + roles[index] + ' }';
				roleEl.style.opacity = '1';
				roleEl.style.transform = 'translateY(0)';
			}, 300);
		}, 3000);

		roleEl.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
	}

	/**
	 * Fix tech stack color indicator dots.
	 * WordPress layout CSS overrides normal CSS sizing on these elements.
	 */
	function fixTechDots() {
		document.querySelectorAll('.tech-card .wp-block-group[style*="9999px"]').forEach(function (dot) {
			dot.style.setProperty('width', '0.75rem', 'important');
			dot.style.setProperty('height', '0.75rem', 'important');
			dot.style.setProperty('min-width', '0.75rem', 'important');
			dot.style.setProperty('max-width', '0.75rem', 'important');
			dot.style.setProperty('min-height', '0.75rem', 'important');
			dot.style.setProperty('max-height', '0.75rem', 'important');
			dot.style.setProperty('flex', '0 0 0.75rem', 'important');
		});
	}

	/**
	 * Toggle the custom mobile nav overlay.
	 */
	function initMobileNav() {
		var burger = document.querySelector('.sh-burger');
		var overlay = document.getElementById('sh-mobile-overlay');
		var closeBtn = overlay ? overlay.querySelector('.sh-mobile-close') : null;

		if (!burger || !overlay) return;

		function openMenu() {
			overlay.classList.add('is-open');
			overlay.setAttribute('aria-hidden', 'false');
			burger.setAttribute('aria-expanded', 'true');
			document.body.style.overflow = 'hidden';
		}

		function closeMenu() {
			overlay.classList.remove('is-open');
			overlay.setAttribute('aria-hidden', 'true');
			burger.setAttribute('aria-expanded', 'false');
			document.body.style.overflow = '';
		}

		burger.addEventListener('click', openMenu);
		if (closeBtn) {
			closeBtn.addEventListener('click', closeMenu);
		}

		// Close when a nav link is clicked
		overlay.addEventListener('click', function (e) {
			if (e.target.closest('.sh-mobile-link') || e.target.closest('.sh-mobile-cta a')) {
				closeMenu();
			}
		});

		// Close on Escape key
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
				closeMenu();
			}
		});
	}

	/**
	 * Initialize on DOM ready.
	 */
	function init() {
		document.body.classList.add('js-enabled');
		initHeaderScroll();
		initScrollAnimations();
		initHeroRoleRotation();
		fixTechDots();
		initMobileNav();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
