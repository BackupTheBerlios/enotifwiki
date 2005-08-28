/*
 * HIGHLIGHTING SCRIPTLET for highlighting terms on a web page.
 * -----------------------------------------------------------
 * This scriptlet is based on the ideas of Julian Robichaux
 * see http://www.nsftools.com/misc/SearchAndHighlight.htm
 * and Markus Arndt (March 2005).
*/

/* Implementation:
 * Insert eg. this a-tag in your web page:
 * <a id="highlight" title="Click to open an input box for highlighting terms."
 * style="cursor: hand;" onclick="SearchAndHighlightPrompt('this and that')" >Highlighting</a>
 * and include the javascript in the script-section.
*/

// Global parameters ----------------------------------------------------------

var useCookie = false;
    /*
     * When setting the parameter to true, the search term is stored in a cookie
     * and this cookie will be used as the default text when prompting for terms
     * which should be highlighted.
     * However, this does not always work properly as a web page can be served from a cache
     * including a cached cookie which in this case is not the last modified cookie.
    */

var warningText = "Sorry, for some reason the text of this page is unavailable. Searching will not work.";
var warningSingleCharacter = "A single character as a search term is ignored.";
var promptText  = "Please enter the words you'd like to highlight, separated by spaces:";
var defaultText = "this and that";

var leftTag  = "<font name='highlight' style='font-weight: bold; color:black; background-color:_c_;'>";
var rightTag = "</font>";

var unique_left ="_p2q4t4h_";
var unique_right="_n5x4q9j_";

var colorArray = new Array("yellow", "lightskyblue", "coral", "springgreen", "gainsboro");



/*
 * This function inserts before and after the search term a text tag,
 * the left text tag comprising the color information as an index number.
*/
function markSearchTerms(node, pattern, color_no)
	{
	if (node.nodeType == 3 /*Node.TEXT_NODE*/)
		{
		node.nodeValue=node.nodeValue.replace(pattern, window.unique_left+color_no+"_"+"$1"+window.unique_right, "gi");
		return true;
		}

	for(var m = node.firstChild; m != null; m = m.nextSibling) {
		if (m != document.getElementById('editform')) { // do not apply highliting to the textarea
			markSearchTerms(m, pattern, color_no);
		}
	}
	return true;
	}


/*
 * This function replaces the text tags by real html font tags.
 * Each search term gets its own background color.
*/
function doHighlight ()
	{
	var pr = new RegExp(window.unique_right, "gi");
	var text = document.body.innerHTML;


	for (var i=0; i<window.colorArray.length; i++)
		{
		var pl = new RegExp(window.unique_left+i+"_", "gi");
		lT = window.leftTag.replace(/_c_/, window.colorArray[i]);
		text = text.replace(pl, lT);
		}

	text = text.replace(pr, window.rightTag);
	document.body.innerHTML = text;
	return true;
	}


function removeHighlightTags()
	{
	var pattern = /<font[^>]*name..highlight[^>]*>([^<]+)<\/font>/gi;
	var text = document.body.innerHTML;

	text = text.replace(pattern , "$1");
	document.body.innerHTML = text;

	return true;
	}


function get_defaultText()
	{
	if (window.useCookie)
		{
		if (document.cookie)
			{
			cookieArray = document.cookie.split(";");
			for (i=0; i<cookieArray.length; i++)
				{
				cookie = cookieArray[i].split("=");
				if (cookie[0]=="hlterms") { defaultText = unescape(cookie[1]); break; }
				}
			}
		}
	else
		{
		defaultText =  window.defaultText;
		}

	if (!defaultText) defaultText = "";

	return defaultText;
	}




function store_searchText(searchText)
	{
	if (window.useCookie)
		{
		var expires = new Date();
		var today = new Date();
		expires.setTime(today.getTime()+1000*60*60*24*7); // one week

		searchText = searchText.replace(/^[ ]*/ig, ""); // trim
		searchText = searchText.replace(/[ ]*$/ig, "");

		var dc = "hlterms="+escape(searchText);  // +"; expires="+escape(expires.toGMTString());
		document.cookie = dc;
		}
	else
		{
		window.defaultText = searchText;
		}
	}


