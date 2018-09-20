function valid(frm) {
		for (i=0; i < document[frm].elements.length; i++)
		{
			var item = document[frm].elements[i];
			var itemspec=item.id;
			var ddd = item.id.substring((item.id.lastIndexOf("_") + 1),item.id.length);
			var alertMsg=ddd.toLowerCase();
			//alert(alertMsg);
			var type=item.type;
			if(item.id.indexOf("req_")>=0)
			{
				switch (item.type)
				{
					case 'text':
								if ((item.value==""))
								{
									alert(innerHTML = "Please enter " +alertMsg);
									item.focus();
									return false;
								}
								if(!(item.value)=="")
								{
										if(item.name=="to_email" || item.name=="txt_user")
									{
										var FieldName=item.name;
										var checkField=validate_email(frm,FieldName);
										if(checkField==false)
										{
										flag=1;
										return false;
										}
									}
									if(item.name=="txt_mobile")
									{
										var FieldName=item.name;
										var checkField=cell_length(frm,FieldName);
										if(checkField==false)
										{
											flag=1;
											return false;
										}
									}
									if(item.name=="comment_email" )
									{
										var FieldName=item.name;
										var checkField=validate_email(frm,FieldName);
										if(checkField==false)
										{
										flag=1;
										return false;
										}
									}

									if(item.name=="email" )
									{
										var FieldName=item.name;
										var checkField=validate_email(frm,FieldName);
										if(checkField==false)
										{
										flag=1;
										return false;
										}
									}


									if(item.name=="femail")
									{											
										var FieldName=item.name;
										var checkField=multiplecheckMailId(item.value);
										if(checkField==false)
										{
										  item.focus();
										  return false;
										}
									} 


									if(item.name=="txt_email" )
									{
										var FieldName=item.name;
										var checkField=validate_email(frm,FieldName);
										if(checkField==false)
										{
										flag=1;
										return false;
										}
									}
									if(item.name=="Email" )
									{
										var FieldName=item.name;
										var checkField=validate_email(frm,FieldName);
										if(checkField==false)
										{
										flag=1;
										return false;
										}
									}
									if(item.name=="txt_confirmemail" )
									{
										var FieldName=item.name;
										var checkField=confirmemail(frm,FieldName);
										if(checkField==false)
										{
										flag=1;
										return false;
										}
									}
									
								}
								if(item.name=="txt_website")	
								{
								
									if(!(item.value)=="")
									{
										var Fieldname=item.name;
										var checkField=check_URL(frm,Fieldname);
										if(checkField==false)
										{
											flag=1;
											return false;
											break;
										}
										else 
										{
											flag=0;
											return true;
											break;
										}
									}
								}
						
						break;
					case 'select-one':
								if ((item.value==""))
								{
									alert(innerHTML = "Please select " +alertMsg);
									item.focus();
									return false;
								}
								break;

					case 'textarea':
								if ((item.value==""))
								{
									alert(innerHTML = "Please Select "  +alertMsg);
									item.focus();
									return false;
								}
								break;
					case 'file':
					{
						if (item.value !="")
						{
							var textname = item.name;
							var imagename = eval(document[frm].elements[textname].value.length);
							if (imagename > 0 )
							{
								var CheckImage = CheckValidImage(frm,textname);
								if(CheckImage==1)
								{
									flag=1;
									return false;
									break;
								}
								else
								{
									flag=0;
									return true;
									break;
								}
							}
						}
					}
					break;
					case 'checkbox':
								if (item.checked==false)
								{
									alert(innerHTML = "Please Select " +alertMsg);
									item.focus();
									return false;
								}
								break;
					case 'radio':
								if (item.checked==false)
								{
									alert(innerHTML = "Please Select " +alertMsg);
									item.focus();
									return false;
								}
								break;
				

					case 'password':
								if(item.value=="")
								{
									var element = document.getElementById(itemspec);
									alert(innerHTML = "Please enter " +alertMsg);
									item.focus();
									flag=1;
									return false;
								}
								if(!(item.value)=="")
								{
									if(item.name=="txt_password")
									{
										var checkField=check_minchartxt_password(frm);
										if(checkField==false)
										return false;
									}
									if(item.name=="txt_password")
									{
										var checkField=valuser(frm);
										if(checkField==false)
										return false;
									}
									if(!(item.value)=="")
									{
										if(item.name=="mypassword")
										{
											var checkField=check_mincharpassword(frm);
											if(checkField==false)
											return false;
										}

										if(item.name=="mypassword")
										{
											var checkField=valuser1(frm);
												if(checkField==false)
											return false;
										}
								}
								if(item.name=="MED_conpass")
								{ 
									
										if(!(item.value)=="")
										{ 
										var email=validepassword(frm);
										if(email==false)
										{
											flag=1;
											return false;
										}
									}
								}


						}
								if(item.name=="txt_con_password")
								{ 
										if(!(item.value)=="")
									{ 
										var email=validetxt_password(frm);
										if(email==false)
										{
											flag=1;
											return false;
										}
									}
								}
						}
			}
		}
}


