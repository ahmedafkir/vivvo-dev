<div class="footer">
	<vte:box module="box_sections">
		<vte:params>
			<vte:param name="id" value="{VIVVO_ROOT_CATEGORY}" />
		</vte:params>
		<vte:template>
			<ul>
				<vte:foreach item="category" from="{categories}">
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
						</li>
					</vte:if>
				</vte:foreach>
			</ul>
		</vte:template>
	</vte:box>
</div>