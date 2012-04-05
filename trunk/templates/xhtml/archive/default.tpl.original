<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<body id="layout_two_column">
		<div id="container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}box/header.tpl" />
            <div class="page_top"> </div>
			<div id="content">
				<div id="dynamic_box_center">
					<div id="box_center_holder">
						<vte:box module="box_article_list" params = "{content_params}">
							<vte:template>
								<vte:if test="{PAGE_TITLE}">
									<h1 class="page_title"><vte:value select="{PAGE_TITLE}" /></h1>
								</vte:if>
								<vte:load module="box_pagination" list="{article_list_object}" />
                                <vte:if test="{article_list}">
                                    <vte:foreach item = "article" from = "{article_list}">
                                        <vte:include file="{VIVVO_TEMPLATE_DIR}summary/default.tpl" />
                                    </vte:foreach>
                                    <vte:else>
                                        <h5 class="subtitle"><vte:value select="{LNG_NO_ENTRIES}" /></h5>
                                    </vte:else>
                                </vte:if>	
								<vte:load module="box_pagination" list="{article_list_object}" />
							</vte:template>
						</vte:box>
					</div>
				</div>
				<div id="dynamic_box_right">
					<div id="box_right_holder">
                    	<div id="box_calendar" class="box box_white">
                            <vte:include file="{VIVVO_TEMPLATE_DIR}box/archive_calendar.tpl" />
                        </div>
                        <div id="box_archive_navigation" class="box box_white">
                            <h3 class="box_title title_white"><vte:value select="{LNG_ARCHIVE_SEARCH}" /></h3>
                            <vte:include file="{VIVVO_TEMPLATE_DIR}box/archive_search.tpl" />
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