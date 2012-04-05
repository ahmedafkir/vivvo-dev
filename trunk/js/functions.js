//+or- font
var tgs = new Array( 'div','td','tr','a');
var szs = new Array( '7pt','8pt','9pt','10pt','11pt','12pt','13pt' );
var startSz = 1;

// +/-
function ts( trgt,inc ) {
	if (!document.getElementById) return
	var d = document,cEl = null,sz = startSz,i,j,cTags;
	
	sz += inc;
	if ( sz < 0 ) sz = 0;
	if ( sz > 6 ) sz = 6;
	startSz = sz;
		
	if ( !( cEl = d.getElementById( trgt ) ) ) cEl = d.getElementsByTagName( trgt )[ 0 ];

	cEl.style.fontSize = szs[ sz ];

	for ( i = 0 ; i < tgs.length ; i++ ) {
		cTags = cEl.getElementsByTagName( tgs[ i ] );
		for ( j = 0 ; j < cTags.length ; j++ ) cTags[ j ].style.fontSize = szs[ sz ];
	}
}
// size
function tsz( trgt,sz ) {
	if (!document.getElementById) return
	var d = document,cEl = null,i,j,cTags;
	
	if ( !( cEl = d.getElementById( trgt ) ) ) cEl = d.getElementsByTagName( trgt )[ 0 ];

	cEl.style.fontSize = sz;

	for ( i = 0 ; i < tgs.length ; i++ ) {
		cTags = cEl.getElementsByTagName( tgs[ i ] );
		for ( j = 0 ; j < cTags.length ; j++ ) cTags[ j ].style.fontSize = sz; //szs[ sz ];
	}
}

function resizeShort(short, summary){
	short.setStyle({overflow:'hidden'});
	
	if (summary){
		var i = 0;
		var text = summary.innerHTML.stripTags();
		summary.update(text);
		
		while (short.scrollHeight > short.offsetHeight) {
			i++;
			if (i > 100) break;
			var text = summary.innerHTML;
			summary.update(text.replace(/\W*\w+\W*$/, ''));
		}
	}
}