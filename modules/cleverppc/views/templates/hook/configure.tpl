<?php
/**

* NOTICE OF LICENSE

*

* This file is licenced under the Software License Agreement.

* With the purchase or the installation of the software in your application

* you accept the licence agreement.

*

* You must not modify, adapt or create derivative works of this source code

*

*  @author    Carlos García Vega

*  @copyright 2010-2015 CleverPPC S.L.

*  @license   LICENSE.txt

*/
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style type="text/css">
@import url('https://fonts.googleapis.com/css?family=Montserrat:300, 400,700,800,900');


*{
	padding: 0px;
	margin: auto;
}
body{
	
	width: 100% !important;
	
	margin: auto;

	background-color: #FFFFFF !important;
}
h1, h2, p{
	font-family:  'Montserrat', sans-serif !important;
	
}
html{
	overflow-x: hidden;
	background-color: #FFFFFF;}
	.clever_column_25 {

		width: 30%;
		text-align: justify;
		padding-right: 1%;
		padding-left: 1%;
		margin: 0 !important;

	}
	#text_ggads_container{
		margin: 0 auto 0 0 !important;
		

	}
	.right_float{
		float:right;
	}
	#prestashop_clever_header{
		height: 5%;
		display: flex;
		justify-content: space-between;
		background-color: #FFFFFF !important; 
		
		padding-left: 1%;
		padding-right: 1%;

	}
	#clever_footer{
		background-image: linear-gradient(to right, #3A006C, #500086, #51009E);
		height: 5%;
		
		width: 100%;
	}

	.btn-header-getggads{
		align-self: right;
	}
	.clever_column_75 {

		width: 50%;
		text-align: justify;
		padding-right: 1%;
		padding-left: 1%;
		display: inline-block;
		margin: 0 !important;
	}
	.left_float{
		float: left;
	}
	.footer-ggads{
		font-size:  200% !important;
		color: #FFFFFF;
		position: fixed;
		bottom: 0;
		width: 100%;
	}
	#content{
		padding-right: 0%;
		padding-left: 0%;
	}
	.container_master{
		height: 60%;
		display: flex;
		justify-content: center;
		align-items: center;
		flex-direction: row;
		margin: auto;
		font-family:  'Montserrat', sans-serif !important;
	}

	.blue_gradient_background{
		background-image: linear-gradient(to right, #3A006C, #500086, #51009E);
		height: 5%;
	}

	.h-8{
		height: 2%;
	}
	#adword{
		width: 100%;
	}
	.w-50{
		width: 50%;
	}
	.ripple {
		background-position: center;
		transition: background 0.8s;
	}
	.ripple:hover {
		background: #ff3385 radial-gradient(circle, transparent 1%, #ff3385  1%) center/15000%;
	}
	.ripple:active {
		background-color:  #ff3385;
		background-size: 100%;
		transition: background 0s;
	}
	#footer_text{
		position: absolute;
		bottom: 0;
		width: 100%;
	}

	.left_column {
		padding-right: 10%;
		padding-left: 10%;
		text-align: justify;
		-webkit-font-smoothing: antialiased;


	}
	.w-50{
		width: 50%;
		text-align: end;
	}	
	.subtitle_clever{
		line-height: 1.6 !important;
		/*padding: 1% 3% 1% 3% ;*/
		text-align: left;

	}

	
	.text_nav{
		
		text-align: center; 
		white-space: nowrap
		margin: 0 !important;
		padding: 1%;
		line-height: 0px !important;
		vertical-align: middle;
		display: inline-block;
	}
	.right_column_img{

		padding-left: 5%;
		width: 50%;
		-webkit-font-smoothing: antialiased;


	}

	.left_column_start {



		margin-top: 5%;
		text-align: justify;
		-webkit-font-smoothing: antialiased;

		float: right !important;


	}

	.clever_show_one_image{
		display: none;
	}

	#

	.left_column_img  {
	/*padding-right: 10%;
	padding-left: 10%;*/
	text-align: right; 
	vertical-align: middle;
	width: 40%;
	display: block;
	

	-webkit-font-smoothing: antialiased;


}

#navbar_button:hover{
	box-shadow: 0 16px 19px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
}

#alternative_text_clever{
	display: none;

}

