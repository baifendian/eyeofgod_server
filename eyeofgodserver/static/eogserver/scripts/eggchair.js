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