function chk_hid()
{
   document.regForm.strnri.disabled=false;	
   document.all.cnt.style.display = '';
   document.regForm.country.value='';
   showDialect(76);
//   document.all.strnri.style.display = '';
//   document.all.strnri.style.display = '';	
}

function numOnlyacccomma(frm,FieldName)
	{
		var alpha;
		alpha=FieldName.value;
		for(var j=0; j<alpha.length; j++)
		{
			var alphaa = alpha.charAt(j);
			var hh = alphaa.charCodeAt(0);
			if(hh != 13 && hh < 43 || hh > 57 || hh == "46")
			{
				hh=null;
				alert("Please Enter numbers Only");
				FieldName.value="";
				FieldName.focus();
				return false;
			}		
		}
		if(alpha.length<10)
		{
			hh=null;
			alert("Please Enter minimum 10 digits");
			FieldName.focus();
			return false;
		}
	}

function CharacterOnly(frm,FieldName)
{
	
	var numeric;
	numeric=FieldName.value;
	for(var j=0; j<numeric.length; j++)
	{
		var alphaa = numeric.charAt(j);
		var hh = alphaa.charCodeAt(0);
	

		if((hh > 64 && hh<91) || (hh > 96 && hh<123) || hh==32)
		{
		}
		else
		{
			
			alert("Please Enter Alphabets Only");
			FieldName.value="";
			FieldName.focus();
			return false;
		}
	}
	
	return true;
}

function chk_hid1(state)
{
  document.regForm.strnri.disabled=true; 
  document.all.cnt.style.display = 'none';
  document.regForm.country.value=1;
  showDialect(1);
  document.forms[0].country.value=1;
  showDialect(1);
  document.forms[0].State.value=State;
  showCity(State,City);

 //  document.all.strnri.style.display = 'none';
 // document.all.strnri.style.display = 'none';	
}
function disableprice(val)
{
	if(val == 1)
	{
			document.forms[0].Crores.disabled = true;
			document.forms[0].Lakhs.disabled = true;
			document.forms[0].Thousands.disabled = true;
	}
	else
	{
			document.forms[0].Crores.disabled = false;
			document.forms[0].Lakhs.disabled = false;
			document.forms[0].Thousands.disabled = false;
	}
}
function disable(val)
{
	if(val == 1)
	{
		document.forms[0].Crores.disabled = true;
		document.forms[0].lakhs.disabled = true;
	}
	else
	{
		document.forms[0].Crores.disabled = false;
		document.forms[0].lakhs.disabled = false;
	}
}
function disable1(val)
{
	if(val == 1)
	{
		document.forms[0].Prop_avail_month.disabled = true;
		document.forms[0].Prop_avil_year.disabled = true;
	}
	else
	{
		document.forms[0].Prop_avail_month.disabled = false;
		document.forms[0].Prop_avil_year.disabled = false;
		document.forms[0].Prop_avail_month.id="req_select-one_Month";
		document.forms[0].Prop_avil_year.id="req_select-one_Year";
	}
}
//FUNCTION FOR CHECKING txt_password AND CONFIRM txt_password
function validetxt_password(frm)
{
	var txt_password=document[frm].elements["txt_password"].value;
	var ConfirmPass=document[frm].elements["txt_con_password"].value;

	if (txt_password!=ConfirmPass)
	{
		alert('Please confirm your password correctly');
		document[frm].elements["txt_con_password"].focus();
		
		return false;
	}
	else
	{
		return true;
	}
}	
function confirmemail(frm)
{
	var txt_password=document[frm].elements["txt_email"].value;
	var ConfirmPass=document[frm].elements["txt_confirmemail"].value;

	if (txt_password!=ConfirmPass)
	{
		alert('Please Confirm Your Email Correctly');
		document[frm].elements["txt_confirmemail"].focus();
		
		return false;
	}
	else
	{
		return true;
	}
}	
function validepassword1(frm)
{
	var Password=document[frm].elements["epass"].value;
	var ConfirmPass=document[frm].elements["txt_con_password"].value;

	if (Password!=ConfirmPass)
	{
		alert('Please confirm your password correctly');
		document[frm].elements["txt_con_password"].focus();
		
		return false;
	}
	else
	{
		return true;
	}
}	
function valuser(frm)
{
	var user1=document[frm].elements["txt_email"].value;
	var pass1=document[frm].elements["txt_password"].value;

	if(user1 == pass1)
	{
		alert('Password should not be same as LoginID');
		document[frm].elements["txt_password"].focus();

		return false;
	}
	else
	{
		return true;
	}
}

