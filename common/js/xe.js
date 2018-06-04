/*! Copyright (C) NAVER <http://www.navercorp.com> */
/**!
 * @concat modernizr.js + common.js + js_app.js + xml2json.js + xml_handler.js + xml_js_filter.js
 * @brief XE Common JavaScript
 **/
;



window.Modernizr = (function( window, document, undefined ) {

    var version = '2.8.3',

    Modernizr = {},

    enableClasses = true,

    docElement = document.documentElement,

    mod = 'modernizr',
    modElem = document.createElement(mod),
    mStyle = modElem.style,

    inputElem  = document.createElement('input')  ,

    smile = ':)',

    toString = {}.toString,

    prefixes = ' -webkit- -moz- -o- -ms- '.split(' '),



    omPrefixes = 'Webkit Moz O ms',

    cssomPrefixes = omPrefixes.split(' '),

    domPrefixes = omPrefixes.toLowerCase().split(' '),

    ns = {'svg': 'http://www.w3.org/2000/svg'},

    tests = {},
    inputs = {},
    attrs = {},

    classes = [],

    slice = classes.slice,

    featureName, 


    injectElementWithStyles = function( rule, callback, nodes, testnames ) {

      var style, ret, node, docOverflow,
          div = document.createElement('div'),
                body = document.body,
                fakeBody = body || document.createElement('body');

      if ( parseInt(nodes, 10) ) {
                      while ( nodes-- ) {
              node = document.createElement('div');
              node.id = testnames ? testnames[nodes] : mod + (nodes + 1);
              div.appendChild(node);
          }
      }

                style = ['&#173;','<style id="s', mod, '">', rule, '</style>'].join('');
      div.id = mod;
          (body ? div : fakeBody).innerHTML += style;
      fakeBody.appendChild(div);
      if ( !body ) {
                fakeBody.style.background = '';
                fakeBody.style.overflow = 'hidden';
          docOverflow = docElement.style.overflow;
          docElement.style.overflow = 'hidden';
          docElement.appendChild(fakeBody);
      }

      ret = callback(div, rule);
        if ( !body ) {
          fakeBody.parentNode.removeChild(fakeBody);
          docElement.style.overflow = docOverflow;
      } else {
          div.parentNode.removeChild(div);
      }

      return !!ret;

    },



    isEventSupported = (function() {

      var TAGNAMES = {
        'select': 'input', 'change': 'input',
        'submit': 'form', 'reset': 'form',
        'error': 'img', 'load': 'img', 'abort': 'img'
      };

      function isEventSupported( eventName, element ) {

        element = element || document.createElement(TAGNAMES[eventName] || 'div');
        eventName = 'on' + eventName;

            var isSupported = eventName in element;

        if ( !isSupported ) {
                if ( !element.setAttribute ) {
            element = document.createElement('div');
          }
          if ( element.setAttribute && element.removeAttribute ) {
            element.setAttribute(eventName, '');
            isSupported = is(element[eventName], 'function');

                    if ( !is(element[eventName], 'undefined') ) {
              element[eventName] = undefined;
            }
            element.removeAttribute(eventName);
          }
        }

        element = null;
        return isSupported;
      }
      return isEventSupported;
    })(),


    _hasOwnProperty = ({}).hasOwnProperty, hasOwnProp;

    if ( !is(_hasOwnProperty, 'undefined') && !is(_hasOwnProperty.call, 'undefined') ) {
      hasOwnProp = function (object, property) {
        return _hasOwnProperty.call(object, property);
      };
    }
    else {
      hasOwnProp = function (object, property) { 
        return ((property in object) && is(object.constructor.prototype[property], 'undefined'));
      };
    }


    if (!Function.prototype.bind) {
      Function.prototype.bind = function bind(that) {

        var target = this;

        if (typeof target != "function") {
            throw new TypeError();
        }

        var args = slice.call(arguments, 1),
            bound = function () {

            if (this instanceof bound) {

              var F = function(){};
              F.prototype = target.prototype;
              var self = new F();

              var result = target.apply(
                  self,
                  args.concat(slice.call(arguments))
              );
              if (Object(result) === result) {
                  return result;
              }
              return self;

            } else {

              return target.apply(
                  that,
                  args.concat(slice.call(arguments))
              );

            }

        };

        return bound;
      };
    }

    function setCss( str ) {
        mStyle.cssText = str;
    }

    function setCssAll( str1, str2 ) {
        return setCss(prefixes.join(str1 + ';') + ( str2 || '' ));
    }

    function is( obj, type ) {
        return typeof obj === type;
    }

    function contains( str, substr ) {
        return !!~('' + str).indexOf(substr);
    }

    function testProps( props, prefixed ) {
        for ( var i in props ) {
            var prop = props[i];
            if ( !contains(prop, "-") && mStyle[prop] !== undefined ) {
                return prefixed == 'pfx' ? prop : true;
            }
        }
        return false;
    }

    function testDOMProps( props, obj, elem ) {
        for ( var i in props ) {
            var item = obj[props[i]];
            if ( item !== undefined) {

                            if (elem === false) return props[i];

                            if (is(item, 'function')){
                                return item.bind(elem || obj);
                }

                            return item;
            }
        }
        return false;
    }

    function testPropsAll( prop, prefixed, elem ) {

        var ucProp  = prop.charAt(0).toUpperCase() + prop.slice(1),
            props   = (prop + ' ' + cssomPrefixes.join(ucProp + ' ') + ucProp).split(' ');

            if(is(prefixed, "string") || is(prefixed, "undefined")) {
          return testProps(props, prefixed);

            } else {
          props = (prop + ' ' + (domPrefixes).join(ucProp + ' ') + ucProp).split(' ');
          return testDOMProps(props, prefixed, elem);
        }
    }    tests['flexbox'] = function() {
      return testPropsAll('flexWrap');
    };


    tests['flexboxlegacy'] = function() {
        return testPropsAll('boxDirection');
    };


    tests['canvas'] = function() {
        var elem = document.createElement('canvas');
        return !!(elem.getContext && elem.getContext('2d'));
    };

    tests['canvastext'] = function() {
        return !!(Modernizr['canvas'] && is(document.createElement('canvas').getContext('2d').fillText, 'function'));
    };



    tests['webgl'] = function() {
        return !!window.WebGLRenderingContext;
    };


    tests['touch'] = function() {
        var bool;

        if(('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
          bool = true;
        } else {
          injectElementWithStyles(['@media (',prefixes.join('touch-enabled),('),mod,')','{#modernizr{top:9px;position:absolute}}'].join(''), function( node ) {
            bool = node.offsetTop === 9;
          });
        }

        return bool;
    };



    tests['geolocation'] = function() {
        return 'geolocation' in navigator;
    };


    tests['postmessage'] = function() {
      return !!window.postMessage;
    };


    tests['websqldatabase'] = function() {
      return !!window.openDatabase;
    };

    tests['indexedDB'] = function() {
      return !!testPropsAll("indexedDB", window);
    };

    tests['hashchange'] = function() {
      return isEventSupported('hashchange', window) && (document.documentMode === undefined || document.documentMode > 7);
    };

    tests['history'] = function() {
      return !!(window.history && history.pushState);
    };

    tests['draganddrop'] = function() {
        var div = document.createElement('div');
        return ('draggable' in div) || ('ondragstart' in div && 'ondrop' in div);
    };

    tests['websockets'] = function() {
        return 'WebSocket' in window || 'MozWebSocket' in window;
    };


    tests['rgba'] = function() {
        setCss('background-color:rgba(150,255,150,.5)');

        return contains(mStyle.backgroundColor, 'rgba');
    };

    tests['hsla'] = function() {
            setCss('background-color:hsla(120,40%,100%,.5)');

        return contains(mStyle.backgroundColor, 'rgba') || contains(mStyle.backgroundColor, 'hsla');
    };

    tests['multiplebgs'] = function() {
                setCss('background:url(https://),url(https://),red url(https://)');

            return (/(url\s*\(.*?){3}/).test(mStyle.background);
    };    tests['backgroundsize'] = function() {
        return testPropsAll('backgroundSize');
    };

    tests['borderimage'] = function() {
        return testPropsAll('borderImage');
    };



    tests['borderradius'] = function() {
        return testPropsAll('borderRadius');
    };

    tests['boxshadow'] = function() {
        return testPropsAll('boxShadow');
    };

    tests['textshadow'] = function() {
        return document.createElement('div').style.textShadow === '';
    };


    tests['opacity'] = function() {
                setCssAll('opacity:.55');

                    return (/^0.55$/).test(mStyle.opacity);
    };


    tests['cssanimations'] = function() {
        return testPropsAll('animationName');
    };


    tests['csscolumns'] = function() {
        return testPropsAll('columnCount');
    };


    tests['cssgradients'] = function() {
        var str1 = 'background-image:',
            str2 = 'gradient(linear,left top,right bottom,from(#9f9),to(white));',
            str3 = 'linear-gradient(left top,#9f9, white);';

        setCss(
                       (str1 + '-webkit- '.split(' ').join(str2 + str1) +
                       prefixes.join(str3 + str1)).slice(0, -str1.length)
        );

        return contains(mStyle.backgroundImage, 'gradient');
    };


    tests['cssreflections'] = function() {
        return testPropsAll('boxReflect');
    };


    tests['csstransforms'] = function() {
        return !!testPropsAll('transform');
    };


    tests['csstransforms3d'] = function() {

        var ret = !!testPropsAll('perspective');

                        if ( ret && 'webkitPerspective' in docElement.style ) {

                      injectElementWithStyles('@media (transform-3d),(-webkit-transform-3d){#modernizr{left:9px;position:absolute;height:3px;}}', function( node, rule ) {
            ret = node.offsetLeft === 9 && node.offsetHeight === 3;
          });
        }
        return ret;
    };


    tests['csstransitions'] = function() {
        return testPropsAll('transition');
    };



    tests['fontface'] = function() {
        var bool;

        injectElementWithStyles('@font-face {font-family:"font";src:url("https://")}', function( node, rule ) {
          var style = document.getElementById('smodernizr'),
              sheet = style.sheet || style.styleSheet,
              cssText = sheet ? (sheet.cssRules && sheet.cssRules[0] ? sheet.cssRules[0].cssText : sheet.cssText || '') : '';

          bool = /src/i.test(cssText) && cssText.indexOf(rule.split(' ')[0]) === 0;
        });

        return bool;
    };

    tests['generatedcontent'] = function() {
        var bool;

        injectElementWithStyles(['#',mod,'{font:0/0 a}#',mod,':after{content:"',smile,'";visibility:hidden;font:3px/1 a}'].join(''), function( node ) {
          bool = node.offsetHeight >= 3;
        });

        return bool;
    };
    tests['video'] = function() {
        var elem = document.createElement('video'),
            bool = false;

            try {
            if ( bool = !!elem.canPlayType ) {
                bool      = new Boolean(bool);
                bool.ogg  = elem.canPlayType('video/ogg; codecs="theora"')      .replace(/^no$/,'');

                            bool.h264 = elem.canPlayType('video/mp4; codecs="avc1.42E01E"') .replace(/^no$/,'');

                bool.webm = elem.canPlayType('video/webm; codecs="vp8, vorbis"').replace(/^no$/,'');
            }

        } catch(e) { }

        return bool;
    };

    tests['audio'] = function() {
        var elem = document.createElement('audio'),
            bool = false;

        try {
            if ( bool = !!elem.canPlayType ) {
                bool      = new Boolean(bool);
                bool.ogg  = elem.canPlayType('audio/ogg; codecs="vorbis"').replace(/^no$/,'');
                bool.mp3  = elem.canPlayType('audio/mpeg;')               .replace(/^no$/,'');

                                                    bool.wav  = elem.canPlayType('audio/wav; codecs="1"')     .replace(/^no$/,'');
                bool.m4a  = ( elem.canPlayType('audio/x-m4a;')            ||
                              elem.canPlayType('audio/aac;'))             .replace(/^no$/,'');
            }
        } catch(e) { }

        return bool;
    };


    tests['localstorage'] = function() {
        try {
            localStorage.setItem(mod, mod);
            localStorage.removeItem(mod);
            return true;
        } catch(e) {
            return false;
        }
    };

    tests['sessionstorage'] = function() {
        try {
            sessionStorage.setItem(mod, mod);
            sessionStorage.removeItem(mod);
            return true;
        } catch(e) {
            return false;
        }
    };


    tests['webworkers'] = function() {
        return !!window.Worker;
    };


    tests['applicationcache'] = function() {
        return !!window.applicationCache;
    };


    tests['svg'] = function() {
        return !!document.createElementNS && !!document.createElementNS(ns.svg, 'svg').createSVGRect;
    };

    tests['inlinesvg'] = function() {
      var div = document.createElement('div');
      div.innerHTML = '<svg/>';
      return (div.firstChild && div.firstChild.namespaceURI) == ns.svg;
    };

    tests['smil'] = function() {
        return !!document.createElementNS && /SVGAnimate/.test(toString.call(document.createElementNS(ns.svg, 'animate')));
    };


    tests['svgclippaths'] = function() {
        return !!document.createElementNS && /SVGClipPath/.test(toString.call(document.createElementNS(ns.svg, 'clipPath')));
    };

    function webforms() {
                                            Modernizr['input'] = (function( props ) {
            for ( var i = 0, len = props.length; i < len; i++ ) {
                attrs[ props[i] ] = !!(props[i] in inputElem);
            }
            if (attrs.list){
                                  attrs.list = !!(document.createElement('datalist') && window.HTMLDataListElement);
            }
            return attrs;
        })('autocomplete autofocus list placeholder max min multiple pattern required step'.split(' '));
                            Modernizr['inputtypes'] = (function(props) {

            for ( var i = 0, bool, inputElemType, defaultView, len = props.length; i < len; i++ ) {

                inputElem.setAttribute('type', inputElemType = props[i]);
                bool = inputElem.type !== 'text';

                                                    if ( bool ) {

                    inputElem.value         = smile;
                    inputElem.style.cssText = 'position:absolute;visibility:hidden;';

                    if ( /^range$/.test(inputElemType) && inputElem.style.WebkitAppearance !== undefined ) {

                      docElement.appendChild(inputElem);
                      defaultView = document.defaultView;

                                        bool =  defaultView.getComputedStyle &&
                              defaultView.getComputedStyle(inputElem, null).WebkitAppearance !== 'textfield' &&
                                                                                  (inputElem.offsetHeight !== 0);

                      docElement.removeChild(inputElem);

                    } else if ( /^(search|tel)$/.test(inputElemType) ){
                                                                                    } else if ( /^(url|email)$/.test(inputElemType) ) {
                                        bool = inputElem.checkValidity && inputElem.checkValidity() === false;

                    } else {
                                        bool = inputElem.value != smile;
                    }
                }

                inputs[ props[i] ] = !!bool;
            }
            return inputs;
        })('search tel url email datetime date month week time datetime-local number range color'.split(' '));
        }
    for ( var feature in tests ) {
        if ( hasOwnProp(tests, feature) ) {
                                    featureName  = feature.toLowerCase();
            Modernizr[featureName] = tests[feature]();

            classes.push((Modernizr[featureName] ? '' : 'no-') + featureName);
        }
    }

    Modernizr.input || webforms();


     Modernizr.addTest = function ( feature, test ) {
       if ( typeof feature == 'object' ) {
         for ( var key in feature ) {
           if ( hasOwnProp( feature, key ) ) {
             Modernizr.addTest( key, feature[ key ] );
           }
         }
       } else {

         feature = feature.toLowerCase();

         if ( Modernizr[feature] !== undefined ) {
                                              return Modernizr;
         }

         test = typeof test == 'function' ? test() : test;

         if (typeof enableClasses !== "undefined" && enableClasses) {
           docElement.className+=" modernizr-" + (test ? '' : 'no-') + feature;
         }
         Modernizr[feature] = test;

       }

       return Modernizr; 
     };


    setCss('');
    modElem = inputElem = null;

    ;(function(window, document) {
                var version = '3.7.0';

            var options = window.html5 || {};

            var reSkip = /^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i;

            var saveClones = /^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i;

            var supportsHtml5Styles;

            var expando = '_html5shiv';

            var expanID = 0;

            var expandoData = {};

            var supportsUnknownElements;

        (function() {
          try {
            var a = document.createElement('a');
            a.innerHTML = '<xyz></xyz>';
                    supportsHtml5Styles = ('hidden' in a);

            supportsUnknownElements = a.childNodes.length == 1 || (function() {
                        (document.createElement)('a');
              var frag = document.createDocumentFragment();
              return (
                typeof frag.cloneNode == 'undefined' ||
                typeof frag.createDocumentFragment == 'undefined' ||
                typeof frag.createElement == 'undefined'
              );
            }());
          } catch(e) {
                    supportsHtml5Styles = true;
            supportsUnknownElements = true;
          }

        }());

            function addStyleSheet(ownerDocument, cssText) {
          var p = ownerDocument.createElement('p'),
          parent = ownerDocument.getElementsByTagName('head')[0] || ownerDocument.documentElement;

          p.innerHTML = 'x<style>' + cssText + '</style>';
          return parent.insertBefore(p.lastChild, parent.firstChild);
        }

            function getElements() {
          var elements = html5.elements;
          return typeof elements == 'string' ? elements.split(' ') : elements;
        }

            function getExpandoData(ownerDocument) {
          var data = expandoData[ownerDocument[expando]];
          if (!data) {
            data = {};
            expanID++;
            ownerDocument[expando] = expanID;
            expandoData[expanID] = data;
          }
          return data;
        }

            function createElement(nodeName, ownerDocument, data){
          if (!ownerDocument) {
            ownerDocument = document;
          }
          if(supportsUnknownElements){
            return ownerDocument.createElement(nodeName);
          }
          if (!data) {
            data = getExpandoData(ownerDocument);
          }
          var node;

          if (data.cache[nodeName]) {
            node = data.cache[nodeName].cloneNode();
          } else if (saveClones.test(nodeName)) {
            node = (data.cache[nodeName] = data.createElem(nodeName)).cloneNode();
          } else {
            node = data.createElem(nodeName);
          }

                                                    return node.canHaveChildren && !reSkip.test(nodeName) && !node.tagUrn ? data.frag.appendChild(node) : node;
        }

            function createDocumentFragment(ownerDocument, data){
          if (!ownerDocument) {
            ownerDocument = document;
          }
          if(supportsUnknownElements){
            return ownerDocument.createDocumentFragment();
          }
          data = data || getExpandoData(ownerDocument);
          var clone = data.frag.cloneNode(),
          i = 0,
          elems = getElements(),
          l = elems.length;
          for(;i<l;i++){
            clone.createElement(elems[i]);
          }
          return clone;
        }

            function shivMethods(ownerDocument, data) {
          if (!data.cache) {
            data.cache = {};
            data.createElem = ownerDocument.createElement;
            data.createFrag = ownerDocument.createDocumentFragment;
            data.frag = data.createFrag();
          }


          ownerDocument.createElement = function(nodeName) {
                    if (!html5.shivMethods) {
              return data.createElem(nodeName);
            }
            return createElement(nodeName, ownerDocument, data);
          };

          ownerDocument.createDocumentFragment = Function('h,f', 'return function(){' +
                                                          'var n=f.cloneNode(),c=n.createElement;' +
                                                          'h.shivMethods&&(' +
                                                                                                                getElements().join().replace(/[\w\-]+/g, function(nodeName) {
            data.createElem(nodeName);
            data.frag.createElement(nodeName);
            return 'c("' + nodeName + '")';
          }) +
            ');return n}'
                                                         )(html5, data.frag);
        }

            function shivDocument(ownerDocument) {
          if (!ownerDocument) {
            ownerDocument = document;
          }
          var data = getExpandoData(ownerDocument);

          if (html5.shivCSS && !supportsHtml5Styles && !data.hasCSS) {
            data.hasCSS = !!addStyleSheet(ownerDocument,
                                                                                'article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}' +
                                                                                    'mark{background:#FF0;color:#000}' +
                                                                                    'template{display:none}'
                                         );
          }
          if (!supportsUnknownElements) {
            shivMethods(ownerDocument, data);
          }
          return ownerDocument;
        }

            var html5 = {

                'elements': options.elements || 'abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output progress section summary template time video',

                'version': version,

                'shivCSS': (options.shivCSS !== false),

                'supportsUnknownElements': supportsUnknownElements,

                'shivMethods': (options.shivMethods !== false),

                'type': 'default',

                'shivDocument': shivDocument,

                createElement: createElement,

                createDocumentFragment: createDocumentFragment
        };

            window.html5 = html5;

            shivDocument(document);

    }(this, document));

    Modernizr._version      = version;

    Modernizr._prefixes     = prefixes;
    Modernizr._domPrefixes  = domPrefixes;
    Modernizr._cssomPrefixes  = cssomPrefixes;


    Modernizr.hasEvent      = isEventSupported;

    Modernizr.testProp      = function(prop){
        return testProps([prop]);
    };

    Modernizr.testAllProps  = testPropsAll;


    Modernizr.testStyles    = injectElementWithStyles;    docElement.className = docElement.className.replace(/(^|\s)no-js(\s|$)/, '$1$2') +

                                                    (enableClasses ? " modernizr-js modernizr-"+classes.join(" modernizr-") : '');

    return Modernizr;

})(this, this.document);
/*yepnope1.5.4|WTFPL*/
(function(a,b,c){function d(a){return"[object Function]"==o.call(a)}function e(a){return"string"==typeof a}function f(){}function g(a){return!a||"loaded"==a||"complete"==a||"uninitialized"==a}function h(){var a=p.shift();q=1,a?a.t?m(function(){("c"==a.t?B.injectCss:B.injectJs)(a.s,0,a.a,a.x,a.e,1)},0):(a(),h()):q=0}function i(a,c,d,e,f,i,j){function k(b){if(!o&&g(l.readyState)&&(u.r=o=1,!q&&h(),l.onload=l.onreadystatechange=null,b)){"img"!=a&&m(function(){t.removeChild(l)},50);for(var d in y[c])y[c].hasOwnProperty(d)&&y[c][d].onload()}}var j=j||B.errorTimeout,l=b.createElement(a),o=0,r=0,u={t:d,s:c,e:f,a:i,x:j};1===y[c]&&(r=1,y[c]=[]),"object"==a?l.data=c:(l.src=c,l.type=a),l.width=l.height="0",l.onerror=l.onload=l.onreadystatechange=function(){k.call(this,r)},p.splice(e,0,u),"img"!=a&&(r||2===y[c]?(t.insertBefore(l,s?null:n),m(k,j)):y[c].push(l))}function j(a,b,c,d,f){return q=0,b=b||"j",e(a)?i("c"==b?v:u,a,b,this.i++,c,d,f):(p.splice(this.i++,0,a),1==p.length&&h()),this}function k(){var a=B;return a.loader={load:j,i:0},a}var l=b.documentElement,m=a.setTimeout,n=b.getElementsByTagName("script")[0],o={}.toString,p=[],q=0,r="MozAppearance"in l.style,s=r&&!!b.createRange().compareNode,t=s?l:n.parentNode,l=a.opera&&"[object Opera]"==o.call(a.opera),l=!!b.attachEvent&&!l,u=r?"object":l?"script":"img",v=l?"script":u,w=Array.isArray||function(a){return"[object Array]"==o.call(a)},x=[],y={},z={timeout:function(a,b){return b.length&&(a.timeout=b[0]),a}},A,B;B=function(a){function b(a){var a=a.split("!"),b=x.length,c=a.pop(),d=a.length,c={url:c,origUrl:c,prefixes:a},e,f,g;for(f=0;f<d;f++)g=a[f].split("="),(e=z[g.shift()])&&(c=e(c,g));for(f=0;f<b;f++)c=x[f](c);return c}function g(a,e,f,g,h){var i=b(a),j=i.autoCallback;i.url.split(".").pop().split("?").shift(),i.bypass||(e&&(e=d(e)?e:e[a]||e[g]||e[a.split("/").pop().split("?")[0]]),i.instead?i.instead(a,e,f,g,h):(y[i.url]?i.noexec=!0:y[i.url]=1,f.load(i.url,i.forceCSS||!i.forceJS&&"css"==i.url.split(".").pop().split("?").shift()?"c":c,i.noexec,i.attrs,i.timeout),(d(e)||d(j))&&f.load(function(){k(),e&&e(i.origUrl,h,g),j&&j(i.origUrl,h,g),y[i.url]=2})))}function h(a,b){function c(a,c){if(a){if(e(a))c||(j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}),g(a,j,b,0,h);else if(Object(a)===a)for(n in m=function(){var b=0,c;for(c in a)a.hasOwnProperty(c)&&b++;return b}(),a)a.hasOwnProperty(n)&&(!c&&!--m&&(d(j)?j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}:j[n]=function(a){return function(){var b=[].slice.call(arguments);a&&a.apply(this,b),l()}}(k[n])),g(a[n],j,b,n,h))}else!c&&l()}var h=!!a.test,i=a.load||a.both,j=a.callback||f,k=j,l=a.complete||f,m,n;c(h?a.yep:a.nope,!!i),i&&c(i)}var i,j,l=this.yepnope.loader;if(e(a))g(a,0,l,0);else if(w(a))for(i=0;i<a.length;i++)j=a[i],e(j)?g(j,0,l,0):w(j)?B(j):Object(j)===j&&h(j,l);else Object(a)===a&&h(a,l)},B.addPrefix=function(a,b){z[a]=b},B.addFilter=function(a){x.push(a)},B.errorTimeout=1e4,null==b.readyState&&b.addEventListener&&(b.readyState="loading",b.addEventListener("DOMContentLoaded",A=function(){b.removeEventListener("DOMContentLoaded",A,0),b.readyState="complete"},0)),a.yepnope=k(),a.yepnope.executeStack=h,a.yepnope.injectJs=function(a,c,d,e,i,j){var k=b.createElement("script"),l,o,e=e||B.errorTimeout;k.src=a;for(o in d)k.setAttribute(o,d[o]);c=j?h:c||f,k.onreadystatechange=k.onload=function(){!l&&g(k.readyState)&&(l=1,c(),k.onload=k.onreadystatechange=null)},m(function(){l||(l=1,c(1))},e),i?k.onload():n.parentNode.insertBefore(k,n)},a.yepnope.injectCss=function(a,c,d,e,g,i){var e=b.createElement("link"),j,c=i?h:c||f;e.href=a,e.rel="stylesheet",e.type="text/css";for(j in d)e.setAttribute(j,d[j]);g||(n.parentNode.insertBefore(e,n),m(c,0))}})(this,document);
Modernizr.load=function(){yepnope.apply(window,[].slice.call(arguments,0));};
;
/*! URI.js v1.19.0 http://medialize.github.io/URI.js/ */
/* build contains: IPv6.js, punycode.js, SecondLevelDomains.js, URI.js, URITemplate.js, jquery.URI.js */
/*
 URI.js - Mutating URLs
 IPv6 Support

 Version: 1.19.0

 Author: Rodney Rehm
 Web: http://medialize.github.io/URI.js/

 Licensed under
   MIT License http://www.opensource.org/licenses/mit-license

 https://mths.be/punycode v1.4.0 by @mathias  URI.js - Mutating URLs
 Second Level Domain (SLD) Support

 Version: 1.19.0

 Author: Rodney Rehm
 Web: http://medialize.github.io/URI.js/

 Licensed under
   MIT License http://www.opensource.org/licenses/mit-license

 URI.js - Mutating URLs

 Version: 1.19.0

 Author: Rodney Rehm
 Web: http://medialize.github.io/URI.js/

 Licensed under
   MIT License http://www.opensource.org/licenses/mit-license

 URI.js - Mutating URLs
 URI Template Support - http://tools.ietf.org/html/rfc6570

 Version: 1.19.0

 Author: Rodney Rehm
 Web: http://medialize.github.io/URI.js/

 Licensed under
   MIT License http://www.opensource.org/licenses/mit-license

 URI.js - Mutating URLs
 jQuery Plugin

 Version: 1.19.0

 Author: Rodney Rehm
 Web: http://medialize.github.io/URI.js/jquery-uri-plugin.html

 Licensed under
   MIT License http://www.opensource.org/licenses/mit-license

*/
(function(d,k){"object"===typeof module&&module.exports?module.exports=k():"function"===typeof define&&define.amd?define(k):d.IPv6=k(d)})(this,function(d){var k=d&&d.IPv6;return{best:function(g){g=g.toLowerCase().split(":");var d=g.length,b=8;""===g[0]&&""===g[1]&&""===g[2]?(g.shift(),g.shift()):""===g[0]&&""===g[1]?g.shift():""===g[d-1]&&""===g[d-2]&&g.pop();d=g.length;-1!==g[d-1].indexOf(".")&&(b=7);var p;for(p=0;p<d&&""!==g[p];p++);if(p<b)for(g.splice(p,1,"0000");g.length<b;)g.splice(p,0,"0000");
for(p=0;p<b;p++){d=g[p].split("");for(var k=0;3>k;k++)if("0"===d[0]&&1<d.length)d.splice(0,1);else break;g[p]=d.join("")}d=-1;var t=k=0,n=-1,q=!1;for(p=0;p<b;p++)q?"0"===g[p]?t+=1:(q=!1,t>k&&(d=n,k=t)):"0"===g[p]&&(q=!0,n=p,t=1);t>k&&(d=n,k=t);1<k&&g.splice(d,k,"");d=g.length;b="";""===g[0]&&(b=":");for(p=0;p<d;p++){b+=g[p];if(p===d-1)break;b+=":"}""===g[d-1]&&(b+=":");return b},noConflict:function(){d.IPv6===this&&(d.IPv6=k);return this}}});
(function(d){function k(b){throw new RangeError(A[b]);}function g(b,f){for(var h=b.length,d=[];h--;)d[h]=f(b[h]);return d}function u(b,f){var h=b.split("@"),d="";1<h.length&&(d=h[0]+"@",b=h[1]);b=b.replace(E,".");h=b.split(".");h=g(h,f).join(".");return d+h}function b(b){for(var f=[],h=0,d=b.length,g,a;h<d;)g=b.charCodeAt(h++),55296<=g&&56319>=g&&h<d?(a=b.charCodeAt(h++),56320==(a&64512)?f.push(((g&1023)<<10)+(a&1023)+65536):(f.push(g),h--)):f.push(g);return f}function p(b){return g(b,function(b){var f=
"";65535<b&&(b-=65536,f+=y(b>>>10&1023|55296),b=56320|b&1023);return f+=y(b)}).join("")}function B(b,f){return b+22+75*(26>b)-((0!=f)<<5)}function t(b,h,d){var g=0;b=d?f(b/700):b>>1;for(b+=f(b/h);455<b;g+=36)b=f(b/35);return f(g+36*b/(b+38))}function n(b){var h=[],d=b.length,g=0,n=128,a=72,c,e;var m=b.lastIndexOf("-");0>m&&(m=0);for(c=0;c<m;++c)128<=b.charCodeAt(c)&&k("not-basic"),h.push(b.charCodeAt(c));for(m=0<m?m+1:0;m<d;){c=g;var l=1;for(e=36;;e+=36){m>=d&&k("invalid-input");var x=b.charCodeAt(m++);
x=10>x-48?x-22:26>x-65?x-65:26>x-97?x-97:36;(36<=x||x>f((2147483647-g)/l))&&k("overflow");g+=x*l;var r=e<=a?1:e>=a+26?26:e-a;if(x<r)break;x=36-r;l>f(2147483647/x)&&k("overflow");l*=x}l=h.length+1;a=t(g-c,l,0==c);f(g/l)>2147483647-n&&k("overflow");n+=f(g/l);g%=l;h.splice(g++,0,n)}return p(h)}function q(h){var d,g,n,r=[];h=b(h);var a=h.length;var c=128;var e=0;var m=72;for(n=0;n<a;++n){var l=h[n];128>l&&r.push(y(l))}for((d=g=r.length)&&r.push("-");d<a;){var x=2147483647;for(n=0;n<a;++n)l=h[n],l>=c&&
l<x&&(x=l);var q=d+1;x-c>f((2147483647-e)/q)&&k("overflow");e+=(x-c)*q;c=x;for(n=0;n<a;++n)if(l=h[n],l<c&&2147483647<++e&&k("overflow"),l==c){var v=e;for(x=36;;x+=36){l=x<=m?1:x>=m+26?26:x-m;if(v<l)break;var p=v-l;v=36-l;r.push(y(B(l+p%v,0)));v=f(p/v)}r.push(y(B(v,0)));m=t(e,q,d==g);e=0;++d}++e;++c}return r.join("")}var w="object"==typeof exports&&exports&&!exports.nodeType&&exports,h="object"==typeof module&&module&&!module.nodeType&&module,r="object"==typeof global&&global;if(r.global===r||r.window===
r||r.self===r)d=r;var v=/^xn--/,D=/[^\x20-\x7E]/,E=/[\x2E\u3002\uFF0E\uFF61]/g,A={overflow:"Overflow: input needs wider integers to process","not-basic":"Illegal input >= 0x80 (not a basic code point)","invalid-input":"Invalid input"},f=Math.floor,y=String.fromCharCode,C;var z={version:"1.3.2",ucs2:{decode:b,encode:p},decode:n,encode:q,toASCII:function(b){return u(b,function(b){return D.test(b)?"xn--"+q(b):b})},toUnicode:function(b){return u(b,function(b){return v.test(b)?n(b.slice(4).toLowerCase()):
b})}};if("function"==typeof define&&"object"==typeof define.amd&&define.amd)define("punycode",function(){return z});else if(w&&h)if(module.exports==w)h.exports=z;else for(C in z)z.hasOwnProperty(C)&&(w[C]=z[C]);else d.punycode=z})(this);
(function(d,k){"object"===typeof module&&module.exports?module.exports=k():"function"===typeof define&&define.amd?define(k):d.SecondLevelDomains=k(d)})(this,function(d){var k=d&&d.SecondLevelDomains,g={list:{ac:" com gov mil net org ",ae:" ac co gov mil name net org pro sch ",af:" com edu gov net org ",al:" com edu gov mil net org ",ao:" co ed gv it og pb ",ar:" com edu gob gov int mil net org tur ",at:" ac co gv or ",au:" asn com csiro edu gov id net org ",ba:" co com edu gov mil net org rs unbi unmo unsa untz unze ",
bb:" biz co com edu gov info net org store tv ",bh:" biz cc com edu gov info net org ",bn:" com edu gov net org ",bo:" com edu gob gov int mil net org tv ",br:" adm adv agr am arq art ato b bio blog bmd cim cng cnt com coop ecn edu eng esp etc eti far flog fm fnd fot fst g12 ggf gov imb ind inf jor jus lel mat med mil mus net nom not ntr odo org ppg pro psc psi qsl rec slg srv tmp trd tur tv vet vlog wiki zlg ",bs:" com edu gov net org ",bz:" du et om ov rg ",ca:" ab bc mb nb nf nl ns nt nu on pe qc sk yk ",
ck:" biz co edu gen gov info net org ",cn:" ac ah bj com cq edu fj gd gov gs gx gz ha hb he hi hl hn jl js jx ln mil net nm nx org qh sc sd sh sn sx tj tw xj xz yn zj ",co:" com edu gov mil net nom org ",cr:" ac c co ed fi go or sa ",cy:" ac biz com ekloges gov ltd name net org parliament press pro tm ","do":" art com edu gob gov mil net org sld web ",dz:" art asso com edu gov net org pol ",ec:" com edu fin gov info med mil net org pro ",eg:" com edu eun gov mil name net org sci ",er:" com edu gov ind mil net org rochest w ",
es:" com edu gob nom org ",et:" biz com edu gov info name net org ",fj:" ac biz com info mil name net org pro ",fk:" ac co gov net nom org ",fr:" asso com f gouv nom prd presse tm ",gg:" co net org ",gh:" com edu gov mil org ",gn:" ac com gov net org ",gr:" com edu gov mil net org ",gt:" com edu gob ind mil net org ",gu:" com edu gov net org ",hk:" com edu gov idv net org ",hu:" 2000 agrar bolt casino city co erotica erotika film forum games hotel info ingatlan jogasz konyvelo lakas media news org priv reklam sex shop sport suli szex tm tozsde utazas video ",
id:" ac co go mil net or sch web ",il:" ac co gov idf k12 muni net org ","in":" ac co edu ernet firm gen gov i ind mil net nic org res ",iq:" com edu gov i mil net org ",ir:" ac co dnssec gov i id net org sch ",it:" edu gov ",je:" co net org ",jo:" com edu gov mil name net org sch ",jp:" ac ad co ed go gr lg ne or ",ke:" ac co go info me mobi ne or sc ",kh:" com edu gov mil net org per ",ki:" biz com de edu gov info mob net org tel ",km:" asso com coop edu gouv k medecin mil nom notaires pharmaciens presse tm veterinaire ",
kn:" edu gov net org ",kr:" ac busan chungbuk chungnam co daegu daejeon es gangwon go gwangju gyeongbuk gyeonggi gyeongnam hs incheon jeju jeonbuk jeonnam k kg mil ms ne or pe re sc seoul ulsan ",kw:" com edu gov net org ",ky:" com edu gov net org ",kz:" com edu gov mil net org ",lb:" com edu gov net org ",lk:" assn com edu gov grp hotel int ltd net ngo org sch soc web ",lr:" com edu gov net org ",lv:" asn com conf edu gov id mil net org ",ly:" com edu gov id med net org plc sch ",ma:" ac co gov m net org press ",
mc:" asso tm ",me:" ac co edu gov its net org priv ",mg:" com edu gov mil nom org prd tm ",mk:" com edu gov inf name net org pro ",ml:" com edu gov net org presse ",mn:" edu gov org ",mo:" com edu gov net org ",mt:" com edu gov net org ",mv:" aero biz com coop edu gov info int mil museum name net org pro ",mw:" ac co com coop edu gov int museum net org ",mx:" com edu gob net org ",my:" com edu gov mil name net org sch ",nf:" arts com firm info net other per rec store web ",ng:" biz com edu gov mil mobi name net org sch ",
ni:" ac co com edu gob mil net nom org ",np:" com edu gov mil net org ",nr:" biz com edu gov info net org ",om:" ac biz co com edu gov med mil museum net org pro sch ",pe:" com edu gob mil net nom org sld ",ph:" com edu gov i mil net ngo org ",pk:" biz com edu fam gob gok gon gop gos gov net org web ",pl:" art bialystok biz com edu gda gdansk gorzow gov info katowice krakow lodz lublin mil net ngo olsztyn org poznan pwr radom slupsk szczecin torun warszawa waw wroc wroclaw zgora ",pr:" ac biz com edu est gov info isla name net org pro prof ",
ps:" com edu gov net org plo sec ",pw:" belau co ed go ne or ",ro:" arts com firm info nom nt org rec store tm www ",rs:" ac co edu gov in org ",sb:" com edu gov net org ",sc:" com edu gov net org ",sh:" co com edu gov net nom org ",sl:" com edu gov net org ",st:" co com consulado edu embaixada gov mil net org principe saotome store ",sv:" com edu gob org red ",sz:" ac co org ",tr:" av bbs bel biz com dr edu gen gov info k12 name net org pol tel tsk tv web ",tt:" aero biz cat co com coop edu gov info int jobs mil mobi museum name net org pro tel travel ",
tw:" club com ebiz edu game gov idv mil net org ",mu:" ac co com gov net or org ",mz:" ac co edu gov org ",na:" co com ",nz:" ac co cri geek gen govt health iwi maori mil net org parliament school ",pa:" abo ac com edu gob ing med net nom org sld ",pt:" com edu gov int net nome org publ ",py:" com edu gov mil net org ",qa:" com edu gov mil net org ",re:" asso com nom ",ru:" ac adygeya altai amur arkhangelsk astrakhan bashkiria belgorod bir bryansk buryatia cbg chel chelyabinsk chita chukotka chuvashia com dagestan e-burg edu gov grozny int irkutsk ivanovo izhevsk jar joshkar-ola kalmykia kaluga kamchatka karelia kazan kchr kemerovo khabarovsk khakassia khv kirov koenig komi kostroma kranoyarsk kuban kurgan kursk lipetsk magadan mari mari-el marine mil mordovia mosreg msk murmansk nalchik net nnov nov novosibirsk nsk omsk orenburg org oryol penza perm pp pskov ptz rnd ryazan sakhalin samara saratov simbirsk smolensk spb stavropol stv surgut tambov tatarstan tom tomsk tsaritsyn tsk tula tuva tver tyumen udm udmurtia ulan-ude vladikavkaz vladimir vladivostok volgograd vologda voronezh vrn vyatka yakutia yamal yekaterinburg yuzhno-sakhalinsk ",
rw:" ac co com edu gouv gov int mil net ",sa:" com edu gov med net org pub sch ",sd:" com edu gov info med net org tv ",se:" a ac b bd c d e f g h i k l m n o org p parti pp press r s t tm u w x y z ",sg:" com edu gov idn net org per ",sn:" art com edu gouv org perso univ ",sy:" com edu gov mil net news org ",th:" ac co go in mi net or ",tj:" ac biz co com edu go gov info int mil name net nic org test web ",tn:" agrinet com defense edunet ens fin gov ind info intl mincom nat net org perso rnrt rns rnu tourism ",
tz:" ac co go ne or ",ua:" biz cherkassy chernigov chernovtsy ck cn co com crimea cv dn dnepropetrovsk donetsk dp edu gov if in ivano-frankivsk kh kharkov kherson khmelnitskiy kiev kirovograd km kr ks kv lg lugansk lutsk lviv me mk net nikolaev od odessa org pl poltava pp rovno rv sebastopol sumy te ternopil uzhgorod vinnica vn zaporizhzhe zhitomir zp zt ",ug:" ac co go ne or org sc ",uk:" ac bl british-library co cym gov govt icnet jet lea ltd me mil mod national-library-scotland nel net nhs nic nls org orgn parliament plc police sch scot soc ",
us:" dni fed isa kids nsn ",uy:" com edu gub mil net org ",ve:" co com edu gob info mil net org web ",vi:" co com k12 net org ",vn:" ac biz com edu gov health info int name net org pro ",ye:" co com gov ltd me net org plc ",yu:" ac co edu gov org ",za:" ac agric alt bourse city co cybernet db edu gov grondar iaccess imt inca landesign law mil net ngo nis nom olivetti org pix school tm web ",zm:" ac co com edu gov net org sch ",com:"ar br cn de eu gb gr hu jpn kr no qc ru sa se uk us uy za ",net:"gb jp se uk ",
org:"ae",de:"com "},has:function(d){var b=d.lastIndexOf(".");if(0>=b||b>=d.length-1)return!1;var k=d.lastIndexOf(".",b-1);if(0>=k||k>=b-1)return!1;var u=g.list[d.slice(b+1)];return u?0<=u.indexOf(" "+d.slice(k+1,b)+" "):!1},is:function(d){var b=d.lastIndexOf(".");if(0>=b||b>=d.length-1||0<=d.lastIndexOf(".",b-1))return!1;var k=g.list[d.slice(b+1)];return k?0<=k.indexOf(" "+d.slice(0,b)+" "):!1},get:function(d){var b=d.lastIndexOf(".");if(0>=b||b>=d.length-1)return null;var k=d.lastIndexOf(".",b-1);
if(0>=k||k>=b-1)return null;var u=g.list[d.slice(b+1)];return!u||0>u.indexOf(" "+d.slice(k+1,b)+" ")?null:d.slice(k+1)},noConflict:function(){d.SecondLevelDomains===this&&(d.SecondLevelDomains=k);return this}};return g});
(function(d,k){"object"===typeof module&&module.exports?module.exports=k(require("./punycode"),require("./IPv6"),require("./SecondLevelDomains")):"function"===typeof define&&define.amd?define(["./punycode","./IPv6","./SecondLevelDomains"],k):d.URI=k(d.punycode,d.IPv6,d.SecondLevelDomains,d)})(this,function(d,k,g,u){function b(a,c){var e=1<=arguments.length,m=2<=arguments.length;if(!(this instanceof b))return e?m?new b(a,c):new b(a):new b;if(void 0===a){if(e)throw new TypeError("undefined is not a valid argument for URI");
a="undefined"!==typeof location?location.href+"":""}if(null===a&&e)throw new TypeError("null is not a valid argument for URI");this.href(a);return void 0!==c?this.absoluteTo(c):this}function p(a){return a.replace(/([.*+?^=!:${}()|[\]\/\\])/g,"\\$1")}function B(a){return void 0===a?"Undefined":String(Object.prototype.toString.call(a)).slice(8,-1)}function t(a){return"Array"===B(a)}function n(a,c){var e={},b;if("RegExp"===B(c))e=null;else if(t(c)){var l=0;for(b=c.length;l<b;l++)e[c[l]]=!0}else e[c]=
!0;l=0;for(b=a.length;l<b;l++)if(e&&void 0!==e[a[l]]||!e&&c.test(a[l]))a.splice(l,1),b--,l--;return a}function q(a,c){var e;if(t(c)){var b=0;for(e=c.length;b<e;b++)if(!q(a,c[b]))return!1;return!0}var l=B(c);b=0;for(e=a.length;b<e;b++)if("RegExp"===l){if("string"===typeof a[b]&&a[b].match(c))return!0}else if(a[b]===c)return!0;return!1}function w(a,c){if(!t(a)||!t(c)||a.length!==c.length)return!1;a.sort();c.sort();for(var e=0,b=a.length;e<b;e++)if(a[e]!==c[e])return!1;return!0}function h(a){return a.replace(/^\/+|\/+$/g,
"")}function r(a){return escape(a)}function v(a){return encodeURIComponent(a).replace(/[!'()*]/g,r).replace(/\*/g,"%2A")}function D(a){return function(c,e){if(void 0===c)return this._parts[a]||"";this._parts[a]=c||null;this.build(!e);return this}}function E(a,c){return function(e,b){if(void 0===e)return this._parts[a]||"";null!==e&&(e+="",e.charAt(0)===c&&(e=e.substring(1)));this._parts[a]=e;this.build(!b);return this}}var A=u&&u.URI;b.version="1.19.0";var f=b.prototype,y=Object.prototype.hasOwnProperty;
b._parts=function(){return{protocol:null,username:null,password:null,hostname:null,urn:null,port:null,path:null,query:null,fragment:null,preventInvalidHostname:b.preventInvalidHostname,duplicateQueryParameters:b.duplicateQueryParameters,escapeQuerySpace:b.escapeQuerySpace}};b.preventInvalidHostname=!1;b.duplicateQueryParameters=!1;b.escapeQuerySpace=!0;b.protocol_expression=/^[a-z][a-z0-9.+-]*$/i;b.idn_expression=/[^a-z0-9\._-]/i;b.punycode_expression=/(xn--)/i;b.ip4_expression=/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
b.ip6_expression=/^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$/;
b.find_uri_expression=/\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?\u00ab\u00bb\u201c\u201d\u2018\u2019]))/ig;b.findUri={start:/\b(?:([a-z][a-z0-9.+-]*:\/\/)|www\.)/gi,end:/[\s\r\n]|$/,trim:/[`!()\[\]{};:'".,<>?\u00ab\u00bb\u201c\u201d\u201e\u2018\u2019]+$/,parens:/(\([^\)]*\)|\[[^\]]*\]|\{[^}]*\}|<[^>]*>)/g};b.defaultPorts={http:"80",https:"443",ftp:"21",
gopher:"70",ws:"80",wss:"443"};b.hostProtocols=["http","https"];b.invalid_hostname_characters=/[^a-zA-Z0-9\.\-:_]/;b.domAttributes={a:"href",blockquote:"cite",link:"href",base:"href",script:"src",form:"action",img:"src",area:"href",iframe:"src",embed:"src",source:"src",track:"src",input:"src",audio:"src",video:"src"};b.getDomAttribute=function(a){if(a&&a.nodeName){var c=a.nodeName.toLowerCase();if("input"!==c||"image"===a.type)return b.domAttributes[c]}};b.encode=v;b.decode=decodeURIComponent;b.iso8859=
function(){b.encode=escape;b.decode=unescape};b.unicode=function(){b.encode=v;b.decode=decodeURIComponent};b.characters={pathname:{encode:{expression:/%(24|26|2B|2C|3B|3D|3A|40)/ig,map:{"%24":"$","%26":"&","%2B":"+","%2C":",","%3B":";","%3D":"=","%3A":":","%40":"@"}},decode:{expression:/[\/\?#]/g,map:{"/":"%2F","?":"%3F","#":"%23"}}},reserved:{encode:{expression:/%(21|23|24|26|27|28|29|2A|2B|2C|2F|3A|3B|3D|3F|40|5B|5D)/ig,map:{"%3A":":","%2F":"/","%3F":"?","%23":"#","%5B":"[","%5D":"]","%40":"@",
"%21":"!","%24":"$","%26":"&","%27":"'","%28":"(","%29":")","%2A":"*","%2B":"+","%2C":",","%3B":";","%3D":"="}}},urnpath:{encode:{expression:/%(21|24|27|28|29|2A|2B|2C|3B|3D|40)/ig,map:{"%21":"!","%24":"$","%27":"'","%28":"(","%29":")","%2A":"*","%2B":"+","%2C":",","%3B":";","%3D":"=","%40":"@"}},decode:{expression:/[\/\?#:]/g,map:{"/":"%2F","?":"%3F","#":"%23",":":"%3A"}}}};b.encodeQuery=function(a,c){var e=b.encode(a+"");void 0===c&&(c=b.escapeQuerySpace);return c?e.replace(/%20/g,"+"):e};b.decodeQuery=
function(a,c){a+="";void 0===c&&(c=b.escapeQuerySpace);try{return b.decode(c?a.replace(/\+/g,"%20"):a)}catch(e){return a}};var C={encode:"encode",decode:"decode"},z,F=function(a,c){return function(e){try{return b[c](e+"").replace(b.characters[a][c].expression,function(e){return b.characters[a][c].map[e]})}catch(m){return e}}};for(z in C)b[z+"PathSegment"]=F("pathname",C[z]),b[z+"UrnPathSegment"]=F("urnpath",C[z]);C=function(a,c,e){return function(m){var l=e?function(a){return b[c](b[e](a))}:b[c];
m=(m+"").split(a);for(var d=0,f=m.length;d<f;d++)m[d]=l(m[d]);return m.join(a)}};b.decodePath=C("/","decodePathSegment");b.decodeUrnPath=C(":","decodeUrnPathSegment");b.recodePath=C("/","encodePathSegment","decode");b.recodeUrnPath=C(":","encodeUrnPathSegment","decode");b.encodeReserved=F("reserved","encode");b.parse=function(a,c){c||(c={preventInvalidHostname:b.preventInvalidHostname});var e=a.indexOf("#");-1<e&&(c.fragment=a.substring(e+1)||null,a=a.substring(0,e));e=a.indexOf("?");-1<e&&(c.query=
a.substring(e+1)||null,a=a.substring(0,e));"//"===a.substring(0,2)?(c.protocol=null,a=a.substring(2),a=b.parseAuthority(a,c)):(e=a.indexOf(":"),-1<e&&(c.protocol=a.substring(0,e)||null,c.protocol&&!c.protocol.match(b.protocol_expression)?c.protocol=void 0:"//"===a.substring(e+1,e+3)?(a=a.substring(e+3),a=b.parseAuthority(a,c)):(a=a.substring(e+1),c.urn=!0)));c.path=a;return c};b.parseHost=function(a,c){a||(a="");a=a.replace(/\\/g,"/");var e=a.indexOf("/");-1===e&&(e=a.length);if("["===a.charAt(0)){var m=
a.indexOf("]");c.hostname=a.substring(1,m)||null;c.port=a.substring(m+2,e)||null;"/"===c.port&&(c.port=null)}else{var l=a.indexOf(":");m=a.indexOf("/");l=a.indexOf(":",l+1);-1!==l&&(-1===m||l<m)?(c.hostname=a.substring(0,e)||null,c.port=null):(m=a.substring(0,e).split(":"),c.hostname=m[0]||null,c.port=m[1]||null)}c.hostname&&"/"!==a.substring(e).charAt(0)&&(e++,a="/"+a);c.preventInvalidHostname&&b.ensureValidHostname(c.hostname,c.protocol);c.port&&b.ensureValidPort(c.port);return a.substring(e)||
"/"};b.parseAuthority=function(a,c){a=b.parseUserinfo(a,c);return b.parseHost(a,c)};b.parseUserinfo=function(a,c){var e=a.indexOf("/"),m=a.lastIndexOf("@",-1<e?e:a.length-1);-1<m&&(-1===e||m<e)?(e=a.substring(0,m).split(":"),c.username=e[0]?b.decode(e[0]):null,e.shift(),c.password=e[0]?b.decode(e.join(":")):null,a=a.substring(m+1)):(c.username=null,c.password=null);return a};b.parseQuery=function(a,c){if(!a)return{};a=a.replace(/&+/g,"&").replace(/^\?*&*|&+$/g,"");if(!a)return{};for(var e={},m=a.split("&"),
l=m.length,d,f,h=0;h<l;h++)if(d=m[h].split("="),f=b.decodeQuery(d.shift(),c),d=d.length?b.decodeQuery(d.join("="),c):null,y.call(e,f)){if("string"===typeof e[f]||null===e[f])e[f]=[e[f]];e[f].push(d)}else e[f]=d;return e};b.build=function(a){var c="";a.protocol&&(c+=a.protocol+":");a.urn||!c&&!a.hostname||(c+="//");c+=b.buildAuthority(a)||"";"string"===typeof a.path&&("/"!==a.path.charAt(0)&&"string"===typeof a.hostname&&(c+="/"),c+=a.path);"string"===typeof a.query&&a.query&&(c+="?"+a.query);"string"===
typeof a.fragment&&a.fragment&&(c+="#"+a.fragment);return c};b.buildHost=function(a){var c="";if(a.hostname)c=b.ip6_expression.test(a.hostname)?c+("["+a.hostname+"]"):c+a.hostname;else return"";a.port&&(c+=":"+a.port);return c};b.buildAuthority=function(a){return b.buildUserinfo(a)+b.buildHost(a)};b.buildUserinfo=function(a){var c="";a.username&&(c+=b.encode(a.username));a.password&&(c+=":"+b.encode(a.password));c&&(c+="@");return c};b.buildQuery=function(a,c,e){var m="",l,d;for(l in a)if(y.call(a,
l)&&l)if(t(a[l])){var f={};var h=0;for(d=a[l].length;h<d;h++)void 0!==a[l][h]&&void 0===f[a[l][h]+""]&&(m+="&"+b.buildQueryParameter(l,a[l][h],e),!0!==c&&(f[a[l][h]+""]=!0))}else void 0!==a[l]&&(m+="&"+b.buildQueryParameter(l,a[l],e));return m.substring(1)};b.buildQueryParameter=function(a,c,e){return b.encodeQuery(a,e)+(null!==c?"="+b.encodeQuery(c,e):"")};b.addQuery=function(a,c,e){if("object"===typeof c)for(var m in c)y.call(c,m)&&b.addQuery(a,m,c[m]);else if("string"===typeof c)void 0===a[c]?
a[c]=e:("string"===typeof a[c]&&(a[c]=[a[c]]),t(e)||(e=[e]),a[c]=(a[c]||[]).concat(e));else throw new TypeError("URI.addQuery() accepts an object, string as the name parameter");};b.setQuery=function(a,c,e){if("object"===typeof c)for(var m in c)y.call(c,m)&&b.setQuery(a,m,c[m]);else if("string"===typeof c)a[c]=void 0===e?null:e;else throw new TypeError("URI.setQuery() accepts an object, string as the name parameter");};b.removeQuery=function(a,c,e){var m;if(t(c))for(e=0,m=c.length;e<m;e++)a[c[e]]=
void 0;else if("RegExp"===B(c))for(m in a)c.test(m)&&(a[m]=void 0);else if("object"===typeof c)for(m in c)y.call(c,m)&&b.removeQuery(a,m,c[m]);else if("string"===typeof c)void 0!==e?"RegExp"===B(e)?!t(a[c])&&e.test(a[c])?a[c]=void 0:a[c]=n(a[c],e):a[c]!==String(e)||t(e)&&1!==e.length?t(a[c])&&(a[c]=n(a[c],e)):a[c]=void 0:a[c]=void 0;else throw new TypeError("URI.removeQuery() accepts an object, string, RegExp as the first parameter");};b.hasQuery=function(a,c,e,m){switch(B(c)){case "String":break;
case "RegExp":for(var l in a)if(y.call(a,l)&&c.test(l)&&(void 0===e||b.hasQuery(a,l,e)))return!0;return!1;case "Object":for(var d in c)if(y.call(c,d)&&!b.hasQuery(a,d,c[d]))return!1;return!0;default:throw new TypeError("URI.hasQuery() accepts a string, regular expression or object as the name parameter");}switch(B(e)){case "Undefined":return c in a;case "Boolean":return a=!(t(a[c])?!a[c].length:!a[c]),e===a;case "Function":return!!e(a[c],c,a);case "Array":return t(a[c])?(m?q:w)(a[c],e):!1;case "RegExp":return t(a[c])?
m?q(a[c],e):!1:!(!a[c]||!a[c].match(e));case "Number":e=String(e);case "String":return t(a[c])?m?q(a[c],e):!1:a[c]===e;default:throw new TypeError("URI.hasQuery() accepts undefined, boolean, string, number, RegExp, Function as the value parameter");}};b.joinPaths=function(){for(var a=[],c=[],e=0,m=0;m<arguments.length;m++){var l=new b(arguments[m]);a.push(l);l=l.segment();for(var d=0;d<l.length;d++)"string"===typeof l[d]&&c.push(l[d]),l[d]&&e++}if(!c.length||!e)return new b("");c=(new b("")).segment(c);
""!==a[0].path()&&"/"!==a[0].path().slice(0,1)||c.path("/"+c.path());return c.normalize()};b.commonPath=function(a,c){var e=Math.min(a.length,c.length),b;for(b=0;b<e;b++)if(a.charAt(b)!==c.charAt(b)){b--;break}if(1>b)return a.charAt(0)===c.charAt(0)&&"/"===a.charAt(0)?"/":"";if("/"!==a.charAt(b)||"/"!==c.charAt(b))b=a.substring(0,b).lastIndexOf("/");return a.substring(0,b+1)};b.withinString=function(a,c,e){e||(e={});var m=e.start||b.findUri.start,d=e.end||b.findUri.end,f=e.trim||b.findUri.trim,h=
e.parens||b.findUri.parens,g=/[a-z0-9-]=["']?$/i;for(m.lastIndex=0;;){var n=m.exec(a);if(!n)break;var r=n.index;if(e.ignoreHtml){var k=a.slice(Math.max(r-3,0),r);if(k&&g.test(k))continue}var v=r+a.slice(r).search(d);k=a.slice(r,v);for(v=-1;;){var q=h.exec(k);if(!q)break;v=Math.max(v,q.index+q[0].length)}k=-1<v?k.slice(0,v)+k.slice(v).replace(f,""):k.replace(f,"");k.length<=n[0].length||e.ignore&&e.ignore.test(k)||(v=r+k.length,n=c(k,r,v,a),void 0===n?m.lastIndex=v:(n=String(n),a=a.slice(0,r)+n+a.slice(v),
m.lastIndex=r+n.length))}m.lastIndex=0;return a};b.ensureValidHostname=function(a,c){var e=!!a,m=!1;c&&(m=q(b.hostProtocols,c));if(m&&!e)throw new TypeError("Hostname cannot be empty, if protocol is "+c);if(a&&a.match(b.invalid_hostname_characters)){if(!d)throw new TypeError('Hostname "'+a+'" contains characters other than [A-Z0-9.-:_] and Punycode.js is not available');if(d.toASCII(a).match(b.invalid_hostname_characters))throw new TypeError('Hostname "'+a+'" contains characters other than [A-Z0-9.-:_]');
}};b.ensureValidPort=function(a){if(a){var c=Number(a);if(!(/^[0-9]+$/.test(c)&&0<c&&65536>c))throw new TypeError('Port "'+a+'" is not a valid port');}};b.noConflict=function(a){if(a)return a={URI:this.noConflict()},u.URITemplate&&"function"===typeof u.URITemplate.noConflict&&(a.URITemplate=u.URITemplate.noConflict()),u.IPv6&&"function"===typeof u.IPv6.noConflict&&(a.IPv6=u.IPv6.noConflict()),u.SecondLevelDomains&&"function"===typeof u.SecondLevelDomains.noConflict&&(a.SecondLevelDomains=u.SecondLevelDomains.noConflict()),
a;u.URI===this&&(u.URI=A);return this};f.build=function(a){if(!0===a)this._deferred_build=!0;else if(void 0===a||this._deferred_build)this._string=b.build(this._parts),this._deferred_build=!1;return this};f.clone=function(){return new b(this)};f.valueOf=f.toString=function(){return this.build(!1)._string};f.protocol=D("protocol");f.username=D("username");f.password=D("password");f.hostname=D("hostname");f.port=D("port");f.query=E("query","?");f.fragment=E("fragment","#");f.search=function(a,c){var b=
this.query(a,c);return"string"===typeof b&&b.length?"?"+b:b};f.hash=function(a,c){var b=this.fragment(a,c);return"string"===typeof b&&b.length?"#"+b:b};f.pathname=function(a,c){if(void 0===a||!0===a){var e=this._parts.path||(this._parts.hostname?"/":"");return a?(this._parts.urn?b.decodeUrnPath:b.decodePath)(e):e}this._parts.path=this._parts.urn?a?b.recodeUrnPath(a):"":a?b.recodePath(a):"/";this.build(!c);return this};f.path=f.pathname;f.href=function(a,c){var e;if(void 0===a)return this.toString();
this._string="";this._parts=b._parts();var d=a instanceof b,l="object"===typeof a&&(a.hostname||a.path||a.pathname);a.nodeName&&(l=b.getDomAttribute(a),a=a[l]||"",l=!1);!d&&l&&void 0!==a.pathname&&(a=a.toString());if("string"===typeof a||a instanceof String)this._parts=b.parse(String(a),this._parts);else if(d||l)for(e in d=d?a._parts:a,d)y.call(this._parts,e)&&(this._parts[e]=d[e]);else throw new TypeError("invalid input");this.build(!c);return this};f.is=function(a){var c=!1,e=!1,d=!1,l=!1,f=!1,
h=!1,n=!1,r=!this._parts.urn;this._parts.hostname&&(r=!1,e=b.ip4_expression.test(this._parts.hostname),d=b.ip6_expression.test(this._parts.hostname),c=e||d,f=(l=!c)&&g&&g.has(this._parts.hostname),h=l&&b.idn_expression.test(this._parts.hostname),n=l&&b.punycode_expression.test(this._parts.hostname));switch(a.toLowerCase()){case "relative":return r;case "absolute":return!r;case "domain":case "name":return l;case "sld":return f;case "ip":return c;case "ip4":case "ipv4":case "inet4":return e;case "ip6":case "ipv6":case "inet6":return d;
case "idn":return h;case "url":return!this._parts.urn;case "urn":return!!this._parts.urn;case "punycode":return n}return null};var G=f.protocol,H=f.port,I=f.hostname;f.protocol=function(a,c){if(a&&(a=a.replace(/:(\/\/)?$/,""),!a.match(b.protocol_expression)))throw new TypeError('Protocol "'+a+"\" contains characters other than [A-Z0-9.+-] or doesn't start with [A-Z]");return G.call(this,a,c)};f.scheme=f.protocol;f.port=function(a,c){if(this._parts.urn)return void 0===a?"":this;void 0!==a&&(0===a&&
(a=null),a&&(a+="",":"===a.charAt(0)&&(a=a.substring(1)),b.ensureValidPort(a)));return H.call(this,a,c)};f.hostname=function(a,c){if(this._parts.urn)return void 0===a?"":this;if(void 0!==a){var e={preventInvalidHostname:this._parts.preventInvalidHostname};if("/"!==b.parseHost(a,e))throw new TypeError('Hostname "'+a+'" contains characters other than [A-Z0-9.-]');a=e.hostname;this._parts.preventInvalidHostname&&b.ensureValidHostname(a,this._parts.protocol)}return I.call(this,a,c)};f.origin=function(a,
c){if(this._parts.urn)return void 0===a?"":this;if(void 0===a){var e=this.protocol();return this.authority()?(e?e+"://":"")+this.authority():""}e=b(a);this.protocol(e.protocol()).authority(e.authority()).build(!c);return this};f.host=function(a,c){if(this._parts.urn)return void 0===a?"":this;if(void 0===a)return this._parts.hostname?b.buildHost(this._parts):"";if("/"!==b.parseHost(a,this._parts))throw new TypeError('Hostname "'+a+'" contains characters other than [A-Z0-9.-]');this.build(!c);return this};
f.authority=function(a,c){if(this._parts.urn)return void 0===a?"":this;if(void 0===a)return this._parts.hostname?b.buildAuthority(this._parts):"";if("/"!==b.parseAuthority(a,this._parts))throw new TypeError('Hostname "'+a+'" contains characters other than [A-Z0-9.-]');this.build(!c);return this};f.userinfo=function(a,c){if(this._parts.urn)return void 0===a?"":this;if(void 0===a){var e=b.buildUserinfo(this._parts);return e?e.substring(0,e.length-1):e}"@"!==a[a.length-1]&&(a+="@");b.parseUserinfo(a,
this._parts);this.build(!c);return this};f.resource=function(a,c){if(void 0===a)return this.path()+this.search()+this.hash();var e=b.parse(a);this._parts.path=e.path;this._parts.query=e.query;this._parts.fragment=e.fragment;this.build(!c);return this};f.subdomain=function(a,c){if(this._parts.urn)return void 0===a?"":this;if(void 0===a){if(!this._parts.hostname||this.is("IP"))return"";var e=this._parts.hostname.length-this.domain().length-1;return this._parts.hostname.substring(0,e)||""}e=this._parts.hostname.length-
this.domain().length;e=this._parts.hostname.substring(0,e);e=new RegExp("^"+p(e));a&&"."!==a.charAt(a.length-1)&&(a+=".");if(-1!==a.indexOf(":"))throw new TypeError("Domains cannot contain colons");a&&b.ensureValidHostname(a,this._parts.protocol);this._parts.hostname=this._parts.hostname.replace(e,a);this.build(!c);return this};f.domain=function(a,c){if(this._parts.urn)return void 0===a?"":this;"boolean"===typeof a&&(c=a,a=void 0);if(void 0===a){if(!this._parts.hostname||this.is("IP"))return"";var e=
this._parts.hostname.match(/\./g);if(e&&2>e.length)return this._parts.hostname;e=this._parts.hostname.length-this.tld(c).length-1;e=this._parts.hostname.lastIndexOf(".",e-1)+1;return this._parts.hostname.substring(e)||""}if(!a)throw new TypeError("cannot set domain empty");if(-1!==a.indexOf(":"))throw new TypeError("Domains cannot contain colons");b.ensureValidHostname(a,this._parts.protocol);!this._parts.hostname||this.is("IP")?this._parts.hostname=a:(e=new RegExp(p(this.domain())+"$"),this._parts.hostname=
this._parts.hostname.replace(e,a));this.build(!c);return this};f.tld=function(a,c){if(this._parts.urn)return void 0===a?"":this;"boolean"===typeof a&&(c=a,a=void 0);if(void 0===a){if(!this._parts.hostname||this.is("IP"))return"";var b=this._parts.hostname.lastIndexOf(".");b=this._parts.hostname.substring(b+1);return!0!==c&&g&&g.list[b.toLowerCase()]?g.get(this._parts.hostname)||b:b}if(a)if(a.match(/[^a-zA-Z0-9-]/))if(g&&g.is(a))b=new RegExp(p(this.tld())+"$"),this._parts.hostname=this._parts.hostname.replace(b,
a);else throw new TypeError('TLD "'+a+'" contains characters other than [A-Z0-9]');else{if(!this._parts.hostname||this.is("IP"))throw new ReferenceError("cannot set TLD on non-domain host");b=new RegExp(p(this.tld())+"$");this._parts.hostname=this._parts.hostname.replace(b,a)}else throw new TypeError("cannot set TLD empty");this.build(!c);return this};f.directory=function(a,c){if(this._parts.urn)return void 0===a?"":this;if(void 0===a||!0===a){if(!this._parts.path&&!this._parts.hostname)return"";
if("/"===this._parts.path)return"/";var e=this._parts.path.length-this.filename().length-1;e=this._parts.path.substring(0,e)||(this._parts.hostname?"/":"");return a?b.decodePath(e):e}e=this._parts.path.length-this.filename().length;e=this._parts.path.substring(0,e);e=new RegExp("^"+p(e));this.is("relative")||(a||(a="/"),"/"!==a.charAt(0)&&(a="/"+a));a&&"/"!==a.charAt(a.length-1)&&(a+="/");a=b.recodePath(a);this._parts.path=this._parts.path.replace(e,a);this.build(!c);return this};f.filename=function(a,
c){if(this._parts.urn)return void 0===a?"":this;if("string"!==typeof a){if(!this._parts.path||"/"===this._parts.path)return"";var e=this._parts.path.lastIndexOf("/");e=this._parts.path.substring(e+1);return a?b.decodePathSegment(e):e}e=!1;"/"===a.charAt(0)&&(a=a.substring(1));a.match(/\.?\//)&&(e=!0);var d=new RegExp(p(this.filename())+"$");a=b.recodePath(a);this._parts.path=this._parts.path.replace(d,a);e?this.normalizePath(c):this.build(!c);return this};f.suffix=function(a,c){if(this._parts.urn)return void 0===
a?"":this;if(void 0===a||!0===a){if(!this._parts.path||"/"===this._parts.path)return"";var e=this.filename(),d=e.lastIndexOf(".");if(-1===d)return"";e=e.substring(d+1);e=/^[a-z0-9%]+$/i.test(e)?e:"";return a?b.decodePathSegment(e):e}"."===a.charAt(0)&&(a=a.substring(1));if(e=this.suffix())d=a?new RegExp(p(e)+"$"):new RegExp(p("."+e)+"$");else{if(!a)return this;this._parts.path+="."+b.recodePath(a)}d&&(a=b.recodePath(a),this._parts.path=this._parts.path.replace(d,a));this.build(!c);return this};f.segment=
function(a,c,b){var e=this._parts.urn?":":"/",d=this.path(),f="/"===d.substring(0,1);d=d.split(e);void 0!==a&&"number"!==typeof a&&(b=c,c=a,a=void 0);if(void 0!==a&&"number"!==typeof a)throw Error('Bad segment "'+a+'", must be 0-based integer');f&&d.shift();0>a&&(a=Math.max(d.length+a,0));if(void 0===c)return void 0===a?d:d[a];if(null===a||void 0===d[a])if(t(c)){d=[];a=0;for(var n=c.length;a<n;a++)if(c[a].length||d.length&&d[d.length-1].length)d.length&&!d[d.length-1].length&&d.pop(),d.push(h(c[a]))}else{if(c||
"string"===typeof c)c=h(c),""===d[d.length-1]?d[d.length-1]=c:d.push(c)}else c?d[a]=h(c):d.splice(a,1);f&&d.unshift("");return this.path(d.join(e),b)};f.segmentCoded=function(a,c,e){var d;"number"!==typeof a&&(e=c,c=a,a=void 0);if(void 0===c){a=this.segment(a,c,e);if(t(a)){var f=0;for(d=a.length;f<d;f++)a[f]=b.decode(a[f])}else a=void 0!==a?b.decode(a):void 0;return a}if(t(c))for(f=0,d=c.length;f<d;f++)c[f]=b.encode(c[f]);else c="string"===typeof c||c instanceof String?b.encode(c):c;return this.segment(a,
c,e)};var J=f.query;f.query=function(a,c){if(!0===a)return b.parseQuery(this._parts.query,this._parts.escapeQuerySpace);if("function"===typeof a){var e=b.parseQuery(this._parts.query,this._parts.escapeQuerySpace),d=a.call(this,e);this._parts.query=b.buildQuery(d||e,this._parts.duplicateQueryParameters,this._parts.escapeQuerySpace);this.build(!c);return this}return void 0!==a&&"string"!==typeof a?(this._parts.query=b.buildQuery(a,this._parts.duplicateQueryParameters,this._parts.escapeQuerySpace),this.build(!c),
this):J.call(this,a,c)};f.setQuery=function(a,c,e){var d=b.parseQuery(this._parts.query,this._parts.escapeQuerySpace);if("string"===typeof a||a instanceof String)d[a]=void 0!==c?c:null;else if("object"===typeof a)for(var f in a)y.call(a,f)&&(d[f]=a[f]);else throw new TypeError("URI.addQuery() accepts an object, string as the name parameter");this._parts.query=b.buildQuery(d,this._parts.duplicateQueryParameters,this._parts.escapeQuerySpace);"string"!==typeof a&&(e=c);this.build(!e);return this};f.addQuery=
function(a,c,e){var d=b.parseQuery(this._parts.query,this._parts.escapeQuerySpace);b.addQuery(d,a,void 0===c?null:c);this._parts.query=b.buildQuery(d,this._parts.duplicateQueryParameters,this._parts.escapeQuerySpace);"string"!==typeof a&&(e=c);this.build(!e);return this};f.removeQuery=function(a,c,e){var d=b.parseQuery(this._parts.query,this._parts.escapeQuerySpace);b.removeQuery(d,a,c);this._parts.query=b.buildQuery(d,this._parts.duplicateQueryParameters,this._parts.escapeQuerySpace);"string"!==
typeof a&&(e=c);this.build(!e);return this};f.hasQuery=function(a,c,e){var d=b.parseQuery(this._parts.query,this._parts.escapeQuerySpace);return b.hasQuery(d,a,c,e)};f.setSearch=f.setQuery;f.addSearch=f.addQuery;f.removeSearch=f.removeQuery;f.hasSearch=f.hasQuery;f.normalize=function(){return this._parts.urn?this.normalizeProtocol(!1).normalizePath(!1).normalizeQuery(!1).normalizeFragment(!1).build():this.normalizeProtocol(!1).normalizeHostname(!1).normalizePort(!1).normalizePath(!1).normalizeQuery(!1).normalizeFragment(!1).build()};
f.normalizeProtocol=function(a){"string"===typeof this._parts.protocol&&(this._parts.protocol=this._parts.protocol.toLowerCase(),this.build(!a));return this};f.normalizeHostname=function(a){this._parts.hostname&&(this.is("IDN")&&d?this._parts.hostname=d.toASCII(this._parts.hostname):this.is("IPv6")&&k&&(this._parts.hostname=k.best(this._parts.hostname)),this._parts.hostname=this._parts.hostname.toLowerCase(),this.build(!a));return this};f.normalizePort=function(a){"string"===typeof this._parts.protocol&&
this._parts.port===b.defaultPorts[this._parts.protocol]&&(this._parts.port=null,this.build(!a));return this};f.normalizePath=function(a){var c=this._parts.path;if(!c)return this;if(this._parts.urn)return this._parts.path=b.recodeUrnPath(this._parts.path),this.build(!a),this;if("/"===this._parts.path)return this;c=b.recodePath(c);var e="";if("/"!==c.charAt(0)){var d=!0;c="/"+c}if("/.."===c.slice(-3)||"/."===c.slice(-2))c+="/";c=c.replace(/(\/(\.\/)+)|(\/\.$)/g,"/").replace(/\/{2,}/g,"/");d&&(e=c.substring(1).match(/^(\.\.\/)+/)||
"")&&(e=e[0]);for(;;){var f=c.search(/\/\.\.(\/|$)/);if(-1===f)break;else if(0===f){c=c.substring(3);continue}var h=c.substring(0,f).lastIndexOf("/");-1===h&&(h=f);c=c.substring(0,h)+c.substring(f+3)}d&&this.is("relative")&&(c=e+c.substring(1));this._parts.path=c;this.build(!a);return this};f.normalizePathname=f.normalizePath;f.normalizeQuery=function(a){"string"===typeof this._parts.query&&(this._parts.query.length?this.query(b.parseQuery(this._parts.query,this._parts.escapeQuerySpace)):this._parts.query=
null,this.build(!a));return this};f.normalizeFragment=function(a){this._parts.fragment||(this._parts.fragment=null,this.build(!a));return this};f.normalizeSearch=f.normalizeQuery;f.normalizeHash=f.normalizeFragment;f.iso8859=function(){var a=b.encode,c=b.decode;b.encode=escape;b.decode=decodeURIComponent;try{this.normalize()}finally{b.encode=a,b.decode=c}return this};f.unicode=function(){var a=b.encode,c=b.decode;b.encode=v;b.decode=unescape;try{this.normalize()}finally{b.encode=a,b.decode=c}return this};
f.readable=function(){var a=this.clone();a.username("").password("").normalize();var c="";a._parts.protocol&&(c+=a._parts.protocol+"://");a._parts.hostname&&(a.is("punycode")&&d?(c+=d.toUnicode(a._parts.hostname),a._parts.port&&(c+=":"+a._parts.port)):c+=a.host());a._parts.hostname&&a._parts.path&&"/"!==a._parts.path.charAt(0)&&(c+="/");c+=a.path(!0);if(a._parts.query){for(var e="",f=0,h=a._parts.query.split("&"),n=h.length;f<n;f++){var g=(h[f]||"").split("=");e+="&"+b.decodeQuery(g[0],this._parts.escapeQuerySpace).replace(/&/g,
"%26");void 0!==g[1]&&(e+="="+b.decodeQuery(g[1],this._parts.escapeQuerySpace).replace(/&/g,"%26"))}c+="?"+e.substring(1)}return c+=b.decodeQuery(a.hash(),!0)};f.absoluteTo=function(a){var c=this.clone(),e=["protocol","username","password","hostname","port"],d,f;if(this._parts.urn)throw Error("URNs do not have any generally defined hierarchical components");a instanceof b||(a=new b(a));if(c._parts.protocol)return c;c._parts.protocol=a._parts.protocol;if(this._parts.hostname)return c;for(d=0;f=e[d];d++)c._parts[f]=
a._parts[f];c._parts.path?(".."===c._parts.path.substring(-2)&&(c._parts.path+="/"),"/"!==c.path().charAt(0)&&(e=(e=a.directory())?e:0===a.path().indexOf("/")?"/":"",c._parts.path=(e?e+"/":"")+c._parts.path,c.normalizePath())):(c._parts.path=a._parts.path,c._parts.query||(c._parts.query=a._parts.query));c.build();return c};f.relativeTo=function(a){var c=this.clone().normalize();if(c._parts.urn)throw Error("URNs do not have any generally defined hierarchical components");a=(new b(a)).normalize();var e=
c._parts;var d=a._parts;var f=c.path();a=a.path();if("/"!==f.charAt(0))throw Error("URI is already relative");if("/"!==a.charAt(0))throw Error("Cannot calculate a URI relative to another relative URI");e.protocol===d.protocol&&(e.protocol=null);if(e.username===d.username&&e.password===d.password&&null===e.protocol&&null===e.username&&null===e.password&&e.hostname===d.hostname&&e.port===d.port)e.hostname=null,e.port=null;else return c.build();if(f===a)return e.path="",c.build();f=b.commonPath(f,a);
if(!f)return c.build();d=d.path.substring(f.length).replace(/[^\/]*$/,"").replace(/.*?\//g,"../");e.path=d+e.path.substring(f.length)||"./";return c.build()};f.equals=function(a){var c=this.clone(),e=new b(a);a={};var d;c.normalize();e.normalize();if(c.toString()===e.toString())return!0;var f=c.query();var h=e.query();c.query("");e.query("");if(c.toString()!==e.toString()||f.length!==h.length)return!1;c=b.parseQuery(f,this._parts.escapeQuerySpace);h=b.parseQuery(h,this._parts.escapeQuerySpace);for(d in c)if(y.call(c,
d)){if(!t(c[d])){if(c[d]!==h[d])return!1}else if(!w(c[d],h[d]))return!1;a[d]=!0}for(d in h)if(y.call(h,d)&&!a[d])return!1;return!0};f.preventInvalidHostname=function(a){this._parts.preventInvalidHostname=!!a;return this};f.duplicateQueryParameters=function(a){this._parts.duplicateQueryParameters=!!a;return this};f.escapeQuerySpace=function(a){this._parts.escapeQuerySpace=!!a;return this};return b});
(function(d,k){"object"===typeof module&&module.exports?module.exports=k(require("./URI")):"function"===typeof define&&define.amd?define(["./URI"],k):d.URITemplate=k(d.URI,d)})(this,function(d,k){function g(b){if(g._cache[b])return g._cache[b];if(!(this instanceof g))return new g(b);this.expression=b;g._cache[b]=this;return this}function u(b){this.data=b;this.cache={}}var b=k&&k.URITemplate,p=Object.prototype.hasOwnProperty,B=g.prototype,t={"":{prefix:"",separator:",",named:!1,empty_name_separator:!1,
encode:"encode"},"+":{prefix:"",separator:",",named:!1,empty_name_separator:!1,encode:"encodeReserved"},"#":{prefix:"#",separator:",",named:!1,empty_name_separator:!1,encode:"encodeReserved"},".":{prefix:".",separator:".",named:!1,empty_name_separator:!1,encode:"encode"},"/":{prefix:"/",separator:"/",named:!1,empty_name_separator:!1,encode:"encode"},";":{prefix:";",separator:";",named:!0,empty_name_separator:!1,encode:"encode"},"?":{prefix:"?",separator:"&",named:!0,empty_name_separator:!0,encode:"encode"},
"&":{prefix:"&",separator:"&",named:!0,empty_name_separator:!0,encode:"encode"}};g._cache={};g.EXPRESSION_PATTERN=/\{([^a-zA-Z0-9%_]?)([^\}]+)(\}|$)/g;g.VARIABLE_PATTERN=/^([^*:.](?:\.?[^*:.])*)((\*)|:(\d+))?$/;g.VARIABLE_NAME_PATTERN=/[^a-zA-Z0-9%_.]/;g.LITERAL_PATTERN=/[<>{}"`^| \\]/;g.expand=function(b,d,k){var h=t[b.operator],n=h.named?"Named":"Unnamed";b=b.variables;var v=[],q,p;for(p=0;q=b[p];p++){var w=d.get(q.name);if(0===w.type&&k&&k.strict)throw Error('Missing expansion value for variable "'+
q.name+'"');if(w.val.length){if(1<w.type&&q.maxlength)throw Error('Invalid expression: Prefix modifier not applicable to variable "'+q.name+'"');v.push(g["expand"+n](w,h,q.explode,q.explode&&h.separator||",",q.maxlength,q.name))}else w.type&&v.push("")}return v.length?h.prefix+v.join(h.separator):""};g.expandNamed=function(b,g,k,h,r,v){var n="",q=g.encode;g=g.empty_name_separator;var p=!b[q].length,f=2===b.type?"":d[q](v),t;var w=0;for(t=b.val.length;w<t;w++){if(r){var u=d[q](b.val[w][1].substring(0,
r));2===b.type&&(f=d[q](b.val[w][0].substring(0,r)))}else p?(u=d[q](b.val[w][1]),2===b.type?(f=d[q](b.val[w][0]),b[q].push([f,u])):b[q].push([void 0,u])):(u=b[q][w][1],2===b.type&&(f=b[q][w][0]));n&&(n+=h);k?n+=f+(g||u?"=":"")+u:(w||(n+=d[q](v)+(g||u?"=":"")),2===b.type&&(n+=f+","),n+=u)}return n};g.expandUnnamed=function(b,g,k,h,r){var n="",q=g.encode;g=g.empty_name_separator;var p=!b[q].length,w;var f=0;for(w=b.val.length;f<w;f++){if(r)var t=d[q](b.val[f][1].substring(0,r));else p?(t=d[q](b.val[f][1]),
b[q].push([2===b.type?d[q](b.val[f][0]):void 0,t])):t=b[q][f][1];n&&(n+=h);if(2===b.type){var u=r?d[q](b.val[f][0].substring(0,r)):b[q][f][0];n+=u;n=k?n+(g||t?"=":""):n+","}n+=t}return n};g.noConflict=function(){k.URITemplate===g&&(k.URITemplate=b);return g};B.expand=function(b,d){var k="";this.parts&&this.parts.length||this.parse();b instanceof u||(b=new u(b));for(var h=0,r=this.parts.length;h<r;h++)k+="string"===typeof this.parts[h]?this.parts[h]:g.expand(this.parts[h],b,d);return k};B.parse=function(){var b=
this.expression,d=g.EXPRESSION_PATTERN,k=g.VARIABLE_PATTERN,h=g.VARIABLE_NAME_PATTERN,r=g.LITERAL_PATTERN,v=[],p=0,u=function(b){if(b.match(r))throw Error('Invalid Literal "'+b+'"');return b};for(d.lastIndex=0;;){var A=d.exec(b);if(null===A){v.push(u(b.substring(p)));break}else v.push(u(b.substring(p,A.index))),p=A.index+A[0].length;if(!t[A[1]])throw Error('Unknown Operator "'+A[1]+'" in "'+A[0]+'"');if(!A[3])throw Error('Unclosed Expression "'+A[0]+'"');var f=A[2].split(",");for(var y=0,B=f.length;y<
B;y++){var z=f[y].match(k);if(null===z)throw Error('Invalid Variable "'+f[y]+'" in "'+A[0]+'"');if(z[1].match(h))throw Error('Invalid Variable Name "'+z[1]+'" in "'+A[0]+'"');f[y]={name:z[1],explode:!!z[3],maxlength:z[4]&&parseInt(z[4],10)}}if(!f.length)throw Error('Expression Missing Variable(s) "'+A[0]+'"');v.push({expression:A[0],operator:A[1],variables:f})}v.length||v.push(u(b));this.parts=v;return this};u.prototype.get=function(b){var d=this.data,g={type:0,val:[],encode:[],encodeReserved:[]};
if(void 0!==this.cache[b])return this.cache[b];this.cache[b]=g;d="[object Function]"===String(Object.prototype.toString.call(d))?d(b):"[object Function]"===String(Object.prototype.toString.call(d[b]))?d[b](b):d[b];if(void 0!==d&&null!==d)if("[object Array]"===String(Object.prototype.toString.call(d))){var h=0;for(b=d.length;h<b;h++)void 0!==d[h]&&null!==d[h]&&g.val.push([void 0,String(d[h])]);g.val.length&&(g.type=3)}else if("[object Object]"===String(Object.prototype.toString.call(d))){for(h in d)p.call(d,
h)&&void 0!==d[h]&&null!==d[h]&&g.val.push([h,String(d[h])]);g.val.length&&(g.type=2)}else g.type=1,g.val.push([void 0,String(d)]);return g};d.expand=function(b,k){var n=(new g(b)).expand(k);return new d(n)};return g});
(function(d,k){"object"===typeof module&&module.exports?module.exports=k(require("jquery"),require("./URI")):"function"===typeof define&&define.amd?define(["jquery","./URI"],k):k(d.jQuery,d.URI)})(this,function(d,k){function g(b){return b.replace(/([.*+?^=!:${}()|[\]\/\\])/g,"\\$1")}function u(b){var d=b.nodeName.toLowerCase();if("input"!==d||"image"===b.type)return k.domAttributes[d]}function b(b){return{get:function(h){return d(h).uri()[b]()},set:function(h,g){d(h).uri()[b](g);return g}}}function p(b,
g){if(!u(b)||!g)return!1;var h=g.match(q);if(!h||!h[5]&&":"!==h[2]&&!t[h[2]])return!1;var k=d(b).uri();if(h[5])return k.is(h[5]);if(":"===h[2]){var r=h[1].toLowerCase()+":";return t[r]?t[r](k,h[4]):!1}r=h[1].toLowerCase();return B[r]?t[h[2]](k[r](),h[4],r):!1}var B={},t={"=":function(b,d){return b===d},"^=":function(b,d){return!!(b+"").match(new RegExp("^"+g(d),"i"))},"$=":function(b,d){return!!(b+"").match(new RegExp(g(d)+"$","i"))},"*=":function(b,d,k){"directory"===k&&(b+="/");return!!(b+"").match(new RegExp(g(d),
"i"))},"equals:":function(b,d){return b.equals(d)},"is:":function(b,d){return b.is(d)}};d.each("origin authority directory domain filename fragment hash host hostname href password path pathname port protocol query resource scheme search subdomain suffix tld username".split(" "),function(h,g){B[g]=!0;d.attrHooks["uri:"+g]=b(g)});var n=function(b,g){return d(b).uri().href(g).toString()};d.each(["src","href","action","uri","cite"],function(b,g){d.attrHooks[g]={set:n}});d.attrHooks.uri.get=function(b){return d(b).uri()};
d.fn.uri=function(b){var d=this.first(),g=d.get(0),h=u(g);if(!h)throw Error('Element "'+g.nodeName+'" does not have either property: href, src, action, cite');if(void 0!==b){var n=d.data("uri");if(n)return n.href(b);b instanceof k||(b=k(b||""))}else{if(b=d.data("uri"))return b;b=k(d.attr(h)||"")}b._dom_element=g;b._dom_attribute=h;b.normalize();d.data("uri",b);return b};k.prototype.build=function(b){if(this._dom_element)this._string=k.build(this._parts),this._deferred_build=!1,this._dom_element.setAttribute(this._dom_attribute,
this._string),this._dom_element[this._dom_attribute]=this._string;else if(!0===b)this._deferred_build=!0;else if(void 0===b||this._deferred_build)this._string=k.build(this._parts),this._deferred_build=!1;return this};var q=/^([a-zA-Z]+)\s*([\^\$*]?=|:)\s*(['"]?)(.+)\3|^\s*([a-zA-Z0-9]+)\s*$/;var w=d.expr.createPseudo?d.expr.createPseudo(function(b){return function(d){return p(d,b)}}):function(b,d,g){return p(b,g[3])};d.expr[":"].uri=w;return d});

!function(e){"use strict";function n(e){if("undefined"==typeof e.length)o(e,"click",t);else if("string"!=typeof e&&!(e instanceof String))for(var n=0;n<e.length;n++)o(e[n],"click",t)}function t(e){var t,o,i,d;return e=e||window.event,t=e.currentTarget||e.srcElement,i=t.getAttribute("href"),i&&(d=e.ctrlKey||e.shiftKey||e.metaKey,o=t.getAttribute("target"),d||o&&!r(o))?(n.open(i),e.preventDefault?e.preventDefault():e.returnValue=!1,!1):void 0}function o(e,n,t){var o,i;return e.addEventListener?e.addEventListener(n,t,!1):(o="on"+n,e.attachEvent?e.attachEvent(o,t):e[o]?(i=e[o],e[o]=function(){t(),i()}):e[o]=t,void 0)}function i(e,n,t){var o,i,r,d,u;return o=document.createElement("iframe"),o.style.display="none",document.body.appendChild(o),i=o.contentDocument||o.contentWindow.document,d='"'+e+'"',n&&(d+=', "'+n+'"'),t&&(d+=', "'+t+'"'),r=i.createElement("script"),r.type="text/javascript",r.text="window.parent = null; window.top = null;window.frameElement = null; var child = window.open("+d+");child.opener = null",i.body.appendChild(r),u=o.contentWindow.child,document.body.removeChild(o),u}function r(e){return"_top"===e||"_self"===e||"_parent"===e}var d=-1!==navigator.userAgent.indexOf("MSIE"),u=window.open;n.open=function(e,n,t){var o;return r(n)?u.apply(window,arguments):d?(o=u.apply(window,arguments),o.opener=null,o):i(e,n,t)},n.patch=function(){window.open=function(){return n.open.apply(this,arguments)}},"undefined"!=typeof exports&&("undefined"!=typeof module&&module.exports?module.exports=n:exports.blankshield=n),"function"==typeof define&&"object"==typeof define.amd&&define("blankshield",[],function(){return n}),e.blankshield=n}(this);
if(typeof window.XE == "undefined") {
	/*jshint -W082 */
	(function($, global) {
		/* OS check */
		var UA = navigator.userAgent.toLowerCase();
		$.os = {
			Linux: /linux/.test(UA),
			Unix: /x11/.test(UA),
			Mac: /mac/.test(UA),
			Windows: /win/.test(UA)
		};
		$.os.name = ($.os.Windows) ? 'Windows' :
			($.os.Linux) ? 'Linux' :
			($.os.Unix) ? 'Unix' :
			($.os.Mac) ? 'Mac' : '';

		var base_url;

		/**
		 * @brief XE 공용 유틸리티 함수
		 * @namespace XE
		 */
		global.XE = {
			loaded_popup_menus : [],
			addedDocument : [],
			URI: global.URI,
			URITemplate : global.URITemplate,
			IPv6: global.IPv6,
			SecondLevelDomains: global.SecondLevelDomains,
			/**
			 * @brief 특정 name을 가진 체크박스들의 checked 속성 변경
			 * @param [itemName='cart',][options={}]
			 */
			checkboxToggleAll : function(itemName) {
				if(!is_def(itemName)) itemName='cart';
				var obj;
				var options = {
					wrap : null,
					checked : 'toggle',
					doClick : false
				};

				switch(arguments.length) {
					case 1:
						if(typeof(arguments[0]) == "string") {
							itemName = arguments[0];
						} else {
							$.extend(options, arguments[0] || {});
							itemName = 'cart';
						}
						break;
					case 2:
						itemName = arguments[0];
						$.extend(options, arguments[1] || {});
				}

				if(options.doClick === true) options.checked = null;
				if(typeof(options.wrap) == "string") options.wrap ='#'+options.wrap;

				if(options.wrap) {
					obj = $(options.wrap).find('input[name="'+itemName+'"]:checkbox');
				} else {
					obj = $('input[name="'+itemName+'"]:checkbox');
				}

				if(options.checked == 'toggle') {
					obj.each(function() {
						$(this).attr('checked', ($(this).attr('checked')) ? false : true);
					});
				} else {
					if(options.doClick === true) {
						obj.click();
					} else {
						obj.attr('checked', options.checked);
					}
				}
			},

			/**
			 * @brief 문서/회원 등 팝업 메뉴 출력
			 */
			displayPopupMenu : function(ret_obj, response_tags, params) {
				var target_srl = params.target_srl;
				var menu_id = params.menu_id;
				var menus = ret_obj.menus;
				var html = "";

				if(this.loaded_popup_menus[menu_id]) {
					html = this.loaded_popup_menus[menu_id];

				} else {
					if(menus) {
						var item = menus.item;
						if(typeof(item.length)=='undefined' || item.length<1) item = new Array(item);
						if(item.length) {
							for(var i=0;i<item.length;i++) {
								var url = item[i].url;
								var str = item[i].str;
								var icon = item[i].icon;
								var target = item[i].target;

								var styleText = "";
								var click_str = "";
								/* if(icon) styleText = " style=\"background-image:url('"+icon+"')\" "; */
								switch(target) {
									case "popup" :
											click_str = 'onclick="popopen(this.href, \''+target+'\'); return false;"';
										break;
									case "javascript" :
											click_str = 'onclick="'+url+'; return false; "';
											url='#';
										break;
									default :
											click_str = 'target="_blank"';
										break;
								}

								html += '<li '+styleText+'><a href="'+url+'" '+click_str+'>'+str+'</a></li> ';
							}
						}
					}
					this.loaded_popup_menus[menu_id] =  html;
				}

				/* 레이어 출력 */
				if(html) {
					var area = $('#popup_menu_area').html('<ul>'+html+'</ul>');
					var areaOffset = {top:params.page_y, left:params.page_x};

					if(area.outerHeight()+areaOffset.top > $(window).height()+$(window).scrollTop())
						areaOffset.top = $(window).height() - area.outerHeight() + $(window).scrollTop();
					if(area.outerWidth()+areaOffset.left > $(window).width()+$(window).scrollLeft())
						areaOffset.left = $(window).width() - area.outerWidth() + $(window).scrollLeft();

					area.css({ top:areaOffset.top, left:areaOffset.left }).show().focus();
				}
			},

			isSameHost: function(url) {
				if(typeof url != "string") return false;

				var target_url = global.XE.URI(url).normalizeHostname().normalizePort().normalizePathname();
				if(target_url.is('urn')) return false;

				var port = [Number(global.http_port) || 80, Number(global.https_port) || 443];

				if(!target_url.hostname()) {
					target_url = target_url.absoluteTo(global.request_uri);
				}

				var target_port = target_url.port();
				if(!target_port) {
					target_port = (target_url.protocol() == 'http') ? 80 : 443;
				}

				if(jQuery.inArray(Number(target_port), port) === -1) {
					return false;
				}

				if(!base_url) {
					base_url = global.XE.URI(global.request_uri).normalizeHostname().normalizePort().normalizePathname();
					base_url = base_url.hostname() + base_url.directory();
				}
				target_url = target_url.hostname() + target_url.directory();

				return target_url.indexOf(base_url) === 0;
			}
		};
	}) (jQuery, window || global);

	/* jQuery(document).ready() */
	(function($, global){
		$(function() {
		$('a[target]').each(function() {
			var $this = $(this);
			var href = String($this.attr('href')).trim();
			var target = String($this.attr('target')).trim();

			if(!target || !href) return;
			if(!href.match(/^(https?:\/\/)/)) return;

			if(target === '_top' || target === '_self' || target === '_parent') {
				$this.data('noopener', false);
				return;
			}

			if(!global.XE.isSameHost(href)) {
				var rel = $this.attr('rel');

				$this.data('noopener', true);

				if(typeof rel == 'string') {
					$this.attr('rel', rel + ' noopener');
				} else {
					$this.attr('rel', 'noopener');
				}
			}
		});

		$('body').on('click', 'a[target]', function(e) {
			var $this = $(this);
			var href = String($this.attr('href')).trim();

			if(!href) return;
			if(!href.match(/^(https?:\/\/)/)) return;

			if($this.data('noopener') !== false && !window.XE.isSameHost(href)) {
				var rel = $this.attr('rel');

				if(typeof rel == 'string') {
					$this.attr('rel', rel + ' noopener');
				} else {
					$this.attr('rel', 'noopener');
				}

					blankshield.open(href);
				e.preventDefault();
			}
		});

		/* select - option의 disabled=disabled 속성을 IE에서도 체크하기 위한 함수 */
		if($.browser.msie) {
			$('select').each(function(i, sels) {
				var disabled_exists = false;
				var first_enable = [];

				for(var j=0; j < sels.options.length; j++) {
					if(sels.options[j].disabled) {
						sels.options[j].style.color = '#CCCCCC';
						disabled_exists = true;
					}else{
						first_enable[i] = (first_enable[i] > -1) ? first_enable[i] : j;
					}
				}

				if(!disabled_exists) return;

				sels.oldonchange = sels.onchange;
				sels.onchange = function() {
					if(this.options[this.selectedIndex].disabled) {

						this.selectedIndex = first_enable[i];
						/*
						if(this.options.length<=1) this.selectedIndex = -1;
						else if(this.selectedIndex < this.options.length - 1) this.selectedIndex++;
						else this.selectedIndex--;
						*/

					} else {
						if(this.oldonchange) this.oldonchange();
					}
				};

				if(sels.selectedIndex >= 0 && sels.options[ sels.selectedIndex ].disabled) sels.onchange();

			});
		}

		/* 단락에디터 fold 컴포넌트 펼치기/접기 */
		var drEditorFold = $('.xe_content .fold_button');
		if(drEditorFold.size()) {
			var fold_container = $('div.fold_container', drEditorFold);
			$('button.more', drEditorFold).click(function() {
				$(this).hide().next('button').show().parent().next(fold_container).show();
			});
			$('button.less', drEditorFold).click(function() {
				$(this).hide().prev('button').show().parent().next(fold_container).hide();
			});
		}

		jQuery('input[type="submit"],button[type="submit"]').click(function(ev){
			var $el = jQuery(ev.currentTarget);

			setTimeout(function(){
				return function(){
					$el.attr('disabled', 'disabled');
				};
			}(), 0);

			setTimeout(function(){
				return function(){
					$el.removeAttr('disabled');
				};
			}(), 3000);
		});
	});
	})(jQuery, window || global);

	(function(global){ // String extension methods
		/**
		 * @brief location.href에서 특정 key의 값을 return
		 **/
		String.prototype.getQuery = function(key) {
			var url = global.XE.URI(this);
			var queries = url.search(true);

			if(typeof queries[key] == 'undefined') {
				return '';
			}

			return queries[key];
		};

		/**
		 * @brief location.href에서 특정 key의 값을 return
		 **/
		String.prototype.setQuery = function(key, val) {
			var uri = global.XE.URI(this);

			if(typeof key != 'undefined') {
				if(typeof val == "undefined" || val === '' || val === null) {
					uri.removeSearch(key);
				} else {
					uri.setSearch(key, String(val));
				}
			}

			return normailzeUri(uri).toString();
		};

		/**
		 * @brief string prototype으로 trim 함수 추가
		 **/
		if(!String.prototype.trim) {
			String.prototype.trim = function() {
				return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
			};
		}

		function normailzeUri(uri) {
			var query = uri.search(true);
			var filename = uri.filename() || 'index.php';
			var protocol = (global.enforce_ssl === true) ? 'https' : 'http';
			var port = 80;

			if(global.XE.isSameHost(uri.toString())) {
				if(jQuery.isEmptyObject(query)) filename = '';
			}

			if(protocol !== 'https' && query.act && jQuery.inArray(query.act, global.ssl_actions) !== -1) {
				protocol = 'https';
			}

			port = (protocol === 'http') ? global.http_port : global.https_port;

			return uri.protocol(protocol)
				.port(port || null)
				.filename(filename)
				.normalizePort();
		}
	})(window || global);

	/**
	 * @brief xSleep(micro time)
	 **/
	function xSleep(sec) {
		sec = sec / 1000;
		var now = new Date();
		var sleep = new Date();
		while( sleep.getTime() - now.getTime() < sec) {
			sleep = new Date();
		}
	}

	/**
	 * @brief 주어진 인자가 하나라도 defined되어 있지 않으면 false return
	 **/
	function isDef() {
		for(var i=0; i < arguments.length; ++i) {
			if(typeof(arguments[i]) == "undefined") return false;
		}
		return true;
	}

	/**
	 * @brief 윈도우 오픈
	 * 열려진 윈도우의 관리를 통해 window.focus()등을 FF에서도 비슷하게 구현함
	 **/
	var winopen_list = [];
	function winopen(url, target, attribute) {
		if(typeof xeVid != 'undefined' && url.indexOf(request_uri) >- 1 && !url.getQuery('vid')) {
			url = url.setQuery('vid',xeVid);
		}

		try {
			if(target != '_blank' && winopen_list[target]) {
				winopen_list[target].close();
				winopen_list[target] = null;
			}
		} catch(e) {
		}

		if(typeof target == 'undefined') target = '_blank';
		if(typeof attribute == 'undefined') attribute = '';

		if(!window.XE.isSameHost(url)) {
			window.blankshield.open(url, target, attribute);
		} else {
			var win = window.open(url, target, attribute);
			win.focus();
			if(target != '_blank') winopen_list[target] = win;
		}

	}

	/**
	 * @brief 팝업으로만 띄우기
	 * common/tpl/popup_layout.html이 요청되는 XE내의 팝업일 경우에 사용
	 **/
	function popopen(url, target) {
		winopen(url, target, "width=800,height=600,scrollbars=yes,resizable=yes,toolbars=no");
	}

	/**
	 * @brief 메일 보내기용
	 **/
	function sendMailTo(to) {
		location.href="mailto:"+to;
	}

	/**
	 * @brief url이동 (open_window 값이 N 가 아니면 새창으로 띄움)
	 **/
	function move_url(url, open_window) {
		if(!url) return false;

		if(/^\./.test(url)) url = window.request_uri + url;

		if(typeof open_window == 'undefined' || open_window == 'N') {
			location.href = url;
		} else {
			winopen(url);
		}

		return false;
	}

	/**
	 * @brief 멀티미디어 출력용 (IE에서 플래쉬/동영상 주변에 점선 생김 방지용)
	 **/
	function displayMultimedia(src, width, height, options) {
		/*jslint evil: true */
		var html = _displayMultimedia(src, width, height, options);
		if(html) document.writeln(html);
	}
	function _displayMultimedia(src, width, height, options) {
		if(src.indexOf('files') === 0) src = request_uri + src;

		var defaults = {
			wmode : 'transparent',
			allowScriptAccess : 'never',
			quality : 'high',
			flashvars : '',
			autostart : false
		};

		var params = jQuery.extend(defaults, options || {});
		var autostart = (params.autostart && params.autostart != 'false') ? 'true' : 'false';
		delete(params.autostart);

		var clsid = "";
		var codebase = "";
		var html = "";

		if(/\.(gif|jpg|jpeg|bmp|png)$/i.test(src)){
			html = '<img src="'+src+'" width="'+width+'" height="'+height+'" />';
		} else if(/\.flv$/i.test(src) || /\.mov$/i.test(src) || /\.moov$/i.test(src) || /\.m4v$/i.test(src)) {
			html = '<embed src="'+request_uri+'common/img/flvplayer.swf" allowfullscreen="true" allowscriptaccess="never" autostart="'+autostart+'" width="'+width+'" height="'+height+'" flashvars="&file='+src+'&width='+width+'&height='+height+'&autostart='+autostart+'" wmode="'+params.wmode+'" />';
		} else if(/\.swf/i.test(src)) {
			clsid = 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000';

			if(typeof(enforce_ssl)!='undefined' && enforce_ssl){ codebase = "https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0"; }
			else { codebase = "http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0"; }
			html = '<object classid="'+clsid+'" codebase="'+codebase+'" width="'+width+'" height="'+height+'" flashvars="'+params.flashvars+'">';
			html += '<param name="movie" value="'+src+'" />';
			for(var name in params) {
				if(params[name] != 'undefined' && params[name] !== '') {
					html += '<param name="'+name+'" value="'+params[name]+'" />';
				}
			}
			html += '' + '<embed src="'+src+'" allowscriptaccess="never" autostart="'+autostart+'"  width="'+width+'" height="'+height+'" flashvars="'+params.flashvars+'" wmode="'+params.wmode+'"></embed>' + '</object>';
		}  else {
			if (jQuery.browser.mozilla || jQuery.browser.opera) {
				// firefox and opera uses 0 or 1 for autostart parameter.
				autostart = (params.autostart && params.autostart != 'false') ? '1' : '0';
			}

			html = '<embed src="'+src+'" allowscriptaccess="never" autostart="'+autostart+'" width="'+width+'" height="'+height+'"';
			if(params.wmode == 'transparent') {
				html += ' windowlessvideo="1"';
			}
			html += '></embed>';
		}
		return html;
	}

	/**
	 * @brief 에디터에서 사용되는 내용 여닫는 코드 (고정, zbxe용)
	 **/
	function zbxe_folder_open(id) {
		jQuery("#folder_open_"+id).hide();
		jQuery("#folder_close_"+id).show();
		jQuery("#folder_"+id).show();
	}
	function zbxe_folder_close(id) {
		jQuery("#folder_open_"+id).show();
		jQuery("#folder_close_"+id).hide();
		jQuery("#folder_"+id).hide();
	}

	/**
	 * @brief 팝업의 경우 내용에 맞춰 현 윈도우의 크기를 조절해줌
	 * 팝업의 내용에 맞게 크기를 늘리는 것은... 쉽게 되지는 않음.. ㅡ.ㅜ
	 * popup_layout 에서 window.onload 시 자동 요청됨.
	 **/
	function setFixedPopupSize() {
		var $ = jQuery, $win = $(window), $pc = $('body>.popup'), w, h, dw, dh, offset;

		offset = $pc.css({overflow:'scroll'}).offset();

		w = $pc.width(10).height(10000).get(0).scrollWidth + offset.left*2;
		h = $pc.height(10).width(10000).get(0).scrollHeight + offset.top*2;

		if(w < 800) w = 800 + offset.left*2;

		dw = $win.width();
		dh = $win.height();

		if(w != dw) window.resizeBy(w - dw, 0);
		if(h != dh) window.resizeBy(0, h - dh);

		$pc.width(w-offset.left*2).css({overflow:'',height:''});
	}

	/**
	 * @brief 추천/비추천,스크랩,신고기능등 특정 srl에 대한 특정 module/action을 호출하는 함수
	 **/
	function doCallModuleAction(module, action, target_srl) {
		var params = {
			target_srl : target_srl,
			cur_mid    : current_mid,
			mid        : current_mid
		};
		exec_xml(module, action, params, completeCallModuleAction);
	}

	function completeCallModuleAction(ret_obj, response_tags) {
		if(ret_obj.message!='success') alert(ret_obj.message);
		location.reload();
	}

	function completeMessage(ret_obj) {
		alert(ret_obj.message);
		location.reload();
	}



	/* 언어코드 (lang_type) 쿠키값 변경 */
	function doChangeLangType(obj) {
		if(typeof(obj) == "string") {
			setLangType(obj);
		} else {
			var val = obj.options[obj.selectedIndex].value;
			setLangType(val);
		}
		location.href = location.href.setQuery('l', '');
	}
	function setLangType(lang_type) {
		var expire = new Date();
		expire.setTime(expire.getTime()+ (7000 * 24 * 3600000));
		setCookie('lang_type', lang_type, expire, '/');
	}

	/* 미리보기 */
	function doDocumentPreview(obj) {
		var fo_obj = obj;
		while(fo_obj.nodeName != "FORM") {
			fo_obj = fo_obj.parentNode;
		}
		if(fo_obj.nodeName != "FORM") return;
		var editor_sequence = fo_obj.getAttribute('editor_sequence');

		var content = editorGetContent(editor_sequence);

		var win = window.open("", "previewDocument","toolbars=no,width=700px;height=800px,scrollbars=yes,resizable=yes");

		var dummy_obj = jQuery("#previewDocument");

		if(!dummy_obj.length) {
			jQuery(
				'<form id="previewDocument" target="previewDocument" method="post" action="'+request_uri+'">'+
				'<input type="hidden" name="module" value="document" />'+
				'<input type="hidden" name="act" value="dispDocumentPreview" />'+
				'<input type="hidden" name="content" />'+
				'</form>'
			).appendTo(document.body);

			dummy_obj = jQuery("#previewDocument")[0];
		} else {
			dummy_obj = dummy_obj[0];
		}

		if(dummy_obj) {
			dummy_obj.content.value = content;
			dummy_obj.submit();
		}
	}

	/* 게시글 저장 */
	function doDocumentSave(obj) {
		var editor_sequence = obj.form.getAttribute('editor_sequence');
		var prev_content = editorRelKeys[editor_sequence].content.value;
		if(typeof(editor_sequence)!='undefined' && editor_sequence && typeof(editorRelKeys)!='undefined' && typeof(editorGetContent)=='function') {
			var content = editorGetContent(editor_sequence);
			editorRelKeys[editor_sequence].content.value = content;
		}

		var params={}, responses=['error','message','document_srl'], elms=obj.form.elements, data=jQuery(obj.form).serializeArray();
		jQuery.each(data, function(i, field){
			var val = jQuery.trim(field.value);
			if(!val) return true;
			if(/\[\]$/.test(field.name)) field.name = field.name.replace(/\[\]$/, '');
			if(params[field.name]) params[field.name] += '|@|'+val;
			else params[field.name] = field.value;
		});

		exec_xml('document','procDocumentTempSave', params, completeDocumentSave, responses, params, obj.form);

		editorRelKeys[editor_sequence].content.value = prev_content;
		return false;
	}

	function completeDocumentSave(ret_obj) {
		jQuery('input[name=document_srl]').eq(0).val(ret_obj.document_srl);
		alert(ret_obj.message);
	}

	/* 저장된 게시글 불러오기 */
	var objForSavedDoc = null;
	function doDocumentLoad(obj) {
		// 저장된 게시글 목록 불러오기
		objForSavedDoc = obj.form;
		popopen(request_uri.setQuery('module','document').setQuery('act','dispTempSavedList'));
	}

	/* 저장된 게시글의 선택 */
	function doDocumentSelect(document_srl, module) {
		if(!opener || !opener.objForSavedDoc) {
			window.close();
			return;
		}

		if(module===undefined) {
			module = 'document';
		}

		// 게시글을 가져와서 등록하기
		switch(module) {
			case 'page' :
				var url = opener.current_url;
				url = url.setQuery('document_srl', document_srl);

				if(url.getQuery('act') === 'dispPageAdminMobileContentModify')
				{
					url = url.setQuery('act', 'dispPageAdminMobileContentModify');
				}
				else
				{
					url = url.setQuery('act', 'dispPageAdminContentModify');
				}
				opener.location.href = url;
				break;
			default :
				opener.location.href = opener.current_url.setQuery('document_srl', document_srl).setQuery('act', 'dispBoardWrite');
				break;
		}
		window.close();
	}


	/* 스킨 정보 */
	function viewSkinInfo(module, skin) {
		popopen("./?module=module&act=dispModuleSkinInfo&selected_module="+module+"&skin="+skin, 'SkinInfo');
	}


	/* 관리자가 문서를 관리하기 위해서 선택시 세션에 넣음 */
	var addedDocument = [];
	function doAddDocumentCart(obj) {
		var srl = obj.value;
		addedDocument[addedDocument.length] = srl;
		setTimeout(function() { callAddDocumentCart(addedDocument.length); }, 100);
	}

	function callAddDocumentCart(document_length) {
		if(addedDocument.length<1 || document_length != addedDocument.length) return;
		var params = [];
		params.srls = addedDocument.join(",");
		exec_xml("document","procDocumentAddCart", params, null);
		addedDocument = [];
	}

	/* ff의 rgb(a,b,c)를 #... 로 변경 */
	function transRGB2Hex(value) {
		if(!value) return value;
		if(value.indexOf('#') > -1) return value.replace(/^#/, '');

		if(value.toLowerCase().indexOf('rgb') < 0) return value;
		value = value.replace(/^rgb\(/i, '').replace(/\)$/, '');
		value_list = value.split(',');

		var hex = '';
		for(var i = 0; i < value_list.length; i++) {
			var color = parseInt(value_list[i], 10).toString(16);
			if(color.length == 1) color = '0'+color;
			hex += color;
		}
		return hex;
	}

	/* 보안 로그인 모드로 전환 */
	function toggleSecuritySignIn() {
		var href = location.href;
		if(/https:\/\//i.test(href)) location.href = href.replace(/^https/i,'http');
		else location.href = href.replace(/^http/i,'https');
	}

	function reloadDocument() {
		location.reload();
	}


	/**
	*
	* Base64 encode / decode
	* http://www.webtoolkit.info/
	*
	**/

	var Base64 = {

		// private property
		_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

		// public method for encoding
		encode : function (input) {
			var output = "";
			var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
			var i = 0;

			input = Base64._utf8_encode(input);

			while (i < input.length) {

				chr1 = input.charCodeAt(i++);
				chr2 = input.charCodeAt(i++);
				chr3 = input.charCodeAt(i++);

				enc1 = chr1 >> 2;
				enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
				enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
				enc4 = chr3 & 63;

				if (isNaN(chr2)) {
					enc3 = enc4 = 64;
				} else if (isNaN(chr3)) {
					enc4 = 64;
				}

				output = output +
				this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
				this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

			}

			return output;
		},

		// public method for decoding
		decode : function (input) {
			var output = "";
			var chr1, chr2, chr3;
			var enc1, enc2, enc3, enc4;
			var i = 0;

			input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

			while (i < input.length) {
				enc1 = this._keyStr.indexOf(input.charAt(i++));
				enc2 = this._keyStr.indexOf(input.charAt(i++));
				enc3 = this._keyStr.indexOf(input.charAt(i++));
				enc4 = this._keyStr.indexOf(input.charAt(i++));

				chr1 = (enc1 << 2) | (enc2 >> 4);
				chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
				chr3 = ((enc3 & 3) << 6) | enc4;

				output = output + String.fromCharCode(chr1);

				if (enc3 != 64) {
					output = output + String.fromCharCode(chr2);
				}
				if (enc4 != 64) {
					output = output + String.fromCharCode(chr3);
				}
			}

			output = Base64._utf8_decode(output);

			return output;

		},

		// private method for UTF-8 encoding
		_utf8_encode : function (string) {
			string = string.replace(/\r\n/g,"\n");
			var utftext = "";

			for (var n = 0; n < string.length; n++) {
				var c = string.charCodeAt(n);

				if (c < 128) {
					utftext += String.fromCharCode(c);
				}
				else if((c > 127) && (c < 2048)) {
					utftext += String.fromCharCode((c >> 6) | 192);
					utftext += String.fromCharCode((c & 63) | 128);
				}
				else {
					utftext += String.fromCharCode((c >> 12) | 224);
					utftext += String.fromCharCode(((c >> 6) & 63) | 128);
					utftext += String.fromCharCode((c & 63) | 128);
				}
			}

			return utftext;
		},

		// private method for UTF-8 decoding
		_utf8_decode : function (utftext) {
			var string = "";
			var i = 0;
			var c = 0, c1 = 0, c2 = 0, c3 = 0;

			while ( i < utftext.length ) {
				c = utftext.charCodeAt(i);

				if (c < 128) {
					string += String.fromCharCode(c);
					i++;
				}
				else if((c > 191) && (c < 224)) {
					c2 = utftext.charCodeAt(i+1);
					string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
					i += 2;
				}
				else {
					c2 = utftext.charCodeAt(i+1);
					c3 = utftext.charCodeAt(i+2);
					string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
					i += 3;
				}
			}

			return string;
		}
	};






	/* ----------------------------------------------
	 * DEPRECATED
	 * 하위호환용으로 남겨 놓음
	 * ------------------------------------------- */

	if(typeof(resizeImageContents) == 'undefined') {
		window.resizeImageContents = function() {};
	}

	if(typeof(activateOptionDisabled) == 'undefined') {
		window.activateOptionDisabled = function() {};
	}

	var objectExtend = jQuery.extend;

	/**
	 * @brief 특정 Element의 display 옵션 토글
	 **/
	function toggleDisplay(objId) {
		jQuery('#'+objId).toggle();
	}

	/**
	 * @brief 에디터에서 사용하되 내용 여닫는 코드 (zb5beta beta 호환용으로 남겨 놓음)
	 **/
	function svc_folder_open(id) {
		jQuery("#_folder_open_"+id).hide();
		jQuery("#_folder_close_"+id).show();
		jQuery("#_folder_"+id).show();
	}
	function svc_folder_close(id) {
		jQuery("#_folder_open_"+id).show();
		jQuery("#_folder_close_"+id).hide();
		jQuery("#_folder_"+id).hide();
	}

	/**
	 * @brief 날짜 선택 (달력 열기)
	 **/
	function open_calendar(fo_id, day_str, callback_func) {
		if(typeof(day_str)=="undefined") day_str = "";

		var url = "./common/tpl/calendar.php?";
		if(fo_id) url+="fo_id="+fo_id;
		if(day_str) url+="&day_str="+day_str;
		if(callback_func) url+="&callback_func="+callback_func;

		popopen(url, 'Calendar');
	}

	var loaded_popup_menus = XE.loaded_popup_menus;
	function createPopupMenu() {}
	function chkPopupMenu() {}
	function displayPopupMenu(ret_obj, response_tags, params) {
		XE.displayPopupMenu(ret_obj, response_tags, params);
	}

	function GetObjLeft(obj) {
		return jQuery(obj).offset().left;
	}
	function GetObjTop(obj) {
		return jQuery(obj).offset().top;
	}

	function replaceOuterHTML(obj, html) {
		jQuery(obj).replaceWith(html);
	}

	function getOuterHTML(obj) {
		return jQuery(obj).html().trim();
	}

	function setCookie(name, value, expire, path) {
		var s_cookie = name + "=" + escape(value) +
			((!expire) ? "" : ("; expires=" + expire.toGMTString())) +
			"; path=" + ((!path) ? "/" : path);

		document.cookie = s_cookie;
	}

	function getCookie(name) {
		var match = document.cookie.match(new RegExp(name+'=(.*?)(?:;|$)'));
		if(match) return unescape(match[1]);
	}

	function is_def(v) {
		return (typeof(v)!='undefined');
	}

	function ucfirst(str) {
		return str.charAt(0).toUpperCase() + str.slice(1);
	}

	function get_by_id(id) {
		return document.getElementById(id);
	}

	jQuery(function($){
		// display popup menu that contains member actions and document actions
		$(document).on('click', function(evt) {
			var $area = $('#popup_menu_area');
			if(!$area.length) $area = $('<div id="popup_menu_area" tabindex="0" style="display:none;z-index:9999" />').appendTo(document.body);

			// 이전에 호출되었을지 모르는 팝업메뉴 숨김
			$area.hide();

			var $target = $(evt.target).filter('a,div,span');
			if(!$target.length) $target = $(evt.target).closest('a,div,span');
			if(!$target.length) return;

			// 객체의 className값을 구함
			var cls = $target.attr('class'), match;
			if(cls) match = cls.match(new RegExp('(?:^| )((document|comment|member)_([1-9]\\d*))(?: |$)',''));
			if(!match) return;

			// mobile에서 touchstart에 의한 동작 시 pageX, pageY 위치를 구함
			if(evt.pageX===undefined || evt.pageY===undefined)
			{
				var touch = evt.originalEvent.touches[0];
				if(touch!==undefined || !touch)
				{
					touch = evt.originalEvent.changedTouches[0];
				}
				evt.pageX = touch.pageX;
				evt.pageY = touch.pageY;
			}

			var action = 'get'+ucfirst(match[2])+'Menu';
			var params = {
				mid        : current_mid,
				cur_mid    : current_mid,
				menu_id    : match[1],
				target_srl : match[3],
				cur_act    : current_url.getQuery('act'),
				page_x     : evt.pageX,
				page_y     : evt.pageY
			};
			var response_tags = 'error message menus'.split(' ');

			// prevent default action
			evt.preventDefault();
			evt.stopPropagation();

			if(is_def(window.xeVid)) params.vid = xeVid;
			if(is_def(XE.loaded_popup_menus[params.menu_id])) return XE.displayPopupMenu(params, response_tags, params);

			show_waiting_message = false;
			exec_xml('member', action, params, XE.displayPopupMenu, response_tags, params);
			show_waiting_message = true;
		});

		/**
		 * Create popup windows automatically.
		 * Find anchors that have the '_xe_popup' class, then add popup script to them.
		 */
		$('body').on('click', 'a._xe_popup', function(event) {
			var $this = $(this);
			var name = $this.attr('name');
			var href = $this.attr('href');
			var win;

			if(!name) name = '_xe_popup_' + Math.floor(Math.random() * 1000);

			var features = 'left=10,top=10,width=10,height=10,resizable=no,scrollbars=no,toolbars=no';

			if(window.XE.isSameHost(href)) {
				win = window.open(href, name, features);
				if(win) win.focus();
			} else {
				window.blankshield.open(href, name, features);
			}

			event.preventDefault();
			return false;
		});

		// date picker default settings
		if($.datepicker) {
			$.datepicker.setDefaults({
				dateFormat : 'yy-mm-dd'
			});
		}
	});
}

(function($){
	var _xe_base, _app_base, _plugin_base;
	var _apps = [];

	_xe_base = {
		/**
		 * @brief return the name of Core module
		 */
		getName : function() {
			return 'Core';
		},

		/**
		 * @brief Create an application class
		 */
		createApp : function(sName, oDef) {
			var _base = getTypeBase();

			$.extend(_base.prototype, _app_base, oDef);

			_base.prototype.getName = function() {
				return sName;
			};

			return _base;
		},

		/**
		 * @brief Create a plugin class
		 */
		createPlugin : function(sName, oDef) {
			var _base = getTypeBase();

			$.extend(_base.prototype, _plugin_base, oDef);

			_base.prototype.getName = function() {
				return sName;
			};

			return _base;
		},

		/**
		 * @brief Get the array of applications
		 */
		getApps : function() {
			return $.makeArray(_apps);
		},

		/**
		 * @brief Get one application
		 */
		getApp : function(indexOrName) {
			indexOrName = (indexOrName||'').toLowerCase();
			if(typeof _apps[indexOrName] != 'undefined') {
				return _apps[indexOrName];
			} else {
				return null;
			}
		},

		/**
		 * @brief Register an application instance
		 */
		registerApp : function(oApp) {
			var sName = oApp.getName().toLowerCase();

			_apps.push(oApp);
			if (!$.isArray(_apps[sName])) {
				_apps[sName] = [];
			}
			_apps[sName].push(oApp);

			oApp.parent = this;

			// register event
			if ($.isFunction(oApp.activate)) oApp.activate();
		},

		/**
		 * @brief Unregister an application instance
		 */
		unregisterApp : function(oApp) {
			var sName  = oApp.getName().toLowerCase();
			var nIndex = $.inArray(oApp, _apps);

			if (nIndex >= 0) _apps = _apps.splice(nIndex, 1);

			if ($.isArray(_apps[sName])) {
				nIndex = $.inArray(oApp, _apps[sName]);
				if (nIndex >= 0) _apps[sName] = _apps[sName].splice(nIndex, 1);
			}

			// unregister event
			if ($.isFunction(oApp.deactivate)) oApp.deactivate();
		},

		/**
		 * @brief overrides broadcast method
		 */
		broadcast : function(msg, params) {
			this._broadcast(this, msg, params);
		},

		_broadcast : function(sender, msg, params) {
			for(var i=0; i < _apps.length; i++) {
				_apps[i]._cast(sender, msg, params);
			}


			// cast to child plugins
			this._cast(sender, msg, params);
		}
	};

	_app_base = {
		_plugins  : [],
		_messages : {},

		/**
		 * @brief get plugin
		 */
		getPlugin : function(sPluginName) {
			sPluginName = sPluginName.toLowerCase();
			if ($.isArray(this._plugins[sPluginName])) {
				return this._plugins[sPluginName];
			} else {
				return [];
			}
		},

		/**
		 * @brief register a plugin instance
		 */
		registerPlugin : function(oPlugin) {
			var self  = this;
			var sName = oPlugin.getName().toLowerCase();

			// check if the plugin is already registered
			if ($.inArray(oPlugin, this._plugins) >= 0) return false;

			// push the plugin into the _plugins array
			this._plugins.push(oPlugin);

			if (!$.isArray(this._plugins[sName])) this._plugins[sName] = [];
			this._plugins[sName].push(oPlugin);

			// register method pool
			$.each(oPlugin._binded_fn, function(api, fn){ self.registerHandler(api, fn); });

			// binding
			oPlugin.oApp = this;

			// registered event
			if ($.isFunction(oPlugin.activate)) oPlugin.activate();

			return true;
		},

		/**
		 * @brief register api message handler
		 */
		registerHandler : function(api, func) {
			var msgs = this._messages; api = api.toUpperCase();
			if (!$.isArray(msgs[api])) msgs[api] = [];
			msgs[api].push(func);
		},

		cast : function(msg, params) {
			return this._cast(this, msg, params || []);
		},

		broadcast : function(sender, msg, params) {
			if (this.parent && this.parent._broadcast) {
				this.parent._broadcast(sender, msg, params);
			}
		},

		_cast : function(sender, msg, params) {
			var i, len;
			var aMsg = this._messages;

			msg = msg.toUpperCase();

			// BEFORE hooker
			if (aMsg['BEFORE_'+msg] || this['API_BEFORE_'+msg]) {
				var bContinue = this._cast(sender, 'BEFORE_'+msg, params);
				if (!bContinue) return;
			}

			// main api function
			var vRet = [], sFn = 'API_'+msg;
			if ($.isArray(aMsg[msg])) {
				for(i=0; i < aMsg[msg].length; i++) {
					vRet.push( aMsg[msg][i](sender, params) );
				}
			}
			if (vRet.length < 2) vRet = vRet[0];

			// AFTER hooker
			if (aMsg['AFTER_'+msg] || this['API_AFTER_'+msg]) {
				this._cast(sender, 'AFTER_'+msg, params);
			}

			if (!/^(?:AFTER|BEFORE)_/.test(msg)) { // top level function
				return vRet;
			} else {
				return $.isArray(vRet)?($.inArray(false, vRet)<0):((typeof vRet=='undefined')?true:!!vRet);
			}
		}
	};

	_plugin_base = {
		oApp : null,

		cast : function(msg, params) {
			if (this.oApp && this.oApp._cast) {
				return this.oApp._cast(this, msg, params || []);
			}
		},

		broadcast : function(msg, params) {
			if (this.oApp && this.oApp.broadcast) {
				this.oApp.broadcast(this, mag, params || []);
			}
		}
	};

	function getTypeBase() {
		var _base = function() {
			var self = this;
			var pool = null;

			if ($.isArray(this._plugins)) this._plugins   = [];
			if (this._messages) this._messages = {};
			else this._binded_fn = {};

			// bind functions
			$.each(this, function(key, val){
				if (!$.isFunction(val)) return true;
				if (!/^API_([A-Z0-9_]+)$/.test(key)) return true;

				var api = RegExp.$1;
				var fn  = function(sender, params){ return self[key](sender, params); };

				if (self._messages) self._messages[api] = [fn];
				else self._binded_fn[api] = fn;
			});

			if ($.isFunction(this.init)) this.init.apply(this, arguments);
		};

		return _base;
	}

	window.xe = $.extend(_app_base, _xe_base);
	window.xe.lang = {}; // language repository

	// domready event
	$(function(){ xe.broadcast('ONREADY'); });

	// load event
	$(window).load(function(){ xe.broadcast('ONLOAD'); });
})(jQuery);

(function (root, factory) {
     if (typeof define === "function" && define.amd) {
         define([], factory);
     } else if (typeof exports === "object") {
         module.exports = factory();
     } else {
         root.X2JS = factory();
     }
 }(this, function () {
	return function (config) {
		'use strict';

		var VERSION = "1.2.0";

		config = config || {};
		initConfigDefaults();
		initRequiredPolyfills();

		function initConfigDefaults() {
			if(config.escapeMode === undefined) {
				config.escapeMode = true;
			}

			config.attributePrefix = config.attributePrefix || "_";
			config.arrayAccessForm = config.arrayAccessForm || "none";
			config.emptyNodeForm = config.emptyNodeForm || "text";

			if(config.enableToStringFunc === undefined) {
				config.enableToStringFunc = true;
			}
			config.arrayAccessFormPaths = config.arrayAccessFormPaths || [];
			if(config.skipEmptyTextNodesForObj === undefined) {
				config.skipEmptyTextNodesForObj = true;
			}
			if(config.stripWhitespaces === undefined) {
				config.stripWhitespaces = true;
			}
			config.datetimeAccessFormPaths = config.datetimeAccessFormPaths || [];

			if(config.useDoubleQuotes === undefined) {
				config.useDoubleQuotes = false;
			}

			config.xmlElementsFilter = config.xmlElementsFilter || [];
			config.jsonPropertiesFilter = config.jsonPropertiesFilter || [];

			if(config.keepCData === undefined) {
				config.keepCData = false;
			}
		}

		var DOMNodeTypes = {
			ELEMENT_NODE 	   : 1,
			TEXT_NODE    	   : 3,
			CDATA_SECTION_NODE : 4,
			COMMENT_NODE	   : 8,
			DOCUMENT_NODE 	   : 9
		};

		function initRequiredPolyfills() {
		}

		function getNodeLocalName( node ) {
			var nodeLocalName = node.localName;
			if(nodeLocalName == null) // Yeah, this is IE!!
				nodeLocalName = node.baseName;
			if(nodeLocalName == null || nodeLocalName=="") // =="" is IE too
				nodeLocalName = node.nodeName;
			return nodeLocalName;
		}

		function getNodePrefix(node) {
			return node.prefix;
		}

		function escapeXmlChars(str) {
			if(typeof(str) == "string")
				return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&apos;');
			else
				return str;
		}

		function unescapeXmlChars(str) {
			return str.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&apos;/g, "'").replace(/&amp;/g, '&');
		}

		function checkInStdFiltersArrayForm(stdFiltersArrayForm, obj, name, path) {
			var idx = 0;
			for(; idx < stdFiltersArrayForm.length; idx++) {
				var filterPath = stdFiltersArrayForm[idx];
				if( typeof filterPath === "string" ) {
					if(filterPath == path)
						break;
				}
				else
				if( filterPath instanceof RegExp) {
					if(filterPath.test(path))
						break;
				}
				else
				if( typeof filterPath === "function") {
					if(filterPath(obj, name, path))
						break;
				}
			}
			return idx!=stdFiltersArrayForm.length;
		}

		function toArrayAccessForm(obj, childName, path) {
			switch(config.arrayAccessForm) {
				case "property":
					if(!(obj[childName] instanceof Array))
						obj[childName+"_asArray"] = [obj[childName]];
					else
						obj[childName+"_asArray"] = obj[childName];
					break;
				/*case "none":
					break;*/
			}

			if(!(obj[childName] instanceof Array) && config.arrayAccessFormPaths.length > 0) {
				if(checkInStdFiltersArrayForm(config.arrayAccessFormPaths, obj, childName, path)) {
					obj[childName] = [obj[childName]];
				}
			}
		}

		function fromXmlDateTime(prop) {
			// Implementation based up on http://stackoverflow.com/questions/8178598/xml-datetime-to-javascript-date-object
			// Improved to support full spec and optional parts
			var bits = prop.split(/[-T:+Z]/g);

			var d = new Date(bits[0], bits[1]-1, bits[2]);
			var secondBits = bits[5].split("\.");
			d.setHours(bits[3], bits[4], secondBits[0]);
			if(secondBits.length>1)
				d.setMilliseconds(secondBits[1]);

			// Get supplied time zone offset in minutes
			if(bits[6] && bits[7]) {
				var offsetMinutes = bits[6] * 60 + Number(bits[7]);
				var sign = /\d\d-\d\d:\d\d$/.test(prop)? '-' : '+';

				// Apply the sign
				offsetMinutes = 0 + (sign == '-'? -1 * offsetMinutes : offsetMinutes);

				// Apply offset and local timezone
				d.setMinutes(d.getMinutes() - offsetMinutes - d.getTimezoneOffset())
			}
			else
				if(prop.indexOf("Z", prop.length - 1) !== -1) {
					d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours(), d.getMinutes(), d.getSeconds(), d.getMilliseconds()));
				}

			// d is now a local time equivalent to the supplied time
			return d;
		}

		function checkFromXmlDateTimePaths(value, childName, fullPath) {
			if(config.datetimeAccessFormPaths.length > 0) {
				var path = fullPath.split("\.#")[0];
				if(checkInStdFiltersArrayForm(config.datetimeAccessFormPaths, value, childName, path)) {
					return fromXmlDateTime(value);
				}
				else
					return value;
			}
			else
				return value;
		}

		function checkXmlElementsFilter(obj, childType, childName, childPath) {
			if( childType == DOMNodeTypes.ELEMENT_NODE && config.xmlElementsFilter.length > 0) {
				return checkInStdFiltersArrayForm(config.xmlElementsFilter, obj, childName, childPath);
			}
			else
				return true;
		}

		function parseDOMChildren( node, path ) {
			if(node.nodeType == DOMNodeTypes.DOCUMENT_NODE) {
				var result = new Object;
				var nodeChildren = node.childNodes;
				// Alternative for firstElementChild which is not supported in some environments
				for(var cidx=0; cidx <nodeChildren.length; cidx++) {
					var child = nodeChildren.item(cidx);
					if(child.nodeType == DOMNodeTypes.ELEMENT_NODE) {
						var childName = getNodeLocalName(child);
						result[childName] = parseDOMChildren(child, childName);
					}
				}
				return result;
			}
			else
			if(node.nodeType == DOMNodeTypes.ELEMENT_NODE) {
				var result = new Object;
				result.__cnt=0;

				var nodeChildren = node.childNodes;

				// Children nodes
				for(var cidx=0; cidx <nodeChildren.length; cidx++) {
					var child = nodeChildren.item(cidx); // nodeChildren[cidx];
					var childName = getNodeLocalName(child);

					if(child.nodeType!= DOMNodeTypes.COMMENT_NODE) {
						var childPath = path+"."+childName;
						if (checkXmlElementsFilter(result,child.nodeType,childName,childPath)) {
							result.__cnt++;
							if(result[childName] == null) {
								result[childName] = parseDOMChildren(child, childPath);
								toArrayAccessForm(result, childName, childPath);
							}
							else {
								if(result[childName] != null) {
									if( !(result[childName] instanceof Array)) {
										result[childName] = [result[childName]];
										toArrayAccessForm(result, childName, childPath);
									}
								}
								(result[childName])[result[childName].length] = parseDOMChildren(child, childPath);
							}
						}
					}
				}

				// Attributes
				for(var aidx=0; aidx <node.attributes.length; aidx++) {
					var attr = node.attributes.item(aidx); // [aidx];
					result.__cnt++;
					result[config.attributePrefix+attr.name]=attr.value;
				}

				// Node namespace prefix
				var nodePrefix = getNodePrefix(node);
				if(nodePrefix!=null && nodePrefix!="") {
					result.__cnt++;
					result.__prefix=nodePrefix;
				}

				if(result["#text"]!=null) {
					result.__text = result["#text"];
					if(result.__text instanceof Array) {
						result.__text = result.__text.join("\n");
					}
					//if(config.escapeMode)
					//	result.__text = unescapeXmlChars(result.__text);
					if(config.stripWhitespaces)
						result.__text = result.__text.trim();
					delete result["#text"];
					if(config.arrayAccessForm=="property")
						delete result["#text_asArray"];
					result.__text = checkFromXmlDateTimePaths(result.__text, childName, path+"."+childName);
				}
				if(result["#cdata-section"]!=null) {
					result.__cdata = result["#cdata-section"];
					delete result["#cdata-section"];
					if(config.arrayAccessForm=="property")
						delete result["#cdata-section_asArray"];
				}

				if( result.__cnt == 0 && config.emptyNodeForm=="text" ) {
					result = '';
				}
				else
				if( result.__cnt == 1 && result.__text!=null  ) {
					result = result.__text;
				}
				else
				if( result.__cnt == 1 && result.__cdata!=null && !config.keepCData  ) {
					result = result.__cdata;
				}
				else
				if ( result.__cnt > 1 && result.__text!=null && config.skipEmptyTextNodesForObj) {
					if( (config.stripWhitespaces && result.__text=="") || (result.__text.trim()=="")) {
						delete result.__text;
					}
				}
				delete result.__cnt;

				if( config.enableToStringFunc && (result.__text!=null || result.__cdata!=null )) {
					result.toString = function() {
						return (this.__text!=null? this.__text:'')+( this.__cdata!=null ? this.__cdata:'');
					};
				}

				return result;
			}
			else
			if(node.nodeType == DOMNodeTypes.TEXT_NODE || node.nodeType == DOMNodeTypes.CDATA_SECTION_NODE) {
				return node.nodeValue;
			}
		}

		function startTag(jsonObj, element, attrList, closed) {
			var resultStr = "<"+ ( (jsonObj!=null && jsonObj.__prefix!=null)? (jsonObj.__prefix+":"):"") + element;
			if(attrList!=null) {
				for(var aidx = 0; aidx < attrList.length; aidx++) {
					var attrName = attrList[aidx];
					var attrVal = jsonObj[attrName];
					if(config.escapeMode)
						attrVal=escapeXmlChars(attrVal);
					resultStr+=" "+attrName.substr(config.attributePrefix.length)+"=";
					if(config.useDoubleQuotes)
						resultStr+='"'+attrVal+'"';
					else
						resultStr+="'"+attrVal+"'";
				}
			}
			if(!closed)
				resultStr+=">";
			else
				resultStr+="/>";
			return resultStr;
		}

		function endTag(jsonObj,elementName) {
			return "</"+ (jsonObj.__prefix!=null? (jsonObj.__prefix+":"):"")+elementName+">";
		}

		function endsWith(str, suffix) {
			return str.indexOf(suffix, str.length - suffix.length) !== -1;
		}

		function jsonXmlSpecialElem ( jsonObj, jsonObjField ) {
			if((config.arrayAccessForm=="property" && endsWith(jsonObjField.toString(),("_asArray")))
					|| jsonObjField.toString().indexOf(config.attributePrefix)==0
					|| jsonObjField.toString().indexOf("__")==0
					|| (jsonObj[jsonObjField] instanceof Function) )
				return true;
			else
				return false;
		}

		function jsonXmlElemCount ( jsonObj ) {
			var elementsCnt = 0;
			if(jsonObj instanceof Object ) {
				for( var it in jsonObj  ) {
					if(jsonXmlSpecialElem ( jsonObj, it) )
						continue;
					elementsCnt++;
				}
			}
			return elementsCnt;
		}

		function checkJsonObjPropertiesFilter(jsonObj, propertyName, jsonObjPath) {
			return config.jsonPropertiesFilter.length == 0
				|| jsonObjPath==""
				|| checkInStdFiltersArrayForm(config.jsonPropertiesFilter, jsonObj, propertyName, jsonObjPath);
		}

		function parseJSONAttributes ( jsonObj ) {
			var attrList = [];
			if(jsonObj instanceof Object ) {
				for( var ait in jsonObj  ) {
					if(ait.toString().indexOf("__")== -1 && ait.toString().indexOf(config.attributePrefix)==0) {
						attrList.push(ait);
					}
				}
			}
			return attrList;
		}

		function parseJSONTextAttrs ( jsonTxtObj ) {
			var result ="";

			if(jsonTxtObj.__cdata!=null) {
				result+="<![CDATA["+jsonTxtObj.__cdata+"]]>";
			}

			if(jsonTxtObj.__text!=null) {
				if(config.escapeMode)
					result+=escapeXmlChars(jsonTxtObj.__text);
				else
					result+=jsonTxtObj.__text;
			}
			return result;
		}

		function parseJSONTextObject ( jsonTxtObj ) {
			var result ="";

			if( jsonTxtObj instanceof Object ) {
				result+=parseJSONTextAttrs ( jsonTxtObj );
			}
			else
				if(jsonTxtObj!=null) {
					if(config.escapeMode)
						result+=escapeXmlChars(jsonTxtObj);
					else
						result+=jsonTxtObj;
				}

			return result;
		}

		function getJsonPropertyPath(jsonObjPath, jsonPropName) {
			if (jsonObjPath==="") {
				return jsonPropName;
			}
			else
				return jsonObjPath+"."+jsonPropName;
		}

		function parseJSONArray ( jsonArrRoot, jsonArrObj, attrList, jsonObjPath ) {
			var result = "";
			if(jsonArrRoot.length == 0) {
				result+=startTag(jsonArrRoot, jsonArrObj, attrList, true);
			}
			else {
				for(var arIdx = 0; arIdx < jsonArrRoot.length; arIdx++) {
					result+=startTag(jsonArrRoot[arIdx], jsonArrObj, parseJSONAttributes(jsonArrRoot[arIdx]), false);
					result+=parseJSONObject(jsonArrRoot[arIdx], getJsonPropertyPath(jsonObjPath,jsonArrObj));
					result+=endTag(jsonArrRoot[arIdx],jsonArrObj);
				}
			}
			return result;
		}

		function parseJSONObject ( jsonObj, jsonObjPath ) {
			var result = "";

			var elementsCnt = jsonXmlElemCount ( jsonObj );

			if(elementsCnt > 0) {
				for( var it in jsonObj ) {

					if(jsonXmlSpecialElem ( jsonObj, it) || (jsonObjPath!="" && !checkJsonObjPropertiesFilter(jsonObj, it, getJsonPropertyPath(jsonObjPath,it))) )
						continue;

					var subObj = jsonObj[it];

					var attrList = parseJSONAttributes( subObj )

					if(subObj == null || subObj == undefined) {
						result+=startTag(subObj, it, attrList, true);
					}
					else
					if(subObj instanceof Object) {

						if(subObj instanceof Array) {
							result+=parseJSONArray( subObj, it, attrList, jsonObjPath );
						}
						else if(subObj instanceof Date) {
							result+=startTag(subObj, it, attrList, false);
							result+=subObj.toISOString();
							result+=endTag(subObj,it);
						}
						else {
							var subObjElementsCnt = jsonXmlElemCount ( subObj );
							if(subObjElementsCnt > 0 || subObj.__text!=null || subObj.__cdata!=null) {
								result+=startTag(subObj, it, attrList, false);
								result+=parseJSONObject(subObj, getJsonPropertyPath(jsonObjPath,it));
								result+=endTag(subObj,it);
							}
							else {
								result+=startTag(subObj, it, attrList, true);
							}
						}
					}
					else {
						result+=startTag(subObj, it, attrList, false);
						result+=parseJSONTextObject(subObj);
						result+=endTag(subObj,it);
					}
				}
			}
			result+=parseJSONTextObject(jsonObj);

			return result;
		}

		this.parseXmlString = function(xmlDocStr) {
			var isIEParser = window.ActiveXObject || "ActiveXObject" in window;
			if (xmlDocStr === undefined) {
				return null;
			}
			var xmlDoc;
			if (window.DOMParser) {
				var parser=new window.DOMParser();
				var parsererrorNS = null;
				// IE9+ now is here
				if(!isIEParser) {
					try {
						parsererrorNS = parser.parseFromString("INVALID", "text/xml").getElementsByTagName("parsererror")[0].namespaceURI;
					}
					catch(err) {
						parsererrorNS = null;
					}
				}
				try {
					xmlDoc = parser.parseFromString( xmlDocStr, "text/xml" );
					if( parsererrorNS!= null && xmlDoc.getElementsByTagNameNS(parsererrorNS, "parsererror").length > 0) {
						//throw new Error('Error parsing XML: '+xmlDocStr);
						xmlDoc = null;
					}
				}
				catch(err) {
					xmlDoc = null;
				}
			}
			else {
				// IE :(
				if(xmlDocStr.indexOf("<?")==0) {
					xmlDocStr = xmlDocStr.substr( xmlDocStr.indexOf("?>") + 2 );
				}
				xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
				xmlDoc.async="false";
				xmlDoc.loadXML(xmlDocStr);
			}
			return xmlDoc;
		};

		this.asArray = function(prop) {
			if (prop === undefined || prop == null)
				return [];
			else
			if(prop instanceof Array)
				return prop;
			else
				return [prop];
		};

		this.toXmlDateTime = function(dt) {
			if(dt instanceof Date)
				return dt.toISOString();
			else
			if(typeof(dt) === 'number' )
				return new Date(dt).toISOString();
			else
				return null;
		};

		this.asDateTime = function(prop) {
			if(typeof(prop) == "string") {
				return fromXmlDateTime(prop);
			}
			else
				return prop;
		};

		this.xml2json = function (xmlDoc) {
			return parseDOMChildren ( xmlDoc );
		};

		this.xml_str2json = function (xmlDocStr) {
			var xmlDoc = this.parseXmlString(xmlDocStr);
			if(xmlDoc!=null)
				return this.xml2json(xmlDoc);
			else
				return null;
		};

		this.json2xml_str = function (jsonObj) {
			return parseJSONObject ( jsonObj, "" );
		};

		this.json2xml = function (jsonObj) {
			var xmlDocStr = this.json2xml_str (jsonObj);
			return this.parseXmlString(xmlDocStr);
		};

		this.getVersion = function () {
			return VERSION;
		};
	}
}))

// xml handler을 이용하는 user function
var show_waiting_message = true;

(function($){
	var x2js = new X2JS();

	/**
	* @brief exec_xml
	* @author NAVER (developers@xpressengine.com)
	**/
	$.exec_xml = window.exec_xml = function(module, act, params, callback_func, response_tags, callback_func_arg, fo_obj) {
		var xml_path = request_uri+"index.php";
		if(!params) params = {};

		// {{{ set parameters
		if($.isArray(params)) params = arr2obj(params);
		params.module = module;
		params.act    = act;

		if(typeof(xeVid)!='undefined') params.vid = xeVid;
		if(typeof(response_tags) == "undefined" || response_tags.length<1) {
			response_tags = ['error','message'];
		} else {
			response_tags.push('error', 'message');
		}
		// }}} set parameters

		// use ssl?
		if ($.isArray(ssl_actions) && params.act && $.inArray(params.act, ssl_actions) >= 0) {
			var url    = default_url || request_uri;
			var port   = window.https_port || 443;
			var _ul    = $('<a>').attr('href', url)[0];
			var target = 'https://' + _ul.hostname.replace(/:\d+$/, '');

			if(port != 443) target += ':'+port;
			if(_ul.pathname[0] != '/') target += '/';

			target += _ul.pathname;
			xml_path = target.replace(/\/$/, '')+'/index.php';
		}

		var _u1 = $('<a>').attr('href', location.href)[0];
		var _u2 = $('<a>').attr('href', xml_path)[0];

		// 현 url과 ajax call 대상 url의 schema 또는 port가 다르면 직접 form 전송
		if(_u1.protocol != _u2.protocol || _u1.port != _u2.port) return send_by_form(xml_path, params);

		var xml = [];
		var xmlHelper = function(params) {
			var stack = [];

			if ($.isArray(params)) {
				$.each(params, function(key, val) {
					stack.push('<value type="array">' + xmlHelper(val) + '</value>');
				});
			}
			else if ($.isPlainObject(params)) {
				$.each(params, function(key, val) {
					stack.push('<' + key + '>' + xmlHelper(val) + '</' + key + '>');
				});
			}
			else if (!$.isFunction(params)) {
					stack.push(String(params).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;'));
			}

			return stack.join('\n');
		};

		xml.push('<?xml version="1.0" encoding="utf-8" ?>');
		xml.push('<methodCall>');
		xml.push('<params>');
		xml.push(xmlHelper(params));
		xml.push('</params>');
		xml.push('</methodCall>');

		var _xhr = null;
		if (_xhr && _xhr.readyState !== 0) _xhr.abort();

		// 전송 성공시
		function onsuccess(data, textStatus, xhr) {
			var resp_xml = $(data).find('response')[0];
			var resp_obj;
			var txt = '';
			var ret = {};
			var tags = {};

			waiting_obj.css('display', 'none').trigger('cancel_confirm');

			if(!resp_xml) {
				alert(_xhr.responseText);
				return null;
			}

			resp_obj = x2js.xml2json(data).response;

			if (typeof(resp_obj)=='undefined') {
				ret.error = -1;
				ret.message = 'Unexpected error occured.';
				try {
					if(typeof(txt=resp_xml.childNodes[0].firstChild.data)!='undefined') {
						ret.message += '\r\n'+txt;
					}
				} catch(e){}

				return ret;
			}

			$.each(response_tags, function(key, val){
				tags[val] = true;
			});
			tags.redirect_url = true;
			tags.act = true;
			$.each(resp_obj, function(key, val){ 
				if(tags[key]) ret[key] = val;
			});

			if(ret.error != '0') {
				if ($.isFunction($.exec_xml.onerror)) {
					return $.exec_xml.onerror(module, act, ret, callback_func, response_tags, callback_func_arg, fo_obj);
				}

				alert( (ret.message || 'An unknown error occured while loading ['+module+'.'+act+']').replace(/\\n/g, '\n') );

				return null;
			}

			if(ret.redirect_url) {
				location.href = ret.redirect_url.replace(/&amp;/g, '&');
				return null;
			}

			if($.isFunction(callback_func)) callback_func(ret, response_tags, callback_func_arg, fo_obj);
		}

		// 모든 xml데이터는 POST방식으로 전송. try-catch문으로 오류 발생시 대처
		try {
			$.ajax({
				url         : xml_path,
				type        : 'POST',
				dataType    : 'xml',
				data        : xml.join('\n'),
				contentType : 'text/plain',
				beforeSend  : function(xhr){ _xhr = xhr; },
				success     : onsuccess,
				error       : function(xhr, textStatus) {
					waiting_obj.css('display', 'none');

					var msg = '';

					if (textStatus == 'parsererror') {
						msg  = 'The result is not valid XML :\n-------------------------------------\n';

						if(xhr.responseText === "") return;

						msg += xhr.responseText.replace(/<[^>]+>/g, '');
					} else {
						msg = textStatus;
					}

					try{
						console.log(msg);
					} catch(ee){}
				}
			});
		} catch(e) {
			alert(e);
			return;
		}

		// ajax 통신중 대기 메세지 출력 (show_waiting_message값을 false로 세팅시 보이지 않음)
		var waiting_obj = $('.wfsr');
		if(show_waiting_message && waiting_obj.length) {

			var timeoutId = $(".wfsr").data('timeout_id');
			if(timeoutId) clearTimeout(timeoutId);
			$(".wfsr").css('opacity', 0.0);
			$(".wfsr").data('timeout_id', setTimeout(function(){
				$(".wfsr").css('opacity', '');
			}, 1000));

			waiting_obj.html(waiting_message).show();
		}
	};

	function send_by_form(url, params) {
		var frame_id = 'xeTmpIframe';
		var form_id  = 'xeVirtualForm';

		if (!$('#'+frame_id).length) {
			$('<iframe name="%id%" id="%id%" style="position:absolute;left:-1px;top:1px;width:1px;height:1px"></iframe>'.replace(/%id%/g, frame_id)).appendTo(document.body);
		}

		$('#'+form_id).remove();
		var form = $('<form id="%id%"></form>'.replace(/%id%/g, form_id)).attr({
			'id'     : form_id,
			'method' : 'post',
			'action' : url,
			'target' : frame_id
		});

		params.xeVirtualRequestMethod = 'xml';
		params.xeRequestURI           = location.href.replace(/#(.*)$/i,'');
		params.xeVirtualRequestUrl    = request_uri;

		$.each(params, function(key, value){
			$('<input type="hidden">').attr('name', key).attr('value', value).appendTo(form);
		});

		form.appendTo(document.body).submit();
	}

	function arr2obj(arr) {
		var ret = {};
		for(var key in arr) {
			if(arr.hasOwnProperty(key)) ret[key] = arr[key];
		}

		return ret;
	}


	/**
	* @brief exec_json (exec_xml와 같은 용도)
	**/
	$.exec_json = window.exec_json = function(action, data, callback_sucess, callback_error){
		if(typeof(data) == 'undefined') data = {};

		action = action.split('.');

		if(action.length == 2) {
			// The cover can be disturbing if it consistently blinks (because ajax call usually takes very short time). So make it invisible for the 1st 0.5 sec and then make it visible.
			var timeoutId = $(".wfsr").data('timeout_id');

			if(timeoutId) clearTimeout(timeoutId);

			$(".wfsr").css('opacity', 0.0);
			$(".wfsr").data('timeout_id', setTimeout(function(){
				$(".wfsr").css('opacity', '');
			}, 1000));

			if(show_waiting_message) $(".wfsr").html(waiting_message).show();

			$.extend(data,{module:action[0],act:action[1]});

			if(typeof(xeVid)!='undefined') $.extend(data,{vid:xeVid});

			try {
				$.ajax({
					type: "POST",
					dataType: "json",
					url: request_uri,
					contentType: "application/json",
					data: $.param(data),
					success: function(data) {
						$(".wfsr").hide().trigger('cancel_confirm');
						if(data.error != '0' && data.error > -1000) {
							if(data.error == -1 && data.message == 'msg_is_not_administrator') {
								alert('You are not logged in as an administrator');
								if($.isFunction(callback_error)) callback_error(data);

								return;
							} else {
								alert(data.message);
								if($.isFunction(callback_error)) callback_error(data);

								return;
							}
						}

						if($.isFunction(callback_sucess)) callback_sucess(data);
					},
					error: function(xhr, textStatus) {
						$(".wfsr").hide();

						var msg = '';

						if (textStatus == 'parsererror') {
							msg  = 'The result is not valid JSON :\n-------------------------------------\n';

							if(xhr.responseText === "") return;

							msg += xhr.responseText.replace(/<[^>]+>/g, '');
						} else {
							msg = textStatus;
						}

						try{
							console.log(msg);
						} catch(ee){}
					}
				});
			} catch(e) {
				alert(e);
				return;
			}
		}
	};

	$.fn.exec_html = function(action,data,type,func,args){
		if(typeof(data) == 'undefined') data = {};
		if(!$.inArray(type, ['html','append','prepend'])) type = 'html';

		var self = $(this);
		action = action.split(".");
		if(action.length == 2){
			var timeoutId = $(".wfsr").data('timeout_id');
			if(timeoutId) clearTimeout(timeoutId);
			$(".wfsr").css('opacity', 0.0);
			$(".wfsr").data('timeout_id', setTimeout(function(){
				$(".wfsr").css('opacity', '');
			}, 1000));
			if(show_waiting_message) $(".wfsr").html(waiting_message).show();

			$.extend(data,{module:action[0],act:action[1]});
			try {
				$.ajax({
					type:"POST",
					dataType:"html",
					url:request_uri,
					data:$.param(data),
					success : function(html){
						$(".wfsr").hide().trigger('cancel_confirm');
						self[type](html);
						if($.isFunction(func)) func(args);
					},
					error: function(xhr, textStatus) {
						$(".wfsr").hide();

						var msg = '';

						if (textStatus == 'parsererror') {
							msg  = 'The result is not valid page :\n-------------------------------------\n';

							if(xhr.responseText === "") return;

							msg += xhr.responseText.replace(/<[^>]+>/g, '');
						} else {
							msg = textStatus;
						}

						try{
							console.log(msg);
						} catch(ee){}
					}

				});

			} catch(e) {
				alert(e);
				return;
			}
		}
	};

	function beforeUnloadHandler(){
		return '';
	}

	$(function($){
		$(document)
			.ajaxStart(function(){
				$(window).bind('beforeunload', beforeUnloadHandler);
			})
			.bind('ajaxStop cancel_confirm', function(){
				$(window).unbind('beforeunload', beforeUnloadHandler);
			});
	});

})(jQuery);

(function($){

	var messages  = [];
	var rules     = [];
	var filters   = {};
	var callbacks = [];
	var extras    = {};

	var Validator = xe.createApp('Validator', {
		init : function() {
			// {{{ add filters
			// email
			var regEmail = /^[\w-]+((?:\.|\+|\~)[\w-]+)*@[\w-]+(\.[\w-]+)+$/;
			this.cast('ADD_RULE', ['email', regEmail]);
			this.cast('ADD_RULE', ['email_address', regEmail]);

			// userid
			var regUserid = /^[a-z]+[\w-]*[a-z0-9_]+$/i;
			this.cast('ADD_RULE', ['userid', regUserid]);
			this.cast('ADD_RULE', ['user_id', regUserid]);

			// url
			var regUrl = /^(https?|ftp|mms):\/\/[0-9a-z-]+(\.[_0-9a-z-]+)+(:\d+)?/;
			this.cast('ADD_RULE', ['url', regUrl]);
			this.cast('ADD_RULE', ['homepage', regUrl]);

			// korean
			var regKor = new RegExp('^[\uAC00-\uD7A3]*$');
			this.cast('ADD_RULE', ['korean', regKor]);

			// korean_number
			var regKorNum = new RegExp('^[\uAC00-\uD7A30-9]*$');
			this.cast('ADD_RULE', ['korean_number', regKorNum]);

			// alpha
			var regAlpha = /^[a-z]*$/i;
			this.cast('ADD_RULE', ['alpha', regAlpha]);

			// alpha_number
			var regAlphaNum = /^[a-z][a-z0-9_]*$/i;
			this.cast('ADD_RULE', ['alpha_number', regAlphaNum]);

			// number
			var regNum = /^[0-9]*$/;
			this.cast('ADD_RULE', ['number', regNum]);

			// float
			var regFloat = /^\d+(\.\d+)?$/;
			this.cast('ADD_RULE', ['float', regFloat]);
			// }}} add filters
		},
		// run validator
		run : function(oForm) {
			var filter = '';

			if (oForm._filter) filter = oForm._filter.value;

			var params = [oForm, filter];
			var result = this.cast('VALIDATE', params);
			if (typeof result == 'undefined') result = false;

			return result;
		},
		API_ONREADY : function() {
			var self = this;

			// hook form submit event
			$('form')
				.each(function(){
					if (this.onsubmit) {
						this['xe:onsubmit'] = this.onsubmit;
						this.onsubmit = null;
					}
				})
				.submit(function(e){
					var legacyFn = this['xe:onsubmit'];
					var hasLegacyFn = $.isFunction(legacyFn);
					var bResult = hasLegacyFn?legacyFn.apply(this):self.run(this);

					if(!bResult)
					{
						e.stopImmediatePropagation();
					}
					return bResult;
				});
		},
		API_VALIDATE : function(sender, params) {
			var result = true, form = params[0], elems = form.elements, filter, filter_to_add, ruleset, callback;
			var fields, names, name, el, val, mod, len, lenb, max, min, maxb, minb, rules, e_el, e_val, i, c, r, if_, fn;

			if(elems.ruleset) {
				filter = form.elements.ruleset.value;
			} else if(elems._filter) {
				filter = form.elements._filter.value;
			}

			if(!filter) return true;

			if($.isFunction(callbacks[filter])) callback = callbacks[filter];
			filter = $.extend({}, filters[filter.toLowerCase()] || {}, extras);

			function regex_quote(str) {
				return str.replace(/([\.\+\-\[\]\{\}\(\)\\])/g, '\\$1');
			}

			// get form names
			fields = [];
			for(i=0,c=form.elements.length; i < c; i++) {
				el   = elems[i];
				name = el.name;

				if(!name || !elems[name]) continue;
				if(!elems[name].length || elems[name][0] === el) fields.push(name);
			}
			fields = fields.join('\n');

			// get field names matching patterns
			filter_to_add = {};
			for(name in filter) {
				if(!filter.hasOwnProperty(name)) continue;

				names = [];
				if(name.substr(0,1) == '^') {
					names = fields.match( (new RegExp('^'+regex_quote(name.substr(1))+'.*$','gm')) );
				} else {
					continue;
				}
				if(!names) names = [];

				for(i=0,c=names.length; i < c; i++) {
					filter_to_add[names[i]]= filter[name];
				}

				filter[name] = null;
				delete filter[name];
			}

			filter = $.extend(filter, filter_to_add);

			for(name in filter) {
				if(!filter.hasOwnProperty(name)) continue;

				f   = filter[name];
				el  = elems[name];
				if(!el)
				{
					el = elems[name + '[]'];
				}
				val = el?$.trim(get_value($(el))):'';
				mod = (f.modifier||'')+',';


				if(!el || el.disabled) continue;

				if(f['if']) {
					if(!$.isArray(f['if'])) f['if'] = [f['if']];
					for(i=0;i<f['if'].length;i++) {
						/*jslint evil: true */
						if_ = f['if'][i];
						fn  = new Function('el', 'return !!(' + (if_.test.replace(/\$(\w+)/g, '(jQuery(\'[name=$1]\').is(\':radio, :checkbox\') ? jQuery(\'[name=$1]:checked\').val() : jQuery(\'[name=$1]\').val())')) +')');
						//fn  = new Function('el', 'return !!(' + (if_.test.replace(/\$(\w+)/g, 'el["$1"].value')) +')');
						if(fn(elems)) f[if_.attr] = if_.value;
						else delete f[if_.attr];

					}
				}

				if(!val) {
					if(f['default']) val = f['default'];
					if(f.required) return this.cast('ALERT', [form, name, 'isnull']) && false;
					else continue;
				}

				min  = parseInt(f.minlength) || 0;
				max  = parseInt(f.maxlength) || 0;
				minb = /b$/.test(f.minlength||'');
				maxb = /b$/.test(f.maxlength||'');
				len  = val.length;
				if(minb || maxb) lenb = get_bytes(val);
				if((min && min > (minb?lenb:len)) || (max && max < (maxb?lenb:len))) {
					return this.cast('ALERT', [form, name, 'outofrange', min, max]) && false;
				}

				if(f.equalto) {
					e_el  = elems[f.equalto];
					e_val = e_el?$.trim(get_value($(e_el))):'';
					if(e_el && e_val !== val) {
						return this.cast('ALERT', [form, name, 'equalto']) && false;
					}
				}

				rules = (f.rule || '').split(',');
				for(i=0,c=rules.length; i < c; i++) {
					if(!(r = rules[i])) continue;

					result = this.cast('APPLY_RULE', [r, val]);
					if(mod.indexOf('not,') > -1) result = !result;
					if(!result) {
						return this.cast('ALERT', [form, name, 'invalid_'+r]) && false;
					}
				}
			}

			if($.isFunction(callback)) return callback(form);

			return true;
		},
		API_ADD_RULE : function(sender, params) {
			var name = params[0].toLowerCase();
			rules[name] = params[1];
		},
		API_DEL_RULE : function(sender, params) {
			var name = params[0].toLowerCase();
			delete rules[name];
		},
		API_GET_RULE : function(sender, params) {
			var name = params[0].toLowerCase();

			if (rules[name]) {
				return rules[name];
			} else {
				return null;
			}
		},
		API_ADD_FILTER : function(sender, params) {
			var name   = params[0].toLowerCase();
			var filter = params[1];

			filters[name] = filter;
		},
		API_DEL_FILTER : function(sender, params) {
			var name = params[0].toLowerCase();
			delete filters[name];
		},
		API_GET_FILTER : function(sender, params) {
			var name = params[0].toLowerCase();

			if (filters[name]) {
				return filters[name];
			} else {
				return null;
			}
		},
		API_ADD_EXTRA_FIELD : function(sender, params) {
			var name = params[0].toLowerCase();
			var prop = params[1];

			extras[name] = prop;
		},
		API_GET_EXTRA_FIELD : function(sender, params) {
			var name = params[0].toLowerCase();
			return extras[name];
		},
		API_DEL_EXTRA_FIELD : function(sender, params) {
			var name = params[0].toLowerCase();
			delete extras[name];
		},
		API_APPLY_RULE : function(sender, params) {
			var name  = params[0];
			var value = params[1];

			if(typeof(rules[name]) == 'undefined') return true; // no filter
			if($.isFunction(rules[name])) return rules[name](value);
			if(rules[name] instanceof RegExp) return rules[name].test(value);
			if($.isArray(rules[name])) return ($.inArray(value, rules[name]) > -1);

			return true;
		},
		API_ALERT : function(sender, params) {
			var form = params[0];
			var field_name = params[1];
			var msg_code = params[2];
			var minlen   = params[3];
			var maxlen   = params[4];

			var field_msg = this.cast('GET_MESSAGE', [field_name]);
			var msg = this.cast('GET_MESSAGE', [msg_code]);

			if (msg != msg_code) msg = (msg.indexOf('%s')<0)?(field_msg+msg):(msg.replace('%s',field_msg));
			if (minlen||maxlen) msg +=  '('+(minlen||'')+'~'+(maxlen||'')+')';

			this.cast('SHOW_ALERT', [msg]);

			// set focus
			$(form.elements[field_name]).focus();
		},
		API_SHOW_ALERT : function(sender, params) {
			alert(params[0]);
		},
		API_ADD_MESSAGE : function(sender, params) {
			var msg_code = params[0];
			var msg_str  = params[1];

			messages[msg_code] = msg_str;
		},
		API_GET_MESSAGE : function(sender, params) {
			var msg_code = params[0];

			return messages[msg_code] || msg_code;
		},
		API_ADD_CALLBACK : function(sender, params) {
			var name = params[0];
			var func = params[1];

			callbacks[name] = func;
		},
		API_REMOVE_CALLBACK : function(sender, params) {
			var name = params[0];

			delete callbacks[name];
		}
	});

	var oValidator = new Validator();

	// register validator
	xe.registerApp(oValidator);

	// 호환성을 위해 추가한 플러그인 - 에디터에서 컨텐트를 가져와서 설정한다.
	var EditorStub = xe.createPlugin('editor_stub', {
		API_BEFORE_VALIDATE : function(sender, params) {
			var form = params[0];
			var seq  = form.getAttribute('editor_sequence');

			// bug fix for IE6,7
			if (seq && typeof seq == 'object') seq = seq.value;

			if (seq) {
				try {
					editorRelKeys[seq].content.value = editorRelKeys[seq].func(seq) || '';
				} catch(e) { }
			}
		}
	});
	oValidator.registerPlugin(new EditorStub());

	// functions
	function get_value($elem) {
		var vals = [];
		if ($elem.is(':radio')){
			return $elem.filter(':checked').val();
		} else if ($elem.is(':checkbox')) {
			$elem.filter(':checked').each(function(){
				vals.push(this.value);
			});
			return vals.join('|@|');
		} else {
			return $elem.val();
		}
	}

	function get_bytes(str) {
		str += '';
		if(!str.length) return 0;

		str = encodeURI(str);
		var c = str.split('%').length - 1;

		return str.length - c*2;
	}

})(jQuery);

/**
 * @function filterAlertMessage
 * @brief ajax로 서버에 요청후 결과를 처리할 callback_function을 지정하지 않았을 시 호출되는 기본 함수
 **/
function filterAlertMessage(ret_obj) {
	var error = ret_obj.error;
	var message = ret_obj.message;
	var act = ret_obj.act;
	var redirect_url = ret_obj.redirect_url;
	var url = location.href;

	if(typeof(message) != "undefined" && message && message != "success") alert(message);

	if(typeof(act)!="undefined" && act) url = current_url.setQuery("act", act);
	else if(typeof(redirect_url) != "undefined" && redirect_url) url = redirect_url;

	if(url == location.href) url = url.replace(/#(.*)$/,'');

	location.href = url;
}

/**
 * @brief Function to process filters
 * @deprecated
 */
function procFilter(form, filter_func) {
	filter_func(form);
	return false;
}

function legacy_filter(filter_name, form, module, act, callback, responses, confirm_msg, rename_params) {
	var v = xe.getApp('Validator')[0], $ = jQuery, args = [];

	if (!v) return false;

	if (!form.elements._filter) $(form).prepend('<input type="hidden" name="_filter" />');
	form.elements._filter.value = filter_name;

	args[0] = filter_name;
	args[1] = function(f) {
		var params = {}, res = [], elms = f.elements, data = $(f).serializeArray();
		$.each(data, function(i, field) {
			var v = $.trim(field.value), n = field.name;
			if(!v || !n) return true;
			if(rename_params[n]) n = rename_params[n];

			if(/\[\]$/.test(n)) n = n.replace(/\[\]$/, '');
			if(params[n]) {
				params[n] += '|@|'+v;
			} else {
				params[n] = field.value;
			}
		});

		if (confirm_msg && !confirm(confirm_msg)) return false;

		exec_xml(module, act, params, callback, responses, params, form);
	};

	v.cast('ADD_CALLBACK', args);
	v.cast('VALIDATE', [form, filter_name]);

	return false;
}
