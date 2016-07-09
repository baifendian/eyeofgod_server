/*文字链预览显示开始*/
jQuery.validator.addMethod("isEqualOrGreaterThan", function(value, element,
		param) {
	$("label[for='" + param.replace("#", "") + "']").html("");
	if (parseInt(value) >= parseInt($(param).val())) {
		return true;
	} else {
		return false;
	}
}, jQuery.validator.format("必须大于等于起始 大小"));
jQuery.validator.addMethod("isEqualOrLesserThan", function(value, element,
		param) {
	$("label[for='" + param.replace("#", "") + "']").html("");
	if (parseInt(value) <= parseInt($(param).val())) {
		return true;
	} else {
		return false;
	}
}, jQuery.validator.format("必须小于等于最大大小"));

jQuery.validator.addMethod("integer", function(value, element) {
	return this.optional(element) || /^-?\d+$/.test(value);
}, jQuery.validator.format("必须为整数"));

// 不建议使用,建议使用accept
jQuery.validator.addMethod("imgOrFlash", function(value, element, param) {
	var format = value.substring(value.lastIndexOf(".") + 1, value.length)
			.toLowerCase();
	if (format != "gif" && format != "jpg" && format != "jpeg"
			&& format != "swf") {
		return false;
	}
	return true;
}, "");
jQuery.validator.addMethod("isTwoDecimals", function(value) {
	if ($.trim(value) == "") {
		return true;
	}
	var reg = new RegExp("^[0-9]+(.[0-9]{1,2})?$", "g");
	if (!reg.test($.trim(value))) {
		return false;
	}
	return true;
}, "最多只能有两位小数!");
jQuery.validator.addMethod("isMoneyYuan", function(value) {
	var reg = new RegExp("^[0-9]+(.[0-9]{1,2})?$", "g");
	if (!reg.test($.trim(value))) {
		return false;
	}
	return true;
}, "最多只能有两位小数!");

jQuery.validator.addMethod("isMoneyYuanes", function(value) {
	var reg = new RegExp("^[0-9]+(.[0-9]{1,2})?$", "g");
	if($.trim(value)!=''){
		if (!reg.test($.trim(value))) {
			return false;
		}
	}	
	return true;
}, "最多只能有两位小数!");

jQuery.validator.addMethod("checkString", function(value) {
	var iu, iuu, regArray = new Array("￥", "◎", "■", "●", "№", "↑", "→", "↓",
			"@", "$", "^", "*", "(", ")", "|", "[", "]", "？", "~", "`", "<",
			">", "‰", "→", "←", "↑", "↓", "¤", "§", "＃", "＆", "＆", "＼", "≡",
			"≠", "≈", "∈", "∪", "∏", "∑", "∧", "∨", "⊥", "‖", "‖", "∠", "⊙",
			"≌", "≌", "√", "∝", "∞", "∮", "∫", "≯", "≮", "＞", "≥", "≤", "≠",
			"±", "＋", "÷", "×", "Ⅱ", "Ⅰ", "Ⅲ", "Ⅳ", "Ⅴ", "Ⅵ", "Ⅶ", "Ⅷ", "Ⅹ",
			"Ⅻ", "╄", "╅", "╇", "┻", "┻", "┇", "┭", "┷", "┦", "┣", "┝", "┤",
			"┷", "┷", "┹", "╉", "╇", "【", "】", "①", "②", "③", "④", "⑤", "⑥",
			"⑦", "⑧", "⑨", "⑩", "┌", "├", "┬", "┼", "┍", "┕", "┗", "┏", "┅",
			"—", "〖", "〗", "←", "〓", "☆", "§", "□", "‰", "◇", "＾", "＠", "△",
			"▲", "＃", "℃", "※", "≈", "￠", "￥", "◎", "■", "●", "№", "↑", "→",
			"↓", "@", "$", "^", "*", "(", ")", "=", "|", "[", "]", "？", "~",
			"`", "<", ">", "‰", "→", "←", "↑", "↓", "¤", "§", "＃", "＆", "＆",
			"＼", "≡", "≠", "≈", "∈", "∪", "∏", "∑", "∧", "∨", "⊥", "‖", "‖",
			"∠", "⊙", "≌", "≌", "√", "∝", "∞", "∮", "∫", "≯", "≮", "＞", "≥",
			"≤", "≠", "±", "＋", "÷", "×", "Ⅱ", "Ⅰ", "Ⅲ", "Ⅳ", "Ⅴ", "Ⅵ", "Ⅶ",
			"Ⅷ", "Ⅹ", "Ⅻ", "╄", "╅", "╇", "┻", "┻", "┇", "┭", "┷", "┦", "┣",
			"┝", "┤", "┷", "┷", "┹", "╉", "╇", "【", "】", "①", "②", "③", "④",
			"⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "┌", "├", "┬", "┼", "┍", "┕", "┗",
			"┏", "┅", "〖", "〗", "←", "〓", "☆", "§", "□", "‰", "◇", "＾", "＠",
			"△", "▲", "＃", "℃", "※", "≈", "￠");
	iuu = regArray.length;
	for (iu = 1; iu <= iuu; iu++) {
		if (value.indexOf(regArray[iu]) != -1) {
			return false;
		}
	}
	return true;
}, "不能包含特殊字符!");

