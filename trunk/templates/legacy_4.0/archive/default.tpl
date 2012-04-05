<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<body id="layout_default">
		<div id="container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}box/header.tpl" />
			<div id="content">
				<div id="dynamic_box_left">
					<div id="box_left_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/sections.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_newsletter.tpl" nocache="1" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_poll.tpl" nocache="1" />
					</div>
				</div>
				<div id="dynamic_box_center">
					<div id="box_center_holder">
						<vte:box module="box_article_list" params = "{content_params}">
							<vte:template>
								<vte:if test="{PAGE_TITLE}">
									<h1>
										<vte:value select="{PAGE_TITLE}" />
									</h1>
								</vte:if>
								<vte:load module="box_pagination" list="{article_list_object}" />
								<div>
									<div class="box_body">
										<vte:if test="{article_list}">
											<vte:foreach item = "article" from = "{article_list}">
												<vte:include file="{VIVVO_TEMPLATE_DIR}summary/default.tpl" />
											</vte:foreach>
											<vte:else>
												<vte:value select="{LNG_NO_ENTRIES}" />
											</vte:else>
										</vte:if>	
									</div>
								</div>
								<vte:load module="box_pagination" list="{article_list_object}" />
							</vte:template>
						</vte:box>
					</div>
				</div>
				<div id="dynamic_box_right">
					<div id="box_right_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/archive_calendar.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}{VIVVO_LOGIN_TEMPLATE}" nocache="1" /> 
						<div id="box_archive_navigation" class="box">
							<div class="box_title_holder"><div class="box_title"><vte:value select="{LNG_ARCHIVE_SEARCH}" /></div></div>
							<div class="box_body">
								<div class="box_content">
									<vte:include file="{VIVVO_TEMPLATE_DIR}box/archive_search.tpl" />
								</div>
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