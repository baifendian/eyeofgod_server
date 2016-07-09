var process_request = "加载中，请稍后...";
function remove_notice(){
	$('div').remove('#showAjaxMsg');//先删了再来
	$("#showAjaxMsg").remove();
}
function showNotice(msg){
	msg = !msg?process_request:msg;
	$('div').remove('#showAjaxMsg');//先删了再来
	$("body").append('<div id="showAjaxMsg">'+msg+'</div>');
	var top = $(window).height() + $(document).scrollTop()-25+'px';
	$("#showAjaxMsg").css({opacity: "1"})
}
$(function(){
	$(document).ajaxStart(function(){
		showNotice();
	});
	$(document).ajaxStop(function(){
		remove_notice();
	});
	$(document).ajaxComplete(function(data){
	   	try{
			if(data=='__NEED_LOGIN__'){
				window.location.href='/home/index/';
			}
		}catch(e){
				
		}
	});
});
/*删除input:type=file的内容*/
function clean_file(id){
	try{
	    var _file = document.getElementById(id);
	    if(_file.files) {
	        _file.value = "";
			return true;
	    }
        if (typeof _file != "object") return null;
        var _span = document.createElement("span");
        _span.id = "__tt__";
        _file.parentNode.insertBefore(_span,_file);
        var tf = document.createElement("form");
        tf.appendChild(_file);
        document.getElementsByTagName("body")[0].appendChild(tf);
        tf.reset();
        _span.parentNode.insertBefore(_file,_span);
        _span.parentNode.removeChild(_span);
        _span = null;
        tf.parentNode.removeChild(tf);
	}catch(e){
			
	}
} 
function number_format(number, decimals, dec_point, thousands_sep) {
  //  discuss at: http://phpjs.org/functions/number_format/
  // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: davook
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Theriault
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Michael White (http://getsprink.com)
  // bugfixed by: Benjamin Lupton
  // bugfixed by: Allan Jensen (http://www.winternet.no)
  // bugfixed by: Howard Yeend
  // bugfixed by: Diogo Resende
  // bugfixed by: Rival
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  //  revised by: Luke Smith (http://lucassmith.name)
  //    input by: Kheang Hok Chin (http://www.distantia.ca/)
  //    input by: Jay Klehr
  //    input by: Amir Habibi (http://www.residence-mixte.com/)
  //    input by: Amirouche
  //   example 1: number_format(1234.56);
  //   returns 1: '1,235'
  //   example 2: number_format(1234.56, 2, ',', ' ');
  //   returns 2: '1 234,56'
  //   example 3: number_format(1234.5678, 2, '.', '');
  //   returns 3: '1234.57'
  //   example 4: number_format(67, 2, ',', '.');
  //   returns 4: '67,00'
  //   example 5: number_format(1000);
  //   returns 5: '1,000'
  //   example 6: number_format(67.311, 2);
  //   returns 6: '67.31'
  //   example 7: number_format(1000.55, 1);
  //   returns 7: '1,000.6'
  //   example 8: number_format(67000, 5, ',', '.');
  //   returns 8: '67.000,00000'
  //   example 9: number_format(0.9, 0);
  //   returns 9: '1'
  //  example 10: number_format('1.20', 2);
  //  returns 10: '1.20'
  //  example 11: number_format('1.20', 4);
  //  returns 11: '1.2000'
  //  example 12: number_format('1.2000', 3);
  //  returns 12: '1.200'
  //  example 13: number_format('1 000,50', 2, '.', ' ');
  //  returns 13: '100 050.00'
  //  example 14: number_format(1e-8, 8, '.', '');
  //  returns 14: '0.00000001'

  number = (number + '')
    .replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}
function sprintf(){
    var arg = arguments,
        str = arg[0] || '',
        i, n;
    for (i = 1, n = arg.length; i < n; i++) {
        str = str.replace(/%s/, arg[i]);
    }
    return str;
}
function SetHome(obj,vrl){
    try{
        obj.style.behavior='url(#default#homepage)';
        obj.setHomePage(vrl);
    }catch(e){
        if(window.netscape){
            try {
                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
            }catch (e){
                alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将 [signed.applets.codebase_principal_support]设置为'true'");
            }
            try{
                var prefs = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefBranch);
                prefs.setCharPref('browser.startup.homepage',vrl);
            }catch(e){}
        }
    }
}
/*************锚点自动定位JS****************/