jQuery.validator.addMethod("checkTimeStartFromTomorrow", function(value,
		element, param) {
	var today = new Date();
	var todayStr = today.getFullYear() + "-";
	if (today.getMonth() + 1 < 10) {
		todayStr = todayStr + "0" + (today.getMonth() + 1);
	} else {
		todayStr = todayStr + (today.getMonth() + 1);
	}
	if (today.getDate() < 10) {
		todayStr = todayStr + "-0" + (today.getDate());
	} else {
		todayStr = todayStr + "-" + (today.getDate());
	}
	if (todayStr > value) {
		return false;
	}
	return true;
}, "");
jQuery.validator.addMethod("checkFilterUrl", function(value, element, param) {
	var filterUrls = $.trim(value);
	var filterUrlArray = new Array();
	if ($.browser.msie)
		filterUrlArray = filterUrls.split("\r\n");
	if ($.browser.mozilla)
		filterUrlArray = filterUrls.split("\n");
	if (filterUrls.length > 0) {
		if (filterUrlArray.length > 100) {
			return false;
		}
		for ( var i = 0; i < filterUrlArray.length; i++) {
			return checkDomain($.trim(filterUrlArray[i]));
		}
	}
	return true;
}, "");
jQuery.validator.addMethod("GBKML_CREATIVE", function(value, element, param) {
	var i, sum;
	sum = 0;
	for (i = 0; i < value.length; i++) {
		if ((value.charCodeAt(i) >= 0) && (value.charCodeAt(i) <= 255)) {
			sum = sum + 1;
		} else {
			sum = sum + 2;
		}

		if (value.charAt(i) == '^' || value.charAt(i) == '{'
				|| value.charAt(i) == '}') {
			sum = sum - 1;
		}
	}

	if (sum < param) {
		return false;
	} else {
		return true;
	}
	return sum;
}, jQuery.validator.format("最短不低于 {0}个字符"));
jQuery.validator.addMethod("KeyWordUrlLength", function(value, element, param) {

	if (value.substring(0, 7) != "http://") {
		value = "http://" + value;
	}

	var i, sum;
	sum = 0;
	for (i = 0; i < value.length; i++) {
		if ((value.charCodeAt(i) >= 0) && (value.charCodeAt(i) <= 255))
			sum = sum + 1;
		else
			sum = sum + 2;
	}
	if (sum <= param) {
		return true;
	} else {
		return false;
	}
	return sum;
}, jQuery.validator.format("url不能超过 {0}个字符"));
jQuery.validator.addMethod("GBKSL", function(value, element, param) {
	var i, sum;
	sum = 0;
	for (i = 0; i < value.length; i++) {
		if ((value.charCodeAt(i) >= 0) && (value.charCodeAt(i) <= 255))
			sum = sum + 1;
		else
			sum = sum + 2;
	}
	if (sum <= param) {
		return true;
	} else {
		return false;
	}
	return sum;
}, jQuery.validator.format("最大不超过 {0}个字符"));
jQuery.validator.addMethod("GBKSL_CREATIVE", function(value, element, param) {
	var i, sum;
	sum = 0;
	for (i = 0; i < value.length; i++) {
		var a = value.charAt(i);
		if (a == "{" || a == "}" || a == "^") {
			// 这三个字符不计入字数
		} else {
			if ((value.charCodeAt(i) >= 0) && (value.charCodeAt(i) <= 255))
				sum = sum + 1;
			else
				sum = sum + 2;
		}
	}
	if (sum <= param) {
		return true;
	} else {
		return false;
	}
	return sum;
}, jQuery.validator.format("最大不超过 {0}个字符"));
jQuery.validator.addMethod("wildcard", function(value) {
	if (value != "" && value.length > 0) {
		var i = value.indexOf("{}");
		if (i != -1) {
			return false;
		}
	}
	return true;
}, "创意默认关键词不能为空!");
jQuery.validator.addMethod("UTF8SL", function(value, element, param) {
	var i, sum;
	sum = 0;
	for (i = 0; i < value.length; i++) {
		if ((value.charCodeAt(i) >= 0) && (value.charCodeAt(i) <= 255))
			sum = sum + 1;
		else
			sum = sum + 3;
	}
	if (sum <= param) {
		return true;
	} else {
		return false;
	}
	return sum;
}, jQuery.validator.format("最大不超过 {0}个字符"));
function checkDomain(nname) {
	var arr = new Array('.com', '.net', '.org', '.biz', '.coop', '.info',
			'.museum', '.name', '.pro', '.edu', '.gov', '.int', '.mil', '.ac',
			'.ad', '.ae', '.af', '.ag', '.ai', '.al', '.am', '.an', '.ao',
			'.aq', '.ar', '.as', '.at', '.au', '.aw', '.az', '.ba', '.bb',
			'.bd', '.be', '.bf', '.bg', '.bh', '.bi', '.bj', '.bm', '.bn',
			'.bo', '.br', '.bs', '.bt', '.bv', '.bw', '.by', '.bz', '.ca',
			'.cc', '.cd', '.cf', '.cg', '.ch', '.ci', '.ck', '.cl', '.cm',
			'.cn', '.co', '.cr', '.cu', '.cv', '.cx', '.cy', '.cz', '.de',
			'.dj', '.dk', '.dm', '.do', '.dz', '.ec', '.ee', '.eg', '.eh',
			'.er', '.es', '.et', '.fi', '.fj', '.fk', '.fm', '.fo', '.fr',
			'.ga', '.gd', '.ge', '.gf', '.gg', '.gh', '.gi', '.gl', '.gm',
			'.gn', '.gp', '.gq', '.gr', '.gs', '.gt', '.gu', '.gv', '.gy',
			'.hk', '.hm', '.hn', '.hr', '.ht', '.hu', '.id', '.ie', '.il',
			'.im', '.in', '.io', '.iq', '.ir', '.is', '.it', '.je', '.jm',
			'.jo', '.jp', '.ke', '.kg', '.kh', '.ki', '.km', '.kn', '.kp',
			'.kr', '.kw', '.ky', '.kz', '.la', '.lb', '.lc', '.li', '.lk',
			'.lr', '.ls', '.lt', '.lu', '.lv', '.ly', '.ma', '.mc', '.md',
			'.mg', '.mh', '.mk', '.ml', '.mm', '.mn', '.mo', '.mp', '.mq',
			'.mr', '.ms', '.mt', '.mu', '.mv', '.mw', '.mx', '.my', '.mz',
			'.na', '.nc', '.ne', '.nf', '.ng', '.ni', '.nl', '.no', '.np',
			'.nr', '.nu', '.nz', '.om', '.pa', '.pe', '.pf', '.pg', '.ph',
			'.pk', '.pl', '.pm', '.pn', '.pr', '.ps', '.pt', '.pw', '.py',
			'.qa', '.re', '.ro', '.rw', '.ru', '.sa', '.sb', '.sc', '.sd',
			'.se', '.sg', '.sh', '.si', '.sj', '.sk', '.sl', '.sm', '.sn',
			'.so', '.sr', '.st', '.sv', '.sy', '.sz', '.tc', '.td', '.tf',
			'.tg', '.th', '.tj', '.tk', '.tm', '.tn', '.to', '.tp', '.tr',
			'.tt', '.tv', '.tw', '.tz', '.ua', '.ug', '.uk', '.um', '.us',
			'.uy', '.uz', '.va', '.vc', '.ve', '.vg', '.vi', '.vn', '.vu',
			'.ws', '.wf', '.ye', '.yt', '.yu', '.za', '.zm', '.zw');

	var mai = nname;
	var val = true;

	var dot = mai.lastIndexOf(".");
	var dname = mai.substring(0, dot);
	var ext = mai.substring(dot, mai.length);

	if (dot > 2 && dot < 57) {
		for ( var i = 0; i < arr.length; i++) {
			if (ext == arr[i]) {
				val = true;
				break;
			} else {
				val = false;
			}
		}
		if (val == false) {
			return false;
		} else {
			for ( var j = 0; j < dname.length; j++) {
				var dh = dname.charAt(j);
				var hh = dh.charCodeAt(0);
				if ((hh > 47 && hh < 59) || (hh > 64 && hh < 91)
						|| (hh > 96 && hh < 123) || hh == 45 || hh == 46) {
					if ((j == 0 || j == dname.length - 1) && hh == 45) {
						return false;
					}
				} else {
					return false;
				}
			}
		}
	} else {
		return false;
	}

	return true;
}

