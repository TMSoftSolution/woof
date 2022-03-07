function cubicBezier(x1, y1, x2, y2){
    var p1 = [x1, y1], p2 = [x2, y2];
    this.calculate = function(t){
        /*return [
            0 * Math.pow(1-t, 3) + p1[0] * 3 * Math.pow(1-t, 2) * t + p2[0] * 3 * (1-t) * Math.pow(t, 2) + 1 * Math.pow(t, 3),
            0 * Math.pow(1-t, 3) + p1[1] * 3 * Math.pow(1-t, 2) * t + p2[1] * 3 * (1-t) * Math.pow(t, 2) + 1 * Math.pow(t, 3),
        ];*/
        return 0 * Math.pow(1-t, 3) + p1[1] * 3 * Math.pow(1-t, 2) * t + p2[1] * 3 * (1-t) * Math.pow(t, 2) + 1 * Math.pow(t, 3);
    };
}