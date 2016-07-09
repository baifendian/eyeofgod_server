//var data = {
//"msg": "",
//"code": 1,
//"data": {
//  "0": {
//    "A": [{
//      "sourceid": 36,
//      "text": "\u5973\u5750\u4fbf",
//      "textid": 4,
//      "state": 1,
//      "location": "A",
//      "subscription": 0
//    }, {
//      "sourceid": 37,
//      "text": "\u5973\u5750\u4fbf",
//      "textid": 4,
//      "state": 1,
//      "location": "A",
//      "subscription": 0
//    }, {
//      "sourceid": 38,
//      "text": "\u5973\u5750\u4fbf",
//      "textid": 4,
//      "state": 1,
//      "location": "A",
//      "subscription": 0
//    }, {
//      "sourceid": 31,
//      "text": "\u5973\u8e72\u4fbf",
//      "textid": 5,
//      "state": 1,
//      "location": "A",
//      "subscription": 0
//    }, {
//      "sourceid": 32,
//      "text": "\u5973\u8e72\u4fbf",
//      "textid": 5,
//      "state": 1,
//      "location": "A",
//      "subscription": 0
//    }, {
//      "sourceid": 33,
//      "text": "\u5973\u8e72\u4fbf",
//      "textid": 5,
//      "state": 1,
//      "location": "A",
//      "subscription": 0
//    }],
//    "B": [{
//      "sourceid": 39,
//      "text": "\u5973\u5750\u4fbf",
//      "textid": 4,
//      "state": 1,
//      "location": "B",
//      "subscription": 0
//    }, {
//      "sourceid": 40,
//      "text": "\u5973\u5750\u4fbf",
//      "textid": 4,
//      "state": 0,
//      "location": "B",
//      "subscription": 0
//    }, {
//      "sourceid": 34,
//      "text": "\u5973\u8e72\u4fbf",
//      "textid": 5,
//      "state": 1,
//      "location": "B",
//      "subscription": 0
//    }, {
//      "sourceid": 35,
//      "text": "\u5973\u8e72\u4fbf",
//      "textid": 5,
//      "state": 1,
//      "location": "B",
//      "subscription": 0
//    }]
//  },
//  "1": {
//    "A": [{
//      "sourceid": 41,
//      "text": "\u7537\u8e72\u4fbf",
//      "textid": 2,
//      "state": 1,
//      "location": "A",
//      "subscription": 0
//    }, {
//      "sourceid": 42,
//      "text": "\u7537\u8e72\u4fbf",
//      "textid": 2,
//      "state": 0,
//      "location": "A",
//      "subscription": 0
//    }, {
//      "sourceid": 43,
//      "text": "\u7537\u8e72\u4fbf",
//      "textid": 2,
//      "state": 0,
//      "location": "A",
//      "subscription": 0
//    }],
//    "B": [{
//      "sourceid": 44,
//      "text": "\u7537\u8e72\u4fbf",
//      "textid": 2,
//      "state": 0,
//      "location": "B",
//      "subscription": 0
//    }, {
//      "sourceid": 45,
//      "text": "\u7537\u8e72\u4fbf",
//      "textid": 2,
//      "state": 0,
//      "location": "B",
//      "subscription": 0
//    }, {
//      "sourceid": 46,
//      "text": "\u7537\u8e72\u4fbf",
//      "textid": 2,
//      "state": 0,
//      "location": "B",
//      "subscription": 0
//    }, {
//      "sourceid": 47,
//      "text": "\u7537\u8e72\u4fbf",
//      "textid": 2,
//      "state": 0,
//      "location": "B",
//      "subscription": 0
//    }]
//  },
//  "sex": 1
//}
//}
//console.log(data);
//var myData = data.data;

var myData = {};

window.location = '?callobjectC&mac';
function getDeviceToken(str) {
  
  $.ajax({
    type:"post",
    url:"http://192.168.1.140:9000/app/state/toliet",
    async:true,
    data: {
      "mac": str
    },
    success: function(data) {
      console.log(data);
      myData = data.data
    },
    error: function(data) {
      
    }
  });
}


var allStateMen = 0;
var allStateWomen = 0;

//男厕状态总和
function menState() {
  for (var i = 0; i < myData[1].A.length; i++) {
    allStateMen += myData[1].A[i].state;
  }
  for (var  i = 0; i < myData[1].B.length; i++) {
    allStateMen += myData[1].B[i].state;
  }
//if (allStateMen === 0) {
//  return '忙';
//} else {
//  return '不忙';
//}
  return allStateMen;
}
var allMenState = menState();
$('#menNotbusy').html('共7间，闲'+allMenState+'间');
if (allMenState === 0) {
  $('.restroom-male .toliet-con-box').eq(0).addClass('toliet-con-notbusy');
  $('.restroom-male .toliet-con-box').eq(1).removeClass('toliet-con-notbusy');
} else {
  $('.restroom-male .toliet-con-box').eq(0).removeClass('toliet-con-notbusy');
  $('.restroom-male .toliet-con-box').eq(1).addClass('toliet-con-notbusy');
}



//女厕状态总和
function womenState() {
  for (var i = 0; i < myData[0].A.length; i++) {
    allStateWomen += myData[0].A[i].state;
  }
  for (var  i = 0; i < myData[0].B.length; i++) {
    allStateWomen += myData[0].B[i].state;
  }
//if (allStateWomen === 0) {
//  return '忙';
//} else {
//  return '不忙';
//}
  return allStateWomen;
}

