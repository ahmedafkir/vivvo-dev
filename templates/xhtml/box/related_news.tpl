<vte:box module="box_article_list">
    <vte:params>
        <vte:param name="search_sort_by" value="relevance" />
        <vte:param name="search_limit" value="5" />
        <vte:param name="search_related" value="{article.get_id}" />
    </vte:params>
    <vte:template>
        <vte:if test="{article_list}">
            <div id="box_related_news" class="box box_white">
                <h3 class="box_title title_white"><vte:value select="{LNG_RELATED_NEWS}" /></h3>
                <ul>
                    <vte:foreach item = "article" from = "{article_list}">
                        <li>
                            <a href="{article.get_href}">
                                <vte:value select="{article.get_title}" />
                            </a>
                        </li>
                    </vte:foreach>
                </ul>
            </div>
        </vte:if>
    </vte:template>
</vte:box>
