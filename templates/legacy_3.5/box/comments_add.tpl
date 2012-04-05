<vte:if test="{VIVVO_COMMENTS_ENABLE}">
	<vte:if test="{ajax_output}">
		<vte:if test="{action}">
			<vte:if test="!{CURRENT_USER}">
				<vte:box module="box_comments">
					<vte:params>
						<vte:param name="search_article_id" value="{COMMENT_article_id}" />
						<vte:param name="search_limit" value="1" />
						<vte:param name="search_sort_by" value="created" />
						<vte:param name="search_order" value="descending" />
					</vte:params>
					<vte:template>
						<vte:if test="{guest_group_wait_comment_for_approval} = '2'">
							<div id="comment_dump">
								<vte:value select="{LNG_ADD_COMMENTS_WAITING}" />
							</div>
							<vte:else>
								<vte:foreach item="comment" from="{comment_list}">
									<div class="comment_holder">
										<div class="comment_header">
											<vte:if test="{comment.get_www}">
												<a href="http://{comment.get_www}" target="_blank"><vte:value select="{comment.get_author}" /></a>
												<vte:else>
													<strong><vte:value select="{comment.get_author}" /></strong>
												</vte:else>
											</vte:if>
											<vte:value select="{LNG_ARTICLE_COMMENTS_POSTED_ON}" />
											<vte:value select="{comment.get_create_dt}" />
										</div>
										<div class="comment_body_holder">
											<div class="comment_body">
												<img src="{comment.get_avatar}" alt="avatar" />
												<div class="comment_text">
													<vte:value select="{comment.get_description}" />
												</div>
												<vte:if test="{VIVVO_COMMENTS_REPORT_INAPPROPRIATE}">
													<div id="comment_report_{comment.get_id}" class="comment_report">
														<a href="javascript:reportComment({comment.get_id});">
															<img src="{VIVVO_THEME}img/alert.gif" title="Report as inappropriate" alt="image" />
														</a>
													</div>
												</vte:if>
												<div class="comment_rate">
													<a href="javascript:voteComment({comment.get_id}, 1);"><img src="{VIVVO_THEME}img/thumbs_up.gif" title="{LNG_COMMENTS_THUMB_UP}" alt="{LNG_COMMENTS_THUMB_UP}" /></a>
													<a href="javascript:voteComment({comment.get_id}, -1);"><img src="{VIVVO_THEME}img/thumbs_down.gif" title="{LNG_COMMENTS_THUMB_DOWN}" alt="{LNG_COMMENTS_THUMB_DOWN}" /></a>
													<div id="comment_vote_{comment.get_id}" class="result">
														<vte:value select="{comment.get_vote}" />
													</div>
												</div>
												<vte:if test="{VIVVO_COMMENTS_ENABLE_THREADED}">
													<div class="comment_reply"><a href="{CURRENT_URL}#post-reply" onclick="reply_to_comment({comment.get_id},{comment.get_root_comment},{comment.get_plain_summary|json_encode_attr});"><vte:value select="{LNG_COMMENT_REPLY}" /></a></div>
												</vte:if>
											</div>
										</div>
									</div>
								</vte:foreach>
							</vte:else>
						</vte:if>
					</vte:template>
				</vte:box>
				<vte:else>
					<vte:if test="{CURRENT_USER.privileges[ARTICLE_COMMENT]} = '2'">
						<div id="comment_dump">
							<vte:value select="{LNG_ADD_COMMENTS_WAITING}" />
						</div>
						<vte:else>
							<vte:box module="box_comments">
								<vte:params>
									<vte:param name="search_article_id" value="{COMMENT_article_id}" />
									<vte:param name="search_limit" value="1" />
									<vte:param name="search_sort_by" value="created" />
									<vte:param name="search_order" value="descending" />
								</vte:params>
								<vte:template>
									<vte:foreach item="comment" from="{comment_list}">
										<div class="comment_holder">
											<div class="comment_header">
												<vte:if test="{comment.get_www}">
													<a href="http://{comment.get_www}" target="_blank"><vte:value select="{comment.get_author}" /></a>
													<vte:else>
														<strong><vte:value select="{comment.get_author}" /></strong>
													</vte:else>
												</vte:if>
												<vte:value select="{LNG_ARTICLE_COMMENTS_POSTED_ON}" />
												<vte:value select="{comment.get_create_dt}" />
											</div>
											<div class="comment_body_holder">
												<div class="comment_body">
													<img src="{comment.get_avatar}" alt="avatar" />
													<div class="comment_text">
														<vte:value select="{comment.get_description}" />
													</div>
													<vte:if test="{VIVVO_COMMENTS_REPORT_INAPPROPRIATE}">
														<div id="comment_report_{comment.get_id}" class="comment_report">
															<a href="javascript:reportComment({comment.get_id});">
																<img src="{VIVVO_THEME}img/alert.gif" title="Report as inappropriate" alt="image" />
															</a>
														</div>
													</vte:if>
													<div class="comment_rate">
														<a href="javascript:voteComment({comment.get_id}, 1);"><img src="{VIVVO_THEME}img/thumbs_up.gif" title="{LNG_COMMENTS_THUMB_UP}" alt="{LNG_COMMENTS_THUMB_UP}" /></a>
														<a href="javascript:voteComment({comment.get_id}, -1);"><img src="{VIVVO_THEME}img/thumbs_down.gif" title="{LNG_COMMENTS_THUMB_DOWN}" alt="{LNG_COMMENTS_THUMB_DOWN}" /></a>
														<div id="comment_vote_{comment.get_id}" class="result">
															<vte:value select="{comment.get_vote}" />
														</div>
													</div>
													<vte:if test="{VIVVO_COMMENTS_ENABLE_THREADED}">
														<div class="comment_reply"><a href="{CURRENT_URL}#post-reply" onclick="reply_to_comment({comment.get_id},{comment.get_root_comment},{comment.get_plain_summary|json_encode_attr});"><vte:value select="{LNG_COMMENT_REPLY}" /></a></div>
													</vte:if>
												</div>
											</div>
										</div>
									</vte:foreach>
								</vte:template>
							</vte:box>
						</vte:else>
					</vte:if>
				</vte:else>
			</vte:if>
			<vte:else>
				<div id="comment_dump">
					<vte:foreach item = "message" from = "{DUMP_MESSAGES}">
						<span class="{message.type}"><vte:value select="{message.get_message}" />
							<vte:if test="{message.additional_info} != ''">
								: <vte:value select="{message.additional_info}" />
							</vte:if>
						</span>
					</vte:foreach>
				</div>
			</vte:else>
		</vte:if>
		<vte:else>
			<div id="comment_form_holder">
				<h4><vte:value select="{LNG_COMMENT_POST}" /> <img src="{VIVVO_THEME}img/comment_icon.gif" alt="comment" /></h4>
				<form method="post" id="comment_form" onsubmit="updateComments();return false;">
					<input type="hidden" name="action" value="comment" />
					<input type="hidden" name="cmd" value="add" />
					<vte:if test="{VIVVO_COMMENTS_ENABLE_THREADED}">
						<input type="hidden" id="COMMENT_reply_to" name="COMMENT_reply_to" value="" />
						<input type="hidden" id="COMMENT_root_comment" name="COMMENT_root_comment" value="" />
					</vte:if>
					<input type="hidden" name="COMMENT_article_id" value="{article.id}" />
					<div id="post-reply" class="box">
						<vte:if test="!{CURRENT_USER}">
							<div class="form_line">
								<label><vte:value select="{LNG_ADD_COMMENTS_AUTHOR}" />: </label>
								<div class="formElement">
									<input name="COMMENT_author" class="text" type="text" value="{USER_NAME}" style="width:240px;" />
								</div>
							</div>
							<div class="form_line">
								<label><vte:value select="{LNG_ADD_COMMENTS_MAIL}" />: </label>
								<div class="formElement">
									<input name="COMMENT_email" class="text" type="text" value="{USER_MAIL}" style="width:240px;" />
								</div>
							</div>
							<div class="form_line">
								<label><vte:value select="{LNG_ADD_COMMENTS_WWW}" />: </label>
								<div class="formElement">
									<input name="COMMENT_www" class="text" type="text" value="{USER_MAIL}" style="width:240px;" />
								</div>
							</div>
						</vte:if>
						<vte:if test="{VIVVO_COMMENTS_ENABLE_THREADED}">
							<div id="writing_reply" style="display:none">
								<vte:value select="{LNG_COMMENT_REPLYING_TO}" />: <span id="writing_reply_to"> </span> <a href="javascript:;" onclick="cancelReplyTo();"><vte:value select="{LNG_CANCEL}" /></a>
							</div>
						</vte:if>
						<vte:if test="{VIVVO_COMMENTS_ENABLE_BBCODE}">
							<vte:header type="script" href="{VIVVO_URL}js/vivvo_bbcbox.js" />
							<div class="bbcodebox">
								<div class="form_line">
									<label><vte:value select="{LNG_ADD_COMMENTS}" />:</label>
									<div class="formElement">
										<ul class="bbc_buttons">
											<li><a href="javascript:;" class="tag_bold"><img src="{VIVVO_THEME}img/bbc_bold.gif" alt="{LNG_COMMENTS_BBCODE_BOLD}" title="{LNG_COMMENTS_BBCODE_BOLD}" /></a></li>
											<li><a href="javascript:;" class="tag_italic"><img src="{VIVVO_THEME}img/bbc_italic.gif" alt="{LNG_COMMENTS_BBCODE_ITALIC}" title="{LNG_COMMENTS_BBCODE_ITALIC}" /></a></li>
											<li><a href="javascript:;" class="tag_underline"><img src="{VIVVO_THEME}img/bbc_underline.gif" alt="{LNG_COMMENTS_BBCODE_UNDERLINE}" title="{LNG_COMMENTS_BBCODE_UNDERLINE}" /></a></li>
											<li><a href="javascript:;" class="tag_quote"><img src="{VIVVO_THEME}img/bbc_quote.gif" alt="{LNG_COMMENTS_BBCODE_QUOTE}" title="{LNG_COMMENTS_BBCODE_QUOTE}" /></a></li>
											<vte:if test="{CURRENT_USER}">
												<li><a href="javascript:;" onclick="$(this).up('.bbcodebox').down('.box_link').toggle()"><img src="{VIVVO_THEME}img/bbc_link.gif" alt="{LNG_COMMENTS_BBCODE_INSERT_LINK}" title="{LNG_COMMENTS_BBCODE_INSERT_LINK}" /></a></li>
											</vte:if>
										</ul>
										<vte:if test="{CURRENT_USER}">
											<div class="box_link" style="display:none">
												<vte:value select="{LNG_COMMENTS_BBCODE_LINK_URL}" />: <input class="text tag_link_href" type="text" value="" /> <vte:value select="{LNG_COMMENTS_BBCODE_LINK_TEXT}" />: <input class="text tag_link_content" type="text" value="" /> <button type="button" class="comment_insert_button tag_link" onclick="$(this).up('.box_link').hide()"><vte:value select="{LNG_COMMENTS_BBCODE_INSERT_LINK}" /></button>
											</div>
										</vte:if>
										<textarea id="COMMENT_description" name="COMMENT_description" class="bbcodearea add_comment" rows="7" cols="63"> </textarea>
									</div>
								</div>
							</div>
							<vte:else>
								<div class="form_line">
									<label><vte:value select="{LNG_ADD_COMMENTS}" />:</label>
									<div class="formElement">
										<textarea class="add_comment" name="COMMENT_description" rows="7" cols="63" onfocus="this.value='';this.onfocus = null;"> </textarea>
									</div>
								</div>
							</vte:else>
						</vte:if>
						<vte:if test="{VIVVO_COMMENTS_CAPTHA}">
							<vte:value select="{article.generate_captcha}" />
							<h4><vte:value select="{LNG_ADD_COMMENT_CAPTCHA}" /></h4>
							<div class="form_line">
								<label><vte:value select="{LNG_IMAGE}" />: </label>
								<div class="formElement">
									<img src="{VIVVO_PROXY_URL}imagecode.html?article_id={article.get_id}" align="middle" alt="" />
								</div>
							</div>
							<div class="form_line">
								<label><vte:value select="{LNG_CODE}" />: </label>
								<div class="formElement">
									<input name="COMMENT_captcha" class="text" type="text" maxlength="8"  />
								</div>
							</div>
						</vte:if>
						<div class="form_line">
							<label><!-- --></label>
							<div class="formElement">
								<input type="submit" value="{LNG_ADD_COMMENTS_BUTTON}" />
							</div>
						</div>
					</div>
				</form>
			</div>
		</vte:else>
	</vte:if>
</vte:if>
