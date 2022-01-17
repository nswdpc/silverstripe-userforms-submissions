<% if $SubmissionSummary.Count > 0 %>

    <h2><%t NSWDPC\UserForms\Submissions\SubmissionListingPage.SUBMISSIONS 'Submissions' %></h2>

    <div class="nsw-table-responsive" role="region" aria-labelledby="submissions-table">
        <table class="nsw-table nsw-table--striped nsw-table--bordered nsw-table--caption-top">
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
                    <td>{$Value}</td>
                <% end_loop %>
                </tr>
            <% end_loop %>
            </tbody>
        </table>
    </div>

<% else %>

    <h2><%t NSWDPC\UserForms\Submissions\SubmissionListingPage.NO_SUBMISSIONS 'No submissions yet' %></h2>

    <p><%t NSWDPC\UserForms\Submissions\SubmissionListingPage.NO_SUBMISSIONS_FOUND 'No submissions found' %></p>

<% end_if %>
