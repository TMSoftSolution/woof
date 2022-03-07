function cubicBezier(x1, y1, x2, y2){
    var p1 = [x1, y1], p2 = [x2, y2];
    this.calculate = function(t){
        return [
            0 * Math.pow(1-t, 3) + p1[0] * 3 * Math.pow(1-t, 2) * t + p2[0] * 3 * (1-t) * Math.pow(t, 2) + 1 * Math.pow(t, 3),
            0 * Math.pow(1-t, 3) + p1[1] * 3 * Math.pow(1-t, 2) * t + p2[1] * 3 * (1-t) * Math.pow(t, 2) + 1 * Math.pow(t, 3),
        ];
    };
}
var easeout = new cubicBezier(0, 0, -0.4, 1),
	easein = new cubicBezier(0,0,1,-0.4),
	puff = new cubicBezier(0,0,0.4,2);
CanvasRenderingContext2D.prototype.clear = function(){
	//console.log(this);
	this.clearRect(0, 0, this.canvas.width, this.canvas.height);
}

var resources = [
	{name: "cloud-1.svg"},
	{name: "cloud-2.svg"},
	{name: "cloud-3.svg"},
	{name: "street.svg"},
	{name: "pin.svg"},
	{name: "map.svg"},
	{name: "world.svg"},
	{name: "logo.svg"}
];
var loadedCount = 0,
	assetCount = null,
	DOMURL = window.URL || window.webkitURL || window;