function call_scroller(arg,limit){
    limit = !limit?100:limit;
    $("html,body").animate({
        scrollTop: $("#"+arg).offset().top - limit
    }, 1000);
}
/****************锚点自动定位JS 结束****************************/

/*text内容长度验证扩展*/
;
(function(){
    $.fn.extend({
        maxLength:function(o){
            var defaults = {
                length:200,
                class_dom:'text_limiter'
            };
            var opt = $.extend({},defaults,o);
            var class_dom = $('.'+opt.class_dom);
            $(class_dom).html(opt.length);
            $(this).keyup(function(){
                return _parse($(this));
            });
            $(this).blur(function(){
                return _parse($(this));
            });
            function _parse(obj){
                var val = $.trim($(obj).val());
                if(val=='')$(class_dom).html(opt.length);
                var t = val.length;
                var l = opt.length-t;
                l = l<=0?0:l;
                $(class_dom).html(l);
                if(l<=0)return $(obj).val(val.substr(0,opt.length));
            }
        }
    });
})(jQuery);
jQuery.cookies=function(h,d,f){
    if(typeof d!="undefined"){
        f=f||{};

        if(d===null){
            d="";
            f=$.extend({},f);
            f.expires=-1
        }
        var j="";
        if(f.expires&&(typeof f.expires=="number"||f.expires.toUTCString)){
            var k;
            if(typeof f.expires=="number"){
                k=new Date();
                k.setTime(k.getTime()+(f.expires*24*60*60*1000))
            }else{
                k=f.expires
            }
            j="; expires="+k.toUTCString()
        }
        var e=f.path?"; path="+(f.path):"";
        var n=f.domain?"; domain="+(f.domain):"";
        var g=f.secure?"; secure":"";
        document.cookie=[h,"=",encodeURIComponent(d),j,e,n,g].join("")
    }
    else{
        var l=h+"=";
        var m=document.cookie.split(";");
        for(var a=0;a<m.length;a++){
            var b=m[a];
            while(b.charAt(0)==" "){
                b=b.substring(1,b.length)
            }
            if(b.indexOf(l)==0){
                return b.substring(l.length,b.length)
            }
        }
        return null
    }
};
function format_bytes(e){
    var d=new Array("Byte","KB","MB","GB","TB","PB","EB","ZB","YB");
    var f=0;
    while(e>1024){
        e/=1024;
        ++f
    }
    return parseFloat(e).toFixed(2)+" "+d[f]
}
function in_array(h,j,k){
    var f="",g=!!k;
    if(g){
        for(f in j){
            if(j[f]===h){
                return true
            }
        }
    }else{
        for(f in j){
            if(j[f]==h){
                return true
            }
        }
    }
    return false
}
function array_unique(c){
    var d=new Array();
    for(i=0;i<c.length;i++){
        if(!in_array(c[i],d)){
            d[i]=c[i]
        }
    }
    return d.join(",").split(",")
}
function sort(s,k){
    var t=[],m=[],r="",q=0,n=false,p=this,o=false,u=[];
    switch(k){
        case"SORT_STRING":
            n=function(a,b){
                return p.strnatcmp(a,b)
            };

            break;
        case"SORT_NUMERIC":
            n=function(a,b){
                return(a-b)
            };

            break;
        case"SORT_REGULAR":default:
            n=function(a,b){
                var c=parseFloat(a),f=parseFloat(b),d=c+""===a,e=f+""===b;
                if(d&&e){
                    return c>f?1:c<f?-1:0
                }else{
                    if(d&&!e){
                        return 1
                    }else{
                        if(!d&&e){
                            return -1
                        }
                    }
                }
                return a>b?1:a<b?-1:0
            };

            break
    }
    for(r in s){
        if(s.hasOwnProperty(r)){
            t.push(s[r]);
            if(o){
                delete s[r]
            }
        }
    }
    t.sort(n);
    for(q=0;q<t.length;q++){
        u[q]=t[q]
    }
    return o||u
}
function empty(c){
    var d;
    if(c===""||c===0||c==="0"||c===null||c===false||typeof c==="undefined"){
        return true
    }
    if(typeof c=="object"){
        for(d in c){
            return false
        }
        return true
    }
    return false
}
function strtotime(f){
    if(f==""||f==null){
        return false
    }
    var d=f.replace(/:/g,"-").replace(/ /g,"-").split("-");
    d[3]=d[3]==undefined?"00":d[3];
    d[4]=d[4]==undefined?"00":d[4];
    d[5]=d[5]==undefined?"00":d[5];
    var e=new Date(Date.UTC(d[0],d[1]-1,d[2],d[3],d[4],d[5]));
    return e.getTime()/1000
}
Date.prototype.format=function(d){
    var f={
        "M+":this.getMonth()+1,
        "d+":this.getDate(),
        "h+":this.getHours(),
        "m+":this.getMinutes(),
        "s+":this.getSeconds(),
        "q+":Math.floor((this.getMonth()+3)/3),
        S:this.getMilliseconds()
    };

    if(/(y+)/.test(d)){
        d=d.replace(RegExp.$1,(this.getFullYear()+"").substr(4-RegExp.$1.length))
    }
    for(var e in f){
        if(new RegExp("("+e+")").test(d)){
            d=d.replace(RegExp.$1,RegExp.$1.length==1?f[e]:("00"+f[e]).substr((""+f[e]).length))
        }
    }
    return d
};

