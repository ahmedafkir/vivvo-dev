-- $Id: data.sql 5447 2010-06-04 11:00:06Z krcko $

/*@QUERY {"title":"Registering system services."}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('comment', NULL, 'lib/vivvo/service/Comments.action.php', 'vivvo_action', '1', NULL),
('tag', NULL, 'lib/vivvo/service/Tags.action.php', 'vivvo_action', '1', NULL),
('vivvoCore', NULL, 'lib/vivvo/service/vivvo_core.action.php', 'vivvo_action', '1', NULL),
('article', NULL, 'lib/vivvo/service/Articles.action.php', 'vivvo_action', '1', NULL),
('category', NULL, 'lib/vivvo/service/Categories.action.php', 'vivvo_action', '1', NULL),
('user', NULL, 'lib/vivvo/service/Users.action.php', 'vivvo_action', '1', NULL),
('file', NULL, 'lib/vivvo/service/file.action.php', 'vivvo_action', '1', NULL),
('login', NULL, 'lib/vivvo/service/login.action.php', 'vivvo_action', '1', NULL),
('UserFilter', NULL, 'lib/vivvo/service/UserFilters.action.php', 'vivvo_action', '1', NULL),
('asset', NULL, 'lib/vivvo/service/assets.action.php', 'vivvo_action', '1', NULL);

/*@QUERY {"title":"Registering box modules."}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('box_sections', 'class_name', 'box_sections', 'modules', '1', NULL),
('box_sections', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_comments', 'class_name', 'box_comments', 'modules', '1', NULL),
('box_comments', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_article_list', 'class_name', 'box_article_list', 'modules', '1', NULL),
('box_article_list', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_users', 'class_name', 'box_users', 'modules', '1', NULL),
('box_users', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_tags', 'class_name', 'box_tags', 'modules', '1', NULL),
('box_tags', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_pagination', 'class_name', 'box_pagination', 'modules', '1', NULL),
('box_pagination', 'file', 'lib/vivvo/framework/pagination.class.php', 'modules', '1', NULL),
('box_files', 'class_name', 'box_files', 'modules', '1', NULL),
('box_files', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_user_filters', 'class_name', 'box_user_filters', 'modules', '1', NULL),
('box_user_filters', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_timeline', 'class_name', 'box_timeline', 'modules', '1', NULL),
('box_timeline', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_author_timeline', 'class_name', 'box_author_timeline', 'modules', '1', NULL),
('box_author_timeline', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_calendar', 'class_name', 'box_calendar', 'modules', '1', NULL),
('box_calendar', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_paged_files', 'class_name', 'box_paged_files', 'modules', '1', NULL),
('box_paged_files', 'file', 'lib/vivvo/box/box_paged_files.php', 'modules', '1', NULL),
('box_generic', 'class_name', 'box_generic', 'modules', '1', NULL),
('box_generic', 'file', 'lib/vivvo/framework/vivvo_system_box.php', 'modules', '1', NULL),
('box_xml_grabber', 'class_name', 'box_xml_grabber', 'modules', '1', NULL),
('box_xml_grabber', 'file', 'lib/vivvo/framework/vivvo_system_box.php', 'modules', '1', NULL),
('box_feed', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_feed', 'class_name', 'box_feed', 'modules', '1', NULL),
('box_asset_files', 'class_name', 'box_asset_files', 'modules', '1', NULL),
('box_asset_files', 'file', 'lib/vivvo/box/box_asset_files.php', 'modules', '1', NULL),
('box_tags_groups', 'class_name', 'box_tags_groups', 'modules', '1', NULL),
('box_tags_groups', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_topics', 'class_name', 'box_tags_groups', 'modules', '1', NULL),
('box_topics', 'file', 'lib/vivvo/box/vivvo_box.php', 'modules', '1', NULL),
('box_article_revisions', 'class_name', 'box_article_revisions', 'modules', '1', NULL),
('box_article_revisions', 'file', 'lib/vivvo/box/box_article_revisions.php', 'modules', '1', NULL),
('box_analytics', 'class_name', 'box_analytics', 'modules', '1', NULL),
('box_analytics', 'file', 'lib/vivvo/framework/vivvo_ga.php', 'modules', '1', NULL);

/*@QUERY {"title":"Setting up system configuration."}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('VIVVO_FRIENDY_URL', NULL, '/*{@MOD_RW_CHECK}*/', NULL, '1', '0|1'),
('VIVVO_URL_FORMAT', NULL, '1', NULL, '1', '\\d'),
('VIVVO_ALLOWED_IP_ADDRESSES', NULL, NULL, NULL, '1', NULL),
('VIVVO_DATE_FORMAT', NULL, 'd/m/Y H:i:s', NULL, '1', NULL),
('VIVVO_USER_SOURCE', NULL, 'vivvo@localhost', NULL, '1', NULL),
('VIVVO_GROUP_DEFAULT_GUEST', NULL, '7', NULL, '1', NULL),
('VIVVO_GROUP_DEFAULT_MEMBER', NULL, '4', NULL, '1', NULL),
('VIVVO_MODULES_FEATURED_AUTHOR_GROUPS', NULL, '1,2,3,6', NULL, '1', NULL),
('VIVVO_MODULES_FRONTEND_ADMIN', NULL, '1', NULL, '1', NULL),
('VIVVO_MEMBER_ACCESS', NULL, '0', NULL, '1', '0|1'),
('VIVVO_CATEGORY_PUBLIC_ACCESS', NULL, '0', NULL, '1', '0|1|2'),
('VIVVO_CATEGORIES_SHOW_SUBCATEGORIES', NULL, '1', NULL, '1', '0|1'),
('VIVVO_ALLOWED_EXTENSIONS', NULL, 'txt,pdf,jpg,png,gif,jpeg,wmv,avi,mp3,swf,flv,mp4,mov', NULL, '1', '[a-zA-Z0-9]{2,4}(,[a-zA-Z0-9]{2,4})*'),
('VIVVO_ADMIN_ADVANCED', NULL, '1', NULL, '1', '0|1'),
('VIVVO_CACHE_ENABLE', NULL, '0', NULL, '1', '0|1|2|3'),
('VIVVO_CACHE_TIME', NULL, '30', NULL, '1', '\\d*'),
('VIVVO_CLOSE_SITE', NULL, '0', NULL, '1', '0|1'),
('VIVVO_CLOSE_SITE_REASON', NULL, 'Performing site maintenance.', NULL, '1', NULL),
('VIVVO_TOS_CONTENT', NULL, '', NULL, '1', NULL),
('VIVVO_TOS', NULL, '1', NULL, '1', '0|1'),
('VIVVO_GENERAL_META_KEYWORDS', NULL, 'Enter some meta keywords', NULL, '1', NULL),
('VIVVO_GENERAL_META_DESCRIPTION', NULL, 'Enter here some meta description', NULL, '1', NULL),
('VIVVO_GENERAL_TIME_ZONE_FORMAT', NULL, '/*{@SYSTEM_DEFAULT_TIMEZONE}*/', NULL, '1', ''),
('VIVVO_GENERAL_BEGINING_OF_WEEK', NULL, '1', NULL, '1', '0|1'),
('VIVVO_GENERAL_WEBSITE_LOGO', NULL, '', NULL, '1', NULL),
('VIVVO_EXPAND_ADVANCED_OPTIONS', NULL, '0', NULL, '1', '0|1'),
('VIVVO_DEFAULT_LANG', NULL, '/*{@VIVVO_INSTALLER_LANG}*/', NULL, '1', NULL),
('VIVVO_DEFAULT_THEME', NULL, 'default', NULL, '1', NULL),
('VIVVO_DEFAULT_TEMPLATE_DIR', NULL, 'xhtml', NULL, '1', NULL),
('VIVVO_TAG_LAYOUT', NULL, 'default.tpl', NULL, '1', NULL),
('VIVVO_ARCHIVE_LAYOUT', NULL, 'default.tpl', NULL, '1', NULL),
('VIVVO_SEARCH_RESULT_LAYOUT', NULL, 'default.tpl', NULL, '1', NULL),
('VIVVO_CATEGORY_LAYOUT', NULL, 'default.tpl', NULL, '1', NULL),
('VIVVO_ARTICLE_LAYOUT', NULL, 'default.tpl', NULL, '1', NULL),
('VIVVO_HOMEPAGE_LAYOUT', NULL, 'default.tpl', NULL, '1', NULL),
('VIVVO_REGISTRATION_CAPTCHA', NULL, '0', NULL, '1', '0|1');

