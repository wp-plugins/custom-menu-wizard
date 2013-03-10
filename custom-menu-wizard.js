/* Plugin Name: Custom Menu Wizard
 * Version: 1.0.0
 * Author: Roger Barrett
 * 
 * Script for controlling this widget's options (in Admin -> Widgets)
*/
jQuery(function($){
	var dotPrefix = '.widget-custom-menu-wizard'
	$(document).on('click', dotPrefix + '-collapsible-fieldset', function(){
		var chkbox = $('input', this).eq(0),
				collapse = !chkbox.prop('checked');
		if(chkbox.length){
			chkbox.prop('checked', collapse);
			$('div', this).css({backgroundPosition:collapse?'0 0':'0 -36px'});
			$(this).next('div')[collapse?'slideUp':'slideDown']();
		}
		this.blur();
		return false;
	});
	$(document).on('change', dotPrefix + '-filter-radio', function(){
		$(dotPrefix + '-filter-radio-dep', this.form).prop('disabled', $(dotPrefix + '-filter-radio', this.form).eq(0).prop('checked'));
	});
	$(document).on('change', dotPrefix + '-select-menu', function(){
		var optgroups = $('select' + dotPrefix + '-filter-radio-dep', this.form).val(0).find('optgroup');
		optgroups.filter(':visible').hide();
		optgroups.eq( this.selectedIndex ).show();
	});
});
