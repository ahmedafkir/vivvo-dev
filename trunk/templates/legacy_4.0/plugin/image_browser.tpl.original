<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<vte:header type="css" href="{VIVVO_THEME}css/lightbox.css" />
	<vte:header type="script" href="{VIVVO_URL}js/lightbox.js" />	
	<body id="layout_default">
		<div id="container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}box/header.tpl" />
			<div id="content">
				<div id="dynamic_box_left">
					<div id="box_left_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/box_ipod_folders.tpl" start_dir="files/images/" />
					</div>
				</div>
				<div id="dynamic_box_center" style="width:830px;">
					<div id="box_center_holder">
						<vte:if test="{PAGE_TITLE}">
							<h1>
								<vte:value select="{PAGE_TITLE}" />
							</h1>
						</vte:if>
						<div><!--content start -->
							<vte:template>
                                <vte:box module="box_paged_files">
                                    <vte:params>
                                        <vte:param name="search_dir" value="files/images/{curent_location}" />
                                        <vte:param name="search_ext" value="jpg" />
                                        <vte:param name="search_limit" value="10" />
                                        <vte:param name="pg" value="{pg}" />
                                    </vte:params>
                                    <vte:template>
                                        <vte:foreach item="file" from="{file_list}">
                                            <a href="thumbnail.php?file={dir|fix_images_path}/{file.filename}&amp;size=article_large" rel="lightbox[roadtrip]">
                            	                <img src="thumbnail.php?file={dir|fix_images_path}/{file.filename}&amp;size=summary_large" style="padding:1px; margin:2px; border:3px solid #595E61;" />
											</a>
                                        </vte:foreach>
                                        <vte:load module="box_pagination" list="{file_list_object}" />
                                    </vte:template>
                                </vte:box>
                            </vte:template>
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