function check_mincharpassword(frm)
{
		
	var passlen = document[frm].elements["mypassword"].value.length;
	
	if ( passlen < 6  )
	{
		alert ( "Password field should not be less than 6 Characters");
		document[frm].elements["mypassword"].focus();
		return false;
	}
	else if ( passlen > 12)
	{
		alert ( "Password field should not be or more than 12 Characters");
		document[frm].elements["mypassword"].focus();
		return false;
	}
}


function validepassword(frm)
{
	var Password=document[frm].elements["mypassword"].value;
	var ConfirmPass=document[frm].elements["MED_conpass"].value;

	if (Password!=ConfirmPass)
	{
		alert('Please enter your confirm password correctly');
		document[frm].elements["MED_conpass"].focus();
		
		return false;
	}
	else
	{
		return true;
	}
}	



function valuser1(frm)
{
	var user1=document[frm].elements["MED_username"].value;
	var pass1=document[frm].elements["mypassword"].value;

	if(user1 == pass1)
	{
		alert('Password should not be same as Username');
		document[frm].elements["mypassword"].focus();

		return false;
	}
	else
	{
		return true;
	}
}

function checkDelete()
{
	var chk=false;
	for (var i=0;i<(document.form1.elements.length);i++)
	{
		if (document.form1.elements[i].checked == true)
		{
			chk=true;
			if(confirm("Are you sure to delete marked messages"))
			{
			 document.form1.submit();
			}
			break;
		}
	}
	if (chk==false)
	{
		alert ("Please select messages!");
		return false;
	}

}

function validatetextarea(frm,field,cntfield,maxlimit) 
{	
	if (field.value.length > maxlimit)
	{// if too long...trim it!
		field.value = field.value.substring(0, maxlimit);
	}
	// otherwise, update 'characters left' counter
	else if(cntfield!=null)
	{
		cntfield.value = maxlimit - field.value.length;
	}
	
}

function selectAll(list)
{
	aler("sucess");
	if(document.form1.hidSelect.value==0)
	{
		for (var i=0;i<list.length;i++)
		{
			document.getElementById(list[i]).checked = true
		}
		document.form1.hidSelect.value=1;
	}
	else
	{
		for (var i=0;i<list.length;i++)
		{
			document.getElementById(list[i]).checked = false
		}
		document.form1.hidSelect.value=0;
	}
}

function check_all(list)
{
	
		if(document.form1.hidSelect.value==0)
	{
		for (var i=0;i<list.length;i++)
		{
			document.getElementById(list[i]).checked = true
		}
		document.form1.hidSelect.value=1;
	}
	else
	{
		for (var i=0;i<list.length;i++)
		{
			document.getElementById(list[i]).checked = false
		}
		document.form1.hidSelect.value=0;
	}
}


/**
function selectAll(list)
{
	alert "sucess";
	if(document.form1.hidSelect.value==0)
	{
		for (var i=0;i<list.length;i++)
		{
			document.getElementById(list[i]).checked = true
		}
		document.form1.hidSelect.value=1;
		
	}
	else
	{
		for (var i=0;i<list.length;i++)
		{
			document.getElementById(list[i]).checked = false
		}
		document.form1.hidSelect.value=0;
	}
}**/