/*
 * This function creates the regular expression and defines the color index
 * for each search term. The parameters are passed to the markSearchTerms function.
*/
function prepareSearchTerms(searchText)
	{
	searchArray = searchText.split(" ");

	if (!document.body || typeof(document.body.innerHTML) == "undefined") {
	if (warnOnFailure) {
	alert(window.warningText);
	}
	return false;
	}

	var color_no=0;
	for (var i = 0; i < searchArray.length; i++) {
	if (searchArray[i].length>1)  // to avoid problems with some special single characters
		{
		// complete words are highlighted, each term is highlighted using a different
		// background color
		var pattern = new RegExp ("\\b([\\w]*"+searchArray[i]+"[\\w]*)\\b", "gi");
		markSearchTerms(document.body, pattern, color_no);
		color_no++;
		if (color_no>window.colorArray.length-1) color_no=0;
		}
	else alert("\""+searchArray[i]+"\": "+warningSingleCharacter);
	}
	return true;
	}



/*
 * -----------------------------------------
 * Entry point to the highlighting scriptlet
 * -----------------------------------------
 * This displays a dialog box that allows a user to enter their own
 * search terms to highlight on the page.
 */
function SearchAndHighlightPrompt(defaultText)
	{

	defaultText = get_defaultText();
	searchText = prompt(window.promptText, defaultText+" ");
	store_searchText(searchText);

	if (searchText!=null)
		{
		searchText = searchText.replace(/[ ]+/g, ' '); // remove multiple blanks
		searchText = searchText.replace(/^[ ]*/, '');  // and trim
		searchText = searchText.replace(/[ ]*$/, '');
		}

	if (!searchText)  {
		removeHighlightTags();
		return false;
		}

	// These three lines perform the real work.
	removeHighlightTags();
	prepareSearchTerms(searchText);
	doHighlight();

	return true;
	}

// end HIGHLIGHTING SCRIPTLET  -----------------------------------------------------------


// Wikipedia JavaScript support functions
// if this is true, the toolbar will no longer overwrite the infobox when you move the mouse over individual items
var noOverwrite=false;
var alertText;
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var is_gecko = ((clientPC.indexOf('gecko')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('khtml') == -1) && (clientPC.indexOf('netscape/7.0')==-1));
var is_safari = ((clientPC.indexOf('AppleWebKit')!=-1) && (clientPC.indexOf('spoofer')==-1));
var is_khtml = (navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled ));
if (clientPC.indexOf('opera')!=-1) {
    var is_opera = true;
    var is_opera_preseven = (window.opera && !document.childNodes);
    var is_opera_seven = (window.opera && document.childNodes);
}

// add any onload functions in this hook (please don't hard-code any events in the xhtml source)
function onloadhook () {
    // don't run anything below this for non-dom browsers
    if(!(document.getElementById && document.getElementsByTagName)) return;
    histrowinit();
    unhidetzbutton();
    tabbedprefs();
    akeytt();
}
if (window.addEventListener) window.addEventListener("load",onloadhook,false);
else if (window.attachEvent) window.attachEvent("onload",onloadhook);


// document.write special stylesheet links
if(typeof stylepath != 'undefined' && typeof skin != 'undefined') {
    if (is_opera_preseven) {
        document.write('<link rel="stylesheet" type="text/css" href="'+stylepath+'/'+skin+'/Opera6Fixes.css">');
    } else if (is_opera_seven) {
        document.write('<link rel="stylesheet" type="text/css" href="'+stylepath+'/'+skin+'/Opera7Fixes.css">');
    } else if (is_khtml) {
        document.write('<link rel="stylesheet" type="text/css" href="'+stylepath+'/'+skin+'/KHTMLFixes.css">');
    }
}
// Un-trap us from framesets
if( window.top != window ) window.top.location = window.location;

