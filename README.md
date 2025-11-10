# GOV.UK Moodle Assessment activity

A GOV.UK Moodle activity module plugin implementing data collection and workflow for an Assessment activity.
Requirements are guided by the Social Work Practice Development Programme (SWPDP) and are subject to ongoing
enhancement through a User-Centred Design (UCD) approach.

**The Assessment activity module plugin has been created to run within a standard Moodle v4.5.5 installation.**

## Activity module plugin installation

### Manual setup

To use this Moodle activity module plugin, [download](https://github.com/DFE-Digital/govuk-moodle-assessment-activity/archive/refs/heads/development.zip)
and extract all files into your Moodle site's activity module plugin directory `/my-moodle-site/public/mod/assessment`.

All top-level files (`index.php`, `version.php`, etc) should be visible in `/my-moodle-site/public/mod/assessment`.

Moodle will detect the Assessment activity module plugin when started. Once installation is confirmed, the required Moodle
database tables will be created:

- mdl_assessment
- mdl_assessment_record
- mdl_assessment_record_item
- mdl_assessment_type
- mdl_assessment_type_item
- mdl_assessment_type_section

Once installed, an Assessment activity can be added to any Moodle course by clicking **Add an activity or resource** and
selecting the newly-available **Assessment** activity.

## Business model

![Asssessment activity module business model](design/business_model.png)

- When an Assessment activity is addded to a Moodle course, an `Assessment` is created. Each `Assessment` has a `name` and is associated with one `AssessmentType`.
- An `AssessmentType` contains zero or more `AssessmentTypeSections`, which can be reordered by changing each `AssessmentTypeSection`'s `displayOrder`.
- An `AssessmentTypeSection` contains zero or more `AssessmentTypeItems`, which can be reordered by changing each `AssessmentTypeItem`'s `displayOrder`.
- `AssessmentTypeItem` is a base class extended by a single class `AssessmentTypeField`, but this can be expanded to more subclasses when required.
- An `AssessmentTypeField` is a data field (text, textbox, date, checkbox) that can be rendered for data collection.
- When a user completes an `Assessment`, an `AssessmentRecord` is created, with as many `AssessmentRecordItems` as there are `AssessmentTypeItems`
for the `Assessment`'s `AssessmentType`'s `AssessmentTypeItems`.