function validurl()
{
	if(document.forms[0].Url.value != "")
	{
		document.forms[0].Url.id="req_txt_Url";
	}
	else
	{
		document.forms[0].Url.id="";
	}
	
}

function confirm_delte()	
{
	flag=confirm("Would you like to Confirm!...");
	if(flag) return true;
	else return false;
}
function chkuser(frm)
{
		
		var checkField=check_txt_username(frm);
		var txt_username = document[frm].elements["reg_user"].value;
		if(checkField==false)
		{
			flag=1;
			return false;
		}
		else
		{
			window.open('forms/chkUser.php?flag=1&uname='+ txt_username,'','width=350,height=190,scrollbars=yes,status=no,toolbar=no,resizable=yes');
		}
}
function check_txt_username(frm)
{
	var user=document[frm].elements["txt_username"].value;
	var sub1=user.substr(0,1);
	var len = document[frm].elements["txt_username"].value.length;
	
	if ( user == "" )
	{
		alert ( "Username field is blank" );
		document[frm].elements["txt_username"].focus();
		return false;
	}
	else 
	{
		var checkField=check_minchartxt_username(frm);
		if(checkField==false)
		{
			flag=1;
			return false;
		}

	}
}

//FUNCTIONS FOR EMAIL VALIDATION
function validate_email(frm,name)
{
	if (emailvalidation(document[frm].elements[name].value)==false)
	{
		document[frm].elements[name].focus(); 
		alert('Invalid E-Mail Address');
		return false;
	}
}

function emailvalidation(i)
{
	var regexp = /^[a-zA-Z0-9_@.-]*$/;
	var val = i;
	var at="@";
	var dot=".";
	var pat=val.indexOf(at);
	var lval=val.length;
	var pdot=val.indexOf(dot);
	var secondat = val.indexOf(at,pat+1);
	var lastat = val.lastIndexOf(at);
	var afterat = val.substring(pat+1,pat+2);
	var afterdot = val.substring(pdot+1,pdot+2);
	var lastchar = val.substring(lval-1,lval);
	var dotafterat = val.indexOf(dot,pat+1);
	var pseconddot = val.indexOf(dot,pdot+1);
	var pthirddot = val.indexOf(dot,pseconddot+1);
	var afterseconddot = val.substring(pseconddot+1,pseconddot+2);
	var afterthirddot = val.substring(pthirddot+1,pthirddot+2);

	if (regexp.test(val) == false ||pat == -1 || pat == 0 || pat == lval-1 || pdot == -1 || pdot == 0 || pdot == lval-1 || secondat != -1 || lastat != pat || afterat == dot || afterat == "-" || afterat == "_" || afterdot == at || afterdot == "-" || afterdot == "_" || afterdot == dot || lastchar == dot || dotafterat == -1 || afterseconddot == at || afterseconddot == "-" || afterseconddot == "_" || afterseconddot == dot || afterthirddot == at || afterthirddot == "-"
	|| afterthirddot == "_" || afterthirddot == dot)
	{
		return false;
	}
	else
	{
		return true;
	}
}


function forceNumber(evt){
  var key = (window.Event) ? evt.which : evt.keyCode;
  if(key < 48 || key > 58) {
	  alert("Enter Number Only");
	  return false;
  } else {
	  return true;
  }
}
//FUNCTION FOR CHECKING txt_username LENGTH
function check_minchartxt_username(frm)
{
	var user=document[frm].elements["txt_username"].value;
	var sub1=user.substr(0,1);
	var len = document[frm].elements["txt_username"].value.length;
	if ( len < 6 )
	{
		alert ( "Username Field should not be less than 6 Characters" );
		document[frm].elements["txt_username"].focus();
		return false;
	}
	else if ( len > 12)
	{
		alert ( "Username Field should not be more than 12 Characters" );
		document[frm].elements["txt_username"].focus();
		return false;
	}
	else if ((sub1 >= 0) && (sub1 <=9))
	{
		alert("Username Field should start with alpha Character");
		document[frm].elements["txt_username"].value="";
		document[frm].elements["txt_username"].focus();
		return false;
	}
}
function check_username(frm)
{
	var user=document[frm].elements["txt_email"].value;
	document.getElementById('NameDiv').style.display="none";
	document.getElementById('load').style.display="none";
	if( user == "" )
	{
		alert("Email ID field is blank");
		document[frm].elements["txt_email"].focus();
		return false;
	}
	else 
	{
		var checkField=validate_email('register','txt_email');
		if(checkField==false)
		{
			return false;
		}
		else
		{
			showuseravail(user,'NameDiv');
		}
	}	
}

