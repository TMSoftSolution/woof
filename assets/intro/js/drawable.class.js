function Drawable(_opts){

	this.x = _opts.x||0;
	this.y = _opts.y||0;
	this.w = _opts.w||10;
	this.h = _opts.h||10;
	this.s = (typeof _opts.s != "undefined")?_opts.s:1;
	this.r = _opts.r||0;
	this.a = (typeof _opts.a != "undefined")?_opts.a:1;
	this.transformOrigin = _opts.transformOrigin || {x: 0.5, y: 0.5};
 	this.update = function(){
 				var x = this.x, y = this.y;
		var cleanTransform = false;
		if (this.s != 1 || this.r != 0) {
			this.ctx.save();
			// console.log("object has scale or rotation value");
			// console.log("translate to "+(this.x+this.w/2)+", "+(this.y+this.h/2))
			this.ctx.translate(this.x+this.w*this.transformOrigin.x, this.y+this.h*this.transformOrigin.y);
			// x = 0;
			// y = 0;
			cleanTransform = true;
		}
		if (this.s != 1) {
			//console.log("scale to "+this.s);
			this.ctx.scale(this.s, this.s);
		}
		if (this.r != 0){
			// console.log("object has rotation value");
			// console.log("rotate by "+this.r+"deg");
			this.ctx.rotate(this.r*Math.PI/180);
		}
		if (this.s != 1 || this.r != 0) {
			this.ctx.translate(-(this.x+this.w*this.transformOrigin.x), -(this.y+this.h*this.transformOrigin.y))
		}
		if (this.a != 1) {
			var resetAlpha = this.ctx.globalAlpha;
			this.ctx.globalAlpha = this.ctx.globalAlpha*this.a;
		}

		//main draw method
		// if(this.a > 0){
			if(this instanceof Layer){
				this.localctx.clear();
				for (var i = 0; i < this.children.length; i++) {
					this.children[i].draw();
				}
				// console.log([this.x, this.y, this.w, this.h]);
				if (typeof this.mask != "undefined") {
					this.localctx.globalCompositeOperation = "destination-in";
					// console.log(this.mask);
					this.mask.draw();
					this.localctx.globalCompositeOperation = "source-over";
				}
			}
			if (this instanceof Shape) {
				this.ctx.beginPath();
				
				this.ctx.fillStyle = this.fill;

				if (this.type == "circle") {
					this.ctx.arc(this.x+this.w/2, this.y+this.h/2, this.w/2, 0, 2*Math.PI, false);
				}else if(this.type == "rectangle"){
					if (this.pattern) {
						this.ctx.rect(this.x-this.pattern.x, this.y-this.pattern.y, this.w, this.h);
					}else{
						this.ctx.rect(this.x, this.y, this.w, this.h);
					}
				}
				this.ctx.closePath();
				this.ctx.fill();
			}
		// }


		if (resetAlpha){
			this.ctx.globalAlpha = resetAlpha;
		}
		if (cleanTransform) {
			this.ctx.restore();
		}
 	};
	this.draw = function(){
		var x = this.x, y = this.y;
		var cleanTransform = false;
		if (this.s != 1 || this.r != 0) {
			this.ctx.save();
			// console.log("object has scale or rotation value");
			// console.log("translate to "+(this.x+this.w/2)+", "+(this.y+this.h/2))
			this.ctx.translate(this.x+this.w*this.transformOrigin.x, this.y+this.h*this.transformOrigin.y);
			// x = 0;
			// y = 0;
			cleanTransform = true;
		}
		if (this.s != 1) {
			//console.log("scale to "+this.s);
			this.ctx.scale(this.s, this.s);
		}
		if (this.r != 0){
			// console.log("object has rotation value");
			// console.log("rotate by "+this.r+"deg");
			this.ctx.rotate(this.r*Math.PI/180);
		}
		if (this.s != 1 || this.r != 0) {
			this.ctx.translate(-(this.x+this.w*this.transformOrigin.x), -(this.y+this.h*this.transformOrigin.y))
		}
		if (this.a != 1) {
			var resetAlpha = this.ctx.globalAlpha;
			this.ctx.globalAlpha = this.ctx.globalAlpha*this.a;
		}

		//main draw method
		// if(this.a > 0){
			if(this instanceof Layer){
				this.localctx.clear();
				for (var i = 0; i < this.children.length; i++) {
					this.children[i].draw();
				}
				// console.log([this.x, this.y, this.w, this.h]);
				if (typeof this.mask != "undefined") {
					this.localctx.globalCompositeOperation = "destination-in";
					// console.log(this.mask);
					this.mask.draw();
					this.localctx.globalCompositeOperation = "source-over";
				}
				this.ctx.drawImage(this.img, this.x, this.y, this.w, this.h);
			}
			if (this instanceof Shape) {
				this.ctx.beginPath();
				if (this.pattern) {
					this.ctx.save();
					this.pattern.obj.update();
					this.ctx.translate(this.pattern.x, this.pattern.y);
					this.ctx.fillStyle = this.ctx.createPattern(this.pattern.obj.img, this.pattern.repeat);
				}else{
					this.ctx.fillStyle = this.fill;
				};

				if (this.type == "circle") {
					this.ctx.arc(this.x+this.w/2, this.y+this.h/2, this.w/2, 0, 2*Math.PI, false);
				}else if(this.type == "rectangle"){
					if (this.pattern) {
						this.ctx.rect(this.x-this.pattern.x, this.y-this.pattern.y, this.w, this.h);
					}else{
						this.ctx.rect(this.x, this.y, this.w, this.h);
					}
				}
				this.ctx.closePath();
				this.ctx.fill();
				if (this.pattern) {
					this.ctx.restore();
				};
			}else{
				this.ctx.drawImage(this.img, this.x, this.y, this.w, this.h);
			}
		// }


		if (resetAlpha){
			this.ctx.globalAlpha = resetAlpha;
		}
		if (cleanTransform) {
			this.ctx.restore();
		}
	}
}