<?php

namespace NSWDPC\UserForms\Submissions;

use DNADesign\ElementalUserForms\Model\ElementForm;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;

/**
 * Add ElementForm support to the page
 * This extension is applied via conditional configuraration
 * @author James
 */
class ElementFormExtension extends \SilverStripe\Core\Extension
{
    /**
     * @var array
     */
    private static $has_one = [
        // @phpstan-ignore class.notFound
        "ElementForm" => ElementForm::class,
    ];

    /**
     * CMS Fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $userDefinedFormField = $fields->dataFieldByName("UserDefinedFormID");
        $elementFormField = DropdownField::create(
            "ElementFormID",
            _t(__CLASS__ . ".FORM_BLOCK", "Form (content block)"),
            // @phpstan-ignore class.notFound
            ElementForm::get()->sort("Title")->map("ID", "Title"),
        )->setEmptyString("");
        if ($userDefinedFormField) {
            $fields->insertAfter("UserDefinedFormID", $elementFormField);
        } else {
            $fields->addFieldToTab("Root.Form", $elementFormField);
        }
    }
}
