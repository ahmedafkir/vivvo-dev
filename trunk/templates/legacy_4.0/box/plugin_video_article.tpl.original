<vte:box module="box_article_list">
    <vte:params>
        <vte:param name="search_field_video_attachment_neq" value="" />
        <vte:param name="search_field_video_attachment_notnull" value="1" />
        <vte:param name="search_id" value="{search_id}" />
        <vte:param name="search_limit" value="1" />
    </vte:params>
    <vte:template>
        <vte:foreach item = "article" from = "{article_list}" key="commented">
            <vte:variable name="video" value="{article|get_video_object}" />
            <h2 class="video_headline"><a href="{article.get_href}"><vte:value select="{article.get_title}" /></a></h2>
            <div class="story_stamp">
                <vte:if test="{VIVVO_ARTICLE_SHOW_AUTHOR}">
                    <vte:if test="{VIVVO_ARTICLE_SHOW_AUTHOR_INFO}">
                        <vte:value select="{LNG_AUTHOR_BY}" /> <span class="story_author"><a href="{article.get_author_href}"><vte:value select="{article.get_author_name}" /></a></span>
                        <vte:else>
                            <vte:value select="{LNG_AUTHOR_BY}" /> <span class="story_author"><vte:value select="{article.get_author_name}" /></span>
                        </vte:else>
                    </vte:if>
                </vte:if>
                <vte:if test="{VIVVO_ARTICLE_SHOW_DATE}">
                    <span class="story_date"><vte:value select="{article.created|pretty_date}" /></span>
                </vte:if>
            </div>
            <p class="video_summary">
                <vte:value select="{article.get_summary}" /> <vte:if test="{article.body}">...</vte:if>
            </p>
            <script type="text/javascript">
                JWPlayer.players[<vte:value select="{video_holder_id|json_encode}" />].load(
                    <vte:value select="{video.file|json_encode}" />,
                    <vte:value select="{video.image|json_encode}" />,
                    'video',
                    <vte:value select="{video.provider|json_encode}" />
                ).play($('video_article_' + <vte:value select="{article.get_id|json_encode}" />));
            </script>
        </vte:foreach>
    </vte:template>
</vte:box>