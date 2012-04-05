<vte:template>
	<div class="box box_white">
        <ul id="popular_tabs" class="tabs">
            <vte:if test="{VIVVO_MODULES_MOST_POPULAR}">
                <li><a href="#box_most_popular"><vte:value select="{LNG_MOST_POPULAR}" /></a></li>
            </vte:if>
            <vte:if test="{VIVVO_MODULES_TOP_RATED}">
                <li><a href="#box_top_rated"><vte:value select="{LNG_TOP_RATED}" /></a></li>
            </vte:if>
            <vte:if test="{VIVVO_COMMENTS_ENABLE}">
                <vte:if test="{VIVVO_MODULES_MOST_COMMENTED}">
                    <li><a href="#box_most_commented"><vte:value select="{LNG_MOST_COMMENTED}" /></a></li>
                </vte:if>
            </vte:if>
        </ul>
        
        <vte:if test="{VIVVO_MODULES_MOST_POPULAR}">
            <vte:box module="box_article_list">
                <vte:params>
                    <vte:param name="cache" value="1" />
                    <vte:param name="search_sort_by" value="most_popular" />
                    <vte:param name="search_limit" value="5" />
                </vte:params>
                <vte:template>
                	<div id="box_most_popular">
                        <vte:foreach item = "article" from = "{article_list}" start="1" loop="1">
                        	<vte:include file="{VIVVO_TEMPLATE_DIR}summary/default.tpl" />
                        </vte:foreach>
                        <ul>
                            <vte:foreach item = "article" from = "{article_list}" start="2">
                                <li>
                                    <a href="{article.get_href}">
                                        <vte:value select="{article.get_title}" />
                                    </a>
                                </li>
                            </vte:foreach>
                        </ul>
                    </div>
                </vte:template>
            </vte:box>
        </vte:if>
        
        <vte:if test="{VIVVO_MODULES_TOP_RATED}">
            <vte:box module="box_article_list">
                <vte:params>
                    <vte:param name="cache" value="1" />
                    <vte:param name="search_sort_by" value="vote_avg" />
                    <vte:param name="search_limit" value="5" />
                </vte:params>
                <vte:template>
                    <ol id="box_top_rated">
                        <vte:foreach item = "article" from = "{article_list}">
                            <li><p><a href="{article.get_href}"><vte:value select="{article.get_title}" /> <span class="article_stats">(<vte:value select="{article.get_vote_average|2}" />)</span></a></p></li>
                        </vte:foreach>
                    </ol>
                </vte:template>
            </vte:box>
        </vte:if>
        <vte:if test="{VIVVO_COMMENTS_ENABLE}">
            <vte:if test="{VIVVO_MODULES_MOST_COMMENTED}">
                <vte:box module="box_article_list">
                    <vte:params>
                        <vte:param name="cache" value="1" />
                        <vte:param name="search_sort_by" value="most_commented" />
                        <vte:param name="search_limit" value="5" />
                    </vte:params>
                    <vte:template>
                        <ul id="box_most_commented">
                            <vte:foreach item="article" from="{article_list}">
                                <li><a href="{article.get_href}"><vte:value select="{article.get_title}" /> <span class="article_stats comment_stats"><vte:value select="{article.get_number_of_comments}" /></span></a></li>
                            </vte:foreach>
                        </ul>
                    </vte:template>
                </vte:box>
            </vte:if>
        </vte:if>
        <script type="text/javascript">
            var popular_tabs = new Control.Tabs('popular_tabs');
        </script>
	</div>
</vte:template>