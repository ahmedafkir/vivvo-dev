<vte:box module="box_article_images">
    <vte:params>
        <vte:param name="search_id" value="{article.id}" />
    </vte:params>
    <vte:template>
    	<vte:if test="{image_list}">
            <vte:header type="css" href="{VIVVO_THEME}css/lightbox.css" />
            <vte:header type="css" href="{VIVVO_THEME}css/plugin_image_gallery.css" />
            <vte:header type="script" href="{VIVVO_STATIC_URL}js/framework/effects.js" />
            <vte:header type="script" href="{VIVVO_STATIC_URL}js/framework/builder.js" />	
            <vte:header type="script" href="{VIVVO_STATIC_URL}js/lightbox.js" />
            <div class="box box_white">
                <h3 class="box_title title_white"><vte:value select="{LNG_PLUGIN_IMAGE_GALLERY_BOX_TITLE}" /></h3>
                <div class=" plugin_image_holder">
                    <vte:foreach item="image" from="{image_list}">
                       <span class="lightbox_image" style="width:{VIVVO_SUMMARY_SMALL_IMAGE_WIDTH}px; height:{VIVVO_SUMMARY_SMALL_IMAGE_WIDTH}px;"><a href="{VIVVO_STATIC_URL}thumbnail.php?file={image.real_path}&amp;size=article_large" rel="lightbox[gallery]" title="{image.title}"><img src="{VIVVO_STATIC_URL}thumbnail.php?file={image.real_path}&amp;size=summary_small" title="{image.title}" alt="{image.title}" /></a></span>
                    </vte:foreach>
                </div>
            </div>
        </vte:if>
    </vte:template>
</vte:box>