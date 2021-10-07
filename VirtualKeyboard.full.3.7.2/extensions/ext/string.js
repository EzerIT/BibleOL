/**
 *  $Id: string.js 555 2009-03-13 09:04:42Z wingedfox $
 *  $HeadURL: https://svn.debugger.ru/repos/jslibs/BrowserExtensions/tags/BrowserExtensions.029/ext/string.js $
 * 
 *  @author Ildar Shaimordanov
 *  @author Ilya Lebedev
 *  @license LGPL
 *  @version $Rev: 555 $
 */
/**
 * Converts UTF-32 codes to string of chars
 *
 * @param {Number} code char code
 * @return {String}
 * @scope public
 */
String.fromCharCodeExt = function(code) {
    if (code<0x10000) {
        return String.fromCharCode(code);
    }
    code -= 0x10000;
    return String.fromCharCode(code >>10 | 0xD800)+String.fromCharCode(code & 0x3FF | 0xDC00)
}
/**
 *  Decodes html entities
 *
 *  @return {String} string with decoded entities
 *  @scope public
 */
String.prototype.entityDecode = function() {
    if (!arguments.callee.span) arguments.callee.span = document.createElement('span');
    var s = arguments.callee.span;
    s.innerHTML = this;
    return s.firstChild?s.firstChild.nodeValue:"";
}

/**
 *  Method is used to trim specified chars from the left of the string
 * 
 *  @param {String, Array} c char or char list to be trimmed, default is \s
 *  @return {String}
 *  @scope public
 */
String.prototype.ltrim = function(c) {
    if (isString(c)) c=c.split("");
    if (isArray(c) || isUndefined(c)) {
        c = isEmpty(c)?"\\s":RegExp.escape(c); 
        c = new RegExp("^(?:"+c+")+", "g");
        return this.replace(c, "");
    }
    return this; 
}
/**
 *  Method is used to trim specified list from the right of the string
 * 
 *  @param {String, Array} c char or char sequence to be trimmed, default is \s
 *  @return {String}
 *  @scope public
 */
String.prototype.rtrim = function(c) {
    if (isString(c)) c=c.split("");
    if (isArray(c) || isUndefined(c)) {
        c = isEmpty(c)?"\\s":RegExp.escape(c); 
        c = new RegExp("(?:"+c+")+$", "g");
        return this.replace(c, "");
    }
    return this; 
}
/**
 *  Method is used to trim specified chars from the left and the right of the string
 * 
 *  @param {String, Array} c char or char list to be trimmed, default is \s
 *  @return {String}
 *  @scope public
 */
String.prototype.trim = function(c) {
    if (isString(c)) c=c.split("");
    if (isArray(c) || isUndefined(c)) {
        c = isEmpty(c)?"\\s":RegExp.escape(c); 
        c = new RegExp("^(?:"+c+")+|(?:"+c+")+$", "g");
        return this.replace(c, "");
    }
    return this; 
}

/**
 *  Duplicates the string
 *  
 *  @return {String}
 *  @scope public
 */
String.prototype.dup = function() {
    var val = this.valueOf();
    return [val,val].join("");
}
/**
 *  Repeats string specified number of times 
 * 
 *  @param {Number} n number of times to repeat the string
 *  @return {String}
 *  @scope public
 */
String.prototype.repeat = function(n /* :Number */) /* :String */ {
    if (isNaN(n=parseInt(n)) || n<0) return "";
    return Array(n+1).join(this.valueOf());
}
/**
 *  Pads the string to the specified length 
 * 
 *  @param {Number} n number of times to repeat c
 *                 positive - on the right side
 *                 negative - on the left side
 *  @param {String} c fill char, space is default 
 *  @return {String} 
 *  @scope public
 */
String.prototype.padding = function(n, c) {
    var val = this.valueOf();
    n = parseInt(n);
    if (!n) return val;
    if (isUndefined(c)) c = " ";
    var pad = String(c).charAt(0).repeat(Math.abs(n) - this.length);
    return (n < 0) ? pad + val : val + pad;
}
/**
 *  Pads the string on the right side
 * 
 *  @param {Number} n number of times to repeat c
 *  @param {String} c fill char
 *  @return {String} 
 *  @scope public
 */
String.prototype.padLeft = function(n, c) {
    return this.padding(-Math.abs(n), c);
}
/**
 *  Pads the string on the left side
 * 
 *  @param {Number} n number of times to repeat c
 *  @param {String} c fill char
 *  @return {String} 
 *  @scope public
 */
String.prototype.padRight = function(n, c) {
    return this.padding(Math.abs(n), c);
}

