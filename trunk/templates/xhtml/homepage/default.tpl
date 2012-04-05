<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<body id="layout_two_column">
		<div id="container"><vte:include file="{VIVVO_TEMPLATE_DIR}box/header.tpl" />
			<div id="content">
				<vte:include file="{VIVVO_TEMPLATE_DIR}box/ticker.tpl" />
				<div id="dynamic_box_center">
					<div id="box_center_holder">
						<div id="content_features" class="features_equal_default">
							<div id="content_features_left">
								<vte:include file="{VIVVO_TEMPLATE_DIR}box/headlines.tpl" />
							</div>
							<div id="content_features_right">
                                <vte:include file="{VIVVO_TEMPLATE_DIR}box/latest_news.tpl" />                                
							</div>
						</div>
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_video_headlines.tpl" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/more_news.tpl" />
					</div>
				</div>
				<div id="dynamic_box_right">
					<div id="box_right_holder">
                    	<vte:include file="{VIVVO_TEMPLATE_DIR}{VIVVO_LOGIN_TEMPLATE}" nocache="1" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/popular_box.tpl" />
                    	<vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_newsletter.tpl" nocache="1" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/featured_author.tpl" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/tag_cloud.tpl" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_poll.tpl" nocache="1" />
					</div>
				</div>
			</div>
			<div id="footer">
				<vte:include file="{VIVVO_TEMPLATE_DIR}box/footer.tpl" />
			</div>
		</div>
	</body>
</html>
