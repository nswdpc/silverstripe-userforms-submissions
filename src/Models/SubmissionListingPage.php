<?php

namespace NSWDPC\UserForms\Submissions;

use DNADesign\ElementalUserForms\Model\ElementForm;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Security\Security;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\UserForms\Model\UserDefinedForm;
use SilverStripe\View\ArrayData;

/**
 * A page to handle display of listing submissions
 * @author James
 * @property int $UserDefinedFormID
 * @method \SilverStripe\UserForms\Model\UserDefinedForm UserDefinedForm()
 */
class SubmissionListingPage extends \Page implements PermissionProvider
{
    public const PERMISSION_VIEW_LISTINGS = "USERFORM_SUBMISSION_VIEWER";

    private static string $icon_class = "font-icon-p-list";

    /**
     * Singular name for CMS
     */
    private static string $singular_name = "A page to list form submissions";

    /**
     * Description for CMS
     */
    private static string $description = "List form submissions for review by users holding required permissions";

    /**
     * Plural name for CMS
     */
    private static string $plural_name = "Pages to list form submissions";

    /**
     * table name
     */
    private static string $table_name = "SubmissionListingPage";

    private static array $has_one = [
        "UserDefinedForm" => UserDefinedForm::class,
    ];

    /**
     * @var array
     */
    private $_cache_summary_values = [];

    private array $_cache_summary_fields = [];

    private $_cache_submission_form;

    /**
     * CMS Fields
     */
    #[\Override]
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab(
            "Root.Form",
            DropdownField::create(
                "UserDefinedFormID",
                _t(self::class . ".FORM_PAGE", "Form (page)"),
                UserDefinedForm::get()->sort("Title")->map("ID", "Title"),
            )->setEmptyString(""),
        );
        return $fields;
    }

    /**
     * Return whether the current member can view submissions
     */
    public static function canViewSubmissions(): bool
    {
        $can = Permission::checkMember(
            Security::getCurrentUser(),
            self::PERMISSION_VIEW_LISTINGS,
        );
        return (bool) $can;
    }

    /**
     * Reset cache properties on write
     */
    #[\Override]
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->_cache_summary_fields = [];
        $this->_cache_summary_values = [];
        $this->_cache_submission_form = null;
    }

    /**
     * Retrieve the form, based on the selection made
     * @return mixed
     */
    public function getSubmissionForm()
    {
        if (!self::canViewSubmissions()) {
            return false;
        }

        if ($this->_cache_submission_form) {
            return $this->_cache_submission_form;
        }

        $form = $this->UserDefinedForm();
        // ElementForm support
        if (
            (!$form || !$form->isInDB()) &&
            class_exists(ElementForm::class) &&
            $this->hasMethod("ElementForm")
        ) {
            $form = $this->ElementForm();
        }

        $this->_cache_submission_form = $form;
        return $this->_cache_submission_form;
    }

    /**
     * Retrieve submissions linked to the form
     */
    public function getSubmissions(): ?DataList
    {
        if (!self::canViewSubmissions()) {
            return null;
        }

        $submissions = null;
        if ($form = $this->getSubmissionForm()) {
            $submissions = $form->Submissions()->sort("Created DESC");
        }

        return $submissions;
    }

    /**
     * Get the form submissions as an ArrayList, with fields based on getSummaryFields
     */
    public function getSubmissionSummary(): ?PaginatedList
    {
        $submissions = $this->getSubmissions();
        if (!$submissions instanceof \SilverStripe\ORM\DataList) {
            return null;
        }

        $request = Controller::curr()->getRequest();
        $paginatedList = PaginatedList::create($submissions, $request);
        $summaryFields = $this->getSummaryFields();
        foreach ($paginatedList->getIterator() as $submittedForm) {
            $fields = ArrayList::create();
            foreach ($summaryFields as $field => $label) {
                $value = $submittedForm->relField($field);
                $summaryFieldRecord = ArrayData::create([
                    "Key" => $field,
                    "Label" => $label,
                    "Value" => $value,
                ]);
                $fields->push($summaryFieldRecord);
            }

            $this->_cache_summary_values[$submittedForm->ID] = $fields;
        }

        return $paginatedList;
    }

    /**
     * Return the summary values for a submission
     */
    public function AvailableSummaryValues($id): ?ArrayList
    {
        if (isset($this->_cache_summary_values[$id])) {
            return $this->_cache_summary_values[$id];
        } else {
            return null;
        }
    }

    /**
     * Return fields that are marked as viewable in the summary
     */
    protected function getSummaryFields(): array
    {
        if (count($this->_cache_summary_fields) > 0) {
            return $this->_cache_summary_fields;
        }

        $form = $this->getSubmissionForm();
        $fields = [];
        if (empty($form->ID)) {
            return $fields;
        }

        // base fields
        $fields["Created.Nice"] = _t(self::class . ".CREATED", "Created");

        $editableFields = $form->Fields()->filter(["ShowInSummary" => 1]);
        foreach ($editableFields as $field) {
            $fields[$field->Name] = $field->Title ?: $field->Name;
        }

        $this->_cache_summary_fields = $fields;
        return $this->_cache_summary_fields;
    }

    /**
     * Get the form submissions as an ArrayList, with fields based on getSummaryFields
     * Accessible from template as {$SummaryFieldLabels}
     */
    public function getSummaryFieldLabels(): ArrayList
    {
        $labels = ArrayList::create();
        $summaryFields = $this->getSummaryFields();
        foreach ($summaryFields as $field => $label) {
            $labels->push(
                ArrayData::create([
                    "Key" => $field,
                    "Label" => $label,
                ]),
            );
        }

        return $labels;
    }

    /**
     * @return array
     */
    #[\Override]
    public function providePermissions()
    {
        return [
            self::PERMISSION_VIEW_LISTINGS => [
                "name" => _t(
                    self::class . ".PERMISSION_VIEW_LISTINGS_DESCRIPTION",
                    "View userform submissions on a submission listing page",
                ),
                "category" => _t(
                    self::class . ".PERMISSION_VIEW_LISTINGS_CATEGORY",
                    "Forms",
                ),
            ],
        ];
    }
}
