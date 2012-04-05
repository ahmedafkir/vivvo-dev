<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
	<channel>
		<generator>Vivvo CMS <vte:value select="{VIVVO_VERSION}" /></generator>
		<title><vte:value select="{VIVVO_WEBSITE_TITLE}" /></title>
		<link><vte:value select="{VIVVO_URL}" /></link>
		<description><vte:value select="{VIVVO_WEBSITE_TITLE}" /></description>
		<lastBuildDate><vte:value select="{CURRENT_TIME|format_date:'D, d M Y H:i:s O','1'}" /></lastBuildDate>
		<ttl>15</ttl>
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
							<vte:if test="{article.video_attachment}">
								<vte:variable name="video" value="{article|get_video_object}" />
								<media:content url="{video.file}" />
								<vte:else>
									<vte:if test="{article.get_image}">
										<media:thumbnail width="{VIVVO_ARTICLE_MEDIUM_IMAGE_WIDTH}" height="{VIVVO_ARTICLE_MEDIUM_IMAGE_HEIGHT}" url="{VIVVO_URL}thumbnail.php?file={article.image}&amp;size=article_medium" />
									</vte:if>
								</vte:else>
							</vte:if>
							<category><vte:value select="{article.get_category_name}" /></category>
							<pubDate><vte:value select="{article.created|format_date:'D, d M Y H:i:s O','1'}" /></pubDate>
							<description><vte:cdata><vte:value select="{article.get_summary}" /></vte:cdata></description>
							<guid isPermaLink="false"><vte:value select="{article.get_href|md5}" /></guid>
						</item>
					</vte:foreach>
				</vte:if>
			</vte:template>
		</vte:box>
	</channel>
</rss>
