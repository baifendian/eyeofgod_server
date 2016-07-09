
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
