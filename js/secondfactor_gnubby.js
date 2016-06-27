function xhr(method, url, params, callback) {
	var http = new XMLHttpRequest();
	if (method == "POST") {
		http.open(method, url, true);
	} else {
		http.open(method, url+"?"+params, true);
	}
	http.onreadystatechange = function() {
		if(http.readyState === XMLHttpRequest.DONE) {
			if(this.status != 200) {
				console.warn("Attention, status code "+this.status+" when loading via xhr url "+url);
			}
			callback(this.responseText, this.status);
	  };
	}
	if (method == "POST") {
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(params);
	} else {
		http.send();
	}
}

function $(a) {
	return document.querySelector(a);
}

window.onload = function() {
	xhr("GET", "chrome-extension://pfboblefjcgdjicmnffhdgionmgcdmne/u2f-comms.html", "", function(response, status) {
		if (status == 200) {
			$("#waiting_usb").removeAttribute("hidden");
			u2f.sign(host, challenge, req, function(data) {
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
			}, 30);
		} else {
			$("#challenge").setAttribute("hidden", "");
			$("#install_extension").removeAttribute("hidden");
		}
	});
}
