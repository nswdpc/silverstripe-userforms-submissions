# Userform submission viewer

Give nominated members the ability to view submissions collected by a userform. Useful for viewing public submissions in a list or table view.

**Note:** possible leakage of sensitive data collected by a form should be taken into account when providing members the ability to view submissions.

## Mitigations

1. Restrict the number of members who have the relevant permission
1. Apply other restrictions, such as network restrictions
1. Limit the fields that can be displayed in the listing via the "Show in summary gridfield" form option

## Installation

```shell
composer require nswdpc/silverstripe-userforms-submissions
```

## License

[BSD-3-Clause](./LICENSE.md)

## Documentation

1. Create the page `'A page to list form submissions'` in the CMS, update its content
1. Select the Form (page) or Form (content block) that will have its submissions listed then save. [UserDefinedForm](https://github.com/silverstripe/silverstripe-userforms) and [ElementForm](https://github.com/dnadesign/silverstripe-elemental-userforms) (if installed) are supported.
1. Assign, or ask an administrator to assign, a member the permission "View userform submissions on a submission listing page"
1. Provide the page URL to those members

## Configuration

1. Fields shown will be those with "Show in summary gridfield" checked in the form configuration, plus ID and Created.
1. Your theme should provide its own `templates/NSWDPC/UserForms/Submissions/Layout/SubmissionListingPage.ss` layout template. The module provides a basic example.

## Maintainers

+ [dpcdigital@NSWDPC:~$](https://dpc.nsw.gov.au)

## Bugtracker

We welcome bug reports, pull requests and feature requests on the Github Issue tracker for this project.

Please review the [code of conduct](./code-of-conduct.md) prior to opening a new issue.

## Security

If you have found a security issue with this module, please email digital[@]dpc.nsw.gov.au in the first instance, detailing your findings.

## Development and contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

Please review the [code of conduct](./code-of-conduct.md) prior to completing a pull request.
