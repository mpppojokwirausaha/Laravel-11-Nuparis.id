// video fullscreen
const videoContainer = document.getElementById("videoContainer");
const video = document.getElementById("videoPlayer");
const fullscreenBtn = document.getElementById("fullscreenBtn");
const expandIcon = document.getElementById("expandIcon");
const collapseIcon = document.getElementById("collapseIcon");

// Toggle fullscreen (TANPA AUDIO LOGIC)
function toggleFullscreen() {
    if (!document.fullscreenElement) {
        videoContainer.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

// Handle fullscreen change (AUDIO DI SINI)
function handleFullscreenChange() {
    if (document.fullscreenElement === videoContainer) {
        expandIcon.classList.add("hidden");
        collapseIcon.classList.remove("hidden");

        video.muted = false;
        video.volume = 0.7;
    } else {
        expandIcon.classList.remove("hidden");
        collapseIcon.classList.add("hidden");

        setTimeout(() => {
            video.muted = true;
            video.volume = 0;
        }, 150);
    }
}

fullscreenBtn.addEventListener("click", toggleFullscreen);
videoContainer.addEventListener("dblclick", toggleFullscreen);

document.addEventListener("fullscreenchange", handleFullscreenChange);
document.addEventListener("webkitfullscreenchange", handleFullscreenChange);
document.addEventListener("mozfullscreenchange", handleFullscreenChange);
document.addEventListener("MSFullscreenChange", handleFullscreenChange);

// Swiper.js
const swiper = new Swiper(".reviewSwiper", {
    slidesPerView: 1,
    spaceBetween: 20,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
    loop: true,
    speed: 600,
    breakpoints: {
        320: {
            slidesPerView: 1,
            spaceBetween: 16,
        },
        768: {
            slidesPerView: 1,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 1,
            spaceBetween: 24,
        },
    },
});

// Marquee Duplication
document.querySelectorAll(".marquee-track").forEach((track) => {
    const container = track.closest(".marquee-container");
    const sets = track.querySelectorAll(".marquee-set");

    let totalWidth = 0;
    sets.forEach((set) => (totalWidth += set.offsetWidth));

    const containerWidth = container.offsetWidth;

    while (totalWidth < containerWidth * 2) {
        const clone = sets[0].cloneNode(true);
        clone.setAttribute("aria-hidden", "true");
        track.appendChild(clone);
        totalWidth += clone.offsetWidth;
    }
});

document.addEventListener("DOMContentLoaded", function () {
    // Navbar Scroll Effect
    function handleNavbarScroll() {
        const navbar = document.getElementById("main-navbar");
        const navbarContent = document.getElementById("navbar-content");
        const logoContainer = document.getElementById("logo-container");
        const logoText = document.getElementById("logo-text");
        const navLinks = document.querySelectorAll(".nav-link");
        const navHome = document.getElementById("nav-home");
        const loginButton = document.getElementById("login-button");

        if (!navbar) return;

        const scrollPosition = window.scrollY;
        const scrollThreshold = 50;

        if (scrollPosition > scrollThreshold) {
            navbar.classList.remove("bg-transparent");
            navbar.classList.add("bg-white", "shadow-md", "backdrop-blur-sm");

            if (navbarContent) navbarContent.classList.replace("py-2", "py-0");
            if (logoContainer)
                logoContainer.classList.replace("bg-white/90", "bg-white");
            if (logoText)
                logoText.classList.replace("text-white", "text-slate-800");

            navLinks.forEach((link) => {
                link.classList.replace("text-white/90", "text-slate-600");
                link.classList.replace(
                    "hover:text-white",
                    "hover:text-primary",
                );
            });

            if (navHome) {
                navHome.classList.replace("text-white", "text-primary");
                navHome.classList.replace("border-white", "border-primary");
            }

            if (loginButton) {
                loginButton.classList.replace("bg-white", "bg-primary");
                loginButton.classList.replace("text-primary", "text-white");
                loginButton.classList.add("hover:bg-red-700");
            }
        } else {
            navbar.classList.remove(
                "bg-white",
                "shadow-md",
                "backdrop-blur-sm",
            );
            navbar.classList.add("bg-transparent");

            if (navbarContent) navbarContent.classList.replace("py-0", "py-2");
            if (logoContainer)
                logoContainer.classList.replace("bg-white", "bg-white/90");
            if (logoText)
                logoText.classList.replace("text-slate-800", "text-white");

            navLinks.forEach((link) => {
                link.classList.replace("text-slate-600", "text-white/90");
                link.classList.replace(
                    "hover:text-primary",
                    "hover:text-white",
                );
            });

            if (navHome) {
                navHome.classList.replace("text-primary", "text-white");
                navHome.classList.replace("border-primary", "border-white");
            }

            if (loginButton) {
                loginButton.classList.replace("bg-primary", "bg-white");
                loginButton.classList.replace("text-white", "text-primary");
                loginButton.classList.remove("hover:bg-red-700");
            }
        }
    }

    handleNavbarScroll();
    window.addEventListener("scroll", handleNavbarScroll);
    window.addEventListener("resize", handleNavbarScroll);

    // Prevent iOS zoom on input focus
    if (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) {
        const inputs = document.querySelectorAll("input, textarea, select");
        inputs.forEach((input) => {
            input.style.fontSize = "16px";
        });
    }
});
