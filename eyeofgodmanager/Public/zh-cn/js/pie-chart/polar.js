// JavaScript Document
$(function(){
	//上网频次分布
    var polarData = [
        {
            value: 300,
            color: "#89b522",
            highlight: "#1ab394",
            label: "App"
        },
        {
            value: 140,
            color: "#a1cd3a",
            highlight: "#1ab394",
            label: "Software"
        },
        {
            value: 100,
            color: "#c7dd3c",
            highlight: "#1ab394",
            label: "Laptop"
        },
		{
			value:400,
			color:"c3ed95",
			heighlight:"#000",
			label:"left"
		}
		
    ];

    var polarOptions = {
        scaleShowLabelBackdrop: true,
        scaleBackdropColor: "rgba(255,255,255,0.75)",
        scaleBeginAtZero: true,
        scaleBackdropPaddingY: 1,
        scaleBackdropPaddingX: 1,
        scaleShowLine: true,
        segmentShowStroke: true,
        segmentStrokeColor: "#fff",
        segmentStrokeWidth: 2,
        animationSteps: 100,
        animationEasing: "easeOutBounce",
        animateRotate: true,
        animateScale: false,
        responsive: true,

    };

    var ctx = document.getElementById("polarChart").getContext("2d");
    var myNewChart = new Chart(ctx).PolarArea(polarData, polarOptions);
	
	
	//上网时长分布
		var radarData = {
        labels: ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"],
        datasets: [
            {
                label: "My First dataset",
                fillColor: "rgba(220,220,220,0.2)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: [65, 59, 90, 81, 56, 55, 40]
            },
            {
                label: "My Second dataset",
                fillColor: "rgba(26,179,148,0.2)",
                strokeColor: "rgba(26,179,148,1)",
                pointColor: "rgba(26,179,148,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: [28, 48, 40, 19, 96, 27, 100]
            }
        ]
    };

    var radarOptions = {
        scaleShowLine: true,
        angleShowLineOut: true,
        scaleShowLabels: false,
        scaleBeginAtZero: true,
        angleLineColor: "rgba(0,0,0,.1)",
        angleLineWidth: 1,
        pointLabelFontFamily: "'Arial'",
        pointLabelFontStyle: "normal",
        pointLabelFontSize: 10,
        pointLabelFontColor: "#666",
        pointDot: true,
        pointDotRadius: 3,
        pointDotStrokeWidth: 1,
        pointHitDetectionRadius: 20,
        datasetStroke: true,
        datasetStrokeWidth: 2,
        datasetFill: true,
        responsive: true,
    }

    var ctx = document.getElementById("radarChart").getContext("2d");
    var myNewChart = new Chart(ctx).Radar(radarData, radarOptions);

	
});
$(function(){
	var bathroomData = [


             { time: "2015-06-17T00:00:00", r0: 5 ,r1:11},
            { time: "2015-06-17T01:09:01", r0: 4, r1: 5},

			{ time: "2015-06-17T10:09:01", r0: 4, r1: 5},
             { time: "2015-06-17T11:24:01", r0: 11, r1: 9},




             { time: "2015-06-17T24:39:01", r0: 11, r1: 6}
         ];
         var bathroomIDs = [ "r0", "r1"];
         var bathroomNames = [ "所有终端访的访客数", "所有终端下单买家"];
        
         Morris.Line({
             element: "morris-chart",
             data: bathroomData,
             xkey: "time",
			  resize: true,
            ykeys: bathroomIDs,
             labels: bathroomNames,
			  lineColors: ['#a9cd55','#18cbcd']//w图
		 });
});