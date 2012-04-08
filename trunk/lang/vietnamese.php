<?php
/* =============================================================================
 * $Revision: 6007 $
 * $Date: 2010-12-02 12:06:53 +0100 (Thu, 02 Dec 2010) $
 *
 * Vivvo CMS v4.5.2r (build 6084)
 *
 * Copyright (c) 2010, Spoonlabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * =============================================================================
 */


define ('VIVVO_DB_COLLATION', 'utf8_unicode_ci');
define ('VIVVO_LANG_CODE' , 'vi-VN');

$lang = array(
	'LNG_SEARCH_ALL' => 'Search all news',
	'LNG_SEARCH_ADVANCED' => 'Advanced search',
	'LNG_SEARCH_CAT' => 'Search categories',
	'LNG_SEARCH_ALL_CATEGORIES' => 'Search in all categories',
	'LNG_SEARCH_KEYWORD' => 'Search by keyword',
	'LNG_SEARCH_USER' => 'Search by Username',
	'LNG_SEARCH_USER_NAME' => 'By name',
	'LNG_SEARCH_USERGROUP' => 'Usergroup',
	'LNG_SEARCH_OPTION' => 'Search options',
	'LNG_SEARCH_POST' => 'Find posts from',
	'LNG_SEARCH_OPTION_ANY_DATE' => 'Any date',
	'LNG_SEARCH_OPTION_YESTRDAY' => 'Yesterday',
	'LNG_SEARCH_OPTION_A_WEEK_AGO' => 'A week ago',
	'LNG_SEARCH_OPTION_2_WEEKS_AGO' => '2 weeks ago',
	'LNG_SEARCH_OPTION_A_MONTH_AGO' => 'A month ago',
	'LNG_SEARCH_OPTION_3_MONTHS_AGO' => '3 months ago',
	'LNG_SEARCH_OPTION_6_MONTHS_AGO' => '6 months ago',
	'LNG_SEARCH_OPTION_A_YEAR_AGO' => 'A year ago',
	'LNG_SEARCH_SITE' => 'Search the site',
	'LNG_SEARCH_SORT' => 'Sort results by',
	'LNG_SEARCH_STATUS' => 'Search by status',
	'LNG_SEARCH_REFINE' => 'Please refine your search',
	'LNG_SEARCH_NARROW' => 'You have more than 100 results. Please narrow your search.',

	'LNG_SEARCH_RESULT' => 'found',
	'LNG_SEARCH_RESULTS' => 'Search results',
	'LNG_SEARCH_NO_RESULT' => 'There is no results for your query.',
	'LNG_SEARCH_NO_RESULT1' => 'There is no results containing',
	'LNG_SEARCH_NO_RESULT2' => ' term. Please, search again.',

	'LNG_SEARCH_FORM_AUTHOR' => 'Author',
	'LNG_SEARCH_FORM_AND_OLDER' => 'and older',
	'LNG_SEARCH_FORM_CATEGORIES' => 'Search categories (use Ctrl for multiple)',
	'LNG_SEARCH_FORM_EXACT_NAME' => 'Exact name',
	'LNG_SEARCH_FORM_KEYWORD' => 'Keyword(s)',
	'LNG_SEARCH_FORM_POST' => 'Find posts from',
	'LNG_SEARCH_FORM_TITLES_ONLY' => 'Titles only',

	'LNG_ARTICLE_POSTED_ON' => 'on',

	'LNG_SELECT_DAY' => 'Select day',
	'LNG_SELECT_MONTH' => 'Select month',
	'LNG_SELECT_YEAR' => 'Select year',

	'LNG_SORT_BY' => 'Sort by',
	'LNG_SORT_AUTHOR' => 'Author',
	'LNG_SORT_DATE' => 'Date',
	'LNG_SORT_TITLE' => 'Title',

	'LNG_LOG_IN' => 'Log in',
	'LNG_LOG_OUT' => 'Log out',
	'LNG_NOT_LOGGED' => 'You have to be logged in to post comments',
	'LNG_NO_LOGGED_NO_COMMENT' => 'You have to be logged in to post comments',

	'LNG_COMMENT' => 'Comment',
	'LNG_COMMENTS' => 'Comments',
	'LNG_COMMENT_POST' => 'Post your comment',
	'LNG_COMMENT_POSTED' => 'posted',
	'LNG_ARTICLE_COMMENTS_POSTED' => 'Posted by',
	'LNG_ARTICLE_COMMENTS_POSTED_ON' => 'on',
	'LNG_COMMENT_RSS' => 'Subscribe to comments feed',
	'LNG_LATEST_COMMENTS' => 'Latest comments',
	'LNG_NO_COMMENT_POSTED' => ' - No comments posted',
	'LNG_NUM_COMMENT_POSTED' => ' comments posted',
	'LNG_COMMENTS_REPORT_INAPPROPRIATE' => 'Report as inappropriate',
	'LNG_COMMENTS_THUMB_UP' => 'Thumbs Up',
	'LNG_COMMENTS_THUMB_DOWN' => 'Thumbs Down',
	'LNG_REPORT_COMMENT_SENT' => 'Inappropriate content reported.',
	'LNG_REPORT_COMMENT_FAILD' => 'Mail could not be sent.',
	'LNG_REPORT_COMMENT_MAIL_SUBJECT' => 'Reported post from <WEBSITE_TITLE>',
	'LNG_REPORT_COMMENT_IP_ADDRESS_AUTHOR_COMMENT' => 'IP address of the person who posted the comment',
	'LNG_REPORT_COMMENT_REPORTED_COMMENT_INFO' => 'User who reported the comment',
	'LNG_REPORT_COMMENT_MESSAGE_1' => 'Please respond to this post as applicable.',
	'LNG_REPORT_COMMENT_MESSAGE_2' => 'This comment is part of this article',
	'LNG_COMMENT_REPLY' => 'Reply',
	'LNG_COMMENT_REPLYING_TO' => 'You\'re writing reply to',

	'LNG_COMMENTS_BBCODE_BOLD' => 'Bold',
	'LNG_COMMENTS_BBCODE_ITALIC' => 'Italic',
	'LNG_COMMENTS_BBCODE_UNDERLINE' => 'Underline',
	'LNG_COMMENTS_BBCODE_QUOTE' => 'Quote',
	'LNG_COMMENTS_BBCODE_INSERT_LINK' => 'Insert link',
	'LNG_COMMENTS_BBCODE_LINK_URL' => 'URL',
	'LNG_COMMENTS_BBCODE_LINK_TEXT' => 'Text',

	'LNG_CANT_POSTED_COMMENTS' => 'Only registred users!',
	'LNG_CANT_POSTED_COMMENTS_BAD_WORDS' => 'You can\'t post this comment beacuse you used the following words:',

	'LNG_ADD_COMMENTS' => 'Add your comments',
	'LNG_ADD_COMMENTS_AUTHOR' => 'Your name',
	'LNG_ADD_COMMENTS_WWW' => 'Your website',
	'LNG_ADD_COMMENT_CAPTCHA' => 'Please enter the code you see in the image:',
	'LNG_ADD_COMMENTS_MAIL' => 'Your e-mail address',
	'LNG_ADD_COMMENTS_WAITING' => 'Your comment has been saved and will be visible after moderator\'s approval.',

	'LNG_ADDED_COMMENT1' => 'Your comment was posted successfully.',
	'LNG_ADDED_COMMENT2' => 'Back to the article page',
	'LNG_ADDED_COMMENT3' => 'Your comment has been submitted for approval.',

	'LNG_NO_ARTICLES' => 'There are no articles in selected category.',
	'LNG_NO_CATEGORY_ARTICLES' => 'There are no articles in this category',
	'LNG_ARTICLES_PER_PAGE' => 'Articles per page',
	'LNG_EMAIL__ARTICLE' => 'Email this article',

	'LNG_POPULAR_TAGS' => 'Popular tags',
	'LNG_ALL_TAGS' => 'View all tags',
	'LNG_ARTICLE_TAGS' => 'Tags',
	'LNG_ARTICLE_TAGGED_AS' => 'Tagged as',
	'LNG_NO_TAGS_FOR_ARTICLE' => 'No tags for this article',
	'LNG_MY_TAGS' => 'My tags',
	'LNG_TAGS_ADD' => 'Add',
	'LNG_IN' => 'In',

	'LNG_SHARE_ON_SOCIAL_BOOKMARKS' => 'Share on',

	'LNG_SUBMIT_BUTTON' => 'Submit',
	'LNG_RESET_BUTTON' => 'Reset',
	'LNG_VOTES_BUTTON' => 'Vote',
	'LNG_MAIL_BUTTON' => 'Subscribe',
	'LNG_SEARCH_BUTTON' => 'Search',
	'LNG_ADD_COMMENTS_BUTTON' => 'Submit',
	'LNG_CANCEL' => 'Cancel',

	'LNG_PLUGIN_CONTACT' => 'Contact us',
	'LNG_PLUGIN_SITEMAP' => 'Sitemap',

	'LNG_AUTHORS' => 'Authors',
	'LNG_AUTHOR_BY' => 'By',
	'LNG_AUTHOR_INFO' => 'Author info',
	'LNG_AUTHOR_LINK' => 'Provided by',
	'LNG_FEATURED_AUTHOR' => 'Featured author',

	'LNG_USER_FULL_NAME' => 'Full name',
	'LNG_USER_FIRSTNAME' => 'First name',
	'LNG_USER_SECONDNAME' => 'Last name',

	'LNG_USER_AUTHOR' => 'Author',
	'LNG_USER_EMAIL' => 'E-mail',
	'LNG_USER_INTEREST' => 'Point of interest:',
	'LNG_USER_FEED' => 'Author feed',
	'LNG_USER_PICTURE' => 'Picture',
	'LNG_USER_PUBLISHED' => 'Published articles:',
	'LNG_USER_TEXT' => 'Short bio',
	'LNG_USER_WWW' => 'Website',

	'LNG_USER_ADD' => 'Add user:',
	'LNG_USER_ADDED' => 'User added succesfully',
	'LNG_USER_ALREADY_EXIST' => 'Username already exists',

	'LNG_USER_USERNAME' => 'Username',
	'LNG_USER_PASSWORD' => 'Password',
	'LNG_USER_FORGOT_YOUR_PASSWORD' => 'Forgot your password?',
	'LNG_USER_FIRSTNAME' => 'Firstname',
	'LNG_USER_FIRST_NAME' => 'Firstname',
	'LNG_USER_LAST_NAME' => 'Lastname',

	'LNG_USER_LOGIN' => 'Log in',
	'LNG_USER_LOGOUT' => 'Log out',
	'LNG_USER_REMEMBER_ME' => 'Remember me',
	'LNG_USER_EMPTY' => 'Username cannnot be empty',
	'LNG_USER_SUBMIT' => 'Add user',

	'LNG_USER_UPLOAD' => 'File is valid, and was successfully uploaded.',
	'LNG_USER_UPLOAD_ERROR' => 'Possible file upload attack!',

	'LNG_ADMIN' => 'Admin',
	'LNG_USER_GROUP_0' => 'Writer',
	'LNG_USER_GROUP_1' => 'Editor',
	'LNG_USER_GROUP_2' => 'Admin',
	'LNG_USER_GROUP_3' => 'Trusted writer',
	'LNG_USER_GROUP_4' => 'Member',
	'LNG_USER_GROUP_5' => 'Premium member',

	'LNG_RELATED_LINKS' => 'No matching news for this article',
	'LNG_RELATED_LINK_BY' => 'by',
	'LNG_RELATED_LINK_POST' => 'posted on',

	'LNG_VOTES' => 'votes',
	'LNG_VOTES_TOTAL' => 'total',
	'LNG_VOTES_RATING' => 'Rating:',
	'LNG_RATE_ARTICLE' => 'Rate this article',
	'LNG_VOTES_SCRIPT_OK' => 'Thank you for voting for this article.',
	'LNG_VOTES_SCRIPT_ALREADY' => 'You already voted for this article!',

	'LNG_MOST_POPULAR' => 'Most Popular',
	'LNG_MOST_EMAILED' => 'Most E-mailed',
	'LNG_MOST_COMMENTED' => 'Most Commented',
	'LNG_TOP_RATED' => 'Top Rated',

	'LNG_ALL_NEWS' => 'All news',
	'LNG_MORE_NEWS' => 'More news',
	'LNG_MORE_FROM' => 'More from',
	'LNG_RELATED_NEWS' => 'Related news',
	'LNG_FEATURED_NEWS' => 'Headlines',
	'LNG_LATEST_NEWS' => 'Latest additions',

	'LNG_BACK' => 'Back',
	'LNG_PAUSE' => 'Pause',
	'LNG_FORWARD' => 'Forward',

	'LNG_SU' => 'Su',
	'LNG_MO' => 'Mo',
	'LNG_TU' => 'Tu',
	'LNG_WE' => 'We',
	'LNG_TH' => 'Th',
	'LNG_FR' => 'Fr',
	'LNG_SA' => 'Sa',
	'LNG_MONTH_1' => 'January',
	'LNG_MONTH_2' => 'February',
	'LNG_MONTH_3' => 'March',
	'LNG_MONTH_4' => 'April',
	'LNG_MONTH_5' => 'May',
	'LNG_MONTH_6' => 'June',
	'LNG_MONTH_7' => 'July',
	'LNG_MONTH_8' => 'August',
	'LNG_MONTH_9' => 'September',
	'LNG_MONTH_10' => 'October',
	'LNG_MONTH_11' => 'November',
	'LNG_MONTH_12' => 'December',
	'LNG_MONTH_SHORT_1' => 'Jan',
	'LNG_MONTH_SHORT_2' => 'Feb',
	'LNG_MONTH_SHORT_3' => 'Mar',
	'LNG_MONTH_SHORT_4' => 'Apr',
	'LNG_MONTH_SHORT_5' => 'May',
	'LNG_MONTH_SHORT_6' => 'Jun',
	'LNG_MONTH_SHORT_7' => 'Jul',
	'LNG_MONTH_SHORT_8' => 'Aug',
	'LNG_MONTH_SHORT_9' => 'Sep',
	'LNG_MONTH_SHORT_10' => 'Oct',
	'LNG_MONTH_SHORT_11' => 'Nov',
	'LNG_MONTH_SHORT_12' => 'Dec',

	'LNG_CODE' => 'Code',

	'LNG_TO' => 'To',
	'LNG_BCC' => 'Bcc',
	'LNG_MESSAGE' => 'Message',
	'LNG_YOUR_EMAIL_ADDRESS' => 'Your email address',
	'LNG_CONFIRM_PASSWORD' => 'Confirm password',
	'LNG_EMAIL_FRIEND' => 'Email to a friend',

	'LNG_SIGN_UP' => 'New member? Register now',
	'LNG_ENETER_YOUR_NEW_PASSWORD' => 'Enter your new password',


	'LNG_MAIL_ADDRESS' => 'Email address',
	'LNG_SENDEMAIL_SUBMIT' => 'Submit',

	'LNG_MORE_HEADLINES' => 'Other headline news',
	'LNG_HEADLINE_LINK' => 'Full story',
	'LNG_FULL_STORY' => ' Full story ',
	'LNG_VISIT_WEBSITE' => ' Visit website ',
	'LNG_DOWNLOAD_ATTACHMENT' => 'Download attachment',
	'LNG_ATTACHMENTS' => 'Attachments',
	'LNG_CLICK_ACCESS_LINK' => 'Click the following to access the sent link',

	'LNG_RESTRICTED_ACCESS' => 'Restricted access.',
	'LNG_RESTRICTED_ONLY_REGISTERED' => 'You don\'t have sufficient privileges to view this page.',
	'LNG_RESTRICTED_ONLY_PREMIUM' => 'Only premium memebers can view this article.',

	'LNG_IMAGE' => 'Image',
	'LNG_INCORRECT_CAPTCHA' => 'Incorrect image code.',

	'LNG_TAG' => 'Tag',
	'LNG_TAGS' => 'Tags',

	'LNG_ARCHIVE' => 'Archive',
	'LNG_ARCHIVE_INCLUDE' => 'include archive',
	'LNG_ARCHIVE_NAVIGATE' => 'Navigate archive',
	'LNG_ARCHIVE_SEARCH' => 'Search archive',

	'LNG_ADD_FAVORITES' => 'Add to favorites',
	'LNG_PRINT_VERSION' => 'Print version',

	'LNG_GO'=> 'Go',
	'LNG_GO_HOME' => 'Home',
	'LNG_SET_HOMEPAGE' => 'Set as homepage',
	'LNG_SITEMAP' => 'Sitemap',
	'LNG_SECTIONS' => 'Sections',
	'LNG_PLAIN_TEXT' => 'Plain text',
	'LNG_SITE_ADMINISTRATION' => 'Site administration',
	'LNG_CLOSE' => 'close',
	'LNG_DISPLAYING' => 'displaying',
	'LNG_TOTAL' => 'total',
	'LNG_PAGE_NOT_FOUND' => 'The page cannot be found',
	'LNG_FONT_SIZE' => 'Font size:',
	'LNG_TIMES_READ' => 'times read',

//Register
	'LNG_EDIT_PERSONAL_INFORMATION' => 'Edit personal information',
	'LNG_SAVE' => 'Save',
	'LNG_EMAIL_TAKEN' => 'Email address is not available.',
	'LNG_CHECKING_EMAIL_AVAILABILITY' => 'Checking email availability ...',
	'LNG_YOUR_WEBSITE_MUST_BE_A_VALID_URL' => 'Website must be a valid url.',
	'LNG_EMAIL_ADDRESS_VALID' => 'Email address valid.',
	'LNG_EMAIL_ADDRESS_NOT_VALID' => 'Email address not valid.',
	'LNG_VERY_WEAK' => 'Very Weak',
	'LNG_WEAK' => 'Weak',
	'LNG_GOOD' => 'Good',
	'LNG_STRONG' => 'Strong',
	'LNG_VERY_STRONG' => 'Very Strong',
	'LNG_PASSWORD_TOO_SHORT' => 'Password is too short. Minimum is 6 characters.',
	'LNG_PASSWORD_MUST_NOT_CONTAIN_USERNAME' => 'Password cannot contain username.',
	'LNG_TOO_MANY_REPEATED_CHARACTERS' => 'Too many repeated characters.',
	'LNG_PASSWORD_INVALID' => 'Password invalid.',
	'LNG_PASSWORDS_ARE_NOT_IDENTICAL' => 'Passwords are not identical.',
	'LNG_PASSWORDS_ARE_IDENTICAL' => 'Passwords are identical.',
	'LNG_CHECKING_USERNAME_AVAILABILITY' => 'checking username availability...',
	'LNG_USERNAME_TAKEN' => 'Username taken',
	'LNG_USERNAME_AVAILABLE' => 'Username available.',
	'LNG_COULD_NOT_CHECK_AVAILABILITY' => 'Could not check availability at this time.',
	'LNG_USERNAME_TOO_SHORT' => 'Username too short!',
	'LNG_USERNAME_INVALID' => 'Username invalid!',
	'LNG_USERNAME_VALID' => 'Username valid.',
	'LNG_REGISTER_VALID_URL' => 'Valid URL.',
	'LNG_REGISTER_INVALID_URL' => 'Invalid URL.',
	'LNG_USERNAME_NOT_VALID' => 'Username not valid',
	'LNG_IS_ALREADY_TAKEN' => 'is already taken',
	'LNG_YOU_HAVE_TO_ENTER_IDENTICAL_PASSWORD' => 'You have to enter identical password in both password fields!',
	'LNG_YOU_HAVE_TO_TYPE_IN_A_REAL_EMAIL' => 'You have to type in a real e-mail address!',
	'LNG_TERMS_OD_SERVICE_INFO' => 'You have to check that you have read and agreed to the Terms Of Service.',
	'LNG_FORGOT_YOUR_PASSWORD' => 'Forgot password?',
	'LNG_RETURN_TO_LOGIN' => 'Return to Login',
	'LNG_USER_TOS_REGISTER' => 'By registering you agree to',
	'LNG_USER_TOS' => 'terms of service',
	'LNG_REGISTRATION_CLOSED' => 'Public registrations are closed.',


	//Close site
	'LNG_SITE_CLOSED' => 'The Site is temporarily closed.',
	'LNG_SITE_CLOSED_REASON' => 'Reason for closing',
	'LNG_SITE_CLOSED_VISIT_LATER' => 'Please, visit later',

	//404 page not found
	'LNG_404_NOT_FOUND' => 'Page Not Found',
	'LNG_404_NOT_FOUND_INFO' => 'The requested URL was not found on this server.',
	'LNG_404_NOT_FOUND_NOTIFY' => 'If you believe this page should be here, please',
	'LNG_404_NOT_FOUND_NOTIFY_LINK' => 'notify administrator.',
	'LNG_404_GO_HOME' => 'Go home',
	'LNG_INFO_REPORTED_404_SUCCESS' => 'Thank you for reporting invalid URL.',
	'LNG_REPORT_404_MAIL_SUBJECT' => 'Reported 404 url from <WEBSITE_TITLE>',
	'LNG_REPORT_404_BODY' => 'Someone has reported 404 Not found page.',

	//Admin meny
	'LNG_EDIT_ARTICLE_OPTION' => 'Edit article options',
	'LNG_EDIT_CATEGORY_OPTION' => 'Edit category options',
	'LNG_MODERATE_COMMENTS' => 'Moderate comments',

	//FRON-END SERVICE INFO

	'LNG_INFO_FORGOT_PASSWORD_INFO' => 'Please enter your username or email address provided during registration. If your username or email address exist in our database, you will receive instructions how to reset your password.',
	'LNG_INFO_FORGOT_PASSWORD_NOTICE' => 'You should receive instructions for reseting password shortly.',
	'LNG_INFO_FORGOT_PASSWORD__HAS_BEEN_SUCCESSFULLY_CHANGED' => 'Your password has been successfully changed.',

	//Articles action
	'LNG_INFO_ARTICLE_VOTE_SUCCESS' => 'Thank you for voting.',
	'LNG_INFO_ARTICLE_E_MAIL_TO_A_FRIEND_SENT_SUCCESS' => 'Email to <MAIL_TO> sent successfully.',

	//Comments action
	'LNG_INFO_COMMENT_ADD_SUCCESS' => 'Your comment was added.',
	'LNG_INFO_COMMENT_REPORTING_SUCCESS' => 'Inappropriate content reported.',
	'LNG_INFO_COMMENT_VOTE_SUCCESS' => 'Voted success',

	'LNG_INFO_USER_REGISTER_SUCCESS' => '<strong>Success! Thank you for signing up!</strong><br /><br />You may now take advantage of the advanced features of our web site. Within the next few minutes, you\'ll recieve a confirming email message. To completely activate your account you must click on the link contained in this email message that you will be receiving.<br /><br /><em>Please note:</em> If the confirming email doesn\'t show up, please look in your "spam" box or "junk mail" box in case it has been mistakenly diverted.',
	'LNG_INFO_USER_CONFIRM_SUCCESS' => 'Your registration was completed successfully',
	'LNG_THANK_YOU_FOR_CONFIRMING' => 'You have successfully completed registration process.',
	'LNG_INFO_USER_EDIT_SUCCESS' => 'User profile was updated successfully',

	'LNG_REGISTRATION_SUBJECT' => 'Registration',
	'LNG_CONFIRMATION_EMAIL' => 'Dear <FULLNAME>,<br />Thank you for signing up! Click or copy and paste this URL to your browser to activate your account:<br /><br /><ACTIVATION_URL><br /><br />Please note that your activation code is NOT your password.<br /><br />Thank you for using our service<br /><br />Best regards,<br /><WEBSITE_NAME><br /><WEBSITE_URL>',

	'LNG_NO_ENTRIES' => 'There were no entries found that match your criteria.',
	'LNG_ALLERADY_LOGGED_IN' => 'Already logged in',

	//Blog section
	'LNG_POPULAR_POSTS' => 'Popular posts',
	'LNG_BLOGGERS' => 'Bloggers',
	'LNG_AUTHORS_BLOGS' => 'Author\'s blogs',

	'LNG_PRETTY_DATE_PREFIX' => '',
	'LNG_PRETTY_DATE_SUFFIX' => ' ago',
	'LNG_PRETTY_DATE_FEW_MOMENTS' => 'few moments',
	'LNG_PRETTY_DATE_VERY_LONG_TIME' => 'very long time',
	'LNG_PRETTY_DATE_1_MINUTE' => '1 minute',
	'LNG_PRETTY_DATE_MINUTES' => 'minutes',
	'LNG_PRETTY_DATE_1_HOUR' => '1 hour',
	'LNG_PRETTY_DATE_HOURS' => 'hours',



	//FRONT-END SERVICE ERRORS

	'LNG_ERROR_2032' => 'Wrong email - Your email.',
	'LNG_ERROR_2033' => 'Unable to update the article after sending the email',
	'LNG_ERROR_2034' => 'Wrong email - To',
	'LNG_ERROR_2035' => 'Article does not exist',
	'LNG_ERROR_2036' => 'Only registered users can send email',

	'LNG_ERROR_2201' => 'You IP address is banned.',
	'LNG_ERROR_2202' => 'Flood protection.',
	'LNG_ERROR_2203' => 'Can\'t insert comment in database.',
	'LNG_ERROR_2204' => 'CAPTCHA is wrong.',
	'LNG_ERROR_2205' => 'You don\'t have sufficient privileges to posting comments.',
	'LNG_ERROR_2206' => 'Posting comments is not allowed.',
	'LNG_ERROR_2219' => 'This comment does not exist.',
	'LNG_ERROR_2220' => 'You must be logged in to report.',
	'LNG_ERROR_2221' => 'Please enter your comment.',
	'LNG_ERROR_2222' => 'You already voted on this comment.',
	'LNG_ERROR_2223' => 'Can\'t vote for the comment.',
	'LNG_ERROR_2224' => 'Can\'t vote for the comment. Please try again later.',
	'LNG_ERROR_2225' => 'Comment does not exist.',
	'LNG_ERROR_2226' => 'You must be logged in to vote.',

	'LNG_ERROR_2314' => 'Password must be minimum 6 characters long.',
	'LNG_ERROR_2315' => 'Password does not match.',
	'LNG_ERROR_2316' => 'Wrong email address.',
	'LNG_ERROR_2317' => 'Can\'t update user after deleting old image.',
	'LNG_ERROR_2318' => 'Can\'t update user.',
	'LNG_ERROR_2319' => 'User doesn\'t exist.',
	'LNG_ERROR_2320' => 'Password must be minimum 6 characters long.',
	'LNG_ERROR_2321' => 'Password does not match.',
	'LNG_ERROR_2322' => 'Wrong email address.',
	'LNG_ERROR_2323' => 'Can\'t update user after deleting old image.',
	'LNG_ERROR_2324' => 'Can\'t update user.',
	'LNG_ERROR_2325' => 'User doesn\'t exist.',
	'LNG_ERROR_2326' => 'You can not edit other users\' data.',
	'LNG_ERROR_2327' => 'You don\'t have sufficient privileges to edit user.',
	'LNG_ERROR_2328' => 'You must be logged in to edit user.',

	'LNG_ERROR_2701' => 'Username already exists.',
	'LNG_ERROR_2702' => 'Invalid e-mail address.',
	'LNG_ERROR_2703' => 'Email address already exists.',
	'LNG_ERROR_2704' => 'Invalid username.',
	'LNG_ERROR_2705' => 'Username already exists.',
	'LNG_ERROR_2706' => 'Password must contain minimum 6 characters.',
	'LNG_ERROR_2707' => 'Password does not match.',
	'LNG_ERROR_2708' => 'Can\'t insert user in database.',
	'LNG_ERROR_2709' => 'You can not register because you are already logged in.',
	'LNG_ERROR_2710' => 'Invalid confirmation data.',
	'LNG_ERROR_2711' => 'Can\'t confirm your profile.',
	'LNG_ERROR_2712' => 'You must specify either user name or e-mail address.',
	'LNG_ERROR_2713' => 'Invalid activation key.',
	'LNG_ERROR_2714' => 'Invalid captcha code.',
	'LNG_ERROR_2751' => 'Invalid username or password. You have used all three login attempts, now you\'ll have to wait one hour before you can try again.',
	'LNG_ERROR_2752' => 'Invalid username or password. You have one more login attempt.',
	'LNG_ERROR_2753' => 'Invalid username or password. You have two more login attempts.',
	'LNG_ERROR_2754' => 'You are an inactive user.',
	'LNG_ERROR_2755' => 'You already sent request for `forgot mail`.'
);
?>