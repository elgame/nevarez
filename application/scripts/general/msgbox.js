var msb = {
	confirm: function(msg, obj, callback){
		$.msgbox(msg, {
		  type: "confirm", 
		  buttons: [
		    {type: "submit", value: "Si"},
		    {type: "cancel", value: "No"}
		  ]
		}, function(result) {
		  if (result) {
			  if($.isFunction(callback))
				  callback.call(this, obj);
			  else
				  window.location = obj.href;
		  }
		});
	},
	
	info: function(msg, obj, callback){
		$.msgbox(msg, {
		  type: "info"
		}, function(result) {
		  if (result) {
			  if($.isFunction(callback))
				  callback.call(this, obj);
			  /*else
				  window.location = obj.href;*/
		  }
		});
	},
	
	error: function(msg, obj, callback){
		$.msgbox(msg, {
			  type: "error"
			}, function(result) {
			  if (result) {
				  if($.isFunction(callback))
					  callback.call(this, obj);
				  /*else
					  window.location = obj.href;*/
			  }
			});
	}
};