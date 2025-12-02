(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();


    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 0) {
            $('.navbar').addClass('position-fixed bg-dark shadow-sm');
        } else {
            $('.navbar').removeClass('position-fixed bg-dark shadow-sm');
        }
    });


    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 1500, 'easeInOutExpo');
        return false;
    });


    // Testimonials carousel
    $('.testimonial-carousel').owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        loop: true,
        nav: false,
        dots: true,
        items: 1,
        dotsData: true,
    });


    /* =========================================================
       Smooth scroll for anchor links with navbar offset
    ============================================================ */

    // helper: get navbar height (recomputes on resize)
    function navbarHeight() {
        var $nav = $('.navbar');
        return ($nav.length ? $nav.outerHeight() : 0);
    }

    // collect section ids that actually exist in DOM from anchors found in nav
    function collectAnchorTargets() {
        var ids = [];
        $('a[href*="#"]').each(function () {
            var href = $(this).attr('href');
            if (!href) return;
            
            // Fix: Abaikan jika href adalah "#!" atau hanya "#"
            if (href === "#" || href === "#!") return;

            var hashIndex = href.indexOf('#');
            if (hashIndex === -1) return;
            
            var hash = href.substring(hashIndex + 1);

            // Fix: Pastikan hash valid (tidak kosong dan bukan tanda seru)
            if (hash === '' || hash === '!') return;

            try {
                if ($('#' + hash).length) {
                    if (ids.indexOf(hash) === -1) ids.push(hash);
                }
            } catch (e) {
                // Abaikan error selector jika hash aneh
                return;
            }
        });
        return ids;
    }

    // smooth scroll to an element id (string, without #)
    function smoothScrollToId(id) {
        // Fix: Validasi ID
        if (!id || id === '!') return;

        try {
            var $target = $('#' + id);
            if (!$target.length) return;
            
            var offset = navbarHeight();
            var top = $target.offset().top - offset;
            $('html, body').animate({ scrollTop: top }, 800, 'easeInOutExpo');
            
            // update address bar hash without jumping
            try {
                history.replaceState(null, null, '#' + id);
            } catch (e) {
                // ignore
            }
        } catch (e) {
            console.log("Invalid selector skipped: " + id);
        }
    }

    // click handler for anchor links
    $(document).on('click', 'a[href*="#"]', function (e) {
        var href = $(this).attr('href');
        if (!href) return;

        // Fix: Abaikan "#!"
        if (href === '#!') return;

        // only process hashes
        if (href.indexOf('#') === -1) return;

        // If link is like "index.php#about" and current pathname does not include "index.php",
        // allow normal navigation.
        if (href.indexOf('index.php#') !== -1) {
            var path = window.location.pathname;
            var onIndex = path.endsWith('index.php') || path.endsWith('/') || path === '';
            if (!onIndex) {
                return;
            }
        }

        // if href starts with a different hostname (external) ignore
        var linkHost = this.hostname;
        if (linkHost && linkHost !== window.location.hostname) {
            return;
        }

        // Now handle same-page anchors like "#about" or "index.php#about" when already on index
        var hash = href.split('#').pop();
        
        // Fix: Jangan jalankan jika hash kosong atau "!"
        if (!hash || hash === '!') return;

        e.preventDefault();
        smoothScrollToId(hash);
    });

    // on load: if URL contains a hash (e.g., index.php#about), scroll to it
    $(window).on('load', function () {
        var hash = window.location.hash;
        if (hash && hash.length > 1 && hash !== '#!') {
            var id = hash.substring(1);
            setTimeout(function () {
                smoothScrollToId(id);
            }, 80);
        }
        // collect section ids for active nav updates
        sectionIds = collectAnchorTargets();
        updateActiveNav(); // set initial active
    });

    // Active nav update while scrolling
    var sectionIds = []; // will be set on load
    function updateActiveNav() {
        if (!sectionIds || sectionIds.length === 0) return;
        var fromTop = $(window).scrollTop() + navbarHeight() + 10;
        var current = '';
        for (var i = 0; i < sectionIds.length; i++) {
            var id = sectionIds[i];
            // Tambahkan try-catch untuk keamanan ekstra
            try {
                var $el = $('#' + id);
                if ($el.length && $el.offset().top <= fromTop) {
                    current = id;
                }
            } catch(e) {}
        }
        // toggle active class
        $('.navbar .nav-link, .navbar .dropdown-item').each(function () {
            var $a = $(this);
            var href = $a.attr('href') || '';
            var match = '';
            if (href.indexOf('#') !== -1) {
                match = href.split('#').pop();
            }
            if (match && match === current && match !== '!') {
                $a.addClass('active');
            } else {
                $a.removeClass('active');
            }
        });
    }

    $(window).on('scroll resize', function () {
        updateActiveNav();
    });

})(jQuery);