function times_to_date(b){
    var b=new Date(b*1000);
    return b.format("yyyy-MM-dd")
}
function get_start_end_days(l,e){
    var o=strtotime(l);
    var n=strtotime(e);
    var q=(n-o)/(60*60*24);
    var d=new Array();
    for(var p=0;p<=q;p++){
        var m=times_to_date(o+(60*60*24*p));
        d.push(m)
    }
    return d
}

function array_filter(d){
    var c=new Array();
    $.each(d,function(b,a){
        if(!empty(a)){
            c.push(a)
        }
    });
    return c
}
function anTimeToNtime(s){
    var l=sort(s.split(","));
    var t=l.length;
    var p=l[0];
    var r=l[t-1];
    var u=get_start_end_days(p,r);
    var q=new Array();
    $.each(u,function(a,b){
        if(!in_array(b,l)){
            u[a]="---"
        }
    });
    var m=u.join("|").split("---");
    var o=new Array();
    $.each(m,function(b,a){
        if(a!="|"){
            o.push(a)
        }
    });
    var n=new Array();
    if(o){
        $.each(o,function(f,d){
            var g=array_filter(d.split("|"));
            var a=g.length;
            var e=g[0];
            var c=g[a-1];
            if(!empty(e)&&!empty(c)){
                var b=e;
                b+="-";
                b+=c;
                n.push(b)
            }
        })
    }
    return n
}
function remove_enter(){
    $("input").bind("keydown",function(b){
        if(b.keyCode==13){
            b.keyCode=0;
            return false
        }
    })
}
function rand_string(f){
    var g=["a","b","c","d","e","f","h","j","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"];
    var h="";
    for(i=0;i<f;i++){
        var e=g[Math.ceil(Math.random()*100)];
        if(e!=undefined){
            h+=e
        }
    }
    if(h.length<f){
        h+=rand_string(f-h.length)
    }
    return h
}
function load_data_for_get(e,d){
    var f=$.ajax({
        type:"GET",
        data:d,
        url:e,
        async:false,
        cache:false
    });
    return f.responseText
}
function load_data_for_post(e,d){
    var f=$.ajax({
        url:urls+"&rand="+Math.random(),
        type:"POST",
        async:false,
        data:d,
        cache:false
    });
    return f.responseText
}
function get_checkbox_val(c){
    var d=$("."+c).map(function(){
        if($(this).attr("checked")){
            $(this).addClass("append_fix_tag");
            $(this).addClass("append_checkbox_val");
            return $(this).val()
        }
    }).get().join(",");
    return $.trim(d)==""?false:d
}
function clone(h,g){
    var k;
    if(h instanceof Array){
        k=[];
        var j=h.length;
        while(j--){
            k[j]=clone(h[j],g)
        }
        return k
    }else{
        if(typeof h=="function"){
            return h
        }else{
            if(h instanceof Object){
                k={};

                for(var f in h){
                    if(f!="parentNode"){
                        k[f]=clone(h[f],g);
                        if(g&&f=="name"){
                            k[f]+=g
                        }
                    }
                }
                return k
            }else{
                return h
            }
        }
    }
}
function checkDate(g,j){
    var h=j.length;
    var l=false;
    for(var m=0;m<h;m++){
        var k=j[m].split("—");
        if(g>=k[0]&&g<=k[1]){
            l=true;
            break
        }
    }
    if(l){
        alert('已存在此时间');
        return true
    }else{
        return false
    }
}
function clickCloum(f,e,d){
    if(d!=undefined&&d.length>0&&d instanceof Array){
        $("#"+f+" table:first tr th").click(function(){
            var b=0;
            fn=false;
            b=$("#"+f+" table:first tr th").index($(this));
            for(var a=0;a<d.length;a++){
                if(b==d[a]){
                    fn=true
                }
            }
            if(fn==false){
                $("#"+e+" tr").each(function(){
                    $(this).find("td").eq(b).css("background","#f2f2f2")
                })
            }
        })
    }else{
        $("#"+f+" table:first tr th").click(function(){
            var a=0;
            a=$("#"+f+" table:first tr th").index($(this));
            $("#"+e+" tr").each(function(){
                $(this).find("td").eq(a).css("background","#f2f2f2")
            })
        })
    }
}
function turnToMyWay(b){
    if(--b>0){
        setTimeout("turnToMyWay("+b+")",1000)
    }else{
        publicLocation(THINK_URL+"/index")
    }
}
function autoCancel(d,c){
    if(--d>0){
        setTimeout("autoCancel("+d+",'"+c+"')",1000)
    }else{
        $("#"+c).dialog("close")
    }
}
var publicLocation=function(b){
    window.location.href=b||"index"
};

