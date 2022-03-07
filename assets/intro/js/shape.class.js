function Shape(_opts){
	var opts = _opts.clone();
	/*if (opts.type == "circle") {
		opts.x -= opts.w/2;
		opts.y = opts.y-opts.h/2;
	};*/

	this.type = opts.type||"rectangle";
	this.fill = opts.fill||"";
	this.pattern = opts.pattern||null;
	Drawable.call(this, opts);
}
Shape.prototype = Object.create(Drawable.prototype);
Shape.prototype.constructor = Shape;