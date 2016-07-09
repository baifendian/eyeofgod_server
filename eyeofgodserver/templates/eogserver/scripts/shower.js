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


////男饼图
//var myChart1 = echarts.init(document.getElementById('pieChartMen'));
//
//// 指定图表的配置项和数据
//var option1 = {
//legend: {
//  orient: 'vertical',
//  x: 'left',
//  data: ['闲', '忙']
//},
//series: [{
//  name: '访问来源',
//  type: 'pie',
//  radius: ['50%', '70%'],
//  avoidLabelOverlap: false,
//  label: {
//    normal: {
//      show: false,
//      position: 'center'
//    },
//    emphasis: {
//      show: true,
//      textStyle: {
//        fontSize: '30',
//        fontWeight: 'bold'
//      }
//    }
//  },
//  labelLine: {
//    normal: {
//      show: false
//    }
//  },
//  data: [{
//    value: 0,
//    name: '闲'
//  }, {
//    value: 1,
//    name: '忙'
//  }]
//}]
//};
//
//// 使用刚指定的配置项和数据显示图表。
//myChart1.setOption(option1);
//
////女饼图
//var myChart2 = echarts.init(document.getElementById('pieChartWomen'));
//
//// 指定图表的配置项和数据
//var option2 = {
//legend: {
//  orient: 'vertical',
//  x: 'left',
//  data: ['闲', '忙']
//},
//series: [{
//  name: '访问来源',
//  type: 'pie',
//  radius: ['50%', '70%'],
//  avoidLabelOverlap: false,
//  label: {
//    normal: {
//      show: false,
//      position: 'center'
//    },
//    emphasis: {
//      show: true,
//      textStyle: {
//        fontSize: '30',
//        fontWeight: 'bold'
//      }
//    }
//  },
//  labelLine: {
//    normal: {
//      show: false
//    }
//  },
//  data: [{
//    value: 1,
//    name: '闲'
//  }, {
//    value: 0,
//    name: '忙'
//  }]
//}]
//};
//// 使用刚指定的配置项和数据显示图表。
//myChart2.setOption(option2);




