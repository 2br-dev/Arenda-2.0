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
    }  
    return monthNumber; 
  }

  getStringOfMonth(month) {
    let monthNumber = '00';
    if (typeof month === 'number' && month < 10) month = '0' + month;
      
    switch(month) {
      case '01':
        monthNumber = 'Январь'; 
        break; 
      case '02':
        monthNumber = 'Февраль'; 
        break;
      case '03':
        monthNumber = 'Март'; 
        break;
      case '04':
        monthNumber = 'Апрель'; 
        break; 
      case '05':
        monthNumber = 'Май'; 
        break;
      case '06':
        monthNumber = 'Июнь'; 
        break;
      case '07':
        monthNumber = 'Июль';  
        break;
      case '08':
        monthNumber = 'Август'; 
        break;
      case '09':
        monthNumber = 'Сентябрь'; 
        break;
      case '10':
        monthNumber = 'Октябрь';  
        break;
      case '11':
        monthNumber = 'Ноябрь'; 
        break;
      case '12':
        monthNumber = 'Декабрь';         
        break;
    }  
    return monthNumber; 
  }

  setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }

  getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) === ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) === 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

  checkCookie() {
    var user = this.getCookie("username");
    if (user !== "") {
      alert("Welcome again " + user);
    } else {
      user = prompt("Please enter your name:", "");
      if (user !== "" && user != null) {
        this.setCookie("username", user, 365);
      }
    }
  }

  toPrice = number => `${Number(number).toFixed(2)} ₽`;

} 

export default Functions;