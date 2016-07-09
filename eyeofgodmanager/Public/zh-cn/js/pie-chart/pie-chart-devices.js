// JavaScript Document

$(function() {
//饼状图
    var data = [{
        label: "男",
        data: 55,
        color: "#89b522",
    }, {
        label: "女",
        data: 45,
        color: "#c7dd3c",
    }];

    var plotObj = $.plot($("#flot-pie-chart-devices"), data, {
        series: {
            pie: {
                show: true
            }
        },
        grid: {
            hoverable: true
        },
        tooltip: true,
        tooltipOpts: {
            content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
            shifts: {
                x: 10,
                y: 0
            },
            defaultTheme: false
        }
    });
	
	
	Morris.Donut({
        element: 'morris-donut-chart',
        data: [{ label: "Teenage", value: 12 },
            { label: "Youngster", value: 30 },
			{ label: "Young", value: 30 },
			{ label: "Mid-Age", value: 30 },
            { label: "Elder", value: 20 } ],
        resize: true,
        colors: ['#89b522', '#a1cd3a','#c7dd3c','#c3ed95','#a3d9bf'],
    });
	
	Morris.Donut({
        element: 'morris-donut-chart2',
        data: [{ label: "Teenage", value: 12 },
            { label: "Youngster", value: 30 },
			{ label: "Young", value: 30 },
			{ label: "Mid-Age", value: 30 },
            { label: "Elder", value: 20 } ],
        resize: true,
        colors: ['#89b522', '#a1cd3a','#c7dd3c','#c3ed95','#a3d9bf'],
    });
	
	
	
	

            //数据可以动态生成，格式自己定义，cha对应china-zh.js中省份的简称			
		var staStatus = [{ cha: 'HAI', name: '海南', des: '<br/>1个活动点',tel:'13718187045',flag:'hn'},
							{ cha: 'GUD', name: '广东', des: '<br/>无活动点',tel:'13718187045' ,flag:'gd'},
							{ cha: 'YUN', name: '云南', des: '<br/>无活动点',tel:'13718187045' ,flag:'yn'},
							{ cha: 'GXI', name: '福建', des: '<br/>无活动点',tel:'13718187045' ,flag:'fj'},
							 { cha: 'GXI', name: '广西', des: '<br/>无活动点',tel:'13718187045' ,flag:'gx'},
							 { cha: 'TAI', name: '台湾', des: '<br/>无活动点',tel:'13718187045' ,flag:'tw'},
							 { cha: 'GUI', name: '贵州', des: '<br/>无活动点',tel:'13718187045' ,flag:'gz'},
							 { cha: 'HUN', name: '湖南', des: '<br/>无活动点',tel:'13718187045' ,flag:'hun'},
                             { cha: 'JXI', name: '江西', des: '<br/>无活动点',tel:'13718187045' ,flag:'jx'},
                             { cha: 'SCH', name: '四川', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'sc'},
                             { cha: 'TIB', name: '西藏', des: '<br/>2个活动点' ,tel:'13718187045' ,flag:'tb'},
                             { cha: 'ZHJ', name: '浙江', des: '<br/>无活动点',tel:'13718187045'  ,flag:'zj'},
                             { cha: 'CHQ', name: '重庆', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'cq'},
                             { cha: 'HUB', name: '湖北', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'hb'},
                             { cha: 'JSU', name: '江苏', des: '<br/>无活动点',tel:'13718187045' ,flag:'js'},
                             { cha: 'HEN', name: '河南', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'hen'},
                             { cha: 'SHA', name: '陕西', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'sx'},
                             { cha: 'QIH', name: '青海', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'qh'},
							 { cha: 'SHX', name: '山西', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'shx'},
							{ cha: 'SHD', name: '山东', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'shd'}, 
							{ cha: 'NXA', name: '宁夏', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'nx'}, 
							{ cha: 'HEB', name: '河北', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'hbe'}, 
							{ cha: 'XIN', name: '新疆', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'xin'}, 
							{ cha: 'NMG', name: '内蒙', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'nei'}, 
							{ cha: 'TAJ', name: '天津', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'taj'}, 
							{ cha: 'LIA', name: '辽宁', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'lia'}, 
							{ cha: 'JIL', name: '吉林', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'jil'}, 
							{ cha: 'HLJ', name: '黑龙江', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'hlj'}, 
							{ cha: 'GAN', name: '甘肃', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'gan'}, 
							{ cha: 'BEJ', name: '北京', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'bej'}, 
							{ cha: 'MAC', name: '澳门', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'mac'}, 
							{ cha: 'HKG', name: '香港', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'hkg'}, 
							{ cha: 'SHH', name: '上海', des: '<br/>无活动点' ,tel:'13718187045' ,flag:'shh'}, 
                         ];
			
			$('#container1').vectorMap({ map: 'china_zh',
                color: "#96ba2a", //地图颜色
                onLabelShow: function (event, label, code) {
                    $.each(staStatus, function (i, items) {
                        if (code == items.cha) {
                            label.html(items.name + items.des +"<br/>"+ items.tel);
                        }
                    });
                }
				
            });
			
            $.each(staStatus, function (i, items) {
                if (items.flag.indexOf('hn') != -1) {//动态设定颜色，此处用了自定义简单的判断
                    var josnStr = "{" + items.cha + ":'#9dc934'}";
                    $('#container1').vectorMap('set', 'colors', eval('(' + josnStr + ')'));
                }
				if (items.flag.indexOf('gd') != -1) {//动态设定颜色，此处用了自定义简单的判断
                    var josnStr = "{" + items.cha + ":'#9dc934'}";
                    $('#container1').vectorMap('set', 'colors', eval('(' + josnStr + ')'));
                }
				if (items.flag.indexOf('yn') != -1) {//动态设定颜色，此处用了自定义简单的判断
                    var josnStr = "{" + items.cha + ":'#9dc934'}";
                    $('#container1').vectorMap('set', 'colors', eval('(' + josnStr + ')'));
                }
				
            });
          
        
		
			
	window.onload=function(){
		var oTable=document.getElementById('map_tab');
		
		var oTBody = oTable.tBodies[0];
		
		oTBody.innerHTML='<tr>'+
                                	'<td>河北省</td>'+
                                    '<td><span>345678123</span><i id="box" class="box_back1"></i></td>'+
                                    '<td>30%</td>'+
                                    
                                '</tr>'+
                                ' <tr>'+
                                	'<td>河南省</td>'+
                                    '<td ><span>345678</span><i  id="box2" class="box_back2"></i></td>'+
                                    '<td>23%</td>'+                                 
                                '</tr>'+
                                '<tr>'+
                                	'<td>河南省</td>'+
                                    '<td ><span>355631</span><i id="box3" class="box_back3"></i></td>'+
                                    '<td>23%</td>'+ 
                                                                    
                               ' </tr>'+
                               ' <tr>'+
                                	'<td>河南省</td>'+
                                    '<td><span>355631111</span><i  id="box4" class="box_back4"></i></td>'+
                                    '<td>23%</td>'+
                                                                    
                                '</tr>'+
                                '<tr>'+
                                	'<td>河南省</td>'+
                                    '<td><span>355631</span><i  id="box5" class="box_back5"></i></td>'+
                                   ' <td>23%</td>'+
                                                                    
                               '</tr>'+
                               '<tr>'+
                                	'<td>河南省</td>'+
                                    '<td><span id="log">35534</span><i id="box6" class="box_back6"></i></td>'+
                                    '<td>23%</td>'+ 
                                                                    
                                '</tr>'+
								'<tr>'+
                                	'<td>河南省</td>'+
                                    '<td><span>355631</span><i  id="box5" class="box_back5"></i></td>'+
                                   ' <td>23%</td>'+
                                                                    
                               '</tr>'+
							   '<tr>'+
                                	'<td>河南省</td>'+
                                    '<td><span>355631</span><i  id="box5" class="box_back5"></i></td>'+
                                   ' <td>23%</td>'+
                                                                    
                               '</tr>'
							   '<tr>'+
                                	'<td>河南省</td>'+
                                    '<td><span>355631</span><i  id="box5" class="box_back5"></i></td>'+
                                   ' <td>23%</td>'+
                                                                    
                               '</tr>'
							   '<tr>'+
                                	'<td>河南省</td>'+
                                    '<td><span>355631</span><i  id="box5" class="box_back5"></i></td>'+
                                   ' <td>23%</td>'+
                                                                    
                               '</tr>'
							   ;
								
								
		
		
		var oBox=document.getElementById('box');
		
		var oBox2=document.getElementById('box2');
		
		var oBox3=document.getElementById('box3');
		var oBox4=document.getElementById('box4');
		var oBox5=document.getElementById('box5');
		var oBox6=document.getElementById('box6');
		
		var total=3900;
			
		oBox2.style.width=oBox.offsetWidth*(3590/total)+'px';
		oBox3.style.width=oBox.offsetWidth*(2900/total)+'px';
		oBox4.style.width=oBox.offsetWidth*(1900/total)+'px';
		oBox5.style.width=oBox.offsetWidth*(900/total)+'px';
		oBox6.style.width=oBox.offsetWidth*(450/total)+'px';
		var aRow=oTBody.rows;
			for(var i=0;i<aRow.length;i++){
				if(i%2==0){
					aRow[i].style.background='#f9f9f9';	
				}
				var oRowTh=aRow[i].children[0];
				oRowTh.style.paddingLeft='10px';
			}
	};

});

