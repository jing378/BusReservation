class BusCarousel {
    constructor() {
        this.carousel = document.querySelector('#busCarouselContainer');
        this.container = this.carousel.querySelector('.bus-container');
        this.cards = this.carousel.querySelectorAll('.bus-card');
        this.currentIndex = 0;
        this.autoPlayInterval = null;
        this.userInteracted = false;
        this.lastInteractionTime = Date.now();

        // Remove any existing dots container
        const existingDots = this.carousel.querySelector('.carousel-dots');
        if (existingDots) {
            existingDots.remove();
        }

        // Create new dots container
        this.dotsContainer = document.createElement('div');
        this.dotsContainer.className = 'carousel-dots';
        this.carousel.appendChild(this.dotsContainer);

        this.init();
    }

    init() {
        if (!this.dotsContainer) {
            this.dotsContainer = document.createElement('div');
            this.dotsContainer.className = 'carousel-dots';
            this.carousel.appendChild(this.dotsContainer);
        }
        this.createDots();
        this.updateDisplay();
        this.bindEvents();
        this.startAutoPlay();
    }

    createDots() {
        this.dotsContainer.innerHTML = ''; // Clear existing dots
        this.cards.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                this.goToSlide(index);
                this.userInteracted = true;
                this.lastInteractionTime = Date.now();
            });
            this.dotsContainer.appendChild(dot);
        });
    }

    startAutoPlay() {
        this.stopAutoPlay();
        this.autoPlayInterval = setInterval(() => {
            if (Date.now() - this.lastInteractionTime > 3000) {
                this.nextBus();
            }
        }, 3000);
    }

    stopAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
        }
    }

    bindEvents() {
        this.carousel.addEventListener('mouseenter', () => this.stopAutoPlay());
        this.carousel.addEventListener('mouseleave', () => this.startAutoPlay());
        
        // Bind navigation methods to window for button access
        window.prevBus = () => this.prevBus();
        window.nextBus = () => this.nextBus();
    }

    prevBus() {
        this.userInteracted = true;
        this.lastInteractionTime = Date.now();
        this.currentIndex = (this.currentIndex - 1 + this.cards.length) % this.cards.length;
        this.updateDisplay();
    }

    nextBus() {
        this.userInteracted = true;
        this.lastInteractionTime = Date.now();
        this.currentIndex = (this.currentIndex + 1) % this.cards.length;
        this.updateDisplay();
    }

    goToSlide(index) {
        this.userInteracted = true;
        this.lastInteractionTime = Date.now();
        this.currentIndex = index;
        this.updateDisplay();
    }

    updateDisplay() {
        this.container.style.transform = `translateX(-${this.currentIndex * 100}%)`;
        
        // Only update dots within this carousel
        this.carousel.querySelectorAll('.dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentIndex);
        });

        const prevBtn = this.carousel.querySelector('.carousel-btn.prev');
        const nextBtn = this.carousel.querySelector('.carousel-btn.next');
        
        if (this.cards.length <= 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'flex';
        }
    }
}

// Initialize and make globally accessible
let busCarousel;
document.addEventListener('DOMContentLoaded', () => {
    busCarousel = new BusCarousel();
    // Make it globally accessible for the onclick handlers
    window.busCarousel = busCarousel;
});