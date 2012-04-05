<feed xmlns="http://www.w3.org/2005/Atom" xml:base="{VIVVO_URL}">
	<title type="text"><vte:value select="{VIVVO_WEBSITE_TITLE}" /></title>
	<id><vte:value select="{VIVVO_URL}" /></id>
	<link rel="alternate" type="text/html" hreflang="en" href="index.php" />
	<link rel="self" type="application/atom+xml" href="{CURRENT_URL}" />
	<rights><vte:cdata><vte:value select="{VIVVO_PRODUCT_COPYRIGHT}" /></vte:cdata></rights>
	<generator>Vivvo CMS <vte:value select="{VIVVO_VERSION}" /></generator>
	<updated><vte:value select="{CURRENT_TIME}" /></updated>
	<vte:box module="box_article_list" params="{content_params}">
		<vte:template>
			<vte:foreach item="article" from="{article_list}">
				<entry>
					<title><vte:value select="{article.get_title}" /></title>
					<id><vte:value select="{article.get_absolute_href}" /></id>
					<link rel="alternate" type="text/html" hreflang="en" href="{article.get_absolute_href}" />
					<published><vte:value select="{article.created|format_date_atom}" /></published>
					<updated><vte:value select="{article.created|format_date_atom}" /></updated>
					<author>
						<name><vte:value select="{article.get_author_name}" /></name>
					</author>
					<category term="tech" scheme="{article.get_absolute_href}" label="tech" />
					<content type="html"><vte:cdata><vte:value select="{article.get_summary}" /></vte:cdata></content>
				</entry>
				<vte:box module="box_comments">
					<vte:params>
						<vte:param name="search_article_id" value="{article.get_id}" />
						<vte:param name="search_sort_by" value="created" />
						<vte:param name="search_order" value="ascending" />
					</vte:params>
					<vte:template>
						<vte:foreach item="comment" from="{comment_list}">
							<entry>
								<title><vte:value select="{comment.get_author}" /></title>
								<id><vte:value select="{article.get_absolute_href}" /></id>
								<link rel="alternate" type="text/html" hreflang="en" href="{article.get_absolute_href}" />
								<published><vte:value select="{comment.create_dt|format_date_atom}" /></published>
								<updated><vte:value select="{comment.create_dt|format_date_atom}" /></updated>
								<author>
									<name><vte:value select="{comment.get_author}" /></name>
								</author>
								<category term="tech" scheme="{article.get_absolute_href}" label="tech" />
								<content type="html"><vte:cdata><vte:value select="{comment.get_description}" /></vte:cdata></content>
							</entry>
						</vte:foreach>
					</vte:template>
				</vte:box>
			</vte:foreach>
		</vte:template>
	</vte:box>
</feed>