/**
 * LSX Health Plan scripts (admin).
 *
 * @package lsx-health-plan
 */

var LSX_HP_ADMIN = Object.create( null );

;( function( $, window, document, undefined ) {

	'use strict';

	LSX_HP_ADMIN.document = $(document);

	/**
     * Start the JS Class
     */
    LSX_HP_ADMIN.init = function() {
        LSX_HP_ADMIN.changeName();
	};

	LSX_HP_ADMIN.changeName = function() {
		$( ".wpua-edit .button" ).last().attr("value","Update Profile Image");
	};

	/**
     * On document ready.
     *
     * @package    lsx-health-plan
     * @subpackage scripts
     */
    LSX_HP_ADMIN.document.ready( function() {
        LSX_HP_ADMIN.init();
    } );

} )( jQuery, window, document );
