<% if $SubmissionSummary.Count > 0 %>

    <h2><%t NSWDPC\UserForms\Submissions\SubmissionListingPage.SUBMISSIONS_FOUND 'Submissions in this form, most recent first' %></h2>

    <% if $SubmissionSummary.MoreThanOnePage %>
        <div class="pagination">
        <% if $SubmissionSummary.NotFirstPage %>
            <a class="prev" href="{$SubmissionSummary.PrevLink}">Prev</a>
        <% end_if %>
        <% loop $SubmissionSummary.PaginationSummary %>
            <% if $CurrentBool %>
                {$PageNum}
            <% else %>
                <% if $Link %>
                    <a href="{$Link}">{$PageNum}</a>
                <% else %>
                    ...
                <% end_if %>
            <% end_if %>
        <% end_loop %>
        <% if $SubmissionSummary.NotLastPage %>
            <a class="next" href="{$SubmissionSummary.NextLink}">Next</a>
        <% end_if %>
        </div>
    <% end_if %>


    <table>
        <thead>
            <tr>
                <% loop $SummaryFieldLabels %>
                <th>{$Label.XML}</th>
                <% end_loop %>
            </tr>
        </thead>
        <tbody>
            <% loop $SubmissionSummary %>
            <tr>
                <% loop $Up.AvailableSummaryValues($ID) %>
                    <td>{$Value}</td>
                <% end_loop %>
            </tr>
            <% end_loop %>
        </tbody>
    </table>


<% else %>


    <h2><%t NSWDPC\UserForms\Submissions\SubmissionListingPage.NO_SUBMISSIONS_FOUND 'No submissions found' %></h2>


    <p><%t NSWDPC\UserForms\Submissions\SubmissionListingPage.NO_SUBMISSIONS_FOUND_CONTENT 'Sorry, nothing found here.' %></p>

<% end_if %>
