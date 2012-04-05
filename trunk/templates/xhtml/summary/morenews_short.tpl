<vte:template>
	<div class="short">
		<div class="short_holder">
			<h2 class="article_title"><a href="{article.get_href}"><vte:value select="{article.get_title}" /></a></h2>
			<vte:if test="{article.image}">
				<div class="image">
					<a href="{article.get_href}">
                        <vte:if test="{article.get_image_caption}">
                            <vte:variable name="image_caption" value="{article.get_image_caption}" />
                            <vte:else>
                                <vte:variable name="image_caption" value="{article.get_title}" />
                            </vte:else>
                        </vte:if>
                        <img src="{VIVVO_STATIC_URL}thumbnail.php?file={article.get_image}&amp;size=summary_medium" alt="{image_caption}" /><br />
                    </a>
				</div>
			</vte:if>
			<p>
				<vte:value select="{article.get_summary}" /> <vte:if test="{article.body}">...</vte:if>
				<vte:if test="!{article.get_link}">
					<vte:if test="{article.body}">
						<a href="{article.get_href}"> <vte:value select="{LNG_FULL_STORY}" /></a>
					</vte:if>
					<vte:else>
						<a class="visit" href="{article.get_link}"><img src="{VIVVO_THEME}img/external.png" alt="{LNG_VISIT_WEBSITE}"/></a>
					</vte:else>
				</vte:if>
			</p>
		</div>
	</div>
</vte:template>