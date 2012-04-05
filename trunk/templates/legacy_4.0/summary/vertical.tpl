<vte:template>
	<div class="category_headline">
		<vte:if test="{article.image}">
			<div class="image">
				<a href="{article.get_href}">
                    <vte:if test="{article.get_image_caption}">
                        <vte:variable name="image_caption" value="{article.get_image_caption}" />
                        <vte:else>
                            <vte:variable name="image_caption" value="{article.get_title}" />
                        </vte:else>
                    </vte:if>
                    <img src="{VIVVO_STATIC_URL}thumbnail.php?file={article.image}&amp;size={image_size}" alt="{image_caption}" />
                </a><br />
			</div>
		</vte:if>
		<h2><a href="{article.get_href}"><vte:value select="{article.get_title}" /></a></h2>
		<div class="summary">
			<vte:value select="{article.get_summary}" />
		</div>
		<div class="article_link">
			<vte:if test="!{article.get_link}">
				<vte:if test="{article.body}">
					<a href="{article.get_href}"> <vte:value select="{LNG_FULL_STORY}" /></a>
				</vte:if>
				<vte:else>
					<a class="visit" href="{article.get_link}"><img src="{VIVVO_THEME}img/external.png" alt="{LNG_VISIT_WEBSITE}"/></a>
				</vte:else>
			</vte:if>
		</div>
	</div>
</vte:template>