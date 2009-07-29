// mouse wheel zoom code:
//wheelZoom: function(e) {
function wheelZoomNewEntry(a)
{
	wheelZoom(a, nMap);
}

function wheelZoomMap(a)
{
	wheelZoom(a, map);
}

function KeyPress(event)
{
	var key = 0;
	if(event.which)
		key = event.which;
	else
		key = event.keyCode;
	
	//alert(key);
	if (key == 13)
	{
		if(event.which)
		{
			event.stopPropagation();
		}
		else
			event.keyCode = 0;
	}
}

function wheelZoom(a, mapZoom)
{
	if (a.detail) // Firefox
	{
		if (a.detail < 0)
		{ mapZoom.zoomIn(); }
		else if (a.detail > 0)
		{ mapZoom.zoomOut(); }
	}
	else if (a.wheelDelta) // IE
	{
		if (a.wheelDelta > 0)
		{ mapZoom.zoomIn(); }
		else if (a.wheelDelta < 0)
		{ mapZoom.zoomOut(); }
	}
	try {
		a.preventDefault();
	}
	catch(e) {}
}

function switchAdvancedSearchMap()
{
	advancedSearchDiv = document.getElementById("avanzamarco");
	if(advancedSearchDiv.style.display=='none')
	{
		advancedSearchDiv.style.display='';
	}
	else
	{
		advancedSearchDiv.style.display='none';
	}

	hideMessage();
}

function showNewEntryForm(id_entry,title,description,url,category,address,longitude,latitude,tags,extraParams,type,content)
{
	newEntryDiv = document.getElementById("newEntry");
	mapDiv = document.getElementById("newEntryMap");

	if(id_entry!=null && id_entry!="")
	{
		changeMarker="yes";
	}
	else
	{
		changeMarker="no";
	}

	if(document.forms.newEntryForm.edition.value!="no" && (!id_entry || id_entry==null || id_entry==""))
	{
		document.forms.newEntryForm.title.value = '';
		document.forms.newEntryForm.description.value = '';
		document.forms.newEntryForm.url.value = 'http://';
		document.forms.newEntryForm.category.value = '';
		document.forms.newEntryForm.address.value = '';
		document.forms.newEntryForm.tags.value = '';

		document.forms.newEntryForm.longitude.value = '';
		document.forms.newEntryForm.latitude.value = '';
		document.newEntryForm.filetype.disabled=false;

		document.getElementById("filetype_div").style.display='';
		document.getElementById("keep_file_div").style.display='none';
		document.forms.newEntryForm.edition.value = "no";
		changeMarker="yes";

		document.forms.newEntryForm.no_location.checked = false;

		// TODO: Change when number of categories can be more than 4
		for (var i = 0; i <= 4; i++) {
			document.getElementById("categoryDesc_"+i).style.display='none';
		}
		document.getElementById("categoryDesc_0").style.display='';

	}
	if(newEntryDiv.style.display=='none')
	{
		newEntryDiv.style.display='';
		mapDiv.style.display='';
		document.forms.newEntryForm.title.focus();
		loadNewMap(longitude,latitude,changeMarker);
	}
	else
	{
		if(changeMarker=="yes")
		{
			loadNewMap(longitude,latitude,changeMarker);
		}
	}

	if(id_entry!=null && id_entry!="")
	{
		document.forms.newEntryForm.title.value=title;
		document.forms.newEntryForm.description.value=toTextarea(description);
		if(url.length==0)
			document.forms.newEntryForm.url.value="http://";
		else
			document.forms.newEntryForm.url.value=url;
		document.forms.newEntryForm.category.value=category;
		document.forms.newEntryForm.address.value=address;
		document.forms.newEntryForm.tags.value = tags;

		document.forms.newEntryForm.longitude.value = longitude;
		document.forms.newEntryForm.latitude.value = latitude;

		// Warning: Change when number of categories can be more than 4
		for (var i = 0; i <= 4; i++) {
			document.getElementById("categoryDesc_"+i).style.display='none';
		}
		document.getElementById("categoryDesc_"+category).style.display='';

		if(longitude=='-10000' && latitude=='-10000')
		{
			document.forms.newEntryForm.no_location.checked = true;
		}

		document.forms.newEntryForm.edition.value = id_entry;

		if(extraParams!=null)
		{
			for(var i=0; i<extraParams.length; i++)
			{
				try
				{
					setInputValue(document.forms.newEntryForm[extraParams[i][0]], extraParams[i][1]);
				}
				catch(e)
				{
				}
			}
		}

		if(content)
		{
			document.newEntryForm.filetype.disabled=true;
			document.getElementById("filetype_div").style.display='none';
			document.getElementById("keep_file_div").style.display='';
		}
		else
		{
			document.newEntryForm.filetype.disabled=false;
			document.getElementById("filetype_div").style.display='';
			document.getElementById("keep_file_div").style.display='none';
		}

		document.forms.newEntryForm.title.focus();
	}

	var descriptionEd = tinyMCE.get('description');
	try
	{
		if(description)
			descriptionEd.setContent(toTextarea(description));
		else
			descriptionEd.setContent("");
	}
	catch(e) {}

	hideMessage();
}

function setInputValue(element, value)
{
	switch(element.type)
	{
		case "select-one":
			for(var el=0; el<element.options.length; el++)
			{
				if(element.options[el].value==value)
				{
					element.selectedIndex = el;
					return;
				}
			}
			break;
		default:
			element.value = value;
			return;
	}
}

