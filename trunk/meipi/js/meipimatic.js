
		function submitMeipimatic()
		{
			var geocoder = false;
			var geocoder2 = false;
			if(!geocoder)
				geocoder = new GClientGeocoder();
			if(!geocoder2)
				geocoder2 = new GClientGeocoder();
			geocoder.getLatLng(
				document.forms.meipimaticForm.centerAddress.value+", "+document.forms.meipimaticForm.city.value,
				function(point) {
					if (!point) {
						document.forms.meipimaticForm.validAddress1.value = "false";
					}

					geocoder2.getLatLng(
						document.forms.meipimaticForm.city.value,
						function(point2) {
							if (!point2) {
								document.forms.meipimaticForm.validAddress2.value = "false";
							}
						document.forms.meipimaticForm.submit();
					});

			});
		}

		function showPreviewMap()
		{
			var geocoder = false;
			var geocoder2 = false;
			var geoOk1 = true;
			var geoOk2 = true;
			if(!geocoder)
				geocoder = new GClientGeocoder();
			if(!geocoder2)
				geocoder2 = new GClientGeocoder();
			geocoder.getLatLng(
				document.forms.meipimaticForm.centerAddress.value+", "+document.forms.meipimaticForm.city.value,
				function(point) {
					if (!point) {
						geoOk1 = false;
					}
					geocoder2.getLatLng(
						document.forms.meipimaticForm.city.value,
						function(point2) {
							if (!point2) {
								geoOk2 = false;
							}
							if(geoOk1&&geoOk2)
							{
								// Open mapPreview.php if no errors found in address
								addr1 = document.forms.meipimaticForm.centerAddress.value;
								addr2 = document.forms.meipimaticForm.city.value;
								viewT = document.forms.meipimaticForm.viewType.value;
								viewB = document.forms.meipimaticForm.viewButtons.value;
								zoom = document.forms.meipimaticForm.zoomLevel.value;
								window.open("mapPreview.php?addr1="+addr1+"&addr2="+addr2+"&viewT="+viewT+"&viewB="+viewB+"&zoom="+zoom);
								document.getElementById("param_centerAddress").className='fo-type';
								document.getElementById("param_city").className='fo-type';
								document.getElementById("mapPreviewErrors").innerHTML="";
								document.getElementById("mapPreviewErrors").style.display='none';
							}
							else
							{
								// Highlight the correct params
								if(!geoOk2)
								{
									document.getElementById("param_centerAddress").className='fo-type';
									document.getElementById("param_city").className='fo-error';
								}
								else
								{
									document.getElementById("param_centerAddress").className='fo-error';
									document.getElementById("param_city").className='fo-type';
								}
								document.getElementById("mapPreviewErrors").innerHTML="<div class=\"fo-linea error\">Error en la direción especificada como centro del mapa</div>";
								document.getElementById("mapPreviewErrors").style.display='';
							}
					});

			});
		}
