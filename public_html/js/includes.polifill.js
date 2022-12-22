Array.prototype.includes||Object.defineProperty(Array.prototype,"includes",{value:function(e,t){if(null==this)throw new TypeError('"this" is null or not defined');var r=Object(this),n=r.length>>>0;if(0==n)return!1;var i,s,t=0|t,c=Math.max(0<=t?t:n-Math.abs(t),0);for(;c<n;){if((i=r[c])===(s=e)||"number"==typeof i&&"number"==typeof s&&isNaN(i)&&isNaN(s))return!0;c++}return!1}}),Array.prototype.find||Object.defineProperty(Array.prototype,"find",{value:function(e){if(null==this)throw new TypeError('"this" is null or not defined');var t=Object(this),r=t.length>>>0;if("function"!=typeof e)throw new TypeError("predicate must be a function");for(var n=arguments[1],i=0;i<r;){var s=t[i];if(e.call(n,s,i,t))return s;i++}},configurable:!0,writable:!0}),Array.prototype.forEach||(Array.prototype.forEach=function(e,t){var r,n;if(null==this)throw new TypeError(" this is null or not defined");var i,s=Object(this),c=s.length>>>0;if("function"!=typeof e)throw new TypeError(e+" is not a function");for(1<arguments.length&&(r=t),n=0;n<c;)n in s&&(i=s[n],e.call(r,i,n,s)),n++}),Array.prototype.filter||(Array.prototype.filter=function(e){"use strict";if(null==this)throw new TypeError;var t=Object(this),r=t.length>>>0;if("function"!=typeof e)throw new TypeError;for(var n,i=[],s=2<=arguments.length?arguments[1]:void 0,c=0;c<r;c++)c in t&&(n=t[c],e.call(s,n,c,t)&&i.push(n));return i}),Array.prototype.findIndex||(Array.prototype.findIndex=function(e){if(null==this)throw new TypeError("Array.prototype.findIndex called on null or undefined");if("function"!=typeof e)throw new TypeError("predicate must be a function");for(var t,r=Object(this),n=r.length>>>0,i=arguments[1],s=0;s<n;s++)if(t=r[s],e.call(i,t,s,r))return s;return-1}),Object.keys||(Object.keys=function(){"use strict";var i=Object.prototype.hasOwnProperty,s=!{toString:null}.propertyIsEnumerable("toString"),c=["toString","toLocaleString","valueOf","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","constructor"],o=c.length;return function(e){if("object"!=typeof e&&("function"!=typeof e||null===e))throw new TypeError("Object.keys called on non-object");var t,r,n=[];for(t in e)i.call(e,t)&&n.push(t);if(s)for(r=0;r<o;r++)i.call(e,c[r])&&n.push(c[r]);return n}}()),Object.values=Object.values||function(t){var e=Object.prototype.toString.call(t);if(null==t)throw new TypeError("Cannot convert undefined or null to object");if(~["[object String]","[object Object]","[object Array]","[object Function]"].indexOf(e)){if(Object.keys)return Object.keys(t).map(function(e){return t[e]});var r,n=[];for(r in t)t.hasOwnProperty(r)&&n.push(t[r]);return n}return[]},"function"!=typeof window.CustomEvent&&(window.CustomEvent=function(e,t){t=t||{bubbles:!1,cancelable:!1,detail:null};var r=document.createEvent("CustomEvent");return r.initCustomEvent(e,t.bubbles,t.cancelable,t.detail),r}),function(e){var t,i,r,n=navigator.userAgent;function s(){for(var e=document.querySelectorAll("picture > img, img[srcset][sizes]"),t=0;t<e.length;t++)!function(e){var t,r,n=e.parentNode;"PICTURE"===n.nodeName.toUpperCase()?(t=i.cloneNode(),n.insertBefore(t,n.firstElementChild),setTimeout(function(){n.removeChild(t)})):(!e._pfLastSize||e.offsetWidth>e._pfLastSize)&&(e._pfLastSize=e.offsetWidth,r=e.sizes,e.sizes+=",100vw",setTimeout(function(){e.sizes=r}))}(e[t])}function c(){clearTimeout(t),t=setTimeout(s,99)}function o(){c(),r&&r.addListener&&r.addListener(c)}e.HTMLPictureElement&&/ecko/.test(n)&&n.match(/rv\:(\d+)/)&&RegExp.$1<45&&addEventListener("resize",(i=document.createElement("source"),r=e.matchMedia&&matchMedia("(orientation: landscape)"),i.srcset="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==",/^[c|i]|d$/.test(document.readyState||"")?o():document.addEventListener("DOMContentLoaded",o),c))}(window),function(e,s,u){"use strict";function d(e){return" "===e||"\t"===e||"\n"===e||"\f"===e||"\r"===e}function w(e,t){return e.res-t.res}function b(e,t){var r,n,i;if(e&&t)for(i=z.parseSet(t),e=z.makeUrl(e),r=0;r<i.length;r++)if(e===z.makeUrl(i[r].url)){n=i[r];break}return n}function r(e){for(var t,r,n,i=/^(?:[+-]?[0-9]+|[0-9]*\.[0-9]+)(?:[eE][+-]?[0-9]+)?(?:ch|cm|em|ex|in|mm|pc|pt|px|rem|vh|vmin|vmax|vw)$/i,s=/^calc\((?:[0-9a-z \.\+\-\*\/\(\)]+)\)$/i,c=function(e){function t(){i&&(s.push(i),i="")}function r(){s[0]&&(c.push(s),s=[])}for(var n,i="",s=[],c=[],o=0,a=0,u=!1;;){if(""===(n=e.charAt(a)))return t(),r(),c;if(u)"*"!==n||"/"!==e[a+1]?a+=1:(u=!1,a+=2,t());else{if(d(n)){if(e.charAt(a-1)&&d(e.charAt(a-1))||!i){a+=1;continue}if(0===o){t(),a+=1;continue}n=" "}else if("("===n)o+=1;else if(")"===n)--o;else{if(","===n){t(),r(),a+=1;continue}if("/"===n&&"*"===e.charAt(a+1)){u=!0,a+=2;continue}}i+=n,a+=1}}}(e),o=c.length,a=0;a<o;a++)if(r=(t=c[a])[t.length-1],n=r,i.test(n)&&0<=parseFloat(n)||(s.test(n)||("0"===n||"-0"===n||"+0"===n))){if(r=r,t.pop(),0===t.length)return r;if(t=t.join(" "),z.matchesMedia(t))return r}return"100vw"}function t(){}function n(e,t,r,n){e.addEventListener?e.addEventListener(t,r,n||!1):e.attachEvent&&e.attachEvent("on"+t,r)}function E(e,t){return e.w?(e.cWidth=z.calcListLength(t||"100vw"),e.res=e.w/e.cWidth):e.res=e.d,e}s.createElement("picture");var i,l,c,o,a,f,h,p,m,A,g,v,y,S,x,T,O,j,P,z={},C=!1,R=s.createElement("img"),L=R.getAttribute,M=R.setAttribute,U=R.removeAttribute,I=s.documentElement,k={},D={algorithm:""},$="data-pfsrc",B=$+"set",_=navigator.userAgent,N=/rident/.test(_)||/ecko/.test(_)&&_.match(/rv\:(\d+)/)&&35<RegExp.$1,W="currentSrc",Q=/\s+\+?\d+(e\d+)?w/,H=/(\([^)]+\))?\s*(.+)/,G=e.picturefillCFG,F="font-size:100%!important;",q=!0,V={},J={},K=e.devicePixelRatio,X={px:1,in:96},Y=s.createElement("a"),Z=!1,ee=/^[ \t\n\r\u000c]+/,te=/^[, \t\n\r\u000c]+/,re=/^[^ \t\n\r\u000c]+/,ne=/[,]+$/,ie=/^\d+$/,se=/^-?(?:[0-9]+|[0-9]*\.[0-9]+)(?:[eE][+-]?[0-9]+)?$/,ce=(o=/^([\d\.]+)(em|vw|px)$/,a=(_=function(t){var r={};return function(e){return e in r||(r[e]=t(e)),r[e]}})(function(e){return"return "+function(){for(var e=arguments,t=0,r=e[0];++t in e;)r=r.replace(e[t],e[++t]);return r}((e||"").toLowerCase(),/\band\b/g,"&&",/,/g,"||",/min-([a-z-\s]+):/g,"e.$1>=",/max-([a-z-\s]+):/g,"e.$1<=",/calc([^)]+)/g,"($1)",/(\d+[\.]*[\d]*)([a-z]+)/g,"($1 * e.$2)",/^(?!(e.[a-z]|[0-9\.&=|><\+\-\*\(\)\/])).*/gi,"")+";"}),function(e,t){var r;if(!(e in V))if(V[e]=!1,t&&(r=e.match(o)))V[e]=r[1]*X[r[2]];else try{V[e]=new Function("e",a(e))(X)}catch(e){}return V[e]}),oe=function(e){if(C){var t,r,n,i=e||{};if(i.elements&&1===i.elements.nodeType&&("IMG"===i.elements.nodeName.toUpperCase()?i.elements=[i.elements]:(i.context=i.elements,i.elements=null)),n=(t=i.elements||z.qsa(i.context||s,i.reevaluate||i.reselect?z.sel:z.selShort)).length){for(z.setupRun(i),Z=!0,r=0;r<n;r++)z.fillImg(t[r],i);z.teardownRun(i)}}};function ae(){2===T.width&&(z.supSizes=!0),l=z.supSrcset&&!z.supSizes,C=!0,setTimeout(oe)}e.console&&console.warn,W in R||(W="src"),k["image/jpeg"]=!0,k["image/gif"]=!0,k["image/png"]=!0,k["image/svg+xml"]=s.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#Image","1.1"),z.ns=("pf"+(new Date).getTime()).substr(0,9),z.supSrcset="srcset"in R,z.supSizes="sizes"in R,z.supPicture=!!e.HTMLPictureElement,z.supSrcset&&z.supPicture&&!z.supSizes&&(O=s.createElement("img"),R.srcset="data:,a",O.src="data:,a",z.supSrcset=R.complete===O.complete,z.supPicture=z.supSrcset&&z.supPicture),z.supSrcset&&!z.supSizes?(O="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==",(T=s.createElement("img")).onload=ae,T.onerror=ae,T.setAttribute("sizes","9px"),T.srcset=O+" 1w,data:image/gif;base64,R0lGODlhAgABAPAAAP///wAAACH5BAAAAAAALAAAAAACAAEAAAICBAoAOw== 9w",T.src=O):C=!0,z.selShort="picture>img,img[srcset]",z.sel=z.selShort,z.cfg=D,z.DPR=K||1,z.u=X,z.types=k,z.setSize=t,z.makeUrl=_(function(e){return Y.href=e,Y.href}),z.qsa=function(e,t){return"querySelector"in e?e.querySelectorAll(t):[]},z.matchesMedia=function(){return e.matchMedia&&(matchMedia("(min-width: 0.1em)")||{}).matches?z.matchesMedia=function(e){return!e||matchMedia(e).matches}:z.matchesMedia=z.mMQ,z.matchesMedia.apply(this,arguments)},z.mMQ=function(e){return!e||ce(e)},z.calcLength=function(e){e=ce(e,!0)||!1;return e=e<0?!1:e},z.supportsType=function(e){return!e||k[e]},z.parseSize=_(function(e){e=(e||"").match(H);return{media:e&&e[1],length:e&&e[2]}}),z.parseSet=function(e){return e.cands||(e.cands=function(t,l){function e(e){var e=e.exec(t.substring(o));return e?(e=e[0],o+=e.length,e):void 0}function r(){for(var e,t,r,n,i,s,c,o=!1,a={},u=0;u<h.length;u++)n=(c=h[u])[c.length-1],i=c.substring(0,c.length-1),s=parseInt(i,10),c=parseFloat(i),ie.test(i)&&"w"===n?((e||t)&&(o=!0),0===s?o=!0:e=s):se.test(i)&&"x"===n?((e||t||r)&&(o=!0),c<0?o=!0:t=c):ie.test(i)&&"h"===n?((r||t)&&(o=!0),0===s?o=!0:r=s):o=!0;o||(a.url=f,e&&(a.w=e),t&&(a.d=t),r&&(a.h=r),r||t||e||(a.d=1),1===a.d&&(l.has1x=!0),a.set=l,p.push(a))}for(var f,h,n,i,s,c=t.length,o=0,p=[];;){if(e(te),c<=o)return p;f=e(re),h=[],","===f.slice(-1)?(f=f.replace(ne,""),r()):function(){for(e(ee),n="",i="in descriptor";;){if(s=t.charAt(o),"in descriptor"===i)if(d(s))n&&(h.push(n),n="",i="after descriptor");else{if(","===s)return o+=1,n&&h.push(n),r(),0;if("("===s)n+=s,i="in parens";else{if(""===s)return n&&h.push(n),r(),0;n+=s}}else if("in parens"===i)if(")"===s)n+=s,i="in descriptor";else{if(""===s)return h.push(n),r(),0;n+=s}else if("after descriptor"===i&&!d(s)){if(""===s)return r(),0;i="in descriptor",--o}o+=1}}()}}(e.srcset,e)),e.cands},z.getEmValue=function(){var e,t,r,n;return!i&&(e=s.body)&&(t=s.createElement("div"),r=I.style.cssText,n=e.style.cssText,t.style.cssText="position:absolute;left:0;visibility:hidden;display:block;padding:0;border:none;font-size:1em;width:1em;overflow:hidden;clip:rect(0px, 0px, 0px, 0px)",I.style.cssText=F,e.style.cssText=F,e.appendChild(t),i=t.offsetWidth,e.removeChild(t),i=parseFloat(i,10),I.style.cssText=r,e.style.cssText=n),i||16},z.calcListLength=function(e){var t;return e in J&&!D.uT||(t=z.calcLength(r(e)),J[e]=t||X.width),J[e]},z.setRes=function(e){var t;if(e)for(var r=0,n=(t=z.parseSet(e)).length;r<n;r++)E(t[r],e.sizes);return t},z.setRes.res=E,z.applySetCandidate=function(e,t){if(e.length){var r,n,i,s,c,o,a=t[z.ns],u=z.DPR,l=a.curSrc||t[W],f=a.curCan||(v=t,y=l,f=e[0].set,(f=b(y,f=!f&&y?(f=v[z.ns].sets)&&f[f.length-1]:f))&&(y=z.makeUrl(y),v[z.ns].curSrc=y,(v[z.ns].curCan=f).res||E(f,f.set.sizes)),f);if(f&&f.set===e[0].set&&((o=N&&!t.complete&&f.res-.1>u)||(f.cached=!0,f.res>=u&&(c=f))),!c)for(e.sort(w),c=e[(s=e.length)-1],n=0;n<s;n++)if((r=e[n]).res>=u){c=e[i=n-1]&&(o||l!==z.makeUrl(r.url))&&(h=e[i].res,p=r.res,d=u,m=e[i].cached,g=A=void 0,h="saveData"===D.algorithm?2.7<h?d+1:(g=(p-d)*(A=Math.pow(h-.6,1.5)),m&&(g+=.1*A),h+g):1<d?Math.sqrt(h*p):h,d<h)?e[i]:r;break}c&&(f=z.makeUrl(c.url),a.curSrc=f,a.curCan=c,f!==l&&z.setSrc(t,c),z.setSize(t))}var h,p,d,m,A,g,v,y},z.setSrc=function(e,t){e.src=t.url,"image/svg+xml"===t.set.type&&(t=e.style.width,e.style.width=e.offsetWidth+1+"px",e.offsetWidth+1&&(e.style.width=t))},z.getSet=function(e){for(var t,r,n=!1,i=e[z.ns].sets,s=0;s<i.length&&!n;s++)if((t=i[s]).srcset&&z.matchesMedia(t.media)&&(r=z.supportsType(t.type))){n=t="pending"===r?r:t;break}return n},z.parseSets=function(e,c,t){var r,n,i,s,o=c&&"PICTURE"===c.nodeName.toUpperCase(),a=e[z.ns];a.src!==u&&!t.src||(a.src=L.call(e,"src"),a.src?M.call(e,$,a.src):U.call(e,$)),a.srcset!==u&&!t.srcset&&z.supSrcset&&!e.srcset||(r=L.call(e,"srcset"),a.srcset=r,s=!0),a.sets=[],o&&(a.pic=!0,function(e){for(var t,r,n=c.getElementsByTagName("source"),i=0,s=n.length;i<s;i++)(t=n[i])[z.ns]=!0,(r=t.getAttribute("srcset"))&&e.push({srcset:r,media:t.getAttribute("media"),type:t.getAttribute("type"),sizes:t.getAttribute("sizes")})}(a.sets)),a.srcset?(n={srcset:a.srcset,sizes:L.call(e,"sizes")},a.sets.push(n),(i=(l||a.src)&&Q.test(a.srcset||""))||!a.src||b(a.src,n)||n.has1x||(n.srcset+=", "+a.src,n.cands.push({url:a.src,d:1,set:n}))):a.src&&a.sets.push({srcset:a.src,sizes:null}),a.curCan=null,a.curSrc=u,a.supported=!(o||n&&!z.supSrcset||i&&!z.supSizes),s&&z.supSrcset&&!a.supported&&(r?(M.call(e,B,r),e.srcset=""):U.call(e,B)),a.supported&&!a.srcset&&(!a.src&&e.src||e.src!==z.makeUrl(a.src))&&(null===a.src?e.removeAttribute("src"):e.src=a.src),a.parsed=!0},z.fillImg=function(e,t){var r,n=t.reselect||t.reevaluate;e[z.ns]||(e[z.ns]={}),r=e[z.ns],!n&&r.evaled===c||(r.parsed&&!t.reevaluate||z.parseSets(e,e.parentNode,t),r.supported?r.evaled=c:(t=e,r=z.getSet(t),e=!1,"pending"!==r&&(e=c,r&&(r=z.setRes(r),z.applySetCandidate(r,t))),t[z.ns].evaled=e))},z.setupRun=function(){Z&&!q&&K===e.devicePixelRatio||(q=!1,K=e.devicePixelRatio,V={},J={},z.DPR=K||1,X.width=Math.max(e.innerWidth||0,I.clientWidth),X.height=Math.max(e.innerHeight||0,I.clientHeight),X.vw=X.width/100,X.vh=X.height/100,c=[X.height,X.width,K].join("-"),X.em=z.getEmValue(),X.rem=X.em)},z.supPicture?(oe=t,z.fillImg=t):(v=e.attachEvent?/d$|^c/:/d$|^c|^i/,y=function(){var e=s.readyState||"";S=setTimeout(y,"loading"===e?200:999),s.body&&(z.fillImgs(),(f=f||v.test(e))&&clearTimeout(S))},S=setTimeout(y,s.body?9:99),x=I.clientHeight,n(e,"resize",(h=function(){q=Math.max(e.innerWidth||0,I.clientWidth)!==X.width||I.clientHeight!==x,x=I.clientHeight,q&&z.fillImgs()},p=99,g=function(){var e=new Date-A;e<p?m=setTimeout(g,p-e):(m=null,h())},function(){A=new Date,m=m||setTimeout(g,p)})),n(s,"readystatechange",y)),z.picturefill=oe,z.fillImgs=oe,z.teardownRun=t,oe._=z,e.picturefillCFG={pf:z,push:function(e){var t=e.shift();"function"==typeof z[t]?z[t].apply(z,e):(D[t]=e[0],Z&&z.fillImgs({reselect:!0}))}};for(;G&&G.length;)e.picturefillCFG.push(G.shift());e.picturefill=oe,"object"==typeof module&&"object"==typeof module.exports?module.exports=oe:"function"==typeof define&&define.amd&&define("picturefill",function(){return oe}),z.supPicture||(k["image/webp"]=(j="image/webp",_="data:image/webp;base64,UklGRkoAAABXRUJQVlA4WAoAAAAQAAAAAAAAAAAAQUxQSAwAAAABBxAR/Q9ERP8DAABWUDggGAAAADABAJ0BKgEAAQADADQlpAADcAD++/1QAA==",(P=new e.Image).onerror=function(){k[j]=!1,oe()},P.onload=function(){k[j]=1===P.width,oe()},P.src=_,"pending"))}(window,document);var URLSearchParams=URLSearchParams||function(){"use strict";function f(e){var t,r,n,i,s,c,o=Object.create(null);if(this[u]=o,e)if("string"==typeof e)for(s=0,c=(i=(e="?"===e.charAt(0)?e.slice(1):e).split("&")).length;s<c;s++)-1<(t=(n=i[s]).indexOf("="))?p(o,d(n.slice(0,t)),d(n.slice(t+1))):n.length&&p(o,d(n),"");else if(a(e))for(s=0,c=e.length;s<c;s++)p(o,(n=e[s])[0],n[1]);else if(e.forEach)e.forEach(l,o);else for(r in e)p(o,r,e[r])}var a=Array.isArray,h=f.prototype,t=/[!'\(\)~]|%20|%00/g,r=/\+/g,n={"!":"%21","'":"%27","(":"%28",")":"%29","~":"%7E","%20":"+","%00":"\0"},i=function(e){return n[e]},u="__URLSearchParams__:"+Math.random();function l(e,t){p(this,t,e)}function p(e,t,r){r=a(r)?r.join(","):r;t in e?e[t].push(r):e[t]=[r]}function d(e){return decodeURIComponent(e.replace(r," "))}function c(e){return encodeURIComponent(e).replace(t,i)}h.append=function(e,t){p(this[u],e,t)},h.delete=function(e){delete this[u][e]},h.get=function(e){var t=this[u];return e in t?t[e][0]:null},h.getAll=function(e){var t=this[u];return e in t?t[e].slice(0):[]},h.has=function(e){return e in this[u]},h.set=function(e,t){this[u][e]=[""+t]},h.forEach=function(r,n){var e=this[u];Object.getOwnPropertyNames(e).forEach(function(t){e[t].forEach(function(e){r.call(n,e,t,this)},this)},this)},h.toJSON=function(){return{}},h.toString=function(){var e,t,r,n,i=this[u],s=[];for(t in i)for(r=c(t),e=0,n=i[t];e<n.length;e++)s.push(r+"="+c(n[e]));return s.join("&")};function m(e){var t=e.append;e.append=h.append,f.call(e,e._usp.search.slice(1)),e.append=t}function A(e,t){if(!(e instanceof t))throw new TypeError("'searchParams' accessed on an object that does not implement interface "+t.name)}function e(t){var r,n,i,e=t.prototype,s=v(e,"searchParams"),c=v(e,"href"),o=v(e,"search");function a(e,t){h.append.call(this,e,t),e=this.toString(),i.set.call(this._usp,e?"?"+e:"")}function u(e){h.delete.call(this,e),e=this.toString(),i.set.call(this._usp,e?"?"+e:"")}function l(e,t){h.set.call(this,e,t),e=this.toString(),i.set.call(this._usp,e?"?"+e:"")}!s&&o&&o.set&&(i=o,n=function(e,t){return e.append=a,e.delete=u,e.set=l,g(e,"_usp",{configurable:!0,writable:!0,value:t})},r=function(e,t){return g(e,"_searchParams",{configurable:!0,writable:!0,value:n(t,e)}),t},Object.defineProperties(e,{href:{get:function(){return c.get.call(this)},set:function(e){var t=this._searchParams;c.set.call(this,e),t&&m(t)}},search:{get:function(){return o.get.call(this)},set:function(e){var t=this._searchParams;o.set.call(this,e),t&&m(t)}},searchParams:{get:function(){return A(this,t),this._searchParams||r(this,new f(this.search.slice(1)))},set:function(e){A(this,t),r(this,e)}}}))}var g=Object.defineProperty,v=Object.getOwnPropertyDescriptor;return e(HTMLAnchorElement),/^function|object$/.test(typeof URL)&&URL.prototype&&e(URL),f}();!function(e){var n=function(){try{return!!Symbol.iterator}catch(e){return!1}}();"forEach"in e||(e.forEach=function(r,n){var e=Object.create(null);this.toString().replace(/=[\s\S]*?(?:&|$)/g,"=").split("=").forEach(function(t){!t.length||t in e||(e[t]=this.getAll(t)).forEach(function(e){r.call(n,e,t,this)},this)},this)}),"keys"in e||(e.keys=function(){var r=[];this.forEach(function(e,t){r.push(t)});var e={next:function(){var e=r.shift();return{done:void 0===e,value:e}}};return n&&(e[Symbol.iterator]=function(){return e}),e}),"values"in e||(e.values=function(){var t=[];this.forEach(function(e){t.push(e)});var e={next:function(){var e=t.shift();return{done:void 0===e,value:e}}};return n&&(e[Symbol.iterator]=function(){return e}),e}),"entries"in e||(e.entries=function(){var r=[];this.forEach(function(e,t){r.push([t,e])});var e={next:function(){var e=r.shift();return{done:void 0===e,value:e}}};return n&&(e[Symbol.iterator]=function(){return e}),e}),!n||Symbol.iterator in e||(e[Symbol.iterator]=e.entries),"sort"in e||(e.sort=function(){for(var e,t,r,n=this.entries(),i=n.next(),s=i.done,c=[],o=Object.create(null);!s;)t=(r=i.value)[0],c.push(t),t in o||(o[t]=[]),o[t].push(r[1]),s=(i=n.next()).done;for(c.sort(),e=0;e<c.length;e++)this.delete(c[e]);for(e=0;e<c.length;e++)t=c[e],this.append(t,o[t].shift())})}(URLSearchParams.prototype),navigator.userAgent.match(/Trident\/7\./)&&document.body.addEventListener("mousewheel",function(e){e.preventDefault();var t=e.wheelDelta,e=window.pageYOffset;window.scrollTo(0,e-t)});