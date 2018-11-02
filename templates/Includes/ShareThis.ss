<% require themedCSS(SocialNetworking, sharethis) %>

<% if $ShowShareIcons && $ShareIcons %>
    <div class="ShareThisHolder socialNetworkingHolder">
        <div class="ShareThisHeader socialNetworkingHeader typography"><h5>Share</h5></div>
        <ul class="ShareThisUL socialNetworkingList fa-ul">
            <% loop $ShareIcons %>
                <% include ShareThisItem %>
            <% end_loop %>
            <% if $IncludeShareAll %>
                <li class="ShareAllLI">$ShareAll.RAW</li>
            <% end_if %>
        </ul>
    </div>
<% end_if %>
