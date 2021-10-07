/**************************************************
 *
 *  Extensions for the RegExp object
 *
 *  @author Ilya Lebedev <ilya@lebedev.net>
 *  @modified $Date: 2007-10-26 19:25:39 +0400 (Fri, 26 Oct 2007) $
 *  @version $Rev: 339 $
 *  @license LGPL 2.1 or later
 **************************************************/
/**
 *  Does escape of special regexp characters
 *
 *  Modified version from Simon Willison
 *
 *  @see http://simon.incutio.com/archive/2006/01/20/escape
 *  @param {String, Array} text to escape
 *  @return {String} escaped result
 *  @scope public
 */
RegExp.escape = function(text /* :String, Array */) /* :String */ {
  if (!arguments.callee.sRE) {
    var specials = [
      '/', '.', '*', '+', '?', '|',
      '(', ')', '[', ']', '{', '}', '$', '^', '\\'
    ];
    arguments.callee.sRE = new RegExp(
      '(\\' + specials.join('|\\') + ')', 'g'
    );
  }
  return isString(text)?text.replace(arguments.callee.sRE, '\\$1')
                       :(isArray(text)?text.map(RegExp.escape).join("|")
                                      :"");
}
