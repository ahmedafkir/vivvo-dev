<vte:if test="{article}">
	<div class="box_breadcrumb">
		<a href="{VIVVO_PROXY_URL}"><vte:value select="{LNG_GO_HOME}" /></a> |
		<vte:foreach item="crumb" from="{article.get_breadcrumb}">
			<a href="{crumb.get_href}"><vte:value select="{crumb.get_category_name}" /></a> |
		</vte:foreach>
		<strong><vte:value select="{article.get_title}" /></strong>
	</div>
</vte:if>