.right_column_start {
	height: 450px;

	background-repeat: no-repeat;
	background-size: auto;
	background-size: 800px 500px;
	background-position: center;
	margin-top: 4%;
	margin-left: 5%;
	-webkit-font-smoothing: antialiased;


}
.prestashop_clever_header{
	height: 10%;
}

/* Clear floats after the columns */
.clever_row:after {
	content: "";
	display: table ;
	clear: both;
	margin-bottom: 2%;
}

.subsubtitle {
	font-size: 22px;
	line-height: 30px;
	-webkit-font-smoothing: antialiased;

}
.subtitle {
	font-size: 30px;
	line-height: 30px;

	color: #FFFFFF;
	-webkit-font-smoothing: antialiased;
}


.clever_content{

	
	width: 100%;
	
}

.btn-full-h{
	height: 100%;
}

.btn {
	display: inline-block;
	font-weight: 400;
	text-align: center;
	white-space: nowrap;
	vertical-align: middle;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	border: 1px solid transparent;

	font-size: 1rem;
	line-height: 1.5;
	border-radius: .25rem;
	transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
	padding:0.3em 1.2em;
	margin:0 0.1em 0.1em 0;
	text-align:center;
}
.btn:hover{
	box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
}

.btn:active{
	background-color: red;
}

.btn-success{
	background-color: #E8005E !important;
	color: #FFFFFF !important;
	border: none !important; 

}

.btn-success_outline{
	background-color: #FFFFFF ;
	color: #E8005E ;
	border: #E8005E 1px solid ; 
	padding: 1em 2em 1em 2em;
	border-radius: 10px;
	font-sze: 30px;


}

.btn-success_outline:hover{
	box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
}

.btn-sucess:hover{
	box-shadow: 0 62px 32px rgba(0,0,0,0.12), 0 11px 21px rgba(0,0,0,0.24);
}

.font-large {
	
	font-size: 16px;
	font-weight: 300;
	line-height: 22px;
	text-align: justify;
	margin-top: 20px;
	margin-bottom: 20px;
	-webkit-font-smoothing: antialiased;

}
#coupon_btn:hover{
	box-shadow: 0 6px 3px rgba(0,0,0,0.12), 0 11px 21px rgba(0,0,0,0.24);

}

.clever_column_75:after {
	clear:both;
	content:" ";
	display:block;
}

#content.bootstrap{
	padding: 0% !important; 

}
.medium_text{
	font-size: 17px !important;
}
.container_clever_cleverF{
	padding: 1% 7% 1% 6%;
}
.image_ggads{
	height: 40%;
}
.parent{
	justify-content: center;
	align-items: center;
	width: 90%;
}

.clever_row {
	display: inline-block;

}
#mobile_logos{
	display: none;
}
#image-ggads{
	text-align: left;
}
@media only screen and (max-width: 1157px){
	#header_btn{
		display: none;
	}
	#image-ggads{
		width: 100%;
		clear: both !important;
		text-align: center;
	}
	#text_ggads_container{
		text-align: center;
		margin: 0px !important;
		width: 100%;
	}
	#clever_subtitle_1{
		font-size: 2vw !important;
	}
	#little_text_2{
		font-size: 1.25vw !important;
		width: 80%;
		text-align: center;
	}

	


}
@media only screen and (max-width: 1280px){
	#header_title{
		font-size: 2vw !important;
	}
	#little_text{
		display: none !important;
	}
	#little_text_2{
		display: inline-block !important;
		font-size: 1.25vw;
		width: 80%;
		text-align: center !important;
	}
	#clever_title_2{
		font-size: 2.5vw !important;		
	}
	#clever_subtitle_2{
		font-size: 1.5vw !important;	

	}
	.clever_content{
		padding: 0 !important;
	}

	.container_clever{
		padding: 1% 3% 1% 3%;

	}

	#header_title{
		font-size: 200% !important;
	}
	#clever_title_1{
		font-size: 3vw !important;	
		font-weight: 700;	

	}

	#text_clever_ggads{
		display: none !important;
	}

	.clever_content{
		padding: 0 !important;
		text-align: center;
	}

	#alternative_text_clever{
		display: inline-block;
		margin: auto;
		width: 100%;
		text-align: center;
	}
	#clever_subtitle_1{
		font-size: 2vw !important;
		margin-bottom: 0.2 !important;
		text-align: center;	
		width: 80%;
	}
	
	.clever_column_25{
		width: 100%!important;
		margin: auto !important;
		text-align: center;
	}
	.left-float{
		float: none !important
		clear: both;
	}
	.right-float{
		float: none !important
		clear: both;
	}
	.clever_show_one_image{
		display: inline-block;
	}
	.clever_column_75{
		width: 100%!important;
	}

	.clever_column_75:after {
		clear:both;
		content:" ";
		display:block;
	}

	#id_ads_images{
		display: none !important;
	}

	.left_column_img{
		align-content: right;
		width: 50% !important;

	}
	.right_column_img{
		width: 50% !important;
		pading-left: 5% !important;
	}
	#adword_header{

		
		letter-spacing: -1px !important;
		font-weight: bold;
	}
	.font-large{
		font-size: 100%;
		
		padding-right: 0%;
	}
	.btn{
		padding: 0.1em 0.5em;
		white-space: normal;

	}
	.medium_text{
		fontsize: 120%;
	}
	#adword{
		margin-top: 150px !important;
	}
	.right_column_start{
		height: 300px;
		background-size: contain;

		margin-top: 50px !important;
		
	}
	.left_column_start{
		margin-top: 0%;
		width: 40% !important;
	}
	#adword_img{
		width: 300px;
	}


	.clever_column{
		width: auto;
		padding-right: 0%;
		padding-left: 0%;
	}

	.left_column{
		padding-right: 10%;
		padding-left: 10%;
	}

	.container_master{
		display: inline-block;
	}
	#adword{
		visibility: none;
	}

	
}

