<%-- Override this template in your theme to provide your own layout --%>

<h1>{$Title.XML}</h1>

<% if $SubmissionSummary.Count > 0 %>

    <div class="nsw-table-responsive" role="region" aria-labelledby="submissions-table">
        <table class="nsw-table nsw-table--striped">
            <caption id="submissions-table"><%t NSWDPC\UserForms\Submissions\SubmissionListingPage.SUBMISSION_TABLE_CAPTION 'Submissions in this form, most recent first' %></caption>
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
                <% loop $Fields %>
                    <td>{$Value.XML}</td>
                <% end_loop %>
                </tr>
            <% end_loop %>
            </tbody>
        </table>
    </div>

<% else %>

    <p><%t NSWDPC\UserForms\Submissions\SubmissionListingPage.NO_SUBMISSIONS_FOUND 'No submissions found' %></p>

<% end_if %>
