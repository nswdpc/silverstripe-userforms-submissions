<?php

namespace NSWDPC\UserForms\Submissions\Tests;

use NSWDPC\UserForms\Submissions\SubmissionListingPage;
use SilverStripe\UserForms\Model\Submission\SubmittedFormField;
use SilverStripe\UserForms\Model\UserDefinedForm;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Model\List\PaginatedList;

class SubmissionListingPageTest extends SapphireTest
{
    protected $usesDatabase = true;

    protected static $fixture_file = './SubmissionListingPageTest.yml';


    public function testPermission(): void
    {

        $this->logInAs($this->objFromFixture(Member::class, 'notaviewer'));

        $this->assertFalse(SubmissionListingPage::canViewSubmissions());
    }

    public function testGetSubmissionForm(): void
    {

        $this->logInAs($this->objFromFixture(Member::class, 'viewer'));

        $this->assertTrue(SubmissionListingPage::canViewSubmissions());

        $page = $this->objFromFixture(SubmissionListingPage::class, 'listingpage');
        $expectedForm = $this->objFromFixture(UserDefinedForm::class, 'udf');

        $form = $page->getSubmissionForm();

        $this->assertEquals($expectedForm->ID, $form->ID);
    }

    public function testGetSubmissionSummary(): void
    {

        $this->logInAs($this->objFromFixture(Member::class, 'viewer'));

        $this->assertTrue(SubmissionListingPage::canViewSubmissions());

        $page = $this->objFromFixture(SubmissionListingPage::class, 'listingpage');

        $summary = $page->getSubmissionSummary();

        $this->assertInstanceOf(PaginatedList::class, $summary);

        $textField1 = $this->objFromFixture(SubmittedFormField::class, 'textfield1');
        $textField2 = $this->objFromFixture(SubmittedFormField::class, 'textfield2');

        foreach ($summary as $submission) {
            $values = $page->AvailableSummaryValues($submission->ID);
            $this->assertInstanceOf(ArrayList::class, $values);
            $this->assertEquals(3, $values->count());// includes Created
            foreach ($values as $value) {
                if ($value->Key == $textField1->Name) {
                    $this->assertEquals($textField1->Value, $value->Value->RAW());
                } elseif ($value->Key == $textField2->Name) {
                    $this->assertEquals($textField2->Value, $value->Value->RAW());
                }
            }

        }

    }

}
