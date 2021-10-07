// Array Extensions  v1.0.7
// documentation: http://www.dithered.com/javascript/array/index.html
// license: http://creativecommons.org/licenses/by/1.0/
// code by Chris Nott (chris[at]dithered[dot]com)
// code by Ilya Lebedev (ilya[at]lebedev[dot]net)


// Array.concat() - Join two arrays
if (isUndefined(Array.prototype.concat)) {
  Array.prototype.concat = function (secondArray) {
     var firstArray = this.copy();
     for (var i = 0, saL = secondArray.length; i < saL; i++) {
        firstArray[firstArray.length] = secondArray[i];
     }
     return firstArray;
  };
}

// Array.copy() - Copy an array
if (isUndefined(Array.prototype.copy)) {
  Array.prototype.copy = function() {
     var copy = new Array();
     for (var i = 0, tL = this.length; i < tL; i++) {
        copy[i] = this[i];
     }
     return copy;
  };
}

// Array.pop() - Remove the last element of an array and return it
if (isUndefined(Array.prototype.pop)) {
  Array.prototype.pop = function() {
    var lastItem = undefined;
    if ( this.length > 0 ) {
        lastItem = this[this.length - 1];
        this.length--;
    }
    return lastItem;
  };
}

// Array.push() - Add an element to the end of an array
if (isUndefined(Array.prototype.push)) {
  Array.prototype.push = function() {
     var currentLength = this.length;
     for (var i = 0; i < arguments.length; i++) {
        this[currentLength + i] = arguments[i];
     }
     return this.length;
  };
}

// Array.shift() - Remove the first element of an array and return it
if (isUndefined(Array.prototype.shift)) {
  Array.prototype.shift = function() {
     var firstItem = this[0];
     for (var i = 0, tL = this.length - 1; i < tL; i++) {
        this[i] = this[i + 1];
     }
     this.length--;
     return firstItem;
  };
}

// Array.slice() - Copy several elements of an array and return them
if (isUndefined(Array.prototype.slice)) {
  Array.prototype.slice = function(start, end) {
     var temp;
     
     if (end == null || end == '') end = this.length;
     
     // negative arguments measure from the end of the array
     else if (end < 0) end = this.length + end;
     if (start < 0) start = this.length + start;
     
     // swap limits if they are backwards
     if (end < start) {
        temp  = end;
        end   = start;
        start = temp;
     }
     
     // copy elements from array to a new array and return the new array
     var newArray = new Array();
     for (var i = 0; i < end - start; i++) {
        newArray[i] = this[start + i];
     }
     return newArray;
  };
}

// Array.splice() - Splice out and / or replace several elements of an array and return any deleted elements
if (isUndefined(Array.prototype.splice)) {
  Array.prototype.splice = function(start, deleteCount) {
     if (deleteCount == null || deleteCount == '') deleteCount = this.length - start;
     
     // create a temporary copy of the array
     var tempArray = this.copy();
     
     // Copy new elements into array (over-writing old entries)
     for (var i = start, aL = start + arguments.length - 2; i < aL; i++) {
        this[i] = arguments[i - start + 2];
     }
     
     // Copy old entries after the end of the splice back into array and return
     var dC = deleteCount - arguments.length + 2;
     for (var i = start + arguments.length - 2, tL = this.length - deleteCount + arguments.length - 2; i < tL; i++) {
        this[i] = tempArray[i + dC];
     }
     this.length = this.length - dC;
     return tempArray.slice(start, start + deleteCount);
  };
}

// Array.unshift - Add an element to the beginning of an array
if (isUndefined(Array.prototype.unshift)) {
  Array.prototype.unshift = function(the_item) {
     for (var loop = this.length-1 ; loop >= 0; loop--) {
        this[loop+1] = this[loop];
     }
     this[0] = the_item;
     return this.length;
  };
}

// Array.indexOf - return index of found element or -1 (similar to String.indexOf)
// Don't do the check on 'undefined' because Mozilla does calculate index weirdly
Array.prototype.indexOf = function(needle,begin) {
   for (var i=(null==begin||isNaN(begin)||begin<0)?0:Math.round(begin),len = this.length, idx = -1; idx==-1 & i<len; i++) {
     idx = (this[i]==needle)?i:idx;
   }
   return idx;
};
// Array.lastIndexOf - return index of found element or -1 (similar to String.lastIndexOf)
// Don't do the check on 'undefined' because Mozilla does calculate index weirdly
Array.prototype.lastIndexOf = function(needle,end) {
   for (var i=(null==end||isNaN(end)||end>this.length)?this.length-1:Math.round(end), idx = -1; idx==-1 & i>-1; i--) {
     idx = (this[i]==needle)?i:idx;
   }
   return idx;
};
// Array.map - maps a function on the array elements
if (isUndefined(Array.prototype.map)) {
  /**
   * Mimicking Mozilla behavior
   *
   * @see http://developer.mozilla.org/En/Core_JavaScript_1.5_Reference:Objects:Array:map
   */
  Array.prototype.map = function(func, tp) {
     if ('function' != typeof func) return this;
     var tmp = new Array(this.length);
     for (var loop = this.length-1 ; loop >= 0; loop--) {
        tmp[loop] = func.call(tp, this[loop], loop, this );
     }
     return tmp;
  };
}

