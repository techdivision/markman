/**
 *
 * JavaScript Library Configuration
 *
 * @category   Appserver
 * @package    TechDivision_Markman
 * @subpackage Utils
 * @author     Lars Roettig <l.roettig@techdsivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
$(document).ready(function () {
    var menu = $('#menu');
    menu.multilevelpushmenu({
        containersToPush: [$('#pushobj')],
        menuWidth: '300px',
        menuHeight: '100%',
        collapsed: true,
        onGroupItemClick: function () {
            var event = arguments[0],
                $menuLevelHolder = arguments[1],
                $item = arguments[2],
                options = arguments[3],
                itemhref = $item.find('a:first').attr("href");
             /** @todo can be remove if working the navigation*/
            itemhref = 'http://localhost:9080/docs/appserver.io/0.6.0-beta1/docs/getting-started/basic-usage.html';
            $('#documentation').load(itemhref + ' #documentation');
        }
    });
    menu.multilevelpushmenu('option', 'menuHeight', $(document).height());
    menu.multilevelpushmenu('redraw');

    $('#bodyDiv').UItoTop({
        autoLinkClass: 'toplink',
        autoLinkText: 'return to Top',
        autoLinkIcon: 'fa fa-caret-square-o-up fa-4',
        min: 200
    });
});

$(window).resize(function () {
    var menu = $('#menu');

    menu.multilevelpushmenu('option', 'menuHeight', $(document).height());
    menu.multilevelpushmenu('redraw');
});
