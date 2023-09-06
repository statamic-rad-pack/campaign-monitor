import FormFields from './components/fieldtypes/FormFieldsFieldtype.vue';
import CustomFieldsField from './components/fieldtypes/CustomFieldsFieldtype.vue';
import UserFieldsField from './components/fieldtypes/UserFieldsFieldtype.vue';


Statamic.booting(() => {
    Statamic.$components.register('campaign_monitor_form_fields-fieldtype', FormFields);
    Statamic.$components.register('campaign_monitor_custom_fields-fieldtype', CustomFieldsField);
    Statamic.$components.register('campaign_monitor_user_fields-fieldtype', UserFieldsField);
});