function findpoint1(id,oid) {
	var t = document.getElementById(''+id+'');
	var sel = getInputSelection(t); 
	$("#"+oid+"").val(sel.end);
}

function getInputSelectPosition(c,v) {
	var d = 0;
	var a = 0;
	if (document.selection) {
		
		var b = document.selection.createRange().duplicate();
		if (b.text == "") {
			d = a = v;// 解决ie下b.text == ""的情况
		} else {
			a = getCursorPosition(c);// b.text == ""为空时与ie不兼容，查过不少资料但是尚未找出原因
			d = a - b.text.length;
		}
		
	} else {// firefox
		
		d = c.selectionStart;
		a = c.selectionEnd;
		
	}
	return ([ d, a ]);
}

function getCursorPosition(f) {
	if (f.createTextRange) {
		var c = document.selection;
		var b = c.createRange();
		var a = b.duplicate();
		f.select();
		try {
			b.setEndPoint("StartToStart", c.createRange());
			var g = b.text.length;
			b.collapse(false);
		} catch (d) {
			var g = -1;
		}
		a.select();
		return g;
	} else {
		return (f.selectionStart);
	}
}

function getInputSelection(el) {
    var start = 0, end = 0, normalizedValue, range,
        textInputRange, len, endRange;

    if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
        start = el.selectionStart;
        end = el.selectionEnd;
    } else {
        range = document.selection.createRange();
			
        if (range && range.parentElement() == el) {
            len = el.value.length;
            normalizedValue = el.value.replace(/\r\n/g, "\n");

            // Create a working TextRange that lives only in the input
            textInputRange = el.createTextRange();
            textInputRange.moveToBookmark(range.getBookmark());

            // Check if the start and end of the selection are at the very end
            // of the input, since moveStart/moveEnd doesn't return what we want
            // in those cases
            endRange = el.createTextRange();
            endRange.collapse(false);

            if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                start = end = len;
            } else {
                start = -textInputRange.moveStart("character", -len);
                start += normalizedValue.slice(0, start).split("\n").length - 1;

                if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                    end = len;
                } else {
                    end = -textInputRange.moveEnd("character", -len);
                    end += normalizedValue.slice(0, end).split("\n").length - 1;
                }
            }
        }
    }

    return {
        start: start,
        end: end
    };
}

