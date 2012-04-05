Event.observe(window, 'load', window.reloadSavePlus = function() {

	$$('ul.save_plus').each(function(ul) {

		if (ul.__save_plus) {
			return;
		}

		ul.__save_plus = true;

		var action_list_toggler = ul.down('.action-list-toggler'),
			arrow_img = action_list_toggler.down('a').down('img'),
			hovered = false,
			timer = null;

		ul.down('.action_button').hover(function() {
			clearTimeout(timer);
		}, function() {
			timer = setTimeout(function() {
				if (!hovered) {
					action_list_toggler.removeClassName('toggle_menu_hover');
					arrow_img.removeClassName('active');
				}
			}, 150);
		});

		action_list_toggler.hover(function(){
			clearTimeout(timer);
			action_list_toggler.addClassName('toggle_menu_hover').hovered = true;
			arrow_img.addClassName('active');
		}, function() {
			action_list_toggler.hovered = false;
			timer = setTimeout(function() {
				action_list_toggler.removeClassName('toggle_menu_hover');
				arrow_img.removeClassName('active');
			}, 150);
		});
	});
});