if (isUndefined(Array.prototype.unique)) {
  /**
   *  Method removes dumplicate entries
   *  
   *  @return {Array}
   *  @scope public
   */
  Array.prototype.unique = function() /* :Array */{
    var tmp = [];
    for(var i=0, tL=this.length; i<tL; i++ ) {
      if( tmp.indexOf(this[i]) < 0 ) tmp[tmp.length] = this[i];
    }
    return tmp;
  };
}

if (isUndefined(Array.prototype.flatten)) {
  /**
   *  Method flattens 2-dimensional array, when no cols supplied only the cols number from 0th row will be counted 
   *  
   *  @param {Number, Array} cols columns to insert to resulting array
   *  @return {Array}
   *  @scope public
   */
  Array.prototype.flatten = function(cols /* :Array */, cd) /* :Array */{
    if (this.length<1) return [];
    if (isNumeric(cols)) cols = [cols];
    var idx = false;
    if (isArray(cols)) {
      idx = {}; 
      for (var i=0,cL=cols.length;i<cL;i++) idx[cols[i]]=true;
    }
    var tmp = [];
    for (var i=0, tL=this.length; i<tL; i++ ) {
        if (isUndefined(this[i])) continue;
        if (!isArray(this[i])) {
            if (false===idx) tmp[tmp.length] = this[i];
        } else {
            for (var k=0, cL=this[i].length; k<cL; k++) {
                if (false===idx || idx.hasOwnProperty(k)) tmp[tmp.length] = this[i][k];
            }
        }
    }
    return tmp;
  };
}
if (isUndefined(Array.prototype.filter)) {
  /**
   *  Method returns array with non-empty (not evaluating to false) entries
   *  
   *  @param {Function} cbk optional callback function to perform a filter
   *  @return {Array}
   *  @scope public
   */
  Array.prototype.filter = function(cbk /* :Function */) /* :Array */ {
      if (!isFunction(cbk)) cbk = null;
      for (var i=0, tL = this.length, tmp = [], ti=null; i<tL;i++) {
          ti = cbk?cbk(this[i]):this[i];
          if (!isEmpty(ti)) tmp[tmp.length] = ti;
      }
      return tmp;
  };
}
if (isUndefined(Array.prototype.binSearch)) {
  /**
   *  Provides binary search for the sorted array, causes unexpected results on unsorted one
   *
   *  @param {Variant} el element to search for
   *  @return {Number} array index
   *  @scope public
   */
  Array.prototype.binSearch = function (el,key) {
      var l = 0
         ,r = this.length
         ,len = Math.max(r-1,0)
         ,c = Math.ceil(r/2)
         ,cnt = 0
      if (null != key)
          while ((!this[c] || el!=this[c][key]) && r>=l) {
              if (this[c] && el>this[c][key])
                  l=c+1
              else
                  r=c-1
              c=Math.max(0,Math.ceil((r+l)/2))
          }
      else
          while (el!=this[c] && r>=l) {
              if (el>this[c])
                  l=c+1
              else
                  r=c-1
              c=Math.max(0,Math.ceil((r+l)/2));
          }
      return c
  }
}
/**
 *  heap sort ( N log N  )
 *
 *  @author Denis Usenko
 *  @scope public
 */
Array.prototype.heapSort = function () {

    // prepare the array with special sorting method
    /**
     *  Innersorting method for the heap sort
     *
     *  @param {Number} low
     *  @param {Number} up
     */
    var sift = function (arr, low, up) {
            var c, tmp = arr[low];
        
            while(true){
                c = (low << 1) + 1;
                if( c > up ) break;
                if( c < up && arr[c+1] > arr[c] ) c++;
                if( tmp >= arr[c] ) break;
                arr[low] = arr[c]; // 1/2 (mini) swap ))
                low = c;
            }
            arr[low] = tmp;
        },
        tmp,
        maximal = this.length - 1,
        i = maximal >> 1;

    while( i >= 0) 
        sift(this, i--, maximal);

    i = maximal;
    while( i > 0 ){
        // full swap
        tmp = this[0];
        this[0] = this[i];
        this[i] = tmp;
        sift(this,0,--i);
    }
    return this;
}
//-----------------------------------------------------------------------------
// STATIC METHODS
//-----------------------------------------------------------------------------
if (isUndefined(Array.range)) {
  /**
   *  Method creates the array with values in the specified range
   *  1 argument: create array from min(0, end) to max(0, end) with increment 1
   *  2 arguments: create array from min(start, end) to max(start, end) with increment 1
   *  3 arguments: create array from min(start, end) to max(start, end) with increment inc
   *  
   *  @param {Number} end end position
   *  @param {Number} start start position
   *  @param {Number} inc increment
   *  @return {Array} 
   *  @scope public
   */
  Array.range = function(end /* :Number */, start /* :Number */, inc /* :Number */) /* :Array */{
    if (!isNumber(end)) return null;
    if (!isNumber(inc)) inc = 1;
    if (!isNumber(start)) start = 0;
    var tmp = []
       ,mn = Math.min(start, end)
       ,mx = Math.max(start, end)
       ,i = Math.abs(inc) 
       ,cnt = -1;
    do {
        cnt++; 
        tmp[cnt] = mn;
        mn += i;
    } while (mn<=mx);
    return inc>0?tmp:tmp.reverse();
  };
}