function showLoginForm()
{
	showLoginFormParams("");
}

function showLoginFormParams(params)
{
	var loginDiv = document.getElementById("loginWindow");
	var loginContentDiv = document.getElementById("logconten");
	var pwdContentDiv = document.getElementById("pwdconten");
	loginDiv.style.display='';
	loginContentDiv.style.display='';
	pwdContentDiv.style.display='none';
	document.forms.login.login.focus();
	if(params!=null && params!="")
	{
		if((""+document.forms.login.next.value).indexOf("?")<0)
		{
			document.forms.login.next.value += "?"+params;
			document.forms.registration.next.value += "?"+params;
		}
		else
		{
			var nextValue = document.forms.login.next.value;
			var aParams = params.split("&");
			for(var i=0; i<aParams.length; i++)
			{
				var aParam = aParams[i].split("=");
				if(nextValue.indexOf(aParam[0]+"=")>-1)
				{
					nextValue = nextValue.replace(new RegExp("&?"+aParam[0]+"=[^&]*"), "");
				}
			}

			nextValue = nextValue+"&"+params;
			nextValue = nextValue.replace("\\?&", "\?");

			document.forms.login.next.value = nextValue;
			document.forms.registration.next.value = nextValue;
		}
	}

	goToTop();
	hideMessage();
}

function showPasswordRecoveryForm()
{
	var loginDiv = document.getElementById("loginWindow");
	var loginContentDiv = document.getElementById("logconten");
	var pwdContentDiv = document.getElementById("pwdconten");
	loginDiv.style.display='';
	loginContentDiv.style.display='none';
	pwdContentDiv.style.display='';
}

function sendPasswordRecoveryCode()
{
	if(document.forms.sendResetPasswordCode.login.value=="")
	{
		document.forms.sendResetPasswordCode.login.focus();
	}
	else
	{
		var params = "?login="+escape(document.forms.sendResetPasswordCode.login.value);
//		showMessage("Enviando mensaje de reseteo de password para usuario "+document.forms.sendResetPasswordCode.login.value);
		GDownloadUrl(getString("commonFiles")+"actions/sendPasswordCode.php"+params, function(data, responseCode) {
			var xml = GXml.parse(data);
			var descriptions = xml.documentElement.getElementsByTagName("description");
			var result = "";
			for (var i = 0; i < descriptions.length; i++)
			{
				result += GXml.value(descriptions[i])+"<br/>";
			}
			showMessage(result);
		});
		document.forms.resetPassword.reset_password_login.value = document.forms.sendResetPasswordCode.login.value;
	}
}

var newEntryMarker = null;
var nMap = null;
function loadNewMap(longitude,latitude,changeMarker)
{
	if (GBrowserIsCompatible())
	{
		if(nMap!=null)
		{
			if(changeMarker=="yes")
			{
				if(newEntryMarker!=null)
				{
					nMap.removeOverlay(newEntryMarker);
				}
				newEntryMarker=null;
				if(longitude!=null && longitude!="" && latitude!=null && latitude!="" && latitude!='-10000' && longitude!='-10000')
				{
					point2 = new GLatLng(parseFloat(latitude),parseFloat(longitude));
					newEntryMarker = new GMarker(point2, {draggable: true});
					GEvent.addListener(newEntryMarker, "dragend", function() {
						document.forms.newEntryForm.longitude.value = newEntryMarker.getPoint().lng();
						document.forms.newEntryForm.latitude.value = newEntryMarker.getPoint().lat();
					});
					nMap.setCenter(new GLatLng(parseFloat(latitude),parseFloat(longitude)), 15);
					nMap.addOverlay(newEntryMarker);
				}
			}
			return;
		}
	 	nMap = new GMap2(document.getElementById("newEntryMap"));
		//nMap.setCenter(new GLatLng(37.4419, -122.1419), 13);
		//nMap.addControl(new GSmallMapControl());
		nMap.addControl(new GLargeMapControl());
		nMap.addControl(new GMapTypeControl({titleSize: 1}));

		GEvent.addListener(nMap, "click", function(marker, point) {
				if (!marker)
				{
					if(newEntryMarker==null)
					{
						newEntryMarker = new GMarker(point, {draggable: true});
						GEvent.addListener(newEntryMarker, "dragend", function() {
								document.forms.newEntryForm.longitude.value = newEntryMarker.getPoint().lng();
								document.forms.newEntryForm.latitude.value = newEntryMarker.getPoint().lat();
							});
						document.forms.newEntryForm.longitude.value = newEntryMarker.getPoint().lng();
						document.forms.newEntryForm.latitude.value = newEntryMarker.getPoint().lat();
						nMap.addOverlay(newEntryMarker);
					}
					else
					{
						newEntryMarker.setPoint(point);
						document.forms.newEntryForm.longitude.value = newEntryMarker.getPoint().lng();
						document.forms.newEntryForm.latitude.value = newEntryMarker.getPoint().lat();
					}
					document.forms.newEntryForm.no_location.checked = false;
				}
			});

		GEvent.addListener(nMap, "moveend", function() {
				var center = nMap.getCenter();
				//document.getElementById("message").innerHTML = center.toString();
				//alert(center.toString());
				//loadData();
			});

		nMap.enableContinuousZoom();
		nMap.enableDoubleClickZoom();
		GEvent.addDomListener(document.getElementById("newEntryMap"), "DOMMouseScroll", wheelZoomNewEntry); // Firefox
		GEvent.addDomListener(document.getElementById("newEntryMap"), "mousewheel",     wheelZoomNewEntry); // IE

		geocoder = new GClientGeocoder();
		//nMap.setCenter(new GLatLng(37.4419, -122.1419), 13);

		if(longitude!=null && longitude!="" && latitude!=null && latitude!="" && latitude!='-10000' && longitude!='-10000')
		{
			point2 = new GLatLng(parseFloat(latitude),parseFloat(longitude));
			newEntryMarker = new GMarker(point2, {draggable: true});
			GEvent.addListener(newEntryMarker, "dragend", function() {
				document.forms.newEntryForm.longitude.value = newEntryMarker.getPoint().lng();
				document.forms.newEntryForm.latitude.value = newEntryMarker.getPoint().lat();
			});
			nMap.setCenter(new GLatLng(parseFloat(latitude),parseFloat(longitude)), 15);
			nMap.addOverlay(newEntryMarker);
		}
		else
		{
			try
			{
				if(typeof map != 'undefined')
				{
					nMap.setCenter(map.getCenter(), 15);
				}
				else
				{
					if("centerAddress"==getString("centerAddress"))
					{
						showAddressNewMap(getString("City"));
					}
					else
					{
						showAddressNewMap(getString("centerAddress")+", "+getString("City"));
					}
				}
			}
			catch(e)
			{
				if("centerAddress"==getString("centerAddress"))
				{
					showAddressNewMap(getString("City"));
				}
				else
				{
					showAddressNewMap(getString("centerAddress")+", "+getString("City"));
				}
			}
		}
	}
}

