var setCookie = function(key1,value1,time1,type){

    var times = new Date();//创建时间对象
    var d = 1000*60*60*24;
    var H = 1000*60*60;
    var i = 1000*60;
    var s = 1000;

    switch (type){
        case "d":
            times.setTime(times.getTime()+d*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        case "H":
            times.setTime(times.getTime()+H*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        case "i":
            times.setTime(times.getTime()+i*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        case "s":
            times.setTime(times.getTime()+s*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        default:
            alert("你输入的格式有错误");
            break;
    }

};

var getCookie = function(key2){
    var arr;
    var reg = new RegExp("(^|)"+key2+"=([^;]*)(;|$)");
    if(arr = document.cookie.match(reg)){
        console.log(arr);
        return unescape(arr[2]);
    }else{
        return null;
    }

};

var delCookie = function(key1,value1,time1,type){

    var times = new Date();//创建时间对象
    var d = 1000*60*60*24;
    var H = 1000*60*60;
    var i = 1000*60;
    var s = 1000;

    switch (type){
        case "d":
            times.setTime(times.getTime()-d*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        case "H":
            times.setTime(times.getTime()-H*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        case "i":
            times.setTime(times.getTime()-i*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        case "s":
            times.setTime(times.getTime()-s*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        default:
            alert("你输入的格式有错误");
            break;
    }

};

var editCookie = function(key1,value1,time1,type){

    var times = new Date();//创建时间对象
    var d = 1000*60*60*24;
    var H = 1000*60*60;
    var i = 1000*60;
    var s = 1000;

    switch (type){
        case "d":
            times.setTime(times.getTime()-d*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        case "H":
            times.setTime(times.getTime()-H*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        case "i":
            times.setTime(times.getTime()-i*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        case "s":
            times.setTime(times.getTime()-s*time1);
            document.cookie = key1+"="+escape(value1)+";expires="+times.toGMTString();
            break;
        default:
            alert("你输入的格式有错误");
            break;
    }

};