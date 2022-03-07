HTMLElement.prototype.addClass = function(className){
	var reg = new RegExp("/[\^\s]?("+className+")[\s$]?/");
	if (reg.test(this.className)) {

	}
}
function cubicBezier(x1, y1, x2, y2){
    var p1 = [x1, y1], p2 = [x2, y2];
    this.calculate = function(t){
        return [
            0 * Math.pow(1-t, 3) + p1[0] * 3 * Math.pow(1-t, 2) * t + p2[0] * 3 * (1-t) * Math.pow(t, 2) + 1 * Math.pow(t, 3),
            0 * Math.pow(1-t, 3) + p1[1] * 3 * Math.pow(1-t, 2) * t + p2[1] * 3 * (1-t) * Math.pow(t, 2) + 1 * Math.pow(t, 3),
        ];
    };
}

function startAnimation(){
	var loadingEL = document.getElementById("loading");
	loadingEL.className+="fadeOut";
	setTimeout(function(){
		document.body.removeChild(loadingEL);
	}, 500);
	document.getElementsByClassName("pin")[0].className+=" puff";
	window.setTimeout(function(){
		changePhase(1);
		window.setTimeout(function(){
			document.getElementsByClassName("map")[0].className+=" zoom";
		}, 1500);
	}, 10000);
}
var lastPhase = 0;
function changePhase(id){
	var phases = document.getElementsByClassName("phase");
	phases[lastPhase].className = phases[lastPhase].className.replace(" fadeIn", "") + " fadeOut";
	phases[id].className = phases[id].className.replace(" fadeOut", "") + " fadeIn";
	window.setTimeout(function(){
		document.getElementById('viewport').removeChild(phases[lastPhase]);

	}, 500);
}
easeout = new cubicBezier(0, 0, -0.4, 1);
document.addEventListener("DOMContentLoaded", function(){
	var loadedCount = 0,
		assetCount = 0,
		ww = window.innerWidth,
		wh = window.innerHeight||window.outerHeight;

	var spinner = document.getElementById("spinner");
	var spinnerCtx = spinner.getContext('2d');
	var size = Math.min(ww,wh)*0.2;
	if (devicePixelRatio != 1) {
		spinner.width = size*devicePixelRatio;
		spinner.height = size*devicePixelRatio;
		spinner.style.width = size+"px";
		spinner.style.height = size+"px";
	}else{
		spinner.width = size;
		spinner.height = size;
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
		lastStep = Date.now();

	function spin(timestamp){
		spinnerCtx.clearRect(0, 0, spinner.width, spinner.height);
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
		window.requestAnimationFrame(spin);
	}
	window.requestAnimationFrame(spin);

	preloadElements = document.getElementsByClassName('svg-holder');

	function loadAsset(el){
		var url = "img/"+el.dataset.asset;
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4){
				el.innerHTML = xhr.responseText+el.innerHTML;
				loadedCount++;
				if(loadedCount == assetCount){
					startAnimation();
				}
			}
		}
		xhr.open('GET', url);
		xhr.send();
	}
	for (var i = 0; i < preloadElements.length; i++) {
		if (preloadElements[i].dataset.asset != "") {
			assetCount+=1;
			loadAsset(preloadElements[i]);
		}
	};

	/*window.addEventListener("keyup", function(e){
		console.log(e.keyCode);
		if (e.keyCode==38) {
			angleOffset+=0.01;
		}else if(e.keyCode == 40){
			angleOffset-=0.01;
		}
		if(e.keyCode==39){
			outerR+=0.5;
		}else if(e.keyCode == 37){
			outerR-=0.5;
		}
		console.log([angleOffset, outerR]);
		spin();
	}, false);*/
}, false);

//swipe gesture code;
var gestureX = 0;
var gestureY = 0;
var swipeRight = new Event('swipeRight');
var swipeLeft = new Event('swipeLeft');

window.addEventListener("touchstart", function(e){
	gestureX = e.touches[0].pageX;
	gestureY = e.touches[0].pageY;
}, false);
window.addEventListener("touchmove", function(e){
	deltaX = e.touches[0].pageX - gestureX;
	deltaY = e.touches[0].pageY - gestureY;
	distance = Math.sqrt( Math.pow(deltaX, 2) + Math.pow(deltaY, 2));
	if (distance > 30) {
		angle = Math.atan2(deltaY, deltaX) * 180 / Math.PI;
		if (angle <= 15 && angle >= -15){
			window.dispatchEvent(swipeLeft);
		}
		if (angle <= -165 || angle >= 165) {
			window.dispatchEvent(swipeRight);
		}
	}
	gestureX = e.touches[0].pageX;
	gestureY = e.touches[0].pageY;
}, false);
window.addEventListener("touchend", function(e){
	gestureX = 0;
	gestureY = 0;
}, false);