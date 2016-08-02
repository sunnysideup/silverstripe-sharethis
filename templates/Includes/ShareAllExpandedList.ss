<% if ShowShareIcons %><% if ShareAllExpandedList %>
	<div class="shareAllExpandedList">
		<div class="shareAllExpandedListHeader"><h5><a href="#" class="shareAllExpandedListLink share">Share</a></h5></div>
		<ul class="shareAllExpandedListUL">
			<li class="shareAllExpandedListULClose"><a href="#">close</a></li>
			<% loop ShareAllExpandedList %><% include ShareThisItem %><% end_loop %>
		</ul>
	</div>
<% end_if %><% end_if %>
