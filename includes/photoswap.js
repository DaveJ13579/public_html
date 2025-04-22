var fAnimate = new Array();var fTimer   = new Array();var fCount   = new Array();var fImages  = new Array();

function f(id,iArray,fInterval,hTime)
{ document.write('<div id=" '+id+' "><img id="'+id+'img" onload="fRun()"/></div>');
  var ss = document.getElementById(id+'img');
  fCount   = 0;
  fImages  = iArray;
  fAnimate = 'run';
  fTimer   = setInterval('fAnimation(\''+id+'\',\''+hTime+'\');',fInterval);
}
function fRun() { fAnimate = 'run'; }

function fAnimation(id,hTime)
{ if(fAnimate=='run')
  {var obj = document.getElementById(id+'img');
    var opa = fCount%200;

    if(opa==0)   { fAnimate = 'load'; obj.src = fImages[Math.floor(fCount/200)%fImages.length]; }
    else if(opa==100)   { fAnimate = 'hold'; setTimeout("fAnimate='run'",hTime);  }
    else if(opa>100) { opa = 200-opa; }
      
    obj.style.opacity = (opa/100).toString();
	fCount++;
    
    if(fCount==(fImages.length*200)) {fCount=0;}
  }
}

