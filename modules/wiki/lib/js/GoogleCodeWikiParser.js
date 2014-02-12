/**
 * @projectDescription A JS implementation of a Google Code Wiki to HTML converter
 * @link http://code.google.com/p/wikiwym
 * @author Stephan Beal
 * @author Fabien M�nager
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * 
 * Usage:
 *   var p = new GoogleCodeWikiParser();
 *   print( p.parse( "... GoogleCode Wiki-syntax input ...") );
 * 
 * Missing features vis-a-vis Google Code Wiki:
 *
 *  - Only [Bracketed] WikiWords, not unmarked WikiWords, are marked up.
 *    The exception is !WikiWord, which is parsed to WikiWord.
 *  - Features which requires specific Google infrastructure (e.g. Gadgets)
 *    are not included.
*
 * Known problems/limitations:
 *  - Inline markup is only matched across a single line. Consecutive
 *    lines are not crossed.
 *  - Certain CSS styles are hard-coded because GoCo does it that way.
 *  - The spacing/line breaks are not quite right when it comes
 *    handling to blank lines.
 *  - Block-level elements are processed twice. We should cache/re-use
 *    the result for the second pass. (The first pass just figures out
 *    which lines to exclude from the inline-markup parsing phase.)
 *  - A {{{...}}} inside backticks will not render properly. Since those are an alias
 *    for backticks, i'm not going to worry about this.
 */

