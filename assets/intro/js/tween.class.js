function Tween(obj, props, duration, loop, easingMode, callback){

	this.obj = obj;
	this.props = {};
	if (!loop && typeof callback != "undefined") {
		this.callback = callback;
	}else{
		this.callback = function(){
			return true;
		};
	};
	if (easingMode) {
		this.easingMode = animationMode[easingMode];
	}else{
		//linear interpolation
		this.easingMode = {
			calculate: function(t){
				return t;
			}
		};
	};
	for(var prop in props){
		if (typeof obj[prop] != "undefined") {
			if (props[prop] instanceof Array) {
				this.props[prop] = {
					origin: props[prop][0],
					delta: props[prop][1]-props[prop][0]
				};
			}else{
				this.props[prop] = {
					origin: obj[prop],
					delta: props[prop]-obj[prop]
				};
			};
		};
	};
	this.duration = duration*1000||400;
	this.loop = loop||true;
	this.time = 0;
	this.step = function(delta){
		this.time += delta;
		if (this.time > this.duration) {
			if(loop){
				this.time = 0;
			}else{
				this.callback(this.time);
				this.time = this.duration;
				this.parent.cleanList.push(this);
			};
		};
		for(var prop in this.props){
			var t = this.easingMode.calculate(this.time/this.duration);
			this.obj[prop] = this.props[prop].origin+(this.props[prop].delta*t);
		};
	};
}