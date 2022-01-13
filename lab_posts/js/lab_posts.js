function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();

function showPlus(){
  var i, tabconent, tablinks;
  tablinks = document.getElementsByClassName("tablinks");
  newtab = document.getElementById("new_tab_btn");
  if(tablinks[tablinks.length - 2].classList.contains("unused_tab")) {
    newtab.style.display = "block";
  } else {
    newtab.style.display = "none";
  }
}

function newTab(){
  var i, tablinks;
  tablinks = document.getElementsByClassName("unused_tab");
  tablinks[0].className = tablinks[0].className.replace(" unused_tab", "");

  showPlus();
}
    
jQuery(document).ready(function($) {
   		console.log(456);
});

window.onbeforeunload = function(){
  return 'Are you sure you want to leave?';
};

function ajaxLabPost(tablinkone, tablinktwo, tablinkthree, tablinkfour, tablinkfive, tablinksix, pst_status) {
	console.log(pst_status);
	textareaone= tinyMCE.get('textareaone').getContent();
	textareatwo= tinyMCE.get('textareatwo').getContent();
	textareathree= tinyMCE.get('textareathree').getContent();
	textareafour= tinyMCE.get('textareafour').getContent();
	textareafive= tinyMCE.get('textareafive').getContent();
	textareasix= tinyMCE.get('textareasix').getContent();
	alert(textareaone);
	jQuery.ajax({
		type: 'POST',
		url: ajaxlabpost.ajaxurl,
		data:{ 
		  action: 'lab_post_callback',
		  tablinkone: tablinkone,
		  tablinktwo: tablinktwo,
		  tablinkthree: tablinkthree,
		  tablinkfour: tablinkfour,
		  tablinkfive: tablinkfive,
		  textareaone: textareaone,
		  textareatwo: textareatwo,
		  textareathree: textareathree,
		  textareafour: textareafour,
		  textareafive: textareafive,
		  post_status: pst_status
		},
		success: function (response) {
			alert(response[0]);
			if(response[0] == 0){
				alert("publish");
			}else if(response[0] == 1){
				window.open(response.substring(1), '_self');
			}else if(response[0] == 2){
				alert("save");
			}
    		},
    		error: function (jqXHR, exception) {
        	  var msg = '';
        	  if (jqXHR.status === 0) {
        	    msg = 'Not connect.\n Verify Network.';
        	  } else if (jqXHR.status == 404) {
        	    msg = 'Requested page not found. [404]';
        	  } else if (jqXHR.status == 500) {
        	    msg = 'Internal Server Error [500].';
        	  } else if (exception === 'parsererror') {
        	    msg = 'Requested JSON parse failed.';
        	  } else if (exception === 'timeout') {
        	    msg = 'Time out error.';
        	  } else if (exception === 'abort') {
        	    msg = 'Ajax request aborted.';
        	  } else {
        	    msg = 'Uncaught Error.\n' + jqXHR.responseText;
        	  }
		  alert(msg);
    		}, 
     		/*error: function(xhr, status, error){
         	  var errorMessage = xhr.status + ': ' + xhr.statusText
         	  alert('Error - ' + errorMessage);
		  alert(ajaxlabpost.ajaxurl);
     		}*/
	});
}

