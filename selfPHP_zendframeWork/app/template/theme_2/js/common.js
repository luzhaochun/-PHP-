
var wait=10;  
	function time(o) {  
		if (wait == 0) {  
			o.removeAttribute("disabled");            
			o.value="免费获取验证码";  
			wait = 10;  
		} else {  
			o.setAttribute("disabled", true);  
			o.value="重新发送(" + wait + ")";  
			wait--;  
			setTimeout(function() {  
				time(o)  
			},1000)  
		}  
	}  
	document.getElementById("fsyzm").onclick=function(){time(this);}  




var reg_mobile = /^(1[34578]\d{9})$/;
var reg = /^[a-zA-Z_0-9]{3,12}$/;
var reg_wx = /^[0-9]{3}$/;

jQuery(function(){
	if(jQuery("#phone").length>0){
    	jQuery("#phone").bind("input", function(){
    		checkUser();
    	});
    	if(/msie/i.test(navigator.userAgent))
    	{
    		document.getElementById('phone').onpropertychange=checkUser;
    	}
		var aid = '';
		if(""=="" && aid.length>0){
			jQuery('#phone').val(aid);
		}
		checkUser('Y');
		
	}
});

function sendSms(){
    var phone=jQuery.trim(jQuery('#phone').val());
	
	if(phone==''){
	$('#errorInfo').html("<div class='e-icon fl'></div><span class='c-red fl'></span></div>手机号码不能为空!").show();
		return false;
	}
    if(reg_mobile.test(phone)){
    	para = 'vtoken=5500796416344f74b3b44f03c44750f9&phone='+phone;
        jQuery.ajax({
            type : "POST",
            url:"/json/user_sendSMS.htm",
            dateType:"json",
            data:para,
            success : function(data){
            	data = eval('(' + data + ')');
    			if(data.state=='Y'){
    				$('#errorInfo').html('').hide();
					timedCount();
    			}else{
    				$('#errorInfo').html("<div class='e-icon fl'></div><span class='c-red fl'></span></div>"+data.message).show();
    			}
            },
			error:function(data){
				$('#errorInfo').html("<div class='e-icon fl'></div><span class='c-red fl'></span></div>请求超时").show();
    		}
    	});
    }else{
   	 jQuery('#errorInfo').html("<div class='e-icon fl'></div><span class='c-red fl'></span></div>手机号码格式错误!").show();
		return false;
	}
}

function userlogin(){
	if(document.getElementById("onekeyLoginForm")){
		var onekeyForm=document.getElementById("onekeyLoginForm")
		onekeyForm.submit();
		return;
	}
	$('#which').val('');
	var phone = jQuery.trim(jQuery('#phone').val());
	var passwd = jQuery.trim(jQuery('#upasswd').val());
	var bakval=$('#bakval').val();
	if(bakval=='1'){
	$('#errorInfo').html("<div class='e-icon fl'></div><span class='c-red fl'></span></div>您的帐号无法登录!").show();
		return false;
	}
	if(phone==''){

		$('#errorInfo').html("<div class='e-icon fl'></div><span class='c-red fl'></span></div>手机号码不能为空!").show();

		return false;
	}
	if(reg_mobile.test(phone) && $('#checkInfo').is(':hidden')){
		checkUser();
	}
	if(!reg_mobile.test(phone)){
		$('#errorInfo').html("<div class='e-icon fl'></div><span class='c-red fl'></span></div>手机号码格式错误!").show();

		return false;
	}
	if(!$('#checkInfo').is(':hidden')){
		if(passwd==''){
		$('#errorInfo').html("<div class='e-icon fl'></div><span class='c-red fl'></span></div>验证码不能为空!").show();

			return false;
		}else{
			if(!reg.test(passwd)){

			$('#errorInfo').html("<div class='e-icon fl'></div><span class='c-red fl'></span></div>用户名或验证码错误").show();

				return false;
			}
		}
		var digest = CryptoJS.SHA256(passwd);
		$('#upasswd').val(digest);
	}
	document.loginForm.submit();
}

function checkUser(obj){
	jQuery('#errorInfo').html('').hide();
	var phone=jQuery('#phone').val();
	if(!reg_mobile.test(phone)){
		showContext();
		return false;
	}else{
	     para = 'mid=364bf060454011e489cad89d672af1cc'+'&q='+phone+'&t=5500796416344f74b3b44f03c44750f9';
		 jQuery.ajax({
         type : "POST",
         url:"/json/user_checkBlackPhone.htm",
         dateType:"json",
         data:para,
         success : function(data){
        	data = eval('(' + data + ')');
			if (data.state){
		            showContext();
					jQuery("#bakval").val(1);
					jQuery("#loginBtn").html("您的帐号无法登录");
		            return false;
				}else{//reg
				jQuery("#bakval").val(0);
				jQuery("#loginBtn").html("连 接 WiFi");
				jQuery('#passwd').focus();
	          jQuery.ajax({
                type : "POST",
                url:"/json/user_checkRegisterPhone.htm",
                dateType:"json",
                data:para,
                success : function(data){
             	data = eval('(' + data + ')');
		    	if (data.state == 'N'){//reg
				if('Y'==obj){ 
					showContext('2');
				}else{
					showContext('1');
				}
        	   }else if (data.state == 'Y'){//quick
				showContext('3');
        	   }else{//default
				showContext();
        	   }
               },error:function(data) {
		  }
	          });
				}
              }
	    });
	}
}

function showContext(obj){
	if(obj=='1'){//register
		jQuery("#checkInfo").show();
	}else if(obj=='2'){//login
		jQuery("#errorInfo").hide();
		jQuery("#checkInfo").show();
		jQuery("#loginBtn").html("连 接 WiFi");
	}else if(obj=='12'){//reg
		jQuery("#checkInfo").show();
		jQuery("#errorInfo").hide();
	}else if(obj=='3'){//quick
		jQuery("#checkInfo").hide();
		jQuery("#loginBtn").html("直 接 登 录");
		jQuery("#errorInfo").hide();
	}else{
		jQuery("#errorInfo").hide();
		jQuery("#checkInfo").show();
	}
}

  

function agreement(obj){
	jQuery(obj).attr("checked",true);
	alert('必须同意《Wi-Fi使用协议》，不同意请关闭此页面');
}
//Wi-Fi使用协议
function goBack(){
  jQuery("#wifi_agreement,#wx_login2").hide();
  jQuery("#login_box").slideDown(500);
  }
function wifi_xy(){
  jQuery("#login_box").hide();
  jQuery("#wifi_agreement").slideDown(500);
  }
jQuery(function(){
  jQuery(".other_login2>ul>li:last").css("margin-right",0);
 });
