<?php

namespace NSWDPC\UserForms\Submissions;

use DNADesign\ElementalUserForms\Model\ElementForm;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Security;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\UserForms\Model;
use SilverStripe\UserForms\Model\UserDefinedForm;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\View\ArrayData;

/**
 * A page to handle display of listing submissions
 * @author James
 */
class SubmissionListingPage extends \Page implements PermissionProvider
{

    const PERMISSION_VIEW_LISTINGS = 'USERFORM_SUBMISSION_VIEWER';

    /**
     * @var string
     */
    private static $icon_class = 'font-icon-p-list';

    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'A page to list form submissions';

    /**
     * Description for CMS
     * @var string
     */
    private static $description = 'List form submissions for review by users holding required permissions';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = 'Pages to list form submissions';

    /**
     * table name
     * @var string
     */
    private static $table_name = 'SubmissionListingPage';

    /**
     * @var array
     */
    private static $has_one = [
        'UserDefinedForm' => UserDefinedForm::class,
        'ElementForm' => ElementForm::class,
    ];

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab(
            'Root.Form',
            DropdownField::create(
                'UserDefinedFormID',
                _t(__CLASS__ . '.FORM_PAGE','Form (page)'),
                UserDefinedForm::get()->sort('Title')->map('ID','Title')
            )->setEmptyString('')
        );

        // if element form is installed
        if(class_exists(ElementForm::class)) {
            $fields->addFieldToTab(
                'Root.Form',
                DropdownField::create(
                    'ElementFormID',
                    _t(__CLASS__ . '.FORM_BLOCK','Form (content block)'),
                    ElementForm::get()->sort('Title')->map('ID','Title')
                )->setEmptyString('')
            );
        }
        return $fields;
    }

    /**
     * Return whether the current member can view submissions
     */
    public static function canViewSubmissions() : bool {
        $can = Permission::checkMember( Security::getCurrentUser(), self::PERMISSION_VIEW_LISTINGS );
        return $can ? true : false;
    }

    /**
     * Retrieve the form, based on the selection made
     * @return mixed
     */
    public function getSubmissionForm() {
        if(!self::canViewSubmissions()) {
            return false;
        }
        $form = $this->UserDefinedForm();
        if(!$form || !$form->exists()) {
            $form = $this->ElementForm();
        }
        return $form;
    }

    /**
     * Return the record id for the submission
     */
    public function getSubmissionFormID() {
        $form = $this->getSubmissionForm();
        if(!empty($form->ID)) {
            return $form->ID;
        } else {
            return "";
        }
    }

    /**
     * Retrieve submissions linked to the form
     * @return mixed
     */
    public function getSubmissions() {
        if(!self::canViewSubmissions()) {
            return false;
        }
        $form = $this->getSubmissionForm();
        $submissions = $form ? $form->Submissions()->sort('Created DESC') : false;
        return $submissions;
    }

    /**
     * Get the form submissions as an ArrayList, with fields based on getSummaryFields
     * @return ArrayList
     */
    public function getSubmissionSummary() : ArrayList {
        $list = ArrayList::create();
        $submissions = $this->getSubmissions();
        $summaryFields = $this->getSummaryFields();
        if(!$submissions) {
            return $list;
        }
        foreach($submissions as $submission) {
            $fields = ArrayList::create();
            foreach($summaryFields as $field => $label) {
                $fields->push(ArrayData::create([
                    'Key' => $field,
                    'Label' => $label,
                    'Value' => $submission->relField($field)
                ]));
            }
            $entry = ArrayData::create([
                'Fields' => $fields
            ]);
            $list->push($entry);
        }
        return $list;
    }

    /**
     * Return fields that are marked as viewable in the summary
     */
    protected function getSummaryFields() : array {
        $form = $this->getSubmissionForm();
        $fields = [];
        if(empty($form->ID)) {
            return $fields;
        }

        // base fields
        $fields['ID'] = _t(__CLASS__ . '.ID','ID');
        $fields['Created.Nice'] = _t(__CLASS__ . '.CREATED','Created');

        if($editableFields = EditableFormField::get()->filter(array('ParentID' => $form->ID))) {
            foreach ($editableFields as $field) {
                if ($field->ShowInSummary) {
                    $fields[$field->Name] = $field->Title ?: $field->Name;
                }
            }
        }
        return $fields;
    }

    /**
     * Get the form submissions as an ArrayList, with fields based on getSummaryFields
     * Accessible from template as {$SummaryFieldLabels}
     * @return ArrayList
     */
    public function getSummaryFieldLabels() : ArrayList {
        $labels = ArrayList::create();
        $summaryFields = $this->getSummaryFields();
        foreach($summaryFields as $field => $label) {
            $labels->push(ArrayData::create([
                'Key' => $field,
                'Label' => $label
            ]));
        }
        return $labels;
    }


    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            self::PERMISSION_VIEW_LISTINGS => [
                'name' => _t(__CLASS__ . '.PERMISSION_VIEW_LISTINGS_DESCRIPTION', 'View userform submissions on a submission listing page'),
                'category' => _t(__CLASS__ . '.PERMISSION_VIEW_LISTINGS_CATEGORY', 'Forms')
            ]
        ];
    }

}
