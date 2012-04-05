<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<body id="layout_default">
		<div id="container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}box/header.tpl" />
			<div id="content">
				<div id="dynamic_box_center">
					<div id="box_center_holder_home">
						<vte:if test="{VIVVO_MODULES_HEADLINES_DISPLAY}">
							<vte:box module="box_article_list">
								<vte:params>
									<vte:param name="search_topic_id" value="1" />
                                    <vte:param name="search_all_tag_ids" value="1,2" />
                                    <vte:param name="search_sort_by" value="order_num" />
                                    <vte:param name="search_order" value="descending" />
                                    <vte:param name="search_limit" value="5" />
                                    <vte:param name="cache" value="1" />
                                    <vte:param name="add_to_printed" value="true" />
                                    <vte:param name="exclude_printed" value="true" />
								</vte:params>
								<vte:template>
									<div id="headlines" class="box_headline">
                                        <vte:foreach item = "article" from = "{article_list}" key="index">
                                        	<div class="headline">
                                                <vte:if test="{article.image}">
                                                    <div class="headline_image">
                                                        <a href="{article.get_href}"><img src="{VIVVO_URL}thumbnail.php?file={article.get_image}&amp;size=summary_medium" align="left" alt="image" /></a>
                                                    </div>
                                                </vte:if>
                                                <h1>
                                                    <a href="{article.get_href}"><vte:value select="{article.get_title}" /></a>
                                                </h1>
                                                <div class="headline_body"><vte:value select="{article.get_summary}" /></div>
                                                <div class="headline_link"><a href="mobile/{article.get_href}"><vte:value select="{LNG_FULL_STORY}" /></a></div>
                                                <div class="clearer"> </div>
                                            </div>
                                        </vte:foreach>                                        
									</div>
								</vte:template>
							</vte:box>
						</vte:if>
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/tag_cloud.tpl" />
						<!-- Main news section -->
						<vte:box module="box_sections">
							<vte:params>
								<vte:param name="cache" value="1" />
								<vte:param name="search_ids" value="{VIVVO_MODULES_MORE_NEWS_CATEGORIES}" />
							</vte:params>
							<vte:template>
								<div id="category_news_box" >
									<div class="main_news_box_holder">
										<vte:for from="{categories}" step="2" key="category_index">
											<div class="main_news_box_row">
												<vte:foreach item="category" from="{categories}" loop="2" start="{category_index}">
													<div id="box_more_category_list_{category.get_id}" class="main_news_category">
														<div class="box_title">
															<h4>
																<a href="{category.get_href}"><vte:value select="{category.get_category_name}" /></a>
															</h4>
															<vte:foreach item="sub_category" from="{category.subcategories}" key="index">
																<span>
																	<vte:if test="{index} != 1"> | </vte:if>
																	<a href="{sub_category.get_href}"><vte:value select="{sub_category.get_category_name}" /></a>
																</span>
															</vte:foreach>
														</div>
														<div class="box_body">
															<vte:box module="box_article_list">
																<vte:params>
																	<vte:param name="search_sort_by" value="created" />
																	<vte:param name="search_limit" value="{VIVVO_MODULES_MORE_NEWS_ARTICLE_NUMBER}" />
																	<vte:param name="search_cid" value="{category.id}" />
																	<vte:param name="add_to_printed" value="true" />
																	<vte:param name="exclude_printed" value="true" />
																</vte:params>
																<vte:template>
																	<vte:foreach item="article" from="{article_list}" start="1" loop="1">
																		<vte:include file="{VIVVO_TEMPLATE_DIR}summary/morenews_short.tpl" />
																	</vte:foreach>
																	<ul>
																		<vte:foreach item="article" from="{article_list}" start="2">
																			<li><a href="{article.get_href}"><vte:value select="{article.title}" /><vte:if test="{article.get_link}"> <img src="{VIVVO_THEME}img/external.png" alt="{LNG_VISIT_WEBSITE}"/></vte:if></a></li>
																		</vte:foreach>
																	</ul>
																</vte:template>
															</vte:box>
														</div>
													</div>
												</vte:foreach>
											</div>
										</vte:for>
									</div>
								</div>
							</vte:template>
						</vte:box>
					</div>
				</div>
			</div>
			<div id="footer">
				<vte:include file="{VIVVO_TEMPLATE_DIR}box/footer.tpl" />
			</div>
		</div>
	</body>
</html>