var remove_w_alert;
var remove_w_alert_2;

function w_alert(parametros)
{
	$("div").remove(".alert");
	window.clearTimeout(remove_w_alert);
	window.clearTimeout(remove_w_alert_2);
	var newDiv = document.createElement("div");
	var newContent = document.createTextNode(parametros.titulo);
	//newDiv.appendChild(newContent);
	newDiv.innerHTML = parametros.titulo;
	newDiv.classList.add("alert");
	newDiv.classList.add("alert-"+parametros.tipo);
	newDiv.classList.add("fade");
	newDiv.classList.add("mb-0");
	newDiv.classList.add("text-center");
	newDiv.setAttribute("id", "id_alert");
	newDiv.setAttribute("role", "alert");

	var currentDiv = document.getElementById("w_alert");
	currentDiv.appendChild(newDiv);

	div_alert = document.getElementById("id_alert");
	setTimeout(function(){ div_alert.classList.add("show"); },200);
	if (typeof parametros.tiempo == 'undefined')
		parametros.tiempo = 5000;
	remove_w_alert = setTimeout(function(){ div_alert.classList.remove("show"); },parametros.tiempo);
	remove_w_alert_2 = window.setTimeout(function(){ div_alert.parentNode.removeChild(div_alert); },parametros.tiempo + 100);
}