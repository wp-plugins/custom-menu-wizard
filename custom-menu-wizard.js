/* Plugin Name: Custom Menu Wizard
 * Version: 1.1.0
 * Author: Roger Barrett
 * 
 * Script for controlling this widget's options (in Admin -> Widgets)
*/
jQuery(function($){
	var dotPrefix = '.widget-custom-menu-wizard',
			filterItems = $('select' + dotPrefix + '-listen');
	$(document)
		//fieldsets...
		.on('click', dotPrefix + '-collapsible-fieldset', function(){
			var chkbox = $('input', this).eq(0),
					collapse = !chkbox.prop('checked');
			if(chkbox.length){
				chkbox.prop('checked', collapse);
				$('div', this).css({backgroundPosition:collapse?'0 0':'0 -36px'});
				$(this).next('div')[collapse?'slideUp':'slideDown']();
			}
			this.blur();
			return false;
		})
		//change of menu...
		.on('change', dotPrefix + '-selectmenu', function(){
			var select = $('select' + dotPrefix + '-listen', this.form),
					from = $('#' + select.attr('id') + '_ignore').find('optgroup'),
					groupClone;
			if(from.length > this.selectedIndex){
				if(select.val() > 0){
					select.val(0);
				}
				groupClone = from.eq( this.selectedIndex ).clone();
				groupClone.find('option[selected]').removeAttr('selected').prop('selected', false);
				select.find('optgroup').remove();
				select.append(groupClone).trigger('change');
			}
		})
		//enableif and disableif...
		.on('change', dotPrefix + '-listen', function(){
			var listeners = $(dotPrefix + '-listen', this.form),
					showAll = listeners.eq(0).prop('checked'),
					rootParent = !showAll && listeners.filter('select').val() < 0;
			$(dotPrefix + '-disableif', this.form).css({color:showAll ? '#999999' : 'inherit'}).find('input,select').prop('disabled', showAll);
			$(dotPrefix + '-enableif', this.form).css({color:!rootParent ? '#999999' : 'inherit'}).find('input,select').prop('disabled', !rootParent);
		});
	//remove non-active optgroups...
	filterItems.find('optgroup').filter(function(){
		return !$(this).data('cmwActiveMenu');
	}).remove();
	//trigger change...
	filterItems.trigger('change');
});
