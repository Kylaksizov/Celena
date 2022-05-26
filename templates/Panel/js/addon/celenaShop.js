$(function(){

    // https://rateyo.fundoocode.ninja/
    $(".rating_plugin").rateYo({
        starWidth: "17",
        fullStar: true,
        readOnly: true,
        normalFill: "#ccc",
        ratedFill: "#ffc500"
    });


    function rgb2hsl(HTMLcolor) {
        r = parseInt(HTMLcolor.substring(0,2),16) / 255;
        g = parseInt(HTMLcolor.substring(2,4),16) / 255;
        b = parseInt(HTMLcolor.substring(4,6),16) / 255;
        let max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;
        if (max == min) {
            h = s = 0;
        } else {
            let d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        return [h, s, l]; // H - цветовой тон, S - насыщенность, L - светлота
    }

    function changeColor (HTMLcolor) {
        let e = rgb2hsl(HTMLcolor.replace('#', ''));
        if ((e[0]<0.55 && e[2]>=0.5) || (e[0]>=0.55 && e[2]>=0.75)) {
            return '#3c4252';
        } else {
            return '#FFFFFF';
        }
    }


    function rgb2hex(rgb) {
        rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
        function hex(x) {
            return ("0" + parseInt(x).toString(16)).slice(-2);
        }
        return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    }

    $(document).on("click", ".plug_poster", function(){
        setTimeout(function(){
            let headerColor = changeColor(rgb2hex($("#result_response_plugin .modal_title").css("background-color")));
            $("#result_response_plugin .modal_title").css("color", headerColor);
            if(headerColor == '#3c4252') $("#result_response_plugin .modal_title .close").addClass("close_black")
            else $("#result_response_plugin .modal_title .close").removeClass("close_black")
        }, 1000)
    })

})