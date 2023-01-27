// ex. IE‚Åg‚¦‚È‚¢hObject.assignh‚ğ•âŠ®‚·‚éPolyfill‚Èjsƒ‰ƒCƒuƒ‰ƒŠhobject-assign.jsh
// https://cpoint-lab.co.jp/article/201804/ie%E3%81%A7%E4%BD%BF%E3%81%88%E3%81%AA%E3%81%84object-assign%E3%82%92%E8%A3%9C%E5%AE%8C%E3%81%99%E3%82%8Bpolyfill%E3%81%AAjs%E3%83%A9%E3%82%A4%E3%83%96%E3%83%A9%E3%83%AAobject-assi/
// https://gist.github.com/spiralx/68cf40d7010d829340cb#file-object-assign-js  

if (!Object.assign) {
  Object.defineProperty(Object, 'assign', {
    enumerable: false,
    configurable: true,
    writable: true,
    value: function(target) {
      'use strict';
      if (target === undefined || target === null) {
        throw new TypeError('Cannot convert first argument to object');
      }

      var to = Object(target);
      for (var i = 1; i < arguments.length; i++) {
        var nextSource = arguments[i];
        if (nextSource === undefined || nextSource === null) {
          continue;
        }
        nextSource = Object(nextSource);

        var keysArray = Object.keys(Object(nextSource));
        for (var nextIndex = 0, len = keysArray.length; nextIndex < len; nextIndex++) {
          var nextKey = keysArray[nextIndex];
          var desc = Object.getOwnPropertyDescriptor(nextSource, nextKey);
          if (desc !== undefined && desc.enumerable) {
            to[nextKey] = nextSource[nextKey];
          }
        }
      }
      return to;
    }
  });
}