/**
 * sprintf(format, argument_list)
 *
 * The string format function like the one in C/C++, PHP, Perl
 * Each conversion specification is defined as below:
 *
 * %[index][alignment][padding][width][precision]type
 *
 * index         An optional index specifier that changes the order of the 
 *               arguments in the list to be displayed.
 * alignment     An optional alignment specifier that says if the result should be 
 *               left-justified or right-justified. The default is 
 *               right-justified; a "-" character here will make it left-justified.
 * padding       An optional padding specifier that says what character will be 
 *               used for padding the results to the right string size. This may 
 *               be a space character or a "0" (zero character). The default is to 
 *               pad with spaces. An alternate padding character can be specified 
 *               by prefixing it with a single quote ('). See the examples below.
 * width         An optional number, a width specifier that says how many 
 *               characters (minimum) this conversion should result in.
 * precision     An optional precision specifier that says how many decimal digits 
 *               should be displayed for floating-point numbers. This option has 
 *               no effect for other types than float.
 * type          A type specifier that says what type the argument data should be 
 *               treated as. Possible types:
 *
 * % - a literal percent character. No argument is required.  
 * b - the argument is treated as an integer, and presented as a binary number.
 * c - the argument is treated as an integer, and presented as the character 
 *       with that ASCII value.
 * d - the argument is treated as an integer, and presented as a decimal number.
 * u - the same as "d".
 * f - the argument is treated as a float, and presented as a floating-point.
 * o - the argument is treated as an integer, and presented as an octal number.
 * s - the argument is treated as and presented as a string.
 * x - the argument is treated as an integer and presented as a hexadecimal 
 *        number (with lowercase letters).
 * X - the argument is treated as an integer and presented as a hexadecimal 
 *        number (with uppercase letters).
 *        
 *  @return {String}
 *  @scope public
 */
String.prototype.sprintf = function() {
    var args = isArray(arguments[0])?arguments[0]:arguments
       ,index = 0
       ,frmt = this.replace(/%%/g, "\0\0")
       ,re = /%((?:\d+\$)?)((?:[-0+# ])?)((?:\d+|\*(?:\d+\$)?)?)((?:.(?:\d+|\*(?:\d+\$)?))?)([bcdeEfosuxX])/g;
    /*
    * The re.exec() method returns the array with the following properties
    * wich are used in this function
    *       x.index contains the substring position found at the origin string
    *       x[0] contains the found substring
    *       x[1] contains the explicit parameter number
    *       x[2] contains the flags
    *       x[3] contains the minwidth
    *       x[4] contains the precision
    *       x[5] contains the type specifier (as [bcdfosuxX])
    */
    frmt = frmt.replace(re, function() {
        var x = arguments
           ,sign = false
           ,ins;

        /*
        *  calculate min width 
        */
        if (!isUndefined(x[3]) && x[3].indexOf("*")==0) {
            x[3] = parseInt(x[3].replace(/\D/g,""))
            if (isNaN(x[3])) {
              x[3] = args[index];
              /*
              *  increment 
              */
              index++;
            } else {
              x[3] = args[x[3]]  
            }
        }
        /*
        *  calculate precision
        */
        if ("" != x[4]) {
            if (x[4].indexOf("*")==1) {
                x[4] = parseInt(x[4].replace(/\D/g,""))
                if (isNaN(x[4])) {
                  x[4] = args[index];
                  /*
                  *  increment 
                  */
                  index++;
                } else {
                  x[4] = args[x[4]]  
                }
            } else {
                x[4] = x[4].replace(/\D/,"")
            }
            x[4] = Math.abs(x[4]);
        }
        /*
        *  calculate insertion value 
        */   
        x[1] = parseInt(x[1]);
        var ins;
        if (isNumeric(x[1])) {
            ins = args[x[1]];
        } else {
            ins = args[index];
            /*
            *  index should be incremented only when no explicit parameter number is specified  
            */
            index++;
        }
        switch (x[5]) {
        case "b":
            if (ins<0) ins = 0x10000000000000000+parseInt(ins);
            ins = Number(ins).bin(x[4]);
            if (x[4]) ins = ins.substr(0,x[4]);
            if (x[2]=='#') ins = '0b'+ins;
            break;
        case "c":
            ins = String.fromCharCode(ins);
            break;
        case "u":
            ins = Math.abs(ins);  
        case "d":
            ins = Math.round(ins);       
            if (ins<0) {
                ins = "-"+Math.abs(ins).dec(x[4]);
            } else {
                ins = Number(ins).dec(x[4]);
                sign = (x[2] == ' ' || x[2] == '+');
            }
            break;
        case "e":
        case "E":
            if (ins>0) {
                sign = (x[2] == ' ' || x[2] == '+');
            }
            ins = Number(ins).toExponential(x[4]?x[4]:6);
            if (x[5]=='E') ins=ins.toUpperCase();
            break;
        case "f":
            if (ins>0) {
                sign = (x[2] == ' ' || x[2] == '+');
            }
            ins = Number(ins).toFixed(isNumeric(x[4])?x[4]:6);
            break;
        case "o":
            if (ins<0) ins = 0x10000000000000000+parseInt(ins);
            ins = Number(ins).toString(8);
            if (x[4]) ins = ins.substr(0,x[4]);
            if (x[2]=='#' && ins != 0) ins = '0'+ins;
            break;
        case "s":
            ins = String(ins);
            if (x[4]) ins = ins.substr(0,x[4]);
            break;
        case "x":
        case "X":
            if (ins<0) ins = 0x10000000000000000+parseInt(ins);
            ins = Number(ins).hex(-x[4]);
            if (x[4]) ins = ins.substr(0,x[4]);
            if (x[2]=='#') ins = '0x'+ins;
            if (x[5]=='X') ins = ins.toUpperCase();
            break;
        }
        if (sign) ins = x[2]+ins;
        if (x[3]) ins = (x[2]=='-' || x[3]<0)?ins.padRight(x[3]):ins.padLeft(x[3],x[2]=='0'?0:" ");
        return ins;
    })
    return frmt.replace(/\0\0/g, "%");
}
