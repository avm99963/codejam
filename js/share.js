function $(a) {
	return document.querySelector(a);
}

function $all(a) {
	return document.querySelectorAll(a);
}

function share() {
	$("#modal-dialog").innerHTML = "<h3>"+i18n.loading+"</h3>";
	$("#modal-dialog").hidden = false;
	$("#modal-dialog-bg").hidden = false;
	var iframe = document.createElement("iframe");
	iframe.setAttribute("src", "iframe/share.php?contest="+contest);
	iframe.className = "loading";
	$("#modal-dialog").appendChild(iframe);
	console.log(iframe);
	iframe.onload = function() {
		if ($("#modal-dialog h3") !== null) {
			$("#modal-dialog").removeChild($("#modal-dialog h3"));
			iframe.className = "";
		}
	}
}

function hidemodal() {
	$("#modal-dialog").hidden = true;
	$("#modal-dialog-bg").hidden = true;
	$("#modal-dialog").innerText = "";
}

function init() {
	$("#share").addEventListener('click', share);
	if (window.contest === undefined) {
		console.error("No contest variable was defined :-S");
	}
}

window.addEventListener('load', init);