<vte:template>
	<vte:if test="{VIVVO_MODULES_SEARCH}">
		<div id="box_search" class="search">
			<form action="{VIVVO_PROXY_URL}search.html" method="post" name="search">
				<input type="hidden" name="search_do_advanced" />
				<input value="" class="text" type="text" name="search_query" id="search_query" style="width: 200px;" />
				<button type="submit" name="search" value="0"><vte:value select="{LNG_SEARCH_BUTTON}" /></button>
				<a href="{VIVVO_PROXY_URL}search.html"> <vte:value select="{LNG_SEARCH_ADVANCED}" /> &raquo;</a>
			</form>
		</div>
	</vte:if>
</vte:template>