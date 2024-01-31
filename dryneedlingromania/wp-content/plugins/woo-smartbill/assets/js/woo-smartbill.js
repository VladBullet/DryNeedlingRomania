(function($){
	"use strict"
	$(document).on( 'click', '.smartbill-button', function( evt ){
		evt.preventDefault();

		var data = {
			'action' : 'woo_smartbill',
			'woo_smartbill_nonce' : woo_smartbill['_nonce'],
			'smartbill_action' : $(this).data('action'),
			'order_id' : $(this).data('order-id')
		};

		if ( 'stergere' == data['smartbill_action'] ) {
			var check = confirm( "Această opțiune nu va modifica factura din SmartBill ci doar va șterge din baza de date factura pentru această comandă pentru a putea fi generată altă factură." );
			if ( ! check ) { return; }
		}

		$('#woo_smartbill_metabox').addClass( 'processing' ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

		$.ajax({
		  	method: "POST",
		  	url: woo_smartbill['url'],
		  	data: data
		}).done(function( response ) {
    		if ( ! response.success ) {

				var mesaj = response.data[0].message + '( cod ' + response.data[0].code + ' )';
				$('#woo_smartbill_metabox').removeClass( 'processing' ).unblock();
				alert( mesaj );

			}else{
				location.reload();
			}
		});
		

	});

	$(document).ready(function(){
		$('.woo_smartbill_refresh_cloud').click(function(evt){
			evt.preventDefault();

			$(this).find('.dashicons').addClass( 'spin' );

			var data = {
				'action' : 'woo_smartbill_cloud',
				'woo_smartbill_nonce' : woo_smartbill['_nonce'],
				'smartbill_action' : $(this).data('action')
			};

			$.ajax({
			  	method: "POST",
			  	url: woo_smartbill['url'],
			  	data: data
			}).done(function( response ) {

				if ( ! response.success ) {

					mesaj = response.data[0].message + '( cod ' + response.data[0].code + ' )';
					alert( mesaj );

				}else{
					location.reload();
				}
	    		
	  		});

		});
	});

})(jQuery);