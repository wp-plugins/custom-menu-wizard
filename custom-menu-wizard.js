/* Plugin Name: Custom Menu Wizard
 * Version: 1.2.1
 * Author: Roger Barrett
 * 
 * Script for controlling this widget's options (in Admin -> Widgets)
*/
jQuery(function($){
	var dotPrefix = '.widget-custom-menu-wizard';
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
		//change of menu, and enableif / disableif...
		.on('change', dotPrefix + '-listen', function(){
			var listeners = $(dotPrefix + '-listen', this.form),
					selectMenu = listeners.filter(dotPrefix + '-selectmenu'),
					others = listeners.not(selectMenu),
					showAll = others.eq(0).prop('checked'),
					filterItem = others.filter('select').eq(0),
					fiVal = parseInt(filterItem.val(), 10),
					groupClone;
			if(selectMenu.is(this)){
				selectMenu = this.selectedIndex;
				if(!filterItem.find('optgroup').filter(function(){
							var keep = $(this).data('cmwOptgroupIndex') === selectMenu;
							if(!keep){
								$(this).remove();
							}
							return keep;
						}).length){
					groupClone = $('#' + filterItem.attr('id') + '_ignore').find('optgroup').eq(selectMenu).clone();
					if(groupClone.length){
						if(fiVal > 0){
							fiVal = 0;
							filterItem.val(fiVal);
						}
						groupClone.find('option[selected]').removeAttr('selected').prop('selected', false);
						filterItem.append(groupClone);
					}
				}
			}
			$.each(
				{	'' : showAll,
					'not-rp' : showAll || fiVal >= 0,
					'not-ci' : showAll || !!fiVal
				},
				function(k, v){
					$(dotPrefix + '-disableif' + k, this.form).css({color:v ? '#999999' : 'inherit'}).find('input,select').prop('disabled', v);
				});
		});

	//when a widget is opened or saved, trigger change on the filter_item select...
	//to do this I've elected to modify WP's window.wpWidgets object and intercept its fixLabels()
	//method, which, handily, gets called whenever a widget is opened or saved!
	if(window.wpWidgets && window.wpWidgets.fixLabels && !window.wpWidgets._cmw_fixLabels){
		//save the original...
		window.wpWidgets._cmw_fixLabels = window.wpWidgets.fixLabels;
		//replace the original...
		window.wpWidgets.fixLabels = function(widget){
			//trigger change on selectmenu...
			widget.find('.widget-custom-menu-wizard-selectmenu').trigger('change');
			//run the original...
			window.wpWidgets._cmw_fixLabels(widget);
    };
	}else{
		//one-off fallback...
		$(dotPrefix + '-selectmenu').trigger('change');
	}
});
