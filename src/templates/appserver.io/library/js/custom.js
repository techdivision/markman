/**
 *
 * JavaScript Library Configuration
 *
 * @category  Tools
 * @package   TechDivision_Markman
 * @author    Lars Roettig <l.roettig@techdsivision.com>
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2014 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.techdivision.com/
 */
$(document).ready(function () {

    // Ajax default settings used for error handling on client side
    $.ajaxSetup({
        timeout: 3000,
        error:function(x,e){
            if('parsererror'==e) {
                $('#documentation').html('<h2 class="error">Sorry, we ran into a technical problem (parse error). Please try again...</h2>');
            } else if('timeout'==e) {
                $('#documentation').html('<h2 class="warning">Request timed out. Please try again...</h2>');
            }
            else if ( "status" in x ) {
                if(0 == x.status){
                    $('#documentation').html('<h2 class="error">You are offline! Please check your network.</h2>');
                }else if (404 == x.status){
                    $('#documentation').html('<h2 class="warning">There is no documentation here (404), please look further ...</h2>');
                }else if(500 == x.status){
                    $('#documentation').html('<h2 class="error">Sorry, we ran into a technical problem (500). Please try again...</h2>');
                }
            }
            else {
                $('#documentation').html('<h2 class="error">Sorry, we ran into a technical problem (unknown error). Please try again...</h2>');
            }
        }
    });

    // Some "globals" we might need
    var currentVersion = $('li.active').attr('node');
    var currentUri = window.location.href.toString().split(window.location.host)[1];
    var uriPrefix = currentUri.substr(0, currentUri.indexOf(currentVersion));

    // uriPrefix has to begin with a slash
    if (uriPrefix.charAt(0) != '/') {

        uriPrefix = '/' + uriPrefix;
    }

    /**
     * We have to configure and initialise the multi push menu
     */
    var menu = $('#menu');
    menu.multilevelpushmenu({
        containersToPush: [$('#pushobj')],
        menuWidth: '250px',
        menuHeight: '100%',
        mode: 'cover',
        collapsed: true,
        onGroupItemClick: function () {
            // Get a clean link (without relative ../ stuff) from the button we clicked
            var cleanLink = '/' + arguments[2].find('a:first').attr("href").replace(/\.\.\//g, '');
            // Grab the current version wer are in
            currentVersion = $('li.active').attr('node');

            // Build up the absolute link to
            var absoluteLink = uriPrefix + currentVersion + cleanLink;

            // Load the requested documentation content
            $('#documentation').load(absoluteLink + ' #documentation');

            // Change the links of the version switcher
            $('li', '#versions').find('a').each(function () {

                $(this).attr("href", uriPrefix + $(this).text().trim() + cleanLink);
            });

            // Scroll to top to show the new content
            $('html, body').animate({ scrollTop: 0 }, 0);

            // Change the URL of the browser
            window.history.pushState("string", "Title", absoluteLink);
        },
        onItemClick: function () {
            scrollToAnchor(arguments[2].find('a:first').attr("href"));
        },
        onBackItemClick: function () {
            // Scroll to top to show the new content
            $('html, body').animate({scrollTop: 0}, 0);
            // fetch the current and spilt in array
            var hrefArray = window.location.href.toString().split(window.location.host)[1].split("/");
            // the elements count
            var count = hrefArray.length;

            // Shorten the new url about 2 url hierarchies if we are in an index file, if not just by one hierarchy
            var shortenBy = 0;
            if (hrefArray.indexOf('index.html', count - 10) !== -1) {

                shortenBy = 2;

            } else {

                shortenBy = 1;
            }

            // Do the actual shortening with a new absoluteLink
            var absoluteLink = '';
            for (var i = 0; i < (count - shortenBy); i++) {
                absoluteLink += hrefArray[i] + "/";
            }
            absoluteLink += 'index.html';

            // Load the requested documentation content
            $('#documentation').load(absoluteLink + ' #documentation');

            // Build up the versionless link for the version switcher
            var versionLessLink = absoluteLink.substring(absoluteLink.indexOf(currentVersion) + currentVersion.length);

            // Change the links of the version switcher
            $('li', '#versions').find('a').each(function () {

                $(this).attr("href", uriPrefix + $(this).text().trim() + versionLessLink);
            });

            // Change the URL of the browser
            window.history.pushState("string", "Title", absoluteLink);
        }
    });
    menu.multilevelpushmenu('option', 'menuHeight', $(document).height());
    menu.multilevelpushmenu('redraw');

    /**
     * We have to configure and initialise the "return to top" js element
     */
    $('#bodyDiv').UItoTop({
        autoLinkClass: 'toplink',
        autoLinkText: 'return to Top',
        autoLinkIcon: 'fa fa-caret-square-o-up fa-4',
        min: 200,
        easingType: 'linear',
        scrollSpeed: 1
    });

    /**
     * We have to initialise the superfish menu used for the version switcher
     */
    $('ul.sf-menu').superfish();

    /**
     * Will scroll to a certain anchor passed to the function
     *
     * @param aid The anchor in a jquery-selector format, e.g. #anchor
     */
    function scrollToAnchor(aid){
        var aTag = $(aid);
        var offSet = aTag.offset().top - 50;
        $('html,body').animate({scrollTop: offSet},'fast');
    }
});

/**
 * On every resize of the window we have to resize the menu as well
 */
$(window).resize(function () {
    var menu = $('#menu');

    menu.multilevelpushmenu('option', 'menuHeight', $(document).height());
    menu.multilevelpushmenu('redraw');
});

