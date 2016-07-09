/*陈建 创建*/
var UI = {
    /*=================/
     tab选项卡
     tabNavBox:'#tabboxs', 				最大的BOX容器
     tabNavObj:'.tabNav',  				选项卡UL样式
     tabNavBtn:'li',								选项卡下面的LI
     tabContentObj:'.tabContent', 	控制下面box
     tabContent:'.list',						控制box下面的隐藏显示层
     currentClass:'current', 				选项卡的样式
     eventType:'click',    				选项卡的点击方式
     onActiveTab: null							选项卡的点击的扩展方法
     controlUnit:true,    					控制选项可不可会
     controlClass:null							启用选项卡样式
     * 2014-06-13 陈建 创建
     ====================*/
    Tab: function (options) {},

    CreateTag: function (options) {}
};

// tab选项卡
UI.Tab = function (options) {
    var defaults = {
        tabNavBox: '#tabboxs',
        tabNavObj: '.tabNav',
        tabNavBtn: 'li',
        tabContentObj: '.tabContent',
        tabMeunObj: '.tabmenu',
        tabContent: '.list',
        currentClass: 'current',
        currenticon: 'up',
        eventType: 'click',
        onActiveTab: null,
        controlUnit: false,
        controlClass: 'active'
    };
    // 处理默认参数
    var opts = $.extend({}, defaults, options);
    $(opts.tabNavBox).each(function () {
        var $this = $(this),
            $tabNavObj = $(opts.tabNavObj, $this),
            $tabContentObj = $(opts.tabContentObj, $this),
            $tabMeunObj = $(opts.tabMeunObj, $this),
            $tabNavBtns = $(opts.tabNavBtn, $tabNavObj),
            $tabContentBlocks = $(opts.tabContent, $tabContentObj);
        //菜单按钮
        var prevBtn = $tabMeunObj.find(".prev");//上一步
        var nextBtn = $tabMeunObj.find(".next");//下一步
        var compBtn = $tabMeunObj.find(".complete");//完成

        $tabNavBtns.bind(opts.eventType, function () {
            var $that = $(this);
            var _index;
            //判断是否有controlUnit是不是开启
            if (opts.controlUnit) {
                $tabNavObj.addClass("activenav");
                $tabNavBtns.eq(0).addClass(opts.controlClass);//添加active属性
                $tabActiveNavBtns = $("." + opts.controlClass, $tabNavObj);
                _index = $tabActiveNavBtns.index($that);

            } else {
                _index = $tabNavBtns.index($that);
            }


            if (_index == -1) return; //当没有active是返回
            OpenTabEvent(_index, true);

        }).eq(0).trigger(opts.eventType);


        //上一步点击事件
        prevBtn.click(function () {
            var currentOBJ = $("." + opts.currentClass, $tabNavObj);//当前选项卡
            var _index = $tabNavBtns.index(currentOBJ);
            if (_index == 0) {
                return;
            } else {
                OpenTabEvent(_index - 1, true);
            }
        });
        //下一步点击事件
        nextBtn.click(function () {
            var currentOBJ = $("." + opts.currentClass, $tabNavObj);
            //判断是否有controlUnit是不是开启
            _index = $tabNavBtns.index(currentOBJ);
            if (opts.controlUnit) {
                $tabNavBtns.eq(_index + 1).addClass(opts.controlClass);//添加active属性

            }
            nextBtndisabled(_index, $tabNavBtns.length);


        });
        //下一步点击事件时判断是不是最大选项卡
        function nextBtndisabled(mun, maxlength) {
            maxlength = maxlength - 1;//共有多少个，length是从1开始，index是从0开始。所以减1
            if (mun == maxlength) {
                return;
            } else {
                OpenTabEvent(mun + 1, true);
            }
        }


        //根据index打开选项卡
        function OpenTabEvent(mun, isClick) {
            if (mun == null) {
                return;
            } else if (mun == 0) {
                prevBtn.attr("disabled", true);
                nextBtn.show();
                compBtn.hide();
            } else if (mun == ($tabNavBtns.length - 1)) {
                nextBtn.hide();
                compBtn.show();
            } else {
                prevBtn.attr("disabled", false);
                nextBtn.show();
                compBtn.hide();
            }
            $tabNavBtns = $(opts.tabNavBtn, $(opts.tabNavObj, opts.tabNavBox));
            $tabNavIcon = $('.fa', $(opts.tabNavObj, opts.tabNavBox));
            $tabContentBlocks = $(opts.tabContent, $(opts.tabContentObj, opts.tabNavBox));
            $tabNavBtns.eq(mun).addClass(opts.currentClass).siblings(opts.tabNavBtn).removeClass(opts.currentClass);
            //更新Icon
            $tabNavIcon.removeClass(opts.currenticon);
            $tabNavIcon.eq(mun).addClass(opts.currenticon);
            $tabContentBlocks.eq(mun).show().siblings(opts.tabContent).hide();

            //扩展方法传递
            if (opts.onActiveTab != null && typeof (opts.onActiveTab) == "function") {
                var result = opts.onActiveTab(mun, $tabNavBtns);
                if (result != null && result == false)
                    return false;
            }


        }

        this.Active = OpenTabEvent;
        return this;


    });// 保存JQ的连贯操作结束
};

