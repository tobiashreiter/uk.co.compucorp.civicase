'use strict';

const Utility = require('./utility.js');
const mouseEventsHelper = require('./mouse-events-helper.js');

module.exports = async (page, scenario, vp) => {
  const utility = new Utility(page, scenario, vp);

  await utility.waitForAngular();

  await mouseEventsHelper(page, scenario, null, true);

  if (scenario.isUIBPopover) {
    // Clone the popover to a new element so that it doesn't get lost
    // See why https://github.com/garris/BackstopJS/issues/689
    await utility.cloneUibPopover();
  }
};
