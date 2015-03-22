function $(a) {
	return document.querySelector(a);
}

window.onload = function() {
	if (window.u2f === undefined) {
		$("#challenge").setAttribute("hidden", "");
		$("#install_extension").removeAttribute("hidden");
	} else {
		$("#waiting_usb").removeAttribute("hidden");
		u2f.sign(req, function(data) {
			$("#waiting_usb").setAttribute("hidden", "");
			if(data.errorCode) {
				$("#usb_error_"+data.errorCode).removeAttribute("hidden");
				return;
			}
			$("#done").removeAttribute("hidden");
			var http = new XMLHttpRequest();
			var url = "ajax/verifychallenge.php";
			var params = "data="+encodeURIComponent(JSON.stringify(data))+"&req="+encodeURIComponent(JSON.stringify(req));
			http.open("POST", url, true);
			http.onreadystatechange = function() {//Call a function when the state changes.
			    if(http.readyState == 4 && http.status == 200) {
			        var response = JSON.parse(http.responseText);
			        if (response.errorCode) {
			        	$("#registrar_container").innerHTML = "<span class='icon svg-ic_error_24px'></span> "+response.errorDetail;
			        } else {
			        	window.location = "index.php";
			        }
			    }
			}
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(params);
		});
	}
}