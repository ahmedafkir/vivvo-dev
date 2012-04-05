<vte:template>
	<div class="box">
		<div class="box_title_holder"><div class="box_title"><vte:value select="{navigation.title}" /></div></div>
		<div class="box_body">
			<div class="box_content">
				<vte:box module="box_navigation_nodes">
					<vte:params>
						<vte:param name="search_navigation_id" value="{navigation.id}" />
					</vte:params>
					<vte:template>
						<ul>
							<vte:foreach item="node" from = "{nodes}">
								<li>
									<a href="{node.href}">
										<vte:value select="{node.title}" />
									</a>
									<vte:if test="{node.subnodes}">
										<vte:load module="box_navigation_nodes" root="{node}" template_string="{template_string}" />
									</vte:if>
								</li>
							</vte:foreach>
						</ul>
					</vte:template>
				</vte:box>
			</div>
		</div>
	</div>
</vte:template>