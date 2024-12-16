require(['jquery', 'jquery/ui', 'slick'], function($) {
    $(document).ready(function() {
        $(".slick-slider").slick({
            dots: true,
            infinite: false,
            slidesToShow: 1,
            slidesToScroll: 1
        });
    });
});