/*@QUERY {"title":"Setting up default article preferences"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('VIVVO_HOMEPAGE_ARTICLE_LIST_NUMBER', NULL, '15', NULL, '1', '[1-9]\\d*'),
('VIVVO_HOMEPAGE_ARTICLE_LIST_TITLE', NULL, 'Latest', NULL, '1', NULL),
('VIVVO_HOMEPAGE_ARTICLE_LIST_ORDER', NULL, 'order_num', NULL, '1', NULL),
('VIVVO_HOMEPAGE_ARTICLE_LIST_CATEGORIES', NULL, '0', NULL, '1', NULL),
('VIVVO_HOMEPAGE_ARTICLE_LIST_COLUMNS', NULL, '1', NULL, '1', '[1-9]'),
('VIVVO_ARTICLE_AUTO_DELETE', NULL, '15', NULL, '1', NULL),
('VIVVO_ARTICLE_AUTO_ARCHIVE', NULL, '10', NULL, '1', NULL),
('VIVVO_ARTICLE_AUTO_ARCHIVE_ITEMS', NULL, 0, NULL, 1, NULL),
('VIVVO_ARTICLE_AUTO_DELETE_ITEMS', NULL, 0, NULL, 1, NULL),
('VIVVO_ARTICLE_AUTO_RELATED', NULL, '10', NULL, '1', NULL),
('VIVVO_ARTICLE_SHOW_AUTHOR_INFO', NULL, '1', NULL, '1', '0|1'),
('VIVVO_ARTICLE_RATING_MEMBER_ONLY', NULL, '1', NULL, '1', '0|1'),
('VIVVO_ARTICLE_SHOW_RATING', NULL, '1', NULL, '1', '0|1'),
('VIVVO_ARTICLE_SHOW_DATE', NULL, '1', NULL, '1', '0|1'),
('VIVVO_ARTICLE_SHOW_AUTHOR', NULL, '1', NULL, '1', '0|1'),
('VIVVO_ARTICLE_SHOW_RELATED', NULL, '1', NULL, '1', '0|1'),
('VIVVO_ARTICLE_RELATED_CATEGORY', NULL, '3', NULL, '1', '0|1|2|3|4|5'),
('VIVVO_ARTICLE_RELATED_TAGS', NULL, '4', NULL, '1', '0|1|2|3|4|5'),
('VIVVO_ARTICLE_RELATED_TOPIC', NULL, '2', NULL, '1', '0|1|2|3|4|5'),
('VIVVO_REVISIONS_AUTODRAFT_TIME', NULL, '10', NULL, '1', '\\d+'),
('VIVVO_REVISIONS_KEEP', NULL, '10', NULL, '1', '\\d+'),
('VIVVO_REVISIONS_KEEP_COPIES', NULL, '1', NULL, '1', '1|0'),
('VIVVO_ARTICLE_LARGE_IMAGE_WIDTH', NULL, '600', NULL, 0, '[1-9]\\d{0,3}'),
('VIVVO_ARTICLE_LARGE_IMAGE_HEIGHT', NULL, '600', NULL, 0, '[1-9]\\d{0,3}'),
('VIVVO_ARTICLE_MEDIUM_IMAGE_WIDTH', NULL, '360', NULL, 0, '[1-9]\\d{0,3}'),
('VIVVO_ARTICLE_MEDIUM_IMAGE_HEIGHT', NULL, '360', NULL, 0, '[1-9]\\d{0,3}'),
('VIVVO_ARTICLE_SMALL_IMAGE_HEIGHT', NULL, '200', NULL, 0, '[1-9]\\d{0,3}'),
('VIVVO_ARTICLE_SMALL_IMAGE_WIDTH', NULL, '200', NULL, '1', '[1-9]\\d{0,3}'),
('VIVVO_SUMMARY_MEDIUM_IMAGE_WIDTH', NULL, '100', NULL, 0, '[1-9]\\d{0,3}'),
('VIVVO_SUMMARY_MEDIUM_IMAGE_HEIGHT', NULL, '100', NULL, 0, '[1-9]\\d{0,3}'),
('VIVVO_SUMMARY_SMALL_IMAGE_WIDTH', NULL, '50', NULL, 0, '[1-9]\\d{0,3}'),
('VIVVO_SUMMARY_SMALL_IMAGE_HEIGHT', NULL, '50', NULL, 0, '[1-9]\\d{0,3}'),
('VIVVO_SUMMARY_LARGE_IMAGE_WIDTH', NULL, '150', NULL, '1', '[1-9]\\d{0,3}'),
('VIVVO_SUMMARY_LARGE_IMAGE_HEIGHT', NULL, '150', NULL, '1', '[1-9]\\d{0,3}'),
('VIVVO_THUMBVIEW_IMAGE_WIDTH', NULL, '100', NULL, '1', NULL),
('VIVVO_THUMBVIEW_IMAGE_HEIGHT', NULL, '90', NULL, '1', NULL),
('VIVVO_COMMENTS_BOX_TEMPLATE', NULL, 'comments.tpl', NULL, '1', NULL),
('VIVVO_COMMENTS_CAPTHA', NULL, '1', NULL, '1', '0|1'),
('VIVVO_COMMENTS_FLOOD_PROTECTION', NULL, '0', NULL, '1', '\\d*'),
('VIVVO_COMMENTS_ENABLE', NULL, '1', NULL, 0, '0|1'),
('VIVVO_COMMENTS_BAD_IP', NULL, '', NULL, '1', NULL),
('VIVVO_COMMENTS_MEMBER_ONLY', NULL, '0', NULL, '1', '0|1'),
('VIVVO_COMMENTS_MODERATE', NULL, '0', NULL, '1', '0|1'),
('VIVVO_COMMENTS_REPORT_INAPPROPRIATE', NULL, '1', NULL, '1', '0|1'),
('VIVVO_COMMENTS_BAD_WORDS', NULL, '', NULL, '1', NULL),
('VIVVO_COMMENTS_IP_FITER', NULL, '', NULL, '1', NULL),
('VIVVO_COMMENTS_NUM_PER_PAGE', NULL, '10', NULL, '1', '\\d+'),
('VIVVO_COMMENTS_ENABLE_BBCODE', NULL, '1', NULL, '1', '1|0'),
('VIVVO_COMMENTS_ENABLE_THREADED', NULL, '1', NULL, '1', '1|0'),
('VIVVO_COMMENTS_ENABLE_GRAVATAR', NULL, '0', NULL, '1', '1|0'),
('VIVVO_COMMENTS_ORDER', NULL, 'ascending', NULL, '1', '(a|de)scending');

/*@QUERY {"title":"Setting up default module preferences"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('VIVVO_MODULES_MOST_POPULAR', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_MOST_COMMENTED', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_MORE_NEWS_CATEGORIES', NULL, '', NULL, '1', NULL),
('VIVVO_MODULES_TICKER', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_TICKER_CATEGORIES', NULL, '', NULL, '1', NULL),
('VIVVO_MODULES_TICKER_ORDER', NULL, 'created', NULL, '1', NULL),
('VIVVO_MODULES_TICKER_DISPLAY_HEADLINES', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_TICKER_NUMBER', NULL, '10', NULL, '1', '[1-9]\\d*'),
('VIVVO_MODULES_MOST_POPULAR_COUNTER', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_MOST_EMAILED', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_ARCHIVE_VIEW', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_ARCHIVE_CALENDAR', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_FEATURED_AUTHOR_PAGE', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_LAST_COMMENTED', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_HEADLINES_ROTATION_TIME', NULL, '5', NULL, 0, '[1-9]\\d{0,3}'),
('VIVVO_MODULES_HEADLINES_DISPLAY', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_FEATURED_AUTHOR', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_FEATURED_AUTHOR_ADMIN', NULL, '0', NULL, '1', '0|1'),
('VIVVO_MODULES_TOP_RATED', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_DHTML_SECTIONS', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_SECTIONS', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_MORE_NEWS_COLUMN_NUMBER', NULL, '2', NULL, '1', '[1-9]'),
('VIVVO_MODULES_MORE_NEWS_ARTICLE_NUMBER', NULL, '4', NULL, '1', '[1-9]\\d*'),
('VIVVO_MODULES_CATEGORY_INCLUDE_FEED', NULL, '0', NULL, '1', NULL),
('VIVVO_MODULES_FEED', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_SEARCH', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_TAGS', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_USERS', NULL, '1', NULL, '1', '0|1'),
('VIVVO_MODULES_PLAINTEXT', NULL, '1', NULL, '1', '0|1');

/*@QUERY {"title":"Setting up email preferences"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('VIVVO_EMAIL_ENABLE', NULL, '1', NULL, '1', '0|1'),
('VIVVO_EMAIL_FLOOD_CHECK', NULL, '20', NULL, '1', '[1-9]\\d*'),
('VIVVO_EMAIL_SMTP_PHP', NULL, '1', NULL, '1', '0|1'),
('VIVVO_EMAIL_SMTP_HOST', NULL, '', NULL, '1', NULL),
('VIVVO_EMAIL_SMTP_PORT', NULL, '25', NULL, '1', '[1-9]\\d*'),
('VIVVO_EMAIL_SMTP_USERNAME', NULL, NULL, NULL, '1', NULL),
('VIVVO_EMAIL_SMTP_PASSWORD', NULL, NULL, NULL, '1', NULL),
('VIVVO_EMAIL_SEND_TYPE', NULL, '0', NULL, '1', '0|1'),
('VIVVO_EMAIL_REGISTER_SUBJECT', NULL, 'Registration', NULL, '1', NULL),
('VIVVO_EMAIL_FORGOT_BODY', NULL, '&lt;vte:template&gt;\r\n\r\nDear &lt;vte:value select=&quot;{user.get_name}&quot; /&gt;,\r\n\r\nNew password was requested for &lt;vte:value select=&quot;{VIVVO_URL}&quot; /&gt;.\r\n\r\nClick on this link to change your password \r\n\r\n&lt;vte:value select=&quot;{activation_url}&quot; /&gt;\r\n\r\nBest regards,\r\n&lt;vte:value select=&quot;{VIVVO_WEBSITE_TITLE}&quot; /&gt;\r\n&lt;vte:value select=&quot;{VIVVO_URL}&quot; /&gt;\r\n&lt;/vte:template&gt;', NULL, '1', NULL),
('VIVVO_EMAIL_TO_A_FRIEND_SUBJECT', NULL, 'Email to a friend', NULL, '1', NULL),
('VIVVO_EMAIL_REGISTER_TEMPLATE', NULL, '&lt;vte:template&gt;\r\nDear &lt;vte:value select=&quot;{new_user}&quot; /&gt;,\r\n\r\nThank you for signing up! Click or copy and paste this URL to your browser to activate your account:\r\n\r\n&lt;vte:value select=&quot;{activation_url}&quot; /&gt;\r\n\r\nPlease note that your activation code is NOT your password.\r\nThank you for using our service\r\n\r\nBest regards,\r\n&lt;vte:value select=&quot;{VIVVO_WEBSITE_TITLE}&quot; /&gt;\r\n&lt;vte:value select=&quot;{VIVVO_URL}&quot; /&gt;\r\n\r\n&lt;/vte:template&gt;', NULL, '1', NULL),
('VIVVO_EMAIL_TO_A_FRIEND_BODY', NULL, '&lt;vte:template&gt;\r\n&lt;vte:value select=&quot;{article.get_title|convert2text}&quot; /&gt;\r\n\r\n&lt;vte:value select=&quot;{article.get_summary|convert2text}&quot; /&gt;\r\n\r\nFollowing link &lt;vte:value select=&quot;{article.get_absolute_href}&quot; /&gt; was sent to you by &lt;vte:value select=&quot;{user_email_address}&quot; /&gt;\r\n\r\nWith this message:\r\n\r\n&lt;vte:value select=&quot;{message}&quot; /&gt;\r\n&lt;/vte:template&gt;', NULL, '1', NULL),
('VIVVO_EMAIL_FORGOT_SUBJECT', NULL, 'New password request', NULL, '1', NULL),
('VIVVO_EMAIL_SEND_CC', NULL, '', NULL, 1, '([a-zA-Z0-9\\._%\\+\\-]+@[a-zA-Z0-9\\.\\-_]+\\.[a-zA-Z]{2,4})?'),
('VIVVO_EMAIL_SEND_BCC', NULL, '', NULL, 1, '([a-zA-Z0-9\\._%\\+\\-]+@[a-zA-Z0-9\\.\\-_]+\\.[a-zA-Z]{2,4})?');

/*@QUERY {"title":"Installing url handlers"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('article', 'content_handler_function', 'article_content_handler', 'url_modules', '1', NULL),
('article', 'url_handler_function', 'article_url_handler', 'url_modules', '1', NULL),
('article', 'file', 'lib/vivvo/url_handlers/article.php', 'url_modules', '1', NULL),
('article1', 'url_handler_function', 'article1_url_handler', 'url_modules', '1', NULL),
('article1', 'file', 'lib/vivvo/url_handlers/article1.php', 'url_modules', '1', NULL),
('article2', 'url_handler_function', 'article2_url_handler', 'url_modules', '1', NULL),
('article2', 'file', 'lib/vivvo/url_handlers/article2.php', 'url_modules', '1', NULL),
('permalink', 'url_handler_function', 'article2_url_handler', 'url_modules', '1', NULL),
('permalink', 'file', 'lib/vivvo/url_handlers/article2.php', 'url_modules', '1', NULL),
('category', 'content_handler_function', 'category_content_handler', 'url_modules', '1', NULL),
('category', 'url_handler_function', 'category_url_handler', 'url_modules', '1', NULL),
('category', 'file', 'lib/vivvo/url_handlers/category.php', 'url_modules', '1', NULL),
('opensearch', 'file', 'lib/vivvo/url_handlers/opensearch.php', 'url_modules', '1', NULL),
('opensearch', 'url_handler_function', 'opensearch_url_handler', 'url_modules', '1', NULL),
('date', 'content_handler_function', 'date_content_handler', 'url_modules', '1', NULL),
('date', 'url_handler_function', 'date_url_handler', 'url_modules', '1', NULL),
('date', 'file', 'lib/vivvo/url_handlers/date.php', 'url_modules', '1', NULL),
('archive', 'content_handler_function', 'archive_content_handler', 'url_modules', '1', NULL),
('archive', 'file', 'lib/vivvo/url_handlers/archive.php', 'url_modules', '1', NULL),
('archive', 'url_handler_function', 'archive_url_handler', 'url_modules', '1', NULL),
('author', 'content_handler_function', 'author_content_handler', 'url_modules', '1', NULL),
('author', 'url_handler_function', 'author_url_handler', 'url_modules', '1', NULL),
('author', 'file', 'lib/vivvo/url_handlers/author.php', 'url_modules', '1', NULL),
('tag', 'file', 'lib/vivvo/url_handlers/tag.php', 'url_modules', '1', NULL),
('tag', 'url_handler_function', 'tag_url_handler', 'url_modules', '1', NULL),
('tag', 'content_handler_function', 'tag_content_handler', 'url_modules', '1', NULL),
('feed', 'file', 'lib/vivvo/url_handlers/feed.php', 'url_modules', '1', NULL),
('feed', 'url_handler_function', 'feed_url_handler', 'url_modules', '1', NULL),
('search.html', 'file', 'lib/vivvo/url_handlers/search.php', 'url_modules', '1', NULL),
('search.html', 'url_handler_function', 'search_url_handler', 'url_modules', '1', NULL),
('search.html', 'content_handler_function', 'search_content_handler', 'url_modules', '1', NULL),
('login.html', 'content_handler_function', 'login_content_handler', 'url_modules', '1', NULL),
('login.html', 'file', 'lib/vivvo/url_handlers/login.php', 'url_modules', '1', NULL),
('login.html', 'url_handler_function', 'login_url_handler', 'url_modules', '1', NULL),
('version.html', 'file', 'lib/vivvo/url_handlers/version.php', 'url_modules', '1', NULL),
('version.html', 'url_handler_function', 'version_url_handler', 'url_modules', '1', NULL),
('imagecode.html', 'url_handler_function', 'imagecode_url_handler', 'url_modules', '1', NULL),
('imagecode.html', 'file', 'lib/vivvo/url_handlers/imagecode.php', 'url_modules', '1', NULL),
('cron_image.html', 'file', 'lib/vivvo/url_handlers/cron_image.php', 'url_modules', '1', NULL),
('cron_image.html', 'url_handler_function', 'cron_image_url_handler', 'url_modules', '1', NULL),
('usercp.html', 'url_handler_function', 'usercp_url_handler', 'url_modules', '1', NULL),
('usercp.html', 'file', 'lib/vivvo/url_handlers/usercp.php', 'url_modules', '1', NULL),
('usercp.html', 'content_handler_function', 'usercp_content_handler', 'url_modules', '1', NULL),
('sitemap.xml', 'file', 'lib/vivvo/url_handlers/google_sitemap.php', 'url_modules', '1', NULL),
('sitemap.xml', 'url_handler_function', 'google_sitemap_url_handler', 'url_modules', '1', NULL);

/*@QUERY {"title":"Setting up user preferences"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('vivvo@localhost', 'file', 'lib/vivvo/core/Users.class.php', 'user', '1', NULL),
('vivvo@localhost', 'user_object', 'Users', 'user', '1', NULL),
('vivvo@localhost', 'user_list', 'Users_list', 'user', '1', NULL),
('vivvo@localhost', 'group_file', 'lib/vivvo/framework/vivvo_user_manager.php', 'user', '1', NULL),
('vivvo@localhost', 'group_object', 'group', 'user', '1', NULL),
('vivvo@localhost', 'group_list', 'group_list', 'user', '1', NULL);

/*@QUERY {"title":"Setting default template params"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('tpl', 'type', 'template', 'files', '1', NULL),
('template', 'class_name', 'template_info', 'file_type', '1', NULL),
('template', 'file', 'lib/vivvo/file/template_info.class.php', 'file_type', '1', NULL);

/*@QUERY {"title":"Registering default cron tasks"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/cron (`id`, `lastrun`, `time_mask`, `file`, `class`, `method`, `arguments`, `hash`) VALUES
(NULL, '0', '0 0 * * *', 'lib/vivvo/tasks/auto_reset_today_read.php', '', 'auto_reset_today_read', 'a:0:{}', '980b7418f1493f364c5223927fb9ee66'),
(NULL, '0', '*/30 * * * *', 'lib/vivvo/tasks/auto_update_article_stats.php', '', 'auto_update_article_stats', 'a:0:{}', 'd314e6c5bbb6564d14930b88a6a089d8'),
(NULL, '0', '15 4 * * 1', 'lib/vivvo/tasks/auto_backup.php', NULL, 'auto_backup', 'a:0:{}', 'f0dac63833d7333dc1e09ab6593f56d3');