function GoogleCodeWikiParser() {
  var ps = this;
  this.inTable = 0;
  this.listLevel = 0;
  this.inCode = 0;
  this.inBQ = 0;
  this.hList = [];
  this.liSpaces = [];
  this.liTags = [];
    /**
        Various regexes and block-level handlers used by GoogleCodeWikiParser.parse().

        This SHOULD be defined at the prototype level, not the instance level,
        but we can't do that because many of them require a handle back to
        the parser instance, and that is difficult to provide.
    */
    this.rx = {
        wikiWord: /\b([A-Z]\w+)\b/,
        headers: [
        [/^\s*======\s*([^<>=]+?)\s*======/, ps.headN(6) ],
        [/^\s*=====\s*([^<>=]+?)\s*=====/, ps.headN(5)],
        [/^\s*====\s*([^<>=]+?)\s*====/, ps.headN(4)],
        [/^\s*===\s*([^<>=]+?)\s*===/, ps.headN(3)],
        [/^\s*==\s*([^<>=]+?)\s*==/, ps.headN(2)],
        [/^\s*=\s*([^<>=]+?)\s*=/, ps.headN(1)],
        [/^\s*#summary\s+(.*)$/i, '<p class="summary">$1</p>'],
        [/^\s*#labels\s+(.*)$/i, '<strong>Labels: </strong><span class="labels">$1</span>'],
        [/^\s*#sidebar.*$/i, function(){ return ps.getWarning("The `#sidebar` directive is not supported by this parser!")}]
        ],
        
        /**
        * Patterns for block-level elements (lists, tables, code).
        *
        * The order of these blocks is _mostly_ insignficant, except that
        * types with "syntactic collisions" must be ordered property. Namely,
        * that means that the BLOCKQUOTE handling must come after these
        * UL/OL handling, since a blockquote would otherwise then consume
        * UL/OL blocks.
        */
        block: {
            /**
            * This object exists only for documentation purposes. It documents
            * the API required by the concrete implementations of block-level
            * markup handlers.
            */
            PLACEHOLDER: {
                /**
                * The regex for the OPENING line of this block type.
                */
                rx:/^ONLY_FOR_DOCUMENTATION_PURPOSES$/,
                
                /**
                * This is called when rx.test(currentInputLine) matches.
                * It is then called in a loop as long as it returns >0.
                * @param {Number} line - Current inputline.
                * @param {Array} result - All translated results should be pushed onto this array.
                * @return {Number}
                *  =0 : The block element consumed the line and has been closed.
                *  >0 : block element consumed the line and would like to try the next line.
                *  <0 : Block element did not consume the line. Block element
                *       has been closed. The caller should re-try the same line
                *       using other available handlers, if necessary.
                */
                doLine: function(line,result) {
                    return -1;
                }
            },

            table: {
                rx: /^\s*\|\|/,

                doLine: function(line, result) {
                var cells = line ? (''+line).split('||') : null;
                if (cells) cells.shift(); // strip empty element after final separator.
                if (!cells || !cells.length) {
                    ps.inTable = 0;
                    result.push('</table><br>');
                    return -1;
                }
                else {

                    cells = cells.slice(0,cells.length-1); // strip trailing empty element
                }
                var out = [];
                ++ps.inTable;
                if(ps.inTable == 1) {
                    out.push('<table class="wikitable">');
                }
                out.push('<tr>');
                var tag = 'td';//( 1 == ps.inTable ) ? 'th': 'td'
                var c;
                for (var i in cells) {
                    if (!cells.hasOwnProperty(i)) continue; // Prototype-specific kludge. The fuckers.
                    c = cells[i];
                    if (c instanceof Function) continue; // Prototype assholes!
                    // IE performs better witthout string concatenation
                    out.push(
                    '<',tag,' style="border: 1px solid #aaa; padding: 5px;">', // hard-coded values taken from Google Code
                        ps.parseInlined(c),
                    '</',tag,'>'
                    );
                }
                out.push('</tr>');
                result.push(out.join(''));
                return 1;
                }
            }/*table*/,

            code: {
                rx: /^\s*\{\{\{(.*)/,
                
                /**
                * Parses non-inlined {{{ ... }}} blocks.
                */
                doLine: function(line, result) {
                //print("TRYING CODE:",line);
                var m;

                if( (undefined ===line) || (m = /(.*)}}}(.*)/.exec(line))) {
                    ps.inCode = 0;
                    if( m && m[1] ) result.push(m[1]);
                    result.push('</pre><p></p>');
                    if( m && m[2] ) result.push(ps.parseInlined(m[2]));
                    //print("ENDING CODE:",line);
                    return 0;
                }

                ++ps.inCode;
                if( 1 == ps.inCode ) {
                    result.push('<pre class="prettyprint">');
                    m = this.rx.exec(line);
                    if( m && m[1] ) result.push(m[1].replace(/&/g,'&amp;').replace(/</g, '&lt;'));
                    //print("STARTING CODE:",line);
                }
                else {
                    result.push( (line && line.length)
                                ? (line.replace(/&/g,'&amp;').replace(/</g, '&lt;'))
                                : '');
                    //print("CONTINUING CODE:",line);
                }
                return 1;
                }
            }/*code*/,

            listOLUL: {
                rx:/^(\s+)([#*])\s+(.*)/, // reminder: Google docs say 2+ spaces, but it seems to tolerate 1 space and many pages use that.

            /**
                * Parses OL lists.
                * 
                * FIXME: adject list entries of different types at the same list level are not handled properly
                * (wrong list type on 2nd/subsequent same-level item).
                */
                doLine: function(line,result,tag) {
                //print("listOLUL.doLine("+line+")");
                var m;
                if( ! (m=this.rx.exec( line )) ) {
                    while( ps.liTags.length ) {
                    result.push('</'+(ps.liTags[ps.liTags.length-1])+'>');
                    ps.liTags = ps.liTags.slice( 0, ps.liTags.length - 1 );
                    }
                    ps.listLevel = 0;
                    ps.liSpaces = [];
                    return -1;
                }
                tag = ('#'==m[2]) ? 'ol' : 'ul';
                var sp = m[1];
                var prevSpace = ps.liSpaces.length ? ps.liSpaces[ps.liSpaces.length-1] : '';
                var prevTag = ps.liTags.length ? ps.liTags[ps.liTags.length-1] : tag;
                if( (sp.length > prevSpace.length) )
                {
                    ++ps.listLevel;
                    ps.liTags.push( tag );
                    result.push('<'+tag+'>');
                    ps.liSpaces.push(sp);
                }
                else if( (sp.length < prevSpace.length) )
                {
                    while( ps.liSpaces.length && (sp.length < prevSpace.length) ) {
                    --ps.listLevel;
                    result.push( '</'+ps.liTags[ps.liTags.length-1]+'>');
                    ps.liTags = ps.liTags.slice( 0, ps.liTags.length - 1 );
                    ps.liSpaces = ps.liSpaces.slice(0, ps.liSpaces.length-1);
                    prevSpace = ps.liSpaces[ps.liSpaces.length-1];
                    }
                }
                else if( 0 == ps.listLevel ) {
                    ++ps.listLevel;
                    ps.liTags.push( tag );
                    result.push('<'+tag+'>');
                    ps.liSpaces.push(sp);
                }
                else if( prevTag != tag )
                { // special case: different list types at same indention level.
                    result.push( '</'+ps.liTags[ps.liTags.length-1]+'>');
                    ps.liTags[ps.liTags.length - 1] = tag;
                    result.push('<'+tag+'>');
                }
                result.push('<li'
                    //+' debug="ps.listLevel='+ps.listLevel+' spaces.length='+sp.length+' tag='+tag+'"'
                    +'>'+ps.parseInlined(m[3])+'</li>');
                return 1;
                }
            }/*listOLUL*/,

            blockquote: {
                rx: /^\s+(\S.*)/,
                
                /**
                * Parses blockquote blocks. Note that there is a syntactic
                * collision with the OL/UL list types, which also require
                * leading spaces. The list handler MUST come before this handler
                * in order to avoid that list blocks become part of
                * block-quoted data. The current handling is not identical
                * to GoCo's, but this is one of the mildly unfortunate corner
                * cases i can live with for the time being.
                */
                doLine: function(line,result) {
                //print("TRYING CODE:",line);
                var m;

                if( !(m = this.rx.exec(line)) ){
                    ps.inBQ = 0;
                    //if( m[1] ) result.push( m[1]);
                    result.push('</blockquote>');
                    return -1;
                }

                ++ps.inBQ;
                if( ps.inBQ == 1 ) {
                    result.push('<blockquote>');
                    //m = this.rx.exec(line);
                    if( m && m[1] ) result.push( ps.parseInlined(m[1]));
                    //print("STARTING CODE:",line);
                }
                else {
                    result.push( ps.parseInlined( line ) );
                    // .replace(/&/g,'&amp;').replace(/</g, '&lt;')
                }
                return 1;
                }
            }/*blockquote*/
        }/*block*/
    }/*rx*/;

}/*GoogleCodeWikiParser()*/;

/**
    Internal helper function. It:

    - Creates a header element of the given level (e.g. H1, H2,...)
    using the given label ($1).

    - Certain characters are stripped from $1 to create an A NAME tag.

    - The parser's internal table of contents is updated to include the
    new entry (but not rendered until parsing is complete).

    Returns the Hn-tagged element (as a string) suitable for HTML output.
*/
GoogleCodeWikiParser.prototype.headerReplace = function headerReplace(lvl, $1) {
    var norm = $1.replace(/\s+/g,'_')
                .replace(/[\W]/g,'_') // FIXME: confirm exactly which chars GoCoWi removes!
                ;
    this.hList.push({
        level: lvl,
        name: $1,
        href: '#'+norm
    });
    return '<h'+lvl+'><a name="'+norm+'"></a>'+$1+'</h'+lvl+'>';
};

/**
    Internal helper function. Returns a function
    which itself expects the arguments ($0,$1)
    (from one of the GoogleCodeWikiParser.prototype.rx.headers
    regexes) and returns headerReplace(N,$1).
*/
GoogleCodeWikiParser.prototype.headN = function headN(N) {
    var n = N;
    var self = this;
    return function($0, $1) { return self.headerReplace(n, $1); };
};

/** 
 * For internal use. 
 */
GoogleCodeWikiParser.prototype.tagsFor = {
  '*': 'strong',
  '_': 'em',
  '^': 'sup',
  '~': 'strike',
  ',': 'sub'
};

/** 
 * Configurable per-instance options. 
 */
GoogleCodeWikiParser.prototype.options = {
  /**
   * If true, certain warning messages
   * are elided.
   */
  disableWarnings: false,
  /**
  * The separator character used when joining parsed lines (or blocks) together.
  * Set it to an empty string to save some space. Set it to '\n' for stuff
  * which should be human-readable. Set it to '\b' or a null/undefined value
  * if you prefer undefined results.
  *
  * Update 20110425: don't change this value! Doing so leads to unpredictable
  * results, like mis-joined lines.
  */
  outputSeparator:'\n'
};

/**
 * Returns a visually-distinct HTML SPAN element containing the given text.
 * 
 * If this.options.disableWarnings is true then this function
 * returns an empty string.
 */
GoogleCodeWikiParser.prototype.getWarning = function(text) {
  return this.options.disableWarnings ? 
           '' : 
           '<span style="color:red;background-color:yellow;">WIKI PARSE WARNING: '+text+'</span>';
};

/** 
 * Some elements are easier to handle if we convert them to an 
 * equivalent form of another element...
 * 
 * BUG: these break down when they are inside a backtick block :/.
 * But since {{{...}}} and `...` are aliases, we don't expect one
 * to be embedded within the other all that often.
 * 
 * TODO: move the {{{ }}} handling into the char-by-char parsing bits.
 */
GoogleCodeWikiParser.prototype.aliases = [
  /** 
   * Converts INLINED {{{...}}} to `...`.
   * 
   * Maintenance reminder: this pattern must not be global (//g) due to
   * a bug in 2 out of 5 browsers i tested, which causes matches to
   * be inconsistently overlooked. (My poor, poor Chrome was one of
   * the two.) Instead we walk the pattern incrementally.
   */
  [/(\{{3}([^\}]+)\}{3})/, '`$2`']
];

GoogleCodeWikiParser.prototype.parseAliases = function(snippet) {
  var x, pair, length = this.aliases.length;
  
  // ^^^ reminder: this breaks if the aliased elements are in backticks.
  for( x = 0; x < length; ++x ) {
    pair = this.aliases[x];
    
    // See comments in GoogleCodeWikiParser.prototype.aliases for why we loop this way.
    while( pair[0].test(snippet) ) { 
      snippet = snippet.replace( pair[0], pair[1] );
    }
  }
  return snippet;
};

/**
*  Scans text for backtick characters. If it finds one, then:
*
*  All characters between the opening and closing backtick
*  are:
*
*  - Wrapped in a TT tag.
*  - Any characters which are also other inline markup characters
*    will be replaced by their &#0NNN; form so that further parsing
*    on the string will not treat them as markup.
*
*  Characters not inside backticks are left as-is.
*
*  @param {String} text = one logical line of wiki text.
*  @return {String} text, with the above-described transformations.
*/
GoogleCodeWikiParser.prototype.parseLineVerbatim = function(text) {
  if( ! text ) return text;
  var ch, i, x,
      buf = '',
      end = text.length,
      out = []
      ;
  function pushbuf() {
    if( buf ) {
      out.push(buf);
      buf = '';
    }
  };
  for( i = 0; i < end; ++i )
  {

    ch = text.charAt(i);
    if( (undefined !== ch) && ('`' !== ch) )
    {
      out.push(ch);
      continue;
    }
    pushbuf();
    // Reminder to self: interesting corner-case bug: wiki code= Should '_Bold' (`'_Bold '`) be emphasized?
    out.push('<tt>');
    for( x = i+1; x < end; ++x ) {
        ch = text.charAt(x);
        if( (undefined ===ch) || ('`' === ch) ) break;
        buf += ch;
    }
    buf = buf.replace( /&/, '&amp;' )
          .replace(/</g,'&lt;')
          // Keep further inline markup within this block from being parsed later on:
          .replace(/[!_*,^~\[]/g, function($0) { return '&#0'+$0.charCodeAt(0)+';'; })
          ;
    pushbuf();
    if( '`' !== ch ){
      out.push( this.getWarning("unterminated backtick!") );
    }
    out.push('</tt>');
    i = x;
  }
  pushbuf();
  return out.join('');
};

/**
 * Parses text for the following markup types:
 *  - The special-case #summary and #labels markers.
 *  - inlined markup (bold, emphasis, etc.)
 *  - headers
 *  - HR elements
 *  
 * Returns the HTML-ized string, or an empty string if !text.
 *  
 * This can be used to parse snippets of code which one
 * knows will not contain lists, tables, and such.
 */
GoogleCodeWikiParser.prototype.parseInlined = function(text) {
  text = ''+text;
  
  if( !text || !text.length ) {
    return '';
  }

  var end, i, x, ch,
      buf = "",
      space, ma,
      marker, tag,
      skipIfPrevIs = /[a-zA-Z]/,
      out = [];
  
/*    if( (m = /^(\s+)/.exec(text) ) )
    { // strip leading spaces but keep them for later?
        space = m[1];
        text = text.substr( space.length );
    }
*/
  function append(val) {
    out.push(val);
  }
    
  function pushbuf() {
    if( buf.length ) {
      append(buf);
      buf = "";
    }
  }
  
  var headersCount = this.rx.headers.length;
  for( i = 0; i < headersCount; ++i ) {
    x = this.rx.headers[i];
    
    //if( ! x || !x[0] ) continue; // WTF is x[0] EVER undefined!?!?!?
    if( ! x[0].test(text) ) continue;
    
    //alert('typeof text == '+(typeof text));
    var old = text;
    text = text.replace( x[0], x[1] );

    // special case for #summary, #labels, #whatever: treat all content as unparseable. (fixes issue #10)
    if( /^#/.test( old ) ) {
      buf += text;
      pushbuf();
      return out.join('');
    }
    
    old = null;
    break;
  }

  text = this.parseLineVerbatim( this.parseAliases(text) );
  if( ma = /^\s*(-{4,})([^-]?.*)/.exec(text) ) {
    text = '<hr/>' + (ma[2] || '');
    if( !ma[2] ){
      buf += text;
      pushbuf();
      return out.join('');
    }
  }
  
  end = text.length;
  var prevChar;
  for ( ch = 0, i = 0; i < end; ++i ) {
    prevChar = ch;
    // A var with the same name ?
    var ch = text.charAt(i);
    if( /\s/.test(ch) ) {
      buf += ch;
      continue;
    }
    
    
    
    // treat all HTML markup as literal - do no parse it.
    else if( '<' === ch ) {
      //allow standalone '<', where next character is not alpha. Transform to &lt;.
      if( ! /[a-zA-Z\/]/.test(text.charAt(i+1)) ) { 
        buf += '&lt;';
        pushbuf();
        continue;
      }
      
      buf += ch;
      for( x = i+1; x < end; ++x ) {
        ch = text.charAt(x);
        //print('ch =',ch);
        buf += ch;
        if( '>' === ch ) break;
      }
      
      if( '>' !== ch ) {
        buf += this.getWarning("unterminated less-than!");
      }
      pushbuf();
      i = x;
      continue;
    }
    
    // [WikiWord(\s+description)?], [http://...(\s+description)?]
    else if( '[' === ch ) {
      // do not start markup in the middle of a word
      if( prevChar && !/\W/.test(prevChar) ) {
          buf += ch;
          continue;
      }
      // Only start a wiki link if the next char is alpha or '/' or '#' (issue #17)
      // 20110429: changed [a-zA-Z] to [\w], which isn't _quite_ what i want,
      // but i need to support umlauts and don't want to write a mega-regex
      // to match it.
      else if( ! /[\/a-zA-Z#\u00c0-\uc00d6\u00d8-\u00f6\u00f8-\u00ff]/.test(text.charAt(i+1)) ) {
          buf += '[';
          continue;
      }
      
      pushbuf();
      
      for( x = i+1; x < end; ++x ){
        ch = text.charAt(x);
        //print('ch =',ch);
        if( ']' === ch ) break;
        buf += ch;
      }
      
      if( ']' !== ch ) {
        buf += this.getWarning("unterminated '['!");
      }
      else {
        var sp = buf.split(/\s+/, 2);
        tag = (sp && (sp.length>1))
            ? buf.substr( sp[0].length+1/*the space*/ )
            : (sp ? sp[0] : 'WIKI_PARSING_ERROR'/*should never happen!*/);
        buf = this.createLink( sp[0], tag );
      }
      
      pushbuf();
      i = x;
      continue;
    }
    
    // !UnlinkedWikiWord or a plain old '!'
    else if( '!' === ch ) {
      marker = i;
      pushbuf();
      
      for( x = i+1; /\w/.test( (ch = text.charAt(x)) ); ) {
        buf += ch;
        ++x;
      }
      
      if( ma = this.rx.wikiWord.exec(buf) ) {
        buf = ma[1]+ch;
        pushbuf();
        i = x;
      }
      else {
        i = marker;
        buf = '!';
        pushbuf();
      }
      continue;
    }
    
    // *BOLD*, _EMPHASIZE_, ^SUPERSCRIPT^
    else if( ma = /([\*_^])/.exec(ch) ) {
      /* special/corner case: do not start markup in the middle of a word

      GoCo's handling is a bit more extensive when it comes to distinguishing
      beteen begin/middle/end-of-word. e.g. for the inline markup:

      __FILE__ == <em></em>FILE<em></em>,

      a~~b~~c~~ d == a~~b~~c <strike>d</strike>

      The pattern is the same regardless of the (inline) markup type, so at
      least it's consistent.
      */
      if( prevChar && skipIfPrevIs.test(prevChar) ) {
        buf += ch;
        continue;
      }
      pushbuf();
      tag = this.tagsFor[ma[1]];
      append('<'+tag+'>');
      for( x = i+1; x < end; ++x ) {
        ch = text.charAt(x);
        if( ma[1] === ch ) break;
        buf += ch;
      }
      if( ma[1] !== ch ) {
        buf += this.getWarning("unterminated '"+ma[1]+"'!");
      }
      else if( buf ) {
        buf = this.parseInlined(buf);
      }
      pushbuf();
      append('</'+tag+'>');
      i = x;
      continue;
    }
    
    // ~~strike~~ or ,,subscript,,
    else if( ma = /([~,])/.exec(ch) ) {
      // Reminder to self: we differ from GoCo here: "will~~strike~~ start in the middle of a word?"
      // but IMO GoCo's behaviour is incorrect there. It is consistent with their other markup
      // handling, though.
      // Special/corner case: do not start markup in the middle of a word
      if( prevChar && skipIfPrevIs.test(prevChar) ) {
        buf += ch;
        continue;
      }
      if( text.charAt(i+1) != ma[1] ) {
        buf += ma[1];
        continue;
      }
      pushbuf();
      x = i+2;
      ch = text.charAt(x);
      tag = this.tagsFor[ma[1]];
      append('<'+tag+'>');
      
      for( ; (x < end) ;) {
        if( ch == ma[1] ) {
          if( text.charAt(x+1) == ma[1] ) {
            ++x;
            break;
          }
        }
        buf += ch;
        ch = text.charAt(++x);
      }
      
      i = x;
      if( ma[1] != ch ) {
        buf = ma[1]+ma[1]+buf; // put the prefix back.
        buf += this.getWarning("mis-terminated '"+ma[1]+ma[1]+"'!");
      }
      else {
        buf = this.parseInlined(buf);
      }
      pushbuf();
      append('</'+tag+'>');
      continue;
    }
    
    else {
      buf += ch;
    }
  }
  pushbuf();
  return out.join('');
};

/**
 * Renders the current state of the table of contents
 * (up to the given maximum header level or some reasonable default).
 * 
 * The TOC state is accumulated during (and reset by) parse().
 * 
 * Bugs: 
 *  - The output list is flat. It should be nested, but i've
 *    been awake for 24 hours, hacking on this code for 16, and
 *    and i can't see straight enough to implement a nested-list TOC.
 *  - Headers with certain markup (e.g. [WikiLinks]) in the
 *    header text do not always render properly.
 */
GoogleCodeWikiParser.prototype.renderTOC = function(maxDepth) {
  maxDepth = maxDepth || 3;
  var hl = this.hList;
  var out = [];
  var h;
  var i;
  var ps = this;
  function render(h) {
    var sp = '';
    for( var x = 0; x < h.level; ++x ) sp += '  ';
    var a = "* <a href='"+h.href+"'"
    //+" SPACE="+sp.length
    +">"+ps.parseInlined(h.name)+"</a>";
    out.push( sp + a );
  }

  for( i in hl ) {
    var h = hl[i];
    if( h.level > maxDepth ) continue;
    render( h );
  }

  var rc, ln;
  var result = [];
  var res;
  for( i = 0; i <= out.length /*need <= for final line.*/; ++i ) {
    res = [];
    ln = out[i];
    //alert( '['+ln+']' );
    rc = this.rx.block.listOLUL.doLine(ln,res);
    if( ! res.length ) break;
    ln = res.join('');
    //alert( '['+ln+']');
    result.push( ln );
    if( rc <= 0 ) break;
  }
  return result.join('');
};

/**
 * Returns HTML code for a link created from the given arguments:
 *  - where is the URL or WikiWord to link to.
 *  - label is an optional label to use for the link. Default=where.
 *  
 * If lbl appears to be an image URL then the returned HTML contain
 * an IMG element and possibly a wrapping ANCHOR element.
 */
GoogleCodeWikiParser.prototype.createLink = function(where,label) {
  //label = label || where;
  var img, ma;
  var rximg = /(gif|jpe?g|bmp|png|tiff?)$/i;
  
  if( rximg.test(label) ) {
    img = label;
  }
  
  if( img ) {
    if( where === label ) {
      return '<img src="'+img+'" />';
    }
    else {
      return '<a href="'+where+'">'+'<img src="'+img+'" /></a>';
    }
  }
  label = label || where;
  return '<a href="'+where+'">'+label.replace(/&/g,'&amp;').replace(/</g,'&lt;')+'</a>';
};

/**
 * Treats text as Google Code wiki syntax and returns an
 * HTML rendering of the document (without BODY/HEAD, etc).
 *
 * The elements parsed are merged by into a string delimited
 * by the character(s) set in this.options.outputSeparator.
 * Block-level elements get only one separator, not one per
 * line of the block (as that would break verbatim/code blocks).
 */
GoogleCodeWikiParser.prototype.parse = function(text) {
  //if( 'string' !== typeof text ) return ''; // Prototype-specific kludge
  this.hList = [];
  this.lines = text ? text.split(/\r?\n/) : [];
  this.lineNo = 0;
  this.blockLines = [];

  var out = [];
  var isblock = [];
  var i = 0,
      k,
      x,
      bl,
      res,
      end = this.lines.length,
      ln;

  for( i = 0; i < end; ++i ) {
    ln = this.lines[i] = this.parseAliases(this.lines[i] /*kludge for inlined {{{...}}}*/);
    if( ! ln || !ln.length ) continue;
    //ln = this.parseAliases(ln);
    /* pre-check the lines for block-level lines and mark them so we can skip parsing/hosing them.*/
    var check;

    for( k in this.rx.block ) {
      if( ! this.rx.block.hasOwnProperty(k) ) continue; // i fucking hate the Prototype lib developers for this.
      check = this.rx.block[k];
      //print("IS #"+i,"a",k,"BLOCK?:",ln);
      if( ! check.rx.test(ln) ) continue;
      //print("#"+i,"IS-A",k,"BLOCK:",ln);
      x = i;
      var rc = undefined;
      for( 1; 1; 1 /*placeholders needed b/c otherwise some packers mis-pack it*/) {
        res = [];
        rc = check.doLine(ln,res);
        if( ! res.length ) break;
        isblock[i] = res.join('');//this.rx.block[k];
//         var kludge = '';
//         for( k in res ) {
//           if( ! res.hasOwnProperty(k) ) continue; // Protoype-specific kludge. Assholes!!!!
//           kludge += res[k];
//         }
//         isblock[i] = kludge;
        if( rc <= 0 ) break;
        //print("#"+i+" IS",k,"BLOCK:",ln);
        ln = this.lines[++i];
      }
      res = null;
      if( rc < 0 ) --i; // back up and try the line again.
      if( undefined !== rc ) break;
    }
  }

  for( i = 0; i < end; ++i ) {
    ln = this.lines[i];
    if( ! isblock[i] ) {
      ln = this.parseInlined( ln );
      this.lines[i] = ln;
    }
  }

  var didMulti = false;
  var emptyCount = 0;
  var prevSkipsBR = false;

  for( i = 0; i < this.lines.length; ++i ){
    ln = this.lines[i];

    if( !ln.length ) {
      ++emptyCount;
      
      continue;
    }

    if( emptyCount ) {
      if( !prevSkipsBR ) {
        //out.push('<br><br>');
        out.push('<p></p>');
        //out.push('<div></div>');
        if( this.enableDebug ) {
            out.push( '<span style="color:red">NON_HEADER_BREAK</span>' );
        }
      }
      emptyCount = 0;
    }

    prevSkipsBR = false;
    didMulti = false;
    
    if( 1 && isblock[i] ) for( k in this.rx.block ) {
      // FIXME: we're duplicating the work we did up above!
      bl = this.rx.block[k];
      prevSkipsBR = true;
      if( bl.rx.test( ln ) ) {
        didMulti = true;
        
        //print("TRYING",k,"ON LINE:",ln);
        res = [];
        for( ; (rc = bl.doLine(ln,res)) > 0; ) {
          ln = this.lines[++i];
          //print("TRYING",k,"ON LINE:",ln);
        }
        
        //print( k,'ended with rc',rc);
        for( k in res ) {
          if( ! res.hasOwnProperty(k) ) continue; // Protoype-specific kludge. Assholes!!!!
          out.push( res[k] );
        }
        res = null;
        if( rc < 0 ) --i; // back up and try the line again.
        break;
        //continue;
      }
    }
    // This next 'else' is broken, broken, broken... but why?
    // Symptom: the closing element of blocks is dropped.
    else if( undefined !== isblock[i] ) {
      //WTF does this miss the closing UL/OL???
      ln = this.parseInlined(isblock[i]);
      didMulti = true;
      prevSkipsBR = true;
      out.push(ln);
      //alert( "BLOCK LINE:\n"+ln);
      continue;
    }

    if( didMulti ) { continue; } // this might not be right when a block element has trailing crap on the closer line.
    else if( i >= end ) break;

    if( /(<\/h\d)|(<\/table)|(<\/[uo]l)|(<\/pre)|(<\/block)(<br)/.test(ln) ) {
      // ^^^ HUGE KLUDGE!
      prevSkipsBR = true;
    }
    out.push(ln);

    emptyCount = 0;
  }

  this.lines = null;

  //one last time to check for <wiki:toc>
  if(1) { 
    var rxtoc = /<wiki:toc\s*(max_depth=["']?(\d+)["']?)?[^>]*>([^<]*<\/wiki[^>]+>)?/;
    var ma;

    for( i = 0; i < out.length; ++i ) {
      ln = out[i];
      if( ! (ma = rxtoc.exec( ln )) ) continue;
      var maxdepth = ma[2] || 3;
      var toc = this.renderTOC(maxdepth);
      out[i] = ln.replace( ma[0], toc  );
      break;
    }
  }
//  return out.join(this.options.outputSeparator);
    console.log(out);
  return out.join(this.options.outputSeparator);
};
/**
    Creates a new GoogleCodeWikiParser instance and returns
    the results of calling its parse(text) member.

    e.g.

    @code
    var html = GoogleCodeWikiParser.parse("... markup code ...");
    @endcode
*/
GoogleCodeWikiParser.parse = function(text) {
    return (new GoogleCodeWikiParser()).parse(text);
};

/**
    Internal testing function. Not for client-side use.
    It requires a JS shell with a print() function which
    sends to stdout (or equivalent).
*/
GoogleCodeWikiParser.test = function() {
  var inp = [
    '=hi, `world`!=',
    '----',
    'some <a href="">inlined HTML</a>.',
    'this should be *bolded* and this _emphasized_.',
    'and a *_bold emphasized_*.',
    'and a *_bold emphasized with {{{verbatim in it}}}_*.',
    '`*` `_` `,,`',
    'A !UnlinkedWikiWord and a ! by itself and !non-wikid and *{{{!InsideVerbatim}}}*.',
    'A _[EmphasizedMarkedUpWikiWord]_.',
    '{{{',
        '  print("Hi, *must not be bolded!* world!");',
        '  if( (i < 10) && (i>1) ) ++a;',
    '}}}',
    '',
    '==List 1==',
    '  # Item 1 *is bolded*.',
    '  # Item 2 _is emphasized_.',
    '  # Item 3 has a [WikiWordLink].',
    '',
    '==List 2==',
    '  * Item 1',
    '  * Item 2',
    '  * Item 3',
    '',
    '|| *header 1* || *header 2* || *header 3* ||',
        '||cell1,1 || cell2,1 || cell 3,1 ||',
        '||cell1,2 || cell2,2 || cell 3,2 ||',
    //'`no closing *backtick*',
    '==List 3==',
    'The source code and API documentation for this class is in:',
    '',
    '  * [http://code.google.com/p/v8-juice/source/browse/trunk/src/include/v8/juice/ClassWrap.h ClassWrap.h]',
    '',
    '~~hi, world!~~',
    ',,hi, world!,,',
    //',,hi, world!,', // should have a warning
    'bye, world!'
  ];
  var g = new GoogleCodeWikiParser();
  var wiki = inp.join('\n');
  var x = g.parse(wiki);
  print(x);
};

if(0) {
  GoogleCodeWikiParser.test();
} // end test
