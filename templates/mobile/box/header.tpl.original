<div id="header">
	<div class="header_image"><img src="{VIVVO_THEME}img/header.jpg" alt="header" /></div>
	<vte:box module="box_sections">
		<vte:params>
			<vte:param name="id" value="{VIVVO_ROOT_CATEGORY}" />
		</vte:params>
		<vte:template>
			<div class="box_pages">
				<ul>
					<li><a href="{VIVVO_PROXY_URL}"><vte:value select="{LNG_GO_HOME}" /></a></li>
					<vte:foreach item="category" from="{categories}">
						<vte:if test="{category.view_subcat}">
							<li>
								<a href="{category.get_href}">
									<vte:value select="{category.category_name}" />
								</a>
							</li>
						</vte:if>
					</vte:foreach>
				</ul>
			</div>
		</vte:template>
	</vte:box>
	<vte:include file="{VIVVO_TEMPLATE_DIR}box/search.tpl" />
</div>