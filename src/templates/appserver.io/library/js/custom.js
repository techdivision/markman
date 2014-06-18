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

                $(this).attr("href", "/" + "${docs.deploy.folder}" + "/" + projectName + "/" + $(this).text().trim() + versionLessLink);
            });

            // Scroll to top to show the new content
            $('html, body').animate({ scrollTop: 0 }, 0);

            // Change the URL of the browser
            window.history.pushState("string", "Title", absoluteLink);
        },
        onBackItemClick: function () {
            // Scroll to top to show the new content
            $('html, body').animate({scrollTop: 0}, 0);
            // fetch the current and spilt in array
            var hrefArray = $(location).attr('href').split("/");
            // the elements count
            var count = hrefArray.length;
            // the new absoluteLink
            var absoluteLink = hrefArray[0] + "//";

            // Shorten the new url about 2 url hierarchies if we are in an index file, if not just by one hierarchy
            var shortenBy = 0;
            if (hrefArray.indexOf('index.html', count - 10) !== -1) {

                shortenBy = 2;

            } else {

                shortenBy = 1;
            }

            // Do the actual shortening
            for (var i = 2; i < (count - shortenBy); i++) {
                absoluteLink += hrefArray[i] + "/";
            }
            absoluteLink += 'index.html';

            // Load the requested documentation content
            $('#documentation').load(absoluteLink + ' #documentation');

            // Build up the versionless link for the version switcher
            var versionLessLink = absoluteLink.substring(absoluteLink.indexOf(projectName + '/'));
            versionLessLink = versionLessLink.substring(versionLessLink.indexOf('/', projectName.length + 2));

            // Change the links of the version switcher
            $('li', '#versions').find('a').each(function () {
                $(this).attr("href", "/" + "${docs.deploy.folder}" + "/" + projectName + "/" + $(this).text().trim() + versionLessLink);
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

