define([],function (){

    //数组对象转url
    var urlEncode : function (param, key, encode) {
        if(param==null) return '';
        var paramStr = '';
        var t = typeof (param);
        if (t == 'string' || t == 'number' || t == 'boolean') {
            paramStr += '&' + key + '=' + ((encode==null||encode) ? encodeURIComponent(param) : param);
        } else {
            for (var i in param) {
                var k = key == null ? i : key + (param instanceof Array ? '[' + i + ']' : '.' + i);
                paramStr += urlEncode(param[i], k, encode);
            }
        }
        return paramStr;
    }

    return {
        urlEncode:urlEncode
    }

});

//无define引用方法
// var tool = (function (){
//     var testFunc = function (){
//         //you code
//     };
//     return {
//         testFunc:testFunc
//     }
// })();
