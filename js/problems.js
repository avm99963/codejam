var input_set = 2;

window.onload = function() {
	document.getElementById("addinputset").onclick = function() {
		event.preventDefault();
		document.getElementById("largeinputs").insertAdjacentHTML('beforeend', '<div id="largeinput'+input_set+'"><h4 style="margin-bottom: 0;">Large input '+input_set+':</h4><div class="padding10"><p style="margin-top: 5px;"><label for="pts_input'+input_set+'">Puntos</label>: <input type="number" name="pts_input'+input_set+'" id="pts_input'+input_set+'" required="required" min="0"><br><label for="pts_input'+input_set+'">Input (<i>.in</i>)</label>: <input type="file" name="in_input'+input_set+'" id="in_input'+input_set+'" accept=".in" required="required"><br><label for="pts_input'+input_set+'">Output (<i>.out</i>)</label>: <input type="file" name="out_input'+input_set+'" id="out_input'+input_set+'" accept=".out" required="required"></p></div></div>');
		input_set++;
		console.log("I love pizza ;-)");
	}
}