//创建标记
UI.CreateTag=function(options){
    var defaults = {
        sourcebox:"#name_rules_list",
        sourcetag:"li",
        targetbox:"#targetbox",
        targethtml:'<li class="search-choice mr5 {select}" model="{model}" ruleid="{ruleid}"><span>{text}</span><a class="fa fa-times closebtn ml5"></a></li>'
    };
    // 处理默认参数
    var opts = $.extend({}, defaults, options);
    var SourceBoxObj=$(opts.sourcebox);
    var SourcetagObj=$(opts.sourcetag,SourceBoxObj);
    var TargetBoxObj=$(opts.targetbox);
    SourcetagObj.each(function(i,e) {
        var targethtml = opts.targethtml.replace("{text}",$(this).text());
        var $inmodel = {'1':'audiences','2':'marketing'},
        	typeid = $('#select_rule').val(),
        	$model = $inmodel[typeid],
        	$typeid = 'ruletype' + typeid;
        	targethtml = targethtml.replace("{ruleid}",$(this).attr('ruleid'));
        	targethtml = targethtml.replace("{select}",$typeid );
        	targethtml = targethtml.replace("{model}",$model);
        $(this).bind("click",function() {
        	var len = TargetBoxObj.find('li.'+$typeid).length;
        	if (len > 0) {
        		//$('#targetbox li').remove();
        		TargetBoxObj.find('li.'+$typeid).remove();
        	}
        	TargetBoxObj.append(targethtml);
        	var len = TargetBoxObj.find('li').length;
            $('#select_count').text(len);
            $('#audiencesid').val('');$('#marketingid').val('');
            TargetBoxObj.find('li').each(function(){
            	var _model = $(this).attr('model'); 
            	$('#'+_model+'id').val($(this).attr('ruleid'));
            });
        });
    });
    TargetBoxObj.on("click",".closebtn",function(){
        var parentLi=$(this).parents("li");
        parentLi.remove();
    	var len = TargetBoxObj.find('li').length;
        $('#select_count').text(len);
        $('#audiencesid').val('');$('#marketingid').val('');
        TargetBoxObj.find('li').each(function(){
        	var _model = $(this).attr('model'); 
        	$('#'+_model+'id').val($(this).attr('ruleid'));
        });
    });
};
	
//创建标记
UI.CreateContactsTag=function(options){
    var defaults = {
        sourcebox:"#name_rules_list",
        sourcetag:"li",
        targetbox:"#targetbox",
        targethtml:'<li class="search-choice mr5 {select}" model="{model}" count="{count}" ruleid="{ruleid}"><span>{text}</span><a class="fa fa-times closebtn ml5"></a></li>'
    };
    // 处理默认参数
    var opts = $.extend({}, defaults, options);
    var SourceBoxObj=$(opts.sourcebox);
    var SourcetagObj=$(opts.sourcetag,SourceBoxObj);
    var TargetBoxObj=$(opts.targetbox);
    SourcetagObj.each(function(i,e) {
        var targethtml = opts.targethtml.replace("{text}",$(this).text());
        var $inmodel = {'1':'audiences','2':'marketing'},
        	typeid = $('#select_rule').val(),
        	$model = $inmodel[typeid],
        	$typeid = 'ruletype' + typeid;
        	targethtml = targethtml.replace("{ruleid}",$(this).attr('ruleid'));
        	targethtml = targethtml.replace("{count}",$(this).attr('count'));
        	targethtml = targethtml.replace("{select}",$typeid );
        	targethtml = targethtml.replace("{model}",$model);
        $(this).bind("click",function() {
        	var len = TargetBoxObj.find('li.'+$typeid).length;
        	var $thisruleid = $(this).attr('ruleid');
        	//将值写入input中，为了方便validate验证是否为空
        	$('#hidden_groups_info').val(1);
        	if ( TargetBoxObj.find('li[ruleid='+$thisruleid+']').length == 0 ) {
            	TargetBoxObj.append(targethtml);
            	var len = TargetBoxObj.find('li').length;
                $('#select_count').text(len);
                $('#audiencesid').val('');
                //$('#marketingid').val('');
                var ids = '';
                var count = 0;
                TargetBoxObj.find('li').each(function(){
                	ids += $(this).attr('ruleid') + ',';
                	count += parseInt($(this).attr('count'));
                });
                $('#audiencesid').val(ids.substring(0,ids.length-1));
                var price = 0.01 * 1000;
                if ($('#channel').val() == 5) {
                    $('#sms_send_count').val(count);
                    //更新计划预算显示
                    update_budget();
                }else{
                    $('#edm_cost').html( count * price / 1000 );  
                }
        	}
        });
    });
    TargetBoxObj.on("click",".closebtn",function(){
        var parentLi=$(this).parents("li");
        parentLi.remove();
    	var len = TargetBoxObj.find('li').length;
    	//将值从input中清空，为了方便validate验证是否为空
    	if(len==0){
    		$('#hidden_groups_info').val('');
    	}
        $('#select_count').text(len);
        $('#audiencesid').val('');
        //$('#marketingid').val('');
        var ids = '';var count = 0;
        TargetBoxObj.find('li').each(function(){
        	ids += $(this).attr('ruleid') + ',';
        	count += parseInt($(this).attr('count'));
        });
        $('#audiencesid').val(ids.substring(0,ids.length-1));
        var price = 0.01 * 1000;
        if ($('#channel').val() == 5){
            $('#sms_send_count').val(count);
            //更新计划预算显示
            update_budget();
        } else{
            $('#edm_cost').html( count * price/1000 );
        }
    });
};

