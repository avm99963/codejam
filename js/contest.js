function $(a) {
	return document.querySelector(a);
}

function $all(a) {
	return document.querySelectorAll(a);
}

function $$(a, b) {
	return a.querySelector(b);
}

var dontstart = false, toasttimeout, counters = {}, toast = {
	create: function(text, time) {
		toast.deleteAll();
		document.body.insertAdjacentHTML('afterBegin', '<div class="toastcontainer"><div class="toast">'+text+'</div></div>');
		if (time > 0) {
			toasttimeout = window.setTimeout(function() {
				toast.timeout = $(".toastcontainer").parentNode.removeChild($(".toastcontainer"));
			}, time);
		}
	},
	deleteAll: function() {
		var toastcontainers = $all(".toastcontainer");
		for (var i = 0; i < toastcontainers.length; i++) {
			toastcontainers[i].parentNode.removeChild(toastcontainers[i]);
		}
		window.clearTimeout(toasttimeout);
	}
};

function updatestats() {
	var http = new XMLHttpRequest();
	var url = "ajax/conteststats.php";
	var params = "contest="+contest;
	http.open("POST", url, true);
	http.onload = function() {
	    if(this.status == 200) {
	        var response = JSON.parse(this.responseText);
	        if (response.errorCode) {
	        	toast.create("No se ha podido refrescar la página porque ha ocurrido un error inesperado:<br>"+response.errorText, 10000);
	        } else {
	        	$("#rank").innerText = response.rank;
	        	$("#score").innerText = response.score;
	        	$("#topscores tbody").innerHTML = "";
	        	for (var i = 0; i < response.topscores.length; i++) {
	        		$("#topscores tbody").insertAdjacentHTML("beforeend", "<tr><td>"+response.topscores[i].contestant+"</td><td>"+response.topscores[i].score+"</td></tr>");
	        	}
	        	for (var i in response.submissions) {
	        		var submission = response.submissions[i];
	        		if (submission.small.status == "correct") {
	        			var small = '<span class="correct">Correct</span>';
	        			if (submission.small.count > 1) {
	        				small += "<br>("+(submission.small.count - 1)+' incorrect attempt'+(((submission.small.count - 1) == 1) ? '' : 's')+")";
	        			}
	        		} else if (submission.small.status == "notattempted") {
	        			var small = 'Not attempted';
	        		} else if (submission.small.status == "incorrect") {
	        			var small = submission.small.count+' incorrect attempt'+((submission.small.count == 1) ? '' : 's');
	        		}

	        		if (submission.small.manuallyjudged === true) {
	        			small += '<span class="mj" title="Manually Judged">(MJ)</span>';
	        		}

	        		if (submission.large.status == "correct") {
	        			var large = '<span class="correct">Correct</span>';
	        		} else if (submission.large.status == "incorrect") {
	        			var large = 'Incorrect';
	        		} else if (submission.large.status == "submitted") {
	        			var large = 'Submitted';
	        		} else if (submission.large.status == "notattempted") {
	        			var large = 'Not attempted';
	        		} else if (submission.large.status == "timeexpired") {
	        			var large = 'Time expired';
	        		}

	        		if (submission.large.manuallyjudged === true) {
	        			large += '<span class="mj" title="Manually Judged">(MJ)</span>';
	        		}

	        		$(".submission[data-problem-id='"+i+"'] .small_submission").innerHTML = small;
	        		$(".submission[data-problem-id='"+i+"'] .large_submission").innerHTML = large;

	        		if (competitionhasendedyey !== true) {
	        			if (submission.small.status != "notattempted") {
	        				$(".solve_msg[data-problem-id='"+i+"'][data-type='small']").innerHTML = small;
	        			}
	        			if (submission.large.status != "notattempted") {
	        				$(".solve_msg[data-problem-id='"+i+"'][data-type='large']").innerHTML = large;
	        			}
	        			if (submission.small.count == 3 || submission.small.status == "correct") {
		        			$(".solve_btn[data-problem-id='"+i+"'][data-type='small']").hidden = true;
		        		}
		        		if (submission.large.status == "submitted" || submission.large.status == "timeexpired") {
		        			$(".solve_btn[data-problem-id='"+i+"'][data-type='large']").hidden = true;
		        		}
	        		}
	        	}
	        }
	    }
	}
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(params);
}

function switchproblem() {
	var active = $all("nav li.active");
	for (var i = 0; i < active.length; i++) {
		active[i].className = "";
	}
	var problemsactive = $all(".problem:not([hidden])");
	for (var i = 0; i < problemsactive.length; i++) {
		problemsactive[i].hidden = true;
	}
	$(".problem[data-problem-id='"+this.getAttribute("data-problem-id")+"']").hidden = false;
	this.className = "active";
}

