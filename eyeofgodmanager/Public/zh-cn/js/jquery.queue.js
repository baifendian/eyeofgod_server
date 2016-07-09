;(function($) {
 function AjaxQueue(override) {
	 this.override = !!override;
 };
 AjaxQueue.prototype = {
	 requests: new Array(),
	 offer: function(options) {
		 var _self = this;
		 var xhrOptions = $.extend({}, options, {
		 complete: function(jqXHR, textStatus) {
		 if($.isArray(options.complete)) {
			 var funcs = options.complete;
			 for(var i = 0, len = funcs.length; i < len; i++)
			 funcs[i].call(this, jqXHR, textStatus);
		 } else {
			   if(options.complete)options.complete.call(this, jqXHR, textStatus);
		  }
		   _self.poll();
		 },
		 beforeSend: function(jqXHR, settings) {
		 if(options.beforeSend)
			 var ret = options.beforeSend.call(this, jqXHR, settings);
			 //若超时 从新执行
			 if(ret === false) {
				 _self.poll();
				 return ret;
			 }
		 }
 });
	if(this.override) {
		 //console.log('go override');
		 this.replace(xhrOptions);
	} else {
		 //console.log('go queue');
		 this.requests.push(xhrOptions);
		 if(this.requests.length == 1){
		   $.ajax(xhrOptions);
		 }
	 }
 },
 replace: function(xhrOptions) {
	 var prevRet = this.peek();
	 if(prevRet != null) {
		 prevRet.abort();
	 }
	 this.requests.shift();
	 this.requests.push($.ajax(xhrOptions));
 },
 poll: function() {
	 if(this.isEmpty()) {
		 return null;
	 }
	 var processedRequest = this.requests.shift();
	 var nextRequest = this.peek();
	 if(nextRequest != null) {
		 $.ajax(nextRequest);
	 }
	 return processedRequest;
 },
 peek: function() {
	 if(this.isEmpty()) {
	 return null;
	 }
	 var nextRequest = this.requests[0];
	 return nextRequest;
 },
 isEmpty: function() {
	 return this.requests.length == 0;
 }
 };

 var queue = {};
 var AjaxManager = {
 createQueue: function(name, override) {
 	return queue[name] = new AjaxQueue(override);
 },
 destroyQueue: function(name) {
	 if(queue[name]) {
		 queue[name] = null;
		 delete queue[name];
	 }
 },
 getQueue: function(name) {
 	return ( queue[name] ? queue[name] : null);
 	}
 };
 $.AM = AjaxManager;
})(jQuery);
/*

使用示例：
var newQueue = $.AM.createQueue('queue');
$(function(){
  newQueue.offer({url:'?c=Message&m=write&a=10'});
  newQueue.offer({url:'?c=Message&m=write&a=10'});
  newQueue.offer({url:'?c=Message&m=write&a=1'});
});

上面的请求顺序就是按照offer先后顺序执行。
几个队列的管理方法：
 $.AM.createQueue('队列名称');//创建一个队列
$.AM.destroyQueue('队列名称');//销毁一个队列
$.AM.getQueue('队列名称');//获取一个队列
*/