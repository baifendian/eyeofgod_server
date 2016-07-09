
//男
$('.sex-tab .tab-chil-male').on('click', function() {
  $(this).addClass('tab-chil-active');
  $('.sex-tab .tab-chil-female').removeClass('tab-chil-active');
  $('.restroom-men').addClass('restroom-show');
  $('.restroom-female').removeClass('restroom-show');
});

//女
$('.sex-tab .tab-chil-female').on('click', function() {
  $(this).addClass('tab-chil-active');
  $('.sex-tab .tab-chil-male').removeClass('tab-chil-active');
  $('.restroom-men').removeClass('restroom-show');
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