function removeNewEntryMarker()
{
	if(newEntryMarker!=null)
	{
		newEntryMarker.remove();
		document.forms.newEntryForm.longitude.value = "";
		document.forms.newEntryForm.latitude.value = "";
		newEntryMarker = null;
	}
}

function showAddressNewMap(address)
{
	if (geocoder)
	{
		geocoder.getLatLng(
			address,
			function(point) {
				if (!point)
				{
					showMessage(address + " not found");
				}
				else
				{
					nMap.setCenter(point, 15);
					//var marker = new GMarker(point);
					//nMap.addOverlay(marker);
					//marker.openInfoWindowHtml(address);
				}
			});
	}
}

function createGMarker(id, lat, lng, text)
{
	var point = new GLatLng(parseFloat(lat), parseFloat(lng));
	var marker = new GMarker(point);
	GEvent.addListener(marker, "click", function() {
			marker.openInfoWindowHtml(text);
		});
	return marker;
}

function goTo()
{
	address = document.forms.newEntryForm.address.value;
	goToAddress(address, true);
}
			
function goToAddress(address, add)
{
	if (geocoder)
	{
		var fullAddress = address;
		if(add)
		{
			fullAddress += ", "+getString("City");
		}

		geocoder.getLatLng(
			fullAddress,
			function(point) {
					if (!point) {
						if(add)
						{
							//goToAddress(address+", "+getString("City"), false);
							goToAddress(address, false);
						}
						else
						{
							showMessage("Address not found");
						}
					}
					else
					{
						if(newEntryMarker==null)
						{
							newEntryMarker = new GMarker(point, {draggable: true});
							GEvent.addListener(newEntryMarker, "dragend", function() {
									document.forms.newEntryForm.longitude.value = newEntryMarker.getPoint().lng();
									document.forms.newEntryForm.latitude.value = newEntryMarker.getPoint().lat();
								});
							document.forms.newEntryForm.longitude.value = newEntryMarker.getPoint().lng();
							document.forms.newEntryForm.latitude.value = newEntryMarker.getPoint().lat();
						  nMap.addOverlay(newEntryMarker);
						}
						else
						{
							newEntryMarker.setPoint(point);
							document.forms.newEntryForm.longitude.value = newEntryMarker.getPoint().lng();
							document.forms.newEntryForm.latitude.value = newEntryMarker.getPoint().lat();
						}
						nMap.setCenter(point, 15);
					}
				});
	}
}

function setDivText(idDiv, text)
{
	oDiv = document.getElementById(idDiv);
	if(oDiv!=null)
	{
		oDiv.innerHTML = text;
	}
}