//URL CHECKING FUNCTION
function check_URL(frm,name)
{
	var mailValid = /^(([w]{3})+\.+([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9]*)+([\.com\.net\.org\.gov\.edu\.info\.co.uk]))+$/;
	if (!document[frm].elements[name].value.match(mailValid))
	{
		alert("Invalid URL");
		document[frm].elements[name].focus();
		return false;
	}
}
function showbutton()
{
	document.all.id3.style.display = '';
	
}
//FUNCTION FOR CHECKING txt_password LENGTH
function check_minchartxt_password(frm)
{
	var passlen = document[frm].elements["txt_password"].value.length;
	if ( passlen < 6  )
	{
		alert ( "Password field should not be less than 6 Characters");
		document[frm].elements["txt_password"].focus();
		return false;
	}
	else if ( passlen > 12)
	{
		alert ( "Password field should not be or more than 12 Characters");
		document[frm].elements["txt_password"].focus();
		return false;
	}
}

checked=false;
function checkedAll (form1) 
{
	 var aa= document.getElementById(form1);
	 if (checked == false)
     {
          checked = true
     }
     else
     {
        checked = false
     }
     for (var i =0; i < aa.elements.length; i++) 
	 {
		aa.elements[i].checked = checked;
	 }
}

function select(a) {
    var theForm = document.recform;
    for (i=0; i<theForm.elements.length; i++) {
        if (theForm.elements[i].name=='make_chk[]')
            theForm.elements[i].checked = a;
    }
	if(document.getElementById('trBrand').style.display==''){
	for (i=0; i<theForm.elements.length; i++) {
        if (theForm.elements[i].name=='make_chk1[]')
            theForm.elements[i].checked = a;
    }
	}
}


function Contacts()
{
	var chk=false;
	for (var i=0;i<(document.myform.elements.length);i++)
	{
		if (document.myform.elements[i].checked == true)
		{
			chk=true;
//			var w = window.open('about:blank','Popup_Window','menubar=0,resizable=0,width=550,height=300');		
//			document.myform.target='Popup_Window';
			document.myform.action="Search_Contact.php?act=Form";
			document.myform.submit();
			break;
		}
	}
	if (chk==false)
	{
		alert ("You must Select any one!...");
		return false;
	}
}

function View_Cotnacts_Property(User_ID,Prop_ID)
{
	window.open('Property_Contact_Form.php?act=View_Send_Enquiry&User_ID='+User_ID+'&Prop_ID='+Prop_ID+'','Popup_Window','menubar=0,resizable=0,width=500,height=300');		

}
function cell_length(frm,FieldName)
{
	var ph,len;
	ph=document[frm].elements[FieldName].value;
	len=ph.length;
	if(len<10)
	{
		alert("Mobile Number Should have minimum 10 digits");
		document[frm].elements[FieldName].focus();
		return false;
	}
	
return true;
}
function compare_search()
{
	var counting=0
	for (var i=0;i<(document.myform.elements.length);i++)
	{
		if (document.myform.elements[i].checked == true)
		{
			counting++;
			if(counting>=2) {
					document.myform.action="Compare_Search.php?act=view";
					document.myform.submit();
					break;
			}
		}
	}
	if (counting==0)
	{
		alert ("You must Select any two!...");
		return false;
	} else if (counting==1)
	{
		alert("You must select at least 2");
		return false;
	} 
}

function Book_Mark()
{
//	alert('success');
	var chk=false;
	for (var i=0;i<(document.myform.elements.length);i++)
	{
		if (document.myform.elements[i].checked == true)
		{
			chk=true;
			document.myform.action="Book_Mark.php?act=Search_BookMark";
			document.myform.submit();
			break;
		}
	}
	if (chk==false)
	{
		alert ("You must Select any one!...");
		return false;
	}
}


