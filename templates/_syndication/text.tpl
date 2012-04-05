<vte:template><vte:value select="{VIVVO_WEBSITE_TITLE}" />: <vte:value select="{PAGE_TITLE}" />
<vte:box module="box_article_list" params="{content_params}"><vte:template><vte:if test="{article_list}">
<vte:foreach item = "article" from="{article_list}">
<vte:value select="{article.get_title|convert2text}" />
================================================================================
<vte:value select="{article.get_author_name}" /> <vte:value select="{LNG_ARTICLE_POSTED_ON}" /> <vte:value select="{article.get_created}" />

<vte:value select="{article.get_summary|convert2text}" />
</vte:foreach></vte:if></vte:template></vte:box></vte:template>