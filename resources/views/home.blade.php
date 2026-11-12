<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Book bus seats online in minutes. Pick your trip, choose your seat and get an instant e-receipt.">
    <title>BusReserve - Book Your Bus Seat Online</title>
    <script>document.documentElement.classList.add('js');</script>
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/landing.css') }}" rel="stylesheet">
</head>
<body>
    @php
        // Hero ticket: the next real departure, or a real route when nothing is scheduled.
        $featured = $departures[0] ?? null;
        $sampleRoute = $routes[0] ?? null;
        $ticketFrom = $featured['origin'] ?? $sampleRoute['origin'] ?? 'Your city';
        $ticketTo = $featured['destination'] ?? $sampleRoute['destination'] ?? 'Anywhere';
        $ticketFare = $featured['price'] ?? $sampleRoute['base_price'] ?? null;
        $takenSeats = [2, 5, 7, 10, 16];

        $destinations = collect($routes)->pluck('destination')->unique()->values()->take(8)->all() ?: ['anywhere'];

        $fareRoutes = collect($routes)->map(fn (array $route) => [
            'label' => $route['origin'].' → '.$route['destination'],
            'price' => (float) $route['base_price'],
        ])->values()->all();
    @endphp

    <div class="scroll-progress" aria-hidden="true"><span id="scrollProgress"></span></div>

    <header class="nav" id="top">
        <div class="container nav-inner">
            <a href="{{ route('home') }}" class="brand">
                <span class="brand-icon"><i class="fas fa-bus"></i></span>
                BusReserve
            </a>

            <nav class="nav-links" id="navLinks" aria-label="Sections">
                <a href="#departures">Departures</a>
                <a href="#fares">Fare Calculator</a>
                <a href="#routes">Routes</a>
                <a href="#how">How It Works</a>
            </nav>

            <div class="nav-actions">
                @if ($user)
                    <a href="{{ $dashboardUrl }}" class="btn btn-primary"><i class="fas fa-gauge"></i> Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">Sign In</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                @endif
                <button class="nav-toggle" id="navToggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="navLinks">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    @if (session('logged_out'))
        <div class="container" role="status">
            You have been logged out.
        </div>
    @endif

    <main>
        <section class="hero">
            <div class="hero-bg" aria-hidden="true">
                <span class="orb orb-1"></span>
                <span class="orb orb-2"></span>
                <span class="orb orb-3"></span>
                <span class="hero-grid-lines"></span>
            </div>

            <div class="container hero-grid">
                <div class="hero-copy">
                    <span class="eyebrow reveal"><span class="live-dot"></span> Real-time seat availability</span>
                    <h1 class="reveal" style="--delay: 80ms">
                        Your journey to
                        <span class="rotator" id="rotator" data-words='@json($destinations)' aria-live="polite">{{ $destinations[0] }}</span>
                        <br>starts <span class="accent">here</span>.
                    </h1>
                    <p class="lead reveal" style="--delay: 160ms">
                        Find your trip, pick the exact seat you want and get your e-receipt in seconds.
                        No queues at the terminal, no guessing whether there's still room.
                    </p>
                    <div class="hero-cta reveal" style="--delay: 240ms">
                        @if ($user)
                            <a href="{{ $dashboardUrl }}" class="btn btn-primary btn-lg btn-shine">Go to Dashboard <i class="fas fa-arrow-right"></i></a>
                            @unless ($user->isAdmin())
                                <a href="{{ route('customer.bookings') }}" class="btn btn-ghost btn-lg"><i class="fas fa-ticket"></i> Book a Seat</a>
                            @endunless
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg btn-shine">Create Free Account <i class="fas fa-arrow-right"></i></a>
                            <a href="#fares" class="btn btn-ghost btn-lg"><i class="fas fa-calculator"></i> Check Fares</a>
                        @endif
                    </div>

                    <dl class="stats reveal" style="--delay: 320ms">
                        <div class="stat">
                            <dt><i class="fas fa-route"></i> Active routes</dt>
                            <dd data-count="{{ $stats['routes'] }}">{{ $stats['routes'] }}</dd>
                        </div>
                        <div class="stat">
                            <dt><i class="fas fa-bus"></i> Buses in service</dt>
                            <dd data-count="{{ $stats['buses'] }}">{{ $stats['buses'] }}</dd>
                        </div>
                        <div class="stat">
                            <dt><i class="fas fa-calendar-check"></i> Upcoming trips</dt>
                            <dd data-count="{{ $stats['trips'] }}">{{ $stats['trips'] }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="hero-visual reveal reveal-scale" style="--delay: 200ms">
                    <div class="ticket-wrap" id="ticketTilt">
                        <div class="ticket">
                            <div class="ticket-shine" aria-hidden="true"></div>
                            <div class="ticket-top">
                                <span class="ticket-label"><i class="fas fa-bus"></i> BusReserve E-Ticket</span>
                                <span class="badge badge-confirmed"><i class="fas fa-check"></i> Confirmed</span>
                            </div>
                            <div class="ticket-route">
                                <div>
                                    <span class="ticket-small">From</span>
                                    <strong>{{ $ticketFrom }}</strong>
                                </div>
                                <span class="ticket-path" aria-hidden="true"><i class="fas fa-bus-simple"></i></span>
                                <div class="text-right">
                                    <span class="ticket-small">To</span>
                                    <strong>{{ $ticketTo }}</strong>
                                </div>
                            </div>
                            <div class="ticket-meta">
                                <div>
                                    <span class="ticket-small">Departure</span>
                                    <span>{{ $featured ? \Illuminate\Support\Carbon::parse($featured['departure_time'])->format('M j, g:i A') : 'Next available' }}</span>
                                </div>
                                <div>
                                    <span class="ticket-small">Seat</span>
                                    <span class="seat-label" id="seatLabel">S12</span>
                                </div>
                                <div>
                                    <span class="ticket-small">Fare</span>
                                    <span>{{ $ticketFare !== null ? '₱'.number_format((float) $ticketFare, 2) : '—' }}</span>
                                </div>
                            </div>
                            <div class="ticket-divider"></div>
                            <div class="seat-preview" id="seatPreview" role="group" aria-label="Try picking a seat">
                                @for ($seat = 1; $seat <= 18; $seat++)
                                    <button type="button"
                                        @class(['seat', 'seat-taken' => in_array($seat, $takenSeats), 'seat-mine' => $seat === 12])
                                        data-seat="{{ $seat }}"
                                        aria-label="Seat {{ $seat }}{{ in_array($seat, $takenSeats) ? ' (taken)' : '' }}"
                                        aria-pressed="{{ $seat === 12 ? 'true' : 'false' }}"></button>
                                @endfor
                            </div>
                            <div class="seat-legend">
                                <span><i class="dot dot-free"></i> Available</span>
                                <span><i class="dot dot-taken"></i> Taken</span>
                                <span><i class="dot dot-mine"></i> Your seat</span>
                            </div>
                            <p class="seat-hint"><i class="fas fa-hand-pointer"></i> Tap a seat to try it</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="road" aria-hidden="true">
                <div class="road-lines"></div>
                <div class="road-bus"><i class="fas fa-bus-simple"></i><span class="road-exhaust"></span></div>
            </div>
        </section>

        <section class="section" id="departures">
            <div class="container">
                <div class="section-head reveal">
                    <div>
                        <span class="eyebrow">Leaving soon</span>
                        <h2>Upcoming departures</h2>
                    </div>
                    <a href="{{ $bookUrl }}" class="link">Book a seat <i class="fas fa-arrow-right"></i></a>
                </div>

                @if ($departures === [])
                    <div class="empty reveal">
                        <div class="empty-icon"><i class="fas fa-calendar-xmark"></i></div>
                        <p>No trips are scheduled right now. New departures are added regularly. Check back soon.</p>
                    </div>
                @else
                    <div class="trip-grid">
                        @foreach ($departures as $trip)
                            @php
                                $departs = \Illuminate\Support\Carbon::parse($trip['departure_time']);
                                $arrives = $trip['arrival_time'] ? \Illuminate\Support\Carbon::parse($trip['arrival_time']) : null;
                                $seatsLeft = max(0, (int) $trip['seats_left']);
                                $fillPercent = $trip['capacity'] > 0 ? round(($trip['capacity'] - $seatsLeft) / $trip['capacity'] * 100) : 0;
                            @endphp
                            <a href="{{ $bookUrl }}" class="card trip spotlight reveal" style="--delay: {{ $loop->index * 90 }}ms">
                                <div class="trip-date">
                                    <span class="trip-day"><i class="far fa-calendar"></i> {{ $departs->format('D, M j') }}</span>
                                    <span @class(['badge', 'badge-low' => $seatsLeft <= 5, 'badge-open' => $seatsLeft > 5])>
                                        {{ $seatsLeft === 0 ? 'Full' : $seatsLeft.' '.str('seat')->plural($seatsLeft).' left' }}
                                    </span>
                                </div>
                                <h3 class="trip-route">{{ $trip['origin'] }} <i class="fas fa-arrow-right"></i> {{ $trip['destination'] }}</h3>
                                <div class="trip-times">
                                    <span><i class="far fa-clock"></i> {{ $departs->format('g:i A') }}</span>
                                    @if ($arrives)
                                        <span class="muted">→ {{ $arrives->format('g:i A') }} · {{ $departs->diff($arrives)->format('%hh %Im') }}</span>
                                    @endif
                                </div>
                                <div class="meter" role="img" aria-label="{{ $fillPercent }}% of seats booked"><span style="--fill: {{ $fillPercent }}%"></span></div>
                                <div class="trip-foot">
                                    <span class="muted"><i class="fas fa-bus"></i> {{ $trip['bus_model'] ?: 'Bus' }}</span>
                                    <span class="price">₱{{ number_format((float) $trip['price'], 2) }}</span>
                                </div>
                                <span class="trip-cta">Reserve <i class="fas fa-arrow-right"></i></span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="section section-alt" id="fares">
            <div class="container">
                <div class="section-head reveal">
                    <div>
                        <span class="eyebrow">Plan your budget</span>
                        <h2>Fare calculator</h2>
                    </div>
                </div>

                @if ($fareRoutes === [])
                    <div class="empty reveal">
                        <div class="empty-icon"><i class="fas fa-calculator"></i></div>
                        <p>Fares will be available once routes are published.</p>
                    </div>
                @else
                    <div class="calc reveal" id="fareCalc" data-routes='@json($fareRoutes)' data-discount="{{ $discountPercent }}">
                        <div class="calc-form">
                            <label class="field">
                                <span class="field-label"><i class="fas fa-route"></i> Route</span>
                                <span class="select-wrap">
                                    <select id="calcRoute">
                                        @foreach ($fareRoutes as $index => $fareRoute)
                                            <option value="{{ $index }}">{{ $fareRoute['label'] }}</option>
                                        @endforeach
                                    </select>
                                </span>
                            </label>

                            <div class="field">
                                <span class="field-label"><i class="fas fa-user-tag"></i> Passenger type</span>
                                <div class="segmented" role="radiogroup" aria-label="Passenger type">
                                    <button type="button" class="segment is-active" role="radio" aria-checked="true" data-discounted="0">Regular</button>
                                    <button type="button" class="segment" role="radio" aria-checked="false" data-discounted="1">Student</button>
                                    <button type="button" class="segment" role="radio" aria-checked="false" data-discounted="1">Senior / PWD</button>
                                    <span class="segment-pill" aria-hidden="true"></span>
                                </div>
                            </div>

                            <div class="field">
                                <span class="field-label"><i class="fas fa-users"></i> Passengers</span>
                                <div class="stepper">
                                    <button type="button" id="calcMinus" aria-label="Fewer passengers"><i class="fas fa-minus"></i></button>
                                    <output id="calcCount" aria-live="polite">1</output>
                                    <button type="button" id="calcPlus" aria-label="More passengers"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="calc-result">
                            <span class="calc-caption">Estimated total</span>
                            <div class="calc-total">₱<span id="calcTotal">0.00</span></div>
                            <ul class="calc-breakdown">
                                <li><span>Base fare</span><span id="calcBase">₱0.00</span></li>
                                <li class="calc-discount" id="calcDiscountRow"><span>Discount ({{ $discountPercent }}%)</span><span id="calcDiscount">−₱0.00</span></li>
                                <li><span>Passengers</span><span>× <span id="calcCountEcho">1</span></span></li>
                            </ul>
                            <a href="{{ $bookUrl }}" class="btn btn-light btn-lg btn-block">Book this trip <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="section" id="routes">
            <div class="container">
                <div class="section-head reveal">
                    <div>
                        <span class="eyebrow">Where we go</span>
                        <h2>Routes &amp; base fares</h2>
                    </div>
                    @if ($routes !== [])
                        <label class="search">
                            <i class="fas fa-magnifying-glass"></i>
                            <input type="search" id="routeSearch" placeholder="Search a city…" aria-label="Search routes" autocomplete="off">
                        </label>
                    @endif
                </div>

                @if ($routes === [])
                    <div class="empty reveal">
                        <div class="empty-icon"><i class="fas fa-route"></i></div>
                        <p>Routes will appear here once they're published.</p>
                    </div>
                @else
                    <div class="route-list" id="routeList">
                        @foreach ($routes as $route)
                            <div @class(['route-row', 'reveal', 'is-extra' => $loop->index >= 8]) style="--delay: {{ ($loop->index % 8) * 60 }}ms"
                                data-search="{{ strtolower($route['origin'].' '.$route['destination']) }}">
                                <span class="route-icon"><i class="fas fa-route"></i></span>
                                <div class="route-name">
                                    <strong>{{ $route['origin'] }} → {{ $route['destination'] }}</strong>
                                    @if ($route['distance_km'])
                                        <span class="muted">{{ rtrim(rtrim(number_format((float) $route['distance_km'], 2), '0'), '.') }} km</span>
                                    @endif
                                </div>
                                <div class="route-fare">
                                    <span class="price">₱{{ number_format((float) $route['base_price'], 2) }}</span>
                                    <span class="muted discount-fare">₱{{ number_format((float) $route['base_price'] * (100 - $discountPercent) / 100, 2) }} discounted</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="no-match" id="routeNoMatch" hidden><i class="fas fa-face-frown"></i> No routes match “<span id="routeQuery"></span>”.</p>
                    @if (count($routes) > 8)
                        <div class="center-row">
                            <button type="button" class="btn btn-ghost" id="routeMore" aria-expanded="false">
                                Show all {{ count($routes) }} routes <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </section>

        <section class="section section-alt" id="how">
            <div class="container">
                <div class="section-head center reveal">
                    <div>
                        <span class="eyebrow">Simple as 1-2-3</span>
                        <h2>How booking works</h2>
                    </div>
                </div>
                <ol class="steps">
                    <li class="card step spotlight reveal" style="--delay: 0ms">
                        <span class="step-num">1</span>
                        <span class="step-icon"><i class="fas fa-user-plus"></i></span>
                        <h3>Create your account</h3>
                        <p>Sign up in under a minute. Tell us if you're a student, senior or PWD so your discount applies automatically.</p>
                    </li>
                    <li class="card step spotlight reveal" style="--delay: 140ms">
                        <span class="step-num">2</span>
                        <span class="step-icon"><i class="fas fa-couch"></i></span>
                        <h3>Pick a trip and seat</h3>
                        <p>Browse scheduled departures and choose your exact seat from the live seat map.</p>
                    </li>
                    <li class="card step spotlight reveal" style="--delay: 280ms">
                        <span class="step-num">3</span>
                        <span class="step-icon"><i class="fas fa-receipt"></i></span>
                        <h3>Get your e-receipt</h3>
                        <p>Your booking code and receipt appear instantly. You can print it or keep it on your phone.</p>
                    </li>
                </ol>
            </div>
        </section>

        <section class="section" id="discounts">
            <div class="container">
                <div class="discount reveal">
                    <div class="discount-badge">
                        <span class="discount-ring" aria-hidden="true"></span>
                        {{ $discountPercent }}%<small>OFF</small>
                    </div>
                    <div class="discount-copy">
                        <h2>Discounted fares for students, seniors &amp; PWD</h2>
                        <p>
                            Register with the matching passenger type and every fare is automatically reduced by
                            {{ $discountPercent }}%. Nothing to claim at the counter.
                        </p>
                        <ul class="pills">
                            <li><i class="fas fa-graduation-cap"></i> Students</li>
                            <li><i class="fas fa-person-cane"></i> Senior Citizens</li>
                            <li><i class="fas fa-wheelchair"></i> Persons with Disability</li>
                        </ul>
                    </div>
                </div>

                <div class="features">
                    <div class="feature spotlight reveal" style="--delay: 0ms">
                        <span class="feature-icon"><i class="fas fa-chair"></i></span>
                        <h3>Choose your seat</h3>
                        <p>See which seats are free and pick the one you want.</p>
                    </div>
                    <div class="feature spotlight reveal" style="--delay: 90ms">
                        <span class="feature-icon"><i class="fas fa-rotate-left"></i></span>
                        <h3>Easy cancellation</h3>
                        <p>Cancel before departure and your payment is refunded.</p>
                    </div>
                    <div class="feature spotlight reveal" style="--delay: 180ms">
                        <span class="feature-icon"><i class="fas fa-clock-rotate-left"></i></span>
                        <h3>Trip history</h3>
                        <p>All your bookings and transactions in one place.</p>
                    </div>
                    <div class="feature spotlight reveal" style="--delay: 270ms">
                        <span class="feature-icon"><i class="fas fa-cloud-sun"></i></span>
                        <h3>Weather updates</h3>
                        <p>Check conditions at your destination before you go.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section-cta">
            <div class="container">
                <div class="cta reveal">
                    <span class="cta-bus" aria-hidden="true"><i class="fas fa-bus"></i></span>
                    <div>
                        <h2>Ready to travel?</h2>
                        <p>Reserve your seat now and skip the terminal line.</p>
                    </div>
                    @if ($user)
                        <a href="{{ $dashboardUrl }}" class="btn btn-light btn-lg">Go to Dashboard <i class="fas fa-arrow-right"></i></a>
                    @else
                        <div class="cta-actions">
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg">Create Account</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Sign In</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <a href="#top" class="brand">
                <span class="brand-icon"><i class="fas fa-bus"></i></span>
                BusReserve
            </a>
            <p class="muted">© {{ now()->year }} BusReserve. Your journey starts here.</p>
        </div>
    </footer>

    <button type="button" class="to-top" id="toTop" aria-label="Back to top"><i class="fas fa-arrow-up"></i></button>

    <script src="{{ asset('assets/js/landing.js') }}" defer></script>
</body>
</html>