// for enhanced RecentChanges
function toggleVisibility( _levelId, _otherId, _linkId) {
	var thisLevel = document.getElementById( _levelId );
	var otherLevel = document.getElementById( _otherId );
	var linkLevel = document.getElementById( _linkId );
	if ( thisLevel.style.display == 'none' ) {
		thisLevel.style.display = 'block';
		otherLevel.style.display = 'none';
		linkLevel.style.display = 'inline';
	} else {
		thisLevel.style.display = 'none';
		otherLevel.style.display = 'inline';
		linkLevel.style.display = 'none';
		}
}

// page history stuff
// attach event handlers to the input elements on history page
function histrowinit () {
    hf = document.getElementById('pagehistory');
    if(!hf) return;
    lis = hf.getElementsByTagName('li');
    for (i=0;i<lis.length;i++) {
        inputs=lis[i].getElementsByTagName('input');
        if(inputs[0] && inputs[1]) {
                inputs[0].onclick = diffcheck;
                inputs[1].onclick = diffcheck;
        }
    }
    diffcheck();
}
// check selection and tweak visibility/class onclick
function diffcheck() { 
    var dli = false; // the li where the diff radio is checked
    var oli = false; // the li where the oldid radio is checked
    hf = document.getElementById('pagehistory');
    if(!hf) return;
    lis = hf.getElementsByTagName('li');
    for (i=0;i<lis.length;i++) {
        inputs=lis[i].getElementsByTagName('input');
        if(inputs[1] && inputs[0]) {
            if(inputs[1].checked || inputs[0].checked) { // this row has a checked radio button
                if(inputs[1].checked && inputs[0].checked && inputs[0].value == inputs[1].value) return false;
                if(oli) { // it's the second checked radio
                    if(inputs[1].checked) {
                    oli.className = "selected";
                    return false 
                    }
                } else if (inputs[0].checked) {
                    return false;
                }
                if(inputs[0].checked) dli = lis[i];
                if(!oli) inputs[0].style.visibility = 'hidden';
                if(dli) inputs[1].style.visibility = 'hidden';
                lis[i].className = "selected";
                oli = lis[i];
            }  else { // no radio is checked in this row
                if(!oli) inputs[0].style.visibility = 'hidden';
                else inputs[0].style.visibility = 'visible';
                if(dli) inputs[1].style.visibility = 'hidden';
                else inputs[1].style.visibility = 'visible';
                lis[i].className = "";
            }
        }
    }
}

// generate toc from prefs form, fold sections
// XXX: needs testing on IE/Mac and safari
// more comments to follow
function tabbedprefs() {
    prefform = document.getElementById('preferences');
    if(!prefform || !document.createElement) return;
    if(prefform.nodeName.toLowerCase() == 'a') return; // Occasional IE problem
    prefform.className = prefform.className + 'jsprefs';
    var sections = new Array();
    children = prefform.childNodes;
    var seci = 0;
    for(i=0;i<children.length;i++) {
        if(children[i].nodeName.toLowerCase().indexOf('fieldset') != -1) {
            children[i].id = 'prefsection-' + seci;
            children[i].className = 'prefsection';
            if(is_opera || is_khtml) children[i].className = 'prefsection operaprefsection';
            legends = children[i].getElementsByTagName('legend');
            sections[seci] = new Object();
            if(legends[0] && legends[0].firstChild.nodeValue)
                sections[seci].text = legends[0].firstChild.nodeValue;
            else
                sections[seci].text = '# ' + seci;
            sections[seci].secid = children[i].id;
            seci++;
            if(sections.length != 1) children[i].style.display = 'none';
            else var selectedid = children[i].id;
        }
    }
    var toc = document.createElement('ul');
    toc.id = 'preftoc';
    toc.selectedid = selectedid;
    for(i=0;i<sections.length;i++) {
        var li = document.createElement('li');
        if(i == 0) li.className = 'selected';
        var a =  document.createElement('a');
        a.href = '#' + sections[i].secid;
        a.onclick = uncoversection;
        a.appendChild(document.createTextNode(sections[i].text));
        a.secid = sections[i].secid;
        li.appendChild(a);
        toc.appendChild(li);
    }
    prefform.insertBefore(toc, children[0]);
    document.getElementById('prefsubmit').id = 'prefcontrol';
}
function uncoversection() {
    oldsecid = this.parentNode.parentNode.selectedid;
    newsec = document.getElementById(this.secid);
    if(oldsecid != this.secid) {
        ul = document.getElementById('preftoc');
        document.getElementById(oldsecid).style.display = 'none';
        newsec.style.display = 'block';
        ul.selectedid = this.secid;
        lis = ul.getElementsByTagName('li');
        for(i=0;i< lis.length;i++) {
            lis[i].className = '';
        }
        this.parentNode.className = 'selected';
    }
    return false;
}

