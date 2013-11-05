; /* Let's make sure all functions close before we start writing our JS code */
(function($, window, document) {
    // jQuery stuff goes here
    $('.gallery').bigGallery({
        maxWidth: 960,
        maxHeight: '',
        useArrows: true
    });
    // Menu icon for devices
    $('#menu-toggle').on('click', function() {
        $('#nav').slideToggle();
    });
    
    // convert tel links to phone number
    var current_width = $(document).width();
    if (current_width <= 640) {
        var $tel = $('.tel');
        $tel.each(function(index) {
            $('<a id="' + $($tel[index]).attr('id') + '" class="' + $($tel[index]).attr('class') + '" href="tel:' + $($tel[index]).text().replace(/\D/g, '') + '">' + $($tel[index]).text() + '</a>').insertBefore($($tel[index]));
            $($tel[index]).remove();
        });
    }
    
    /**
     * iframe width responsive
     */
    if ($('iframe').length) {
        function resizeIframe() {
            var $iframe = $('iframe'),
                    iframe_width = $iframe.width(),
                    iframe_height = Math.round(iframe_width * 9 / 16);
            $iframe.height(iframe_height);
        }
    }
    $(window).on('resize', function() {
        if ($('iframe').length) {
            resizeIframe();
        }
    });

    $(document).ready(function() {
        if ($('iframe').length) {
            resizeIframe();
        }
    });    

})(window.jQuery, window, window.document);