function showEntryData(idMeipi,id,dirThumbnail,dirEntry,userId,isLogged)
{
	setDivText("entryTitle", "<a href=\"javascript:showEntryWindow('"+idMeipi+"', "+id+", '"+dirEntry+"','"+userId+"','"+isLogged+"');\" class=\"entryTitle_cat"+entries[id]["category"]+"\">"+entries[id]["titleOverEntry"]+"</a>");
	setDivText("entryDescription", entries[id]["textOverEntry"]+"<br/>"+"<span class=\"entryDate\">"+entries[id]["date"]+"</span> - <span class=\"entryLogin\"><a href=\""+getString("commonFiles")+"list.php?id_user="+entries[id]["id_user"]+"\">"+entries[id]["login"]+"</a></span>");
	if(entries[id]["file"]!=undefined && entries[id]["file"]!=null && entries[id]["file"]!="" && entries[id]["file"]!="NULL")
	{
		if(entries[id]["type"]=="0")
		{
			setDivText("entryContent", "<a href=\"javascript:showEntryWindow('"+idMeipi+"', "+id+", '"+dirEntry+"','"+userId+"','"+isLogged+"');\" class=\"entryTitle_cat"+entries[id]["category"]+"\"><img src='"+dirThumbnail+entries[id]["file"]+"' /></a>");
			document.getElementById("overEntry").style.width='250px';
		}
		else if (entries[id]["type"]=="1")
		{
			setDivText("entryContent", "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/"+entries[id]["file"]+"\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://www.youtube.com/v/"+entries[id]["file"]+"&autoplay=1\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>");
			document.getElementById("overEntry").style.width='450px';
		}
		else if (entries[id]["type"]=="2")
		{
			setDivText("entryContent", "<embed FlashVars=\"autoPlay=true\" style=\"width:400px; height:326px;\" id=\"VideoPlayback\" type=\"application/x-shockwave-flash\" src=\"http://video.google.com/googleplayer.swf?docId="+entries[id]["file"]+"\" wmode=\"transparent\"></embed>");
			document.getElementById("overEntry").style.width='425px';
		}
		else if (entries[id]["type"]=="3")
		{
			setDivText("entryContent", "<a href=\"javascript:showEntryWindow('"+idMeipi+"', "+id+", '"+dirEntry+"','"+userId+"','"+isLogged+"');\" class=\"entryTitle_cat"+entries[id]["category"]+"\"><img src=\"/images/lively.gif\" /></a>");
			document.getElementById("overEntry").style.width='250px';
		}
	}
	else
	{
		document.getElementById("overEntry").style.width='250px';
		setDivText("entryContent", "");
	}

	document.getElementById("helpInfo").style.display='none';
	document.getElementById("entryTitle").style.display='';
	document.getElementById("entryContent").style.display='';
	document.getElementById("entryDescription").style.display='';
	document.getElementById("entryLatitude").style.display='';
	document.getElementById("entryLongitude").style.display='';
	document.getElementById("overEntry").style.display='';

  entryWindowDiv = document.getElementById("entryWindow");
	if(entryWindowDiv!=null)
	{
		entryWindowDiv.style.display='none';
	}

	hideMessage();
}

function closeEntry()
{
	document.getElementById("overEntry").style.display='none';

	hideMessage();
}

// deprecated
function showHelpInfo()
{
  entryTitleDiv = document.getElementById("entryTitle");
	if(entryTitleDiv!=null)
	{
		entryTitleDiv.style.display='none';
	}
  entryContentDiv = document.getElementById("entryContent");
	if(entryContentDiv!=null)
	{
		entryContentDiv.style.display='none';
	}
  entryDescriptionDiv = document.getElementById("entryDescription");
	if(entryDescriptionDiv!=null)
	{
		entryDescriptionDiv.style.display='none';
	}
  entryLatitudeDiv = document.getElementById("entryLatitude");
	if(entryLatitudeDiv!=null)
	{
		entryLatitudeDiv.style.display='none';
	}
  entryLongitudeDiv = document.getElementById("entryLongitude");
	if(entryLongitudeDiv!=null)
	{
		entryLongitudeDiv.style.display='none';
	}
	document.getElementById("overEntry").style.width='250px';
	document.getElementById("helpInfo").style.display='';
	document.getElementById("overEntry").style.display='';
  entryWindowDiv = document.getElementById("entryWindow");
	if(entryWindowDiv!=null)
	{
		entryWindowDiv.style.display='none';
	}

	hideMessage();
}

function getRadioValue(name)
{
	var tmpels = document.getElementsByName(name);
	for(var i=0;i<tmpels.length;i++)
	{
		if(tmpels[i].checked)
		{
			return tmpels[i].value;
		}
	}
	return "";
}

