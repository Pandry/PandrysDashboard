/* Minimize Button */
$(".panel-min-button").click(function(){
    if($(this).hasClass("fa-plus")){
        $(this).removeClass("fa-plus").addClass("fa-minus");
    }else if($(this).hasClass("fa-minus")){
        $(this).addClass("fa-plus").removeClass("fa-minus");
    }
});


  

function updateData(){
            var http_request = new XMLHttpRequest();
            try{
               // Opera 8.0+, Firefox, Chrome, Safari
               http_request = new XMLHttpRequest();
            }catch (e){
               // Internet Explorer Browsers
               try{
                  http_request = new ActiveXObject("Msxml2.XMLHTTP");
					
               }catch (e) {
				
                  try{
                     http_request = new ActiveXObject("Microsoft.XMLHTTP");
                  }catch (e){
                     // Something went wrong
                     alert("Your browser broke!");
                     return false;
                  }
               }
            }
			
            http_request.onreadystatechange = function(){
			
               if (http_request.readyState == 4  ){

                  var jsonObj = JSON.parse(http_request.responseText);
                  cpurtgraph.animate((jsonObj['user']+jsonObj['nice']+jsonObj['sys'])/100);
                  updateRamGraph([jsonObj['activeP'], jsonObj['bufferedP'], jsonObj['cachedP']]);
                  
               }
            }
			
            http_request.open("GET", "index.php?action=getinfo", true);
            http_request.send();


}


/* Grafico RAM */
function updateRamGraph(data){
  /* Struct: [used, buffered, cached] */
  var used = data[0],
    buffered = data[1],
    cached = data[2],
    unit = ($(".gr-ram-xlg-surface")[0].attributes.width.value)/100,
    usedrect = $(".gr-ram-xlg-userusedmem"),
    cachedrect = $(".gr-ram-xlg-cachedmem"),
    bufferect = $(".gr-ram-xlg-bufferedmem");

    
    cachedrect.animate({ svgWidth: (cached+buffered+used)*100*unit});
    bufferect.animate({ svgWidth: (buffered+used)*100*unit});
    usedrect.animate({ svgWidth: used*100*unit});

    
  
}



















/* Grafico CPU */
var cpurtgraph = new ProgressBar.SemiCircle(cpuTableContent, {
  strokeWidth: 6,
  color: '#FFEA82',
  trailColor: '#eee',
  trailWidth: 1,
  easing: 'easeInOut',
  duration: 1400,
  svgStyle: null,
  text: {
    value: '100',
    alignToBottom: false
  },
  from: {color: '#009688'},
  to: {color: '#A94037'},
  // Set default step function for all animate calls
  step: (state, cpurtgraph) => {
    cpurtgraph.path.setAttribute('stroke', state.color);
    var value = Math.round(cpurtgraph.value() * 100);
    if (value === 0) {
      cpurtgraph.setText('0');
    } else {
      cpurtgraph.setText(value);
    }

    cpurtgraph.text.style.color = state.color;
  }
});






$(document).ready(function(){
window.paceOptions = {
    ajax: false,
    restartOnRequestAfter: false,
};
cpurtgraph.text.style.fontFamily = '"Raleway", Helvetica, sans-serif';
cpurtgraph.text.style.fontSize = '2rem';

cpurtgraph.animate(1);
window.setInterval(function(){updateData();}, 1000);
});