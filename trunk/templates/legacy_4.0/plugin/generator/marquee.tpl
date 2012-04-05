<vte:template>
	<marquee id="ticker" loop="" onmouseover="this.stop();" onmouseout="this.start();" scrollamount="1" scrolldelay="15" truespeed="truespeed" behavior="scroll" direction="left">
		<vte:foreach item = "article" key="index" from = "{article_list}">
			<span class="ticker_category"><a href=""><vte:value select="{article.get_category_name}" /></a> | </span>
			<a href="{article.get_href}"><vte:value select="{article.get_title}" /></a>
		</vte:foreach>
	</marquee>
<vte:template>