// Timezone stuff
// tz in format [+-]HHMM
function checkTimezone( tz, msg ) {
	var localclock = new Date();
	// returns negative offset from GMT in minutes
	var tzRaw = localclock.getTimezoneOffset();
	var tzHour = Math.floor( Math.abs(tzRaw) / 60);
	var tzMin = Math.abs(tzRaw) % 60;
	var tzString = ((tzRaw >= 0) ? "-" : "+") + ((tzHour < 10) ? "0" : "") + tzHour + ((tzMin < 10) ? "0" : "") + tzMin;
	if( tz != tzString ) {
		var junk = msg.split( '$1' );
		document.write( junk[0] + "UTC" + tzString + junk[1] );
	}
}
function unhidetzbutton() {
    tzb = document.getElementById('guesstimezonebutton')
    if(tzb) tzb.style.display = 'inline';
}

// in [-]HH:MM format...
// won't yet work with non-even tzs
function fetchTimezone() {
	// FIXME: work around Safari bug
	var localclock = new Date();
	// returns negative offset from GMT in minutes
	var tzRaw = localclock.getTimezoneOffset();
	var tzHour = Math.floor( Math.abs(tzRaw) / 60);
	var tzMin = Math.abs(tzRaw) % 60;
	var tzString = ((tzRaw >= 0) ? "-" : "") + ((tzHour < 10) ? "0" : "") + tzHour +
		":" + ((tzMin < 10) ? "0" : "") + tzMin;
	return tzString;
}

function guessTimezone(box) {
	document.preferences.wpHourDiff.value = fetchTimezone();
}

function showTocToggle() {
  if (document.createTextNode) {
    // Uses DOM calls to avoid document.write + XHTML issues

    var linkHolder = document.getElementById('toctitle')
    if (!linkHolder) return;

    var outerSpan = document.createElement('span');
    outerSpan.className = 'toctoggle';

    var toggleLink = document.createElement('a');
    toggleLink.id = 'togglelink';
    toggleLink.className = 'internal';
    toggleLink.href = 'javascript:toggleToc()';
    toggleLink.appendChild(document.createTextNode(tocHideText));

    outerSpan.appendChild(document.createTextNode('['));
    outerSpan.appendChild(toggleLink);
    outerSpan.appendChild(document.createTextNode(']'));

    linkHolder.appendChild(document.createTextNode(' '));
    linkHolder.appendChild(outerSpan);

    var cookiePos = document.cookie.indexOf("hidetoc=");
    if (cookiePos > -1 && document.cookie.charAt(cookiePos + 8) == 1)
     toggleToc();
  }
}

function changeText(el, newText) {
  // Safari work around
  if (el.innerText)
    el.innerText = newText;
  else if (el.firstChild && el.firstChild.nodeValue)
    el.firstChild.nodeValue = newText;
}
  
function toggleToc() {
 	var toc = document.getElementById('toc').getElementsByTagName('ul')[0];
  var toggleLink = document.getElementById('togglelink')
  
 	if(toc && toggleLink && toc.style.display == 'none') {
     changeText(toggleLink, tocHideText);
 		toc.style.display = 'block';
     document.cookie = "hidetoc=0";
	} else {
    changeText(toggleLink, tocShowText);
		toc.style.display = 'none';
    document.cookie = "hidetoc=1";
	}
}

