<div class="box_content">
	<vte:header type="css" href="{VIVVO_THEME}css/plugin_video.css" />
    <vte:header type="script" href="{VIVVO_STATIC_URL}js/jw_player.js" />
    <vte:if test="!{video_holder_id}">
		<vte:variable name="video_holder_id" value="video_playlist" />
        <vte:if test="!{video_width}">
            <vte:variable name="video_width" value="600" />
        </vte:if>
        <vte:if test="!{video_height}">
            <vte:variable name="video_height" value="300" />
        </vte:if>
        <div class="player_container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}system/flash.tpl" id="jwp_{video_holder_id}" width="{video_width}" height="{video_height}" movie="{VIVVO_STATIC_URL}flash/player.swf" flashvars="playerready=jwp_{video_holder_id}_onplayerready&amp;backcolor=181818&amp;frontcolor=EEEEEE" />
        </div>
    </vte:if>
    <script type="text/javascript">
        new JWPlayer(<vte:value select="{video_holder_id|json_encode}" />, '.box_content', '.item_holder');
    </script>
    <vte:foreach item="article" key="index" from="{article_list}">
        <div id="{video_holder_id}_{index}">
            <vte:attribute name="class">video_item <vte:if test="{index} = 1">selected</vte:if></vte:attribute>
            <vte:variable name="video" value="{article|get_video_object}" />
            <div class="item_holder">
                <div class="video_button">
                    <a href="#play" onclick="JWPlayer.players[{video_holder_id|json_encode_attr}].load({video.file|json_encode_attr},{video.image|json_encode_attr},'video',{video.provider|json_encode_attr}).play(this); return false;"><img src="{VIVVO_THEME}img/play_button_light.png" alt="{LNG_PLUGIN_VIDEO_BOX_PLAY}" title="{LNG_PLUGIN_VIDEO_BOX_PLAY}" /></a>
                </div>
                <vte:if test="{article.image}">
                    <vte:if test="{article.get_image_caption}">
                        <vte:variable name="image_caption" value="{article.get_image_caption}" />
                        <vte:else>
                            <vte:variable name="image_caption" value="{article.get_title}" />
                        </vte:else>
                    </vte:if>
                    <div class="video_image">
                        <img src="{VIVVO_STATIC_URL}thumbnail.php?file={article.image}&amp;size=summary_small" alt="{image_caption}" /><br />
                    </div>
                </vte:if>
                <div class="video_title"><a href="{article.get_href}"><vte:value select="{article.get_title}" /></a></div>
            </div>
            <vte:if test="{index} = 1">
                <script type="text/javascript">JWPlayer.players[<vte:value select="{video_holder_id|json_encode}" />].setInitial(<vte:value select="{video.file|json_encode}" />,<vte:value select="{video.image|json_encode}" />,'video',<vte:value select="{video.provider|json_encode}" />);</script>
            </vte:if>
        </div>
    </vte:foreach>
</div>