function submitNewEntry(minLat, maxLat, minLon, maxLon)
{
// ToDo: alert("minLat: "+minLat+"\n"+"maxLat: "+maxLat+"\n"+"minLon: "+minLon+"\n"+"maxLon: "+maxLon+"\n");
	noLocation = document.forms.newEntryForm.no_location.checked;	
	latitude = document.forms.newEntryForm.latitude.value-0;
	longitude = document.forms.newEntryForm.longitude.value-0;
	if(document.forms.newEntryForm.title.value==""
		|| document.forms.newEntryForm.category.options[document.forms.newEntryForm.category.selectedIndex].value==""
		|| (!noLocation &&
			(document.forms.newEntryForm.longitude.value==""
			|| document.forms.newEntryForm.latitude.value==""
			|| (minLat!=null && minLat>latitude)
			|| (maxLat!=null && maxLat<latitude)
			|| (minLon!=null && minLon>longitude)
			|| (maxLon!=null && maxLon<longitude))
			)
		|| (getRadioValue("filetype")=="video" && document.forms.newEntryForm.video.value!="" && document.forms.newEntryForm.videotype.options[document.forms.newEntryForm.videotype.selectedIndex].value=="")
		)
	{
		missing = new Array();
		if(document.forms.newEntryForm.title.value=="")
			missing[missing.length] = getString("Title");
		if(document.forms.newEntryForm.category.options[document.forms.newEntryForm.category.selectedIndex].value=="")
			missing[missing.length] = getString("Category");
		if(document.forms.newEntryForm.longitude.value=="" || document.forms.newEntryForm.latitude.value=="")
			missing[missing.length] = getString("Position");
		if(minLat!=null && minLat>latitude)
			missing[missing.length] = getString("Invalid latitude");
		else if(maxLat!=null && maxLat<latitude)
			missing[missing.length] = getString("Invalid latitude");
		if(minLon!=null && minLon>longitude)
			missing[missing.length] = getString("Invalid longitude");
		else if(maxLon!=null && maxLon<longitude)
			missing[missing.length] = getString("Invalid longitude");
		if(getRadioValue("filetype")=="video" && document.forms.newEntryForm.video.value!="" && document.forms.newEntryForm.videotype.options[document.forms.newEntryForm.videotype.selectedIndex].value=="")
			missing[missing.length] = getString("Wrong video type");
		showMessage(getString("Missing information")+": "+missing.join(", "));
		return false;
	}
	else
	{
		document.forms.newEntryForm.submit();
	}
}

				/*var params = "?title="+escape(document.forms.newEntryForm.title.value);
				params += "&description="+escape(document.forms.newEntryForm.description.value);
				params += "&latitude="+escape(document.forms.newEntryForm.latitude.value);
				params += "&longitude="+escape(document.forms.newEntryForm.longitude.value);
				params += "&category="+escape(document.forms.newEntryForm.category.value);
				//alert("newEntry.php"+escape(params));
				GDownloadUrl("newEntry.php"+params, function(data, responseCode) {
					//alert(responseCode);
					var xml = GXml.parse(data);
					var results = xml.documentElement.getElementsByTagName("result");
alert(results);
					var inserted = true;
					alert(results.item(0));
					alert(results.length);
					for (var i = 0; i < results.length; i++)
					{
					alert(i);
						/*var code = results[i].getAttribute("code");
						if(code!=0)
						{
							alert("qwe"+results[i].getAttribute("description"));
							inserted = false;
						}
						else
							alert("asd"+results[i].getAttribute("description"));
						* /
					}
					if(inserted)
						alert("ha sido insertado"+inserted);
					else
						alert("No ha sido insertado");
				});*/
				
function cancelLogin()
{
	var loginDiv = document.getElementById("loginWindow");
	if(loginDiv.style.display=='')
	{
		loginDiv.style.display = 'none';;
	}

	hideMessage();
}

function cancelSaveMosaic()
{
	mosaicDiv = document.getElementById("mosaicNameWindow");
	if(mosaicDiv.style.display=='')
	{
		mosaicDiv.style.display = 'none';
	}

	hideMessage();
}

function cancel()
{
	newEntryDiv = document.getElementById("newEntry");
	mapDiv = document.getElementById("newEntryMap");
	if(newEntryDiv.style.display=='')
	{
		newEntryDiv.style.display='none';
		mapDiv.style.display='none';
	}

	if(document.getElementById("map")!=null)
	{
		GEvent.addDomListener(document.getElementById("map"), 'DOMMouseScroll', wheelZoomMap);
		GEvent.addDomListener(document.getElementById("map"), 'mousewheel', wheelZoomMap);
	}

	hideMessage();
}

function switchTag(tag)
{
	sTags = document.forms.newEntryForm.tags.value;
	aTags = sTags.split(" ");
	removed=false;
	for(iTag=0; iTag<aTags.length; )
	{
		if(aTags[iTag]==tag)
		{
			aTags.splice(iTag, 1);
			removed=true;
		}
		else if(aTags[iTag]=="")
		{
			aTags.splice(iTag, 1);
		}
		else
		{
			iTag++;
		}
	}
	if(removed)
	{
			sTags = aTags.join(" ");
	}
	else
	{
		if(aTags.length>0)
			sTags = aTags.join(" ")+" "+tag;
		else
			sTags = tag;
	}
	document.forms.newEntryForm.tags.value = sTags;
}

function submitNewComment()
{
	tinyMCE.triggerSave(true,true);
	if(document.forms.comment.subject.value==""
		|| document.forms.comment.id_entry.value==""
		|| document.forms.comment.comment.value=="")
	{
		showMessage(getString("Missing information"));
		return false;
	}
	else
	{
		document.forms.comment.submit();
	}
}

function loginLoad()
{
	if(document.forms.login.login.value==""
		|| document.forms.login.pwd1.value=="")
	{
		document.forms.login.login.focus();
	}
}

var c = true;
function atoHtml(str)
{
//if(c)
	//c = confirm(str);
//if(c)
	//c = confirm(unescape(str));
//return unescape(str);
	//alert(str);
	//str = str.replaceAll("<", "&lt;");
	//str = str.replaceAll(">", "&gt;");
	str = str.replaceAll("\\\\n", "<br/>");
	//str = str.replaceAll("\\n", "<br/>");
	//str = str.replaceAll("\n", "<br/>");
	str = str.replaceAll("\\\\", "\\");
	str = str.replaceAll("\\'", "'");
	str = str.replaceAll('\\"', '"');
	str = str.replaceAll("&lt;br/&gt;", "<br/>");
	return str;
}

function toTextarea(str)
{
	str = str.replaceAll("<br/>", "\n");
	str = unescape(str);
	return str;
}

// Replaces all instances of the given substring.
String.prototype.replaceAll = function(
	strTarget, // The substring you want to replace
	strSubString // The string you want to replace in.
)
{
	var strText = this;
	var intIndexOfMatch = strText.indexOf( strTarget );

	// Keep looping while an instance of the target string
	// still exists in the string.
	while (intIndexOfMatch != -1){
		// Relace out the current instance.
		strText = strText.replace( strTarget, strSubString )

		// Get the index of any next matching substring.
		intIndexOfMatch = strText.indexOf( strTarget );
	}

	// Return the updated string with ALL the target strings
	// replaced out with the new substring.
	return( strText );
}

