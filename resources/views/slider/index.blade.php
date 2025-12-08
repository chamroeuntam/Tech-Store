@vite(['resources/css/user-management.css'])
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
/* --- HERO BANNER WRAPPER --- */
.hero-banner {
    position: relative;
    width: 100%;
    max-width: 1270px;
    height: 220px;
    margin: 0 auto;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 32px 0 rgba(0,0,0,0.08);
}

/* --- SWIPER CONTAINER --- */
.swiper {
    width: 100%;
    height: 220px;
}

/* --- SLIDE IMAGE --- */
.swiper-slide img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 1rem;
}

/* --- NAVIGATION BUTTONS --- */
.swiper-button-next,
.swiper-button-prev {
    color: #6365f100 !important;
    width: 36px;
    height: 36px;
    box-shadow: 0 2px 8px 0 rgba(99,102,241,0.08);
    transition: background 0.2s;
}
.swiper-button-next:hover,
.swiper-button-prev:hover {
    color: #3b82f6 !important;
    
}

/* --- PAGINATION --- */
.swiper-pagination-bullet {
    background: #6366f1;
    opacity: 0.7;
}
.swiper-pagination-bullet-active {
    background: #3b82f6;
}

/* --- RESPONSIVE --- */
@media (max-width: 768px) {
    .hero-banner, .swiper, .swiper-slide img { height: 120px; }
}
@media (max-width: 480px) {
    .hero-banner, .swiper, .swiper-slide img { height: 70px; }
    .swiper-button-next,
    .swiper-button-prev { display: none; }
}
</style>

<!-- =========================== -->
<!--         SLIDESHOW           -->
<!-- =========================== -->
<section class="hero-banner my-4">
    <div class="swiper heroSwiper">
        <div class="swiper-wrapper">
            @foreach($sliders as $slider)
                <div class="swiper-slide">
                    @if($slider->link)
                        <a href="{{ $slider->link }}" target="_blank" rel="noopener">
                            <img src="{{ asset('storage/' . $slider->image_path) }}" alt="{{ $slider->title }}">
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $slider->image_path) }}" alt="{{ $slider->title }}">
                    @endif
                </div>
            @endforeach
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    new Swiper(".heroSwiper", {
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        }
    });
});
</script>
