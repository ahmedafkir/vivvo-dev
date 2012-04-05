<vte:box module="box_article_list">
    <vte:params>
        <vte:param name="cache" value="1" />
        <vte:param name="search_order" value="descending" />
        <vte:param name="search_limit" value="5" />
        <vte:param name="search_cid" value="{article.get_category_id}" />
        <vte:param name="add_to_printed" value="true" />
		<vte:param name="exclude_printed" value="true" />
    </vte:params>
    <vte:template>
        <div id="box_category_related" class="box">
            <div class="box_title_holder"><div class="box_title"><vte:value select="{LNG_MORE_FROM}" /> <vte:value select="{article.get_category_name}" /></div></div>
            <div class="box_body">
                <div class="box_content">
                    <ul>
                        <vte:foreach item="article"  key="index" from="{article_list}">
                            <li><a href="{article.get_href}"><vte:value select="{article.get_title}" /></a></li>
                        </vte:foreach>
                    </ul>
                </div>
            </div>
        </div>
    </vte:template>
</vte:box>
