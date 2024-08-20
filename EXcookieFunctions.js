/* Description: Sets a cookie for this website
   Input parameters:  cname - string for cookie name
             cvalue - string for cookie value
             numDays - int for days to expiration 
   returns: Nothing
   side effect: new cookie is added to document.cookie */
function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";

}

/* Description: Gets a cookie with the given name if one exists
   Input parameters:  cname - string for cookie name
           
   returns: String with cookie value if one exists; null otherwise
   side effects: none */
function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');

  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return null;
}

/* Description: Deletes the cookie with the given name if it exists
   Input parameters:  cname - string for cookie name
   returns: nothing
   side effects: cookie is erased by setting its value to empty string and a negative expiration date */
function eraseCookie(cname) {
  setCookie(cname, "", -1);
}

function redirect(){
  setTimeout(function() {
    window.location.href = './products.html';
  }, 5000);
}
