<vte:if test="{VIVVO_MODULES_SECTIONS}">
	<div id="box_sections" class="box">
		<div class="box_title"><span><vte:value select="{LNG_SECTIONS}" /></span></div>
		<div class="box_body">
			<vte:box module="box_sections">
				<vte:params>
					<vte:param name="id" value="0" />
				</vte:params>
				<vte:template>
					<ul>
						<vte:foreach item = "category" from = "{categories}">
							<vte:if test="{category.view_subcat}">
								<li>
									<a>
										<vte:attribute name="href">
											<vte:if test="{category.redirect}">
												<vte:value select="{category.redirect}" />
												<vte:else>
													<vte:value select="{category.get_href}" />
												</vte:else>
											</vte:if>
										</vte:attribute>
										<vte:value select="{category.category_name}" />
									</a>
									<vte:if test="{category.subcategories}">
										<vte:box module="box_sections">
											<vte:params>
												<vte:param name="id" value="{category.id}" />
												<vte:param name="prefix" value="" />
											</vte:params>
											<vte:template>
												<ul>
													<vte:foreach item = "category" from = "{categories}">
														<vte:if test="{category.view_subcat}">
															<li>
																<a>
																	<vte:attribute name="href">
																		<vte:if test="{category.redirect}">
																			<vte:value select="{category.redirect}" />
																			<vte:else>
																				<vte:value select="{category.get_href}" />
																			</vte:else>
																		</vte:if>
																	</vte:attribute>
																	&#8250;<vte:value select="{prefix}" />
																	<vte:value select="{category.category_name}" />
																</a>
																<vte:if test="{category.subcategories}">
																	<vte:load module="box_sections" id="{category.id}" template_string="{template_string}" prefix="&#8250;{prefix}" />
																</vte:if>
															</li>
														</vte:if>
													</vte:foreach>
												</ul>
											</vte:template>
										</vte:box>
									</vte:if>
								</li>
							</vte:if>
						</vte:foreach>
					</ul>
				</vte:template>
			</vte:box>
        </div>
	</div>
</vte:if>