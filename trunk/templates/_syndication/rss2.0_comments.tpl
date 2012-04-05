<rss version="2.0" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
	<channel>
		<generator>Vivvo CMS <vte:value select="{VIVVO_VERSION}" /></generator>
		<title><vte:value select="{VIVVO_WEBSITE_TITLE}" /></title>
		<link><vte:value select="{VIVVO_URL}" /></link>
		<copyright><vte:cdata><vte:value select="{VIVVO_PRODUCT_COPYRIGHT}" /></vte:cdata></copyright>
		<image>
			<title><vte:value select="{VIVVO_WEBSITE_TITLE}" /></title>
			<url><vte:value select="{VIVVO_URL}" />files.php?file=<vte:value select="{VIVVO_GENERAL_WEBSITE_LOGO}" /></url>
			<link><vte:value select="{VIVVO_ABSOLUTE_URL}" /></link>
		</image>
		<vte:box module="box_article_list" params="{content_params}">
			<vte:template>
				<vte:if test="{article_list}">
					<vte:foreach item="article" from="{article_list}">
						<item>
							<title><vte:value select="{article.get_title}" /></title>
							<link><vte:value select="{article.get_absolute_href}" /></link>
							<category><vte:value select="{article.get_category_name}" /></category>
							<pubDate><vte:value select="{article.created|format_date:'D, d M Y H:i:s O','1'}" /></pubDate>
							<description><vte:cdata><vte:value select="{article.get_summary}" /></vte:cdata></description>
						</item>
						<vte:box module="box_comments">
							<vte:params>
								<vte:param name="search_article_id" value="{article.get_id}" />
								<vte:param name="search_sort_by" value="created" />
								<vte:param name="search_order" value="ascending" />
								<vte:param name="search_limit" value="-1" />
							</vte:params>
							<vte:template>
								<vte:if test="{comment_list}">
									<vte:foreach item="comment" from="{comment_list}">
										<item>
											<title><vte:value select="{comment.get_author}" /></title>
											<vte:if test="{comment.get_www}">
												<link>http://<vte:value select="{comment.get_www|preg_replace:'`^http://`',''}" /></link>
											</vte:if>
											<category><vte:value select="{article.get_category_name}" /></category>
											<pubDate><vte:value select="{comment.create_dt|format_date:'D, d M Y H:i:s O','1'}" /></pubDate>
											<description><vte:cdata><vte:value select="{comment.get_description}" /></vte:cdata></description>
										</item>
									</vte:foreach>
								</vte:if>
							</vte:template>
						</vte:box>
					</vte:foreach>
				</vte:if>
			</vte:template>
		</vte:box>
		<description><vte:value select="{VIVVO_WEBSITE_TITLE}" /></description>
	</channel>
</rss>