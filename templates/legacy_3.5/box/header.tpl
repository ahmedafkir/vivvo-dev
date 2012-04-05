<div id="header">
	<div id="top_links">
        <a href="{VIVVO_URL}"><vte:value select="{LNG_GO_HOME}" /></a> |
        <a onclick="this.style.behavior='url(#default#homepage)';this.setHomePage('{VIVVO_URL}');" href="#"><vte:value select="{LNG_SET_HOMEPAGE}" /></a> |
        <a href="javascript:bookmarksite('{TITLE}',document.URL);"><vte:value select="{LNG_ADD_FAVORITES}" /></a> 
        <vte:if test="{VIVVO_MODULES_FEED}">
            | 
            <vte:if test="{CURRENT_URL} = {VIVVO_URL}">
                <a href="{VIVVO_PROXY_URL}feed/index.rss">Rss</a> / <a href="{VIVVO_PROXY_URL}feed/index.atom">Atom</a>
                <vte:else>
                    <a href="{CURRENT_URL|switch_format:'rss'}">Rss</a> / <a href="{CURRENT_URL|switch_format:'atom'}">Atom</a>
                </vte:else>
            </vte:if>
        </vte:if>
        <vte:if test="{VIVVO_MODULES_PLAINTEXT}">
            | 
            <vte:if test="{CURRENT_URL} = {VIVVO_URL}">
                <a href="{VIVVO_PROXY_URL}feed/index.txt"><vte:value select="{LNG_PLAIN_TEXT}" /></a>
                <vte:else>
                    <a href="{CURRENT_URL|switch_format:'txt'}"><vte:value select="{LNG_PLAIN_TEXT}" /></a>
                </vte:else>
            </vte:if>
        </vte:if>
        <vte:if test="{VIVVO_MODULES_ARCHIVE_VIEW}">
            | 
            <a href="{VIVVO_PROXY_URL}archive"><vte:value select="{LNG_ARCHIVE}" /></a>
        </vte:if>
        <vte:if test="{CURRENT_USER.can|'ACCESS_ADMIN'}">
            | <a href="{VIVVO_URL}{VIVVO_FS_ADMIN_DIR}index.php"><vte:value select="{LNG_SITE_ADMINISTRATION}" /></a>
        </vte:if>
    </div>
	<div class="header_image"><img src="{VIVVO_THEME}img/t4_header.gif" alt="header" /></div>
	<vte:include file="{VIVVO_TEMPLATE_DIR}box/pages.tpl" />
	<vte:include file="{VIVVO_TEMPLATE_DIR}box/search.tpl" />
</div>