function solve() {
	if (competitionhasendedyey === true) {
		this.hidden = true;
		$(".solve_container[data-problem-id='"+this.getAttribute("data-problem-id")+"'][data-type='"+this.getAttribute("data-type")+"']").hidden = false;
	} else {
		var confirmed = confirm("Esto va a hacer que empieze un contador de "+((this.getAttribute("data-type") == "large") ? "8" : "4")+" minutos, después del cual no podrás enviar ninguna solución al problema. ¿Estás seguro de querer resolver el problema?");
		if (confirmed === true) {
			var problemId = this.getAttribute("data-problem-id"),
				type = this.getAttribute("data-type");
			var http = new XMLHttpRequest();
			var url = "ajax/startcounter.php";
			var params = "problem="+problemId+"&type="+type;
			http.open("POST", url, true);
			http.onload = function() {
				toast.deleteAll();
				if(this.status == 200) {
					var response = JSON.parse(this.responseText);
			        if (response.errorCode) {
			        	toast.create("No se ha podido empezar el contador: "+response.errorText, 10000);
			        	$(".solve_btn[data-problem-id='"+problemId+"'][data-type='"+type+"']").disabled = false;
			        } else {
			        	$(".solve_container[data-problem-id='"+response.problem+"'][data-type='"+response.type+"'] .file_download").innerHTML = '<img src="img/file.gif"> <a href="'+response.inputurl+'" data-problem-id="'+response.problem+'" data-type="'+response.type+'">Download '+response.inputfilename+'.in</a>';
			        	$(".solve_container[data-problem-id='"+response.problem+"'][data-type='"+response.type+"'] .time").innerText = ((response.type == "large") ? "08" : "04")+":00";
			        	$(".solve_btn[data-problem-id='"+response.problem+"'][data-type='"+response.type+"']").hidden = true;
						$(".solve_container[data-problem-id='"+response.problem+"'][data-type='"+response.type+"']").hidden = false;
						counters[response.problem+"-"+response.type] = {
							problemId: response.problem,
							type: response.type,
							endtime: response.endtime,
							ntry: response.ntry
						};
						$(".solve_btn[data-problem-id='"+response.problem+"'][data-type='"+response.type+"']").disabled = false;
						$("a[data-problem-id='"+response.problem+"'][data-type='"+response.type+"']").click();
			        }
				} else {
					toast.create("No se ha podido contactar con el servidor correctamente. Por favor, vuelve a probar.", 10000);
				}
			}
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(params);
			$(".solve_btn[data-problem-id='"+problemId+"'][data-type='"+type+"']").disabled = true;
			toast.create("Cargando...");
		}
	}
}

function hide() {
	this.parentNode.parentNode.hidden = true;
	$(".solve_btn[data-problem-id='"+this.parentNode.parentNode.getAttribute("data-problem-id")+"'][data-type='"+this.parentNode.parentNode.getAttribute("data-type")+"']").hidden = false;
}