function del(b){
    show_confirm('<div class="alertcon"><img width="32" height="32" border="0" src="/Public/images/i.png">&nbsp;<span>'+muneall.deldelay+"</span></div>",function(){
        $.post(THINK_URL+"/delete",{
            id:b+""
        },function(a){
            show_dialog('<div class="alertcon"><img width="32" height="32" border="0" src="/Public/images/succ.png">&nbsp;<span>'+a+"</span></div>","",350);
            turnToMyWay(2)
        })
    })
}
function lock(b){
    show_confirm('<div class="alertcon"><img width="32" height="32" border="0" src="/Public/images/i.png">&nbsp;<span>'+muneall.lockdelay+"</span></div>",function(){
        $.post(THINK_URL+"/lock",{
            id:b+""
        },function(a){
            show_dialog('<div class="alertcon"><img width="32" height="32" border="0" src="/Public/images/succ.png">&nbsp;<span>'+a+"</span></div>","",350);
            turnToMyWay(2)
        })
    })
}
function unlock(b){
    show_confirm('<div class="alertcon"><img width="32" height="32" border="0" src="/Public/images/i.png">&nbsp;<span>'+muneall.unlockdelay+"</span></div>",function(){
        $.post(THINK_URL+"/unlock",{
            id:b+""
        },function(a){
            show_dialog('<div class="alertcon"><img width="32" height="32" border="0" src="/Public/images/succ.png">&nbsp;<span>'+a+"</span></div>","",350);
            turnToMyWay(2)
        })
    })
}
function showDialog(f,e,d){
    $("#"+f).dialog({
        autoOpen:false,
        width:e,
        height:d,
        bgiframe:true,
        resizable:false,
        modal:true,
        overlay:{
            backgroundColor:"#000",
            opacity:0.5
        }
    });
    $("#"+f).dialog("open")
}
function _show_resize(_this,dom_id){
    $(_this).next().show();
    $(_this).css('background-position','-411px -556px').attr({
        "hide":'false'
    });
    jqgrid_autoWidth(dom_id);
    $(window).resize(function(){ 　　
        jqgrid_autoWidth(dom_id);
    });
}
function _hide_resize(_this,dom_id){
    $(_this).next().hide();
    $(_this).css('background-position','-440px -556px').attr({
        "hide":'true'
    });
    jqgrid_autoWidth2(dom_id);
    $(window).resize(function(){ 　　
        jqgrid_autoWidth2(dom_id);
    })
}

