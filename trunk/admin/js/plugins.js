vivvo.admin.plugins = Class.create ();

var adminPlugins = {

	initialize: function() {

		if ($('plugins_main_tabs')){
			new Control.Tabs('plugins_main_tabs');
		}

		$$('#plugins_list img').each(function (img){
			if ($(img.id.replace(/plugin_pin_/, 'menu_plugin_'))) {
				img.src = img.src.replace(/add/, 'remove');
			}
		});
	},

	toggleMenuItem: function(href, title) {

		var params = {
			SECURITY_TOKEN: vivvo.token,
			id: 'menu_plugin_' + href,
			title: title,
			href: 'plugins.php?plugin=' + href,
 			plugin_name: href
		};

		if ($('menu_plugin_' + href)) {
			params.action = 'unregister';
		} else {
			params.action = 'register';
		}

		new Ajax.Updater('menu_container', 'ajax.php?main_nav', {

			method: 'post',
			evalScripts: true,
			parameters: params,

			onSuccess: function(xhr) {

				if (xhr.getResponseHeader('X-Vivvo-Action-Status') == 1) {

					var plugin_item = $('plugin_pin_' + href);

					if (params.action == 'unregister') {
						plugin_item.src = plugin_item.src.replace('remove', 'add');
					} else {
						plugin_item.src = plugin_item.src.replace('add', 'remove');
					}
				}
			},
			
			onComplete: vivvo.mainNav.initialize.bind(vivvo.mainNav)
		})
	}
};

Event.observe(window, 'load', adminPlugins.initialize.bind(adminPlugins));