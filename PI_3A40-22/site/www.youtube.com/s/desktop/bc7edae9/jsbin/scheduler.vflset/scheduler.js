(function(){/*

 Copyright The Closure Library Authors.
 SPDX-License-Identifier: Apache-2.0
*/
'use strict';var h,l="function"==typeof Object.defineProperties?Object.defineProperty:function(a,b,c){if(a==Array.prototype||a==Object.prototype)return a;a[b]=c.value;return a};
function aa(a){a=["object"==typeof globalThis&&globalThis,a,"object"==typeof window&&window,"object"==typeof self&&self,"object"==typeof global&&global];for(var b=0;b<a.length;++b){var c=a[b];if(c&&c.Math==Math)return c}throw Error("Cannot find global object");}
var ba=aa(this);function ca(a,b){if(b)a:{var c=ba;a=a.split(".");for(var d=0;d<a.length-1;d++){var e=a[d];if(!(e in c))break a;c=c[e]}a=a[a.length-1];d=c[a];b=b(d);b!=d&&null!=b&&l(c,a,{configurable:!0,writable:!0,value:b})}}
ca("Symbol",function(a){function b(f){if(this instanceof b)throw new TypeError("Symbol is not a constructor");return new c(d+(f||"")+"_"+e++,f)}
function c(f,g){this.g=f;l(this,"description",{configurable:!0,writable:!0,value:g})}
if(a)return a;c.prototype.toString=function(){return this.g};
var d="jscomp_symbol_"+(1E9*Math.random()>>>0)+"_",e=0;return b});
var da="function"==typeof Object.create?Object.create:function(a){function b(){}
b.prototype=a;return new b},m;
if("function"==typeof Object.setPrototypeOf)m=Object.setPrototypeOf;else{var n;a:{var ea={a:!0},p={};try{p.__proto__=ea;n=p.a;break a}catch(a){}n=!1}m=n?function(a,b){a.__proto__=b;if(a.__proto__!==b)throw new TypeError(a+" is not extensible");return a}:null}var q=m;
function r(a,b){a.prototype=da(b.prototype);a.prototype.constructor=a;if(q)q(a,b);else for(var c in b)if("prototype"!=c)if(Object.defineProperties){var d=Object.getOwnPropertyDescriptor(b,c);d&&Object.defineProperty(a,c,d)}else a[c]=b[c];a.V=b.prototype}
var u=this||self;function v(a){a=a.split(".");for(var b=u,c=0;c<a.length;c++)if(b=b[a[c]],null==b)return null;return b}
function fa(a,b,c){return a.call.apply(a.bind,arguments)}
function ha(a,b,c){if(!a)throw Error();if(2<arguments.length){var d=Array.prototype.slice.call(arguments,2);return function(){var e=Array.prototype.slice.call(arguments);Array.prototype.unshift.apply(e,d);return a.apply(b,e)}}return function(){return a.apply(b,arguments)}}
function w(a,b,c){Function.prototype.bind&&-1!=Function.prototype.bind.toString().indexOf("native code")?w=fa:w=ha;return w.apply(null,arguments)}
function x(a,b){a=a.split(".");var c=u;a[0]in c||"undefined"==typeof c.execScript||c.execScript("var "+a[0]);for(var d;a.length&&(d=a.shift());)a.length||void 0===b?c[d]&&c[d]!==Object.prototype[d]?c=c[d]:c=c[d]={}:c[d]=b}
;var y={},z=null;var B="function"===typeof Uint8Array;var C="function"===typeof Symbol&&"symbol"===typeof Symbol()?Symbol(void 0):void 0;function D(a){var b;C?b=a[C]:b=a.g;return null==b?0:b}
function E(a){Object.isFrozen(a)||(C?a[C]|=1:void 0!==a.g?a.g|=1:Object.defineProperties(a,{g:{value:1,configurable:!0,writable:!0,enumerable:!1}}));return a}
;function ia(a){return null!==a&&"object"===typeof a&&!Array.isArray(a)&&a.constructor===Object}
var ja=Object.freeze(E([]));function ka(a){a=a.j;if(Array.isArray(a)&&D(a)&2)throw Error("Cannot mutate an immutable Message");}
var la="undefined"!=typeof Symbol&&"undefined"!=typeof Symbol.hasInstance;function ma(a){var b=na;b=void 0===b?oa:b;return pa(a,b)}
function qa(a,b){if(null!=a){if(Array.isArray(a))a=pa(a,b);else if(ia(a)){var c={},d;for(d in a)c[d]=qa(a[d],b);a=c}else a=b(a);return a}}
function pa(a,b){for(var c=a.slice(),d=0;d<c.length;d++)c[d]=qa(c[d],b);Array.isArray(a)&&D(a)&1&&E(c);return c}
function na(a){if(a&&"object"==typeof a&&a.toJSON)return a.toJSON();a:switch(typeof a){case "number":a=isFinite(a)?a:String(a);break a;case "object":if(a&&!Array.isArray(a)&&B&&null!=a&&a instanceof Uint8Array){var b;void 0===b&&(b=0);if(!z){z={};for(var c="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789".split(""),d=["+/=","+/","-_=","-_.","-_"],e=0;5>e;e++){var f=c.concat(d[e].split(""));y[e]=f;for(var g=0;g<f.length;g++){var k=f[g];void 0===z[k]&&(z[k]=g)}}}b=y[b];c=Array(Math.floor(a.length/
3));d=b[64]||"";for(e=f=0;f<a.length-2;f+=3){var t=a[f],A=a[f+1];k=a[f+2];g=b[t>>2];t=b[(t&3)<<4|A>>4];A=b[(A&15)<<2|k>>6];k=b[k&63];c[e++]=""+g+t+A+k}g=0;k=d;switch(a.length-f){case 2:g=a[f+1],k=b[(g&15)<<2]||d;case 1:a=a[f],c[e]=""+b[a>>2]+b[(a&3)<<4|g>>4]+k+d}a=c.join("")}}return Array.isArray(a)?ma(a):a}
function oa(a){return B&&null!=a&&a instanceof Uint8Array?new Uint8Array(a):a}
;var ra;function F(a,b,c){var d=ra;ra=null;a||(a=d);d=this.constructor.h;a||(a=d?[d]:[]);this.g=(d?0:-1)-(this.constructor.g||0);this.l=void 0;this.j=a;a:{d=this.j.length;a=d-1;if(d&&(d=this.j[a],ia(d))){this.h=a-this.g;this.i=d;break a}void 0!==b&&-1<b?(this.h=Math.max(b,a+1-this.g),this.i=void 0):this.h=Number.MAX_VALUE}if(c)for(b=0;b<c.length;b++)if(a=c[b],a<this.h)a+=this.g,(d=this.j[a])?Array.isArray(d)&&E(d):this.j[a]=ja;else{d=this.i||(this.i=this.j[this.h+this.g]={});var e=d[a];e?Array.isArray(e)&&
E(e):d[a]=ja}}
F.prototype.toJSON=function(){return ma(this.j)};
F.prototype.toString=function(){return this.j.toString()};function G(){F.apply(this,arguments)}
r(G,F);function sa(){var a={};Object.defineProperties(G,(a[Symbol.hasInstance]={value:Object[Symbol.hasInstance],configurable:!1,writable:!1,enumerable:!1},a))}
la&&sa();function ta(a,b){var c=this.g,d=!0;d=void 0===d?!1:d;ka(a);a.l||(a.l={});var e=b?b.j:b;a.l[c]=b;b=d;b=void 0===b?!1:b;ka(a);c<a.h&&!b?a.j[c+a.g]=e:(a.i||(a.i=a.j[a.h+a.g]={}))[c]=e;return a}
;function H(){G.apply(this,arguments)}
r(H,G);function ua(){var a={};Object.defineProperties(H,(a[Symbol.hasInstance]={value:Object[Symbol.hasInstance],configurable:!1,writable:!1,enumerable:!1},a))}
la&&ua();function I(){this.u=this.u;this.C=this.C}
I.prototype.u=!1;I.prototype.dispose=function(){this.u||(this.u=!0,this.H())};
I.prototype.H=function(){if(this.C)for(;this.C.length;)this.C.shift()()};function J(a){H.call(this,a,1)}
r(J,H);function K(a){H.call(this,a)}
r(K,H);var va=new function(a){this.g=a;this.h=ta}(406606992,K);function wa(){K.apply(this,arguments)}
r(wa,K);var L,M,N,O=u.window,P=(null===(L=null===O||void 0===O?void 0:O.yt)||void 0===L?void 0:L.config_)||(null===(M=null===O||void 0===O?void 0:O.ytcfg)||void 0===M?void 0:M.data_)||{},xa=(null===(N=null===O||void 0===O?void 0:O.ytcfg)||void 0===N?void 0:N.obfuscatedData_)||[];function ya(){J.apply(this,arguments)}
r(ya,J);var za=new ya(xa),Aa=P.EXPERIMENT_FLAGS;if(!Aa||!Aa.jspb_i18n_extension){var Ba=new wa;va.h(za,Ba)}x("yt.config_",P);x("yt.configJspb_",xa);function Q(a,b){return a in P?P[a]:b}
;function Ca(a,b){a=Da(a);return void 0===a&&void 0!==b?b:Number(a||0)}
function Da(a){var b=Q("EXPERIMENTS_FORCED_FLAGS",{});return void 0!==b[a]?b[a]:Q("EXPERIMENT_FLAGS",{})[a]}
;var Ea=Ca("web_emulated_idle_callback_delay",300),Fa=1E3/60-3;
function R(a){a=void 0===a?{}:a;I.call(this);this.g=[];this.g[8]=[];this.g[4]=[];this.g[3]=[];this.g[2]=[];this.g[1]=[];this.g[0]=[];this.l=0;this.O=a.timeout||1;this.i={};this.s=Fa;this.D=this.h=this.o=0;this.F=this.m=!1;this.v=[];this.I=w(this.S,this);this.N=w(this.U,this);this.K=w(this.P,this);this.L=w(this.R,this);this.M=w(this.T,this);this.B=this.G=!1;var b;if(b=!!window.requestIdleCallback)b=Da("disable_scheduler_requestIdleCallback"),b=!("string"===typeof b&&"false"===b?0:b);this.J=b;(this.A=
!!a.useRaf&&!!window.requestAnimationFrame)&&document.addEventListener("visibilitychange",this.I)}
r(R,I);function Ga(a,b){var c=Date.now();S(b);b=Date.now()-c;a.m||(a.s-=b)}
function Ha(a,b,c,d){++a.D;if(10==c)return Ga(a,b),a.D;var e=a.D;a.i[e]=b;a.m&&!d?a.v.push({id:e,priority:c}):(a.g[c].push(e),a.F||a.m||(0!=a.h&&T(a)!=a.o&&U(a),a.start()));return e}
function Ia(a){a.v.length=0;for(var b=4;0<=b;b--)a.g[b].length=0;a.g[8].length=0;a.i={};U(a)}
function T(a){if(a.g[8].length){if(a.B)return 4;if(!document.hidden&&a.A)return 3}for(var b=4;b>=a.l;b--)if(0<a.g[b].length)return 0<b?!document.hidden&&a.A?3:2:1;return 0}
function S(a){try{a()}catch(b){(a=v("yt.logging.errors.log"))&&a(b)}}
function Ja(a){if(a.g[8].length)return!0;for(var b=3;0<=b;b--)if(a.g[b].length)return!0;return!1}
h=R.prototype;h.R=function(a){var b=void 0;a&&(b=a.timeRemaining());this.G=!0;V(this,b);this.G=!1};
h.U=function(){V(this)};
h.P=function(){Ka(this)};
h.T=function(){this.B=!0;var a=T(this);4==a&&a!=this.o&&(U(this),this.start());V(this);this.B=!1};
h.S=function(){document.hidden||Ka(this);this.h&&(U(this),this.start())};
function Ka(a){U(a);a.m=!0;for(var b=Date.now(),c=a.g[8];c.length;){var d=c.shift(),e=a.i[d];delete a.i[d];e&&S(e)}La(a);a.m=!1;Ja(a)&&a.start();a.s-=Date.now()-b}
function La(a){for(var b=0,c=a.v.length;b<c;b++){var d=a.v[b];a.g[d.priority].push(d.id)}a.v.length=0}
function V(a,b){a.B&&4==a.o&&a.h||U(a);a.m=!0;b=Date.now()+(b||a.s);for(var c=a.g[4];c.length;){var d=c.shift(),e=a.i[d];delete a.i[d];e&&S(e)}c=a.G?0:1;c=a.l>c?a.l:c;if(!(Date.now()>=b)){do{a:{d=a;e=c;for(var f=3;f>=e;f--)for(var g=d.g[f];g.length;){var k=g.shift(),t=d.i[k];delete d.i[k];if(t){d=t;break a}}d=null}d&&S(d)}while(d&&Date.now()<b)}a.m=!1;La(a);a.s=Fa;Ja(a)&&a.start()}
h.start=function(){this.F=!1;if(0==this.h)switch(this.o=T(this),this.o){case 1:var a=this.L;this.h=this.J?window.requestIdleCallback(a,{timeout:3E3}):window.setTimeout(a,Ea);break;case 2:this.h=window.setTimeout(this.N,this.O);break;case 3:this.h=window.requestAnimationFrame(this.M);break;case 4:this.h=window.setTimeout(this.K,0)}};
function U(a){if(a.h){switch(a.o){case 1:var b=a.h;a.J?window.cancelIdleCallback(b):window.clearTimeout(b);break;case 2:case 4:window.clearTimeout(a.h);break;case 3:window.cancelAnimationFrame(a.h)}a.h=0}}
h.H=function(){Ia(this);U(this);this.A&&document.removeEventListener("visibilitychange",this.I);I.prototype.H.call(this)};var W=v("yt.scheduler.instance.timerIdMap_")||{},Ma=Ca("kevlar_tuner_scheduler_soft_state_timer_ms",800),X=0,Y=0;function Z(){var a=v("ytglobal.schedulerInstanceInstance_");if(!a||a.u)a=new R(Q("scheduler",void 0)||{}),x("ytglobal.schedulerInstanceInstance_",a);return a}
function Na(){var a=v("ytglobal.schedulerInstanceInstance_");a&&(a&&"function"==typeof a.dispose&&a.dispose(),x("ytglobal.schedulerInstanceInstance_",null))}
function Oa(){Ia(Z())}
function Pa(a,b,c){if(0==c||void 0===c)return c=void 0===c,-Ha(Z(),a,b,c);var d=window.setTimeout(function(){var e=Ha(Z(),a,b);W[d]=e},c);
return d}
function Qa(a){var b=Z();Ga(b,a)}
function Ra(a){var b=Z();if(0>a)delete b.i[-a];else{var c=W[a];c?(delete b.i[c],delete W[a]):window.clearTimeout(a)}}
function Sa(a){var b=v("ytcsi.tick");b&&b(a)}
function Ta(){Sa("jse");Ua()}
function Ua(){window.clearTimeout(X);Z().start()}
function Va(){var a=Z();U(a);a.F=!0;window.clearTimeout(X);X=window.setTimeout(Ta,Ma)}
function Wa(){window.clearTimeout(Y);Y=window.setTimeout(function(){Sa("jset");Xa(0)},Ma)}
function Xa(a){Wa();var b=Z();b.l=a;b.start()}
function Ya(a){Wa();var b=Z();b.l>a&&(b.l=a,b.start())}
function Za(){window.clearTimeout(Y);var a=Z();a.l=0;a.start()}
;v("yt.scheduler.initialized")||(x("yt.scheduler.instance.dispose",Na),x("yt.scheduler.instance.addJob",Pa),x("yt.scheduler.instance.addImmediateJob",Qa),x("yt.scheduler.instance.cancelJob",Ra),x("yt.scheduler.instance.cancelAllJobs",Oa),x("yt.scheduler.instance.start",Ua),x("yt.scheduler.instance.pause",Va),x("yt.scheduler.instance.setPriorityThreshold",Xa),x("yt.scheduler.instance.enablePriorityThreshold",Ya),x("yt.scheduler.instance.clearPriorityThreshold",Za),x("yt.scheduler.initialized",!0));}).call(this);
