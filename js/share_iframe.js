var invited = [], selectize;

function updated() {
	if (document.querySelector("input").value == "") {
		document.querySelector("#done").innerText = "Listo";
	} else {
		document.querySelector("#done").innerText = "Invitar";
	}
}

function remove() {
	var http = new XMLHttpRequest();
	var url = "../ajax/removecontestant.php";
	var params = "contest="+contest+"&contestant="+this.parentNode.parentNode.getAttribute("data-id");
	http.open("POST", url, true);
	http.onload = function() {
	    if(this.status == 200) {
	        var response = JSON.parse(this.responseText);
	        if (response.errorCode) {
	        	alert("Ha ocurrido un error inesperado: "+response.errorText);
	        } else {
	        	document.querySelector("tr[data-id='"+response.id+"']").parentNode.removeChild(document.querySelector("tr[data-id='"+response.id+"']"));
	        	if (document.querySelectorAll("tr").length == 0) {
	        		document.querySelector("#nocontestants").hidden = false;
	        		document.querySelector("#contestants").hidden = true;
	        	}
	        	reinit();
	        }
	    }
	}
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(params);
}

function done() {
	if (document.querySelector("input").value == "") {
		parent.hidemodal();
	} else {
		var people = document.querySelector("input").value;
		var http = new XMLHttpRequest();
		var url = "../ajax/invite.php";
		var params = "contest="+contest+"&people="+people;
		http.open("POST", url, true);
		http.onload = function() {
		    if(this.status == 200) {
		        var response = JSON.parse(this.responseText);
		        if (response.errorCode) {
		        	alert("Ha ocurrido un error inesperado: "+response.errorText);
		        } else {
		        	var people_array = people.split(",");
		        	if (document.querySelectorAll("tr").length == 0) {
		        		document.querySelector("#nocontestants").hidden = true;
		        		document.querySelector("#contestants").hidden = false;
		        	}
		        	for (var i = 0; i < people_array.length; i++) {
		        		document.querySelector("table").insertAdjacentHTML("beforeend", '<tr data-id="'+people_array[i]+'"><td>'+document.querySelector("#invite_textbox").selectize.getItem(people_array[i]).children(".username")[0].innerText+'</td><td><span class="icon svg-ic_remove_circle_24px"></span></td></tr>');
		        		document.querySelector("table tr[data-id='"+people_array[i]+"'] .icon").addEventListener("click", remove);
		        	}
		        	reinit();
		        }
		    }
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(params);
	}
}

function reinit() {
	invited = [];
	var table = document.querySelectorAll("table tr");
	for (var i = 0; i < table.length; i++) {
		invited[i] = table[i].getAttribute("data-id");
	}
	document.querySelector("#invite_textbox").selectize.clear();
	document.querySelector("#invite_textbox").selectize.clearOptions();
	document.querySelector("#invite_textbox").selectize.clearCache();
}

function init() {
	var table = document.querySelectorAll("table tr");
	for (var i = 0; i < table.length; i++) {
		invited[i] = table[i].getAttribute("data-id");
		table[i].querySelector(".icon").addEventListener("click", remove);
	}
	selectize = $("#invite_textbox").selectize({
		delimiter: ',',
		valueField: 'id',
		labelField: 'username',
	    searchField: ['username', 'name', 'surname', 'email'],
	    create: false,
	    render: {
	    	item: function(item, escape) {
	    		return '<div><span class="username">'+escape(item.username)+'</span><span class="name">'+escape(item.name+' '+item.surname)+'</span></div>';
	    	},
	        option: function(item, escape) {
	        	return '<div><span class="label">'+escape(item.username)+'</span><span class="caption">'+escape(item.name+' '+item.surname)+'</span></div>';
	        }
	    },
	    load: function(query, callback) {
	        if (!query.length) return callback();
	        $.ajax({
	            url: '../ajax/people.php?q='+encodeURIComponent(query)+'&exclude='+encodeURIComponent(JSON.stringify(invited)),
	            type: 'GET',
	            error: function() {
	                callback();
	            },
	            success: function(res) {
	            	callback(JSON.parse(res));
	            }
	        });
	    }
	});
	selectize.on('change', updated);
	document.querySelector("#done").addEventListener('click', done);
}

window.addEventListener('load', init);