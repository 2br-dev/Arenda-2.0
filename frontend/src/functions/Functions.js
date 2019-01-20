import $ from 'jquery';

class Functions {
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

  getNumberOfMonth(month) {
    let monthNumber = '00';
    switch(month) {
      case 'Январь':
        monthNumber = '01'; 
        break; 
      case 'Февраль':
        monthNumber = '02'; 
        break;
      case 'Март':
        monthNumber = '03'; 
        break;
      case 'Апрель':
        monthNumber = '04'; 
        break; 
      case 'Май':
        monthNumber = '05'; 
        break;
      case 'Июнь':
        monthNumber = '06'; 
        break;
      case 'Июль':
        monthNumber = '07';  
        break;
      case 'Август':
        monthNumber = '08'; 
        break;
      case 'Сентябрь':
        monthNumber = '09'; 
        break;
      case 'Октябрь':
        monthNumber = '10';  
        break;
      case 'Ноябрь':
        monthNumber = '11'; 
        break;
      case 'Декабрь':
        monthNumber = '12';         
        break;
      default: 
    }  
    return monthNumber; 
  }

} 

export default Functions;