/*
 *  Document   : app.js
 *  Author     : pixelcave
 *  Description: Custom scripts and plugin initializations (available to all pages)
 *
 *  Feel free to remove the plugin initilizations from uiInit() if you would like to
 *  use them only in specific pages. Also, if you remove a js plugin you won't use, make
 *  sure to remove its initialization from uiInit().
 */

var App = function() {

    /* Cache variables of some often used jquery objects */
    var page            = jQuery('#page-container');
    var pageContent     = jQuery('#page-content');
    var header          = jQuery('header');
    var footer          = jQuery('#page-content + footer');

    /* Sidebar */
    var sidebar         = jQuery('#sidebar');
    var sidebarAlt      = jQuery('#sidebar-alt');
    var sScroll         = jQuery('.sidebar-scroll');

    /* Initialization UI Code */
    var uiInit = function() {

        // Initialize sidebars functionality
        handleSidebar('init');

        // Sidebar navigation functionality
        handleNav();

        // Scroll to top functionality
        scrollToTop();

        // Template Options, change features
        templateOptions();

        // Resize #page-content to fill empty space if exists (also add it to resize and orientationchange events)
        resizePageContent();
        jQuery(window).resize(function(){ resizePageContent(); });
        jQuery(window).bind('orientationchange', resizePageContent);

        // Add the correct copyright year at the footer
        var yearCopy = jQuery('#year-copy'), d = new Date();
        if (d.getFullYear() === 2014) { yearCopy.html('2014'); } else { yearCopy.html('2014-' + d.getFullYear().toString().substr(2,2)); }

        // Initialize chat demo functionality (in sidebar)
        chatUi();

        // Initialize tabs
        jQuery('[data-toggle="tabs"] a, .enable-tabs a').click(function(e){ e.preventDefault(); jQuery(this).tab('show'); });

        // Initialize Tooltips
        jQuery('[data-toggle="tooltip"], .enable-tooltip').tooltip({container: 'body', animation: false});

        // Initialize Popovers
        jQuery('[data-toggle="popover"], .enable-popover').popover({container: 'body', animation: true});

        // Initialize single image lightbox
        jQuery('[data-toggle="lightbox-image"]').magnificPopup({type: 'image', image: {titleSrc: 'title'}});

        // Initialize image gallery lightbox
        jQuery('[data-toggle="lightbox-gallery"]').magnificPopup({
            delegate: 'a.gallery-link',
            type: 'image',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                arrowMarkup: '<button type="button" class="mfp-arrow mfp-arrow-%dir%" title="%title%"></button>',
                tPrev: 'Previous',
                tNext: 'Next',
                tCounter: '<span class="mfp-counter">%curr% of %total%</span>'
            },
            image: {titleSrc: 'title'}
        });

        // Initialize Editor
        // jQuery('.textarea-editor').wysihtml5();

        // Initialize Chosen
        jQuery('.select-chosen').chosen({width: "100%"});

        // Initiaze Slider for Bootstrap
        jQuery('.input-slider').slider();

        // Initialize Tags Input
        jQuery('.input-tags').tagsInput({ width: 'auto', height: 'auto'});

        // Initialize Datepicker
        jQuery('.input-datepicker, .input-daterange').datepicker({weekStart: 1});
        jQuery('.input-datepicker-close').datepicker({weekStart: 1}).on('changeDate', function(e){ jQuery(this).datepicker('hide'); });

        // Initialize Timepicker
        jQuery('.input-timepicker').timepicker({minuteStep: 1,showSeconds: true,showMeridian: true});
        jQuery('.input-timepicker24').timepicker({minuteStep: 1,showSeconds: true,showMeridian: false});

        // Easy Pie Chart
        jQuery('.pie-chart').easyPieChart({
            barColor: jQuery(this).data('bar-color') ? jQuery(this).data('bar-color') : '#777777',
            trackColor: jQuery(this).data('track-color') ? jQuery(this).data('track-color') : '#eeeeee',
            lineWidth: jQuery(this).data('line-width') ? jQuery(this).data('line-width') : 3,
            size: jQuery(this).data('size') ? jQuery(this).data('size') : '80',
            animate: 800,
            scaleColor: false
        });

        // Initialize Placeholder
        jQuery('input, textarea').placeholder();
    };

    /* Sidebar Navigation functionality */
    var handleNav = function() {

        // Animation Speed, change the values for different results
        var upSpeed     = 250;
        var downSpeed   = 250;

        // Get all vital links
        var allTopLinks     = jQuery('.sidebar-nav a');
        var menuLinks       = jQuery('.sidebar-nav-menu');
        var submenuLinks    = jQuery('.sidebar-nav-submenu');

        // Primary Accordion functionality
        menuLinks.click(function(){
            var link = jQuery(this);

            if (link.parent().hasClass('active') !== true) {
                if (link.hasClass('open')) {
                    link.removeClass('open').next().slideUp(upSpeed);

                    // Resize #page-content to fill empty space if exists
                    setTimeout(resizePageContent, upSpeed);
                }
                else {
                    jQuery('.sidebar-nav-menu.open').removeClass('open').next().slideUp(upSpeed);
                    link.addClass('open').next().slideDown(downSpeed);

                    // Resize #page-content to fill empty space if exists
                    setTimeout(resizePageContent, ((upSpeed > downSpeed) ? upSpeed : downSpeed));
                }
            }

            return false;
        });

        // Submenu Accordion functionality
        submenuLinks.click(function(){
            var link = jQuery(this);

            if (link.parent().hasClass('active') !== true) {
                if (link.hasClass('open')) {
                    link.removeClass('open').next().slideUp(upSpeed);

                    // Resize #page-content to fill empty space if exists
                    setTimeout(resizePageContent, upSpeed);
                }
                else {
                    link.closest('ul').find('.sidebar-nav-submenu.open').removeClass('open').next().slideUp(upSpeed);
                    link.addClass('open').next().slideDown(downSpeed);

                    // Resize #page-content to fill empty space if exists
                    setTimeout(resizePageContent, ((upSpeed > downSpeed) ? upSpeed : downSpeed));
                }
            }

            return false;
        });
    };

    /* Sidebar Functionality */
    var handleSidebar = function(mode, extra) {
        if (mode === 'init') {
            // Init sidebars scrolling (if we have a fixed header)
            if (header.hasClass('navbar-fixed-top') || header.hasClass('navbar-fixed-bottom')) {
                handleSidebar('sidebar-scroll');
            }

            // Close the other sidebar if we hover over a partial one
            // In smaller screens (the same applies to resized browsers) two visible sidebars
            // could mess up our main content (not enough space), so we hide the other one :-)
            jQuery('.sidebar-partial #sidebar')
                .mouseenter(function(){ handleSidebar('close-sidebar-alt'); });
            jQuery('.sidebar-alt-partial #sidebar-alt')
                .mouseenter(function(){ handleSidebar('close-sidebar'); });
        } else {
            var windowW = window.innerWidth
                        || document.documentElement.clientWidth
                        || document.body.clientWidth;

            if (mode === 'toggle-sidebar') {
                if ( windowW > 991) { // Toggle main sidebar in large screens (> 991px)
                    page.toggleClass('sidebar-visible-lg');

                    if (page.hasClass('sidebar-visible-lg')) {
                        handleSidebar('close-sidebar-alt');
                    }

                    // If 'toggle-other' is set, open the alternative sidebar when we close this one
                    if (extra === 'toggle-other') {
                        if (!page.hasClass('sidebar-visible-lg')) {
                            handleSidebar('open-sidebar-alt');
                        }
                    }
                } else { // Toggle main sidebar in small screens (< 992px)
                    page.toggleClass('sidebar-visible-xs');

                    if (page.hasClass('sidebar-visible-xs')) {
                        handleSidebar('close-sidebar-alt');
                    }
                }
            } else if (mode === 'toggle-sidebar-alt') {
                if ( windowW > 991) { // Toggle alternative sidebar in large screens (> 991px)
                    page.toggleClass('sidebar-alt-visible-lg');

                    if (page.hasClass('sidebar-alt-visible-lg')) {
                        handleSidebar('close-sidebar');
                    }

                    // If 'toggle-other' is set open the main sidebar when we close the alternative
                    if (extra === 'toggle-other') {
                        if (!page.hasClass('sidebar-alt-visible-lg')) {
                            handleSidebar('open-sidebar');
                        }
                    }
                } else { // Toggle alternative sidebar in small screens (< 992px)
                    page.toggleClass('sidebar-alt-visible-xs');

                    if (page.hasClass('sidebar-alt-visible-xs')) {
                        handleSidebar('close-sidebar');
                    }
                }
            }
            else if (mode === 'open-sidebar') {
                if ( windowW > 991) { // Open main sidebar in large screens (> 991px)
                    page.addClass('sidebar-visible-lg');
                } else { // Open main sidebar in small screens (< 992px)
                    page.addClass('sidebar-visible-xs');
                }

                // Close the other sidebar
                handleSidebar('close-sidebar-alt');
            }
            else if (mode === 'open-sidebar-alt') {
                if ( windowW > 991) { // Open alternative sidebar in large screens (> 991px)
                    page.addClass('sidebar-alt-visible-lg');
                } else { // Open alternative sidebar in small screens (< 992px)
                    page.addClass('sidebar-alt-visible-xs');
                }

                // Close the other sidebar
                handleSidebar('close-sidebar');
            }
            else if (mode === 'close-sidebar') {
                if ( windowW > 991) { // Close main sidebar in large screens (> 991px)
                    page.removeClass('sidebar-visible-lg');
                } else { // Close main sidebar in small screens (< 992px)
                    page.removeClass('sidebar-visible-xs');
                }
            }
            else if (mode === 'close-sidebar-alt') {
                if ( windowW > 991) { // Close alternative sidebar in large screens (> 991px)
                    page.removeClass('sidebar-alt-visible-lg');
                } else { // Close alternative sidebar in small screens (< 992px)
                    page.removeClass('sidebar-alt-visible-xs');
                }
            }
            else if (mode == 'sidebar-scroll') { // Init sidebars scrolling
                if (sScroll.length && (!sScroll.parent('.slimScrollDiv').length)) {
                    // Initialize Slimscroll plugin on both sidebars
                    sScroll.slimScroll({ height: jQuery(window).height(), color: '#fff', size: '3px', touchScrollStep: 100 });

                    // Resize sidebars scrolling height on window resize or orientation change
                    jQuery(window).resize(sidebarScrollResize);
                    jQuery(window).bind('orientationchange', sidebarScrollResizeOrient);
                }
            }
        }

        return false;
    };

    // Sidebar Scrolling Resize Height on window resize and orientation change
    var sidebarScrollResize         = function() { sScroll.css('height', jQuery(window).height()); };
    var sidebarScrollResizeOrient   = function() { setTimeout(sScroll.css('height', jQuery(window).height()), 500); };

    /* Resize #page-content to fill empty space if exists */
    var resizePageContent = function() {
        var windowH         = jQuery(window).height();
        var sidebarH        = sidebar.outerHeight();
        var sidebarAltH     = sidebarAlt.outerHeight();
        var headerH         = header.outerHeight();
        var footerH         = footer.outerHeight();

        // If we have a fixed sidebar/header layout or each sidebars’ height < window height
        if (header.hasClass('navbar-fixed-top') || header.hasClass('navbar-fixed-bottom') || ((sidebarH < windowH) && (sidebarAltH < windowH))) {
            if (page.hasClass('footer-fixed')) { // if footer is fixed don't remove its height
                pageContent.css('min-height', windowH - headerH + 'px');
            } else { // else if footer is static, remove its height
                pageContent.css('min-height', windowH - (headerH + footerH) + 'px');
            }
        }  else { // In any other case set #page-content height the same as biggest sidebar's height
            if (page.hasClass('footer-fixed')) { // if footer is fixed don't remove its height
                pageContent.css('min-height', ((sidebarH > sidebarAltH) ? sidebarH : sidebarAltH) - headerH + 'px');
            } else { // else if footer is static, remove its height
                pageContent.css('min-height', ((sidebarH > sidebarAltH) ? sidebarH : sidebarAltH) - (headerH + footerH) + 'px');
            }
        }
    };

    /* Scroll to top functionality */
    var scrollToTop = function() {
        // Get link
        var link = jQuery('#to-top');
        var windowW = window.innerWidth
                        || document.documentElement.clientWidth
                        || document.body.clientWidth;

        jQuery(window).scroll(function() {
            // If the user scrolled a bit (150 pixels) show the link in large resolutions
            if ((jQuery(this).scrollTop() > 150) && (windowW > 991)) {
                link.fadeIn(100);
            } else {
                link.fadeOut(100);
            }
        });

        // On click get to top
        link.click(function() {
            jQuery('html, body').animate({scrollTop: 0}, 400);
            return false;
        });
    };

    /* Demo chat functionality (in sidebar) */
    var chatUi = function() {
        var chatUsers       = jQuery('.chat-users');
        var chatTalk        = jQuery('.chat-talk');
        var chatMessages    = jQuery('.chat-talk-messages');
        var chatInput       = jQuery('#sidebar-chat-message');
        var chatMsg         = '';

        // Initialize scrolling on chat talk list
        jQuery('.chat-talk-messages').slimScroll({ height: 210, color: '#fff', size: '3px', position: 'left', touchScrollStep: 100 });

        // If a chat user is clicked show the chat talk
        jQuery('a', chatUsers).click(function(){
            chatUsers.slideUp();
            chatTalk.slideDown();

            return false;
        });

        // If chat talk close button is clicked show the chat user list
        jQuery('#chat-talk-close-btn').click(function(){
            chatTalk.slideUp();
            chatUsers.slideDown();

            return false;
        });

        // When the chat message form is submitted
        jQuery('#sidebar-chat-form').submit(function(e){
            // Get text from message input
            chatMsg = chatInput.val();

            // If the user typed a message
            if (chatMsg) {
                // Add it to the message list
                chatMessages.append('<li class="chat-talk-msg chat-talk-msg-highlight themed-border animation-slideLeft">' + jQuery('<div />').text(chatMsg).html() + '</li>');

                // Scroll the message list to the bottom
                chatMessages.animate({ scrollTop: chatMessages[0].scrollHeight}, 500);

                // Reset the message input
                chatInput.val('');
            }

            // Don't submit the message form
            e.preventDefault();
        });
    };

    /* Template Options, change features functionality */
    var templateOptions = function() {
        /*
         * Color Themes
         */
        var colorList = jQuery('.sidebar-themes');
        var themeLink = jQuery('#theme-link');
        var theme;

        if (themeLink.length) {
            theme = themeLink.attr('href');

            jQuery('li', colorList).removeClass('active');
            jQuery('a[data-theme="' + theme + '"]', colorList).parent('li').addClass('active');
        }

        jQuery('a', colorList).click(function(e){
            // Get theme name
            theme = jQuery(this).data('theme');

            jQuery('li', colorList).removeClass('active');
            jQuery(this).parent('li').addClass('active');

            if (theme === 'default') {
                if (themeLink.length) {
                    themeLink.remove();
                    themeLink = jQuery('#theme-link');
                }
            } else {
                if (themeLink.length) {
                    themeLink.attr('href', theme);
                } else {
                    jQuery('link[href="css/themes.css"]').before('<link id="theme-link" rel="stylesheet" href="' + theme + '">');
                    themeLink = jQuery('#theme-link');
                }
            }
        });

        // Prevent template options dropdown from closing on clicking options
        jQuery('.dropdown-options a').click(function(e){ e.stopPropagation(); });

        /* Page Style */
        var optMainStyle        = jQuery('#options-main-style');
        var optMainStyleAlt     = jQuery('#options-main-style-alt');

        if (page.hasClass('style-alt')) {
            optMainStyleAlt.addClass('active');
        } else {
            optMainStyle.addClass('active');
        }

        optMainStyle.click(function() {
            page.removeClass('style-alt');
            jQuery(this).addClass('active');
            optMainStyleAlt.removeClass('active');
        });

        optMainStyleAlt.click(function() {
            page.addClass('style-alt');
            jQuery(this).addClass('active');
            optMainStyle.removeClass('active');
        });

        /* Header options */
        var optHeaderDefault    = jQuery('#options-header-default');
        var optHeaderInverse    = jQuery('#options-header-inverse');
        var optHeaderTop        = jQuery('#options-header-top');
        var optHeaderBottom     = jQuery('#options-header-bottom');

        if (header.hasClass('navbar-default')) {
            optHeaderDefault.addClass('active');
        } else {
            optHeaderInverse.addClass('active');
        }

        if (header.hasClass('navbar-fixed-top')) {
            optHeaderTop.addClass('active');
        } else if (header.hasClass('navbar-fixed-bottom')) {
            optHeaderBottom.addClass('active');
        }

        optHeaderDefault.click(function() {
            header.removeClass('navbar-inverse').addClass('navbar-default');
            jQuery(this).addClass('active');
            optHeaderInverse.removeClass('active');
        });

        optHeaderInverse.click(function() {
            header.removeClass('navbar-default').addClass('navbar-inverse');
            jQuery(this).addClass('active');
            optHeaderDefault.removeClass('active');
        });

        optHeaderTop.click(function() {
            page.removeClass('header-fixed-bottom').addClass('header-fixed-top');
            header.removeClass('navbar-fixed-bottom').addClass('navbar-fixed-top');
            jQuery(this).addClass('active');
            optHeaderBottom.removeClass('active');
            handleSidebar('sidebar-scroll');

            // Resize #page-content
            resizePageContent();
        });

        optHeaderBottom.click(function() {
            page.removeClass('header-fixed-top').addClass('header-fixed-bottom');
            header.removeClass('navbar-fixed-top').addClass('navbar-fixed-bottom');
            jQuery(this).addClass('active');
            optHeaderTop.removeClass('active');
            handleSidebar('sidebar-scroll');

            // Resize #page-content
            resizePageContent();
        });

        /* Footer */
        var optFooterStatic = jQuery('#options-footer-static');
        var optFooterFixed  = jQuery('#options-footer-fixed');

        if (page.hasClass('footer-fixed')) {
            optFooterFixed.addClass('active');
        } else {
            optFooterStatic.addClass('active');
        }

        optFooterStatic.click(function() {
            page.removeClass('footer-fixed');
            jQuery(this).addClass('active');
            optFooterFixed.removeClass('active');

            // Resize #page-content
            resizePageContent();
        });

        optFooterFixed.click(function() {
            page.addClass('footer-fixed');
            jQuery(this).addClass('active');
            optFooterStatic.removeClass('active');

            // Resize #page-content
            resizePageContent();
        });
    };

    /* Datatables Basic Bootstrap integration (pagination integration included under the Datatables plugin in plugins.js) */
    var dtIntegration = function() {
        jQuery.extend(true, jQuery.fn.dataTable.defaults, {
            "sDom": "<'row'<'col-sm-6 col-xs-5'l><'col-sm-6 col-xs-7'f>r>t<'row'<'col-sm-5 hidden-xs'i><'col-sm-7 col-xs-12 clearfix'p>>",
            "sPaginationType": "bootstrap",
            "oLanguage": {
                "sLengthMenu": "_MENU_",
                "sSearch": "<div class=\"input-group\">_INPUT_<span class=\"input-group-addon\"><i class=\"fa fa-search\"></i></span></div>",
                "sInfo": "<strong>_START_</strong>-<strong>_END_</strong> of <strong>_TOTAL_</strong>",
                "oPaginate": {
                    "sPrevious": "",
                    "sNext": ""
                }
            }
        });
    };

    return {
        init: function() {
            uiInit(); // Initialize UI Code
        },
        sidebar: function(mode, extra) {
            handleSidebar(mode, extra); // Handle sidebars - access functionality from everywhere
        },
        datatables: function() {
            dtIntegration(); // Datatables Bootstrap integration
        }
    };
}();

/* Initialize app when page loads */
jQuery(function(){ App.init(); });