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
	var url = "ajax/configure2sv.php";
	var params = "secret="+encodeURIComponent(secret)+"&code="+encodeURIComponent(code);
	http.open("POST", url, true);
	http.onreadystatechange = function() {
	    if(http.readyState == 4 && http.status == 200) {
	    	$("#input").removeAttribute("hidden");
	    	$("#waiting").setAttribute("hidden", "");
	        var response = JSON.parse(http.responseText);
	        if (response.errorCode) {
	        	if (response.errorCode == 3) {
	        		$('#verificationcode').className = "form-error";
	        		$("#error_3").removeAttribute("hidden");
	        		$("#mysqli_error").innerText = response.errorDetail;
	        	} else {
	        		$('#verificationcode').className = "form-error";
	        		$("#error_"+response.errorCode).removeAttribute("hidden");
	        	}
	        } else {
	        	window.location = "2stepverification.php?msg=configured";
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
}