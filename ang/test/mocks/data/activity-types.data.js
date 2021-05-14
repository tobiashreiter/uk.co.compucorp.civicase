(function () {
  var module = angular.module('civicase.data');

  CRM['civicase-base'].activityTypes = {
    1: {
      label: 'Meeting',
      icon: 'fa-slideshare',
      name: 'Meeting',
      grouping: 'communication',
      is_active: '1',
      value: '1'
    },
    2: {
      label: 'Phone Call',
      icon: 'fa-phone',
      name: 'Phone Call',
      grouping: 'communication',
      is_active: '1',
      value: '2'
    },
    3: {
      label: 'Email',
      icon: 'fa-envelope-o',
      name: 'Email',
      grouping: 'communication',
      is_active: '1',
      value: '3'
    },
    4: {
      label: 'Outbound SMS',
      icon: 'fa-mobile',
      name: 'SMS',
      grouping: 'communication',
      is_active: '1',
      value: '4'
    },
    5: {
      label: 'Event Registration',
      name: 'Event Registration',
      is_active: '1',
      value: '5'
    },
    6: {
      label: 'Contribution',
      name: 'Contribution',
      is_active: '1',
      value: '6'
    },
    7: {
      label: 'Membership Signup',
      name: 'Membership Signup',
      is_active: '1',
      value: '7'
    },
    8: {
      label: 'Membership Renewal',
      name: 'Membership Renewal',
      is_active: '1',
      value: '8'
    },
    9: {
      label: 'Tell a Friend',
      name: 'Tell a Friend',
      is_active: '1',
      value: '9'
    },
    10: {
      label: 'Pledge Acknowledgment',
      name: 'Pledge Acknowledgment',
      is_active: '1',
      value: '10'
    },
    11: {
      label: 'Pledge Reminder',
      name: 'Pledge Reminder',
      is_active: '1',
      value: '11'
    },
    12: {
      label: 'Inbound Email',
      name: 'Inbound Email',
      grouping: 'communication',
      is_active: '1',
      value: '12'
    },
    13: {
      label: 'Open Case',
      icon: 'fa-folder-open-o',
      name: 'Open Case',
      grouping: 'milestone',
      is_active: '1',
      value: '13'
    },
    14: {
      label: 'Follow up',
      icon: 'fa-share-square-o',
      name: 'Follow up',
      grouping: 'communication',
      is_active: '1',
      value: '14'
    },
    15: {
      label: 'Change Case Type',
      icon: 'fa-random',
      name: 'Change Case Type',
      grouping: 'system',
      is_active: '1',
      value: '15'
    },
    16: {
      label: 'Change Case Status',
      icon: 'fa-pencil-square-o',
      name: 'Change Case Status',
      grouping: 'system',
      is_active: '1',
      value: '16'
    },
    17: {
      label: 'Membership Renewal Reminder',
      name: 'Membership Renewal Reminder',
      is_active: '1',
      value: '17'
    },
    18: {
      label: 'Change Case Start Date',
      icon: 'fa-calendar',
      name: 'Change Case Start Date',
      grouping: 'system',
      is_active: '1',
      value: '18'
    },
    19: {
      label: 'Bulk Email',
      name: 'Bulk Email',
      is_active: '1',
      value: '19'
    },
    20: {
      label: 'Assign Case Role',
      icon: 'fa-user-plus',
      name: 'Assign Case Role',
      grouping: 'system',
      is_active: '1',
      value: '20'
    },
    21: {
      label: 'Remove Case Role',
      icon: 'fa-user-times',
      name: 'Remove Case Role',
      grouping: 'system',
      is_active: '1',
      value: '21'
    },
    22: {
      label: 'Print/Merge Document',
      icon: 'fa-file-pdf-o',
      name: 'Print PDF Letter',
      grouping: 'communication',
      is_active: '1',
      value: '22'
    },
    23: {
      label: 'Merge Case',
      icon: 'fa-compress',
      name: 'Merge Case',
      grouping: 'system',
      is_active: '1',
      value: '23'
    },
    24: {
      label: 'Reassigned Case',
      icon: 'fa-user-circle-o',
      name: 'Reassigned Case',
      grouping: 'system',
      is_active: '1',
      value: '24'
    },
    25: {
      label: 'Link Cases',
      icon: 'fa-link',
      name: 'Link Cases',
      grouping: 'system',
      is_active: '1',
      value: '25'
    },
    26: {
      label: 'Change Case Tags',
      icon: 'fa-tags',
      name: 'Change Case Tags',
      grouping: 'system',
      is_active: '1',
      value: '26'
    },
    27: {
      label: 'Add Client To Case',
      icon: 'fa-users',
      name: 'Add Client To Case',
      grouping: 'system',
      is_active: '1',
      value: '27'
    },
    28: {
      label: 'Survey',
      name: 'Survey',
      is_active: '1',
      value: '28'
    },
    29: {
      label: 'Canvass',
      name: 'Canvass',
      is_active: '1',
      value: '29'
    },
    30: {
      label: 'PhoneBank',
      name: 'PhoneBank',
      is_active: '1',
      value: '30'
    },
    31: {
      label: 'WalkList',
      name: 'WalkList',
      is_active: '1',
      value: '31'
    },
    32: {
      label: 'Petition Signature',
      name: 'Petition',
      is_active: '1',
      value: '32'
    },
    33: {
      label: 'Change Custom Data',
      icon: 'fa-table',
      name: 'Change Custom Data',
      grouping: 'system',
      is_active: '1',
      value: '33'
    },
    34: {
      label: 'Mass SMS',
      name: 'Mass SMS',
      is_active: '1',
      value: '34'
    },
    35: {
      label: 'Change Membership Status',
      name: 'Change Membership Status',
      is_active: '1',
      value: '35'
    },
    36: {
      label: 'Change Membership Type',
      name: 'Change Membership Type',
      is_active: '1',
      value: '36'
    },
    37: {
      label: 'Cancel Recurring Contribution',
      name: 'Cancel Recurring Contribution',
      is_active: '1',
      value: '37'
    },
    38: {
      label: 'Update Recurring Contribution Billing Details',
      name: 'Update Recurring Contribution Billing Details',
      is_active: '1',
      value: '38'
    },
    39: {
      label: 'Update Recurring Contribution',
      name: 'Update Recurring Contribution',
      is_active: '1',
      value: '39'
    },
    40: {
      label: 'Reminder Sent',
      name: 'Reminder Sent',
      is_active: '1',
      value: '40'
    },
    41: {
      label: 'Export Accounting Batch',
      name: 'Export Accounting Batch',
      is_active: '1',
      value: '41'
    },
    42: {
      label: 'Create Batch',
      name: 'Create Batch',
      is_active: '1',
      value: '42'
    },
    43: {
      label: 'Edit Batch',
      name: 'Edit Batch',
      is_active: '1',
      value: '43'
    },
    44: {
      label: 'SMS delivery',
      name: 'SMS delivery',
      is_active: '1',
      value: '44'
    },
    45: {
      label: 'Inbound SMS',
      name: 'Inbound SMS',
      is_active: '1',
      value: '45'
    },
    46: {
      label: 'Payment',
      name: 'Payment',
      is_active: '1',
      value: '46'
    },
    47: {
      label: 'Refund',
      name: 'Refund',
      is_active: '1',
      value: '47'
    },
    48: {
      label: 'Change Registration',
      name: 'Change Registration',
      is_active: '1',
      value: '48'
    },
    49: {
      label: 'Downloaded Invoice',
      name: 'Downloaded Invoice',
      is_active: '1',
      value: '49'
    },
    50: {
      label: 'Emailed Invoice',
      name: 'Emailed Invoice',
      is_active: '1',
      value: '50'
    },
    51: {
      label: 'Contact Merged',
      name: 'Contact Merged',
      is_active: '1',
      value: '51'
    },
    52: {
      label: 'Contact Deleted by Merge',
      name: 'Contact Deleted by Merge',
      is_active: '1',
      value: '52'
    },
    53: {
      label: 'Change Case Subject',
      icon: 'fa-pencil-square-o',
      name: 'Change Case Subject',
      grouping: 'system',
      is_active: '1',
      value: '53'
    },
    54: {
      label: 'Failed Payment',
      name: 'Failed Payment',
      is_active: '1',
      value: '54'
    },
    55: {
      label: 'Interview',
      icon: 'fa-comment-o',
      name: 'Interview',
      is_active: '1',
      value: '55'
    },
    56: {
      label: 'Medical evaluation',
      name: 'Medical evaluation',
      grouping: 'milestone',
      is_active: '1',
      value: '56'
    },
    58: {
      label: 'Mental health evaluation',
      name: 'Mental health evaluation',
      grouping: 'milestone',
      is_active: '1',
      value: '58'
    },
    60: {
      label: 'Secure temporary housing',
      name: 'Secure temporary housing',
      grouping: 'milestone',
      is_active: '1',
      value: '60'
    },
    62: {
      label: 'Income and benefits stabilization',
      name: 'Income and benefits stabilization',
      is_active: '1',
      value: '62'
    },
    64: {
      label: 'Long-term housing plan',
      name: 'Long-term housing plan',
      grouping: 'milestone',
      is_active: '1',
      value: '64'
    },
    66: {
      label: 'ADC referral',
      name: 'ADC referral',
      is_active: '1',
      value: '66'
    },
    68: {
      label: 'File Upload',
      icon: 'fa-file',
      name: 'File Upload',
      is_active: '1',
      value: '68'
    },
    69: {
      label: 'Remove Client From Case',
      icon: 'fa-user-times',
      name: 'Remove Client From Case',
      grouping: 'system',
      is_active: '1',
      value: '69'
    },
    70: {
      label: 'Case Task',
      name: 'Case Task',
      grouping: 'task',
      is_active: '1',
      value: '70'
    },
    72: {
      label: 'Communication Act',
      name: 'Communication Act',
      grouping: 'communication',
      is_active: '1',
      value: '72'
    },
    73: {
      label: 'Alert',
      icon: 'fa-exclamation',
      name: 'Alert',
      grouping: 'alert',
      is_active: '0',
      value: '73'
    }
  };

  module.constant('ActivityTypesData', {
    values: CRM['civicase-base'].activityTypes
  });
}());
