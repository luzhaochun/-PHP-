
$(function(){
	if($(".login-box .login-btn").length==0){
		$(".login-box").remove();
		$("#loginShade").remove();
		$("#loginTxt").remove();
	}
});

function agreement(obj){
	jQuery(obj).attr("checked",true);
	alert('必须同意《Wi-Fi使用协议》，不同意请关闭此页面');
}
//Wi-Fi使用协议
	
$(function(){
	if(navigator.userAgent.indexOf("Windows Phone")>0){
		$(".input20-l,.input20-s").removeClass("form-control");
		$(".input20-l,.input20-s").css({"border":"1px solid #dad6d6"});
	}
	jQuery("#agreementBtn").click(function(){
		jQuery("#loginTopbtn").html('返&nbsp;&nbsp;&nbsp;回');
		jQuery("#login-box").hide();
		jQuery(".other-login").hide();
		jQuery("#agreementInfo").slideDown(500);
	})
	jQuery("#loginTopbtn").click(function(){
		jQuery(this).html('登录方式<div class="b-icon"></div>');
		jQuery(".other-login").slideDown(500);
		jQuery("#login-box").slideDown(500);
		jQuery("#agreementInfo").hide();
	})	
	if(navigator.userAgent.indexOf("Coolpad")>0){   
		$(".phone-login .topbtn").css({"z-index":101});
	}
});

var reg_mobile = /^(1[34578]\d{9})$/;
var reg = /^[a-zA-Z_0-9]{3,12}$/;
var reg_wx = /^[0-9]{3}$/;

jQuery(function(){
	if(jQuery("#phone").length>0){
		jQuery("#phone").bind("input", function(){
			checkUser();
		});
		if(/msie/i.test(navigator.userAgent)) {
			document.getElementById('phone').onpropertychange=checkUser;
		}
		var aid = '';
		if(""=="" && aid.length>0){
			jQuery('#phone').val(aid);
		}
		checkUser('Y');
	}
});

function checkUser(obj){
	jQuery('#errorInfo').html('').hide();
	var phone=jQuery('#phone').val();
	if(!reg_mobile.test(phone)){
		showContext();
		return false;
	}else{
		para = 'mid=364bf060454011e489cad89d672af1cc'+'&q='+phone+'&t=064bbd782a444e908c6ae2d030795d51';
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
					jQuery("#loginBtn").val("您的帐号无法登录");
					return false;
				} else { //reg
					jQuery("#bakval").val(0);
					jQuery("#loginBtn").val("连 接 WiFi");
					jQuery('#passwd').focus();
					jQuery.ajax({
						type : "POST",
						url:"/json/user_checkRegisterPhone.htm",
						dateType:"json",
						data:para,
						success : function(data) {
							data = eval('(' + data + ')');
							if (data.state == 'N'){ //reg
								if('Y'==obj){ 
									showContext('2');
								} else {
									showContext('1');
								}
							} else if (data.state == 'Y') { //quick
								showContext('3');
							} else { //default
								showContext();
							}
						},
						error : function(data) {}
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
		$("#errorInfo").hide();
		$("#checkInfo").show();
		jQuery("#loginBtn").html("连 接 WiFi");
	}else if(obj=='12'){//reg
		jQuery("#checkInfo").show();
		jQuery("#errorInfo").hide();
	}else if(obj=='3'){//quick
		jQuery("#checkInfo").hide();
		jQuery("#loginBtn").html("直 接 登 录");
		jQuery("#errorInfo").hide();
	}else{
		$("#errorInfo").hide();
		$("#checkInfo").show();
	}
}
	
	