function $(a) {
	return document.querySelector(a);
}

function register() {
	$("#registrar").setAttribute("hidden", "");
	u2f.register([req], sigs, function(data) {
		$("#waiting_usb").setAttribute("hidden", "");
		if(data.errorCode) {
			$("#usb_error_"+data.errorCode).removeAttribute("hidden");
			return;
		}
		$("#done").removeAttribute("hidden");
		var http = new XMLHttpRequest();
		var url = "ajax/registerkey.php";
		var params = "data="+encodeURIComponent(JSON.stringify(data))+"&req="+encodeURIComponent(JSON.stringify(req));
		http.open("POST", url, true);
		http.onreadystatechange = function() {//Call a function when the state changes.
		    if(http.readyState == 4 && http.status == 200) {
		        var response = JSON.parse(http.responseText);
		        if (response.errorCode) {
		        	$("#registrar_container").innerHTML = "<span class='icon svg-ic_error_24px'></span> "+response.errorDetail;
		        } else {
		        	window.location = "securitykeys.php";
		        }
		    }
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(params);
	});
	$("#waiting_usb").removeAttribute("hidden");
}

window.onload = function() {
	if(window.u2f !== undefined) {
		$("#extension").removeAttribute("hidden");
	} else {
		$(".step .text").style.color = "red";
		$(".step .text").innerHTML = '<b>Instala la extensión <a href="https://chrome.google.com/webstore/detail/fido-u2f-universal-2nd-fa/pfboblefjcgdjicmnffhdgionmgcdmne">FIDO U2F</a> de la Chrome Web Store</b>.<br>Después vuelve a cargar esta página.';
	}
	$("#registrar").addEventListener('click', register);
}