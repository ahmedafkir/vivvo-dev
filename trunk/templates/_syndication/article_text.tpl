<vte:template><vte:value select="{VIVVO_WEBSITE_TITLE}" />: <vte:value select="{PAGE_TITLE}" />

<vte:value select="{article.get_title|convert2text}" />
================================================================================
<vte:value select="{article.get_author_name}" /> <vte:value select="{LNG_ARTICLE_POSTED_ON}" /> <vte:value select="{article.get_created}" />
<vte:if test="{article.get_abstract}">
<vte:value select="{article.get_abstract|convert2text}" /></vte:if>

<vte:value select="{article.get_body|convert2text}" />
</vte:template>