function vote(idMeipi, idEntry, vote)
{
	params = "?id_entry="+idEntry;
	params += "&vote="+vote;

	GDownloadUrl(getString("commonFiles")+"actions/newVote.php"+params, function(data, responseCode) {
		var xml = GXml.parse(data);
		var results = xml.documentElement.getElementsByTagName("result");
		for (var i = 0; i < results.length; i++)
		{
			if(results[i].getAttribute("code")==1)
			{
				showMessage(getString("Voted"));
				var html = getString("Voted");
				html += ": ";
				var ranking1to5 = (Math.round(vote/2.5)/2)+3;
				for (var rank = 1; rank <= 5; rank++)
				{
				var newVote = 5*(rank-3);
				html += "<a onclick='vote(\""+idMeipi+"\", \""+idEntry+"\", "+newVote+" )'>";
				if(ranking1to5>=rank)
				{
					html += "<img src='"+getString("commonFiles")+"images/star_on.png' />";
				}
				else if (ranking1to5>=rank-0.5)
				{
					html += "<img src='"+getString("commonFiles")+"images/star_half.png' />";
				}
				else
				{
					html += "<img src='"+getString("commonFiles")+"images/star_off.png' />";
				}
				html += "</a>";
				}
				document.getElementById("votePlace").innerHTML = html;
				document.getElementById("rankPlace").style.display = 'none';
			}
		}
	});
}

/** 0 <= votes <= 20 */
function votesToColor(votes)
{
	return "rgb("+Math.round(10*votes)+", "+Math.round(10*votes)+", "+Math.round(255-10*votes)+")";
}

function cancelEntryWindow()
{
	entryWindowDiv = document.getElementById("entryWindow");
	if(entryWindowDiv.style.display=='')
	{
		entryWindowDiv.style.display = 'none';
	}

	hideMessage();
	try
	{
		tinyMCE.execCommand("mceRemoveControl", true, "comment");
	}
	catch(e) { }
}

function isValidLatLon(lat, lon)
{
	return lat>-5000 && lon>-5000;
}

