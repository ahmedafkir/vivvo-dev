function updatePoll(id){
	pollParam = $('poll_form_' + id).serialize(true);
	pollParam.template_output = 'box/plugin_poll';
    
    if (!('baseHref' in window)){
    
        window.baseHref = $$('head>base')[0].readAttribute('href');
    }
    
    var base = baseHref.replace(/^(https?:\/\/)(?!www\.)/i, location.href.match(/https?:\/\/www\./i) ? '$1www.' : '$1');
        
	new Ajax.Updater('poll_form_holder_' + id, base + 'index.php?search_pid='+id, {
		parameters: pollParam,
		evalScripts: false
	});
}
