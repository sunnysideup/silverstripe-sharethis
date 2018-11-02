<% require themedCSS(SocialNetworking, sharethis) %>

<% if $ShowSocialNetworks && $SocialNetworks %>
    <div id="SocialNetworkingLinksHolder" class="socialNetworkingHolder">
        <div id="SocialNetworkingLinksHeader" class="socialNetworkingHeader typography"><h5>Follow us</h5></div>
        <ul id="SocialNetworkingLinksUL" class="socialNetworkingList">
            <% loop SocialNetworks %>
                <li class="$FirstLast $Code">
                    <a href="$Link">$Icon.SetHeight(32)<span class="iconTitle">$Title</span></a>
                </li>
            <% end_loop %>
        </ul>
    </div>
<% end_if %>
