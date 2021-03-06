/*
 * FancyBox - jQuery Plugin
 * Simple and fancy lightbox alternative
 *
 * Examples and documentation at: http://fancybox.net
 * 
 * Copyright (c) 2008 - 2010 Janis Skarnelis
 * That said, it is hardly a one-person project. Many people have submitted bugs, code, and offered their advice freely. Their support is greatly appreciated.
 * 
 * Version: 1.3.4 (11/11/2010)
 * Requires: jQuery v1.3+
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */

(function(a){var m,q,r,e,y,l,z,k,v,w,o=0,d={},n=[],h=0,c={},i=[],B=null,s=new Image,D=/\.(jpg|gif|png|bmp|jpeg)(.*)?$/i,O=/[^\.]\.(swf)\s*$/i,E,F=1,u=0,t="",p,f,j=!1,x=a.extend(a("<div/>")[0],{prop:0}),G=a.browser.msie&&7>a.browser.version&&!window.XMLHttpRequest,H=function(){q.hide();s.onerror=s.onload=null;B&&B.abort();m.empty()},I=function(){!1===d.onError(n,o,d)?(q.hide(),j=!1):(d.titleShow=!1,d.width="auto",d.height="auto",m.html('<p id="fancybox-error">The requested content cannot be loaded.<br />Please try again later.</p>'),
A())},C=function(){var b=n[o],c,e,f,i,k,h;H();d=a.extend({},a.fn.fancybox.defaults,"undefined"==typeof a(b).data("fancybox")?d:a(b).data("fancybox"));h=d.onStart(n,o,d);if(!1===h)j=!1;else{"object"==typeof h&&(d=a.extend(d,h));f=d.title||(b.nodeName?a(b).attr("title"):b.title)||"";b.nodeName&&!d.orig&&(d.orig=a(b).children("img:first").length?a(b).children("img:first"):a(b));""===f&&(d.orig&&d.titleFromAlt)&&(f=d.orig.attr("alt"));c=d.href||(b.nodeName?a(b).attr("href"):b.href)||null;if(/^(?:javascript)/i.test(c)||
"#"==c)c=null;d.type?(e=d.type,c||(c=d.content)):d.content?e="html":c&&(e=c.match(D)?"image":c.match(O)?"swf":a(b).hasClass("iframe")?"iframe":0===c.indexOf("#")?"inline":"ajax");if(e)switch("inline"==e&&(b=c.substr(c.indexOf("#")),e=0<a(b).length?"inline":"ajax"),d.type=e,d.href=c,d.title=f,d.autoDimensions&&("html"==d.type||"inline"==d.type||"ajax"==d.type?(d.width="auto",d.height="auto"):d.autoDimensions=!1),d.modal&&(d.overlayShow=!0,d.hideOnOverlayClick=!1,d.hideOnContentClick=!1,d.enableEscapeButton=
!1,d.showCloseButton=!1),d.padding=parseInt(d.padding,10),d.margin=parseInt(d.margin,10),m.css("padding",d.padding+d.margin),a(".fancybox-inline-tmp").unbind("fancybox-cancel").bind("fancybox-change",function(){a(this).replaceWith(l.children())}),e){case "html":m.html(d.content);A();break;case "inline":if(!0===a(b).parent().is("#fancybox-content")){j=!1;break}a('<div class="fancybox-inline-tmp" />').hide().insertBefore(a(b)).bind("fancybox-cleanup",function(){a(this).replaceWith(l.children())}).bind("fancybox-cancel",
function(){a(this).replaceWith(m.children())});a(b).appendTo(m);A();break;case "image":j=!1;a.fancybox.showActivity();s=new Image;s.onerror=function(){I()};s.onload=function(){j=true;s.onerror=s.onload=null;d.width=s.width;d.height=s.height;a("<img />").attr({id:"fancybox-img",src:s.src,alt:d.title}).appendTo(m);J()};s.src=c;break;case "swf":d.scrolling="no";i='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+d.width+'" height="'+d.height+'"><param name="movie" value="'+c+'"></param>';
k="";a.each(d.swf,function(b,a){i=i+('<param name="'+b+'" value="'+a+'"></param>');k=k+(" "+b+'="'+a+'"')});i+='<embed src="'+c+'" type="application/x-shockwave-flash" width="'+d.width+'" height="'+d.height+'"'+k+"></embed></object>";m.html(i);A();break;case "ajax":j=!1;a.fancybox.showActivity();d.ajax.win=d.ajax.success;B=a.ajax(a.extend({},d.ajax,{url:c,data:d.ajax.data||{},error:function(b){b.status>0&&I()},success:function(b,a,e){if((typeof e=="object"?e:B).status==200){if(typeof d.ajax.win==
"function"){h=d.ajax.win(c,b,a,e);if(h===false){q.hide();return}if(typeof h=="string"||typeof h=="object")b=h}m.html(b);A()}}}));break;case "iframe":J()}else I()}},A=function(){var b=d.width,c=d.height,b=-1<b.toString().indexOf("%")?parseInt((a(window).width()-2*d.margin)*parseFloat(b)/100,10)+"px":"auto"==b?"auto":b+"px",c=-1<c.toString().indexOf("%")?parseInt((a(window).height()-2*d.margin)*parseFloat(c)/100,10)+"px":"auto"==c?"auto":c+"px";m.wrapInner('<div style="width:'+b+";height:"+c+";overflow: "+
("auto"==d.scrolling?"auto":"yes"==d.scrolling?"scroll":"hidden")+';position:relative;"></div>');d.width=m.width();d.height=m.height();J()},J=function(){var b,g;q.hide();if(e.is(":visible")&&!1===c.onCleanup(i,h,c))a.event.trigger("fancybox-cancel"),j=!1;else{j=!0;a(l.add(r)).unbind();a(window).unbind("resize.fb scroll.fb");a(document).unbind("keydown.fb");e.is(":visible")&&"outside"!==c.titlePosition&&e.css("height",e.height());i=n;h=o;c=d;if(c.overlayShow){if(r.css({"background-color":c.overlayColor,
opacity:c.overlayOpacity,cursor:c.hideOnOverlayClick?"pointer":"auto",height:a(document).height()}),!r.is(":visible")){if(G)a("select:not(#fancybox-tmp select)").filter(function(){return"hidden"!==this.style.visibility}).css({visibility:"hidden"}).one("fancybox-cleanup",function(){this.style.visibility="inherit"});r.show()}}else r.hide();f=P();t=c.title||"";u=0;k.empty().removeAttr("style").removeClass();if(!(!1===c.titleShow||" "===c.title))if((t=a.isFunction(c.titleFormat)?c.titleFormat(t,i,h,c):
t&&t.length?"float"==c.titlePosition?'<table id="fancybox-title-float-wrap" cellpadding="0" cellspacing="0"><tr><td id="fancybox-title-float-left"></td><td id="fancybox-title-float-main">'+t+'</td><td id="fancybox-title-float-right"></td></tr></table>':'<div id="fancybox-title-'+c.titlePosition+'">'+t+"</div>":!1)&&""!==t)switch(k.addClass("fancybox-title-"+c.titlePosition).html(t).appendTo("body").show(),c.titlePosition){case "inside":k.css({width:f.width-2*c.padding,marginLeft:c.padding,marginRight:c.padding});
u=k.outerHeight(!0);k.appendTo(y);f.height+=u;break;case "over":k.css({marginLeft:c.padding,width:f.width-2*c.padding,bottom:c.padding}).appendTo(y);break;case "float":k.css("left",-1*parseInt((k.width()-f.width-40)/2,10)).appendTo(e);break;default:k.css({width:f.width-2*c.padding,paddingLeft:c.padding,paddingRight:c.padding}).appendTo(e)}k.hide();e.is(":visible")?(a(z.add(v).add(w)).hide(),b=e.position(),p={top:b.top,left:b.left,width:e.width(),height:e.height()},g=p.width==f.width&&p.height==f.height,
l.fadeTo(c.changeFade,0.3,function(){var b=function(){l.html(m.contents()).fadeTo(c.changeFade,1,K)};a.event.trigger("fancybox-change");l.empty().removeAttr("filter").css({"border-width":c.padding,width:f.width-2*c.padding,height:d.autoDimensions?"auto":f.height-u-2*c.padding});g?b():(x.prop=0,a(x).animate({prop:1},{duration:c.changeSpeed,easing:c.easingChange,step:L,complete:b}))})):(e.removeAttr("style"),l.css("border-width",c.padding),"elastic"==c.transitionIn?(p=N(),l.html(m.contents()),e.show(),
c.opacity&&(f.opacity=0),x.prop=0,a(x).animate({prop:1},{duration:c.speedIn,easing:c.easingIn,step:L,complete:K})):("inside"==c.titlePosition&&0<u&&k.show(),l.css({width:f.width-2*c.padding,height:d.autoDimensions?"auto":f.height-u-2*c.padding}).html(m.contents()),e.css(f).fadeIn("none"==c.transitionIn?0:c.speedIn,K)))}},Q=function(){(c.enableEscapeButton||c.enableKeyboardNav)&&a(document).bind("keydown.fb",function(b){if(27==b.keyCode&&c.enableEscapeButton)b.preventDefault(),a.fancybox.close();else if((37==
b.keyCode||39==b.keyCode)&&c.enableKeyboardNav&&"INPUT"!==b.target.tagName&&"TEXTAREA"!==b.target.tagName&&"SELECT"!==b.target.tagName)b.preventDefault(),a.fancybox[37==b.keyCode?"prev":"next"]()});c.showNavArrows?((c.cyclic&&1<i.length||0!==h)&&v.show(),(c.cyclic&&1<i.length||h!=i.length-1)&&w.show()):(v.hide(),w.hide())},K=function(){a.support.opacity||(l.get(0).style.removeAttribute("filter"),e.get(0).style.removeAttribute("filter"));d.autoDimensions&&l.css("height","auto");e.css("height","auto");
t&&t.length&&k.show();c.showCloseButton&&z.show();Q();c.hideOnContentClick&&l.bind("click",a.fancybox.close);c.hideOnOverlayClick&&r.bind("click",a.fancybox.close);a(window).bind("resize.fb",a.fancybox.resize);c.centerOnScroll&&a(window).bind("scroll.fb",a.fancybox.center);"iframe"==c.type&&a('<iframe id="fancybox-frame" name="fancybox-frame'+(new Date).getTime()+'" frameborder="0" hspace="0" '+(a.browser.msie?'allowtransparency="true""':"")+' scrolling="'+d.scrolling+'" src="'+c.href+'"></iframe>').appendTo(l);
e.show();j=!1;a.fancybox.center();c.onComplete(i,h,c);var b,g;i.length-1>h&&(b=i[h+1].href,"undefined"!==typeof b&&b.match(D)&&(g=new Image,g.src=b));0<h&&(b=i[h-1].href,"undefined"!==typeof b&&b.match(D)&&(g=new Image,g.src=b))},L=function(b){var a={width:parseInt(p.width+(f.width-p.width)*b,10),height:parseInt(p.height+(f.height-p.height)*b,10),top:parseInt(p.top+(f.top-p.top)*b,10),left:parseInt(p.left+(f.left-p.left)*b,10)};"undefined"!==typeof f.opacity&&(a.opacity=0.5>b?0.5:b);e.css(a);l.css({width:a.width-
2*c.padding,height:a.height-u*b-2*c.padding})},M=function(){if(!1===c.titleShow||" "===c.title||"over"===c.titlePosition)var b=0;"outside"===c.titlePosition&&" "!==c.title&&(b=15);"inside"===c.titlePosition&&" "!==c.title&&(b=28);"float"===c.titlePosition&&" "!==c.title&&(b=28);"iframe"==c.type&&(b=0);return[a(window).width()-2*c.margin,a(window).height()-(2*c.margin+b),a(document).scrollLeft()+c.margin,a(document).scrollTop()+c.margin]},P=function(){var b=M(),a={},e=c.autoScale,f=2*c.padding;a.width=
-1<c.width.toString().indexOf("%")?parseInt(b[0]*parseFloat(c.width)/100,10):c.width+f;a.height=-1<c.height.toString().indexOf("%")?parseInt(b[1]*parseFloat(c.height)/100,10):c.height+f;if(e&&(a.width>b[0]||a.height>b[1]))if("image"==d.type||"swf"==d.type){if(e=c.width/c.height,a.width>b[0]&&(a.width=b[0],a.height=parseInt((a.width-f)/e+f,10)),a.height>b[1])a.height=b[1],a.width=parseInt((a.height-f)*e+f,10)}else a.width=Math.min(a.width,b[0]),a.height=Math.min(a.height,b[1]);a.top=parseInt(Math.max(b[3]-
20,b[3]+0.5*(b[1]-a.height-40)),10);a.left=parseInt(Math.max(b[2]-20,b[2]+0.5*(b[0]-a.width-40)),10);return a},N=function(){var b=d.orig?a(d.orig):!1,g={};b&&b.length?(g=b.offset(),g.top+=parseInt(b.css("paddingTop"),10)||0,g.left+=parseInt(b.css("paddingLeft"),10)||0,g.top+=parseInt(b.css("border-top-width"),10)||0,g.left+=parseInt(b.css("border-left-width"),10)||0,g.width=b.width(),g.height=b.height(),g={width:g.width+2*c.padding,height:g.height+2*c.padding,top:g.top-c.padding-20,left:g.left-c.padding-
20}):(b=M(),g={width:2*c.padding,height:2*c.padding,top:parseInt(b[3]+0.5*b[1],10),left:parseInt(b[2]+0.5*b[0],10)});return g},R=function(){q.is(":visible")?(a("div",q).css("top",-40*F+"px"),F=(F+1)%12):clearInterval(E)};a.fn.fancybox=function(b){if(!a(this).length)return this;a(this).data("fancybox",a.extend({},b,a.metadata?a(this).metadata():{})).unbind("click.fb").bind("click.fb",function(b){b.preventDefault();j||(j=!0,a(this).blur(),n=[],o=0,b=a(this).attr("rel")||"",!b||""==b||"nofollow"===b?
n.push(this):(n=a("a[rel="+b+"], area[rel="+b+"]"),o=n.index(this)),C())});return this};a.fancybox=function(b,c){var d;if(!j){j=!0;d="undefined"!==typeof c?c:{};n=[];o=parseInt(d.index,10)||0;if(a.isArray(b)){for(var e=0,f=b.length;e<f;e++)"object"==typeof b[e]?a(b[e]).data("fancybox",a.extend({},d,b[e])):b[e]=a({}).data("fancybox",a.extend({content:b[e]},d));n=jQuery.merge(n,b)}else"object"==typeof b?a(b).data("fancybox",a.extend({},d,b)):b=a({}).data("fancybox",a.extend({content:b},d)),n.push(b);
if(o>n.length||0>o)o=0;C()}};a.fancybox.showActivity=function(){clearInterval(E);q.show();E=setInterval(R,66)};a.fancybox.hideActivity=function(){q.hide()};a.fancybox.next=function(){return a.fancybox.pos(h+1)};a.fancybox.prev=function(){return a.fancybox.pos(h-1)};a.fancybox.pos=function(a){j||(a=parseInt(a),n=i,-1<a&&a<i.length?(o=a,C()):c.cyclic&&1<i.length&&(o=a>=i.length?0:i.length-1,C()))};a.fancybox.cancel=function(){j||(j=!0,a.event.trigger("fancybox-cancel"),H(),d.onCancel(n,o,d),j=!1)};
a.fancybox.close=function(){function b(){r.fadeOut("fast");k.empty().hide();e.hide();a.event.trigger("fancybox-cleanup");l.empty();c.onClosed(i,h,c);i=d=[];h=o=0;c=d={};j=!1}if(!j&&!e.is(":hidden"))if(j=!0,c&&!1===c.onCleanup(i,h,c))j=!1;else if(H(),a(z.add(v).add(w)).hide(),a(l.add(r)).unbind(),a(window).unbind("resize.fb scroll.fb"),a(document).unbind("keydown.fb"),l.find("iframe").attr("src",G&&/^https/i.test(window.location.href||"")?"javascript:void(false)":"about:blank"),"inside"!==c.titlePosition&&
k.empty(),e.stop(),"elastic"==c.transitionOut){p=N();var g=e.position();f={top:g.top,left:g.left,width:e.width(),height:e.height()};c.opacity&&(f.opacity=1);k.empty().hide();x.prop=1;a(x).animate({prop:0},{duration:c.speedOut,easing:c.easingOut,step:L,complete:b})}else e.fadeOut("none"==c.transitionOut?0:c.speedOut,b)};a.fancybox.resize=function(){r.is(":visible")&&r.css("height",a(document).height());a.fancybox.center(!0)};a.fancybox.center=function(a){var d,f;if(!j&&(f=!0===a?1:0,d=M(),f||!(e.width()>
d[0]||e.height()>d[1])))e.stop().animate({top:parseInt(Math.max(d[3]-20,d[3]+0.5*(d[1]-l.height()-40)-c.padding)),left:parseInt(Math.max(d[2]-20,d[2]+0.5*(d[0]-l.width()-40)-c.padding))},"number"==typeof a?a:200)};a.fancybox.init=function(){a("#fancybox-wrap").length||(a("body").append(m=a('<div id="fancybox-tmp"></div>'),q=a('<div id="fancybox-loading"><div></div></div>'),r=a('<div id="fancybox-overlay"></div>'),e=a('<div id="fancybox-wrap"></div>')),y=a('<div id="fancybox-outer"></div>').append('<div class="fancybox-bg" id="fancybox-bg-n"></div><div class="fancybox-bg" id="fancybox-bg-ne"></div><div class="fancybox-bg" id="fancybox-bg-e"></div><div class="fancybox-bg" id="fancybox-bg-se"></div><div class="fancybox-bg" id="fancybox-bg-s"></div><div class="fancybox-bg" id="fancybox-bg-sw"></div><div class="fancybox-bg" id="fancybox-bg-w"></div><div class="fancybox-bg" id="fancybox-bg-nw"></div>').appendTo(e),
y.append(l=a('<div id="fancybox-content"></div>'),z=a('<a id="fancybox-close"></a>'),k=a('<div id="fancybox-title"></div>'),v=a('<a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a>'),w=a('<a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a>')),z.click(a.fancybox.close),q.click(a.fancybox.cancel),v.click(function(b){b.preventDefault();a.fancybox.prev()}),w.click(function(b){b.preventDefault();a.fancybox.next()}),
a.fn.mousewheel&&e.bind("mousewheel.fb",function(b,c){if(j)b.preventDefault();else if(0==a(b.target).get(0).clientHeight||a(b.target).get(0).scrollHeight===a(b.target).get(0).clientHeight)b.preventDefault(),a.fancybox[0<c?"prev":"next"]()}),a.support.opacity||e.addClass("fancybox-ie"),G&&(q.addClass("fancybox-ie6"),e.addClass("fancybox-ie6"),a('<iframe id="fancybox-hide-sel-frame" src="'+(/^https/i.test(window.location.href||"")?"javascript:void(false)":"about:blank")+'" scrolling="no" border="0" frameborder="0" tabindex="-1"></iframe>').prependTo(y)))};
a.fn.fancybox.defaults={padding:10,margin:40,opacity:!1,modal:!1,cyclic:!1,scrolling:"auto",width:560,height:340,autoScale:!0,autoDimensions:!0,centerOnScroll:!1,ajax:{},swf:{wmode:"transparent"},hideOnOverlayClick:!0,hideOnContentClick:!1,overlayShow:!0,overlayOpacity:0.7,overlayColor:"#777",titleShow:!0,titlePosition:"float",titleFormat:null,titleFromAlt:!1,transitionIn:"fade",transitionOut:"fade",speedIn:300,speedOut:300,changeSpeed:300,changeFade:"fast",easingIn:"swing",easingOut:"swing",showCloseButton:!0,
showNavArrows:!0,enableEscapeButton:!0,enableKeyboardNav:!0,onStart:function(){},onCancel:function(){},onComplete:function(){},onCleanup:function(){},onClosed:function(){},onError:function(){}};a(document).ready(function(){a.fancybox.init()})})(jQuery);