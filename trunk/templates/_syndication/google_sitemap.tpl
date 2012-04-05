<urlset xmlns="http://www.google.com/schemas/sitemap/0.84" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">
	<vte:box module="box_users">
		<vte:params>
			<vte:param name="search_limit" value="1000" />
			<vte:param name="search_user_type" value="{VIVVO_MODULES_FEATURED_AUTHOR_GROUPS}" />
		</vte:params>
		<vte:template>
			<vte:foreach item="user" from="{user_list}">
				<url>
					<loc><vte:value select="{user.get_href|make_absolute_url}" /></loc>
					<lastmod><vte:value select="{CURRENT_TIME}" /></lastmod>
				</url>
			</vte:foreach>
		</vte:template>
	</vte:box>
	<vte:box module="box_sections">
		<vte:template>
			<vte:foreach item="category" from="{categories}">
				<url>
					<loc>
                    	<vte:if test="{category.redirect}">
                            <vte:value select="{category.redirect|make_absolute_url}" />
                            <vte:else>
                                <vte:value select="{category.get_href|make_absolute_url}" />
                            </vte:else>
                        </vte:if>
                    </loc>
					<lastmod><vte:value select="{CURRENT_TIME}" /></lastmod>
				</url>
				<vte:box module="box_article_list">
					<vte:params>
						<vte:param name="search_sort_by" value="created" />
						<vte:param name="search_limit" value="100" />
						<vte:param name="search_cid" value="{category.id}" />
					</vte:params>
					<vte:template>
						<vte:foreach item="article" from="{article_list}">
							<url>
								<loc><vte:value select="{article.get_absolute_href}" /></loc>
								<lastmod><vte:value select="{article.created|format_date_atom}" /></lastmod>
							</url>
						</vte:foreach>
					</vte:template>
				</vte:box>
				<vte:if test="{category.subcategories}">
					<vte:load module="box_sections" id="{category.id}" template_string="{template_string}" />
				</vte:if>
			</vte:foreach>
		</vte:template>
	</vte:box>
</urlset>