function download()
{
	var chk=false;
	for (var i=0;i<(document.myform.elements.length);i++)
	{
		if (document.myform.elements[i].checked == true)
		{
			chk=true;
			document.myform.action="download.php";
			document.myform.submit();
			break;
		}
	}
	if (chk==false)
	{
		alert ("You must Select any one!...");
		return false;
	}
}


function chkuser(frm)
{
		var checkField=check_txt_username(frm);
		var txt_username = document[frm].elements["txt_username"].value;
		if(checkField==false)
		{
			flag=1;
			return false;
		}
		else
		{
			window.open('forms/chkUser.php?flag=1&uname='+ txt_username,'','width=350,height=190,scrollbars=yes,status=no,toolbar=no,resizable=yes');
		}
}

function check_txt_username(frm)
{
	var user=document[frm].elements["txt_username"].value;
	var sub1=user.substr(0,1);
	var len = document[frm].elements["txt_username"].value.length;
	
	if ( user == "" )
	{
		alert ( "txt_username field is blank" );
		document[frm].elements["txt_username"].focus();
		return false;
	}
	else 
	{
		var checkField=check_minchartxt_username(frm);
		if(checkField==false)
		{
			flag=1;
			return false;
		}

	}
}

function winclose(uname)
{
	window.opener.document.forms[0].txt_username.value = uname;
	window.close();
}

function check_minchartxt_username(frm)
{
	var user=document[frm].elements["txt_username"].value;
	var sub1=user.substr(0,1);
	var len = document[frm].elements["txt_username"].value.length;
	if ( len < 6 )
	{
		alert ( "Username Field should not be less than 6 Characters" );
		document[frm].elements["txt_username"].focus();
		return false;
	}
	else if ( len > 12)
	{
		alert ( "Username Field should not be more than 12 Characters" );
		document[frm].elements["txt_username"].focus();
		return false;
	}
	else if ((sub1 >= 0) && (sub1 <=9))
	{
		alert("Username Field should start with alpha Character");
		document[frm].elements["txt_username"].value="";
		document[frm].elements["txt_username"].focus();
		return false;
	}
}
function onemore(rname)
{
	var element=document.getElementById(rname);
	element.style.display="block";
}

//FUNCTION FOR CHECKING WHETHER THE IMAGES UPLOADED ARE VALID OR NOT
function CheckValidImage(frm,name)
{
	var image=document[frm].elements[name].value;
	var imagelength=document[frm].elements[name].value.length;
	var imageindex=image.lastIndexOf(".") + 1;
	var last=image.substring(imageindex,imagelength);
	if(document[frm].elements[name].value != "")
	{
		if(last != "jpg" && last != "gif" && last != "jpeg" && last != "png" && last != "JPG" && last != "GIF" && last != "JPEG" && last != "PNG")
		{
			alert("Please Upload valid jpg or gif or png file");
			return flag=1;
		}
		else
		{
			return flag=0;
		}
	}
}

