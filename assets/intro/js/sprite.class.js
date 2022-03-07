function Sprite(_opts){
	var opts = _opts.clone();
	this.img = opts.img || new Image();

	if (!opts.w && !opts.h) {
		opts.w = opts.img.width;
		opts.h = opts.img.height;
	} else if (!opts.w) {
		opts.w = opts.h *(opts.img.width/opts.img.height);
	} else if (!opts.h) {
		opts.h = opts.w *(opts.img.height/opts.img.width);
	}
	Drawable.call(this, opts);
	this.rasterize = function(){
		var temp_canvas = document.createElement("canvas");
		var temp_ctx = temp_canvas.getContext('2d');
		temp_canvas.width = this.w;
		temp_canvas.height = this.h;
		temp_ctx.drawImage(this.img, 0, 0, this.w, this.h);
		this.img = temp_canvas;
	}
}

Sprite.prototype = Object.create(Drawable.prototype);
Sprite.prototype.constructor = Sprite;