/*@QUERY {"title":"Registering cron tasks preferences"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('auto_archive', 'class_name', NULL, 'cron_task', '1', NULL),
('auto_archive', 'method', 'auto_archive', 'cron_task', '1', NULL),
('auto_archive', 'file', 'lib/vivvo/tasks/auto_archive.php', 'cron_task', '1', NULL),
('auto_archive', 'template', 'admin/templates/maintenance/tasks/auto_archive.xml', 'cron_task', '1', NULL),
('auto_archive', 'arguments', 'a:0:{}', 'cron_task', '1', NULL),
('auto_delete', 'arguments', 'a:0:{}', 'cron_task', '1', NULL),
('auto_delete', 'method', 'auto_delete', 'cron_task', '1', NULL),
('auto_delete', 'class_name', NULL, 'cron_task', '1', NULL),
('auto_delete', 'template', 'admin/templates/maintenance/tasks/auto_delete.xml', 'cron_task', '1', NULL),
('auto_delete', 'file', 'lib/vivvo/tasks/auto_delete.php', 'cron_task', '1', NULL),
('auto_reset_today_read', 'arguments', 'a:0:{}', 'cron_task', '1', NULL),
('auto_reset_today_read', 'class_name', NULL, 'cron_task', '1', NULL),
('auto_reset_today_read', 'method', 'auto_reset_today_read', 'cron_task', '1', NULL),
('auto_reset_today_read', 'file', 'lib/vivvo/tasks/auto_reset_today_read.php', 'cron_task', '1', NULL),
('auto_backup', 'arguments', 'a:0:{}', 'cron_task', '1', NULL),
('auto_backup', 'method', 'auto_backup', 'cron_task', '1', NULL),
('auto_backup', 'class_name', NULL, 'cron_task', '1', NULL),
('auto_backup', 'template', 'admin/templates/maintenance/tasks/auto_backup.xml', 'cron_task', '1', NULL),
('auto_backup', 'file', 'lib/vivvo/tasks/auto_backup.php', 'cron_task', '1', NULL),
('auto_relate', 'arguments', 'a:0:{}', 'cron_task', '1', NULL),
('auto_relate', 'method', 'auto_relate', 'cron_task', '1', NULL),
('auto_relate', 'class_name', NULL, 'cron_task', '1', NULL),
('auto_relate', 'template', 'admin/templates/maintenance/tasks/auto_relate.xml', 'cron_task', '1', NULL),
('auto_relate', 'file', 'lib/vivvo/tasks/auto_relate.php', 'cron_task', '1', NULL);

/*@QUERY {"title":"Installing Vivvo Chart support"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('VIVVO_CHART_URL', NULL, '_chart', NULL, '1', NULL),
('box_chart', 'file', 'lib/vivvo/framework/vivvo_chart.class.php', 'modules', '1', NULL),
('box_chart', 'class_name', 'box_chart', 'modules', '1', NULL),
('_chart', 'file', 'lib/vivvo/framework/vivvo_chart.class.php', 'url_modules', '1', NULL),
('_chart', 'url_handler_function', 'vivvo_chart_url_handler', 'url_modules', '1', NULL);

/*@QUERY {"title":"Installing Google Analytics client"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('VIVVO_GA_PROFILEID', NULL, NULL, NULL, '1', NULL),
('VIVVO_GA_PASSWORD', NULL, NULL, NULL, '1', NULL),
('VIVVO_GA_EMAIL', NULL, NULL, NULL, '1', NULL),
('VIVVO_GA_CACHE_PERIOD', NULL, '300', NULL, '1', NULL),
('VIVVO_GA_CODE', NULL, NULL, NULL, '1', NULL),
('VIVVO_GA_ENABLED', NULL, '1', NULL, '1', NULL),
('data_providers', 'google-analytics', 'a:2:{s:5:"class";s:23:"vivvo_ga_chart_provider";s:4:"file";s:32:"lib/vivvo/framework/vivvo_ga.php";}', 'vivvo_chart', '1', NULL);

/*@QUERY {"title":"Registering default user groups"}*/
INSERT INTO `/*{VIVVO_DB_PREFIX}*/group` (`id`, `name`, `domain_id`, `allow_delete`, `allow_edit`) VALUES
(1, 'Editor', 1, 1, 1),
(2, 'Admin', 1, 0, 0),
(3, 'Trusted writer', 1, 1, 1),
(4, 'Member', 1, 0, 1),
(5, 'Premium member', 1, 1, 1),
(6, 'Writer', 1, 1, 1),
(7, 'Guest', 1, 0, 1);