var allWomenState = womenState();
$('#womenNotbusy').html('共10间，闲'+allWomenState+'间');
if (allWomenState === 0) {
  $('.restroom-female .toliet-con-box').eq(0).addClass('toliet-con-notbusy');
  $('.restroom-female .toliet-con-box').eq(1).removeClass('toliet-con-notbusy');
} else {
  $('.restroom-female .toliet-con-box').eq(0).removeClass('toliet-con-notbusy');
  $('.restroom-female .toliet-con-box').eq(1).addClass('toliet-con-notbusy');
}


if (myData.sex === 1) {
  //tab
  $('.sex-tab .tab-chil').eq(0).addClass('tab-chil-active');
  $('.sex-tab .tab-chil').eq(1).removeClass('tab-chil-active');
  
  //内容
  $('.restroom-men').addClass('restroom-show');
  $('restroom-female').removeClass('restroom-show');
} else {
  //tab
  $('.sex-tab .tab-chil').eq(0).removeClass('tab-chil-active');
  $('.sex-tab .tab-chil').eq(1).addClass('tab-chil-active');
  
  //内容
  $('.restroom-men').removeClass('restroom-show');
  $('restroom-female').addClass('restroom-show');
}



//男  忙
function mendataDetail1(area) {
  var myHtml = ''
  for (var i = 0; i < area.length; i++) {
    area[i].subscription == 0
    if (area[i].state === 0) {
      if (area[i].subscription == 0) {
        var myHtml = myHtml + '<tr>'
                +'<td>'+ area[i].sourceid +'</td>'
                +'<td>'+ area[i].text +'</td>'
                +'<td>占用</td>'
                +'<td width="125"><input class="table-remind-btn" onclick="singleRemind(this, this.value, id)" type="button" value="提醒我" /></td>'
                +'</tr>';
      } else {
        var myHtml = myHtml + '<tr>'
                +'<td>'+ area[i].sourceid +'</td>'
                +'<td>'+ areaA[i].text +'</td>'
                +'<td>占用</td>'
                +'<td width="125"><input class="table-remind-btn table-remind-btn-disabled" disabled="disabled" onclick="singleRemind(this, this.value, id)" type="button" value="取消提醒" /></td>'
                +'</tr>';
      }
      
    } else {
      if (area[i].subscription == 0) {
        var myHtml = myHtml + '<tr>'
                +'<td>'+ area[i].sourceid +'</td>'
                +'<td>'+ area[i].text +'</td>'
                +'<td>空闲</td>'
                +'<td width="125"><input class="table-remind-btn" onclick="singleRemind(this, this.value, id)" type="button" value="提醒我" /></td>'
                +'</tr>';
      } else {
        var myHtml = myHtml + '<tr>'
                +'<td>'+ area[i].sourceid +'</td>'
                +'<td>'+ area[i].text +'</td>'
                +'<td>空闲</td>'
                +'<td width="125"><input class="table-remind-btn table-remind-btn-disabled" disabled="disabled" onclick="singleRemind(this, this.value, id)" type="button" value="取消提醒" /></td>'
                +'</tr>';
      }
      
    }
    
  }
  return myHtml;
}
$('.restroom-male table').eq(0).html($('.restroom-male table').eq(0).html() + mendataDetail1(myData[1].A));
$('.restroom-male table').eq(1).html($('.restroom-male table').eq(1).html() + mendataDetail1(myData[1].B));
$('.restroom-female table').eq(0).html($('.restroom-female table').eq(0).html() + mendataDetail1(myData[0].A));
$('.restroom-female table').eq(1).html($('.restroom-female table').eq(1).html() + mendataDetail1(myData[0].B));

//男 闲
function mendataDetail2(area) {
  var myHtml = ''
  for (var i = 0; i < area.length; i++) {
    area[i].subscription == 0
    if (area[i].state === 0) {
        var myHtml = myHtml + '<tr><td>'+area[i].sourceid+'</td>'
              +'<td>'+area[i].text+'</td>'
              +'<td>占用</td></tr>';
      
    } else {
      if (area[i].subscription == 0) {
        var myHtml = myHtml + '<tr><td>'+area[i].sourceid+'</td>'
              +'<td>'+area[i].text+'</td>'
              +'<td>空闲</td></tr>';
      }
      
    }
    
  }
  return myHtml;
}

$('.restroom-male table').eq(2).html($('.restroom-male table').eq(2).html() + mendataDetail2(myData[1].A));
$('.restroom-male table').eq(3).html($('.restroom-male table').eq(3).html() + mendataDetail2(myData[1].B));
$('.restroom-female table').eq(2).html($('.restroom-female table').eq(2).html() + mendataDetail2(myData[0].A));
$('.restroom-female table').eq(3).html($('.restroom-female table').eq(3).html() + mendataDetail2(myData[0].B));



//男
$('.sex-tab .tab-chil-male').on('click', function() {
  $(this).addClass('tab-chil-active');
  $('.sex-tab .tab-chil-female').removeClass('tab-chil-active');
  $('.restroom-male').addClass('restroom-show');
  $('.restroom-female').removeClass('restroom-show');
});

//女
$('.sex-tab .tab-chil-female').on('click', function() {
  $(this).addClass('tab-chil-active');
  $('.sex-tab .tab-chil-male').removeClass('tab-chil-active');
  $('.restroom-male').removeClass('restroom-show');
  $('.restroom-female').addClass('restroom-show');
});

//all提醒我
$('.toliet-remind-btn').on('click', function() {
  if ($(this).html() === '提醒我') {
    $(this).addClass('remind-btn-active');
    $(this).html('取消提醒');
  } else {
    $(this).removeClass('remind-btn-active');
    $(this).html('提醒我');
  }
});


//单个提醒
function singleRemind(obj, inner) {
  console.log(obj.tagName);
  if (inner === '提醒我') {
    obj.value = '取消提醒';
  } else {
    obj.value = '提醒我';
  }
}

$('.toliet-con-busy table').html();
