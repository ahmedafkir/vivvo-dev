<vte:foreach item="comment" from="{comment_list}">
	<div class="comment_holder">
		<a name="comment_{comment.get_id}"> </a>
		<div class="comment_header">
			<vte:if test="{comment.get_www}">
				<a href="http://{comment.get_www}" target="_blank"><vte:value select="{comment.get_author}" /></a>
				<vte:else>
					<strong><vte:value select="{comment.get_author}" /></strong>
				</vte:else>
			</vte:if>
			<vte:value select="{LNG_ARTICLE_COMMENTS_POSTED_ON}" />
			<vte:value select="{comment.get_create_dt}" />
			<vte:if test="{VIVVO_COMMENTS_ENABLE_THREADED}">
				<div class="comment_reply"><a href="{CURRENT_URL}#post-reply" onclick="reply_to_comment({comment.get_id},{comment.get_root_comment},{comment.get_plain_summary|json_encode_attr});"><vte:value select="{LNG_COMMENT_REPLY}" /></a></div>
			</vte:if>
		</div>
		<div class="comment_body_holder">
			<div class="comment_body">
				<img src="{comment.get_avatar}" alt="avatar" />
				<div class="comment_text">
					<vte:value select="{comment.get_description}" />
				</div>
				<vte:if test="{VIVVO_EMAIL_ENABLE}">
					<vte:if test="{VIVVO_COMMENTS_REPORT_INAPPROPRIATE}">
						<div id="comment_report_{comment.get_id}" class="comment_report">
							<a href="javascript:reportComment({comment.get_id});">
								<img src="{VIVVO_THEME}img/alert.gif" title="{LNG_COMMENTS_REPORT_INAPPROPRIATE}" alt="{LNG_COMMENTS_REPORT_INAPPROPRIATE}" />
							</a>
						</div>
					</vte:if>
				</vte:if>
				<div class="comment_rate">
					<a href="javascript:voteComment({comment.get_id}, 1);"><img src="{VIVVO_THEME}img/thumbs_up.gif" title="{LNG_COMMENTS_THUMB_UP}" alt="{LNG_COMMENTS_THUMB_UP}" /></a>
					<a href="javascript:voteComment({comment.get_id}, -1);"><img src="{VIVVO_THEME}img/thumbs_down.gif" title="{LNG_COMMENTS_THUMB_DOWN}" alt="{LNG_COMMENTS_THUMB_DOWN}" /></a>
					<div id="comment_vote_{comment.get_id}" class="result">
						<vte:value select="{comment.get_vote}" />
					</div>
				</div>
			</div>
			<vte:if test="{VIVVO_COMMENTS_ENABLE_THREADED}">
				<vte:value select="{comment.render_responses|'box/comment_responses.tpl'}" />
			</vte:if>
		</div>
	</div>
</vte:foreach>