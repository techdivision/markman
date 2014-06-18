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

    // Some "globals" we might need
    var projectName = $("a.navbar-brand").text();

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
            var itemHref = arguments[2].find('a:first').attr("href").replace(/\.\.\//g, '');

            // We need to build up several links
            var absoluteLink = "/" + "${docs.deploy.folder}" + "/" + projectName + "/" + itemHref;
            var versionLessLink = itemHref.substring(itemHref.indexOf('/'));

            // Load the requested documentation content
            $('#documentation').load(absoluteLink + ' #documentation');

            // Change the links of the version switcher
            $('li', '#versions').find('a').each(function () {

                $(this).attr("href", "/" + "docs" + "/" + projectName + "/" + $(this).text().trim() + versionLessLink);
            });

            $('html, body').animate({ scrollTop: 0 }, 0);

            // Change the URL of the browser
            window.history.pushState("string", "Title", absoluteLink);
        },
        onBackItemClick: function () {
            // scroll top
            $('html, body').animate({scrollTop: 0}, 0);
            // fetch the current and spilt in array
            var hrefArray = $(location).attr('href').split("/");
            // the elements count
            var count = hrefArray.length;
            // the new absoluteLink
            var absoluteLink = hrefArray[0] + "//";

            // short the new url about 2 url hierarchy
            for (var i = 2; i < (count - 2); i++) {
                absoluteLink += hrefArray[i] + "/";
            }

            var versionLessLink = absoluteLink.split('/')[1];

            // Load the requested documentation content
            $('#documentation').load(absoluteLink + ' #documentation');

            // Change the links of the version switcher
            $('li', '#versions').find('a').each(function () {
                $(this).attr("href", "/" + "docs" + "/" + projectName + "/" + $(this).text().trim() + versionLessLink);
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
});

/**
 * On every resize of the window we have to resize the menu as well
 */
$(window).resize(function () {
    var menu = $('#menu');

    menu.multilevelpushmenu('option', 'menuHeight', $(document).height());
    menu.multilevelpushmenu('redraw');
});

