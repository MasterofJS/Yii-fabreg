/*! modernizr 3.3.1 (Custom Build) | MIT *
 * http://modernizr.com/download/?-flexbox-flexboxlegacy-forcetouch-touchevents-setclasses !*/
!function(e,n,t){function r(e,n){return typeof e===n}function o(){var e,n,t,o,i,s,a;for(var f in _)if(_.hasOwnProperty(f)){if(e=[],n=_[f],n.name&&(e.push(n.name.toLowerCase()),n.options&&n.options.aliases&&n.options.aliases.length))for(t=0;t<n.options.aliases.length;t++)e.push(n.options.aliases[t].toLowerCase());for(o=r(n.fn,"function")?n.fn():n.fn,i=0;i<e.length;i++)s=e[i],a=s.split("."),1===a.length?Modernizr[a[0]]=o:(!Modernizr[a[0]]||Modernizr[a[0]]instanceof Boolean||(Modernizr[a[0]]=new Boolean(Modernizr[a[0]])),Modernizr[a[0]][a[1]]=o),g.push((o?"":"no-")+a.join("-"))}}function i(e){var n=x.className,t=Modernizr._config.classPrefix||"";if(b&&(n=n.baseVal),Modernizr._config.enableJSClass){var r=new RegExp("(^|\\s)"+t+"no-js(\\s|$)");n=n.replace(r,"$1"+t+"js$2")}Modernizr._config.enableClasses&&(n+=" "+t+e.join(" "+t),b?x.className.baseVal=n:x.className=n)}function s(){return"function"!=typeof n.createElement?n.createElement(arguments[0]):b?n.createElementNS.call(n,"http://www.w3.org/2000/svg",arguments[0]):n.createElement.apply(n,arguments)}function a(e){return e.replace(/([a-z])-([a-z])/g,function(e,n,t){return n+t.toUpperCase()}).replace(/^-/,"")}function f(){var e=n.body;return e||(e=s(b?"svg":"body"),e.fake=!0),e}function u(e,t,r,o){var i,a,u,l,c="modernizr",d=s("div"),p=f();if(parseInt(r,10))for(;r--;)u=s("div"),u.id=o?o[r]:c+(r+1),d.appendChild(u);return i=s("style"),i.type="text/css",i.id="s"+c,(p.fake?p:d).appendChild(i),p.appendChild(d),i.styleSheet?i.styleSheet.cssText=e:i.appendChild(n.createTextNode(e)),d.id=c,p.fake&&(p.style.background="",p.style.overflow="hidden",l=x.style.overflow,x.style.overflow="hidden",x.appendChild(p)),a=t(d,e),p.fake?(p.parentNode.removeChild(p),x.style.overflow=l,x.offsetHeight):d.parentNode.removeChild(d),!!a}function l(e,n){return!!~(""+e).indexOf(n)}function c(e,n){return function(){return e.apply(n,arguments)}}function d(e,n,t){var o;for(var i in e)if(e[i]in n)return t===!1?e[i]:(o=n[e[i]],r(o,"function")?c(o,t||n):o);return!1}function p(e){return e.replace(/([A-Z])/g,function(e,n){return"-"+n.toLowerCase()}).replace(/^ms-/,"-ms-")}function m(n,r){var o=n.length;if("CSS"in e&&"supports"in e.CSS){for(;o--;)if(e.CSS.supports(p(n[o]),r))return!0;return!1}if("CSSSupportsRule"in e){for(var i=[];o--;)i.push("("+p(n[o])+":"+r+")");return i=i.join(" or "),u("@supports ("+i+") { #modernizr { position: absolute; } }",function(e){return"absolute"==getComputedStyle(e,null).position})}return t}function v(e,n,o,i){function f(){c&&(delete P.style,delete P.modElem)}if(i=r(i,"undefined")?!1:i,!r(o,"undefined")){var u=m(e,o);if(!r(u,"undefined"))return u}for(var c,d,p,v,h,y=["modernizr","tspan"];!P.style;)c=!0,P.modElem=s(y.shift()),P.style=P.modElem.style;for(p=e.length,d=0;p>d;d++)if(v=e[d],h=P.style[v],l(v,"-")&&(v=a(v)),P.style[v]!==t){if(i||r(o,"undefined"))return f(),"pfx"==n?v:!0;try{P.style[v]=o}catch(g){}if(P.style[v]!=h)return f(),"pfx"==n?v:!0}return f(),!1}function h(e,n,t,o,i){var s=e.charAt(0).toUpperCase()+e.slice(1),a=(e+" "+O.join(s+" ")+s).split(" ");return r(n,"string")||r(n,"undefined")?v(a,n,o,i):(a=(e+" "+A.join(s+" ")+s).split(" "),d(a,n,t))}function y(e,n,r){return h(e,t,t,n,r)}var g=[],_=[],C={_version:"3.3.1",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,n){var t=this;setTimeout(function(){n(t[e])},0)},addTest:function(e,n,t){_.push({name:e,fn:n,options:t})},addAsyncTest:function(e){_.push({name:null,fn:e})}},Modernizr=function(){};Modernizr.prototype=C,Modernizr=new Modernizr;var x=n.documentElement,b="svg"===x.nodeName.toLowerCase(),w=C._config.usePrefixes?" -webkit- -moz- -o- -ms- ".split(" "):["",""];C._prefixes=w;var E=function(){function e(e,n){var o;return e?(n&&"string"!=typeof n||(n=s(n||"div")),e="on"+e,o=e in n,!o&&r&&(n.setAttribute||(n=s("div")),n.setAttribute(e,""),o="function"==typeof n[e],n[e]!==t&&(n[e]=t),n.removeAttribute(e)),o):!1}var r=!("onblur"in n.documentElement);return e}();C.hasEvent=E;var S=C.testStyles=u;Modernizr.addTest("touchevents",function(){var t;if("ontouchstart"in e||e.DocumentTouch&&n instanceof DocumentTouch)t=!0;else{var r=["@media (",w.join("touch-enabled),("),"heartz",")","{#modernizr{top:9px;position:absolute}}"].join("");S(r,function(e){t=9===e.offsetTop})}return t});var T="Moz O ms Webkit",O=C._config.usePrefixes?T.split(" "):[];C._cssomPrefixes=O;var z=function(n){var r,o=w.length,i=e.CSSRule;if("undefined"==typeof i)return t;if(!n)return!1;if(n=n.replace(/^@/,""),r=n.replace(/-/g,"_").toUpperCase()+"_RULE",r in i)return"@"+n;for(var s=0;o>s;s++){var a=w[s],f=a.toUpperCase()+"_"+r;if(f in i)return"@-"+a.toLowerCase()+"-"+n}return!1};C.atRule=z;var A=C._config.usePrefixes?T.toLowerCase().split(" "):[];C._domPrefixes=A;var N={elem:s("modernizr")};Modernizr._q.push(function(){delete N.elem});var P={style:N.elem.style};Modernizr._q.unshift(function(){delete P.style}),C.testAllProps=h;var j=C.prefixed=function(e,n,t){return 0===e.indexOf("@")?z(e):(-1!=e.indexOf("-")&&(e=a(e)),n?h(e,n,t):h(e,"pfx"))};Modernizr.addTest("forcetouch",function(){return E(j("mouseforcewillbegin",e,!1),e)?MouseEvent.WEBKIT_FORCE_AT_MOUSE_DOWN&&MouseEvent.WEBKIT_FORCE_AT_FORCE_MOUSE_DOWN:!1}),C.testAllProps=y,Modernizr.addTest("flexbox",y("flexBasis","1px",!0)),Modernizr.addTest("flexboxlegacy",y("boxDirection","reverse",!0)),o(),i(g),delete C.addTest,delete C.addAsyncTest;for(var R=0;R<Modernizr._q.length;R++)Modernizr._q[R]();e.Modernizr=Modernizr}(window,document);