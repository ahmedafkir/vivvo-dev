<vte:if test="{VIVVO_COMMENTS_ENABLE}">
	<vte:box module="box_comments">
		<vte:params>
			<vte:param name="search_article_id" value="{article.get_id}" />
			<vte:param name="search_sort_by" value="created" />
			<vte:param name="search_order" value="{VIVVO_COMMENTS_ORDER}" />
			<vte:param name="pg" value="{pg}" />
			<vte:param name="threaded" value="{VIVVO_COMMENTS_ENABLE_THREADED}" />
			<vte:param name="search_limit" value="{VIVVO_COMMENTS_NUM_PER_PAGE}" />
		</vte:params>
		<vte:template>
			<div id="box_comments">
				<div class="box_body">
					<div class="comment_block">
						<vte:if test="!{ajax_output}">
							<h4>
                            	<vte:if test="{VIVVO_MODULES_FEED}">
                                    <a href="{CURRENT_URL|switch_format:'rss'}"><img src="{VIVVO_THEME}img/icon_feed.gif" class="comment_feed" alt="{LNG_COMMENT_RSS}" title="{LNG_COMMENT_RSS}" /></a>
                                </vte:if>
								<vte:value select="{LNG_COMMENTS}" /> (<vte:value select="{article.get_number_of_comments}" /> <vte:value select="{LNG_COMMENT_POSTED}" />):
							</h4>
						</vte:if>
						<div id="comment_list">
							<vte:attribute name="style">
								<vte:if test="{ajax_output}">margin:0</vte:if>
							</vte:attribute>
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
							<div id="new_comment_holder">  </div>
							<vte:box module="box_pagination">
								<vte:params>
									<vte:param name="list" value="{comment_list_object}" />
								</vte:params>
								<vte:template>
									<div id="box_pagination">
										<span class="pagination">
											<vte:if test="{previous_page} != ''">
												<a href="javascript:loadCommentsPage({current_page}-1);"><img src="{VIVVO_THEME}img/pagination_back.gif" alt="back" /></a>
											</vte:if>
											<vte:foreach item="page" from="{page_list}">
												<vte:if test="{current_page} != {page[number]}">
													<a href="javascript:loadCommentsPage({page[number]});"><vte:value select="{page[number]}" /></a>
													<vte:else>
														<vte:value select="{page[number]}" />
													</vte:else>
												</vte:if>
											</vte:foreach>
											<vte:if test="{next_page} != ''">
												<a href="javascript:loadCommentsPage({current_page}+1);"><img src="{VIVVO_THEME}img/pagination_next.gif" alt="next" /></a>
											</vte:if>
										</span>
										<vte:value select="{LNG_TOTAL}" />:
										<span class="pagination_total">
											<vte:value select="{total_records}" />
										</span>
										| <vte:value select="{LNG_DISPLAYING}" />:
										<span class="pagination_total">
											<vte:value select="{displaying}" />
										</span>
									</div>
								</vte:template>
							</vte:box>
						</div>
						<vte:if test="!{ajax_output}">
							<vte:include file="{VIVVO_TEMPLATE_DIR}box/comments_add.tpl" />
						</vte:if>
					</div>
				</div>
			</div>
		</vte:template>
	</vte:box>
	<vte:if test="!{ajax_output}">
		<script type="text/javascript">
			function reportComment(id) {
				new Ajax.Updater('comment_report_' + id, '<vte:value select="{VIVVO_ABSOLUTE_URL}" />', {
					parameters: {
						action: 'comment',
						cmd: 'reportInappropriateContent',
						COMMENT_id: id,
						template_output: 'box/dump'
					}
				});
			}
			function voteComment(id, vote) {
				new Ajax.Updater('comment_vote_' + id, '<vte:value select="{VIVVO_ABSOLUTE_URL}" />', {
					parameters: {
						action: 'comment',
						cmd: 'vote',
						COMMENT_id: id,
						COMMENT_vote: vote,
						template_output: 'box/dump'
					}
				});
			}
			function loadCommentsPage(pg) {
				new Ajax.Updater('comment_list', '<vte:value select="{VIVVO_ABSOLUTE_URL}" />', {
					parameters: {
						action: 'comment',
						cmd: 'proxy',
						pg: pg,
						CURRENT_URL: '<vte:value select="{CURRENT_URL}" />',
						article_id: <vte:value select="{article.get_id}" />,
						template_output: 'box/comments'
					}
				});
			}

			function updateComments() {

				var commentParam = $('comment_form').serialize(true);
				commentParam.template_output = 'box/comments_add';
				commentParam.form_container = 'comment_form_holder';

				var dump = $('comment_dump');
				if (dump) dump.remove();

				new Ajax.Updater(holder, '<vte:value select="{VIVVO_ABSOLUTE_URL}" />', {
					parameters: commentParam,
					evalScripts: true,
					insertion: 'before'
					<vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
					,onSuccess: function(xhr) {
						if (xhr.getResponseHeader('X-Vivvo-Action-Status') == 1) {
							_gaq.push(['_trackEvent', 'Article', 'Comment', '<vte:value select="{article.get_id}" />', 1]);
						}
					}
					</vte:if>
				});
			}
			<vte:if test="{VIVVO_COMMENTS_ENABLE_THREADED}">
			function reply_to_comment(id, root, summary) {
				$('COMMENT_reply_to').value = id;
				$('COMMENT_root_comment').value = root;
				$('COMMENT_description').focus();
				$('writing_reply_to').update(summary);
				$('writing_reply').show();
			}

			function cancelReplyTo() {
				$('COMMENT_reply_to').value = '';
				$('COMMENT_root_comment').value = '';
				$('writing_reply').hide();
			}
			</vte:if>
		</script>
	</vte:if>
</vte:if>