@media only screen and (max-width: 700px){
	.container_clever{
		padding: 1% 3% 1% 3%;

	}
	#clever_title_1{

		font-size: 4vw !important;		
	}

	#clever_subtitle_1{
		font-size: 2vw !important;
		margin-bottom: 0.2 !important;
		font-size: 3vw !important;

	}

	#textggads_header{
		font-size: 2vw;
	}
	#adword_header{

		font-size: 220% !important;
		letter-spacing: -1px !important;
		text-align: center;
	}
	.font-large{
		font-size: 120%;
		
		padding-right: 0%;
	}
	.btn{
		padding: 0.1em 0.5em;
		white-space: normal;

	}
	.medium_text{
		fontsize: 190%;
	}
	#adword{
		visibility: none;
	}
	.right_column_start{
		display: none;
		
	}
	.left_column_start{
		margin-top: 0%;
		width: 100%;
		padding-right: 0%;
	}
	

	.subtitle{
		letter-spacing: -1.9px;
		color: #FFFFFF;
	}

	#mobile_logos{
		display: inline-block;
		width: 100%;
		text-align: center;
	}
	.clever_column{
		width: auto;
		padding-right: 0%;
		padding-left: 0%;
		
	}
}
@media only screen and (max-width: 500px){
	.clever_column{
		width: auto !important;
		padding-right: 10% !important;
		padding-left: 10% !important;
	}
	.right_column_img{
		padding-right: 0% !important;
		padding-left : 0% !important;
		width: 40% !important;
	}
	.left_column_img{
		padding-right: 0% !important;
		padding-left : 25% !important;
		width: auto!important;

	}
}


</style>



<div class="blue_gradient_background" id="blue_header"></div>


