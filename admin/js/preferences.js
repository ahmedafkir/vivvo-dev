vivvo.admin.preferences = Class.create ();

Object.extend (
	vivvo.admin.preferences.prototype,
	{
		initialize: function (){
			$$('#content .expand').each(
				function(el){
					Event.observe(el, 'click',
						function (e){
							var el = Event.element(e);
							el.up('.form_box_header').next('.form_box_body').toggle();
							el.toggleClassName('collapse');
							if (el.hasClassName('collapse')){
								el.update(vivvo.admin.lang.get('LNG_ADMIN_HIDE_FORM'));
							}else{
								el.update(vivvo.admin.lang.get('LNG_ADMIN_SHOW_FORM'));
							}
							//$('category_save_button').makePositioned();
						}
					);
				}
			);
			$$(".info_help").each( function(link) {
				new Tooltip(link, {
					mouseFollow: false
				});
			});
		}
	}
);

document.observe("dom:loaded", function() {
	new vivvo.admin.preferences();
});