// this function generates the actual toolbar buttons with localized text
// we use it to avoid creating the toolbar where javascript is not enabled
function addButton(imageFile, speedTip, tagOpen, tagClose, sampleText) {

	imageFile=escapeQuotesHTML(imageFile);
	speedTip=escapeQuotesHTML(speedTip);
	tagOpen=escapeQuotes(tagOpen);
	tagClose=escapeQuotes(tagClose);
	sampleText=escapeQuotes(sampleText);
	var mouseOver="";

	// we can't change the selection, so we show example texts
	// when moving the mouse instead, until the first button is clicked
	if(!document.selection && !is_gecko) {
		// filter backslashes so it can be shown in the infobox
		var re=new RegExp("\\\\n","g");
		tagOpen=tagOpen.replace(re,"");
		tagClose=tagClose.replace(re,"");
		mouseOver = "onMouseover=\"if(!noOverwrite){document.infoform.infobox.value='"+tagOpen+sampleText+tagClose+"'};\"";
	}

	document.write("<a href=\"javascript:insertTags");
	document.write("('"+tagOpen+"','"+tagClose+"','"+sampleText+"');\">");

	document.write("<img width=\"23\" height=\"22\" src=\""+imageFile+"\" border=\"0\" alt=\""+speedTip+"\" title=\""+speedTip+"\""+mouseOver+">");
	document.write("</a>");
	return;
}

function addInfobox(infoText,text_alert) {
	alertText=text_alert;
	var clientPC = navigator.userAgent.toLowerCase(); // Get client info

	var re=new RegExp("\\\\n","g");
	alertText=alertText.replace(re,"\n");

	// if no support for changing selection, add a small copy & paste field
	// document.selection is an IE-only property. The full toolbar works in IE and
	// Gecko-based browsers.
	if(!document.selection && !is_gecko) {
 		infoText=escapeQuotesHTML(infoText);
	 	document.write("<form name='infoform' id='infoform'>"+
			"<input size=80 id='infobox' name='infobox' value=\""+
			infoText+"\" readonly='readonly'></form>");
 	}

}

function escapeQuotes(text) {
	var re=new RegExp("'","g");
	text=text.replace(re,"\\'");
	re=new RegExp("\\n","g");
	text=text.replace(re,"\\n");
	return escapeQuotesHTML(text);
}

function escapeQuotesHTML(text) {
	var re=new RegExp('&',"g");
	text=text.replace(re,"&amp;");
	var re=new RegExp('"',"g");
	text=text.replace(re,"&quot;");
	var re=new RegExp('<',"g");
	text=text.replace(re,"&lt;");
	var re=new RegExp('>',"g");
	text=text.replace(re,"&gt;");
	return text;
}

// apply tagOpen/tagClose to selection in textarea,
// use sampleText instead of selection if there is none
// copied and adapted from phpBB
function insertTags(tagOpen, tagClose, sampleText) {

	var txtarea = document.editform.wpTextbox1;
	// IE
	if(document.selection  && !is_gecko) {
		var theSelection = document.selection.createRange().text;
		if(!theSelection) { theSelection=sampleText;}
		txtarea.focus();
		if(theSelection.charAt(theSelection.length - 1) == " "){// exclude ending space char, if any
			theSelection = theSelection.substring(0, theSelection.length - 1);
			document.selection.createRange().text = tagOpen + theSelection + tagClose + " ";
		} else {
			document.selection.createRange().text = tagOpen + theSelection + tagClose;
		}

	// Mozilla
	} else if(txtarea.selectionStart || txtarea.selectionStart == '0') {
 		var startPos = txtarea.selectionStart;
		var endPos = txtarea.selectionEnd;
		var scrollTop=txtarea.scrollTop;
		var myText = (txtarea.value).substring(startPos, endPos);
		if(!myText) { myText=sampleText;}
		if(myText.charAt(myText.length - 1) == " "){ // exclude ending space char, if any
			subst = tagOpen + myText.substring(0, (myText.length - 1)) + tagClose + " ";
		} else {
			subst = tagOpen + myText + tagClose;
		}
		txtarea.value = txtarea.value.substring(0, startPos) + subst +
		  txtarea.value.substring(endPos, txtarea.value.length);
		txtarea.focus();

		var cPos=startPos+(tagOpen.length+myText.length+tagClose.length);
		txtarea.selectionStart=cPos;
		txtarea.selectionEnd=cPos;
		txtarea.scrollTop=scrollTop;

	// All others
	} else {
		var copy_alertText=alertText;
		var re1=new RegExp("\\$1","g");
		var re2=new RegExp("\\$2","g");
		copy_alertText=copy_alertText.replace(re1,sampleText);
		copy_alertText=copy_alertText.replace(re2,tagOpen+sampleText+tagClose);
		var text;
		if (sampleText) {
			text=prompt(copy_alertText);
		} else {
			text="";
		}
		if(!text) { text=sampleText;}
		text=tagOpen+text+tagClose;
		document.infoform.infobox.value=text;
		// in Safari this causes scrolling
		if(!is_safari) {
			txtarea.focus();
		}
		noOverwrite=true;
	}
	// reposition cursor if possible
	if (txtarea.createTextRange) txtarea.caretPos = document.selection.createRange().duplicate();
}

