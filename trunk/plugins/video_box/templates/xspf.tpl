<playlist version="1" xmlns="http://xspf.org/ns/0/">
	<title><vte:value select="{VIVVO_WEBSITE_TITLE}" /><vte:if test="{PAGE_TITLE}"> - <vte:value select="{PAGE_TITLE}" /></vte:if></title>
	<info><vte:value select="{VIVVO_URL}" /></info>
	<creator><vte:value select="{VIVVO_WEBSITE_TITLE}" /></creator>
	<trackList>
		<vte:box module="box_article_list" params="{content_params}">
			<vte:template>
				<vte:foreach item="article" from="{article_list}">
					<vte:if test="{article.video_attachment}">
						<vte:variable name="video" value="{article|get_video_object}" />
						<track>
							<title><vte:value select="{article.get_title}" /></title>
							<creator><vte:value select="{article.get_author}" /></creator>
							<location>
								<vte:value select="{video.file}" />
							</location>
							<info><vte:value select="{article.get_absolute_href}" /></info>
							<vte:if test="{article.image}">
								<image><vte:value select="{video.image}" /></image>
							</vte:if>
							<vte:if test="{article.get_abstract}">
								<annotation><vte:cdata><vte:value select="{article.get_abstract}" /></vte:cdata></annotation>
							</vte:if>
						</track>
					</vte:if>
				</vte:foreach>
			</vte:template>
		</vte:box>
	</trackList>
</playlist>