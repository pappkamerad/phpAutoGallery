function Go(x) {
 if(x == "nothing") {
   document.forms[0].reset();
   document.forms[0].elements[0].blur();
   return;
 }
 else if(x == "end")
   top.location.href = parent.frames[1].location;
 else {
   top.location.href = x;
   document.forms[0].reset();
   document.forms[0].elements[0].blur();
 }
}