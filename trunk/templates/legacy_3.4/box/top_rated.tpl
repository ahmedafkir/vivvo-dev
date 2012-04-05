<vte:if test="{VIVVO_MODULES_TOP_RATED}">
    <vte:box module="box_article_list">
        <vte:params>
            <vte:param name="cache" value="1" />
            <vte:param name="search_sort_by" value="vote_avg" />
            <vte:param name="search_limit" value="5" />
            <vte:param name="fields" value="title,SEfriendly,category_id" />
        </vte:params>
        <vte:template>
         <div id="box_top_rated" class="box">
            <div class="box_title_holder"><div class="box_title">Top Rated</div></div>
            <div class="box_body">
                <div class="box_content">
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
            </div>
        </div>
        </vte:template>
    </vte:box>
</vte:if>

