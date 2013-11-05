/*
 * jQuery BigGallery - v2.0
 * http://bigemployee.com/projects/big-gallery/
 *
 * A responsive jQuery slider with thumbnails
 * Based on
 * jQuery RefineSlide plugin v0.4.1
 * http://github.com/alexdunphy/refineslide
 * Requires: jQuery v1.8+
 * MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

;
(function($, window, document) {
    'use strict';
    // Baked-in settings for extension
    var defaults = {
        maxWidth: 960, // Max slider width - should be set to image width
        maxHeight: 640, // Max slider height - should be set to image height
        transition: 'fade', // String (default 'fade'): Transition type ('custom', random', 'cubeH', 'cubeV', 'fade', 'sliceH', 'sliceV', 'slideH', 'slideV', 'scale', 'blockScale', 'kaleidoscope', 'fan', 'blindH', 'blindV')
        customTransitions: [],
        fallback3d: 'fade', // String (default 'fade'): Fallback for browsers that support transitions, but not 3d transforms (only used if primary transition makes use of 3d-transforms)
        controls: 'dots', // String (default 'dots'): Control types ('custom', 'dots', 'thumbs', 'numbers', 'bars', 'none')
        fit: 'bestfit', // String (default 'bestfit'): Display fit types ('custom', 'bestfit', 'cover', 'contain', 'original', 'none')
        modal: false, // Bool (default false): Modal mode
        perspective: 1000, // Perspective (used for 3d transforms)
        swipe: true, // Bool (default true): Navigation type thumbnails
        useArrows: false, // Bool (default false): Navigation type previous and next arrows
        thumbMargin: 5, // Int (default 3): Percentage width of thumb margin
        thumbWidth: 150, // Int (default 150): Width of thumbnail image
        autoPlay: false, // Int (default false): Auto-cycle slider
        delay: 5000, // Int (default 5000) Time between slides in ms
        transitionDuration: 800, // Int (default 800): Transition length in ms
        startSlide: 0, // Int (default 0): First slide
        keyNav: true, // Bool (default true): Use left/right arrow keys to switch slide
        captionWidth: 50, // Int (default 50): Percentage of slide taken by caption
        arrowTemplate: '<div class="be-arrows"><a href="#prev" class="be-prev"><i class="fa fa-angle-left"></i></a><a href="#next" class="be-next"><i class="fa fa-angle-right"></i></a></div>', // String: The markup used for arrow controls (if arrows are used). Must use classes '.be-next' & '.be-prev'
        onInit: function() {
        }, // Func: User-defined, fires with slider initialisation
        onChange: function() {
        }, // Func: User-defined, fires with transition start
        afterChange: function() {
        }  // Func: User-defined, fires after transition end
    };
    // RS (RefineSlide) object constructor
    function RS(elem, settings) {
        this.$slider = $(elem).addClass('be-slider'); // Elem: Slider element
        this.settings = $.extend({}, defaults, settings); // Obj: Merged user settings/defaults
        this.$slides = this.$slider.find('> li'); // Elem Arr: Slide elements
        this.totalSlides = this.$slides.length; // Int: Number of slides
        this.cssTransitions = testBrowser.cssTransitions(); // Bool: Test for CSS transition support
        this.cssTransforms3d = testBrowser.cssTransforms3d(); // Bool: Test for 3D transform support
        this.currentPlace = this.settings.startSlide; // Int: Index of current slide (starts at 0)
        this.$currentSlide = this.$slides.eq(this.currentPlace); // Elem: Starting slide
        this.inProgress = false; // Bool: Prevents overlapping transitions
        this.$sliderWrap = this.$slider.wrap('<div class="be-wrap" />').parent(); // Elem: Slider wrapper div
        this.$sliderBG = this.$slider.wrap('<div class="be-slide-bg" />').parent(); // Elem: Slider background (useful for styling & essential for cube transitions)
        this.sliderWidth = this.settings.maxWidth; // Int: Slider Width by default is the max width
        this.sliderHeight = this.settings.maxHeight; // Int: Slider Height by default is the max height
        this.settings.slider = this; // Make slider object accessible to client call code with 'this.slider' (there's probably a better way to do this)
        this.isInit = true;
        this.init();
    }

    RS.prototype = {
        cycling: null,
        $slideImages: null,
        init: function() {
            // User-defined function to fire on slider initialisation
            this.settings.onInit();
            // Setup captions
            this.captions();
            if (this.settings.transition === 'custom') {
                this.nextAnimIndex = -1; // Set animation index for custom animation
            }

            if (this.settings.useArrows) {
                this.setArrows(); // Setup arrow navigation
            }

            if (this.settings.keyNav) {
                this.setKeys(); // Setup keyboard navigation
            }

            for (var i = 0; i < this.totalSlides; i++) { // Add slide identifying classes
                this.$slides.eq(i).addClass('be-slide-' + i);
            }

            if (this.settings.autoPlay) {
                this.setAutoPlay();
                // Listen for slider mouseover
                this.$slider.on({
                    mouseenter: $.proxy(function() {
                        if (this.cycling !== null) {
                            clearTimeout(this.cycling);
                        }
                    }, this),
                    mouseleave: $.proxy(this.setAutoPlay, this) // Resume slideshow
                });
            }

            // Get the first image in each slide <li>
            this.$slideImages = this.$slides.find('img:eq(0)').addClass('be-slide-image');

            this.setup();
        }

        , setup: function() {
            var that = this;
            if (this.settings.maxWidth !== '' && !this.settings.modal)
                this.$sliderWrap.css('max-width', this.settings.maxWidth);
            
            if (this.settings.maxHeight !== '' && !this.settings.modal)
                this.$sliderWrap.css({'max-height': this.settings.maxHeight, 'height': this.settings.maxHeight});
            
            if (this.settings.fit !== 'none' && this.settings.fit !== '')
                this.$sliderWrap.addClass('be-' + this.settings.fit);
            
            if (this.settings.controls !== 'none' && this.settings.controls !== 'thumbs')
                this.setControls(this.settings.controls);
            
            if (this.settings.controls === 'thumbs')
                this.setThumbs();
            
            if (this.settings.modal){
                this.$thumbModalClose = $('<i class="be-modal-close icon-remove" />');
                this.$thumbModalClose.appendTo(this.$sliderWrap);
                this.$sliderWrap.addClass('be-modal');
                this.sliderWidth = this.$slider.width();
//                console.log(this.sliderWidth + 'px');
            }
            
            // Display first slide
            this.$currentSlide.css({'opacity': 1, 'z-index': 2});

            // Load images
            // Get height and width
            // If full screen mode do the window ratio calc
            // add the top and left offset

            if (that.settings.fit === 'cover' || that.settings.fit === 'contain')
                that.fit(true);

            // Add swipe events
            if (this.settings.swipe) {
                this.$slider.on('swipeleft', function(e) {
                    e.preventDefault();
                    that.next();
                }).on('swiperight', function(e) {
                    e.preventDefault();
                    that.prev();
                });
            }
            
            // Window Resize Event
            $(window).on('resize', function() {
                if(that.settings.controls === 'thumbs')
                    that.update(true, true);
                if(that.settings.fit === 'cover' || that.settings.fit === 'contain')
                    that.fit(false);
                if(that.settings.modal){
                    that.sliderWidth = that.$slider.width();
                }
            });
        }
        , fit: function(firstRun) {
            var that = this;
            var sliderAspectRatio = 1;
            
            // @todo: can enjoy a little bit of drying over here
            if(!firstRun){
                var resizedSliderWidth = this.$slider.width();
                this.sliderHeight =  (this.settings.maxHeight === '100%') ?
                    $(window).height() : (resizedSliderWidth * (this.sliderHeight / this.sliderWidth));
                this.sliderWidth = resizedSliderWidth;
                that.$slides.css({
                    width: this.sliderWidth,
                    height: this.sliderHeight
                });
                sliderAspectRatio = this.sliderWidth / this.sliderHeight;
//                console.log('RE Slider Width: ' + that.sliderWidth);
//                console.log('RE Slider Height: ' + that.sliderHeight);
//                console.log('Slider Aspect Ratio: ' + sliderAspectRatio);
            }
            this.$slideImages.imagesLoaded()
//                    .done(function(instance){
//                        console.log('Slider Width: ' + that.$slider.width());
//                        console.log('Slider Height: ' + that.$slider.height());
//                        console.log('all images successfully loaded');
//                    })
                    .progress(function(instance, image) {
                if (firstRun) {
                    that.sliderWidth = that.$slider.width();
                    that.sliderHeight =  (that.settings.maxHeight === '100%') ?
                     $(window).height() : that.$slider.height();
                    that.$slides.css({
                        width: that.sliderWidth,
                        height: that.sliderHeight
                    });
                    sliderAspectRatio = that.sliderWidth / that.sliderHeight;
                    firstRun = false;
                    that.isInit = firstRun;
//                    console.log('Slider Width: ' + that.sliderWidth);
//                    console.log('Slider Height: ' + that.sliderHeight);
//                    console.log('Slider Aspect Ratio: ' + sliderAspectRatio);
                }
//                var result = image.isLoaded ? 'loaded' : 'broken';
//                console.log('image is ' + result + ' for ' + image.img.src);
                // Now lets find the right fit
                if (that.settings.fit === 'cover' || that.settings.fit === 'contain')
                    that.fitImages(that.settings.fit, sliderAspectRatio, image.img, image.img.width, image.img.height);
            });
        }
        , fitImages: function(fit, sliderAspectRatio, image, imageWidth, imageHeight) {
            var imageAspectRatio = imageWidth / imageHeight;
            var newWidth = this.sliderHeight * imageAspectRatio;
            var newHeight = this.sliderHeight;
            var marginTop = 0;
            var marginLeft = 0;

            if (sliderAspectRatio > imageAspectRatio) {
                if (fit === 'cover') {
                    newWidth = this.sliderWidth;
                    newHeight = this.sliderWidth * (imageHeight / imageWidth);
                    marginTop = ((this.sliderHeight - newHeight) / 2);
                    this.setImageCSS(image, newWidth, newHeight, marginTop, marginLeft);
                }
                else {
                    marginLeft = ((this.sliderWidth - newWidth) / 2);
                    this.setImageCSS(image, newWidth, newHeight, marginTop, marginLeft);
                }
            }
            else {
                if (fit === 'cover') {
                    marginLeft = ((this.sliderWidth - newWidth) / 2);
                    this.setImageCSS(image, newWidth, newHeight, marginTop, marginLeft);
                }
                else {
                    newWidth = this.sliderWidth;
                    newHeight = this.sliderWidth * (imageHeight / imageWidth);
                    marginTop = ((this.sliderHeight - newHeight) / 2);
                    this.setImageCSS(image, newWidth, newHeight, marginTop, marginLeft);
                }
            }
//            console.log("width: " + newWidth + " height: " + newHeight + " imageWidth: " + imageWidth + " imageHeight: " + imageHeight + " this.sliderHeight: " + this.sliderHeight);
        }
        , setImageCSS: function(image, newWidth, newHeight, marginTop, marginLeft){
            $(image).css({
                width: newWidth,
                height: newHeight,
                marginTop: marginTop,
                marginLeft: marginLeft,
                'max-width': 'none',
                'max-height': 'none'
            });            
        }
        , setArrows: function() {
            var that = this;
            // Append user-defined arrow template (elems) to '.be-wrap' elem
            this.$slider.after(this.settings.arrowTemplate);
            // Fire next() method when clicked
            $('.be-next', this.$sliderWrap).on('click', function(e) {
                e.preventDefault();
                that.next();
            });
            // Fire prev() method when clicked
            $('.be-prev', this.$sliderWrap).on('click', function(e) {
                e.preventDefault();
                that.prev();
            });
        }

        , setThumbArrows: function() {
            var that = this;
            // Append user-defined arrow template (elems) to '.be-wrap' elem
            this.$thumbWrap.append(this.settings.arrowTemplate);
            // Fire next() method when clicked
            this.$thumbsPrev = this.$thumbWrap.find('.be-prev');
            this.$thumbsNext = this.$thumbWrap.find('.be-next');

            this.$thumbsNext.on('click', function(e) {
                e.preventDefault();
                that.nextThumbs();
            });
            // Fire prev() method when clicked
            this.$thumbsPrev.on('click', function(e) {
                e.preventDefault();
                that.prevThumbs();
            });

            this.update(true, true);
        }

        , next: function() {
            if (this.settings.transition === 'custom') {
                this.nextAnimIndex++;
            }

            // If on final slide, loop back to first slide
            if (this.currentPlace === this.totalSlides - 1) {
                this.transition(0, true); // Call transition
            } else {
                this.transition(this.currentPlace + 1, true); // Call transition
            }
        }

        , prev: function() {
            if (this.settings.transition === 'custom') {
                this.nextAnimIndex--;
            }

            // If on first slide, loop round to final slide
            if (this.currentPlace === 0) {
                this.transition(this.totalSlides - 1, false); // Call transition
            } else {
                this.transition(this.currentPlace - 1, false); // Call transition
            }
        }

        , setKeys: function() {
            var that = this;
            // Bind keyboard left/right arrows to next/prev methods
            $(document).on('keydown', function(e) {
                if (e.keyCode === 39) { // Right arrow key
                    that.next();
                } else if (e.keyCode === 37) { // Left arrow key
                    that.prev();
                }
            });
        }

        , setAutoPlay: function() {
            var that = this;
            // Set timeout to object property so it can be accessed/cleared externally
            this.cycling = setTimeout(function() {
                that.next();
            }, this.settings.delay);
        }

        , setThumbs: function() {
            var that = this;
            // Wrapper to contain thumbnails
            this.$thumbWrap = $('<div class="be-thumb-wrap" />');
            this.$thumbWrap.appendTo(this.$sliderWrap);
            this.$thumbs = $('<div class="be-thumbs" />');
            this.$thumbs.appendTo(this.$thumbWrap);
            // initialize shift left
            this.shiftLeft = 0;
            // thumb outer width
            this.thumbOuterWidth = (this.settings.thumbWidth + this.settings.thumbMargin);
            // thumbs total width, not to forget last thumb has no margin
            // @todo test the margins, especially with box-sizing: border-box;
            this.thumbsWidth = (this.totalSlides * this.thumbOuterWidth) - this.settings.thumbMargin;
            this.getThumbWrapOffset();
            // this.thumbsWidth = (this.totalSlides * this.thumbOuterWidth);
            // Loop to apply thumbnail widths/margins to <a> wraps, appending an image clone to each
            for (var i = 0; i < this.totalSlides; i++) {
                var $thumb = $('<a />')
                        .attr('href', '#')
                        .data('be-num', i);
                this.$slideImages.eq(i).clone()
                        .removeAttr('style').css({
                    width: this.settings.thumbWidth,
                    marginRight: this.settings.thumbMargin
                })
                        .appendTo(this.$thumbs)
                        .wrap($thumb);
            }
            this.$thumbs.css({width: this.thumbsWidth});
            // set thumb arrows
            this.setThumbArrows();
            // thumbs width
            this.$controlLinks = this.$thumbs.find('a');
            // Safety margin to stop IE7 wrapping the thumbnails (no visual effect in other browsers)
            // this.$thumbs.children().last().css('margin-right', -10);

            // Add active class to starting slide's respective thumb
            this.$controlLinks.eq(this.settings.startSlide).addClass('active');
            // Listen for click events on thumnails
            this.$thumbs.on('click', 'a', function(e) {
                e.preventDefault();
                that.transition(parseInt($(this).data('be-num'))); // Call transition using identifier from thumb class
            });
        }

        , setControls: function(controlType) {
            var that = this;
            // Wrapper to contain thumbnails
            this.$controlWrap = $('<div class="be-controls be-' + controlType + '" />');
            this.$controlWrap.appendTo(this.$sliderWrap);

            // Loop to apply thumbnail widths/margins to <a> wraps, appending an image clone to each
            for (var i = 0; i < this.totalSlides; i++) {
                $('<a />')
                        .attr('href', '#')
                        .text(i + 1)
                        .data('be-num', i)
                        .appendTo(this.$controlWrap);
            }
            this.$controlLinks = this.$controlWrap.find('a');

            // Add active class to starting slide's respective thumb
            this.$controlLinks.eq(this.settings.startSlide).addClass('active');
            // Listen for click events on thumnails
            this.$controlWrap.on('click', 'a', function(e) {
                e.preventDefault();
                that.transition(parseInt($(this).data('be-num'))); // Call transition using identifier from thumb class
            });
        }

        , captions: function() {
            var that = this,
                    $captions = this.$slides.find('.be-caption');
            // User-defined caption width
            $captions.css({
                width: that.settings.captionWidth + '%',
                opacity: 0
            });
            // Display starting slide's caption
            this.$currentSlide.find('.be-caption').css('opacity', 1);
            $captions.each(function() {
                $(this).css({
                    transition: 'opacity ' + that.settings.transitionDuration + 'ms linear',
                    backfaceVisibility: 'hidden'
                });
            });
        }

        , transition: function(slideNum, forward) {
            // If inProgress flag is not set (i.e. if not mid-transition)
            if (!this.inProgress) {
                // If not already on requested slide
                if (slideNum !== this.currentPlace) {
                    // Check whether the requested slide index is ahead or behind in the array (if not passed in as param)
                    if (typeof forward === 'undefined') {
                        forward = slideNum > this.currentPlace ? true : false;
                    }

                    if (this.settings.controls !== 'none') {
                        // If controls exist, revise active class states
                        this.$controlLinks.eq(this.currentPlace).removeClass('active');
                        this.$controlLinks.eq(slideNum).addClass('active');
                    }

                    // Assign next slide prop (elem)
                    this.$nextSlide = this.$slides.eq(slideNum);
                    // Assign next slide index prop (int)
                    this.currentPlace = slideNum;
                    // User-defined function, fires with transition
                    this.settings.onChange();
                    // Instantiate new Transition object, passing in self (RS obj), transition type (string), direction (bool)
                    new Transition(this, this.settings.transition, forward);

                    if (this.settings.controls === 'thumbs' && this.isOverflowThumb(forward)) {
                        if (forward) {
                            (this.isFirstThumb()) ? this.shiftToFirstThumb() : this.nextThumbs();
                        }
                        else {
                            (this.isLastThumb()) ? this.shiftToLastThumb() : this.prevThumbs();
                        }
//                        console.log("this.isLastThumb() " + this.isLastThumb() + " Or this.isFirstThumb() " + this.isFirstThumb());
                    }
                }
            }
        }
        , getMaxShiftLeft: function() {
            // @todo: test this.settings.thumbMargin when padding for .be-thumb-wrap is 0, or when box-sizing is not border-box
            // return this.maxShiftLeft = -(this.thumbsWidth - this.thumbWrapWidth + this.settings.thumbMargin);
            return this.maxShiftLeft = -(this.thumbsWidth - this.thumbWrapWidth);
        }
        , getThumbWrapWidth: function() {
            // thumb wrapper width
            return this.thumbWrapWidth = this.$thumbWrap.outerWidth(true);
        }
        , getThumbsVisible: function() {
            // number of visible thumbs
            return this.thumbsVisible = Math.floor(this.thumbWrapWidth / this.thumbOuterWidth);
        }
        , nextThumbs: function() {
            this.shiftLeft = this.shiftLeft - (this.thumbsVisible * this.thumbOuterWidth);
            if (this.shiftLeft < this.maxShiftLeft)
                this.shiftLeft = this.maxShiftLeft;
            this.$thumbs.stop().css({
                transform: 'translate3d(' + this.shiftLeft +'px, 0, 0)'
            });
            this.update(false, true);
        }
        , prevThumbs: function() {
            this.shiftLeft = this.shiftLeft + (this.thumbsVisible * this.thumbOuterWidth);
            if (this.shiftLeft > 0)
                this.shiftLeft = 0;
            this.$thumbs.stop().css({
                transform: 'translate3d(' + this.shiftLeft +'px, 0, 0)'
            });
            this.update(false, true);
        }
        , isFirstThumb: function() {
            return (this.currentPlace === 0) ? true : false;
        }
        , isLastThumb: function() {
            return (this.currentPlace === this.totalSlides - 1) ? true : false;
        }
        , getThumbWrapOffset: function() {
            this.thumbWrapLeftOffset = this.$thumbWrap.offset().left;
            this.thumbWrapRightOffset = this.thumbWrapLeftOffset + this.thumbWrapWidth + this.settings.thumbMargin;
        }
        , isOverflowThumb: function(forward) {
            this.activeLeftOffset = this.$controlLinks.eq(this.currentPlace).offset().left;
            this.activeRightOffset = this.activeLeftOffset + this.thumbOuterWidth;
//            console.log("this.activeLeftOffset: " + this.activeLeftOffset);
//            console.log("this.activeRightOffset: " + this.activeRightOffset);

            if ((this.thumbWrapLeftOffset - this.settings.thumbMargin <= this.activeLeftOffset) && (this.activeRightOffset <= this.thumbWrapRightOffset + this.settings.thumbMargin)) {
                return false;
            }
            else if ((this.thumbWrapRightOffset + this.settings.thumbMargin < this.activeRightOffset) && forward) {
                return true;
            }
            else if ((this.activeLeftOffset < this.thumbWrapLeftOffset - this.settings.thumbMargin) && !forward) {
                return true;
            }
            else if (((this.thumbWrapRightOffset + this.settings.thumbMargin <= this.activeLeftOffset) && !forward && this.isLastThumb()) || ((this.activeRightOffset <= this.thumbWrapLeftOffset + this.settings.thumbMargin) && forward && this.isFirstThumb())) {
                return true;
            }
            return false;
        }
        , shiftToFirstThumb: function() {
            this.shiftLeft = 0;
            this.$thumbs.stop().css({
                transform: 'translate3d(' + this.shiftLeft +'px, 0, 0)'
            });
            this.update(false, true);
        }
        , shiftToLastThumb: function() {
            this.shiftLeft = this.maxShiftLeft;
            this.$thumbs.stop().css({
                transform: 'translate3d(' + this.shiftLeft +'px, 0, 0)'
            });
            this.update(false, true);
        }
        , adjustToFirstThumb: function() {
            this.shiftLeft = 0;
            this.$thumbs.css({
                marginLeft: this.shiftLeft + 'px'
            });
        }
        , adjustToLastThumb: function() {
            this.shiftLeft = this.maxShiftLeft;
            this.$thumbs.css({
                marginLeft: this.shiftLeft + 'px'
            });
        }
        , adjustThumbControls: function() {
            if (this.$thumbs.offset().left + this.$thumbs.width() < (this.thumbWrapRightOffset)) {
                this.adjustToLastThumb();
            }
            if (this.$thumbs.width() < this.$thumbWrap.width()) {
                this.adjustToFirstThumb();
                this.hideThumbControls();
            }
            else {
                this.toggleThumbControls();
            }
        }
        , toggleThumbControls: function() {
            this.showThumbControls();
            (this.shiftLeft === 0) ? this.$thumbsPrev.hide() : this.$thumbsPrev.show();
            (this.shiftLeft !== 0 && this.shiftLeft <= this.maxShiftLeft) ? this.$thumbsNext.hide() : this.$thumbsNext.show();

        }
        , showThumbControls: function() {
            this.$thumbsPrev.show();
            this.$thumbsNext.show();
        }
        , hideThumbControls: function() {
            this.$thumbsPrev.hide();
            this.$thumbsNext.hide();
        }
        , update: function(calculations, transitions) {
            if (calculations) {
                this.getThumbWrapWidth();
                this.getThumbWrapOffset();
                this.getThumbsVisible();
                this.adjustThumbControls();
            }
            if (transitions) {
                this.getMaxShiftLeft();
                this.toggleThumbControls();
            }
//
//            console.log("=========================================");
//            console.log("thumbWrapWidth: " + this.thumbWrapWidth);
//            console.log("thumbsVisible: " + this.thumbsVisible);
//            console.log("thumbsWidth: " + this.thumbsWidth);
//            console.log("shift left: " + this.shiftLeft);
//            console.log("max shift left: " + this.maxShiftLeft);
//            console.log("this.$thumbs.offset().left:" + this.$thumbs.offset().left);
//            console.log("this.thumbWrapRightOffset: " + this.thumbWrapRightOffset);
        }
    };
    // Transition object constructor
    function Transition(RS, transition, forward) {
        this.RS = RS; // RS (RefineSlide) object
        this.RS.inProgress = true; // Set RS inProgress flag to prevent additional Transition objects being instantiated until transition end
        this.forward = forward; // Bool: true for forward, false for backward
        this.transition = transition; // String: name of transition requested

        if (this.transition === 'custom') {
            this.customAnims = this.RS.settings.customTransitions;
            this.isCustomTransition = true;
        }

        // Remove incorrect specified elements from customAnims array.
        if (this.transition === 'custom') {
            var that = this;
            $.each(this.customAnims, function(i, obj) {
                if ($.inArray(obj, that.anims) === -1) {
                    that.customAnims.splice(i, 1);
                }
            });
        }

        this.fallback3d = this.RS.settings.fallback3d; // String: fallback to use when 3D transforms aren't supported

        this.init(); // Call Transition initialisation method
    }

    // Transition object Prototype
    Transition.prototype = {
        // Fallback to use if CSS transitions are unsupported
        fallback: 'fade'

                // Array of possible animations
                , anims: ['cubeH', 'cubeV', 'fade', 'sliceH', 'sliceV', 'slideH', 'slideV', 'scale', 'blockScale', 'kaleidoscope', 'fan', 'blindH', 'blindV']

                , customAnims: []

                , init: function() {
            // Call requested transition method
            this[this.transition]();
        }

        , before: function(callback) {
            var that = this;
            // Prepare slide opacity & z-index
            this.RS.$currentSlide.css('z-index', 2);
            this.RS.$nextSlide.css({'opacity': 1, 'z-index': 1});
            // Fade out/in captions with CSS/JS depending on browser capability
            if (this.RS.cssTransitions) {
                this.RS.$currentSlide.find('.be-caption').css('opacity', 0);
                this.RS.$nextSlide.find('.be-caption').css('opacity', 1);
            } else {
                this.RS.$currentSlide.find('.be-caption').animate({'opacity': 0}, that.RS.settings.transitionDuration);
                this.RS.$nextSlide.find('.be-caption').animate({'opacity': 1}, that.RS.settings.transitionDuration);
            }

            // Check if transition describes a setup method
            if (typeof this.setup === 'function') {
                // Setup required by transition
                var transition = this.setup();
                setTimeout(function() {
                    callback(transition);
                }, 20);
            } else {
                // Transition execution
                this.execute();
            }

            // Listen for CSS transition end on elem (set by transition)
            if (this.RS.cssTransitions) {
                $(this.listenTo).one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', $.proxy(this.after, this));
            }
        }

        , after: function() {
            // Reset transition CSS
            this.RS.$sliderBG.removeAttr('style');
            this.RS.$slider.removeAttr('style');
            /* @todo: known issue - removing these 2 lines messes up our 3d transitions. To be fixed later */
            /* removed for cover fill mode */
            this.RS.$currentSlide.removeAttr('style');
            this.RS.$nextSlide.removeAttr('style');
            this.RS.$currentSlide.css({
                zIndex: 1,
                opacity: 0
            });
            this.RS.$nextSlide.css({
                zIndex: 2,
                opacity: 1
            });
            // Additional reset steps required by transition (if any exist)
            if (typeof this.reset === 'function') {
                this.reset();
            }

            // If slideshow is active, reset the timeout
            if (this.RS.settings.autoPlay) {
                clearTimeout(this.RS.cycling);
                this.RS.setAutoPlay();
            }

            // Assign new slide position
            this.RS.$currentSlide = this.RS.$nextSlide;
            // Remove RS obj inProgress flag (i.e. allow new Transition to be instantiated)
            this.RS.inProgress = false;
            // User-defined function, fires after transition has ended
            this.RS.settings.afterChange();
        }

        , fade: function() {
            var that = this;
            // If CSS transitions are supported by browser
            if (this.RS.cssTransitions) {
                // Setup steps
                this.setup = function() {
                    // Set event listener to next slide elem
                    that.listenTo = that.RS.$currentSlide;
                    that.RS.$currentSlide.css('transition', 'opacity ' + that.RS.settings.transitionDuration + 'ms linear');
                };
                // Execution steps
                this.execute = function() {
                    // Display next slide over current slide
                    that.RS.$currentSlide.css('opacity', 0);
                };
            } else { // JS animation fallback
                this.execute = function() {
                    that.RS.$currentSlide.animate({'opacity': 0}, that.RS.settings.transitionDuration, function() {
                        // Reset steps
                        that.after();
                    });
                };
            }

            this.before($.proxy(this.execute, this));
        }

        // cube() method is used by cubeH() & cubeV() - not for calling directly
        , cube: function(tz, ntx, nty, nrx, nry, wrx, wry) { // Args: translateZ, (next slide) translateX, (next slide) translateY, (next slide) rotateX, (next slide) rotateY, (wrap) rotateX, (wrap) rotateY
            // Fallback if browser does not support 3d transforms/CSS transitions
            if (!this.RS.cssTransitions || !this.RS.cssTransforms3d) {
                return this[this['fallback3d']](); // User-defined transition
            }

            var that = this;
            // Setup steps
            this.setup = function() {
                // Set event listener to '.be-slider' <ul>
                that.listenTo = that.RS.$slider;
                this.RS.$sliderBG.css('perspective', 1000);
                // props for slide <li>s
                that.RS.$currentSlide.css({
                    transform: 'translateZ(' + tz + 'px)',
                    backfaceVisibility: 'hidden'
                });
                // props for next slide <li>
                that.RS.$nextSlide.css({
                    opacity: 1,
                    backfaceVisibility: 'hidden',
                    transform: 'translateY(' + nty + 'px) translateX(' + ntx + 'px) rotateY(' + nry + 'deg) rotateX(' + nrx + 'deg)'
                });
                // props for slider <ul>
                that.RS.$slider.css({
                    transform: 'translateZ(-' + tz + 'px)',
                    transformStyle: 'preserve-3d'
                });
            };
            // Execution steps
            this.execute = function() {
                that.RS.$slider.css({
                    transition: 'all ' + that.RS.settings.transitionDuration + 'ms ease-in-out',
                    transform: 'translateZ(-' + tz + 'px) rotateX(' + wrx + 'deg) rotateY(' + wry + 'deg)'
                });
            };
            this.before($.proxy(this.execute, this));
        }

        , cubeH: function() {
            // Set to half of slide width
            var dimension = $(this.RS.$slides).width() / 2;
            // If next slide is ahead in array
            if (this.forward) {
                this.cube(dimension, dimension, 0, 0, 90, 0, -90);
            } else {
                this.cube(dimension, -dimension, 0, 0, -90, 0, 90);
            }
        }

        , cubeV: function() {
            // Set to half of slide height
            var dimension = $(this.RS.$slides).height() / 2;
            // If next slide is ahead in array
            if (this.forward) {
                this.cube(dimension, 0, -dimension, 90, 0, -90, 0);
            } else {
                this.cube(dimension, 0, dimension, -90, 0, 90, 0);
            }
        }

        // grid() method is used by many transitions - not for calling directly
        // Grid calculations are based on those in the awesome flux slider (joelambert.co.uk/flux)
        , grid: function(cols, rows, ro, tx, ty, sc, op) { // Args: columns, rows, rotate, translateX, translateY, scale, opacity
            // Fallback if browser does not support CSS transitions
            if (!this.RS.cssTransitions) {
                return this[this['fallback']]();
            }

            var that = this;
            // Setup steps
            this.setup = function() {
                // The time (in ms) added to/subtracted from the delay total for each new gridlet
                var count = (that.RS.settings.transitionDuration) / (cols + rows);
                // Gridlet creator (divisions of the image grid, positioned with background-images to replicate the look of an entire slide image when assembled)
                function gridlet(width, height, top, left, src, imgWidth, imgHeight, c, r) {
                    var delay = (c + r) * count;
                    // Return a gridlet elem with styles for specific transition
                    return $('<div class="be-gridlet" />').css({
//                        width: that.RS.sliderWidth,
//                        height: that.RS.sliderHeight,
                        width: '100%',
                        height: '100%',
                        top: top,
                        left: left,
                        backgroundImage: 'url(' + src + ')',
//                        backgroundPosition: '-' + left + 'px -' + top + 'px',
                        backgroundPosition: '50% 50%',
                        backgroundSize: imgWidth + 'px ' + imgHeight + 'px',
                        backgroundRepeat: 'no-repeat',
                        transition: 'all ' + that.RS.settings.transitionDuration + 'ms ease-in-out ' + delay + 'ms',
                        transform: 'none'
                    });
                }

                // Get the next slide's image
                that.$img = that.RS.$currentSlide.find('img.be-slide-image');
                // Create a grid to hold the gridlets
                that.$grid = $('<div />').addClass('be-grid');
                // Prepend the grid to the next slide (i.e. so it's above the slide image)
                that.RS.$currentSlide.prepend(that.$grid);
                // vars to calculate positioning/size of gridlets
                var imgWidth = that.$img.width(),
                        imgHeight = that.$img.height(),
                        imgSrc = that.$img.attr('src'),
                        colWidth = Math.floor(imgWidth / cols),
                        rowHeight = Math.floor(imgHeight / rows),
                        colRemainder = imgWidth - (cols * colWidth),
                        colAdd = Math.ceil(colRemainder / cols),
                        rowRemainder = imgHeight - (rows * rowHeight),
                        rowAdd = Math.ceil(rowRemainder / rows),
                        leftDist = 0;
                // tx/ty args can be passed as 'auto'/'min-auto' (meaning use slide width/height or negative slide width/height)
                tx = tx === 'auto' ? imgWidth : tx;
                tx = tx === 'min-auto' ? -imgWidth : tx;
                ty = ty === 'auto' ? imgHeight : ty;
                ty = ty === 'min-auto' ? -imgHeight : ty;
                // Loop through cols
                for (var i = 0; i < cols; i++) {
                    var topDist = 0,
                            newColWidth = colWidth;
                    // If imgWidth (px) does not divide cleanly into the specified number of cols, adjust individual col widths to create correct total
                    if (colRemainder > 0) {
                        var add = colRemainder >= colAdd ? colAdd : colRemainder;
                        newColWidth += add;
                        colRemainder -= add;
                    }

                    // Nested loop to create row gridlets for each col
                    for (var j = 0; j < rows; j++) {
                        var newRowHeight = rowHeight,
                                newRowRemainder = rowRemainder;
                        // If imgHeight (px) does not divide cleanly into the specified number of rows, adjust individual row heights to create correct total
                        if (newRowRemainder > 0) {
                            add = newRowRemainder >= rowAdd ? rowAdd : rowRemainder;
                            newRowHeight += add;
                            newRowRemainder -= add;
                        }

                        // Create & append gridlet to grid
                        that.$grid.append(gridlet(newColWidth, newRowHeight, topDist, leftDist, imgSrc, imgWidth, imgHeight, i, j));
                        topDist += newRowHeight;
                    }

                    leftDist += newColWidth;
                }

                // Set event listener on last gridlet to finish transitioning
                that.listenTo = that.$grid.children().last();
                // Show grid & hide the image it replaces
                that.$grid.show();
                that.$img.css('opacity', 0);
                // Add identifying classes to corner gridlets (useful if applying border radius)
                that.$grid.children().first().addClass('be-top-left');
                that.$grid.children().last().addClass('be-bottom-right');
                that.$grid.children().eq(rows - 1).addClass('be-bottom-left');
                that.$grid.children().eq(-rows).addClass('be-top-right');
            };
            // Execution steps
            this.execute = function() {
                that.$grid.children().css({
                    opacity: op,
                    transform: 'rotate(' + ro + 'deg) translateX(' + tx + 'px) translateY(' + ty + 'px) scale(' + sc + ')'
                });
            };
            this.before($.proxy(this.execute, this));
            // Reset steps
            this.reset = function() {
                that.$img.css('opacity', 1);
                that.$grid.remove();
            };
        }

        , sliceH: function() {
            this.grid(1, 8, 0, 'min-auto', 0, 1, 0);
        }

        , sliceV: function() {
            this.grid(10, 1, 0, 0, 'auto', 1, 0);
        }

        , slideV: function() {
            var dir = this.forward ?
                    'min-auto' :
                    'auto';
            this.grid(1, 1, 0, 0, dir, 1, 1);
        }

        , slideH: function() {
            var dir = this.forward ?
                    'min-auto' :
                    'auto';
//            this.grid(1, 1, 0, dir, 0, 1, 1);
            var tx = dir;

            var that = this;
                tx = tx === 'auto' ? that.RS.sliderWidth : tx;
                tx = tx === 'min-auto' ? -that.RS.sliderWidth : tx;
                console.log(that.RS.sliderWidth);
            // If CSS transitions are supported by browser
            if (this.RS.cssTransitions) {
                // Setup steps
                this.setup = function() {
                    // Set event listener to next slide elem
                    that.listenTo = that.RS.$currentSlide;
                    that.RS.$currentSlide.css('transition', 'all ' + that.RS.settings.transitionDuration + 'ms linear');
                };
                // Execution steps
                this.execute = function() {
                    // Display next slide over current slide
                    that.RS.$currentSlide.css({
//                        'opacity': 0,
                        transform: 'translate3d(' + tx +'px, 0, 0)'});
//                    console.log('translate3d(' + tx +'px, 0, 0)');
                };
            } else { // JS animation fallback
                this.execute = function() {
                    that.RS.$currentSlide.animate({'opacity': 0}, that.RS.settings.transitionDuration, function(){
                        // Reset steps
                        that.after();
                    });
                };
            }
            this.before($.proxy(this.execute, this));
        }

        , scale: function() {
            this.grid(1, 1, 0, 0, 0, 1.5, 0);
        }

        , blockScale: function() {
            this.grid(8, 6, 0, 0, 0, .6, 0);
        }

        , kaleidoscope: function() {
            this.grid(10, 8, 0, 0, 0, 1, 0);
        }

        , fan: function() {
            this.grid(1, 10, 45, 100, 0, 1, 0);
        }

        , blindV: function() {
            this.grid(1, 8, 0, 0, 0, .7, 0);
        }

        , blindH: function() {
            this.grid(10, 1, 0, 0, 0, .7, 0);
        }

        , random: function() {
            // Pick a random transition from the anims array (obj prop)
            this[this.anims[Math.floor(Math.random() * this.anims.length)]]();
        }

        , custom: function() {
            if (this.RS.nextAnimIndex < 0) {
                this.RS.nextAnimIndex = this.customAnims.length - 1;
            }
            if (this.RS.nextAnimIndex === this.customAnims.length) {
                this.RS.nextAnimIndex = 0;
            }

            // Pick the next item in the list of transitions provided by user.
            this[this.customAnims[this.RS.nextAnimIndex]]();
        }
    };
    // Obj to check browser capabilities
    var testBrowser = {
        // Browser vendor CSS prefixes
        browserVendors: ['', '-webkit-', '-moz-', '-ms-', '-o-', '-khtml-']

                // Browser vendor DOM prefixes
                , domPrefixes: ['', 'Webkit', 'Moz', 'ms', 'O', 'Khtml']

                // Method to iterate over a property (using all DOM prefixes)
                // Returns true if prop is recognised by browser (else returns false)
                , testDom: function(prop) {
            var i = this.domPrefixes.length;
            while (i--) {
                if (typeof document.body.style[this.domPrefixes[i] + prop] !== 'undefined') {
                    return true;
                }
            }

            return false;
        }

        , cssTransitions: function() {
            // Use Modernizr if available & implements csstransitions test
            if (typeof window.Modernizr !== 'undefined' && Modernizr.csstransitions !== 'undefined') {
                return Modernizr.csstransitions;
            }

            // Use testDom method to check prop (returns bool)
            return this.testDom('Transition');
        }

        , cssTransforms3d: function() {
            // Use Modernizr if available & implements csstransforms3d test
            if (typeof window.Modernizr !== 'undefined' && Modernizr.csstransforms3d !== 'undefined') {
                return Modernizr.csstransforms3d;
            }

            // Check for vendor-less prop
            if (typeof document.body.style['perspectiveProperty'] !== 'undefined') {
                return true;
            }

            // Use testDom method to check prop (returns bool)
            return this.testDom('Perspective');
        }
    };
    // jQuery plugin wrapper
    $.fn['bigGallery'] = function(settings) {
        return this.each(function() {
            // Check if already instantiated on this elem
            if (!$.data(this, 'bigGallery')) {
                // Instantiate & store elem + string
                $.data(this, 'bigGallery', new RS(this, settings));
            }
        });
    };
})(window.jQuery, window, window.document);