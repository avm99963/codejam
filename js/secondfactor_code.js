function $(a) {
	return document.querySelector(a);
}

function verify() {
	var code = $('#verificationcode').value;
	$("#error_1").setAttribute("hidden", "");
	$("#error_2").setAttribute("hidden", "");
	$("#error_3").setAttribute("hidden", "");
	if (code.length != 6) {
		$('#verificationcode').className = "form-error";
		$("#error_1").removeAttribute("hidden");
		return false;
	}
	$("#input").setAttribute("hidden", "");
	$("#waiting").removeAttribute("hidden");
	var http = new XMLHttpRequest();
	var url = "ajax/verify2sv.php";
	var params = "code="+encodeURIComponent(code);
	http.open("POST", url, true);
	http.onreadystatechange = function() {
	    if(http.readyState == 4 && http.status == 200) {
	    	$("#waiting").setAttribute("hidden", "");
	        var response = JSON.parse(http.responseText);
	        if (response.errorCode) {
	        	if (response.errorCode < 2) {
	        		$("#input").removeAttribute("hidden");
	        		$('#verificationcode').className = "form-error";
	        	}
	        	$("#error_"+response.errorCode).removeAttribute("hidden");
	        } else {
	        	window.location = "index.php";
	        }
	    }
	}
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(params);
}

function verify_keypress() {
    if (event.keyCode == 13) {
        verify();
    }
}

window.onload = function() {
	$("#verify").addEventListener('click', verify);
	$("#verificationcode").addEventListener('keypress', verify_keypress);
	$("#verificationcode").focus();
}