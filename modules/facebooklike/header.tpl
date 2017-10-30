<div id="fb-root"></div>
<script type="text/javascript">

(function(d, s, id) {ldelim}
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/{$fl_lang_code}/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
{rdelim}(document, 'script', 'facebook-jssdk'));

</script>
{if $fl_default_image}
<meta property="og:image" content="{$fl_default_image}" /> 
<link rel="image_src" href="{$fl_default_image}" />
{/if}