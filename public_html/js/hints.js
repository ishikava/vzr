"use strict";function _typeof(t){return(_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function _inherits(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&_setPrototypeOf(t,e)}function _setPrototypeOf(t,e){return(_setPrototypeOf=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}function _createSuper(n){var o=_isNativeReflectConstruct();return function(){var t,e=_getPrototypeOf(n);return _possibleConstructorReturn(this,o?(t=_getPrototypeOf(this).constructor,Reflect.construct(e,arguments,t)):e.apply(this,arguments))}}function _possibleConstructorReturn(t,e){return!e||"object"!==_typeof(e)&&"function"!=typeof e?_assertThisInitialized(t):e}function _assertThisInitialized(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}function _isNativeReflectConstruct(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],function(){})),!0}catch(t){return!1}}function _getPrototypeOf(t){return(_getPrototypeOf=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function _classCallCheck(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function _defineProperties(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}function _createClass(t,e,n){return e&&_defineProperties(t.prototype,e),n&&_defineProperties(t,n),t}var Hints=function(){function e(t){_classCallCheck(this,e),this.selector=t,this.duration=[325,275],this.setListeners(),this.checkLabelParent(),this.setCopyActions()}return _createClass(e,[{key:"setListeners",value:function(){tippy(this.selector,{theme:"alfa",trigger:this.isTouchDevice()?"click":"mouseenter",arrow:!0,appendTo:this.getOptionAppendTo(),duration:this.duration})}},{key:"getOptionAppendTo",value:function(){return function(){return document.body}}},{key:"isTouchDevice",value:function(){return"ontouchstart"in window}},{key:"checkLabelParent",value:function(){$(this.selector).on("click",function(t){$(t.currentTarget).parents("label").length&&t.preventDefault()})}},{key:"setCopyActions",value:function(){var r=this;$(this.selector).filter("[data-copy-content]").on("click",function(t){var e,n=$(t.currentTarget),o=n.data("copy-content"),n=n.data("copy-notification")?n.data("copy-notification"):"Скопировано в буфер!";r.copyTextToClipboard(o)&&(e=t.currentTarget._tippy.props.content,console.log(t.currentTarget._tippy),t.currentTarget._tippy.props.duration=0,t.currentTarget._tippy.setContent(n),t.currentTarget._tippy.show(),setTimeout(function(){t.currentTarget._tippy.hide(),t.currentTarget._tippy.props.duration=r.duration,t.currentTarget._tippy.setContent(e)},1e3))})}},{key:"fallbackCopyTextToClipboard",value:function(t){var e=document.createElement("textarea");e.value=t,e.style.top="0",e.style.left="0",e.style.position="fixed",document.body.appendChild(e),e.focus(),e.select();t=!1;try{t=document.execCommand("copy")}catch(t){}return document.body.removeChild(e),t}},{key:"copyTextToClipboard",value:function(t){return navigator.clipboard?(navigator.clipboard.writeText(t).then(function(){},function(){}),!0):this.fallbackCopyTextToClipboard(t)}}]),e}(),HintsHtml=function(){_inherits(e,Hints);var t=_createSuper(e);function e(){return _classCallCheck(this,e),t.apply(this,arguments)}return _createClass(e,[{key:"setListeners",value:function(){tippy(this.selector,{theme:"alfa",trigger:this.isTouchDevice()?"click":"mouseenter",arrow:!0,appendTo:this.getOptionAppendTo(),allowHTML:!0,duration:this.duration,content:function(t){t=$(t).attr("data-tippy-html");return $("[data-tippy-html-content=".concat(t,"]")).html()}})}}]),e}();window.initHints=function(){new Hints("[data-tippy-content]"),new HintsHtml("[data-tippy-html]")},$(function(){window.initHints()});