
//假数据
var myData = {
  "toliet": {
    "status": 1,
    "location": {
      "a": 1,
      "b": 3
    }
  },
  "restroom": {
    "status": 0,
    "location": {
      "c": 0
    }
  },
  "shower": {
    "status": 1,
    "location": {
      "d": 3
    }
  },
  "eggchaiir": {
    "status": 0,
    "location": {
      "e": 0
    }
  }
}


//首页

//轮播图
$('#slide').swipeSlide({
  continuousScroll: true,
  speed : 1500,
  transitionType : 'cubic-bezier(0.22, 0.69, 0.72, 0.88)',
  firstCallback : function(i,sum,me){
      me.find('.dot').children().first().addClass('cur');
  },
  callback : function(i,sum,me){
      me.find('.dot').children().eq(i).addClass('cur').siblings().removeClass('cur');
  }
});

//数据展示

//洗手间
if (myData.toliet.status === 1) {
  $('.app-toilet .resource-status').html('空闲');
  $('.app-toilet .resource-num').css({'display': 'block'});
  $('.app-toilet .remindme-btn').css({'display': 'none'})
} else {
  $('.app-toilet .resource-status').html('占用');
  $('.app-toilet .resource-num').css({'display': 'none'});
  $('.app-toilet .remindme-btn').css({'display': 'block'})
}

//休息室
if (myData.restroom.status === 1) {
  $('.app-lounge .resource-status').html('空闲');
  $('.app-lounge .resource-num').css({'display': 'block'});
  $('.app-lounge .remindme-btn').css({'display': 'none'})
} else {
  $('.app-lounge .resource-status').html('占用');
  $('.app-lounge .resource-num').css({'display': 'none'});
  $('.app-lounge .remindme-btn').css({'display': 'block'})
}
 
 //淋浴间
 if (myData.shower.status === 1) {
  $('.app-shower .resource-status').html('空闲');
  $('.app-shower .resource-num').css({'display': 'block'});
  $('.app-shower .remindme-btn').css({'display': 'none'})
} else {
  $('.app-shower .resource-status').html('占用');
  $('.app-shower .resource-num').css({'display': 'none'});
  $('.app-shower .remindme-btn').css({'display': 'block'})
}

//蛋椅
if (myData.eggchaiir.status === 1) {
  $('.app-egg .resource-status').html('空闲');
  $('.app-egg .resource-num').css({'display': 'block'});
  $('.app-egg .remindme-btn').css({'display': 'none'})
} else {
  $('.app-egg .resource-status').html('占用');
  $('.app-egg .resource-num').css({'display': 'none'});
  $('.app-egg .remindme-btn').css({'display': 'block'})
}
 
//洗手间跳转
$('.app-toilet .app-icon').on('click', function() {
  window.location = '?callobjectC&pushViewController&toliet.html';
});

//休息室跳转
$('.app-lounge .app-icon').on('click', function() {
  window.location = '?callobjectC&pushViewController&restroom.html';
});

//淋浴间跳转
$('.app-shower .app-icon').on('click', function() {
  window.location = '?callobjectC&pushViewController&shower.html';
});

//蛋椅跳转
$('.app-egg .app-icon').on('click', function() {
  window.location = '?callobjectC&pushViewController&eggchair.html';
});

//提醒按钮事件
$('.remindme-btn div').on('click', function() {
  if ($(this).html() === '提醒我') {
    $(this).html('取消提醒');
    $(this).addClass('remind-btn-active');
    if ($(this).attr('class').indexOf('tolietBtn') != -1) {
      alert('洗手间');
    } else if ($(this).attr('class').indexOf('loungeBtn') != -1) {
      alert('休息室');
    } else if ($(this).attr('class').indexOf('showerBtn') != -1) {
      alert('淋浴间');
    } else if ($(this).attr('class').indexOf('eggBtn') != -1) {
      alert('蛋椅');
    }
    console.log($(this).attr('class'));
  } else {
    $(this).html('提醒我');
    $(this).removeClass('remind-btn-active');
    if ($(this).attr('class').indexOf('tolietBtn') != -1) {
      alert('洗手间');
    } else if ($(this).attr('class').indexOf('loungeBtn') != -1) {
      alert('休息室');
    } else if ($(this).attr('class').indexOf('showerBtn') != -1) {
      alert('淋浴间');
    } else if ($(this).attr('class').indexOf('eggBtn') != -1) {
      alert('蛋椅');
    }
    console.log($(this).attr('class'));
  }
});