function _auto_resize(_this,dom_id){
    $(_this).toggle(function(){
        _hide_resize($(this),dom_id);
    },function(){
        _show_resize($(this),dom_id);
    });
}
function _resize_fix(_this,dom_id){
    _this.bind('click', function(){
        switch($(this).attr('hide')){
            case 'true':
                _show_resize(_this,dom_id);
                break;
            case 'false':
                _hide_resize(_this,dom_id);
                break;
        }
    });
}
function is_email(email){
    var   pattern   =   /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
    return !pattern.test(email)?false:true;
}
function checkScroll(){
    var f=document.body.scrollHeight||document.documentElement.scrollHeight;
    var a=document.documentElement;
    var e=self.innerHeight||a&&a.clientHeight||document.body.clientHeight;
    return f>e
};
function check_form_is_empty(form_class){
    var result = true;
    $("."+form_class).each(function(){
        var val = $(this).val();
        if(empty(val)){
            $(this).addClass('bfp_empty_input_val');
            result = false;
        }else{
            $(this).removeClass('bfp_empty_input_val');
        }
    });
    return result;
}

//验证数字(整数、浮点数都可以通过)
function isNumber(oNum){
    if(!oNum) return false;
    var strP=/^[\+\-]?\d*?\.?\d*?$/; //正整数
    if(!strP.test(oNum)) return false;
    return true;
}

//验证正整数
function isNumber_zheng(oNum){
    if(!oNum) return false;
    var strP = /^[0-9]*[1-9][0-9]*$/;
    if(!strP.test(oNum)) return false;
    return true;
}
//jqgrid调用
function jqgrid_autoWidth(_id, _minWidth, leftWidth, ie6Num, ieNum, browNum, minNum){
	var ie6Percentage,
		ie9Percentage,
		iePercentage,
		browPercentage,
		$obj;
	if(screen.width > 1024){
		ie6Percentage = 0.96;
		iePercentage = 0.975;
		ie9Percentage = 0.965;
		browPercentage = 0.965;
		
	}else if( screen.width <= 1024 ){
		ie6Percentage = 0.96;
		iePercentage = 1.14;
		browPercentage = 1.14;
	}
	
	//alert(ie6Percentage +'--'+ iePercentage +'--'+ browPercentage);
    _minWidth = _minWidth || 1206,
    leftWidth = leftWidth == 0 ? leftWidth.toString():leftWidth || 178,
	ie6Num = ie6Num || ie6Percentage,
    ieNum = ieNum || iePercentage,
    browNum = browNum || browPercentage;
	
//	_minWidth = _minWidth || 1004,
//    leftWidth = leftWidth == 0 ? leftWidth.toString():leftWidth || 178,
//    ie6Num = ie6Num || 0.96,
//    ieNum = ieNum || 1.14,
//    browNum = browNum || 1.16;

	//alert(_minWidth +'--'+ leftWidth +'--'+ ie6Num +'--'+ ieNum +'--'+ browNum);
	//alert($(window).width() +'--'+ _minWidth);
	$obj = $("#" + _id);
	//console.log($(window).width() +'---'+ _minWidth);
//	console.log($(window).width() > _minWidth);
    if($(window).width() > _minWidth){
        if(!$.browser.msie ){
            window.setTimeout(function(){
				//console.log($(window).width()+'--'+browNum+'--'+leftWidth);
//				console.log($(window).width()*browNum+'--'+leftWidth);
//				console.log(document.body.clientWidth*browNum+'--'+leftWidth);
				//console.log(document.body.clientWidth + '--' + document.body.offsetWidth + '--' + document.body.scrollWidth);
				$obj.setGridWidth($(window).width()*browNum-leftWidth);
				$obj.setGridWidth(document.body.clientWidth*browNum-leftWidth);
            },50)
        }else if($.browser.version === "9.0"){
			$obj.setGridWidth($(window).width()*ie9Percentage-leftWidth);
			$obj.setGridWidth(document.body.clientWidth*ie9Percentage-leftWidth);
		}
	/*	else if($.browser.version == "6.0"){
            $obj.setGridWidth($(window).width()*ie6Num-leftWidth);
            $obj.setGridWidth(document.body.clientWidth*ie6Num-leftWidth);
			alert("1111")
        }*/
		else{
            $obj.setGridWidth($(window).width()*ieNum-leftWidth);
            //$("#" + _id).setGridWidth(document.body.clientWidth*ieNum-leftWidth);
            try{
                $obj.setGridWidth(document.body.clientWidth*ieNum-leftWidth);
            }catch(x){
			
            }
        }
    }else{
//        $obj.setGridWidth(_minWidth * minNum - leftWidth);
		$obj.setGridWidth(_minWidth * 0.97 - leftWidth);
    }
};

