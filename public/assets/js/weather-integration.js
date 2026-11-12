class WeatherDashboard {
    constructor() {
        this.cache = new Map();
        this.cacheExpiry = 30 * 60 * 1000; // 30 minutes
        this.autoSlideInterval = null;
        this.autoSlideDelay = 5000; // 5 seconds between slides
        this.isOffline = false;
    }

    /**
     * Check if browser has internet connectivity
     */
    async checkConnectivity(retries = 3) {
        try {
            // Check browser's navigator.onLine first
            if (!navigator.onLine) {
                console.log('Browser reports offline status');
                return false;
            }

            // Try to fetch a simple resource with timeout
            for (let attempt = 0; attempt < retries; attempt++) {
                try {
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 second timeout
                    
                    const response = await fetch('/api/weather', { // no params: answered instantly, no OpenWeather call
                        method: 'HEAD',
                        cache: 'no-store',
                        signal: controller.signal,
                        credentials: 'same-origin'
                    });
                    
                    clearTimeout(timeoutId);
                    
                    // Check if response status indicates server is reachable
                    if (response.ok || response.status === 400) {
                        // 400 is fine - means server got our request
                        console.log('Connectivity check passed');
                        return true;
                    }
                } catch (attemptError) {
                    console.log(`Connectivity check attempt ${attempt + 1} failed:`, attemptError.message);
                    // Wait before retry
                    if (attempt < retries - 1) {
                        await new Promise(resolve => setTimeout(resolve, 500));
                    }
                }
            }
            
            console.error('All connectivity checks failed');
            return false;
        } catch (error) {
            console.error('Connectivity check error:', error);
            return false;
        }
    }

    /**
     * Create Lottie 404 animation for offline state
     */
    createLottieAnimation() {
        return `
            <div class="lottie-animation-container">
                <lottie-player 
                    src="/assets/animations/404-lottie.json" 
                    background="transparent" 
                    speed="1" 
                    loop 
                    autoplay
                    class="lottie-404">
                </lottie-player>
            </div>
        `;
    }

    createOfflineError() {
        return `
            <div class="weather-offline-container">
                <div class="offline-content-wrapper">
                    <lottie-player 
                        src="/assets/animations/Error 404.json" 
                        background="transparent" 
                        speed="1" 
                        loop 
                        autoplay
                        class="offline-lottie">
                    </lottie-player>
                    <div class="offline-error-message">
                        <div class="offline-error-title">
                            <i class="fas fa-wifi-slash"></i>
                            <span>Couldn't Connect to Internet</span>
                        </div>
                        <div class="offline-error-description">
                            Unable to load weather information
                        </div>
                        <div class="offline-error-hint">
                            Check your connection and refresh the page
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    async getWeatherByLocation(latitude, longitude) {
        try {
            // Check cache first
            const cacheKey = `${latitude},${longitude}`;
            const cached = this.cache.get(cacheKey);
            if (cached && (Date.now() - cached.timestamp) < this.cacheExpiry) {
                return cached.data;
            }

            // Use the PHP backend endpoint to get weather data
            const response = await fetch(`/api/weather?latitude=${encodeURIComponent(latitude)}&longitude=${encodeURIComponent(longitude)}`);

            if (!response.ok) {
                throw new Error(`Weather API error: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Failed to fetch weather data');
            }

            // Cache the result
            this.cache.set(cacheKey, {
                timestamp: Date.now(),
                data: data.data
            });

            return data.data;
        } catch (error) {
            console.error('Weather fetch error:', error);
            return null;
        }
    }

    getFallbackIcon(condition) {
        // Map weather conditions to FontAwesome icons as fallback
        const iconMap = {
            'clear': 'sun',
            'clouds': 'cloud',
            'rain': 'cloud-rain',
            'drizzle': 'cloud-rain',
            'thunderstorm': 'cloud-bolt',
            'snow': 'snowflake',
            'mist': 'smog',
            'smoke': 'smog',
            'haze': 'smog',
            'dust': 'smog',
            'fog': 'smog',
            'sand': 'wind',
            'ash': 'smog',
            'squall': 'wind',
            'tornado': 'tornado'
        };

        // Find the matching condition (case-insensitive)
        const match = Object.keys(iconMap).find(key => 
            condition.toLowerCase().includes(key.toLowerCase())
        );

        return match ? iconMap[match] : 'cloud'; // Default to cloud if no match
    }

    createWeatherCard(city, weatherData) {
        if (!weatherData) return '';

        const advisory = this.getWeatherAdvisory(weatherData);
        const fallbackIcon = this.getFallbackIcon(weatherData.description);
        
        return `
            <div class="weather-card">
                <div class="weather-card-header">
                    <h4>${city}</h4>
                </div>
                <div class="weather-info">
                    <img src="https://openweathermap.org/img/w/${weatherData.icon}.png" 
                         alt="${weatherData.description}"
                         style="width: 50px; height: 50px;"
                         onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                    <i class="fas fa-${fallbackIcon} fa-2x" style="display:none;color:#3b82f6;"></i>
                    <div class="temperature">${Math.round(weatherData.temperature)}°C</div>
                    <div class="condition">${weatherData.description}</div>
                    <div class="weather-details">
                        <div class="weather-detail-item">
                            <i class="fas fa-droplet text-blue-400"></i>
                            <span>${weatherData.humidity}<small>%</small></span>
                        </div>
                        <div class="weather-detail-item">
                            <i class="fas fa-wind text-gray-400"></i>
                            <span>${Math.round(weatherData.wind_speed)}<small>km/h</small></span>
                        </div>
                        <div class="weather-detail-item">
                            <i class="fas fa-temperature-half text-orange-400"></i>
                            <span>${Math.round(weatherData.feels_like)}<small>°C</small></span>
                        </div>
                    </div>
                </div>
                ${advisory ? `
                <div class="weather-advisory">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>${advisory}</span>
                </div>
                ` : ''}
            </div>
        `;
    }

    getWeatherAdvisory(weather) {
        const temp = weather.temperature;
        const condition = weather.condition.toLowerCase();
        
        if (condition.includes('rain') || condition.includes('thunder')) {
            return 'Exercise caution while driving - Wet conditions';
        } else if (temp > 35) {
            return 'High temperature alert - Check AC systems';
        } else if (condition.includes('snow') || condition.includes('fog') || condition.includes('mist')) {
            return 'Reduced visibility expected - Drive carefully';
        } else if (weather.wind_speed > 50) {
            return 'Strong winds - Drive with caution';
        }
        return '';
    }

    startAutoSlide() {
        if (this.autoSlideInterval) {
            clearInterval(this.autoSlideInterval);
        }
        
        this.autoSlideInterval = setInterval(() => {
            if (this.currentSlide < this.totalSlides - 1) {
                this.nextWeather();
            } else {
                // Reset to first slide when reaching the end
                this.currentSlide = -1;
                this.nextWeather();
            }
        }, this.autoSlideDelay);
    }

    stopAutoSlide() {
        if (this.autoSlideInterval) {
            clearInterval(this.autoSlideInterval);
            this.autoSlideInterval = null;
        }
    }

    initializeWeatherCarousel() {
        const container = document.querySelector('.weather-container');
        if (!container) return;

        const slides = container.querySelectorAll('.weather-slide');
        if (slides.length <= 1) return;

        const carousel = container.closest('.weather-carousel');
        // Add class to control visibility of carousel elements
        carousel.classList.add('has-multiple-slides');

        // Initialize the dots
        const dotsContainer = carousel.querySelector('.weather-carousel-dots');
        if (dotsContainer) {
            // Clear existing dots
            dotsContainer.innerHTML = '';
            
            // Create new dots
            slides.forEach((_, i) => {
                const dot = document.createElement('div');
                dot.className = `dot${i === 0 ? ' active' : ''}`;
                dot.addEventListener('click', () => {
                    this.currentSlide = i;
                    this.updateSlidePosition();
                    this.stopAutoSlide();
                    this.startAutoSlide();
                });
                dotsContainer.appendChild(dot);
            });
        }

        this.currentSlide = 0;
        this.totalSlides = slides.length;
        this.updateSlidePosition();

        // Start auto-sliding
        this.startAutoSlide();

        // Pause auto-sliding on hover
        carousel.addEventListener('mouseenter', () => this.stopAutoSlide());
        carousel.addEventListener('mouseleave', () => this.startAutoSlide());
    }

    updateSlidePosition() {
        const container = document.querySelector('.weather-container');
        if (!container) return;
        
        container.style.transform = `translateX(-${this.currentSlide * 100}%)`;
        
        // Update dots with more specific selector
        const carousel = container.closest('.weather-carousel');
        if (carousel) {
            const dots = carousel.querySelectorAll('.weather-carousel-dots .dot');
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === this.currentSlide);
            });
        }
    }

    prevWeather() {
        if (this.currentSlide > 0) {
            this.currentSlide--;
            this.updateSlidePosition();
        }
    }

    nextWeather() {
        if (this.currentSlide < this.totalSlides - 1) {
            this.currentSlide++;
            this.updateSlidePosition();
        }
    }

    async updateCurrentLocationWeather() {
        const weatherContainer = document.getElementById('weather-alerts');
        if (!weatherContainer) return;

        // Show loading state
        weatherContainer.innerHTML = `
            <div class="loading-weather" style="text-align: center; padding: 20px; color: #94a3b8;">
                <i class="fas fa-spinner fa-spin"></i> Loading weather information...
            </div>
        `;

        try {
            // Check connectivity first
            const isConnected = await this.checkConnectivity();
            if (!isConnected) {
                this.isOffline = true;
                weatherContainer.innerHTML = this.createOfflineError();
                
                // Hide carousel navigation elements
                const carouselBtns = weatherContainer.parentElement.querySelectorAll('.carousel-btn');
                const carouselDots = weatherContainer.parentElement.querySelector('.carousel-dots');
                carouselBtns.forEach(btn => btn.style.display = 'none');
                if (carouselDots) carouselDots.style.display = 'none';
                return;
            }

            this.isOffline = false;

            const position = await new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error('Location services are not supported by this browser.'));
                    return;
                }

                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: false,
                    timeout: 10000,
                    maximumAge: 30 * 60 * 1000
                });
            });
            const weather = await this.getWeatherByLocation(
                position.coords.latitude,
                position.coords.longitude
            );

            if (!weather) {
                throw new Error('Failed to load weather information for your current location.');
            }

            weatherContainer.innerHTML = `
                <div class="weather-slide">
                    ${this.createWeatherCard(weather.location || 'Current location', weather)}
                </div>
            `;
            this.currentSlide = 0;
            this.totalSlides = 1;

        } catch (error) {
            console.error('Error updating weather:', error);
            
            // Check if it's a network error
            if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                this.isOffline = true;
                weatherContainer.innerHTML = this.createOfflineError();
            } else {
                let errorMessage = 'Failed to load weather information';
                let errorIcon = 'exclamation-circle';
                
                weatherContainer.innerHTML = `
                    <div class="error-weather" style="text-align: center; padding: 20px; color: #ef4444;">
                        <i class="fas fa-${errorIcon}"></i> ${errorMessage}
                    </div>
                `;
            }
            
            // Hide carousel navigation elements on error
            const carouselBtns = weatherContainer.parentElement.querySelectorAll('.carousel-btn');
            const carouselDots = weatherContainer.parentElement.querySelector('.carousel-dots');
            carouselBtns.forEach(btn => btn.style.display = 'none');
            if (carouselDots) carouselDots.style.display = 'none';
        }
    }
}

// Initialize weather dashboard (no API key needed as it's handled by the backend)
const weatherDashboard = new WeatherDashboard();