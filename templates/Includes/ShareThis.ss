<% require themedCSS(SocialNetworking, sharethis) %>

<% if ShowShareIcons %><% if ShareIcons %>
	<div class="ShareThisHolder socialNetworkingHolder">
		<div class="ShareThisHeader socialNetworkingHeader typography"><h5>Share</h5></div>
		<ul class="ShareThisUL socialNetworkingList">
			<% loop ShareIcons %><li class="icon-for{$Key}"><a href="$URL" <% if OnClick %>onclick="$OnClick"<% end_if %> title="$Title"><img src="$ImageSource" alt="$Title"<% if UseStandardImage %> width="16" height="16"<% end_if %> /> <span class="iconTitle">$Title</span></a></li><% end_loop %>
			<% if IncludeShareAll %><li class="ShareAllLI">$ShareAll</li><% end_if %>
		</ul>
	</div>
<% end_if %><% end_if %>
