function Timeline(){
	this.keyframes = {};
	this.cleanList = [];
	this.repeat = false;
	this.length = 0;
	this.progress = 0;

	this.addKeyframe = function(tween, start){
		if (!(tween instanceof Tween)) {
			throw "First parameter is not an instance of Tween";
		};

		var keyframe = start*1000;
		end = keyframe+tween.duration;
		this.length = Math.max(this.length, end);

		if (typeof this.keyframes[keyframe] == "undefined") {
			this.keyframes[keyframe] = new Array();
		}
		var len = this.keyframes[keyframe].push(tween);

		//tell child where to look when it needs to remove 
		tween.parent = this;
		tween.start = keyframe;
		this.keyframes[keyframe][len-1].id = len-1;

		return true;
	};

	this.removeKeyframe = function(tween){
		if (this.repeat == false) {
			if (!(tween instanceof Tween)) {
				throw "First parameter is not an instance of Tween";
			};
			this.keyframes[tween.start].splice(tween.id, 1);
			if (this.keyframes[tween.start].length == 0) {
				delete this.keyframes[tween.start];
			}else{
				for (var i = 0; i < this.keyframes[tween.start].length; i++) {
					this.keyframes[tween.start][i].id = i;
				};
			};
		}else{
			return true;
		};
	};
	//need a last stepped timecode to calculate delta
	this.lastStep = Date.now();
	this.update = function(delta){
		//var delta = timecode-this.lastStep;
		//this.lastStep = timecode;
		this.progress += delta;
		if (this.progress < 0 ) {
			this.progress = 0;
		}
		for(var keyframe in this.keyframes){
			if (keyframe <= this.progress) {
				for (var i = 0; i < this.keyframes[keyframe].length; i++) {
					this.keyframes[keyframe][i].step(delta);
				};
			};
		};
		if (this.repeat && this.progress >= this.length) {
			this.reset();
		};
		this.cleanUp();
	};
	this.cleanUp = function(){
		for (var i = 0; i < this.cleanList.length; i++) {
			this.removeKeyframe(this.cleanList[i]);
		};
		this.cleanList = [];
	}
	this.reset = function(){
		this.progress = 0;
		for(var keyframe in this.keyframes){
			for (var i = 0; i < this.keyframes[keyframe].length; i++) {
				this.keyframes[keyframe][i].time = 0;
				this.keyframes[keyframe][i].step(0);
			}
		};
	};
}