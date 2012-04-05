<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<vte:header type="css" href="{VIVVO_THEME}css/article_styles.css" />
	<vte:header type="css" href="{VIVVO_THEME}css/print.css" media="print" />
	<vte:header type="keyword" value="{article.get_keywords}" />
	<vte:header type="description" value="{article.get_description}" />
	<body id="layout_two_column">
		<div id="container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}box/header.tpl" />
            <div class="page_top"> </div>
			<div id="content">
				<div id="dynamic_box_center">
					<div id="box_center_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/article_breadcrumb.tpl" />
						<vte:header type="script" href="{VIVVO_STATIC_URL}js/jw_player.js" />
						<div id="article_holder">
							<h1 class="page_title"><vte:value select="{article.get_title}" /></h1>
							<div class="story_stamp">
                            	<vte:if test="{VIVVO_ARTICLE_SHOW_AUTHOR}">
									<vte:if test="{VIVVO_ARTICLE_SHOW_AUTHOR_INFO}">
										<vte:value select="{LNG_AUTHOR_BY}" /> <span class="story_author"><a href="{article.get_author_href}"><vte:value select="{article.get_author_name}" /></a></span>
										<vte:else>
											<vte:value select="{LNG_AUTHOR_BY}" /> <span class="story_author"><vte:value select="{article.get_author_name}" /></span>
										</vte:else>
									</vte:if>
								</vte:if>
								<vte:if test="{VIVVO_ARTICLE_SHOW_DATE}">
									<span class="story_date"><vte:value select="{article.created|pretty_date}" /></span>
								</vte:if>
							</div>
							<vte:include file="{VIVVO_TEMPLATE_DIR}box/font_size.tpl" />
							<div id="article_body">
                            	<div id="box_video_headline_container" style="text-align:center;">
									<vte:if test="{article.video_attachment}">
										<vte:variable name="video" value="{article|get_video_object}" />
                                        <div id="box_video_container"> </div>
                                        <script type="text/javascript">
                                                jwplayer("box_video_container").setup({
                                                    flashplayer: "<vte:value select="{VIVVO_URL}" />flash/player.swf",
													width: 600,
                                                    height: 338,
                                                    controlbar:'bottom',
                                                    backcolor:'181818',
                                                    frontcolor:'EEEEEE',
													stretching:'fill'
                                                });
    
                                                jwplayer("box_video_container").load(
                                                    [
                                                        { file:"<vte:value select="{video.file}"/>", image:"<vte:value select="{VIVVO_STATIC_URL}" />files.php?file=<vte:value select="{article.image}" />" }
                                                    ]
                                                );		
                                        </script>
									</vte:if>
								</div>
								<p><strong><vte:value select="{article.get_abstract}" /></strong></p>
								<vte:value select="{article.get_body}" />
							</div>
							<vte:include file="{VIVVO_TEMPLATE_DIR}box/article_social_bookmarks.tpl" />
							<vte:if test="{article.show_comment}">
								<vte:include file="{VIVVO_TEMPLATE_DIR}box/{VIVVO_COMMENTS_BOX_TEMPLATE}" />
							</vte:if>
						</div>
					</div>
				</div>
				<div id="dynamic_box_right">
					<div id="box_right_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/article_tools.tpl" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/article_social_bookmarks.tpl" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/article_tags.tpl" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_image_gallery_lightbox.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/article_vote.tpl" nocache="1" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/related_news.tpl" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/category_related.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_multiple_attachments.tpl" />
					</div>
				</div>
			</div>
			<div id="footer">
				<vte:include file="{VIVVO_TEMPLATE_DIR}box/footer.tpl" />
			</div>
		</div>
        <vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
			<script type="text/javascript">_gaq.push(['_trackEvent', 'Article', 'View', '<vte:value select="{article.get_id}" />', 1]);</script>
		</vte:if>
	</body>
</html>