/*@QUERY {"title":"Registering default file types"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/asset_file_types (`id`, `type`, `extensions`) VALUES
(1, 'images', 'jpg,jpeg,gif,png,bmp'),
(2, 'documents', 'doc,docx,txt,rtf,pdf'),
(3, 'video', 'flv,avi,mov,mpg,mpeg'),
(4, 'audio', 'mp3,wav'),
(5, 'archive', 'zip,tgz,gz,rar'),
(6, 'directory', '.dirext');

/*@QUERY {"title":"Registering default group privileges"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/group_privileges (`id`, `group_id`, `user_source`, `privileges`) VALUES
(1, 1, 'vivvo@localhost', '{"MANAGE_TAGS": 1, "MANAGE_TRASHBIN": 0, "MODERATE_COMMENTS": 1, "ACCESS_ADMIN": 1, "MANAGE_FILES": 0, "UPLOAD": 1, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [{"from": ["*"], "to": ["*"]}], "READ": {"Categories": [0]}, "WRITE": {"Categories": [0]}, "EDITOR": {"Categories": [0]}}'),
(2, 2, 'vivvo@localhost', '{"MANAGE_TAGS": 1, "MANAGE_TRASHBIN": 1, "MODERATE_COMMENTS": 1, "ACCESS_ADMIN": 1, "MANAGE_FILES": 1, "UPLOAD": 1, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [{"from": ["*"], "to": ["*"]}], "READ": {"Categories": [0]}, "WRITE": {"Categories": [0]}, "EDITOR": {"Categories": [0]}}'),
(3, 3, 'vivvo@localhost', '{"MANAGE_TAGS": 1, "MANAGE_TRASHBIN": 0, "MODERATE_COMMENTS": 0, "ACCESS_ADMIN": 1, "MANAGE_FILES": 0, "UPLOAD": 1, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [{"from": [0, 1], "to": [0, 1]}], "READ": {"Categories": [0]},"WRITE": {"Categories": [0]}, "EDITOR": {"Categories": []}}'),
(4, 4, 'vivvo@localhost', '{"MANAGE_TAGS": 0, "MANAGE_TRASHBIN": 0, "MODERATE_COMMENTS": 0, "ACCESS_ADMIN": 0, "MANAGE_FILES": 0, "UPLOAD": 0, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [], "READ": {"Categories": [0]}, "WRITE": {"Categories": []}, "EDITOR": {"Categories": []}}'),
(5, 5, 'vivvo@localhost', '{"MANAGE_TAGS": 0, "MANAGE_TRASHBIN": 0, "MODERATE_COMMENTS": 0, "ACCESS_ADMIN": 0, "MANAGE_FILES": 0, "UPLOAD": 0, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [], "READ": {"Categories": [0]}, "WRITE": {"Categories": []}, "EDITOR": {"Categories": []}}'),
(6, 6, 'vivvo@localhost', '{"MANAGE_TAGS": 0, "MANAGE_TRASHBIN": 0, "MODERATE_COMMENTS": 0, "ACCESS_ADMIN": 1, "MANAGE_FILES": 0, "UPLOAD": 0, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [], "READ": {"Categories": [0]}, "WRITE": {"Categories": [0]}, "EDITOR": {"Categories": []}}'),
(7, 7, 'vivvo@localhost', '{"MANAGE_TAGS": 0, "MANAGE_TRASHBIN": 0, "MODERATE_COMMENTS": 0, "ACCESS_ADMIN": 0, "MANAGE_FILES": 0, "UPLOAD": 0, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [], "READ": {"Categories": [0]}, "WRITE": {"Categories": []}, "EDITOR": {"Categories": []}}');

/*@QUERY {"title":"Installing system topics"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/tags_groups VALUES
(100, 'Keywords', 'keywords', 'default.tpl', 'default.tpl', NULL),
(1, 'System', 'system', 'default.tpl', 'default.tpl', NULL);

/*@QUERY {"title":"Reseting id of 'Keywords' system topic"}*/
UPDATE /*{VIVVO_DB_PREFIX}*/tags_groups SET `id` = 0 WHERE `id` = 100;

/*@QUERY {"title":"Installing url handler for 'Keywords' topic"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/configuration (`variable_name`, `variable_property`, `variable_value`, `module`, `domain_id`, `reg_exp`) VALUES
('keywords', 'content_handler_function', 'topic_content_handler', 'url_modules', '1', NULL),
('keywords', 'url_handler_function', 'topic_url_handler', 'url_modules', '1', NULL),
('keywords', 'file', 'lib/vivvo/url_handlers/topic.php', 'url_modules', '1', NULL);

/*@QUERY {"title":"Installing system tags"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/tags VALUES
(1, 'Homepage', 'homepage'),
(2, 'Headlines', 'headlines'),
(3, 'Video', 'video');

/*@QUERY {"title":"Inserting system tags into topics"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/tags_to_tags_groups VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3);
