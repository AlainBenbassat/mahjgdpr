# Mahj RGPD

## Handle newsletter subscribers with personal email address

### Settings screen

URL: /civicrm/mahjgdpr/settings

- Specify the groups to search within.
- Specify the email domains to search for. (e.g. @aol.com, @gmail.com...)
- When the contact was created

Contacts created before the specified date AND with the specified email domain AND who are a member of the specified newsletter group, will be added to the group "RGPD Contacts à relancer".

### Ask for confirmation

The contacts in the group "RGPD Contacts à relancer" will be asked for confirmation via profile "Confirmation RGPD".

Their answer will be stored in the custom group "RGPD".

Contacts who do not reply, will be removed manually from the database.

## Remove old mailings

Old mailings will be removed from the database after 1095 days (= 3 years) using the CiviCR extension https://lab.civicrm.org/extensions/archivemailing.

## Remove old activities

Old activities will be removed from the database after 1095 days (= 3 years) using the API in this extension: Activity.deleteoldactivities.

## Remove old events

Old events will be removed from the database after 1095 days (= 3 years) using the API in this extension: Event.deleteoldevents.