function akeytt() {
    if(typeof ta == "undefined" || !ta) return;
    pref = 'alt-';
    if(is_safari || navigator.userAgent.toLowerCase().indexOf( 'mac' ) + 1 ) pref = 'control-';
    if(is_opera) pref = 'shift-esc-';
    for(id in ta) {
        n = document.getElementById(id);
        if(n){
            a = n.childNodes[0];
            if(a){
                if(ta[id][0].length > 0) {
                    a.accessKey = ta[id][0];
                    ak = ' ['+pref+ta[id][0]+']';
                } else {
                    ak = '';
                }
                a.title = ta[id][1]+ak;
            } else {
                if(ta[id][0].length > 0) {
                    n.accessKey = ta[id][0];
                    ak = ' ['+pref+ta[id][0]+']';
                } else {
                    ak = '';
                }
                n.title = ta[id][1]+ak;
            }
        }
    }
}

function setupRightClickEdit() {
	if( document.getElementsByTagName ) {
		var divs = document.getElementsByTagName( 'div' );
		for( var i = 0; i < divs.length; i++ ) {
			var el = divs[i];
			if( el.className == 'editsection' ) {
				addRightClickEditHandler( el );
			}
		}
	}
}

function addRightClickEditHandler( el ) {
	for( var i = 0; i < el.childNodes.length; i++ ) {
		var link = el.childNodes[i];
		if( link.nodeType == 1 && link.nodeName.toLowerCase() == 'a' ) {
			var editHref = link.getAttribute( 'href' );
			
			// find the following a
			var next = el.nextSibling;
			while( next.nodeType != 1 )
				next = next.nextSibling;
			
			// find the following header
			next = next.nextSibling;
			while( next.nodeType != 1 )
				next = next.nextSibling;
			
			if( next && next.nodeType == 1 &&
				next.nodeName.match( /^[Hh][1-6]$/ ) ) {
				next.oncontextmenu = function() {
					document.location = editHref;
					return false;
				}
			}
		}
	}
}

function fillDestFilename() {
	if (!document.getElementById) return;
	var path = document.getElementById('wpUploadFile').value;
	// Find trailing part
	var slash = path.lastIndexOf( '/' );
	var backslash = path.lastIndexOf( '\\' );
	var fname;
	if ( slash == -1 && backslash == -1 ) {
		fname = path;
	} else if ( slash > backslash ) {
		fname = path.substring( slash+1, 10000 );
	} else {
		fname = path.substring( backslash+1, 10000 );
	}

	// Capitalise first letter and replace spaces by underscores
	fname = fname.charAt(0).toUpperCase().concat(fname.substring(1,10000)).replace( / /g, '_' );

	// Output result
	var destFile = document.getElementById('wpDestFile');
	if (destFile) destFile.value = fname;
}
	

function considerChangingExpiryFocus() {
	if (!document.getElementById) return;
	var drop = document.getElementById('wpBlockExpiry');
	if (!drop) return;
	var field = document.getElementById('wpBlockOther');
	if (!field) return;
	var opt = drop.value;
	if (opt == 'other')
		field.style.display = '';
	else
		field.style.display = 'none';
}