function setEntryInfo(idMeipi,title,id_entry,dirEntry,content,type,text,url,date,lastEditedMsg,id_user,login,userId,comments,isLogged,voteColor,userVote,idCategory,lat,lng,address,tags,canEdit,extraParams)
{
	tagString = "";
	tagLinks = "";
	for (var i = 0; i < tags.length; i++)
	{
		if(i == 0)
		{
			tagString += tags[i];
			tagLinks += "<a href='"+getString("commonFiles")+"list.php?tag="+tags[i]+"'>"+tags[i]+"</a>";
		}
		else
		{
			tagString += " "+tags[i];
			tagLinks += " <a href='"+getString("commonFiles")+"list.php?tag="+tags[i]+"'>"+tags[i]+"</a>";
		}
	}
	tagString = tagString.replaceAll("&#039;", "&quot;");
	title = title.replaceAll("&#039;", "&quot;");
	text = text.replaceAll("&#039;", "&quot;");
	url = url.replaceAll("&#039;", "&quot;");

	oDiv = document.getElementById("entryWindow");
	entryInfo = "<div style=\"float: right;\"><input type=\"button\" onClick=\"javascript: cancelEntryWindow()\" value=\"[ X ]\" /></div><br/><br/>"
	entryInfo += "<div>";
	entryInfo += "	<h2>"+title+"</h2>";
	entryInfo += "	<div class=\"entry\" id=\""+id_entry+"\">";
	if (content)
	{
		if (type == "0")
		{
  			entryInfo += "		<p><img src=\""+dirEntry+content+"\" alt=\""+title+"\" align=\"center\"/></p>";
		}
		else if (type == "1")
		{
  			entryInfo += "		<p><object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/"+content+"\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://www.youtube.com/v/"+content+"&autoplay=1\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object></p>";
		}
		else if (type == "2")
		{
  			entryInfo += "		<p><embed FlashVars=\"autoPlay=true\" style=\"width:400px; height:326px;\" id=\"VideoPlayback\" type=\"application/x-shockwave-flash\" src=\"http://video.google.com/googleplayer.swf?docId="+content+"\" wmode=\"transparent\"></embed></p>";
		}
	}
	entryInfo += "		<p><span class=\"entryText\">"+text+"</span></p>";
	if (url.length > 0)
	{
		entryInfo += "	<p><span class=\"entryUrl\"><a href=\""+url+"\" target=\"_blank\">www</a></span></p>";
	}
	if (tagLinks.length > 0)
	{
		entryInfo += "	<p><span class=\"tagString\">"+getString("tags")+": "+tagLinks+"</span></p>";
	}
	entryInfo += "		<p><span class=\"entryDate\">"+date+"</span> - <span class=\"entryLogin\"><a href=\""+getString("commonFiles")+"list.php?id_user="+id_user+"\">"+login+"</a>";
	if((""+lastEditedMsg).length>0)
	{
		entryInfo += " - <span class=\"entryDate\">"+lastEditedMsg+"</span>";
	}
	entryInfo += "</span></p>";
	if(isValidLatLon(lat, lng))
	{
		entryInfo += "		<a href=\""+getString("commonFiles")+"map.php?id_entry="+id_entry+"\">"+getString("View in map")+"</a>";
	}

	if (((isLogged=="yes") && (userId == id_user)) || canEdit)
	{
		entryInfo += "		<a href=\"javascript:showDeleteConfirmation('"+id_entry+"','"+getString("commonFiles")+"');\">"+getString("Delete entry")+"</a>";
		entryInfo += "		<a href=\"javascript:showNewEntryForm('"+id_entry+"','"+title+"','"+text+"','"+url+"','"+idCategory+"','"+address+"','"+lng+"','"+lat+"','"+tagString+"', '"+type+"', '"+content+"');\">"+getString("Edit entry")+"</a>";

		entryInfo += "		<form name=\"archive\" action=\""+getString("commonFiles")+"actions/archive.php\" method=\"post\"><input type=\"hidden\" name=\"id_meipi\" value=\""+idMeipi+"\" /><input type=\"hidden\" name=\"id_entry\" value=\""+id_entry+"\" /><select name=\"status\"><option value=\"archive\">"+getString("archive")+"</option><option value=\"active\">"+getString("active")+"</option></select><input type=\"text\" name=\"comment\" /><input type=\"submit\" /></form>"+getString("Edit entry")+"</a>";
	}
	entryInfo += "		<p><a href=\""+getString("commonFiles")+"meipi.php?open_entry="+id_entry+"\">Permalink</a></p>";
	entryInfo += "	</div>";
	if (comments.length > 0)
	{
		entryInfo += "<div class=\"comments\">";
		for (var i = 0; i < comments.length; i++)
		{
			entryInfo += "<div class=\"comment\" id=\""+comments[i]["id"]+"\">";
			entryInfo += "<h3>"+comments[i]["subject"]+"</h3>";
			entryInfo += "<p>"+comments[i]["text"]+"<br/><span class=\"entryDate\">"+comments[i]["date"]+"</span> - <span class=\"entryLogin\"><a href=\""+getString("commonFiles")+"list.php?id_user="+comments[i]["id_user"]+"\">"+comments[i]["login"]+"</a></span></p>";
			entryInfo += "</div>";
		}
		entryInfo += "</div>";
	}
	if (isLogged == "yes")
	{
		entryInfo += getString("Post a comment");
		entryInfo += "<form name=\"comment\" action=\""+getString("commonFiles")+"actions/newComment.php\" method=\"post\">";
		entryInfo += "<input type=\"hidden\" name=\"id_meipi\" value=\""+idMeipi+"\" /><br/>";
		entryInfo += "<input type=\"hidden\" name=\"id_entry\" value=\""+id_entry+"\" /><br/>";
		entryInfo += "<input type=\"text\" name=\"subject\" /><br/>";
		entryInfo += "<textarea name=\"comment\"></textarea><br/>";
		entryInfo += "<input type=\"button\" value=\""+getString("Submit")+"\" onClick=\"javascript: submitNewComment()\" />";
		entryInfo += "</form>";
	}
	else
	{
		entryInfo += "<div>";
		entryInfo += "<a href=\"javascript:showLoginFormParams('open_entry="+id_entry+"');\">"+getString("Log in to write a comment")+"</a>";
		entryInfo += "</div>";
	}
	entryInfo += "<div>";
	entryInfo += "<a href=\""+getString("commonFiles")+"rssComments.php?id_entry="+id_entry+"\">"+getString("Comments RSS")+"</a>";
	entryInfo += "</div>";
	entryInfo += "</div>";

	var ranking1to5 = (Math.round(userVote/2.5)/2)+3;
	entryInfo += getString("Vote");
	for(var i=1; i<=5; i++)
	{
		if(isLogged=="yes")
		{
			entryInfo += "<a onclick='vote(\""+idMeipi+"\", \""+id_entry+"\", "+(5*(i-3))+")'>";
		}
		else
		{
			entryInfo += "<a onclick='showLoginFormParams(\"open_entry="+id_entry+"\")'>";
		}
		if(ranking1to5>=i)
		{
			entryInfo += "<img src='"+getString("commonFiles")+"images/star_on.png' />";
		}
		else if(ranking1to5>=i-0.5)
		{
			entryInfo += "<img src='"+getString("commonFiles")+"images/star_half.png' />";
		}
		else
		{
			entryInfo += "<img src='"+getString("commonFiles")+"images/star_off.png' />";
		}
		entryInfo += "</a>";
	}
	//entryInfo += "<div id=\"track\" style=\"background-color: " + voteColor + "; width: 200px; height: 5px;\">";
	//entryInfo += "<div class=\"selected\" id=\"handle\" style=\"width: 10px; height: 5px; background-color: rgb(255, 0, 0); cursor: move; left: 0px; position: relative;\"></div>";
	//entryInfo += "</div>";
	//entryInfo += "<div id=\"votedDiv\" style=\"display: none;\">" + getString("Voted") + "</div>";

	oDiv.innerHTML = entryInfo;
	oDiv.style.display='';
	
	//userVote=userVote+10;
	//if (isLogged == "yes")
	//{
		//// <![CDATA[
			//new Control.Slider('handle','track',{range:$R(0,20),
			//sliderValue: userVote,
			//onSlide:function(v){document.getElementById("track").style.backgroundColor=votesToColor(v);},
			//onChange:function(v){vote(idMeipi, id_entry, v-10);}});
		//// ]]>
	//}
	//else
	//{
		//// <![CDATA[
			//new Control.Slider('handle','track',{range:$R(0,20),
			//sliderValue: userVote,
			//onChange:function(v){showLoginFormParams("open_entry="+id_entry);}});
		//// ]]>
	//}
}

