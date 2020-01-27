var mag_ajax_js = Object.create( null );

;( function( $, window, document, undefined ) {

	'use strict';

	mag_ajax_js.init_ajax = function( ) {
		$('.cmb-post-search-ajax').each(
			function () {
				var fid 		= $(this).attr('id');
				var name        = $(this).attr('id');
				var query_args 	= $(this).attr('data-queryargs');
				var object		= $(this).attr('data-object');
	
				name = name.replace( "][", "_" );
				name = name.replace( "]", "" );
				name = name.replace( "[", "_" );
	
				$(this).devbridgeAutocomplete({
					serviceUrl: psa.ajaxurl,
					type: 'POST',
					triggerSelectOnValidInput: false,
					showNoSuggestionNotice: true,
					transformResult: function(r) {
						var suggestions = $.parseJSON(r);
						if($('input#'+name+' li').length){
							var selected_vals 	= Array();
							var d 				= 0;
							$('input#'+name+' input').each(function(index, element) {
								selected_vals.push( $(this).val() );
							});
							$(suggestions).each(function(ri, re){
								if($.inArray((re.data).toString(), selected_vals) > -1){
									suggestions.splice(ri-d, 1);
									d++;
								}
							});
						}
						$(suggestions).each(function(ri, re){
							re.value = $('<textarea />').html(re.value).text();
						});
						return {suggestions: suggestions};
					},
					params:{
						action  	: 'cmb_post_search_ajax_get_results',
						psacheck	: psa.nonce,
						object		: object,
						query_args	: query_args,
					},
					onSearchStart: function(){
						$(this).next('img.cmb-post-search-ajax-spinner').css('display', 'inline-block');
					},
					onSearchComplete: function(){
						$(this).next('img.cmb-post-search-ajax-spinner').hide();
					},
					onSelect: function (suggestion) {
						$(this).devbridgeAutocomplete('clearCache');
						var lid 	 = $(this).attr('id') + '_results';
						var name     = $(this).attr('id');
						name = name.replace( "][", "_" );
						name = name.replace( "]", "" );
						name = name.replace( "[", "_" );
	
						var limit 	 = $(this).attr('data-limit');
						var sortable = $(this).attr('data-sortable');
						if ( limit > 1 ) {
							var handle = (sortable == 1) ? '<span class="hndl"></span>' : '';
							$( '#'+name+'_results' ).append('<li>'+handle+'<input type="hidden" name="'+lid+'[]" value="'+suggestion.data+'"><a href="'+suggestion.guid+'" target="_blank" class="edit-link">'+suggestion.value+'</a><a class="remover"><span class="dashicons dashicons-no"></span><span class="dashicons dashicons-dismiss"></span></a></li>');
							$(this).val('');
							if ( limit === $('input#'+name + '_results li').length ){
								$(this).prop( 'disabled', 'disabled' );
							} else {
								$(this).focus();
							}
						} else {
							$('input#'+name).val( suggestion.data );
						}
	
						if ( $(this).parents('.cmb-row').hasClass('cmb-repeat-group-field') && limit <= 1 ) {
							console.log('cmb-group');
							$( 'input[name='+name+'_store]' ).val( suggestion.data );
						}
	
					}
				});			
			
				if($(this).attr('data-sortable') == 1){
					$('input#'+name).sortable({ 
						handle				 : '.hndl', 
						placeholder			 : 'ui-state-highlight', 
						forcePlaceholderSize : true 
					});	
				}
				
				if($(this).attr('data-limit') == 1){
					$(this).on('blur', function(){
						if($(this).val() === ''){
							var name     = $(this).attr('id');
							name = name.replace( "][", "_" );
							name = name.replace( "]", "" );
							name = name.replace( "[", "_" );
							$('input#'+name).val('');
						}
					});
				}
			
			}
		);
	};

	mag_ajax_js.watch_remove_click = function() {
		$('.cmb-post-search-ajax-results').on( 'click', 'a.remover', function(){
			$(this).parent('li').fadeOut( 400, function(){ 
				var iid = $(this).parents('ul').attr('id').replace('_results', '');
				$(this).remove(); 
				$('#' + iid).removeProp( 'disabled' );
				$('#' + iid).devbridgeAutocomplete('clearCache');
			});
		});
	};

	mag_ajax_js.event_init = function( evt, $row ) {
		mag_ajax_js.init_ajax();
	};

	$(document).ready(function() {
		mag_ajax_js.init_ajax();
		mag_ajax_js.watch_remove_click();
		$( document ).on( 'cmb2_add_row', mag_ajax_js.event_init )
	});


} )( jQuery, window, document );
