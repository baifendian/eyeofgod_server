
//历史假数据
//var myData = {
//"msg": "123",
//"code": 1,
//"data": {
//  "sex": 1,
//  "location": "F",
//  "advanced": 0,
//  "mac": "test1"
//}
//}

var myData = {};

window.location = '?callobjectC&mac';
function getDeviceToken(str) {
  
  $.ajax({
    type:"post",
    url:"http://192.168.1.140:9000/app/info/registered",
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



//历史数据

if (myData.code === 1) {
  
  //性别
  if (myData.data.sex === 1) {
    $('.setting-sex .fa').eq(0).removeClass('fa-circle-o');
    $('.setting-sex .fa').eq(0).addClass('fa-check-circle-o');
  } else {
    $('.setting-sex .fa').eq(1).removeClass('fa-circle-o');
    $('.setting-sex .fa').eq(1).addClass('fa-check-circle-o');
  }
  
  //工位
  if (myData.data.location === 'A') {
    $('.setting-station .fa').eq(0).removeClass('fa-circle-o');
    $('.setting-station .fa').eq(0).addClass('fa-check-circle-o');
  } else if (myData.data.location === 'B') {
    $('.setting-station .fa').eq(1).removeClass('fa-circle-o');
    $('.setting-station .fa').eq(1).addClass('fa-check-circle-o');
  } else if (myData.data.location === 'C') {
    $('.setting-station .fa').eq(2).removeClass('fa-circle-o');
    $('.setting-station .fa').eq(2).addClass('fa-check-circle-o');
  } else if (myData.data.location === 'D') {
    $('.setting-station .fa').eq(3).removeClass('fa-circle-o');
    $('.setting-station .fa').eq(3).addClass('fa-check-circle-o');
  } else if (myData.data.location === 'E') {
    $('.setting-station .fa').eq(4).removeClass('fa-circle-o');
    $('.setting-station .fa').eq(4).addClass('fa-check-circle-o');
  } else if (myData.data.location === 'F') {
    $('.setting-station .fa').eq(5).removeClass('fa-circle-o');
    $('.setting-station .fa').eq(5).addClass('fa-check-circle-o');
  }
  
  //高级设置
  if (myData.data.advanced === 0) {
    $('.setting-priority .fa').eq(0).removeClass('fa-circle-o');
    $('.setting-priority .fa').eq(0).addClass('fa-check-circle-o');
  } else {
    $('.setting-priority .fa').eq(1).removeClass('fa-circle-o');
    $('.setting-priority .fa').eq(1).addClass('fa-check-circle-o');
  }
}

//选项操作

//性别
$('.setting-sex p').on('click', function() {
  
  //初始化
  $('.setting-sex .fa').removeClass('fa-check-circle-o');
  $('.setting-sex .fa').addClass('fa-circle-o');
  
  //被操作的元素设置
  $(this).children('.fa').removeClass('fa-circle-o');
  $(this).children('.fa').addClass('fa-check-circle-o');
});

//工位
$('.setting-station p').on('click', function() {
  
  //初始化
  $('.setting-station .fa').removeClass('fa-check-circle-o');
  $('.setting-station .fa').addClass('fa-circle-o');
  
  //被操作的元素设置
  $(this).children('.fa').removeClass('fa-circle-o');
  $(this).children('.fa').addClass('fa-check-circle-o');
});

//高级设置
$('.setting-priority p').on('click', function() {
  
  //初始化
  $('.setting-priority .fa').removeClass('fa-check-circle-o');
  $('.setting-priority .fa').addClass('fa-circle-o');
  
  //被操作的元素设置
  $(this).children('.fa').removeClass('fa-circle-o');
  $(this).children('.fa').addClass('fa-check-circle-o');
});

$('#sureBtn').on('click', function() {
  window.location.dismiss = '?callobjectC&dismiss';
});
