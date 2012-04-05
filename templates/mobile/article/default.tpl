<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<vte:header type="css" href="{VIVVO_THEME}css/article_styles.css" />
	<vte:header type="css" href="{VIVVO_THEME}css/print.css" media="print" />
	<vte:header type="keyword" value="{article.get_keywords}" />
	<vte:header type="description" value="{article.get_description}" />
	<body id="layout_default">
		<div id="container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}box/header.tpl" />
			<div id="content">
				<div id="dynamic_box_center">
					<div id="box_center_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/article_breadcrumb.tpl" />
						<div id="article_holder">
							<h1><vte:value select="{article.get_title}" /></h1>
							<div class="article_metadata">
								<vte:if test="{VIVVO_ARTICLE_SHOW_DATE}">
									<span class="metadata_time"><vte:value select="{article.get_created}" /></span>
								</vte:if>
								<vte:if test="{VIVVO_ARTICLE_SHOW_AUTHOR}">
									<vte:if test="{VIVVO_ARTICLE_SHOW_AUTHOR_INFO}">
										<a href="{article.get_author_href}"><vte:value select="{article.get_author_name}" /></a>
										<vte:else>
											<vte:value select="{article.get_author_name}" />
										</vte:else>
									</vte:if>
								</vte:if>
							</div>
							<div id="article_body">
								<vte:if test="{article.image}">
									<div class="image" style="width:{VIVVO_SUMMARY_MEDIUM_IMAGE_WIDTH}px;">
										<img src="{VIVVO_URL}thumbnail.php?file={article.image}&amp;size=summary_medium" alt="image" />
									</div>
								</vte:if>
								<vte:if test="{article.image}">
									<p><strong><vte:value select="{article.get_abstract}" /></strong></p>
								</vte:if>
								<vte:value select="{article.get_body}" />
								<vte:include file="{VIVVO_TEMPLATE_DIR}box/related_news.tpl" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="footer">
				<vte:include file="{VIVVO_TEMPLATE_DIR}box/footer.tpl" />
			</div>
		</div>
	</body>
</html>