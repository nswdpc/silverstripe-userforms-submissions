<?php

namespace NSWDPC\UserForms\Submissions;

use SilverStripe\Control\Middleware\HTTPCacheControlMiddleware;

/**
 * The controller for the page to handle display of listing submissions from forms
 * @author James
 * @extends \PageController<\NSWDPC\UserForms\Submissions\SubmissionListingPage>
 */
class SubmissionListingPageController extends \PageController
{
    /**
     * @inheritdoc
     */
    #[\Override]
    public function init()
    {
        if(!SubmissionListingPage::canViewSubmissions()) {
            return $this->httpError(403);
        }

        HTTPCacheControlMiddleware::singleton()->disableCache(true);
        parent::init();
    }
}
