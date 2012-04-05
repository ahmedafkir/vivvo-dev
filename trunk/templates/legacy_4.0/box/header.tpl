<div id="header">
	<div class="header_image">
        <a href="{VIVVO_URL}"><img src="{VIVVO_THEME}img/logo.gif" alt="{VIVVO_WEBSITE_TITLE}" title="{VIVVO_WEBSITE_TITLE}" /></a>
        <div class="header_ad"><!-- Code for your banner here --></div>
    </div>
	<vte:include file="{VIVVO_TEMPLATE_DIR}box/pages.tpl" />
	<vte:if test="{VIVVO_MODULES_DHTML_SECTIONS}">
		<vte:include file="{VIVVO_TEMPLATE_DIR}box/dhtml_menu.tpl" />
	</vte:if>
	<vte:include file="{VIVVO_TEMPLATE_DIR}box/search.tpl" />
</div>