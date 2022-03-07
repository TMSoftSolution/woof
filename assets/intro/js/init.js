/*************************\
|* define easing methods *|
\*************************/
var animationMode = {
	easeout: new cubicBezier(0, 0, -0.4, 1),
	easein: new cubicBezier(0,0,1,-0.4),
	puff: new cubicBezier(0,0,0.4,2)
};

/*********************************\
|* add clearing method to canvas *|
\*********************************/
CanvasRenderingContext2D.prototype.clear = function(){
	this.clearRect(0, 0, this.canvas.width, this.canvas.height);
}

Object.prototype.clone = function(){
    var copy = this.constructor();
    for (var attr in this) {
        if (this.hasOwnProperty(attr)) copy[attr] = this[attr];
    }
    return copy;
};

var resources = [
	{name: "cloud-1.svg"},
	{name: "cloud-2.svg"},
	{name: "cloud-3.svg"},
	{name: "street.svg"},
	{name: "pin.svg"},
	{name: "map.svg"},
	{name: "world.svg"},
	{name: "logo.svg"},
	{name: "stars.svg"}
];

/**************************\
|* preload all art assets *|
\**************************/
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
var pixelRatio = Math.min(devicePixelRatio, 1.5);
// var pixelRatio = devicePixelRatio;

document.addEventListener("DOMContentLoaded", function(){
	/******************************\
	|* set up sizing and contexts *|
	\******************************/
	var ww = window.innerWidth,
	wh = window.innerHeight||window.outerHeight;


	/***********************************************************\
	|* we are using a separate context here for the spinner    *|
	|* because the animation code was already finished as part *|
	|* of a proof of concept                                   *|
	\***********************************************************/
	var spinner = document.getElementById("spinner");
	var spinnerCtx = spinner.getContext('2d');
	var size = Math.min(ww,wh)*0.2;


	var viewport = document.getElementById("viewport");
	var ctx = viewport.getContext("2d");
	var textareas = document.body.getElementsByClassName('textarea');
	var dots = document.body.getElementsByClassName('dot');
	Drawable.prototype.ctx = ctx;
	console.log(pixelRatio);
	if (pixelRatio != 1) {
		spinner.width = size*pixelRatio;
		spinner.height = size*pixelRatio;
		spinner.style.width = size+"px";
		spinner.style.height = size+"px";
		viewport.width = ww*pixelRatio;
		viewport.height = wh*pixelRatio;
		viewport.style.width = ww+"px";
		viewport.style.height = wh+"px";
	}else{
		spinner.width = size;
		spinner.height = size;
		viewport.width = ww;
		viewport.height = wh;
	}

	var center = {x: spinner.width/2, y: spinner.height*0.412},
		outerR = spinner.width*0.2768,
		innerR = spinner.width*0.146,
		pinPoint = {x: spinner.width/2, y: spinner.height*0.8967},
		angle = 0,
		width = 0,
		start = null,
		angleOffset = 0.165;

	spinner.style.marginTop = "-"+(size/2)+"px";
	spinner.style.marginLeft = "-"+(size/2)+"px";

	spinnerCtx.strokeStyle = "#292B30";
	spinnerCtx.lineWidth = spinner.width*0.03;
	spinnerCtx.lineCap = "round";
	spinnerCtx.lineJoin = "round";


	/*****************************************\
	|* animation code for preloading spinner *|
	\*****************************************/
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
            width = 360*animationMode.easeout.calculate(progress/750);
        }else if (progress <1500) {
            angle = 360*animationMode.easeout.calculate((progress-750)/750);
            width = 360*(1-animationMode.easeout.calculate((progress-750)/750));
        }
        spinnerCtx.arc(center.x, center.y, innerR, angle*Math.PI/180, (angle+width)*Math.PI/180 );

        if (progress >= 1500) {
            start = timestamp;
        }
		spinnerCtx.stroke();
		if (loadedCount != assetCount) {
			window.requestAnimationFrame(spin);
		}else {
			//call init after finished preloading and stop spinning
			init();
			//use CSS animation for fade out
			document.getElementById("loading").className = "fadeOut";
			window.setTimeout(function(){
				document.body.removeChild(document.getElementById("loading"));
			}, 500);
		}
	}

	//start spinner animation
	window.requestAnimationFrame(spin);

	//start preloading resources
	preload(0);


	/**************************************\
	|* set up Drawable objects and layers *|
	\**************************************/
	var timeline1 = new Timeline();
	var timeline1b = new Timeline();
	timeline1b.repeat = true;
	var timeline2 = new Timeline();
	timeline2.repeat = true;
	var timeline3 = new Timeline();
	var phase1 = new Layer({
		w: viewport.width,
		h: viewport.height*0.75
	});
	var phase2,
		fadeBox,
		globe,
		space;
	var lastStep;
	var fps = 0;
	var drawList = [];
	var phase = 1;
	var transitioning = false;
	function init(){
		var cloud1 = new Sprite({
			img: resources[0].img,
			x: viewport.width,
			y: viewport.height*-0.004,
			w: viewport.width*0.28632
		});
		var cloud2 = new Sprite({
			img: resources[1].img,
			x: viewport.width,
			y: viewport.height*0.19,
			w: viewport.width*0.42865
		});
		var cloud3 = new Sprite({
			img: resources[2].img,
			x: viewport.width,
			y: viewport.height*0.36,
			w: viewport.width*0.24063
		});
		var street = new Sprite({
			img: resources[3].img,
			x: -(viewport.width*0.235),
			w: viewport.width*1.47
		});
		street.y = viewport.height*0.75-street.h;

		var pin = new Sprite({
			img: resources[4].img,
			x: viewport.width*0.415,
			w: viewport.width*0.23352,
			a: 0,
			s: 0,
			transformOrigin: {x: 0.5, y: 1}
		});
		pin.y = viewport.height*0.75-(street.h*0.2)-pin.h;

		var map = new Sprite({
			img: resources[5].img,
			w: viewport.width*4,
		});

		phase2 = new Layer({
			w: map.w,
			h: map.h,
			x: (viewport.width-map.w)/2,
			y: (viewport.height*0.75-map.h)/2,
			a: 0
		});

		phase2.addChild("map", map);

		var radarOpts = {
			w: pin.w*1.5,
			h: pin.w*1.5,
			fill: "#FEF25B"
		};

		var ripple = new Layer(radarOpts);
		ripple.x = pin.x+(pin.w/2-ripple.w/2);
		ripple.y = pin.y+(pin.w/2-ripple.w/2);
		ripple.a = 0;

		var pulseOpts = {
			type: "circle",
			w: pin.w,
			h: pin.w,
			x: (radarOpts.w-pin.w)/2,
			y: (radarOpts.h-pin.w)/2,
			s: 0.6
		};

		var pulse1 = new Shape(pulseOpts);
		var pulse2 = new Shape(pulseOpts);
		var pulse3 = new Shape(pulseOpts);
		radarOpts = {
			w: phase2.w*0.15,
			h: phase2.w*0.15,
			s: 0.1
		};
		pulseOpts = {
			type: "circle",
			w: radarOpts.w*0.55,
			h: radarOpts.w*0.55
		}
		pulseOpts.x = radarOpts.w/2-pulseOpts.w/2;
		pulseOpts.y = radarOpts.h/2-pulseOpts.h/2;
		logoOpts = {
			img: resources[7].img,
			w: radarOpts.w*0.3,
		}
		logoOpts.h = logoOpts.w*(resources[7].img.height/resources[7].img.width);
		logoOpts.x = radarOpts.w/2-logoOpts.w/2;
		logoOpts.y = radarOpts.h*0.52-logoOpts.h/2;
		var pulse4 = new Shape(pulseOpts);
		var pulse5 = new Shape(pulseOpts);
		var pulse6 = new Shape(pulseOpts);
		var pulse7 = new Shape(pulseOpts);
		var pulse8 = new Shape(pulseOpts);
		var pulse9 = new Shape(pulseOpts);
		var pulse10 = new Shape(pulseOpts);
		var pulse11 = new Shape(pulseOpts);
		var pulse12 = new Shape(pulseOpts);
		var pulse13 = new Shape(pulseOpts);
		var pulse14 = new Shape(pulseOpts);
		var pulse15 = new Shape(pulseOpts);
		cloud1.rasterize();
		delete resources[0].img;
		cloud2.rasterize();
		delete resources[1].img;
		cloud3.rasterize();
		delete resources[2].img;
		street.rasterize();
		delete resources[3].img;
		pin.rasterize();
		delete resources[4].img;
		map.rasterize();
		delete resources[5].img;

		phase1.addChild("cloud1", cloud1);
		phase1.addChild("cloud2", cloud2);
		phase1.addChild("cloud3", cloud3);
		phase1.addChild("street", street);
		ripple.addChild("pulse1", pulse1);
		ripple.addChild("pulse2", pulse2);
		ripple.addChild("pulse3", pulse3);

		phase1.addChild("ripple", ripple);
		phase1.addChild("pin", pin);
		/*phase1.addMask(new Shape({
			y: phase1.h/2,
			w: phase1.w,
			type: "circle"
		}));*/
		var fade = ctx.createLinearGradient(0, viewport.height*0.35, 0, viewport.height*0.8);
		fade.addColorStop(0, "rgba(255,255,255,0)");
		fade.addColorStop(1, "rgba(255,255,255,1)");

		fadeBox = new Shape({
			type: "rectangle",
			x: 0,
			y: viewport.height*0.35,
			w: viewport.width,
			h: viewport.height*0.65,
			fill: fade
		});

		radarOpts.x = phase2.w/2-radarOpts.w/2;
		radarOpts.y = phase2.h/2-radarOpts.h/2;
		radarOpts.fill = "#FEF25B";
		radarOpts.s =0.8;
		var radarYellow = new Layer(radarOpts);
		radarOpts.fill = "#FEF25B";
		radarOpts.x = phase2.w*0.66-radarOpts.w/2;
		radarOpts.y = phase2.h*0.55-radarOpts.h/2;
		radarOpts.s = 0;
		radarOpts.a = 0;
		var radarRed = new Layer(radarOpts);
		radarOpts.fill = "#FEF25B";
		radarOpts.x = phase2.w*0.3-radarOpts.w/2;
		radarOpts.y = phase2.h*0.55-radarOpts.h/2;
		var radarBlue = new Layer(radarOpts);
		radarOpts.fill = "#FEF25B";
		radarOpts.x = phase2.w*0.53-radarOpts.w/2;
		radarOpts.y = phase2.h*0.3-radarOpts.h/2;
		var radarGreen = new Layer(radarOpts);
		radarYellow.addChild("pulse4", pulse4);
		radarYellow.addChild("pulse5", pulse5);
		radarYellow.addChild("pulse6", pulse6);
		radarYellow.addChild("logo", new Sprite(logoOpts));
		radarYellow.childMap.logo.rasterize();
		radarRed.addChild("pulse7", pulse7);
		radarRed.addChild("pulse8", pulse8);
		radarRed.addChild("pulse9", pulse9);
		radarRed.addChild("logo", new Sprite(logoOpts));
		radarRed.childMap.logo.rasterize();
		radarBlue.addChild("pulse10", pulse10);
		radarBlue.addChild("pulse11", pulse11);
		radarBlue.addChild("pulse12", pulse12);
		radarBlue.addChild("logo", new Sprite(logoOpts));
		radarBlue.childMap.logo.rasterize();
		radarGreen.addChild("pulse13", pulse13);
		radarGreen.addChild("pulse14", pulse14);
		radarGreen.addChild("pulse15", pulse15);
		radarGreen.addChild("logo", new Sprite(logoOpts));
		radarGreen.childMap.logo.rasterize();

		globe = new Layer({
			w: viewport.width*0.85,
			h: viewport.width*0.85,
			s:1.25,
			a: 0
		});
		globe.x= (viewport.width-globe.w)/2;
		globe.y = (viewport.height*0.4)-(globe.h/2);
		// globe.x = (viewport.width-globe.w)/1.5;
		// globe.y = (viewport.height-globe.h)/1.5;
		globe.addChild("bg", new Shape({
			type: "rectangle",
			w: globe.w,
			h: globe.h,
			fill: "#fff",
			x: 0,
			y: 0,
		}));
		globe.addMask(new Shape({
			type: "circle",
			w: globe.w,
			h: globe.h,

		}));

		var worldSprite = new Sprite({
			img: resources[6].img,
			h: globe.h*0.9
		});
		worldSprite.rasterize();
		delete resources[6].img;

		var worldLayer = new Layer({
			w: worldSprite.w,
			h: worldSprite.h
		});
		worldLayer.addChild("map", worldSprite);

		radarOpts = {
			w: globe.w*0.4,
			h: globe.w*0.4,
			x: worldSprite.w*0.1,
			y: worldSprite.h*0.2,
			fill: "#FEF25B"
		};

		pulseOpts = {
			type: "circle",
			w: radarOpts.w*0.4,
			h: radarOpts.h*0.4,
			x: radarOpts.w*0.3,
			y: radarOpts.h*0.3
		}
		logoOpts.w = pulseOpts.w*0.6;
		logoOpts.h = pulseOpts.h*0.6;
		logoOpts.x = pulseOpts.x+pulseOpts.w*0.22;
		logoOpts.y = pulseOpts.y+pulseOpts.h*0.2;

		worldLayer.addChild("yellow", new Layer(radarOpts));
		worldLayer.childMap.yellow.addChild("pulse1", new Shape(pulseOpts));
		worldLayer.childMap.yellow.addChild("pulse2", new Shape(pulseOpts));
		worldLayer.childMap.yellow.addChild("pulse3", new Shape(pulseOpts));
		worldLayer.childMap.yellow.addChild("logo", new Sprite(logoOpts));
		radarOpts.fill = "#FEF25B";
		radarOpts.x = worldSprite.w*0.35;
		radarOpts.y = worldSprite.h*0.4;
		worldLayer.addChild("red", new Layer(radarOpts));
		worldLayer.childMap.red.addChild("pulse1", new Shape(pulseOpts));
		worldLayer.childMap.red.addChild("pulse2", new Shape(pulseOpts));
		worldLayer.childMap.red.addChild("pulse3", new Shape(pulseOpts));
		worldLayer.childMap.red.addChild("logo", new Sprite(logoOpts));
		radarOpts.fill = "#FEF25B";
		radarOpts.x = worldSprite.w*0.55;
		radarOpts.y = worldSprite.h*0.05;
		worldLayer.addChild("blue", new Layer(radarOpts));
		worldLayer.childMap.blue.addChild("pulse1", new Shape(pulseOpts));
		worldLayer.childMap.blue.addChild("pulse2", new Shape(pulseOpts));
		worldLayer.childMap.blue.addChild("pulse3", new Shape(pulseOpts));
		worldLayer.childMap.blue.addChild("logo", new Sprite(logoOpts));
		radarOpts.fill = "#FEF25B";
		radarOpts.x = worldSprite.w*0.7;
		radarOpts.y = worldSprite.h*0.55;
		worldLayer.addChild("green", new Layer(radarOpts));
		worldLayer.childMap.green.addChild("pulse1", new Shape(pulseOpts));
		worldLayer.childMap.green.addChild("pulse2", new Shape(pulseOpts));
		worldLayer.childMap.green.addChild("pulse3", new Shape(pulseOpts));
		worldLayer.childMap.green.addChild("logo", new Sprite(logoOpts));
		delete resources[7].img;
		var world = new Shape({
			type: "rectangle",
			x: 0,
			y: globe.h*0.05,
			w: worldSprite.w,
			h: worldSprite.h
		});
		world.pattern = {
			obj: worldLayer,
			repeat: "repeat",
			x: 0,
			y: globe.h*0.05
		};
		/*world.pattern.obj.addChild("bg", new Shape({
			w: 100,
			h: 100,
			fill: "#f0f"
		}));
		world.pattern.obj.addChild("logo", new Sprite(logoOpts));*/
		globe.addChild("world", world);
		phase2.addChild("red", radarRed);
		phase2.addChild("blue", radarBlue);
		phase2.addChild("green", radarGreen);
		phase2.addChild("yellow", radarYellow);

		space = new Layer({
			w: viewport.width,
			h: viewport.height,
			a: 0
		});
		space.addChild("bg", new Shape({
			type: "rectangle",
			w: space.w,
			h: space.h
		}));
		space.addChild("stars", new Sprite({
			img: resources[8].img,
			w: space.w
		}));
		space.childMap.stars.rasterize();
		space.childMap.stars.y = globe.y - (space.childMap.stars.h-globe.h)/2;
		delete resources[8].img;
		// for (var i = 0; i < 50; i++) {
		// 	space.addChild("star"+i, new Shape({
		// 		type: "circle",
		// 		w: space.w*0.01,
		// 		fill: "#fff",
		// 		x: space.w*Math.random(),
		// 		y: space.h*Math.random()
		// 	}));
		// };

		drawList.push(phase1);

		start = null;
		lastStep = Date.now();
		
		//timeline1.addKeyframe(new Tween(cloud1, {x: viewport.width*-0.3}, 30, true), 5);
		//timeline1.addKeyframe(new Tween(cloud2, {x: viewport.width*-0.45}, 45, true), 2);


		timeline1.addKeyframe(new Tween(cloud1, {x: viewport.width*-0.3}, 60, true), 0.001);
		timeline1.keyframes[1][0].time = timeline1.keyframes[1][0].duration*0.15;
		timeline1.addKeyframe(new Tween(cloud2, {x: viewport.width*-0.45}, 45, true), 0.001);
		timeline1.keyframes[1][1].time = timeline1.keyframes[1][1].duration*0.8;


		timeline1.addKeyframe(new Tween(cloud3, {x: viewport.width*-0.25}, 25, true), 0.001);
		timeline1b.addKeyframe(new Tween(pin, {s: 1, a: 1}, 0.35, false, "puff"), 2);
		timeline1b.addKeyframe(new Tween(pin, {y: -pin.h}, 3, false,"easeout"), 7.35);
		timeline1b.addKeyframe(new Tween(ripple, {a: [0,1]}, 0.001, false), 2.35);
		timeline1b.addKeyframe(new Tween(ripple, {y: -pin.h-(pin.y-ripple.y)}, 3, false,"easeout", function(){
			changePhase(2);
		}), 7.35);
		timeline1b.addKeyframe(new Tween(pulse1, {s: 1.5, a: 0}, 3, true), 2);
		timeline1b.addKeyframe(new Tween(pulse2, {s: 1.5, a: 0}, 3, true), 2.66);
		timeline1b.addKeyframe(new Tween(pulse3, {s: 1.5, a: 0}, 3, true), 3.33);

		timeline2.addKeyframe(new Tween(phase1, {a: [1, 0]}, 0.5, false, "easeout"), 0.001);
		timeline2.addKeyframe(new Tween(phase2, {a: [0, 1]}, 0.5, false, "easeout"), 0.001);
		timeline2.addKeyframe(new Tween(phase2, {s: [1, 0.375]}, 16, false, "easeout"), 0.001);

		timeline2.addKeyframe(new Tween(radarYellow, {s: 1.5}, 15, false, "easeout"), 1.5);
		timeline2.addKeyframe(new Tween(pulse4, {s: 1.8, a: 0}, 3, true), 1);
		timeline2.addKeyframe(new Tween(pulse5, {s: 1.8, a: 0}, 3, true), 1.66);
		timeline2.addKeyframe(new Tween(pulse6, {s: 1.8, a: 0}, 3, true), 2.33);
		timeline2.addKeyframe(new Tween(radarRed, {s: [0, 0.8], a: [0, 1]}, 0.35, false, "puff"), 7);
		timeline2.addKeyframe(new Tween(radarRed, {s: [0.8, 1.5], a: [1, 1]}, 9, false, "easeout"), 7.35);
		timeline2.addKeyframe(new Tween(pulse7, {s: 1.8, a: 0}, 3, true), 7);
		timeline2.addKeyframe(new Tween(pulse8, {s: 1.8, a: 0}, 3, true), 7.66);
		timeline2.addKeyframe(new Tween(pulse9, {s: 1.8, a: 0}, 3, true), 8.33);
		timeline2.addKeyframe(new Tween(radarBlue, {s: [0, 0.8], a: [0, 1]}, 0.35, false, "puff", function(){
			textareas[0].className = "textarea";
			textareas[1].className = "textarea";
			textareas[2].className += " active";
			textareas[3].className = "textarea";
		}), 9);
		timeline2.addKeyframe(new Tween(radarBlue, {s: [0.8, 1.5], a: [1, 1]}, 7, false, "easeout"), 9.35);
		timeline2.addKeyframe(new Tween(pulse10, {s: 1.8, a: 0}, 3, true), 9);
		timeline2.addKeyframe(new Tween(pulse11, {s: 1.8, a: 0}, 3, true), 9.66);
		timeline2.addKeyframe(new Tween(pulse12, {s: 1.8, a: 0}, 3, true), 10.33);
		timeline2.addKeyframe(new Tween(radarGreen, {s: [0, 0.8], a: [0, 1]}, 0.35, false, "puff"), 11);
		timeline2.addKeyframe(new Tween(radarGreen, {s: [0.8, 1.5], a: [1, 1]}, 5, false, "easeout"), 11.35);
		timeline2.addKeyframe(new Tween(pulse13, {s: 1.8, a: 0}, 3, true), 11);
		timeline2.addKeyframe(new Tween(pulse14, {s: 1.8, a: 0}, 3, true), 11.66);
		timeline2.addKeyframe(new Tween(pulse15, {s: 1.8, a: 0}, 3, true), 12.33);
		timeline2.addKeyframe(new Tween(phase2, {s: [0.375, 0.25], a: [1, 0]}, 0.5, false, "easeout", function(){
			changePhase(3);
		}), 16.6);
		timeline3.addKeyframe(new Tween(space, { a: 1}, 1, false, "easeout"), 0.001);
		// timeline2.addKeyframe(new Tween(globe, {s: 1, a: 1}, 0.5, false, "easeout"), 16.6);
		timeline3.addKeyframe(new Tween(world.pattern, {x: -world.w}, 20, true), 0.001);

		timeline3.addKeyframe(new Tween(worldLayer.childMap.yellow.childMap.pulse1, {s: 2.5, a: 0}, 3, true), 0.001);
		timeline3.addKeyframe(new Tween(worldLayer.childMap.yellow.childMap.pulse2, {s: 2.5, a: 0}, 3, true), 1.33);
		timeline3.addKeyframe(new Tween(worldLayer.childMap.red.childMap.pulse1, {s: 2.5, a: 0}, 3, true), 0.001);
		timeline3.addKeyframe(new Tween(worldLayer.childMap.red.childMap.pulse2, {s: 2.5, a: 0}, 3, true), 1.33);
		timeline3.addKeyframe(new Tween(worldLayer.childMap.blue.childMap.pulse1, {s: 2.5, a: 0}, 3, true), 0.001);
		timeline3.addKeyframe(new Tween(worldLayer.childMap.blue.childMap.pulse2, {s: 2.5, a: 0}, 3, true), 1.33);
		timeline3.addKeyframe(new Tween(worldLayer.childMap.green.childMap.pulse1, {s: 2.5, a: 0}, 3, true), 0.001);
		timeline3.addKeyframe(new Tween(worldLayer.childMap.green.childMap.pulse2, {s: 2.5, a: 0}, 3, true), 1.33);

		timeline3.addKeyframe(new Tween(globe, {s: 1, a: 1}, 0.75, false, "easeout"), 0.001);
	};
	function step(timestamp){
		ctx.clear();
		if (!lastStep) {lastStep = timestamp};
		var delta = timestamp-lastStep;
		lastStep = timestamp;
        //var progress = timestamp - start;
        if ( phase == 1 ) {
	        timeline1.update(delta);
	        timeline1b.update(delta);
        } else if (phase == 2){
        	timeline2.update(delta);
        }else if(phase == 3){
        	timeline3.update(delta);
        }
		for (var i = 0; i < drawList.length; i++) {
			drawList[i].draw();
		};
        window.requestAnimationFrame(step);
	};
	window.requestAnimationFrame(step);


	function changePhase(id){
		if (phase != id && id < 4) {
			dots[phase-1].className = "dot";
			dots[id-1].className += " active";
			if (id == 1){
				drawList =[];
				phase1.a = 1;
				phase1.childMap.pin.s = 0;
				phase1.childMap.pin.a = 0;
				phase1.childMap.ripple.a = 0;
				drawList.push(phase1);
				timeline1.reset();
				timeline1b.reset();
				textareas[0].className += " active";
				textareas[1].className = "textarea";
				textareas[2].className = "textarea";
				textareas[3].className = "textarea";
			}
			if (id == 2) {
				drawList = [];
				drawList.push(phase2);
				drawList.push(fadeBox);
				phase2.a=1;
				timeline2.reset();
				phase2.childMap.red.s = 0;
				phase2.childMap.red.a = 0;
				phase2.childMap.blue.s = 0;
				phase2.childMap.blue.a = 0;
				phase2.childMap.green.s = 0;
				phase2.childMap.green.a = 0;

				start = null;
				textareas[0].className = "textarea";
				textareas[1].className += " active";
				textareas[2].className = "textarea";
				textareas[3].className = "textarea";
			}
			if(id == 3){
				drawList = [space, globe];
				timeline3.reset();
				textareas[0].className = "textarea";
				textareas[1].className = "textarea";
				textareas[2].className = "textarea";
				textareas[3].className += " active";
			}
			phase = id;
		};
	}

	if (typeof window.ontouchstart != "undefined") {
		window.addEventListener("touchstart", clickHandler, false);
	}else{
		window.addEventListener("click", clickHandler, false);
	}

	function clickHandler(e){
		var id = phase+1;
		if (e.target.className == "dot") {
			id = parseInt(e.target.dataset.id);
		}
		changePhase(id);
	}

	document.getElementById('getStarted').addEventListener("click", getStarted, "false");

	function getStarted(){
		//app developer adds code here to exit UIWebView
		//call document.location.href = ""; reference here http://stackoverflow.com/questions/10843736/close-uiwebview-using-javascriptwindow-close
	}
}, false);