function Show_Map1(id,Map)
{
day = new Date();
id1 = "Map";
window.open('Show_map.php?file='+Map+'&id='+id, id1, 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=500,left='+(screen.width/2-250)+', top=0');
}

function Show_Image(id,Pid)
{
day = new Date();
id1 = day.getTime();
window.open('Show_Image.php?file='+id+'&id='+Pid, id1, 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=600,height=600,left='+(screen.width/2-250)+', top='+(screen.height/2-150));
}

function Show_All_Image(id,Pid)
{
day = new Date();
id1 = day.getTime();
window.open('Show_All_Image.php?file='+id+'&id='+Pid, id1, 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=600,height=600,left='+(screen.width/2-250)+', top='+(screen.height/2-150));
}

function DelImg(id,path)
{	
	if(confirm("Are you sure to want to delete ?"))
	window.location=path+"&url="+path+"&delId="+id;
}

function check_Status(User_ID) {
	var Prop_ID=document.uploadvideo.propertyid.value;
	if(Prop_ID=='') {  } else { check_property(Prop_ID,User_ID);  }
}

function resume()
{
	var r = document.career.getElementById(Resume);
	if(r == "")
	{
		alert("Please upload your Resume");
		document.career.Resume.focus();
		return false;
	}
}
function add_to_cart(Prop_ID) {
	url="Add_Shoping_Cart.php?Prop_ID="+Prop_ID;
	window.open(url,'AddCart','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=300,height=300');
}

function numOnly()
{
	if(window.event.keyCode != 13 && window.event.keyCode<45 || window.event.keyCode>57 || window.event.keyCode == "47" || window.event.keyCode == "46")
	{
		window.event.keyCode=null;
		alert("Please Enter Numeric Values Only");
	}
}


function multiplecheckMailId(mailids)
{
var arr = new Array(
'.com','.net','.org','.biz','.coop','.info','.museum','.name','.pro',
'.edu','.gov','.int','.mil','.ac','.ad','.ae','.af','.ag','.ai','.al',
'.am','.an','.ao','.aq','.ar','.as','.at','.au','.aw','.az','.ba','.bb',
'.bd','.be','.bf','.bg','.bh','.bi','.bj','.bm','.bn','.bo','.br','.bs',
'.bt','.bv','.bw','.by','.bz','.ca','.cc','.cd','.cf','.cg','.ch','.ci',
'.ck','.cl','.cm','.cn','.co','.cr','.cu','.cv','.cx','.cy','.cz','.de',
'.dj','.dk','.dm','.do','.dz','.ec','.ee','.eg','.eh','.er','.es','.et',
'.fi','.fj','.fk','.fm','.fo','.fr','.ga','.gd','.ge','.gf','.gg','.gh',
'.gi','.gl','.gm','.gn','.gp','.gq','.gr','.gs','.gt','.gu','.gv','.gy',
'.hk','.hm','.hn','.hr','.ht','.hu','.id','.ie','.il','.im','.in','.io',
'.iq','.ir','.is','.it','.je','.jm','.jo','.jp','.ke','.kg','.kh','.ki',
'.km','.kn','.kp','.kr','.kw','.ky','.kz','.la','.lb','.lc','.li','.lk',
'.lr','.ls','.lt','.lu','.lv','.ly','.ma','.mc','.md','.mg','.mh','.mk',
'.ml','.mm','.mn','.mo','.mp','.mq','.mr','.ms','.mt','.mu','.mv','.mw',
'.mx','.my','.mz','.na','.nc','.ne','.nf','.ng','.ni','.nl','.no','.np',
'.nr','.nu','.nz','.om','.pa','.pe','.pf','.pg','.ph','.pk','.pl','.pm',
'.pn','.pr','.ps','.pt','.pw','.py','.qa','.re','.ro','.rw','.ru','.sa',
'.sb','.sc','.sd','.se','.sg','.sh','.si','.sj','.sk','.sl','.sm','.sn',
'.so','.sr','.st','.sv','.sy','.sz','.tc','.td','.tf','.tg','.th','.tj',
'.tk','.tm','.tn','.to','.tp','.tr','.tt','.tv','.tw','.tz','.ua','.ug',
'.uk','.um','.us','.uy','.uz','.va','.vc','.ve','.vg','.vi','.vn','.vu',
'.ws','.wf','.ye','.yt','.yu','.za','.zm','.zw','.in');

var sd = mailids;
var ids = sd.split(",");
var val = true;
var beforeat="";
var afterat="";
var afterat2="";
var invalid=false;


for(var j=0; j<(ids.length); j++)
{
	var temp = "wrong";
	var mai = ids[j];

	if(mai.charCodeAt(mai.length-1)==13)
		mai=mai.substring(0,mai.length-1);
	
	var dot = mai.lastIndexOf(".");
	var con = mai.substring(dot,mai.length);
	con=con.toLowerCase();
	con=con.toString();
	
	for(var i=0; i<(arr.length); i++)
	{
		if(con == arr[i])
		{
			temp='right';
		}
	}

	if(temp=="wrong")
		val=false;
	
	var att=mai.lastIndexOf("@");
	beforeat=mai.substring(0,att);
	beforeat=beforeat.toLowerCase();
	beforeat=beforeat.toString();
	var asci1=beforeat.charCodeAt(0);

	afterat=mai.substring(att+1, dot);
	afterat=afterat.toLowerCase();
	afterat=afterat.toString();

	afterat2=mai.substring(att+1, mai.length);
	afterat2=afterat2.toLowerCase();
	afterat2=afterat2.toString();
	//alert(afterat2);
	if(beforeat=="" || afterat=="" || beforeat.length>30)
		val=false;

	if(afterat2.length>64 || afterat.length<2)
		val=false;

	if((afterat.charCodeAt(0))==45 || (afterat.charCodeAt(afterat.length-1))==45)
		val=false;
		
	if(val==true)
	{
		if(asci1 > 47 && asci1 < 58)
			val=false;
		
		if(asci1 < 48 || asci1 > 57)
		{
			for(var k=0; k<=beforeat.length-1; k++)
			{
				var asci2=beforeat.charCodeAt(k);
				if((asci2<=44 || asci2==47) || (asci2>=58 && asci2<=94) || (asci2==96) || (asci2>=123 && asci2<=127))
				{
					val=false;
					break;
				}
			}
		
			for(var m=0; m<=afterat.length-1; m++)
			{
				var asci3=afterat.charCodeAt(m);
				if((asci3<=44) || (asci3==46) || (asci3==47) || (asci3>=58 && asci3<=96) || (asci3>=123 && asci3<=127))
				{
					val=false;
					break;
				}
			}	
		}
	}
	
	if(val==false)
	{
		invalid=true;
		break;
	}
}
if(invalid==true)
{
	alert("Your maild "+mai+" is not valid");	
	return false;
}
else
{
	return true;
}

}


function sendprivatemsg(frmname)
{
	
	var sub=document[frmname].subjecttxt.value;
	var msg=document[frmname].msgtxt.value;
	var forum_id=document[frmname].forum_id.value;
	var touserid=document[frmname].touserid.value;
	var tousername=document[frmname].toname.value;
	if(sub=='')
	{
		alert("Please enter the subject");
		document[frmname].subjecttxt.focus();
		return false
	}
	if(msg=='')
	{
		alert("Please enter the Message");
		document[frmname].msgtxt.focus();
		return false
	}
	totalmsg='&sub='+sub+'&msg='+msg+'&touserid='+touserid+'&tousername='+tousername+'&forum_id='+forum_id; 
	//alert(forum_id);
	sendmsg('msgdiv',totalmsg);
}



function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function validation(frm)
{
	if(document.register.search_make.value==''){
		alert("Please Select Car Make");
		document.register.search_make.focus();
		return false;
	}
	if(document.register.search_model.value==''){
		alert("Please Select Car Model");
		document.register.search_model.focus();
		return false;
	}
	if(document.register.month_manufacture.value==''){
		alert("Please Select Make Month");
		document.register.month_manufacture.focus();
		return false;
	}if(document.register.year_manufacture.value==''){
		alert("Please Select Make Year");
		document.register.year_manufacture.focus();
		return false;
	}
	
	if(document.register.search_state.value==''){
		alert("Please Select State");
		document.register.search_state.focus();
		return false;
	}if(document.register.search_city.value==''){
		alert("Please Select City");
		document.register.search_city.focus();
		return false;
	}
	if(document.register.feedback.value==''){
		alert("Please Enter feedback");
		document.register.feedback.focus();
		return false;
	}
	/*if(document.register.body_colour.value==''){
		alert("Please Select Color");
		document.register.body_colour.focus();
		return false;
	}*/
	if(document.getElementById('yyy').length==0){}else
	{var ln = document.getElementById('yyy').length; for(var i=0;i<ln;i++){document.getElementById('yyy').options[i].selected=true;}}
	if(document.register.name.value==''){
		alert("Please Enter Contact Name");
		document.register.name.focus();
		return false;
	}if(document.register.email.value==''){
		alert("Please Enter Email");
		document.register.email.focus();
		return false;
	}
	if(document.register.email.value!=''){
		var checkField=validate_email('register','email');
		if(checkField==false)
		{
		flag=1;
		return false;
		}
	}
	
	if(document.register.mobile.value==''){
		if(document.register.stdphone.value=='' && document.register.phone.value!=''){
			alert("Please Enter STD Code");
			document.register.stdphone.focus();
			return false;
		}
		if(document.register.stdphone.value!='' && document.register.phone.value==''){
			alert("Please Enter Phone Number");
			document.register.phone.focus();
			return false;
		}
	}
	if(document.register.mobile.value==''){
	if(document.register.stdphone.value==''){
		alert("Please Enter Mobile Or Phone");
		document.register.mobile.focus();
		return false;
	}
	}
}