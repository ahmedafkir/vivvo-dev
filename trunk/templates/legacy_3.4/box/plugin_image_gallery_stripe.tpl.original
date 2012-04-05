<vte:box module="box_article_images">
    <vte:params>
        <vte:param name="search_id" value="{article.id}" />
    </vte:params>
    <vte:template>
    	<vte:if test="{image_list}">
            <vte:header type="css" href="{VIVVO_THEME}css/plugin_image_gallery.css" />
            <vte:header type="script" href="{VIVVO_URL}js/framework/effects.js" />
            <vte:header type="script" href="{VIVVO_URL}js/glider.js" />
            <vte:header type="script" href="{VIVVO_URL}plugins/image_gallery/js/plugin_image_gallery.js" />
			<div id="image_gallery_stripe" class="image">
				<div id="image_gallery_stripe_body" class="box_stripes" style="width:{VIVVO_ARTICLE_MEDIUM_IMAGE_WIDTH}px;">
					<div class="scroller" style="height: auto;">
						<div style="width:10000px;">
                            <vte:foreach item="image" from="{image_list}">
                                <div class="section">
                                    <img src="thumbnail.php?file={image.real_path}&amp;size=article_medium"  title="{image.title}" alt="{image.title}" />
                                    <div class="stripe_summary_holder" style="width:{VIVVO_ARTICLE_MEDIUM_IMAGE_WIDTH}px;">
                                        <strong><vte:value select="{image.get_title}" /></strong>
                                        <div class="summary">
                                            <vte:value select="{image.get_description}" />
                                        </div>
                                    </div>
                                </div>
                            </vte:foreach>
						</div>
					</div>
					<div class="controls"> 
						<span class="section_next" onclick="image_gallery_glider.next();$('image_current').update(image_gallery_glider.current._index + 1);"><img src="{VIVVO_THEME}img/image_scroller_next.gif" alt="Next" title="Next" /></span>
						<span class="section_previous" onclick="image_gallery_glider.previous();$('image_current').update(image_gallery_glider.current._index + 1);"><img src="{VIVVO_THEME}img/image_scroller_back.gif" alt="Previous" title="Previous" /></span>
						<span id="image_current">1</span> of <span id="image_total"><vte:value select="{image_list_object.get_count}" /></span> 
					</div>
				</div>
			</div>
        </vte:if>
    </vte:template>
</vte:box>