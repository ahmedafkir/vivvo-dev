<vte:template>
	<div id="box_pagination">
		<span class="pagination">	
			<vte:if test="{previous_page_group} != ''">
				<a href="{previous_page_group}" class="page_groups"><img src="{VIVVO_THEME}img/pagination_first.gif" alt="first" /></a>
			</vte:if>
			
			<vte:if test="{previous_page} != ''">
				<a href="{previous_page}" class="page_groups"><img src="{VIVVO_THEME}img/pagination_back.gif" alt="back" /></a>
			</vte:if>
		 
			<vte:foreach item = "page" from = "{page_list}">
				<vte:if test="{current_page} != {page[number]}">
					<a href="{page[href]}"><vte:value select="{page[number]}" /></a>
					<vte:else>
						<span class="page_active"><vte:value select="{page[number]}" /></span>
					</vte:else>
				</vte:if>
			</vte:foreach>
			
			<vte:if test="{next_page} != ''">
				<a href="{next_page}" class="page_groups"><img src="{VIVVO_THEME}img/pagination_next.gif" alt="next" /></a>
			</vte:if>
		
			<vte:if test="{next_page_group} != ''">
				<a href="{next_page_group}" class="page_groups"><img src="{VIVVO_THEME}img/pagination_last.gif" alt="last" /></a>
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