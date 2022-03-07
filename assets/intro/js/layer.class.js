function Layer(_opts){
	this.fill = _opts.fill||"#000";
	delete _opts.fill;
	Drawable.call(this, _opts);
	this.children = [];
	this.childMap = {};

	this.img = document.createElement("canvas");
	this.img.width = this.w;
	this.img.height = this.h;
	this.localctx = this.img.getContext('2d');

	this.addChild = function(name, obj){
		obj.parent = this;
		obj.ctx = this.localctx;
		if (obj instanceof Shape && obj.fill == "" && typeof this.fill != "undefined") {
			obj.fill = this.fill;
		};
		this.children.push(obj);
		this.childMap[name] = this.children[this.children.length-1];
		return true;
	}
	this.addMask = function(obj){
		obj.parent = this;
		obj.ctx = this.localctx;
		this.mask = obj;
		console.log([this.x, this.y]);
		return true;
	}
}
Layer.prototype = Object.create(Drawable.prototype);
Layer.prototype.constructor = Layer;