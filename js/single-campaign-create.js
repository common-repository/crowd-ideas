function validcheckfields(){	var field_title = document.getElementById("title").value.trim();	if(field_title == '') {alert('Fill the title'); document.getElementById("title").focus();return false;}	var field_description = document.getElementById("description").value.trim();	if(field_description == '') {alert('Fill the description'); document.getElementById("description").focus();return false;}	var startDate = document.getElementById("datepicker").value;	var endDate = document.getElementById("datepicker1").value;	//alert(startDate);	//alert(endDate);		if(startDate == '') {alert('Fill the start date'); document.getElementById("datepicker").focus();return false;}	if(endDate == '') {alert('Fill the end date'); document.getElementById("datepicker1").focus();return false;}		if (endDate < startDate) {		alert("End date must be greater than or equal to start date");	}								/*var regExp = /(\d{1,2})\/(\d{1,2})\/(\d{2,4})/;	if(parseInt(endDate.replace(regExp, "$3$2$1")) < parseInt(startDate.replace(regExp, "$3$2$1"))){		alert("End date must be greater than or equal to start date");		document.getElementById("datepicker1").focus();		return false;	}	*/	else {document.getElementById('formID').submit(); }			}