function preload(id){
	assetCount++;
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			resources[id].img = new Image();
			resources[id].img.onload = function(){
				//console.log(resources[id].img);
			};
			resources[id].img.src = 'data:image/svg+xml,'+xhr.responseText;

			loadedCount++;
			if (id < (resources.length-1)) {
				preload(id+1);
			}
		}
	};
	xhr.open("GET", "img/"+resources[id].name);
	xhr.send();
}
preload(0);
function delta(origin, destination, t){
	return (origin - destination)*t;
}
document.addEventListener("DOMContentLoaded", function(){
	var ww = window.innerWidth,
		wh = window.innerHeight||window.outerHeight;

	var spinner = document.getElementById("spinner");
	var spinnerCtx = spinner.getContext('2d');
	var size = Math.min(ww,wh)*0.2;

	var viewport = document.getElementById("viewport");
	var ctx = viewport.getContext("2d");

	var buffer = document.createElement("canvas");
	var bufferctx = buffer.getContext("2d");
	var radar = [new Object(),new Object(),new Object(),new Object()];
	radar[0].canvas = document.createElement("canvas");
	radar[0].ctx = radar[0].canvas.getContext("2d");

	radar[1].canvas = document.createElement("canvas");
	radar[1].ctx = radar[1].canvas.getContext("2d");

	radar[2].canvas = document.createElement("canvas");
	radar[2].ctx = radar[2].canvas.getContext("2d");

	radar[3].canvas = document.createElement("canvas");
	radar[3].ctx = radar[3].canvas.getContext("2d");

	if (devicePixelRatio != 1) {
		spinner.width = size*devicePixelRatio;
		spinner.height = size*devicePixelRatio;
		spinner.style.width = size+"px";
		spinner.style.height = size+"px";
		viewport.width = ww*devicePixelRatio;
		viewport.height = wh*devicePixelRatio;
		viewport.style.width = ww+"px";
		viewport.style.height = wh+"px";
		buffer.width = ww*devicePixelRatio;
		buffer.height = wh*devicePixelRatio;
	}else{
		spinner.width = size;
		spinner.height = size;
		viewport.width = ww;
		viewport.height = wh;
		buffer.width = ww;
		buffer.height = wh;
	}

	var center = {x: spinner.width/2, y: spinner.height*0.412},
		outerR = spinner.width*0.2768,
		innerR = spinner.width*0.146,
		pinPoint = {x: spinner.width/2, y: spinner.height*0.8967};

	spinner.style.marginTop = "-"+(size/2)+"px";
	spinner.style.marginLeft = "-"+(size/2)+"px";

	spinnerCtx.strokeStyle = "#292B30";
	spinnerCtx.lineWidth = spinner.width*0.03;
	spinnerCtx.lineCap = "round";
	spinnerCtx.lineJoin = "round";

	angleOffset = 0.165;
	var angle = 0,
		width = 0,
		start = null,
		lastStep = Date.now(),
		transitioning = false;
		
	function spin(timestamp){
		spinnerCtx.clear();
		spinnerCtx.beginPath();
		spinnerCtx.arc(center.x, center.y, outerR, (1-angleOffset)*Math.PI, (0+angleOffset)*Math.PI);
		spinnerCtx.lineTo(pinPoint.x, pinPoint.y);
		spinnerCtx.closePath();
        spinnerCtx.stroke();

        spinnerCtx.beginPath();
        if (!start) start = timestamp;
        var progress = timestamp - start;
        if (progress < 750) {
            angle = 0;
            width = 360*easeout.calculate(progress/750)[1];
        }else if (progress <1500) {
            angle = 360*easeout.calculate((progress-750)/750)[1];
            width = 360*(1-easeout.calculate((progress-750)/750)[1]);
        }
        spinnerCtx.arc(center.x, center.y, innerR, angle*Math.PI/180, (angle+width)*Math.PI/180 );
        fps = Math.round(1000/(timestamp-lastStep));
        lastStep = timestamp;
        if (progress >= 1500) {
            start = timestamp;
        }
		spinnerCtx.stroke();
		if (loadedCount != assetCount) {
			window.requestAnimationFrame(spin);
		}else {
			init();
			document.getElementById("loading").className = "fadeOut";
		}
	}
	window.requestAnimationFrame(spin);
 	var street, cloud1, cloud2, cloud3, pin, map, logo;
 	// var phase1Height = viewport.width*1.47;
 	var phase1Height = viewport.height*0.75
	function init(){
		cloud1 = {
			img: resources[0].img,
			left: viewport.width,
			width: viewport.width*0.28632,
			top: viewport.height*-0.004
		}
		cloud1.height = cloud1.width*(cloud1.img.height/cloud1.img.width);

		cloud2 = {
			img: resources[1].img,
			left: viewport.width,
			width: viewport.width*0.42865,
			top: viewport.height*0.19
		}
		cloud2.height = cloud2.width*(cloud2.img.height/cloud2.img.width);

		cloud3 = {
			img: resources[2].img,
			left: viewport.width,
			width: viewport.width*0.24063,
			top: viewport.height*0.36
		}
		cloud3.height = cloud3.width*(cloud3.img.height/cloud3.img.width);

		street = {
			img: resources[3].img,
			left: -(viewport.width*0.235),
			width: viewport.width*1.47
		};

		street.height = street.width*(street.img.height/street.img.width);
		street.top = phase1Height-street.height;

		pin = {
			img: resources[4].img,
			left: viewport.width*0.415,
			width: viewport.width*0.23352
		};

		pin.height = pin.width*(pin.img.height/pin.img.width);
		pin.top= phase1Height-(street.height*0.2)-pin.height;

		map = {
			img: resources[5].img,
			left: viewport.width*-6.55,
			top: viewport.height*-5.4,
			width: viewport.width*14
		}

		map.height = map.width*(map.img.height/map.img.width);

		// radar1.width = radar2.width = radar3.width = radar4.width = pin.width*1.2;
		// radar1.height = radar2.height = radar3.height = radar4.height = pin.width*1.2;
		// radar1ctx.fillStyle = "#FEF25B";
		// radar2ctx.fillStyle = "#FF7171";
		// radar3ctx.fillStyle = "#63BEFF";
		// radar4ctx.fillStyle = "#05E6AE";

		logo = {
			img: resources[7].img,
			width: pin.width*0.8
		}

		logo.height = logo.width*(logo.img.height/logo.img.width);

		start = null;
		lastStep = Date.now();
		ctx.font=""+(18*devicePixelRatio)+"pt sans-serif";
		window.requestAnimationFrame(step);
	}
	var phase = 1;
	var fps = 0;
	var s = 1;
	function step(timestamp){
		ctx.clear();
		if (!start) start = timestamp;
		var progress = timestamp - start;
		//console.log(progress);
		//window.requestAnimationFrame(step);
		if (phase == 1 && !transitioning) {
			if (progress>5000){
				ctx.drawImage(cloud1.img, cloud1.left-delta(cloud1.left, viewport.width*-0.3, (timestamp-5000)%30000/30000), cloud1.top, cloud1.width, cloud1.height);
			};
			if (progress>10000){
				ctx.drawImage(cloud2.img, cloud2.left-delta(cloud2.left, viewport.width*-0.45, (timestamp-10000)%26000/26000), cloud2.top, cloud2.width, cloud2.height);
			};
			if (progress>20000){
				ctx.drawImage(cloud3.img, cloud3.left-delta(cloud3.left, viewport.width*-0.25, (timestamp-20000)%20000/20000), cloud3.top, cloud3.width, cloud3.height);
			};
			ctx.drawImage(street.img, street.left, street.top, street.width, street.height);
			if (progress>2000 && progress<=2350){
				ctx.save();
				//console.log([pin.left, pin.top, pin.width, pin.height]);
				// ctx.restore();
				var t = (progress-2000)%350/350;
				var scale = puff.calculate(t)[1];
				ctx.translate(pin.left+(pin.width/2), pin.top+pin.height);
				ctx.scale(scale, scale);
				ctx.drawImage(pin.img, -(pin.width/2), -pin.height, pin.width, pin.height);
				//ctx.rect(-50,-100,100,100);
				ctx.fillStyle="blue";
				ctx.fill();
				ctx.restore();
			}else if(progress>2350){
				ctx.fillStyle = "#FEF25B";
				for (var i = 0; i < 3; i++) {
					ctx.beginPath();
					var t = (progress-2350+(660*i))%3000/3000;
					ctx.globalAlpha = 1-t;
					var r = (pin.width/2)+(pin.width/2*1.2)*easeout.calculate(t)[1];
					ctx.arc(pin.left+(pin.width/2), pin.top+(pin.width/2), r, 2 * Math.PI, false);
					ctx.fill();
					ctx.globalAlpha = 1;
				};
				ctx.drawImage(pin.img, pin.left, pin.top, pin.width, pin.height);
			};
		};

		if (phase == 2) {
			var reset = false;
			// if (progress > 5000 && progress < 20000) {
			// 	s = 1-0.8*easeout.calculate(((progress-5000) % 15000)/15000)[1];
			// }else if(progress > 22000 && progress < 23000){
			// 	ctx.save();
			// 	var t = easeout.calculate(((progress-22000) % 1000)/1000)[1];
			// 	s = 0.2-0.12*t;
			// 	ctx.globalAlpha = 1-t;
			// };
			// if (s != 1) {
			// 	ctx.save();
			// 	ctx.translate(viewport.width/2, phase1Height/2);
			// 	ctx.scale(s, s);
			// 	ctx.translate(-viewport.width/2*s, -phase1Height/2*s);
			// };
			// if (progress < 23000) {
				ctx.drawImage(map.img, map.left, map.top, map.width, map.height);
			// };

			if (s != 1) {
				ctx.restore();
			};

			var grd = ctx.createLinearGradient(0, viewport.height/2, 0, viewport.height*0.8);
			ctx.rect(0, 0, viewport.width, viewport.height);
			grd.addColorStop(0, 'rgba(255,255,255,0)');
			grd.addColorStop(1, 'rgba(255,255,255,1)');
			ctx.fillStyle = grd;
			ctx.fill();
			if (ctx.globalAlpha != 1) {
				ctx.globalAlpha = 1;
			};


		};
		if (transitioning) {
			if(progress > 950){
				transitioning = false;
			}
			var t = (1-easeout.calculate((progress%1000)/1000)[1]);
			ctx.globalAlpha = t;
			ctx.fillStyle = "#fff";
			ctx.rect(0, 0, viewport.width, viewport.height);
			ctx.fill();
			ctx.drawImage(buffer, 0, 0);
			ctx.globalAlpha = 1;
		};
		ctx.fillStyle = "black";
		if (timestamp%100 >= 90) {
			fps = Math.round(1000/(timestamp-lastStep));
		};
		ctx.fillText(fps+"fps", 10, 20*devicePixelRatio);
		lastStep = timestamp;
		window.requestAnimationFrame(step);
	}
	var top = 0.5625;

	window.addEventListener("keyup", function(e){
		console.log(e.keyCode);
		if (e.keyCode==38) {
			top += 0.01;
		}else if(e.keyCode == 40){
			top -= 0.01;
		}
		if (e.keyCode == 32) {
			changePhase();
		}
		pin.top = phase1Height*top;
		/*if(e.keyCode==39){
			outerR+=0.5;
		}else if(e.keyCode == 37){
			outerR-=0.5;
		}*/
		console.log(top);
		//spin();
	}, false);
	window.addEventListener("touchstart", function(e){
		changePhase();
	}, false);
	function changePhase(){
		bufferctx.putImageData(ctx.getImageData(0,0,viewport.width, viewport.height), 0,0);
		start = null;
		phase++;
		transitioning = true;
	}
}, false);