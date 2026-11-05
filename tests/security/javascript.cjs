/* Run with node tests/security/javascript.cjs; no browser or dependencies needed. */
const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const root = path.resolve(__dirname, '../..');
let checks = 0;
function check(value, message) { assert.ok(value, message); checks++; }
const chain = {length: 0};
for (const method of ['on', 'bind', 'click', 'ready', 'each', 'addClass', 'html', 'css', 'show', 'focus']) chain[method] = () => chain;
for (const method of ['outerHeight', 'outerWidth', 'scrollTop', 'scrollLeft']) chain[method] = () => 0;
chain.height = chain.width = () => 1000;
const $ = selector => selector === '<a>' ? {attr(name, value) { this[0] = new URL(value); return this; }} : chain;
$.fn = {};
$.extend = Object.assign;
$.isArray = Array.isArray;
$.inArray = (value, array) => array.indexOf(value);
$.isPlainObject = value => value !== null && typeof value === 'object' && !Array.isArray(value);
$.isFunction = value => typeof value === 'function';
$.each = (object, callback) => Object.keys(object).forEach(key => callback(key, object[key]));
let request;
$.ajax = options => { request = options; };
const context = {
    navigator: {userAgent: 'security-test'}, document: {}, jQuery: $,
    location: {href: 'http://example.com/'}, request_uri: 'http://example.com/',
    ssl_actions: [], X2JS: function() {}, alert: message => { throw new Error(message); }
};
context.window = context;
vm.createContext(context);
vm.runInContext(fs.readFileSync(path.join(root, 'common/js/common.js'), 'utf8'), context);
vm.runInContext(fs.readFileSync(path.join(root, 'common/js/xml_handler.js'), 'utf8'), context);
context.escapeInput = '<>&"\'';
check(vm.runInContext('escapeInput.escape()', context) === '&lt;&gt;&amp;&quot;&#39;', 'Escape every HTML delimiter');
context.XE.displayPopupMenu({menus: {item: {
    url: '/page?x=" onclick="alert(1)', str: '<img src=x onerror=alert(1)>', target: '_blank'
}}}, [], {menu_id: 'quotes', page_x: 0, page_y: 0});
const html = context.XE.loaded_popup_menus.quotes;
check(html.includes('&quot; onclick=&quot;') && !html.includes('href="/page?x="'), 'Popup URL quotes remain inside the href attribute');
check(!html.includes('<img') && html.includes('&lt;img'), 'Popup labels render as text');
context.XE.displayPopupMenu({menus: {item: {
    url: 'javascript:alert(1)', str: 'unsafe', target: '_blank'
}}}, [], {menu_id: 'scheme', page_x: 0, page_y: 0});
check(context.XE.loaded_popup_menus.scheme.includes('href="#"'), 'Popup rejects executable URL schemes');
context.exec_xml('module', 'procExample', {'unsafe><injected': '<payload>&"', '': 'ignored'}, null);
check(request.data.includes('<unsafeinjected>&lt;payload&gt;&amp;&quot;</unsafeinjected>'), 'XML request keys cannot inject tags and values remain escaped');
check(!request.data.includes('<>') && !request.data.includes('<injected>'), 'XML request ignores empty keys');
for (const file of ['common/js/xe.js', 'common/js/xe.min.js', 'common/js/plugins/jquery.fileupload/js/main.min.js', 'addons/oembed/oembed.min.js', 'modules/admin/tpl/js/admin.min.js']) {
    new vm.Script(fs.readFileSync(path.join(root, file), 'utf8'), {filename: file});
    checks++;
}
console.log('OK: ' + checks + ' JavaScript security checks');