function showEntryWindow(idMeipi,idEntry,dirEntry,userId,isLogged)
{
	params = "?id_entry="+idEntry;

	GDownloadUrl(getString("commonFiles")+"entryHtml.php?open_entry="+idEntry, function(data, responseCode) {
			$("entryWindow").replace(data);
			tinyMCE.execCommand("mceAddControl", true, "comment");
		} );

	intermediateWindow = document.getElementById("overEntry");
	if(intermediateWindow!=null)
	{
		intermediateWindow.style.display='none';
	}

	goToTop();
	hideMessage();
}

function goToTop()
{
	parent.scroll(0,0);
}

function addToMosaic(idMeipi,idContent)
{
	params = "?operation=add";
	params += "&id_content="+idContent;
	if (idMeipi.length > 0)
	{
		params += "&id_meipi="+idMeipi;
	}
	GDownloadUrl(getString("commonFiles")+"actions/mosaicOperations.php"+params, function(data, responseCode) {
		var xml = GXml.parse(data);
		var results = xml.documentElement.getElementsByTagName("result");
		for (var i = 0; i < results.length; i++) {
			if(results[i].getAttribute("code")==1)
			{
				showMessage(getString("Added to mosaic"));
			}
		}
	});
}

function unselectFromMosaic(idMeipi,idContent)
{
	params = "?operation=del";
	params += "&id_content="+idContent;
	if (idMeipi.length > 0)
	{
		params += "&id_meipi="+idMeipi;
	}
	GDownloadUrl(getString("commonFiles")+"actions/mosaicOperations.php"+params, function(data, responseCode) {
		var xml = GXml.parse(data);
		var results = xml.documentElement.getElementsByTagName("result");
		for (var i = 0; i < results.length; i++) {
			if(results[i].getAttribute("code")==1)
			{
				//alert("Unselected!");
			}
		}
	});
}

function showMessage(msg)
{
	setDivText("messageText", msg);
	document.getElementById("messageWindow").style.display='';
	try
	{
		document.getElementById("hideMessage").focus();
	}
	catch(e)
	{}
}

function hideMessage()
{
	if(document.getElementById("messageWindow")!=null)
		document.getElementById("messageWindow").style.display='none';
	setDivText("messageText", "");
}

//deprecated
function selectType(type)
{
	document.forms.newEntryForm.filetype[type].checked=true;
}

function showFileType(type)
{
	document.getElementById("file_photo").style.display='none';
	document.getElementById("file_without").style.display='none';
	document.getElementById("file_video").style.display='none';
	id = "file_"+type;
	document.getElementById(id).style.display='';
}

function showCategoryDesc(numCats,cat)
{
	for (var i = 0; i <= numCats; i++) {
		id = "categoryDesc_"+i;
		document.getElementById(id).style.display='none';
	}
	if(cat=='')
		cat='0';
	id = "categoryDesc_"+cat;
	document.getElementById(id).style.display='';
}

function showDeleteConfirmation(id_entry,idMeipi)
{
	confirmationText = getString("DeleteConfirmation")+"<br/>";
	confirmationText += "<a href=\""+getString("commonFiles")+"actions/deleteEntry.php?id_meipi="+idMeipi+"&id_entry="+id_entry+"\">"+getString("Delete entry")+"</a>";
	showMessage(confirmationText);
}

function selectLanguage(dropdown)
{
	var newUrl = ""+document.location;
	var anchor = "";
	if(newUrl.indexOf("#")>0)
	{
		anchor = newUrl.substring(newUrl.indexOf("#"));
		newUrl = newUrl.replace(new RegExp("#.*"), "");
	}
	newUrl = newUrl.replace(new RegExp("&?lang=[^&]*"), "");
	if(newUrl.indexOf( "?" )<0)
	{
		newUrl += "?";
	}
	else if(newUrl.indexOf( "?" ) < newUrl.length-1)
	{
		newUrl += "&";
	}
	newUrl += "lang="+dropdown.value;
	newUrl += anchor;
	document.location = newUrl;
}

function showElement(elementName)
{
	try
	{
		var elementObject = document.getElementById(elementName);
		elementObject.style.display = '';
	}
	catch(e)
	{}
}

function hideElement(elementName)
{
	try
	{
		var elementObject = document.getElementById(elementName);
		elementObject.style.display = 'none';
	}
	catch(e)
	{}
}

function showLongDesc()
{
	document.getElementById("paginaDesc").style.display='none';
	document.getElementById("paginaLongDesc").style.display='';
}

function hideLongDesc()
{
	document.getElementById("paginaDesc").style.display='';
	document.getElementById("paginaLongDesc").style.display='none';
}

function toggleLongDesc()
{
	if(document.getElementById("paginaDesc").style.display=='none')
		hideLongDesc();
	else
		showLongDesc();
}

function pageLoaded()
{
	tinyMCE.init({
			mode: "textareas",
			theme : "advanced",
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,outdent,indent,blockquote,link,unlink,image,bullist,numlist",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			relative_urls: false,
			remove_script_host: false,
			entity_encoding : "raw"
		});
}

if(Event && Event.observe)
{
	Event.observe(window, 'load', pageLoaded);
}