function submit() {
	var problemId = this.parentNode.parentNode.getAttribute("data-problem-id"),
		type = this.parentNode.parentNode.getAttribute("data-type"),
		output = $(".solve_container[data-problem-id='"+problemId+"'][data-type='"+type+"'] .output").files[0];
	if (competitionhasendedyey !== true) {
		var sourcecode = $(".solve_container[data-problem-id='"+problemId+"'][data-type='"+type+"'] .sourcecode").files[0];
		if (!sourcecode) {
			alert("No se ha seleccionado el archivo de código fuente");
			return false;
		}
		if (sourcecode.size >= 200*1024) {
			alert("El código fuente debe pesar menos de 200kB");
			return false;
		}
	}
	if (output) {
		if (output.size >= 200*1024) {
			alert("El archivo de salida debe pesar menos de 200kB");
			return false;
		}
		var fd = new FormData();
		fd.append("output", output);
		fd.append("contest", contest);
		fd.append("problem", problemId);
		fd.append("type", type);
		fd.append("competitionhasendedyey", ((competitionhasendedyey === true) ? 1 : 0));

		if (competitionhasendedyey !== true) {
			fd.append("sourcecode", sourcecode);
		}

		var http = new XMLHttpRequest();
		var url = "ajax/submitsolution.php";
		http.open("POST", url, true);
		http.onload = function() {
			toast.deleteAll();
		    if(this.status == 200) {
		        var response = JSON.parse(this.responseText);
		        if (response.errorCode) {
		        	toast.create("No se ha juzgado la solución: "+response.errorText, 10000);
		        } else {
		        	if (type == "large" && competitionhasendedyey !== true) {
		        		toast.create("Solución enviada. Se juzgará cuando acabe la competición", 30000);
		        	} else {
		        		toast.create("Solución juzgada: "+((response.judged == 1) ? "<span style='color: green;'>Correcta</span>" : "<span style='color: red;'>Incorrecta</span>"), 30000);
		        	}
		        	if (competitionhasendedyey === true) {
		        		$(".solve_msg[data-problem-id='"+problemId+"'][data-type='"+type+"']").innerText = "Última solución juzgada: "+((response.judged == 1) ? "Correcta" : "Incorrecta");
		        	} else {
		        		updatestats();
		        	}
		        	$(".solve_container[data-problem-id='"+problemId+"'][data-type='"+type+"']").hidden = true;
		        	$(".solve_container[data-problem-id='"+problemId+"'][data-type='"+type+"'] input.output").value = "";
		        	if (competitionhasendedyey !== true) {
		        		$(".solve_container[data-problem-id='"+problemId+"'][data-type='"+type+"'] input.sourcecode").value = "";
		        	}
					$(".solve_btn[data-problem-id='"+problemId+"'][data-type='"+type+"']").hidden = false;
					$(".solve_container[data-problem-id='"+problemId+"'][data-type='"+type+"'] .submit").disabled = false;
					delete counters[problemId+"-"+type];
					if (competitionhasendedyey === true) {
						$(".solve_btn[data-problem-id='"+problemId+"'][data-type='"+type+"']").hidden = false;
					}
		        }
		        $(".solve_container[data-problem-id='"+problemId+"'][data-type='"+type+"'] .submit").disabled = false;
		    } else {
		    	toast.create("No se ha podido contactar con el servidor correctamente. Por favor, vuelve a probar.", 10000);
		    }
		}
		http.send(fd);
		$(".solve_container[data-problem-id='"+problemId+"'][data-type='"+type+"'] .submit").disabled = true;
		toast.create("Cargando...");
	} else {
		alert("No se ha seleccionado el archivo de salida");
	}
}

function secondstotime(seconds) {
    var sec_num = parseInt(seconds, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    var time    = hours+':'+minutes+':'+seconds;
    return time;
}

function secondstominutes(seconds) {
    var sec_num = parseInt(seconds, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    var time    = minutes+':'+seconds;
    return time;
}

function doTimer() {
    function instance() {
        if(new Date().getTime() >= (contestends * 1000)) { // If time passed
        	for (var counter in counters) {
        		$(".solve_container[data-problem-id='"+counters[counter].problemId+"'][data-type='"+counters[counter].type+"']").hidden = true;
        		delete counters[counter];
        	}
        	window.setTimeout(updatestats, Math.floor((Math.random() * 5000) + 1200));
        	$("#time").innerText = "La competición ha terminado";
        } else {
        	var time2 = contestends - (Math.round(new Date().getTime()/1000.0));
        	var time = secondstotime(time2);
        	$("#time").innerText = time;
        	for (var counter in counters) {
        		var timecounter = counters[counter].endtime - Math.round(new Date().getTime()/1000.0);
        		if (timecounter <= 0) {
        			$(".solve_container[data-problem-id='"+counters[counter].problemId+"'][data-type='"+counters[counter].type+"']").hidden = true;
        			if (counters[counter].type == "small" && counters[counter].ntry != 3) {
        				$(".solve_btn[data-problem-id='"+counters[counter].problemId+"'][data-type='"+counters[counter].type+"']").hidden = false;
        			}
        			delete counters[counter];
        			window.setTimeout(updatestats, 1200);
        		} else {
        			if (timecounter > time2) {
        				timecounter = time2;
        			}
        			$(".solve_container[data-problem-id='"+counters[counter].problemId+"'][data-type='"+counters[counter].type+"'] .time").innerText = secondstominutes(timecounter);
        		}
        	}
        	if (time.split(":")[2] == "00") {
        		updatestats();
        	}
            var diff = (new Date().getTime()) % 1000;
            window.setTimeout(instance, (1000 - diff));
        }
    }
    window.setTimeout(instance, 1000);
}

function init() {
	// Navbar
	var problems = $all("nav li");
	for (var i = 0; i < problems.length; i++) {
		problems[i].addEventListener('click', switchproblem);
	}

	var buttons = $all(".solve_btn");
	for (var i = 0; i< buttons.length; i++) {
		buttons[i].addEventListener('click', solve);
	}

	var hide_buttons = $all(".hide");
	for (var i = 0; i< hide_buttons.length; i++) {
		hide_buttons[i].addEventListener('click', hide);
	}

	var submit_buttons = $all(".submit");
	for (var i = 0; i< submit_buttons.length; i++) {
		submit_buttons[i].addEventListener('click', submit);
	}

	updatestats();
}

window.addEventListener('load', init);