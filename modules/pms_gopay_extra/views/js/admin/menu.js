
/** ########################################################################### * 
 *                                                                             * 
 *                      Presta Module Shop | Copyright 2018                    * 
 *                           www.prestamoduleshop.com                          * 
 *                                                                             * 
 *             Please do not change this text, remove the link,                * 
 *          or remove all or any part of the creator copyright notice          * 
 *                                                                             * 
 *    Please also note that although you are allowed to make modifications     * 
 *     for your own personal use, you may not distribute the original or       * 
 *                 the modified code without permission.                       * 
 *                                                                             * 
 *                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 * 
 *             Download is allowed only from www.prestamoduleshop.com          * 
 *                                                                             * 
 *       This software is provided as is, without warranty of any kind.        * 
 *           The author shall not be liable for damages of any kind.           * 
 *               Use of this software indicates that you agree.                * 
 *                                                                             * 
 *                                    ***                                      * 
 *                                                                             * 
 *              Prosím, neměňte tento text, nemažte odkazy,                    * 
 *      neodstraňujte části a nebo celé oznámení těchto autorských práv        * 
 *                                                                             * 
 *     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    * 
 *        pro vlastní osobní potřebu, nesmíte distribuovat původní nebo        * 
 *                        upravený kód bez povolení.                           * 
 *                                                                             * 
 *                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          * 
 *          Stažení je povoleno pouze z www.prestamoduleshop.com               * 
 *                                                                             * 
 *   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    * 
 *          Autor nenese odpovědnost za škody jakéhokoliv druhu.               * 
 *                  Používáním tohoto softwaru znamená,                        * 
 *           že souhlasíte s výše uvedenými autorskými právy.                  * 
 *                                                                             * 
 * ########################################################################### **//* js  pms_gopay_extra  */

//  for  debuging  function
//console.log(print_r(data.data));

var menu_status = "max";

$(document).ready(function () {
	// mapuji prvek "core-menu-toggle" jako přepínač
	document.getElementById("core-menu-toggle").addEventListener("click", togleMenu);
	if (localStorage['core-menu']) {
		menuSetup(localStorage['core-menu']);
	}
	window.onresize = resizer;

	jQuery.extend({
		handleError: function( s, xhr, status, e ) {
			// If a local callback was specified, fire it
			if ( s.error )
				s.error( xhr, status, e );
			// If we have some XML response text (e.g. from an AJAX call) then log it in the console
			else if(xhr.responseText)
				console.log(xhr.responseText);
		}
	});
});

// max, min
function menuSetup(type) {
	if (type != menu_status) {
		var co_menu = document.getElementById('core-menu');
		var co_content = document.getElementById('core-module-page');
		if (type == "max") {
			co_menu.classList.remove('menu-mini');
			co_content.classList.remove('menu-mini');
			menu_status = "max";
			localStorage['core-menu'] = menu_status;
		} else if (type == "min") {
			co_menu.classList.add('menu-mini');
			co_content.classList.add('menu-mini');
			menu_status = "min";
			localStorage['core-menu'] = menu_status;
		}
	}
}

// funkce se stará o přepínání menu pomocí přepínače
function togleMenu() {
	var co_menu = document.getElementById('core-menu');
	if (co_menu.classList.contains('menu-mini')) {
		menuSetup("max");
	} else {
		menuSetup("min");
	}
}

// funkce se volá, když se mění velikost okna
var resizer_status = "menu_status"; // pomocná proměnná, aby se menu nepřeplo vždy, když se mění velikost
function resizer() {
	if ($(window).width() < 992) {
		menuSetup("min");
		resizer_status = "min";
	} else {
		if (resizer_status == "min") {
			menuSetup("max");
			resizer_status = "max";
		}
	}
}
