<li class="icon-for{$Key} ">
	<% if FAIcon %><i class="fa-li fa $FAIcon fa-1x"></i><% end_if %>
	<a href="$URL" <% if OnClick %>onclick="$OnClick" <% end_if %>title="$Title.ATT">
		<% if ImageSource %><img src="$ImageSource" alt="$Title"<% if UseStandardImage %> width="16" height="16"<% end_if %> /><% end_if %>
		<span class="iconTitle">$Title</span>
	</a>
</li>
