<vte:template>
	<vte:if test="{VIVVO_CLOSE_SITE}">
		<vte:header type="script" href="{VIVVO_URL}js/closed_site.js" />
	</vte:if>
	<vte:header type="script" href="{VIVVO_URL}js/framework/builder.js" />
	<vte:header type="script" href="{VIVVO_URL}{VIVVO_FS_ADMIN_DIR}js/vivvo.js" />
	<script type="text/javascript">
		if(typeof(vivvo) == "undefined") vivvo = {};
		vivvo.params = {};
		vivvo.params.content = '<vte:value select="{content_params|json_encode}" />'.evalJSON();
		vivvo.params.article = {};
		vivvo.params.article.id = '<vte:value select="{article.id}" />';
		vivvo.params.article.category = '<vte:value select="{article.category_id}" />';
		vivvo.params.article.comments = '<vte:value select="{article.show_comment}" />';
		vivvo.token = '<vte:value select="{VIVVO_SECURITY_TOKEN}" />';
		vivvo.fs_admin_dir = '<vte:value select="{VIVVO_FS_ADMIN_DIR}" />';
	</script>
	<vte:header type="script" href="{VIVVO_URL}js/control_modal.js" />
	<vte:header type="css" href="{VIVVO_URL}{VIVVO_FS_ADMIN_DIR}css/modal.css" />
    <vte:header type="css" href="{VIVVO_URL}{VIVVO_FS_ADMIN_DIR}css/frontend_modal_options.css" />
	<vte:header type="script" href="{VIVVO_URL}js/admin_menu.js" />
</vte:template>