/*将object类型变量转换成string类型的*/
function obj2string(o){
    var r=[];
    if(typeof o=="string"){
        return "\""+o.replace(/([\'\"\\])/g,"\\$1").replace(/(\n)/g,"\\n").replace(/(\r)/g,"\\r").replace(/(\t)/g,"\\t")+"\"";
    }
    if(typeof o=="object"){
        if(!o.sort){
            for(var i in o){
                r.push(i+":"+obj2string(o[i]));
            }
            if(!!document.all&&!/^\n?function\s*toString\(\)\s*\{\n?\s*\[native code\]\n?\s*\}\n?\s*$/.test(o.toString)){
                r.push("toString:"+o.toString.toString());
            }
            r="{"+r.join()+"}";
        }else{
            for(var i=0;i<o.length;i++){
                r.push(obj2string(o[i]))
            }
            r="["+r.join()+"]";
        }
        return r;
    }
    return o.toString();
}
/**
* Created with JetBrains WebStorm.
* Author: Shiming
* Date: 2013-03-14
* Time: 23:38:23
* version: 0.1.3
* create this jQuery plugin for ie6,7,8 to fix placeholder attribute
*/
;(function($){
    var Placeholder,
        inputHolder = 'placeholder' in document.createElement('input'),
        textareaHolder = 'placeholder' in document.createElement('textarea');
    Placeholder = {
        ini:function () {
            if (inputHolder && textareaHolder) {
                return false;
            }
            this.el = $(':text[placeholder],:password[placeholder],textarea[placeholder]');
            this.setHolders();
        },
        setHolders: function(obj){
            var el = obj ? $(obj) : this.el;
            if (el) {
				var self = this;
                el.each(function() {
                    var span = $('<label />');
                    span.text( $(this).attr('placeholder') );
					var et = $.browser.msie && $.browser.version<8?0:20;
					var h = $(this).height() + et;
		            span.css({
                        color: '#999',
                        fontSize: $(this).css('fontSize'),
                        fontFamily: $(this).css('fontFamily'),
                        fontWeight: $(this).css('fontWeight'),
                        position: 'absolute',
                        top: $(this).offset().top -7,
                        left: $(this).offset().left-3,
                        width: $(this).width()+10,
                        height: h,
                        lineHeight: h + 'px',
                        textIndent: $(this).css('textIndent'),
                        paddingLeft: 10 ,
                        paddingTop: $(this).css('borderTopWidth'),
                        paddingRight: $(this).css('borderRightWidth'),
                        paddingBottom: $(this).css('borderBottomWidth'),
                        display: 'inline',
                        overflow: 'hidden'
                    })
                    if (!$(this).attr('id')) {
                        $(this).attr('id', self.guid());
                    }
                    span.attr('for', $(this).attr('id'));
                    $(this).after(span);
                    self.setListen(this, span);
                })
            }
        },
        setListen : function(el, holder) {
            if (!inputHolder || !textareaHolder) {
                el = $(el);
                el.bind('keydown', function(e){
                        if (el.val() != '') {
                            holder.hide(0);
                        } else if ( /[a-zA-Z0-9`~!@#\$%\^&\*\(\)_+-=\[\]\{\};:'"\|\\,.\/\?<>]/.test(String.fromCharCode(e.keyCode)) ) {
                            holder.hide(0);
                        } else {
                            holder.show(0);
                        }
                });
                el.bind('keyup', function(e){
                        if (el.val() != '') {
                            holder.hide(0);
                        } else {
                            holder.show(0);
                        }

                })
            }
        },
        guid: function() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random()*16| 0,
                    v = c == 'x' ? r : (r&0x3|0x8);
                return v.toString(16);
            }).toUpperCase();
        }
    }

    $.fn.placeholder = function () {
        if (inputHolder && textareaHolder) {
            return this;
        }
        Placeholder.setListen(this);
        return this;
    }
    $.placeholder = Placeholder;
})(jQuery);