<div class="clever_header"  id="clever_head">

	<div class="adwordsd_h">
		<div style="clear: both; display: table;"></div>
		<div class="clever_content">
			<div class="" id="prestashop_clever_header">
				<div id="text_ggads_container" class="btn-full-h">
					<div class="left_float" id="image-ggads">
						<img style="margin-right: 10px; height: 278%;" src="https://res.cloudinary.com/cleverppc/image/upload/v1551110789/image_vp4mbe.png" >

					</div>
				</div>
				<div id="header_btn" style="margin-top: 2% !important; margin-right: 0 !important; margin-bottom: 0 !important; margin-left: auto !important; ">
					<button class="btn-success_outline"  onclick="beginClever();" style="font-weight: 400; font-family: 'Montserrat', 'sans-serif" >
						Begin installation
					</button>
				</div>
			</div>

			<div class="container_master">

				<div class="clever_show_one_image"  style=" text-align: center; width: 100%;">
					<img style="width: 100%; height: 300px; " src="https://res.cloudinary.com/cleverppc/image/upload/v1550760255/woman-server_y0hvsd.svg" />
				</div>
				<div style="clear: both; margin: 0px !important"></div>

				<div id="alternative_text_clever">
					<h1 style=" font-weight:  900 !important; font-size: 1.3vw ; letter-spacing: -0.06vw !important;" id="clever_title_1">
						Start your ad on Google today!
					</h1>


					<div style="text-align: center;">
						<h2 class="subtitle_clever" style=" font-weight:  200 !important; font-size: 3vw; letter-spacing: -0.05vw !important; margin-bottom: 1em;" id="clever_subtitle_1">
						Advertise your products all over Google's huge network and get more traffic. Install now and get a promotional coupon* up to 120&#8364; to spend on your campaigns. </h2>
					</div>
					<button class="btn-success ripple" id="coupon_btn" style="color: #FFFFFF; padding: 1em 2em 1em 2em;margin-top: 2%;  text-decoration: none; font-family: 'Montserrat', sans-serif !important; font-weight: 600;  margin-bottom: 1em; font-size: 2vw;" onclick="beginClever();" target="_blank" >Start installation and get the coupon! </button>
				</div>


				<div class="clever_column_25 left_float" id="text_clever_ggads" style="display: inline-block; text-align: left;">

					<h1 style=" font-weight:  900 !important; font-size: 1.5vw ; letter-spacing: -0.06vw !important;  margin-bottom: 2%; " id="clever_title_2" >
						Start your ad on Google today!
					</h1>


					<div style="text-align: center;">
						<h1  class="subtitle_clever" style=" font-weight:  400 !important; font-size: 1vw;  letter-spacing: -0.05vw !important; margin-bottom: 1em;" id="clever_subtitle_2">
						Advertise your products all over Google's huge network and get more traffic. Install now and get a promotional coupon* up to 120&#8364; to spend on your campaigns. </h1>
					</div>
					<div style="padding: 0% 3% 0% 3%">
						<button class="btn-success ripple" id="coupon_btn" style="color: #FFFFFF; padding: 1em 2em 1em 2em; text-decoration: none; font-family: 'Montserrat', sans-serif !important; font-weight: 600;  margin-bottom: 1em; font-size: 0.75vw;" href='/' onclick="beginClever();" target="_blank" >Start installation and get the coupon!</button>
					</div>
				</div>

				<div class="clever_column_75 right_float" id="id_ads_images" style="display: flex;">

					<div  id="adword">

						<img style="float: left;  width: 50%; height: 500px; " src="https://res.cloudinary.com/cleverppc/image/upload/v1551255965/charts-and-graphs-prestahop-clever_logo-colour_z1gh6c.svg" />
						<img style="float:right; width: 50%; height: 500px; " src="https://res.cloudinary.com/cleverppc/image/upload/v1550760255/woman-server_y0hvsd.svg" />


					</div>
					<div style="clear: both; display: table;">

					</div>



				</div>

				<div style="padding: 0 3% 0 3%; font-family: 'Montserrat', 'sans-serif'; display: none; margin-bottom: 4em;" id="little_text_2">
					<span>
						*The coupon will be automatically applied only if the Google Ads account selected complies with Google's requirements.
					</span>
				</div>


				

			</div>
			<div style="padding: 0 3% 0 3%; font-family: 'Montserrat', 'sans-serif'; margin-bottom: 4em;" id="little_text">
				<span>
					 
				</span>
			</div>


			
		</div>


	</div>
	<div id="footer_text">
		<div style="padding: 0 3% 0 3%; font-family: 'Montserrat', 'sans-serif'; margin-bottom: 4em;" id="little_text">
			<span>
				*The coupon will be automatically applied only if the Google Ads account selected complies with Google's requirements.
			</span>
		</div>
		<div class="blue_gradient_background footer-ggads"  id="clever_footer" style="display: block !important;">

		</div>
	</div>
	
</div>



<script>

	if (!!document.getElementsByClassName('page-head')[0]){
		var topoffsetHeight = document.getElementsByClassName('page-head')[0].offsetHeight;
		var breadCrumbHeight = document.getElementsByClassName('page-breadcrumb')[0].offsetHeight;
		var page_header = document.getElementById('blue_header');
		page_header.style.marginTop = (topoffsetHeight - breadCrumbHeight) + 'px';
		var clever_content = document.getElementsByClassName('clever_content')[0];


	}
	



</script>
</body>

