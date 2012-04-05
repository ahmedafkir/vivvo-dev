<vte:if test="{VIVVO_COMMENTS_ENABLE}">
	<vte:box module="box_comments">
		<vte:params>
			<vte:param name="search_limit" value="5" />
			<vte:param name="search_sort_by" value="created" />
			<vte:param name="search_order" value="descending" />
			<vte:param name="cache" value="1" />
		</vte:params>
		<vte:template>
			<div id="latest_comments" class="box">
				<div class="box_title_holder"><div class="box_title">Latest comments</div></div>
				<div class="box_body">
					<div class="box_content">
						<vte:if test="{comment_list}">
							<div class="comments">
								<ul>
									<vte:foreach item = "comment" from = "{comment_list}">
										<li>
											<div class="comment_header"><vte:value select="{LNG_ARTICLE_COMM_POSTED}" /> <strong><vte:value select="{comment.get_author}" /></strong>, <vte:value select="{comment.get_create_dt}" /></div>
										  <div class="comment_body">
											<img src="{comment.get_gravatar|24}" alt="avatar" />
											<vte:if test="{comment.get_www}">
												<a href="http://{comment.get_www}" target="_blank"><vte:value select="{comment.get_author}" /></a>
												<vte:else>
													<vte:value select="{comment.get_author}" />
												</vte:else>
											</vte:if>
											<vte:value select="{comment.get_summary}" />
											</div>
										</li>
									</vte:foreach>
								</ul>
							</div>
							<vte:else>
								<div class="info_message">No items matching selected criteria.</div> 
							</vte:else>
						</vte:if>
					</div>
				</div>
			</div>
		</vte:template>
	</vte:box>
</vte:if>