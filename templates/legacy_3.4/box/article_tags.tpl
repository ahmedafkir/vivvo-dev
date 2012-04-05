<vte:if test="{VIVVO_MODULES_TAGS}">
	<div id="article_tags">
		<div class="box">
			<div class="box_title_holder"><div class="box_title"><vte:value select="{LNG_ARTICLE_TAGS}" /></div></div>
			<div class="box_body">
				<div class="box_content">
					<vte:if test="{article.get_tag_links}">
                        <vte:foreach from="{article.get_tag_links}" item="tag" key="comma">
                            <a href="{tag.get_href}" title="{LNG_IN} {tag.get_group_name}"><vte:value select="{tag.get_name}" /></a><vte:if test="{comma}!={comma_count}">, </vte:if>
                        </vte:foreach>
                        <vte:else>
                            <vte:value select="{LNG_NO_TAGS_FOR_ARTICLE}" />
                        </vte:else>
                    </vte:if>
				</div>
			</div>
		</div>
	</div>
</vte:if>