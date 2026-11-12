class WeatherCarousel {
    constructor() {
        this.currentSlide = 0;
        this.carousel = document.querySelector('#weatherCarouselContainer');
        this.container = this.carousel.querySelector('.weather-container');
        this.slides = this.carousel.querySelectorAll('.weather-slide');
        this.totalSlides = this.slides.length;
        
        // Remove any existing dots
        const existingDots = this.carousel.querySelector('.carousel-dots');
        if (existingDots) {
            existingDots.remove();
        }

        // Only setup if we have multiple slides
        if (this.totalSlides > 1) {
            this.setupDots();
            this.setupButtons();
            this.carousel.classList.add('has-multiple-slides');
            this.startAutoSlide();
        }
    }

    setupDots() {
        const dotsContainer = document.createElement('div');
        dotsContainer.className = 'carousel-dots';
        
        for (let i = 0; i < this.totalSlides; i++) {
            const dot = document.createElement('div');
            dot.className = 'dot' + (i === 0 ? ' active' : '');
            dot.addEventListener('click', () => this.goToSlide(i));
            dotsContainer.appendChild(dot);
        }
        
        this.carousel.appendChild(dotsContainer);
        this.dots = dotsContainer.querySelectorAll('.dot');
    }

    setupButtons() {
        // Update button event listeners
        const prevButton = this.carousel.querySelector('.carousel-btn.prev');
        const nextButton = this.carousel.querySelector('.carousel-btn.next');

        if (prevButton) {
            prevButton.addEventListener('click', () => this.prevSlide());
        }
        if (nextButton) {
            nextButton.addEventListener('click', () => this.nextSlide());
        }
    }

    updateDots() {
        if (this.dots) {
            this.dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === this.currentSlide);
            });
        }
    }

    goToSlide(index) {
        if (index < 0 || index >= this.totalSlides) return;
        this.currentSlide = index;
        this.container.style.transform = `translateX(-${index * 100}%)`;
        this.updateDots();
        this.resetAutoSlide();
    }

    nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        this.goToSlide(this.currentSlide);
    }

    prevSlide() {
        this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        this.goToSlide(this.currentSlide);
    }

    startAutoSlide() {
        this.stopAutoSlide(); // Clear any existing interval
        this.autoSlideInterval = setInterval(() => this.nextSlide(), 5000);
    }

    stopAutoSlide() {
        if (this.autoSlideInterval) {
            clearInterval(this.autoSlideInterval);
            this.autoSlideInterval = null;
        }
    }

    resetAutoSlide() {
        this.stopAutoSlide();
        this.startAutoSlide();
    }
}

// Initialize the weather carousel when the document is loaded
let weatherCarousel;
document.addEventListener('DOMContentLoaded', () => {
    weatherCarousel = new WeatherCarousel();
    // Make it globally accessible for the onclick handlers
    window.weatherCarousel = weatherCarousel;
});