<% require themedCSS(SocialNetworking) %>

<% if ThisPageHasSocialNetworkingLinks %><% if SocialNetworkingLinksDataObjects %>
<div id="SocialNetworkingLinksHolder" class="socialNetworkingHolder">
	<div id="SocialNetworkingLinksHeader" class="socialNetworkingHeader typography"><h5>follow</h5></div>
	<ul id="SocialNetworkingLinksUL" class="socialNetworkingList">
		<% control SocialNetworkingLinksDataObjects %><li class="$FirstLast $Code"><a href="$Link">$Icon.SetHeight(32) <span class="iconTitle">$Title</span></a></li><% end_control %>
	</ul>
</div>
<% end_if %><% end_if %>

