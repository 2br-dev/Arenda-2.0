import $ from 'jquery';

class Hide {
  hideRenters() {
    if (window.location.pathname === '/reestr') {
      $('.renter').each(function () {
        if ($(this).children(".renter-contract").length > 0) {
          return;
        } else {
          $(this).hide();
        }
      });
    }
  }
} 

export default Hide;