(function($){

	$(document).ready(function(){
		var wrapper = $( '#order_data .order_data_column_container .order_data_column:nth-child(2) div.edit_address' ),
			tip_facturare_el = wrapper.find( '#_billing_tip_facturare' ),
			tip_facturare = '',
			el_class = '';

		if ( tip_facturare_el.length > 0 ) {
			tip_facturare = tip_facturare_el.val();
			el_class = '.show_if_' + tip_facturare;
			wrapper.find( '.av_facturare_field' ).hide();
			wrapper.find( '.av_facturare_field').filter( el_class ).show();

			tip_facturare_el.change(function(){
				tip_facturare = tip_facturare_el.val();
				el_class = '.show_if_' + tip_facturare;
				wrapper.find( '.av_facturare_field' ).hide();
				wrapper.find( '.av_facturare_field').filter( el_class ).show();
			});
		}

	});

})(jQuery);