<div class="box box_video">
	<vte:header type="css" href="{VIVVO_THEME}css/plugin_video.css" />
	<vte:header type="script" href="{VIVVO_STATIC_URL}js/jw_player.js" />
    <div id="box_video_headline_container" class="headline_video_player">
		<vte:include file="{VIVVO_TEMPLATE_DIR}system/flash.tpl" id="jwp_video_headlines" width="320" height="240" movie="{VIVVO_STATIC_URL}flash/player.swf" flashvars="playerready=jwp_video_headlines_onplayerready&amp;backcolor=181818&amp;frontcolor=EEEEEE&amp;stretching=fill" />
		<script type="text/javascript">new JWPlayer('video_headlines', '.headline_video_playlist', 'headline_video_item');</script>
    </div>
    <vte:box module="box_article_list">
        <vte:params>
        	<vte:param name="search_topic_id" value="1" />
            <vte:param name="search_limit" value="5" />
            <vte:param name="search_all_tag_ids" value="1,3" />
            <vte:param name="search_sort_by" value="order_num" />
            <vte:param name="search_order" value="descending" />
            <vte:param name="cache" value="1" />
            <vte:param name="add_to_printed" value="true" />
        </vte:params>
        <vte:template>
        	<div class="headline_video_playlist">
                <vte:foreach item="article" from="{article_list}" key="index">
					<vte:variable name="video" value="{article|get_video_object}" />
                    <div class="headline_video_item">
                    	<vte:if test="{article.image}">
                        	<div class="image">
                                <a href="{article.get_href}">
                                    <vte:if test="{article.get_image_caption}">
                                        <vte:variable name="image_caption" value="{article.get_image_caption}" />
                                        <vte:else>
                                            <vte:variable name="image_caption" value="{article.get_title}" />
                                        </vte:else>
                                    </vte:if>
                                    <img src="{VIVVO_STATIC_URL}thumbnail.php?file={article.image}&amp;size=summary_small" alt="{image_caption}" /><br />
                                </a>
                            </div>
                        </vte:if>
                        <a href="{article.get_href}"><vte:value select="{article.title}" /><vte:if test="{article.get_link}"> <img src="{VIVVO_THEME}img/external.png" alt="{LNG_VISIT_WEBSITE}"/></vte:if></a>
                        <a href="#play" onclick="JWPlayer.players.video_headlines.load({video.file|json_encode_attr},{video.image|json_encode_attr},'video',{video.provider|json_encode_attr}).play(this);return false;"><img src="{VIVVO_THEME}img/play_button.png" class="play_video" alt="{LNG_PLUGIN_VIDEO_BOX_PLAY}" title="{LNG_PLUGIN_VIDEO_BOX_PLAY}" /></a>
                    </div>
					<vte:if test="{index} = 1">
						<script type="text/javascript">JWPlayer.players.video_headlines.setInitial(<vte:value select="{video.file|json_encode}" />,<vte:value select="{video.image|json_encode}" />,'video',<vte:value select="{video.provider|json_encode}" />);</script>
					</vte:if>
                </vte:foreach>
            </div>
        </vte:template>
    </vte:box>
    <div class="clearer"> </div>
</div>