function availableWord(value, l) {
	displayCreative();// 实时显示创意效果
	//displayModifyCreative();
	var i, sum, w;
	sum = 0;
	w = 0;
	for (i = 0; i < value.length; i++) {
		if (value.charAt(i) == '{' || value.charAt(i) == '}' || value.charAt(i) == '^') {

		} else {
			if ((value.charCodeAt(i) >= 0) && (value.charCodeAt(i) <= 255)) {
				sum = sum + 1;
			} else {
				sum = sum + 2;
			}
		}
	}
	w = l - sum;
	return sum;
}

function getStringNum(value) {
	var i, sum;
	sum = 0;
	for (i = 0; i < value.length; i++) {
		if (value.charAt(i) == '{' || value.charAt(i) == '}'
				|| value.charAt(i) == '^') {

		} else if ((value.charCodeAt(i) >= 0) && (value.charCodeAt(i) <= 255)) {
			sum = sum + 1;
		} else {
			sum = sum + 2;
		}
	}
	return sum;
}

function displayCreative() {
	var title = $("#sem_titleid").val();
	var description1 = $("#sem_remark1").val();
	var description2 = $("#sem_remark2").val();
	var displayUrl = $("#default_display_url").val();
	var mdisplayUrl = $("#mobile_display_url").val();
	var va = 0;
	for(var i=0 ; i < title.length ; i++ ){
		if(title.charAt(i) == "{"){
			for(var j=i+1;j<title.length;j++){
				if(title.charAt(j) == "{"){
					title = title.substring(0,j)+"｛" + title.substring(j+1,title.length);
					continue;
				}else if(title.charAt(j)=="}"){
					break;
				}
				
				if(j == (title.length-1)){
					title = title.substring(0,i)+"｛" + title.substring(i+1,title.length);
				}
			}
		}
	}
	for(var i=0;i<title.length;i++){
		if(title.charAt(i) == "}"){
			for(var j=i-1;j>va;j--){
				if(title.charAt(j) == "}"){
					title = title.substring(0,j)+"｝" + title.substring(j+1,title.length);
				}else if(title.charAt(j)=="{"){
					va=j+1;
					break;
				}
			}
		}
	}

	// 推广位预览
	var t1 = title.replace(
			new RegExp("{", "g"), "<span style='color:red'>").replace(
			new RegExp("}", "g"), "</span>").replace(new RegExp("｛", "g"), "{").replace(new RegExp("｝", "g"), "}");
	$(".pre_title").html(t1);
	
	//右侧推广位和移动设备推广位只显示28个字符，14个汉字
	var right_pos_str = title;
	var tid = right_pos_str.indexOf("^");
	if (tid < 0) {
		tid = right_pos_str.length;
	}
	var ti1 = 0;
	var ti2 = tid;// 得到排除^{}的真实字数
	var tsl = 0;
	for ( var i = 0; (i < ti2 && tsl < 28); i++) {
		if (right_pos_str.charAt(i) == "{" || right_pos_str.charAt(i) == "}") {
			tsl - 1 ;
		} else {
			var re = /[^\u4e00-\u9fa5]/;
			if (re.test(right_pos_str.charAt(i))) {
				tsl += 1;
			} else {
				if (tsl == 27) {
					tsl + 1;
					break;
				} else {
					tsl += 2;
				}
			}
		}
		ti1 += 1;
	}
	right_pos_str= right_pos_str.substr(0,ti1).replace(
			new RegExp("{", "g"), "<span style='color:red'>").replace(
					new RegExp("}", "g"), "</span>").replace(new RegExp("｛", "g"), "{").replace(new RegExp("｝", "g"), "}");
	$(".pre_title").eq(2).html(right_pos_str);
	$(".pre_title").eq(3).html(right_pos_str);
	
	var d12 = description1 + description2;
	d12wn = d12.replace(new RegExp("{", "g"), "").replace(new RegExp("}", "g"),"");
	if (getStringNum(d12wn) > 80) {// ^{}均不算做字数，超过80个字符要折行
		var i1 = 0;
		var i2 = 0;// 得到排除^{}的真实字数
		for ( var i = 0; i < d12.length; i++) {
			if (i1 < 80) {
				if (d12.charAt(i) == "{" || d12.charAt(i) == "}") {
				} else {
					if ((d12.charCodeAt(i) >= 0) && (d12.charCodeAt(i) <= 255))
						i1 = i1 + 1;
					else
						i1 = i1 + 2;
				}
				i2 = i + 1;
			}
		}
		var d1 = d12.substr(0, i2);
		var d2 = d12.substr(i2, d12.length);
		//d12 = d1 + "<br/>" + d2;
		d12 = d1 + d2;
	}
	d12 = d12.replace(new RegExp("{", "gm"), "<span style='color:red'>")
			.replace(new RegExp("}", "gm"), "</span>");
	var de1 = description1.replace(
			new RegExp("{", "g"), "<span style='color:red'>").replace(
			new RegExp("}", "g"), "</span>");
	$(".pre_remark_long").html(d12);
	$(".pre_remark").html(de1);
	$(".pre_displayurl").html(displayUrl);
	$(".append_url").html(de1);
	$(".pre_sendlink").html(displayUrl);
	$(".pre_mdisplayurl").html(mdisplayUrl);
	return ;
}


