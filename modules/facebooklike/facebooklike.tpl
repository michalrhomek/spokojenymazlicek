<script type="text/javascript">

window.fbAsyncInit = function() {ldelim}

FB.Event.subscribe('edge.create', function(targetUrl) {ldelim}
_gaq.push(['_trackSocial', 'facebook', 'like', targetUrl]);
{rdelim});

FB.Event.subscribe('edge.create', function(targetUrl) {ldelim}
_gaq.push(['_trackSocial', 'facebook', 'unlike', targetUrl]);
{rdelim});

FB.Event.subscribe('edge.create', function(targetUrl) {ldelim}
_gaq.push(['_trackSocial', 'facebook', 'send', targetUrl]);
{rdelim});

{rdelim}
</script>
{if $fl_default_hook}
<li>
{else}
{* If you're using a custom hook, you can customize
 the following div with the style you want.
 If the share / send box appear cut off, add position: absolute to the style*}
<div class="facebook_container">
{/if}
	<div class="fb-like" data-send="{$fl_send}" data-width="{$fl_width}" data-show-faces="{$fl_faces}" data-layout="{$fl_layout}" data-font="{$fl_font}" data-action="{$fl_text}" data-colorscheme="{$fl_color}"></div>
{if $fl_default_hook}
</li>
{else}
</div>
{/if}