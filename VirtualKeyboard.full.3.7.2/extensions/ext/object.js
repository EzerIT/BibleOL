/*
*  Checks if property is derived from prototype, applies method if it is not exists
*
*  @param string property name
*  @return bool true if prototyped
*  @access public
*/
if ('undefined' == typeof Object.hasOwnProperty) {
  Object.prototype.hasOwnProperty = function (prop) {
    return !('undefined' == typeof this[prop] || this.constructor && this.constructor.prototype[prop] && this[prop] === this.constructor.prototype[prop]);
  }
}
