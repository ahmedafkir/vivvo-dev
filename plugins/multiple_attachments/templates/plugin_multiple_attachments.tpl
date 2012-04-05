<vte:box module="box_article_attachments">
	<vte:params>
		<vte:param name="search_id" value="{article.id}" />
	</vte:params>
	<vte:template>
    	<vte:header type="script" href="{VIVVO_STATIC_URL}js/tooltips.js" />
        <vte:header type="script" href="{VIVVO_STATIC_URL}js/framework/builder.js" />
        <vte:header type="script" href="{VIVVO_STATIC_URL}js/framework/effects.js" />
		<vte:if test="{attachment_list}">
			<div class="box box_gray">
				<h3 class="box_title title_gray"><vte:value select="{LNG_ATTACHMENTS}" /></h3>
                <div class=" plugin_image_holder">
                    <vte:foreach item="attachment" from="{attachment_list}">
                        <div class="content_attachment">
                            <img src="{VIVVO_THEME}img/attachment.gif" alt="{LNG_DOWNLOAD_ATTACHMENT}" title="{LNG_DOWNLOAD_ATTACHMENT}" />
                            <a href="{VIVVO_STATIC_URL}files.php?force&amp;file={attachment.real_path}" class="desc" title="{attachment.get_description}">
                                <vte:if test="{attachment.title}">
                                    <vte:value select="{attachment.title}" />
                                    <vte:else>
                                        <vte:value select="{attachment.get_filename}" />
                                    </vte:else>
                                </vte:if>
                            </a>
                        </div>
                    </vte:foreach>
                </div>
			</div>
		</vte:if>
        <script type="text/javascript">
			$$(".desc").each( function(link) {
				new Tooltip(link, {
					mouseFollow: false
				});
			});
		</script>
	</vte:template>
</vte:box>